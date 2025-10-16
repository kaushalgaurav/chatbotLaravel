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

    // Verbose logging flag (you chose B)
    protected bool $verbose = true;

    public $tries = 3;             // max retries
    public $timeout = 3600;        // seconds

    public function __construct(int $chatbotId, string $uploadUuid) {
        $this->chatbotId = $chatbotId;
        $this->uploadUuid = $uploadUuid;
        // $this->onQueue('default');
        // $this->onConnection('database');

        // Use Redis connection and dedicated import queue
        $this->onConnection('redis');
        $this->onQueue(env('IMPORT_QUEUE', 'imports'));
    }

    public function collection(Collection $rows) {
        if ($this->verbose) {
            Log::info('✅ ProductsImport COLLECTION TRIGGERED', [
                'row_count' => $rows->count(),
                'chatbot_id' => $this->chatbotId,
                'upload_uuid' => $this->uploadUuid,
            ]);

            // Log SQL queries for debugging (verbose)
            DB::listen(function ($query) {
                Log::debug('SQL', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time,
                ]);
            });
        }

        try {
            DB::transaction(function () use ($rows) {
                $inserted = 0;
                $updated = 0;

                // foreach ($rows as $index => $row) {
                //     // Normalize the row
                //     $map = $this->normalizeRow($row->toArray());

                //     if ($this->verbose) {
                //         Log::info('ROW NORMALIZED', [
                //             'index' => $index,
                //             'original_row' => $row->toArray(),
                //             'normalized' => $map,
                //         ]);
                //     }

                //     $uniqueId = trim((string)($map['product_unique_id'] ?? ''));

                //     if (empty($uniqueId)) {
                //         Log::warning('SKIPPING ROW - MISSING UNIQUE ID', [
                //             'index' => $index,
                //             'normalized' => $map,
                //         ]);
                //         continue;
                //     }

                //     $data = [
                //         'chatbot_id' => $this->chatbotId,
                //         'product_name' => $map['product_name'] ?? null,
                //         'product_unique_id' => $uniqueId,
                //         'product_image' => $map['product_image'] ?? null,
                //         'description' => $map['description'] ?? null,
                //         'price' => $this->sanitizePrice($map['price'] ?? null),
                //         'tags' => $map['tags'] ?? null,
                //         'product_link' => $map['product_link'] ?? null,
                //     ];

                //     if ($this->verbose) {
                //         Log::info('READY TO PERSIST', $data);
                //     }

                //     $existing = Product::where('chatbot_id', $this->chatbotId)
                //         ->where('product_unique_id', $uniqueId)
                //         ->first();

                //     if ($existing) {
                //         try {
                //             $existing->update($data);
                //             $updated++;
                //             if ($this->verbose) {
                //                 Log::info('UPDATED PRODUCT', [
                //                     'id' => $existing->id,
                //                     'product_unique_id' => $uniqueId,
                //                 ]);
                //             }
                //         } catch (\Throwable $e) {
                //             Log::error('ERROR UPDATING PRODUCT', [
                //                 'unique_id' => $uniqueId,
                //                 'error' => $e->getMessage(),
                //                 'data' => $data,
                //             ]);
                //             // continue to next row (don't abort whole import)
                //             continue;
                //         }
                //     } else {
                //         try {
                //             Product::create($data);
                //             $inserted++;
                //             if ($this->verbose) {
                //                 Log::info('INSERTED PRODUCT', ['product_unique_id' => $uniqueId]);
                //             }
                //         } catch (\Throwable $e) {
                //             Log::error('ERROR INSERTING PRODUCT', [
                //                 'unique_id' => $uniqueId,
                //                 'error' => $e->getMessage(),
                //                 'data' => $data,
                //             ]);
                //             // continue to next row (don't abort whole import)
                //             continue;
                //         }
                //     }
                // }

                $productsToInsert = [];
                foreach ($rows as $row) {
                    $map = $this->normalizeRow($row->toArray());
                    $uniqueId = trim($map['product_unique_id'] ?? '');
                    if (!$uniqueId) continue;

                    $productsToInsert[] = [
                        'chatbot_id' => $this->chatbotId,
                        'product_name' => $map['product_name'],
                        'product_unique_id' => $uniqueId,
                        'product_image' => $map['product_image'] ?? null,
                        'description' => $map['description'] ?? null,
                        'price' => $this->sanitizePrice($map['price'] ?? null),
                        'tags' => $map['tags'] ?? null,
                        'product_link' => $map['product_link'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                Product::upsert(
                    $productsToInsert,
                    ['product_unique_id', 'chatbot_id'], // unique keys for update
                    ['product_name', 'product_image', 'description', 'price', 'tags', 'product_link', 'updated_at']
                );

                // Update upload job stats
                $job = UploadJob::where('upload_uuid', $this->uploadUuid)->first();
                if ($job) {
                    $job->increment('processed_rows', $rows->count());
                    $job->increment('inserted', $inserted);
                    $job->increment('updated', $updated);

                    if ($job->processed_rows >= $job->total_rows && $job->status !== 'done') {
                        // build snapshot only when done
                        $this->buildSnapshot($this->chatbotId);
                        $job->status = 'done';
                        $job->save();
                        if ($this->verbose) {
                            Log::info('UPLOAD JOB DONE', ['upload_uuid' => $this->uploadUuid]);
                        }
                    } else {
                        $job->status = 'processing';
                        $job->save();
                    }
                } else {
                    Log::warning('UploadJob not found for upload_uuid', ['upload_uuid' => $this->uploadUuid]);
                }
            });
        } catch (\Throwable $e) {
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

            // Re-throw so queue marks job as failed
            throw $e;
        }
    }

    public function chunkSize(): int {
        return 100; // adjust as needed
    }

    /**
     * Normalize a row from Excel into a consistent associative array.
     * Accepts flexible header variations (product-name, Product Name, NAME, etc).
     */
    private function normalizeRow(array $row): array {
        $out = [];

        foreach ($row as $k => $v) {
            // Step 1: remove control characters (including BOM) and normalize whitespace
            // Use Unicode-aware pattern to remove control characters
            $cleanKey = preg_replace('/[\p{C}]+/u', '', (string)$k); // remove control chars
            $cleanKey = strtolower($cleanKey);
            $cleanKey = str_replace(['-', '/', '\\', '_'], ' ', $cleanKey);
            $cleanKey = trim($cleanKey);
            $cleanKey = preg_replace('/\s+/', ' ', $cleanKey); // collapse spaces

            // Normalize value: trim and convert empty to null
            $value = is_string($v) ? trim($v) : $v;
            if ($value === '') $value = null;

            // Flexible header detection using contains or exact matches
            // product_name
            if (
                $cleanKey === 'product name' || $cleanKey === 'product_name' ||
                $cleanKey === 'name' || str_contains($cleanKey, 'product name') ||
                str_contains($cleanKey, 'productname')
            ) {
                $out['product_name'] = $value;
                continue;
            }

            // product_unique_id
            if (
                $cleanKey === 'product unique id' || $cleanKey === 'unique id' ||
                $cleanKey === 'product_unique_id' || str_contains($cleanKey, 'unique') && str_contains($cleanKey, 'id')
            ) {
                $out['product_unique_id'] = $value;
                continue;
            }

            // product image
            if ($cleanKey === 'product image' || $cleanKey === 'image' || str_contains($cleanKey, 'image') || str_contains($cleanKey, 'img')) {
                $out['product_image'] = $value;
                continue;
            }

            // description
            if ($cleanKey === 'description' || str_contains($cleanKey, 'description') || str_contains($cleanKey, 'desc')) {
                $out['description'] = $value;
                continue;
            }

            // price
            if ($cleanKey === 'price' || str_contains($cleanKey, 'price') || str_contains($cleanKey, 'cost')) {
                $out['price'] = $value;
                continue;
            }

            // tags
            if ($cleanKey === 'tags' || str_contains($cleanKey, 'tag') || str_contains($cleanKey, 'label')) {
                $out['tags'] = $value;
                continue;
            }

            // link / url
            if ($cleanKey === 'product link' || $cleanKey === 'link' || $cleanKey === 'url' || str_contains($cleanKey, 'link') || str_contains($cleanKey, 'url')) {
                $out['product_link'] = $value;
                continue;
            }

            // If header unknown and verbose, log it
            if ($this->verbose) {
                Log::debug('UNKNOWN HEADER IN ROW', ['raw' => $k, 'clean' => $cleanKey]);
            }
        }

        return $out;
    }

    /**
     * Sanitize price value: remove currency symbols, commas and convert to float|null
     */
    private function sanitizePrice($value) {
        if (is_null($value) || $value === '') return null;
        $v = preg_replace('/[^\d\.\-]/', '', (string)$value);
        return $v === '' ? null : (float)$v;
    }

    /**
     * Build snapshot stored in ProductJson as a JSON array of objects (no double-encoding)
     */
    private function buildSnapshot(int $chatbotId) {
        $allProducts = Product::where('chatbot_id', $chatbotId)->get();

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

        // Save array directly. Model casts will convert to JSON when writing.
        ProductJson::updateOrCreate(
            ['chatbot_id' => $chatbotId],
            ['products' => $snapshot]
        );

        if ($this->verbose) {
            Log::info('SNAPSHOT BUILT', ['chatbot_id' => $chatbotId, 'count' => count($snapshot)]);
        }
    }
}
