<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\FinancialTrans;
use App\Models\CommonFeeCollection;
use ProcessCSV;

class ImportController extends Controller
{
    //


    public function uploadeDocument()
    {
        return view('admin.pages.dataImport.upload');
    }


    public function uploadCSV(Request $request)
    {
        dd($request->file('csv_file'));

        if ($request->hasFile('csv_file') && $request->file('csv_file')->getSize() > 262144000) {
            // return back()->withErrors(['csv_file' => 'The file size exceeds the maximum limit of 250MB.']);
            dd('ffile size');
        }

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        dd('File passed validation');

        $path = $request->file('csv_file')->storeAs('uploads', 'import.csv');
        ProcessCSV::dispatch(storage_path('app/' . $path));

        return back()->with('success', 'CSV file upload started. It will be processed in the background.');
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
