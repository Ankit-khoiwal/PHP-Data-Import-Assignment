<?php

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;


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
        $file = fopen($this->filePath, 'r');
        fgetcsv($file); // Skip header

        DB::beginTransaction();

        try {
            while (($data = fgetcsv($file)) !== FALSE) {
                DB::table('temp_import')->insert([
                    'voucher_no' => $data[0],
                    'due_amount' => $data[1],
                    'paid_amount' => $data[2],
                    'concession' => $data[3],
                    'scholarship' => $data[4],
                    'refund' => $data[5],
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e->getMessage());
        }

        fclose($file);
    }
}
