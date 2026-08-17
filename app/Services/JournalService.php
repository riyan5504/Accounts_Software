<?php

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalItems;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class JournalService
{
    /*
    |--------------------------------------------------------------------------
    | System Account Constants
    |--------------------------------------------------------------------------
    */

    const ACCOUNT_SUPPLIER_PAYABLE      = 'Supplier Payable';
    const ACCOUNT_CUSTOMER_RECEIVABLE   = 'Customer Receivable';

    const ACCOUNT_WORK_IN_PROGRESS      = 'Work In Progress';
    const ACCOUNT_FINISHED_GOODS        = 'Finished Goods';

    const ACCOUNT_RAW_MATERIAL          = 'Raw Material';

    const ACCOUNT_RAW_MATERIAL_EXPENSE  = 'Raw Material Expense';
    const ACCOUNT_PACKAGING_EXPENSE     = 'Packaging Expense';
    const ACCOUNT_LABOR_EXPENSE         = 'Labor Expense';
    const ACCOUNT_DEPRECIATION_EXPENSE  = 'Depreciation Expense';
    const ACCOUNT_UTILITY_EXPENSE       = 'Utility Expense';
    const ACCOUNT_FACTORY_OVERHEAD      = 'Factory Overhead';
    const ACCOUNT_TRANSPORT_EXPENSE     = 'Transport Expense';
    const ACCOUNT_QC_EXPENSE            = 'QC Expense';

    const ACCOUNT_CASH                  = 'Cash';
    const ACCOUNT_BANK                  = 'Bank';



    /*
    | Main Journal Posting
    */

    public function createJournal(array $data)
    {
        DB::transaction(function () use ($data) {

            $this->validateJournal($data);
            $this->removeOldJournal(
                $data['module_type'],
                $data['module_id']
            );
            $journal = JournalEntry::create([
                'company_id'   => $data['company_id'],
                'module_type'  => $data['module_type'],
                'module_id'    => $data['module_id'],
                'reference_no' => $data['reference_no'] ?? null,
                'date'         => $data['date'],
                'particulars'  => $data['particulars'] ?? null,
                'created_by'   => Auth::id(),
            ]);

            foreach ($data['items'] as $item) {
                JournalItems::create([
                    'company_id'       => $data['company_id'],
                    'journal_entry_id' => $journal->id,
                    'account_id' => $this->accountId(
                        $item['account'],
                        $data['company_id']
                    ),
                    'debit'  => $item['debit'] ?? 0,
                    'credit' => $item['credit'] ?? 0,
                    'vendor_id'   => $item['vendor_id'] ?? null,
                    'customer_id' => $item['customer_id'] ?? null,
                ]);
            }
        });
    }

    /*
    | Validate Journal
    */

    private function validateJournal(array $data)
    {
        if (!isset($data['items']) || count($data['items']) == 0) {
            throw new Exception("Journal Items Missing.");
        }

        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($data['items'] as $item) {
            if (!isset($item['account'])) {
                throw new Exception("Account Missing.");
            }
            $debit = (float)($item['debit'] ?? 0);
            $credit = (float)($item['credit'] ?? 0);
            if ($debit < 0 || $credit < 0) {
                throw new Exception("Negative Amount Not Allowed.");
            }
            if ($debit == 0 && $credit == 0) {
                throw new Exception("Debit and Credit both cannot be Zero.");
            }
            $totalDebit += $debit;
            $totalCredit += $credit;
        }

        if (round($totalDebit, 2) != round($totalCredit, 2)) {
            throw new Exception(
                "Journal Not Balanced. Debit = {$totalDebit}, Credit = {$totalCredit}"
            );
        }
    }

    /*
    | Delete Old Journal
    */

    private function removeOldJournal(
        string $moduleType,
        int $moduleId
    ) {
        JournalEntry::where('module_type', $moduleType)
            ->where('module_id', $moduleId)
            ->delete();
    }

    /*
    | Account Helper
    */

    private function accountId($account, $companyId)
    {
        if (is_numeric($account)) {
            $id = Account::where('company_id', $companyId)
                ->where('id', $account)
                ->value('id');

            if (!$id) {
                throw new Exception(
                    "Account ID '{$account}' not found for this company."
                );
            }
            return $id;
        }

        $id = Account::where('company_id', $companyId)
            ->where('account_name', $account)
            ->value('id');
        if (!$id) {
            throw new Exception(
                "Account '{$account}' not found."
            );
        }
        return $id;
    }
}
