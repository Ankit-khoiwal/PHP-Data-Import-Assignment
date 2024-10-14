<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ProcessCSV implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    public function handle()
    {
        if (!file_exists($this->filePath) || !is_readable($this->filePath)) {
            Log::error('File not found or not readable: ' . $this->filePath);
            return;
        }

        $file = fopen($this->filePath, 'r');
        fgetcsv($file); // Skip header row

        DB::beginTransaction();

        try {
            while (($data = fgetcsv($file)) !== false) {
                DB::table('temp_import')->insert([
                    'voucher_no' => $data[0] ?? null, // Handle potential null values
                    'due_amount' => $data[1] ?? null,
                    'paid_amount' => $data[2] ?? null,
                    'concession' => $data[3] ?? null,
                    'scholarship' => $data[4] ?? null,
                    'refund' => $data[5] ?? null,
                ]);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error processing CSV: ' . $e->getMessage());
        } finally {
            fclose($file);
        }
    }
}
