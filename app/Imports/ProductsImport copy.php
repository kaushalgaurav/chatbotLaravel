<?php

namespace App\Imports;

use App\Models\ChatbotProduct;
use App\Models\Product;
use App\Models\UploadJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ShouldQueue;

class ProductsImport implements ToCollection, WithHeadingRow, WithChunkReading, ShouldQueue {
    protected $chatbotId;
    protected $uploadUuid;

    public function __construct(int $chatbotId, string $uploadUuid) {
        $this->chatbotId = $chatbotId;
        $this->uploadUuid = $uploadUuid;
    }

    /**
     * This will be called for each chunk (a Collection of rows)
     */
    public function collection(Collection $rows) {
        // process chunk inside transaction -> atomic per chunk
        DB::transaction(function () use ($rows) {
            $inserted = 0;
            $updated = 0;
            foreach ($rows as $row) {
                // HeadingRow takes headers to lower-case keys with spaces replaced by underscores if configured.
                // We'll normalize possible header names.
                $rowArr = $row->toArray();

                // Normalization helper -> map known variations to our keys
                $map = $this->normalizeRow($rowArr);

                $unique = trim((string)($map['product_unique_id'] ?? ''));
                if (empty($unique)) {
                    // skip rows without unique id
                    continue;
                }

                $productData = [
                    'chatbot_id' => $this->chatbotId,
                    'product_name' => $map['product_name'] ?? null,
                    'product_unique_id' => $unique,
                    'product_image' => $map['product_image'] ?? null,
                    'description' => $map['description'] ?? null,
                    'price' => $this->sanitizePrice($map['price'] ?? null),
                    'tags' => $map['tags'] ?? null,
                    'product_link' => $map['product_link'] ?? null,
                ];

                // Upsert logic - update on duplicate product_unique_id per chatbot
                $existing = Product::where('chatbot_id', $this->chatbotId)
                    ->where('product_unique_id', $unique)
                    ->first();

                if ($existing) {
                    // Overwrite with new values if present (you can tune merges)
                    $existing->update($productData);
                    $updated++;
                } else {
                    Product::create($productData);
                    $inserted++;
                }
            }

            // Update UploadJob counters
            $job = UploadJob::where('upload_uuid', $this->uploadUuid)->first();
            if ($job) {
                $job->increment('processed_rows', $rows->count());
                $job->increment('inserted', $inserted);
                $job->increment('updated', $updated);
                // set status to processing if not set
                if ($job->status !== 'processing') {
                    $job->status = 'processing';
                    $job->save();
                }
            }
        });
    }

    public function chunkSize(): int {
        return 1000; // tune as needed
    }

    private function sanitizePrice($value) {
        if (is_null($value) || $value === '') return null;
        $v = preg_replace('/[^\d\.\-]/', '', (string)$value);
        return $v === '' ? null : (float)$v;
    }

    private function normalizeRow(array $row) {
        // Map common header variants to standard keys.
        $out = [];
        foreach ($row as $k => $v) {
            $key = strtolower(trim(str_replace(['-', '/', '\\'], ' ', $k)));
            $key = preg_replace('/\s+/', ' ', $key);
            $key = trim($key);

            // fuzzy checks:
            if (strpos($key, 'product name') !== false || strpos($key, 'name') === 0) {
                $out['product_name'] = $v;
            } elseif (strpos($key, 'unique') !== false && strpos($key, 'id') !== false) {
                $out['product_unique_id'] = $v;
            } elseif (strpos($key, 'image') !== false) {
                $out['product_image'] = $v;
            } elseif (strpos($key, 'description') !== false) {
                $out['description'] = $v;
            } elseif (strpos($key, 'price') !== false) {
                $out['price'] = $v;
            } elseif (strpos($key, 'tag') !== false) {
                $out['tags'] = $v;
            } elseif (strpos($key, 'link') !== false || strpos($key, 'url') !== false) {
                $out['product_link'] = $v;
            } else {
                // unknown column — ignore
            }
        }
        return $out;
    }
}
