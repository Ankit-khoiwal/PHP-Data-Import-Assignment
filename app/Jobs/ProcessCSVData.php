<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessCSVData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1200000000;
    public $tries = 5;

    public function handle()
    {
        $this->insertStaticTables();
        $this->insertBranches();
        $this->insertFeeCategories();
        $this->insertFeeTypes();
        $this->insertFinancialTransactions();
        $this->insertCommonFeeCollections();
        $this->insertCommonFeeCollectionHeadwise();
        $this->insertFinancialTranDetails();
    }

    private function insertStaticTables()
    {
        $collectionTypes = [
            ['id' => 1, 'collection_desc' => 'academic'],
            ['id' => 2, 'collection_desc' => 'academicmisc'],
            ['id' => 3, 'collection_desc' => 'hostel'],
            ['id' => 4, 'collection_desc' => 'hostelmisc'],
            ['id' => 5, 'collection_desc' => 'transport'],
            ['id' => 6, 'collection_desc' => 'transportmisc']
        ];
        DB::table('FeeCollectionTypes')->insertOrIgnore($collectionTypes);

        $entryModes = [
            ['id' => 1, 'entry_modename' => 'DUE', 'crdr' => 'D', 'entrymodeno' => 0],
            ['id' => 2, 'entry_modename' => 'REVDUE', 'crdr' => 'C', 'entrymodeno' => 12],
            ['id' => 3, 'entry_modename' => 'SCHOLARSHIP', 'crdr' => 'C', 'entrymodeno' => 15],
            ['id' => 4, 'entry_modename' => 'SCHOLARSHIPREV', 'crdr' => 'D', 'entrymodeno' => 16],
            ['id' => 5, 'entry_modename' => 'CONCESSION', 'crdr' => 'C', 'entrymodeno' => 15],
            ['id' => 6, 'entry_modename' => 'RCPT', 'crdr' => 'C', 'entrymodeno' => 0],
            ['id' => 7, 'entry_modename' => 'REVRCPT', 'crdr' => 'D', 'entrymodeno' => 0],
            ['id' => 8, 'entry_modename' => 'JV', 'crdr' => 'C', 'entrymodeno' => 14],
            ['id' => 9, 'entry_modename' => 'REVJV', 'crdr' => 'D', 'entrymodeno' => 14],
            ['id' => 10, 'entry_modename' => 'PMT', 'crdr' => 'D', 'entrymodeno' => 1],
            ['id' => 11, 'entry_modename' => 'REVPMT', 'crdr' => 'C', 'entrymodeno' => 1],
            ['id' => 12, 'entry_modename' => 'Fundtransfer', 'crdr' => '+ ve and -ve', 'entrymodeno' => 1]
        ];
        DB::table('EntryMode')->insertOrIgnore($entryModes);

        $modules = [
            ['id' => 1, 'module_name' => 'academic'],
            ['id' => 11, 'module_name' => 'academicmisc'],
            ['id' => 2, 'module_name' => 'hostel'],
            ['id' => 22, 'module_name' => 'hostelmisc'],
            ['id' => 3, 'module_name' => 'transport'],
            ['id' => 33, 'module_name' => 'transportmisc'],
        ];
        DB::table('Module')->insertOrIgnore($modules);
    }

    private function insertBranches()
    {
        $branches = DB::table('Temporary_completedata')->select('faculty')->distinct()->get();
        foreach ($branches as $branch) {
            DB::table('Branches')->updateOrInsert(['branch_name' => $branch->faculty]);
        }
    }

    private function insertFeeCategories()
    {
        $categories = DB::table('Temporary_completedata')->select('fee_category')->distinct()->get();
        foreach ($categories as $category) {
            if (!empty($category->fee_category)) {
                DB::table('FeeCategory')->updateOrInsert(['fee_category' => $category->fee_category]);
            }
        }
    }

    private function insertFeeTypes()
    {
        $feeHeads = DB::table('Temporary_completedata')->select('fee_head', 'faculty', 'fee_category')->distinct()->get();

        foreach ($feeHeads as $feeHead) {
            $branch = DB::table('Branches')->where('branch_name', $feeHead->faculty)->first();
            $feeCategory = DB::table('FeeCategory')->where('fee_category', $feeHead->fee_category)->first();

            if ($branch && $feeCategory && !empty($feeHead->fee_head)) {
                DB::table('FeeTypes')->updateOrInsert(
                    ['f_name' => $feeHead->fee_head, 'fee_category_id' => $feeCategory->id, 'br_id' => $branch->id],
                    [
                        'collection_id' => 1,
                        'seq_id' => 1,
                        'fee_type_ledger' => $feeHead->fee_head,
                        'fee_head_type' => 1,
                    ]
                );
            }
        }
    }

    private function insertFinancialTransactions()
    {
        DB::table('Temporary_completedata')->orderBy('id')->chunk(1000, function ($transactions) {
            $batchData = [];
            $cachedBranches = [];
            $cachedEntryModes = [];

            foreach ($transactions as $transaction) {
                if (!isset($cachedBranches[$transaction->faculty])) {
                    $branch = DB::table('Branches')->where('branch_name', $transaction->faculty)->first();
                    if ($branch) {
                        $cachedBranches[$transaction->faculty] = $branch->id;
                    } else {
                        continue;
                    }
                }

                if (!isset($cachedEntryModes[$transaction->voucher_type])) {
                    $entryMode = DB::table('EntryMode')->where('entry_modename', $transaction->voucher_type)->first();
                    if ($entryMode) {
                        $cachedEntryModes[$transaction->voucher_type] = $entryMode->id;
                    } else {
                        continue;
                    }
                }

                $batchData[] = [
                    'module_id' => 1,
                    'trans_id' => uniqid(),
                    'admno' => $transaction->admno_uniqueid,
                    'amount' => $transaction->due_amount,
                    'crdr' => $this->getCrdr($transaction->voucher_type),
                    'tran_date' => $transaction->date,
                    'academic_year' => $transaction->academic_year,
                    'entry_mode' => $cachedEntryModes[$transaction->voucher_type],
                    'voucher_no' => $transaction->voucher_no,
                    'br_id' => $cachedBranches[$transaction->faculty],
                    'type_of_concession' => $this->getConcessionType($transaction),
                ];

                if (count($batchData) >= 1000) {
                    DB::table('FinancialTrans')->insert($batchData);
                    $batchData = [];
                }
            }

            if (!empty($batchData)) {
                DB::table('FinancialTrans')->insert($batchData);
            }
        });
    }

    private function insertCommonFeeCollections()
    {
        DB::table('Temporary_completedata')->orderBy('id')->chunk(1000, function ($collections) {
            $batchData = [];
            $cachedBranches = [];

            foreach ($collections as $collection) {
                if (!isset($cachedBranches[$collection->faculty])) {
                    $branch = DB::table('Branches')->where('branch_name', $collection->faculty)->first();
                    if ($branch) {
                        $cachedBranches[$collection->faculty] = $branch->id;
                    } else {
                        continue;
                    }
                }

                $batchData[] = [
                    'module_id' => 1,
                    'receipt_id' => $collection->receipt_no,
                    'amount' => $this->getTotalPaidAmount($collection),
                    'br_id' => $cachedBranches[$collection->faculty],
                    'academic_year' => $collection->academic_year,
                    'financial_year' => $collection->academic_year,
                ];

                if (count($batchData) >= 1000) {
                    DB::table('CommonFeeCollection')->insert($batchData);
                    $batchData = [];
                }
            }

            if (!empty($batchData)) {
                DB::table('CommonFeeCollection')->insert($batchData);
            }
        });
    }

    private function getTotalPaidAmount($collection)
    {
        return $collection->paid_amount + $collection->adjusted_amount + $collection->refund_amount;
    }

    private function insertCommonFeeCollectionHeadwise()
    {
        DB::table('CommonFeeCollection')->orderBy('id')->chunk(1000, function ($collections) {
            // Get all receipt IDs in one query
            $receiptIds = $collections->pluck('receipt_id');

            // Fetch related transactions in one go
            $relatedTransactions = DB::table('Temporary_completedata')
                ->whereIn('receipt_no', $receiptIds)
                ->select('receipt_no', 'fee_head', 'paid_amount')
                ->get();

            // Fetch fee types in one go (if you expect a manageable number of fee types)
            $feeTypes = DB::table('FeeTypes')->pluck('id', 'f_name');

            $batchData = [];

            foreach ($collections as $collection) {
                foreach ($relatedTransactions->where('receipt_no', $collection->receipt_id) as $transaction) {
                    if (isset($feeTypes[$transaction->fee_head])) {
                        $batchData[] = [
                            'common_fee_collection_id' => $collection->id,
                            'module_id' => $collection->module_id,
                            'head_id' => $feeTypes[$transaction->fee_head],
                            'head_name' => $transaction->fee_head,
                            'amount' => $transaction->paid_amount,
                            'br_id' => $collection->br_id,
                        ];
                    }
                }

                // Insert in batches of 1000
                if (count($batchData) >= 1000) {
                    DB::table('CommonFeeCollectionHeadwise')->insert($batchData);
                    $batchData = [];
                }
            }

            // Insert remaining data
            if (!empty($batchData)) {
                DB::table('CommonFeeCollectionHeadwise')->insert($batchData);
            }
        });
    }


    private function insertFinancialTranDetails()
    {
        DB::table('FinancialTrans')->orderBy('id')->chunk(1000, function ($transactions) {

            $voucherNos = $transactions->pluck('voucher_no');

            $relatedData = DB::table('Temporary_completedata')
                ->whereIn('voucher_no', $voucherNos)
                ->select('voucher_no', 'fee_head', 'due_amount')
                ->get();

            $feeTypes = DB::table('FeeTypes')->pluck('id', 'f_name');   

            $batchData = [];

            foreach ($transactions as $transaction) {
                foreach ($relatedData->where('voucher_no', $transaction->voucher_no) as $data) {
                    if (isset($feeTypes[$data->fee_head])) {
                        $batchData[] = [
                            'financial_trans_id' => $transaction->id,
                            'module_id' => $transaction->module_id,
                            'amount' => $data->due_amount,
                            'head_id' => $feeTypes[$data->fee_head],
                            'crdr' => $this->getCrdr($transaction->entry_mode),
                            'br_id' => $transaction->br_id,
                            'head_name' => $data->fee_head,
                        ];
                    }
                }

                // Insert in batches of 1000
                if (count($batchData) >= 1000) {
                    DB::table('FinancialTranDetails')->insert($batchData);
                    $batchData = [];
                }
            }

            // Insert remaining data
            if (!empty($batchData)) {
                DB::table('FinancialTranDetails')->insert($batchData);
            }
        });
    }


    private function getConcessionType($transaction)
    {
        if ($transaction->concession_amount > 0) {
            return 1;
        } elseif ($transaction->scholarship_amount > 0) {
            return 2;
        }
        return null;
    }

    private function getCrdr($entryModeName)
    {
        $creditModes = ['REVDUE', 'SCHOLARSHIP', 'CONCESSION', 'RCPT'];
        return in_array($entryModeName, $creditModes) ? 'C' : 'D';
    }
}
