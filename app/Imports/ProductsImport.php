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

class ProductsImport implements ToCollection, WithHeadingRow, WithChunkReading, ShouldQueue {
    use Importable, Queueable;

    protected int $chatbotId;
    protected string $uploadUuid;

    public function __construct(int $chatbotId, string $uploadUuid) {
        $this->chatbotId = $chatbotId;
        $this->uploadUuid = $uploadUuid;
        $this->onQueue('default');
        $this->onConnection('database');
    }

    // public function collection(Collection $rows) {
    //     try {
    //         DB::transaction(function () use ($rows) {
    //             $inserted = 0;
    //             $updated = 0;

    //             foreach ($rows as $row) {
    //                 $map = $this->normalizeRow($row->toArray());
    //                 $uniqueId = trim((string)($map['product_unique_id'] ?? ''));
    //                 if (!$uniqueId) continue;

    //                 $data = [
    //                     'chatbot_id' => $this->chatbotId,
    //                     'product_name' => $map['product_name'] ?? null,
    //                     'product_unique_id' => $uniqueId,
    //                     'product_image' => $map['product_image'] ?? null,
    //                     'description' => $map['description'] ?? null,
    //                     'price' => $this->sanitizePrice($map['price'] ?? null),
    //                     'tags' => $map['tags'] ?? null,
    //                     'product_link' => $map['product_link'] ?? null,
    //                 ];

    //                 $existing = Product::where('chatbot_id', $this->chatbotId)
    //                     ->where('product_unique_id', $uniqueId)
    //                     ->first();

    //                 if ($existing) {
    //                     $existing->update($data);
    //                     $updated++;
    //                 } else {
    //                     Product::create($data);
    //                     $inserted++;
    //                 }
    //             }

    //             $job = UploadJob::where('upload_uuid', $this->uploadUuid)->first();
    //             if ($job) {
    //                 $job->increment('processed_rows', $rows->count());
    //                 $job->increment('inserted', $inserted);
    //                 $job->increment('updated', $updated);

    //                 if ($job->processed_rows >= $job->total_rows && $job->status !== 'done') {
    //                     $this->buildSnapshot($this->chatbotId);
    //                     $job->status = 'done';
    //                     $job->save();
    //                 } else {
    //                     $job->status = 'processing';
    //                     $job->save();
    //                 }
    //             }
    //         });
    //     } catch (\Throwable $e) {
    //         Log::error("ProductsImport chunk failed: " . $e->getMessage(), ['rows' => $rows->toArray()]);
    //         throw $e;
    //     }
    // }

    public function collection(Collection $rows) {
        try {
            DB::transaction(function () use ($rows) {
                $inserted = 0;
                $updated = 0;

                foreach ($rows as $row) {
                    $map = $this->normalizeRow($row->toArray());
                    $uniqueId = trim((string)($map['product_unique_id'] ?? ''));

                    if (empty($uniqueId)) continue;

                    $data = [
                        'chatbot_id' => $this->chatbotId,
                        'product_name' => $map['product_name'] ?? null,
                        'product_unique_id' => $uniqueId,
                        'product_image' => $map['product_image'] ?? null,
                        'description' => $map['description'] ?? null,
                        'price' => $this->sanitizePrice($map['price'] ?? null),
                        'tags' => $map['tags'] ?? null,
                        'product_link' => $map['product_link'] ?? null,
                    ];

                    $existing = Product::where('chatbot_id', $this->chatbotId)
                        ->where('product_unique_id', $uniqueId)
                        ->first();

                    if ($existing) {
                        $existing->update($data);
                        $updated++;
                    } else {
                        Product::create($data);
                        $inserted++;
                    }
                }

                $job = UploadJob::where('upload_uuid', $this->uploadUuid)->first();
                if ($job) {
                    $job->increment('processed_rows', $rows->count());
                    $job->increment('inserted', $inserted);
                    $job->increment('updated', $updated);

                    if ($job->processed_rows >= $job->total_rows && $job->status !== 'done') {
                        $this->buildSnapshot($this->chatbotId);
                        $job->status = 'done';
                        $job->save();
                    } else {
                        $job->status = 'processing';
                        $job->save();
                    }
                }
            });
        } catch (\Throwable $e) {
            \Log::error('ProductsImport error: ' . $e->getMessage(), [
                'chatbot_id' => $this->chatbotId,
                'rows' => $rows->toArray()
            ]);
            $job = UploadJob::where('upload_uuid', $this->uploadUuid)->first();
            if ($job) {
                $job->status = 'failed';
                $job->error = $e->getMessage();
                $job->save();
            }
            throw $e; // re-throw to mark the job as failed
        }
    }


    public function chunkSize(): int {
        return 100; // adjust if needed
    }

    private function normalizeRow(array $row): array {
        $out = [];
        foreach ($row as $k => $v) {
            $key = strtolower(trim(str_replace(['-', '/', '\\'], ' ', $k)));
            $key = preg_replace('/\s+/', ' ', $key);

            if (strpos($key, 'product name') !== false || strpos($key, 'name') === 0) $out['product_name'] = $v;
            elseif (strpos($key, 'unique') !== false && strpos($key, 'id') !== false) $out['product_unique_id'] = $v;
            elseif (strpos($key, 'image') !== false) $out['product_image'] = $v;
            elseif (strpos($key, 'description') !== false) $out['description'] = $v;
            elseif (strpos($key, 'price') !== false) $out['price'] = $v;
            elseif (strpos($key, 'tag') !== false) $out['tags'] = $v;
            elseif (strpos($key, 'link') !== false || strpos($key, 'url') !== false) $out['product_link'] = $v;
        }
        return $out;
    }

    private function sanitizePrice($value) {
        if (is_null($value) || $value === '') return null;
        $v = preg_replace('/[^\d\.\-]/', '', (string)$value);
        return $v === '' ? null : (float)$v;
    }

    private function buildSnapshot(int $chatbotId) {
        $allProducts = Product::where('chatbot_id', $chatbotId)->get();
        $snapshot = [];

        foreach ($allProducts as $p) {
            $snapshot[$p->product_unique_id] = [
                'product_name' => $p->product_name,
                'product_image' => $p->product_image,
                'description' => $p->description,
                'price' => $p->price,
                'tags' => $p->tags,
                'product_link' => $p->product_link,
                'created_at' => $p->created_at?->toDateTimeString(),
                'updated_at' => $p->updated_at?->toDateTimeString(),
            ];
        }

        ProductJson::updateOrCreate(
            ['chatbot_id' => $chatbotId],
            ['products' => $snapshot]
        );
    }
}
