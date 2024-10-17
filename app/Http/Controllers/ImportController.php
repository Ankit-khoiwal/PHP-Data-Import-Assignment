<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCSVData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportController extends Controller
{
    public function __construct()
    {
        $this->middleware('throttle:60,1'); // Rate limiting: 60 requests per minute
    }

    public function uploadeDocument()
    {
        return view('admin.pages.dataImport.upload');
    }

    public function uploadCSVData(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt']);

        try {
            set_time_limit(0);
            $filePath = $request->file('csv_file')->getRealPath();
            $batchSize = 1000;
            $totalRows = 0;
            $insertedRows = 0;

            if (($handle = fopen($filePath, 'r')) !== false) {
                fgetcsv($handle); // Skip header

                while (!feof($handle)) {
                    $batchData = [];
                    $rowCount = 0;

                    while ($rowCount < $batchSize && ($data = fgetcsv($handle)) !== false) {
                        if ($this->isValidRow($data)) {
                            $mappedData = $this->mapRowToData($data);
                            $batchData[] = $mappedData;
                            $rowCount++;
                            $totalRows++;
                        }
                    }

                    if (!empty($batchData)) {
                        DB::transaction(function () use ($batchData) {
                            DB::table('Temporary_completedata')->insert($batchData);
                        });
                        $insertedRows += count($batchData);
                    }
                }
                fclose($handle);
            }

            if ($insertedRows > 0) {
                ProcessCSVData::dispatch(); // Dispatch job to process data in the background
                return response()->json([
                    'status' => 200,
                    'message' => 'CSV data uploaded and processing started successfully.',
                    'total_rows_processed' => $totalRows,
                    'total_rows_inserted' => $insertedRows,
                ]);
            }

            return response()->json(['status' => 500, 'message' => 'No data inserted.'], 500);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    private function isValidRow($data)
    {
        return is_array($data) && count(array_filter($data)) > 0;
    }

    private function mapRowToData($data)
    {
        return [
            'date' => $data[1] ?? null,
            'academic_year' => $data[2] ?? null,
            'session' => $data[3] ?? null,
            'voucher_type' => $data[5] ?? null,
            'voucher_no' => $data[6] ?? null,
            'roll_no' => $data[7] ?? null,
            'admno_uniqueid' => $data[8] ?? null,
            'status' => $data[9] ?? null,
            'fee_category' => $data[10] ?? null,
            'faculty' => $data[11] ?? null,
            'program' => $data[12] ?? null,
            'department' => $data[13] ?? null,
            'batch' => $data[14] ?? null,
            'receipt_no' => $data[15] ?? null,
            'fee_head' => $data[16] ?? null,
            'due_amount' => $data[17] ?? null,
            'paid_amount' => $data[18] ?? null,
            'concession_amount' => $data[19] ?? null,
            'scholarship_amount' => $data[20] ?? null,
            'reverse_concession_amount' => $data[21] ?? null,
            'write_off_amount' => $data[22] ?? null,
            'adjusted_amount' => $data[23] ?? null,
            'refund_amount' => $data[24] ?? null,
            'fund_transfer_amount' => $data[25] ?? null,
            'remarks' => $data[26] ?? null,
        ];
    }
}
