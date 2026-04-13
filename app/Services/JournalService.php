<?php

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class JournalService
{
    /**
     * Remove old entries for a module
     */
    public function removeOldEntries($moduleType, $moduleId)
    {
        JournalEntry::where('module_type', $moduleType)
            ->where('module_id', $moduleId)
            ->delete();
    }


    /**
     * Purchase Journal Entry
     */
    public function createPurchaseJournal($purchase)
    {
        // পুরনো জার্নাল থাকলে মুছে ফেলি
        $this->removeOldEntries('purchase', $purchase->id);

        $date = $purchase->date ?? Carbon::today();

        $debitAccount   = $purchase->debit_account_id;   // Expense / Asset / Inventory
        $cashAccount    = $purchase->payment_account_id;   // Cash / Bank
        $vendorAccount = $this->vendorPayableAccountId($purchase->vendor); // Vendor Payable

        $total = $purchase->grand_total;
        $paid  = $purchase->paid_amt;
        $due   = $purchase->due_amt;

        $user  = auth()->id();


        /*
        |--------------------------------------------------------------------------
        | CASE-1 : FULLY PAID
        |--------------------------------------------------------------------------
        |
        | Dr  Expense/Asset/Inventory
        | Cr  Cash/Bank
        |
        */
        if ($purchase->payment_status === 'paid') {

            // Debit Entry
            JournalEntry::create([
                'module_type' => 'purchase',
                'module_id' => $purchase->id,
                'account_id' => $debitAccount,
                'debit' => $total,
                'credit' => 0,
                'vendor_id' => $purchase->vendor_id,
                'reference_no' => $purchase->reference_no,
                'date' => $date,
                'transaction_type' => $purchase->account_cat,
                'particulars' => 'Purchase fully paid',
                'created_by' => $user
            ]);

            // Credit Entry
            JournalEntry::create([
                'module_type' => 'purchase',
                'module_id' => $purchase->id,
                'account_id' => $cashAccount,
                'debit' => 0,
                'credit' => $total,
                'vendor_id' => $purchase->vendor_id,
                'reference_no' => $purchase->reference_no,
                'date' => $date,
                'transaction_type' => $purchase->account_cat,
                'particulars' => 'Purchase paid by cash/bank',
                'created_by' => $user
            ]);
        }



        /*
        |--------------------------------------------------------------------------
        | CASE-2 : UNPAID (FULL CREDIT)
        |--------------------------------------------------------------------------
        |
        | Dr  Expense/Asset/Inventory
        | Cr  Vendor Payable
        |
        */
        if ($purchase->payment_status === 'unpaid') {

            JournalEntry::create([
                'module_type' => 'purchase',
                'module_id' => $purchase->id,
                'account_id' => $debitAccount,
                'debit' => $total,
                'credit' => 0,
                'vendor_id' => $purchase->vendor_id,
                'reference_no' => $purchase->reference_no,
                'date' => $date,
                'transaction_type' => $purchase->account_cat,
                'particulars' => 'Purchase on Vendor Credit',
                'created_by' => $user
            ]);

            JournalEntry::create([
                'module_type' => 'purchase',
                'module_id' => $purchase->id,
                'account_id' => $vendorAccount,
                'debit' => 0,
                'credit' => $total,
                'vendor_id' => $purchase->vendor_id,
                'reference_no' => $purchase->reference_no,
                'date' => $date,
                'transaction_type' => $purchase->account_cat,
                'particulars' => 'Vendor Payable recognized',
                'created_by' => $user
            ]);
        }




        /*
        |--------------------------------------------------------------------------
        | CASE-3 : PARTIAL PAID
        |--------------------------------------------------------------------------
        |
        | Dr  Expense/Asset/Inventory   (Full Amount)
        | Cr  Cash/Bank                 (Paid)
        | Cr  Vendor Payable            (Due)
        |
        */
        if ($purchase->payment_status === 'partial') {

            // Debit Full Amount
            JournalEntry::create([
                'module_type' => 'purchase',
                'module_id' => $purchase->id,
                'account_id' => $debitAccount,
                'debit' => $total,
                'credit' => 0,
                'vendor_id' => $purchase->vendor_id,
                'reference_no' => $purchase->reference_no,
                'date' => $date,
                'transaction_type' => $purchase->account_cat,
                'particulars' => 'Vendor payable',
                'created_by' => $user
            ]);

            // Credit Paid Amount
            JournalEntry::create([
                'module_type' => 'purchase',
                'module_id' => $purchase->id,
                'account_id' => $cashAccount,
                'debit' => 0,
                'credit' => $paid,
                'vendor_id' => $purchase->vendor_id,
                'reference_no' => $purchase->reference_no,
                'date' => $date,
                'transaction_type' => $purchase->account_cat,
                'particulars' => 'Purchase Amount partial Paid',
                'created_by' => $user
            ]);

            // Credit Due Amount
            JournalEntry::create([
                'module_type' => 'purchase',
                'module_id' => $purchase->id,
                'account_id' => $vendorAccount,
                'debit' => 0,
                'credit' => $due,
                'vendor_id' => $purchase->vendor_id,
                'reference_no' => $purchase->reference_no,
                'date' => $date,
                'transaction_type' => $purchase->account_cat,
                'particulars' => 'Vendor payable remaining',
                'created_by' => $user
            ]);
        }
    }



    /**
     * Vendor Payable Account ID
     */
    private function vendorPayableAccountId($vendor)
    {
        return $vendor->account_id
            ?? Account::where('account_name', 'Accounts Payable')
            ->where('ac_type', 'Liability')
            ->value('id');
    }

    public function createProductionJournal($production, $sectionTotal)
    {
        $this->removeOldEntries('production', $production->id);

        $date = $production->date;
        $user = Auth::id();

        $wipAccount = Account::where('account_name', 'Work In Process')->value('id');

        $sections = [
            'Raw Material Expense'   => $sectionTotal->raw_grand_price,
            'Packaging Expense'      => $sectionTotal->pack_grand_price,
            'Labor Expense'          => $sectionTotal->labor_grand_price,
            'Depreciation Expense'   => $sectionTotal->depreciation_grand_price,
            'Utility Expense'        => $sectionTotal->utility_grand_price,
            'Factory Overhead'       => $sectionTotal->overhead_grand_price,
            'Transport Expense'      => $sectionTotal->transport_grand_price,
            'QC Expense'             => $sectionTotal->qc_grand_price,
        ];

        foreach ($sections as $accountName => $amount) {
            if (!$amount || $amount <= 0) continue;

            $expenseAccount = Account::where('account_name', $accountName)->value('id');

            // DR Expense
            JournalEntry::create([
                'module_type' => 'production',
                'module_id'   => $production->id,
                'account_id'  => $expenseAccount,
                'debit'       => $amount,
                'credit'      => 0,
                'date'        => $date,
                'particulars' => "Production {$accountName}",
                'created_by'  => $user,
            ]);

            // CR WIP
            JournalEntry::create([
                'module_type' => 'production',
                'module_id'   => $production->id,
                'account_id'  => $wipAccount,
                'debit'       => 0,
                'credit'      => $amount,
                'date'        => $date,
                'particulars' => "WIP credited for {$accountName}",
                'created_by'  => $user,
            ]);
        }
    }
}
