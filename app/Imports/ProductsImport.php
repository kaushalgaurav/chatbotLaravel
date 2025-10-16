<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductJson;
use App\Models\UploadJob;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\Importable;
use Throwable;
use App\Events\ImportProgressUpdated;

class ProductsImport implements ToCollection, WithHeadingRow, WithChunkReading, ShouldQueue {
    use Importable, Queueable;

    public int $tries = 3;
    public int $timeout = 3600;

    protected int $chatbotId;
    protected string $uploadUuid;

    public function __construct(int $chatbotId, string $uploadUuid) {
        $this->chatbotId = $chatbotId;
        $this->uploadUuid = $uploadUuid;

        $this->onConnection('redis');
        $this->onQueue(env('IMPORT_QUEUE', 'imports'));
    }

    public function collection(Collection $rows) {
        try {
            if ($rows->isEmpty()) {
                Log::info("No rows to process for upload {$this->uploadUuid}");
                return;
            }

            DB::transaction(function () use ($rows) {
                $productsToUpsert = [];

                foreach ($rows as $row) {
                    $map = $this->normalizeRow($row->toArray());
                    $uniqueId = trim((string)($map['product_unique_id'] ?? ''));

                    if (empty($uniqueId)) continue;

                    $productsToUpsert[] = [
                        'chatbot_id' => $this->chatbotId,
                        'product_unique_id' => $uniqueId,
                        'product_name' => $map['product_name'] ?? null,
                        'product_image' => $map['product_image'] ?? null,
                        'description' => $map['description'] ?? null,
                        'price' => $this->sanitizePrice($map['price'] ?? null),
                        'tags' => $map['tags'] ?? null,
                        'product_link' => $map['product_link'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                // Only insert if data exists
                if (!empty($productsToUpsert)) {
                    Product::upsert(
                        $productsToUpsert,
                        ['product_unique_id', 'chatbot_id'],
                        ['product_name', 'product_image', 'description', 'price', 'tags', 'product_link', 'updated_at']
                    );

                    $job = UploadJob::where('upload_uuid', $this->uploadUuid)->first();
                    if ($job) {
                        $job->increment('processed_rows', $rows->count());
                        $job->increment('inserted', count($productsToUpsert));
                        $job->status = $job->processed_rows >= $job->total_rows ? 'done' : 'processing';
                        $job->save();

                        // Broadcast progress
                        event(new ImportProgressUpdated($job));

                        if ($job->status === 'done') {
                            $this->buildSnapshot($this->chatbotId);
                        }
                    }
                } else {
                    Log::info("No valid products found for upload {$this->uploadUuid}");
                }
            });
        } catch (Throwable $e) {
            Log::error('ProductsImport error: ' . $e->getMessage(), [
                'chatbot_id' => $this->chatbotId,
                'upload_uuid' => $this->uploadUuid,
            ]);

            $job = UploadJob::where('upload_uuid', $this->uploadUuid)->first();
            if ($job) {
                $job->status = 'failed';
                $job->error = $e->getMessage();
                $job->save();
            }

            throw $e;
        }
    }

    public function chunkSize(): int {
        return 500;
    }

    /**
     * Flexible header normalization
     */
    private function normalizeRow(array $row): array {
        $out = [];
        foreach ($row as $k => $v) {
            $key = strtolower(trim($k));
            $key = preg_replace('/[\s\-_\/\\\]+/', ' ', $key); // normalize spaces/underscores/dashes
            $value = trim((string)$v) ?: null;

            if (str_contains($key, 'product name')) $out['product_name'] = $value;
            elseif (str_contains($key, 'unique') && str_contains($key, 'id')) $out['product_unique_id'] = $value;
            elseif (str_contains($key, 'image')) $out['product_image'] = $value;
            elseif (str_contains($key, 'description')) $out['description'] = $value;
            elseif (str_contains($key, 'price')) $out['price'] = $value;
            elseif (str_contains($key, 'tag')) $out['tags'] = $value;
            elseif (str_contains($key, 'link') || str_contains($key, 'url')) $out['product_link'] = $value;
        }

        return $out;
    }

    private function sanitizePrice($value) {
        if (is_null($value)) return null;
        $v = preg_replace('/[^\d\.\-]/', '', (string)$value);
        return $v === '' ? null : (float)$v;
    }

    private function buildSnapshot(int $chatbotId) {
        $allProducts = Product::where('chatbot_id', $chatbotId)->get();
        if ($allProducts->isEmpty()) return; // Do not create ProductJson if no products

        $snapshot = $allProducts->map(function ($p) {
            return [
                'product_id' => $p->product_unique_id,
                'product_name' => $p->product_name,
                'product_image' => $p->product_image,
                'description' => $p->description,
                'price' => $p->price,
                'tags' => $p->tags,
                'product_link' => $p->product_link,
                'created_at' => $p->created_at?->toDateTimeString(),
                'updated_at' => $p->updated_at?->toDateTimeString(),
            ];
        })->toArray();

        ProductJson::updateOrCreate(
            ['chatbot_id' => $chatbotId],
            ['products' => $snapshot]
        );
    }
}
