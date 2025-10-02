<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use App\Models\Company;
use App\Models\StockPrice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ProcessStockExcel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $path;
    protected string $company;

    public function __construct(string $path, string $company)
    {
        $this->path = $path;
        $this->company = $company;
    }

    public function handle(): void
    {
        // ✅ Get file from cache and decode
        $encoded = Cache::get($this->path);

        if (!$encoded) {
            Log::error("❌ Excel file not found in cache: {$this->path}");
            return;
        }

        $contents = base64_decode($encoded);

        // Write to temp file
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_') . '.xlsx';
        file_put_contents($tempFile, $contents);

        try {
            $reader = ReaderEntityFactory::createReaderFromFile($tempFile);
            $reader->open($tempFile);
        } catch (\Throwable $e) {
            Log::error("❌ Failed to open Excel from cache: " . $e->getMessage());
            @unlink($tempFile);
            Cache::forget($this->path);
            return;
        }

        // Ensure company exists
        $company = Company::firstOrCreate(['name' => $this->company]);

        $rowCount = 0;
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $cells = $row->toArray();

                // Skip header row
                if ($rowCount === 0 && strtolower($cells[0]) === 'date') {
                    $rowCount++;
                    continue;
                }

                $date  = $cells[0] ?? null;
                $price = $cells[1] ?? null;

                if ($date && $price) {
                    try {
                        $dt = Carbon::parse($date)->toDateString();

                        StockPrice::create([
                            'company_id' => $company->id,
                            'date'       => $dt,
                            'price'      => (float) $price,
                        ]);

                        $rowCount++;
                    } catch (\Exception $e) {
                        Log::warning("⚠️ Skipped row with invalid date: " . $date);
                    }
                }
            }
        }

        $reader->close();

        // Clean up
        @unlink($tempFile);
        Cache::forget($this->path);

        Log::info("✅ Finished processing {$rowCount} rows for company {$this->company}");
    }
}
