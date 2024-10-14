<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\FinancialTrans;
use App\Models\CommonFeeCollection;
use App\Jobs\ProcessCSV;

class ImportController extends Controller
{
    //


    public function uploadeDocument()
    {
        return view('admin.pages.dataImport.upload');
    }


    public function uploadCSVData(Request $request)
    {
        // Validate the file input
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        set_time_limit(0); // Remove the time limit for long processes

        // Store the uploaded CSV file
        $filePath = $request->file('csv_file')->getRealPath();

        // Open the file for reading
        if (($handle = fopen($filePath, 'r')) !== false) {

            // Skip the header row
            fgetcsv($handle);

            $batchData = []; // Initialize an array to hold batch data
            $batchSize = 1000; // Define the batch size
            $rowCount = 0;

            // Loop through the CSV file and read rows
            while (($data = fgetcsv($handle)) !== false) {
                $batchData[] = [
                    'date' => $data[1],
                    'academic_year' => $data[2],
                    'session' => $data[3],
                    'alloted_category' => $data[4],
                    'voucher_type' => $data[5],
                    'voucher_no' => $data[6],
                    'roll_no' => $data[7],
                    'admno_uniqueid' => $data[8],
                    'status' => $data[9],
                    'fee_category' => $data[10],
                    'faculty' => $data[11],
                    'program' => $data[12],
                    'department' => $data[13],
                    'batch' => $data[14],
                    'receipt_no' => $data[15],
                    'fee_head' => $data[16],
                    'due_amount' => $data[17],
                    'paid_amount' => $data[18],
                    'concession_amount' => $data[19],
                    'scholarship_amount' => $data[20],
                    'reverse_concession_amount' => $data[21],
                    'write_off_amount' => $data[22],
                    'adjusted_amount' => $data[23],
                    'refund_amount' => $data[24],
                    'fund_transfer_amount' => $data[25],
                    'remarks' => $data[26],
                ];

                $rowCount++;

                // If the batch size is reached, insert into the database
                if ($rowCount == $batchSize) {
                    DB::table('temp_import')->insert($batchData);
                    $batchData = []; // Reset the batch array
                    $rowCount = 0;   // Reset row count
                }
            }

            // Insert any remaining rows that didn't complete a full batch
            if (count($batchData) > 0) {
                DB::table('temp_import')->insert($batchData);
            }

            fclose($handle); // Close the file after reading
        }

        return response()->json([
            'status' => 200,
            'message' => 'CSV data successfully imported in chunks of 1000.',
        ]);
    }


    private function importData($path)
    {
        $file = fopen($path, 'r');

        fgetcsv($file);

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
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        fclose($file);
    }

    public function verifyData()
    {
        $totalCount = DB::table('temp_import')->count();

        $dueAmount = DB::table('temp_import')->sum('due_amount');
        $paidAmount = DB::table('temp_import')->sum('paid_amount');
        $concession = DB::table('temp_import')->sum('concession');
        $scholarship = DB::table('temp_import')->sum('scholarship');
        $refund = DB::table('temp_import')->sum('refund');

        $expected = [
            'due' => 12654422921,
            'paid' => 11461021901,
            'concession' => 90544480,
            'scholarship' => 471818093,
            'refund' => -173381473
        ];

        if (
            $dueAmount == $expected['due'] &&
            $paidAmount == $expected['paid'] &&
            $concession == $expected['concession'] &&
            $scholarship == $expected['scholarship'] &&
            $refund == $expected['refund']
        ) {
            return back()->with('success', 'Data verified successfully.');
        }

        return back()->withErrors(['error' => 'Data verification failed!']);
    }


    public function distributeData()
    {
        $data = DB::table('temp_import')->get();

        foreach ($data as $row) {
            if ($row->entry_mode == 'DUE' || $row->entry_mode == 'SCHOLARSHIP') {
                FinancialTrans::create([
                    'voucher_no' => $row->voucher_no,
                    'amount' => $row->due_amount,
                    'paid_amount' => $row->paid_amount,
                ]);
            } else {

                CommonFeeCollection::create([
                    'display_receipt_id' => $row->receipt_id,
                    'due_amount' => $row->due_amount,
                    'paid_amount' => $row->paid_amount,
                ]);
            }
        }

        return back()->with('success', 'Data distributed successfully.');
    }
}
