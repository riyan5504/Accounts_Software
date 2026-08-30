<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Company;
use App\Models\Expense;
use App\Models\ExpenseItem;
use App\Models\Investment;
use App\Models\JournalEntry;
use App\Models\Partner;
use App\Models\User;
use App\Services\JournalService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function accounts()
    {
        return view('account.account-module');
    }

    public function accountEntry()
    {
        $accounts = Account::withoutTrashed()
            ->orderBy('ac_type')
            ->orderBy('account_name')
            ->get();

        return view('account.account-entry', compact('accounts'));
    }

    public function accountStore(Request $request)
    {
        $request->validate([
            'account_name' => 'required|string|max:255',
            'ac_type' => 'required',
            'ac_cat' => 'nullable|string|max:255',
            'op_balance' => 'nullable|numeric',
        ]);

        Account::create([
            'account_name' => $request->account_name,
            'ac_type' => $request->ac_type,
            'ac_cat' => $request->ac_cat,
            'op_balance' => $request->op_balance ?? 0,
        ]);

        return redirect()->back()->with('success', 'Account created successfully');
    }


    public function accountEdit($id)
    {
        $account = Account::find($id);
        $accounts = Account::orderBy('ac_type', 'asc')
            ->orderBy('account_name', 'asc')
            ->get();
        return view('account.account-edit', compact('accounts', 'account'));
    }

    public function accountUpdate(Request $request, $id)
    {
        $request->validate([
            'account_name' => 'required',
            'ac_type' => 'required',
        ]);

        $account = Account::findOrFail($id);

        $account->update([
            'account_name' => $request->account_name,
            'ac_type' => $request->ac_type,
            'ac_cat' => $request->ac_cat,
            'op_balance' => $request->op_balance,
        ]);

        return redirect('/account/entry')->with('success', 'Account updated');
    }

    public function accountDelete($id)
    {
        $account = Account::findOrFail($id);
        $account->delete();

        return back()->with('success', 'Account moved to trash');
    }

    public function accountTrashList()
    {
        $accounts = Account::onlyTrashed()->latest()->get();

        return view('account.account-trash', compact('accounts'));
    }

    public function restoreAccount($id)
    {
        $account = Account::onlyTrashed()->findOrFail($id);

        $account->restore();

        return redirect()->route('account.account-add')->with('success', 'Account restored successfully!');
    }

    public function forceAccountDelete($id)
    {
        $account = Account::onlyTrashed()->findOrFail($id);

        $account->forceDelete(); // 🔥 permanently delete

        return back()->with('success', 'Account permanently deleted!');
    }

    public function expenseEntry()
    {
        $lastVoucher = Expense::latest('id')->first();
        $nextNo = 'EXP-' . str_pad(($lastVoucher?->id ?? 0) + 1, 2, '0', STR_PAD_LEFT);

        $accounts = Account::all();
        $users = User::all();

        return view('account.expense', compact('nextNo', 'accounts', 'users'));
    }
    public function expenceStore(Request $request)
    {
        DB::transaction(function () use ($request) {

            // 1️⃣ Save Expense
            $expense = Expense::create([
                'date' => $request->date,
                'voucher_no' => $request->voucher_no,
                'reference_no' => $request->reference_no,
                'expense_account_id' => $request->expense_account_id,
                'payment_account_id' => $request->payment_account_id,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_status,
                'sub_total' => $request->sub_total,
                'tax_rate' => $request->tax_rate ?? 0,
                'tax_amount' => $request->tax_amount ?? 0,
                'total_amount' => $request->total_amount,
                'paid_amount' => $request->paid_amount ?? $request->total_amount,
                'due_amount' => $request->due_amount ?? 0,
                'pay_to' => $request->pay_to,
                'expense_type' => $request->expense_type ?? null,
                'created_by' => auth()->id(),
                'branch_id' => $request->branch_id ?? null,
            ]);

            /// 2️⃣ Save Items
            $items = $request->particulars; // array
            $qtys = $request->qty;
            $rates = $request->rate;
            $amounts = $request->amount;

            foreach ($items as $index => $item) {
                ExpenseItem::create([
                    'expense_id' => $expense->id,
                    'particulars' => $item,
                    'qty' => $qtys[$index],
                    'rate' => $rates[$index],
                    'amount' => $amounts[$index],
                ]);
            }
        });

        return redirect()->back()->with('success', 'Expense & Journal entries created successfully!');
    }

    public function partnerEntry()
    {
        $partners = Partner::get();
        return view('account.partners', compact('partners'));
    }

    public function partnerStore(Request $request)
    {
        $request->validate([
            'p_name' => 'required|string|max:255',
            'p_phone' => [
                'required',
                'string',
                Rule::unique('partners')->where(function ($q) {
                    return $q->where('company_id', auth()->user()->company_id);
                })
            ],
            'p_email' => [
                'required',
                'email',
                Rule::unique('partners')->where(function ($q) {
                    return $q->where('company_id', auth()->user()->company_id);
                })
            ],
            'p_address' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // 1. Partner create
            $partner = Partner::create([
                'company_id' => auth()->user()->company_id,
                'p_name' => $request->p_name,
                'p_phone' => $request->p_phone,
                'p_email' => $request->p_email,
                'p_address' => $request->p_address,
            ]);
            // 2. Capital Account create
            $account = Account::create([
                'account_name' => $partner->p_name . ' Capital',
                'ac_type' => 'equity',
                'ac_cat' => 'Capital Equity',
                'company_id' => auth()->user()->company_id,
            ]);
            // 3. Link account to partner
            $partner->update([
                'account_id' => $account->id
            ]);
            DB::commit();
            return back()->with('success', 'Partner & Capital Account created successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    public function investmentEntry()
    {
        $companyId = auth()->user()->company_id;
        $partners = Partner::where('company_id', $companyId)->get();
        $accounts = Account::where('company_id', $companyId)->get();

        return view('account.investment-create', compact('partners', 'accounts'));
    }

    public function investmentStore(Request $request, JournalService $journalService)
    {
        $request->validate([
            'date' => 'required|date',
            'partner_id' => 'required',
            'amount' => 'required|numeric|min:1',
            'invest_type' => 'required|in:capital,loan',
            'debit_account_id' => 'required|integer',
            'credit_account_id' => 'required|integer',
            'attachment' => 'nullable|file|max:5120',
        ]);

        DB::transaction(function () use ($request, $journalService) {

            /*| File Upload*/

            $attachment = null;

            if ($request->hasFile('attachment')) {

                // Investment attachment folder
                $uploadPath = public_path('backend/dist/assets/invest');

                // Folder না থাকলে তৈরি করবে
                if (!File::exists($uploadPath)) {
                    File::makeDirectory($uploadPath, 0755, true);
                }

                // Original extension সহ unique filename
                $file = $request->file('attachment');
                $fileName = 'invest_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // File move করে নির্দিষ্ট folder এ রাখবে
                $file->move($uploadPath, $fileName);

                // Database এ শুধু filename রাখবে
                $attachment = $fileName;
            }
            /*Investment Save*/
            $investment = Investment::create([
                'company_id' => auth()->user()->company_id,
                'partner_id' => $request->partner_id,
                'amount' => $request->amount,
                'attachment' => $attachment,
                'invest_type' => $request->invest_type,
                'debit_account_id' => $request->debit_account_id,
                'credit_account_id' => $request->credit_account_id,
                'reference' => $request->reference,
                'note' => $request->note,
                'date' => $request->date,
                'created_by' => auth()->id(),
            ]);

            /*Journal Entry
        | Capital:
        | Debit  = Cash / Bank
        | Credit = Capital / Equity Account
        |
        | Loan:
        | Debit  = Cash / Bank
        | Credit = Loan / Liability Account
        |
        */
            $particulars = $request->invest_type === 'capital'
                ? 'Capital Investment'
                : 'Loan Investment';
            $journalService->createJournal([
                'company_id' => auth()->user()->company_id,
                'module_type' => 'investment',
                'module_id' => $investment->id,
                'reference_no' => $request->reference,
                'date' => $request->date,
                'particulars' => $particulars,
                'items' => [
                    // Debit: টাকা Cash/Bank-এ এসেছে
                    [
                        'account' => $request->debit_account_id,
                        'debit' => $request->amount,
                        'credit' => 0,
                    ],

                    // Credit: Capital অথবা Loan
                    [
                        'account' => $request->credit_account_id,
                        'debit' => 0,
                        'credit' => $request->amount,
                    ],
                ],
            ]);
        });

        return back()->with(
            'success',
            'Investment saved and journal created successfully'
        );
    }

    public function investmentList()
    {
        $companyId = auth()->user()->company_id;

        $investments = Investment::with('partner')
            ->where('company_id', $companyId)
            ->orderBy('date', 'desc')
            ->get();

        return view('account.invest-list', compact('investments'));
    }

    public function investmentEdit($id)
    {
        $companyId = auth()->user()->company_id;
        $accounts = Account::where('company_id', $companyId)->get();
        $partners = Partner::where('company_id', $companyId)->get();

        $investment = Investment::with('partner')
            ->where('company_id', $companyId)
            ->findOrFail($id);

        return view('account.invest-edit', compact('investment', 'accounts', 'partners'));
    }

    public function investmentUpdate(Request $request, $id, JournalService $journalService)
    {
        $companyId = auth()->user()->company_id;

        $request->validate([
            'date' => 'required|date',
            'partner_id' => 'required|integer',
            'amount' => 'required|numeric|min:1',
            'invest_type' => 'required|in:capital,loan',
            'debit_account_id' => 'required|integer',
            'credit_account_id' => 'required|integer',
            'reference' => 'nullable|string|max:255',
            'note' => 'nullable|string',
            'attachment' => 'nullable|file|max:5120',
        ]);

        /* Validate Investment */
        $investment = Investment::where('company_id', $companyId)
            ->findOrFail($id);

        /* Validate Partner */
        $partner = Partner::where('company_id', $companyId)
            ->findOrFail($request->partner_id);

        /*
        |--------------------------------------------------------------------------
        | Validate Debit Account
        |--------------------------------------------------------------------------
        | Debit must be Asset
        */
        $debitAccount = Account::where('company_id', $companyId)
            ->where('id', $request->debit_account_id)
            ->where('ac_type', 'asset')
            ->first();

        if (!$debitAccount) {
            return back()
                ->withErrors([
                    'debit_account_id' => 'Invalid debit account.'
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Credit Account
        |--------------------------------------------------------------------------
        | Capital = Equity
        | Loan    = Liability
        */

        $requiredCreditType =
            $request->invest_type === 'capital'
            ? 'equity'
            : 'liability';

        $creditAccount = Account::where('company_id', $companyId)
            ->where('id', $request->credit_account_id)
            ->where('ac_type', $requiredCreditType)
            ->first();
        if (!$creditAccount) {
            return back()
                ->withErrors([
                    'credit_account_id' =>
                    'Invalid credit account for selected investment type.'
                ])
                ->withInput();
        }

        /* Update Investment + Journal */
        DB::transaction(function () use ($request, $investment, $journalService, $companyId) {

            /* Attachment */
            $attachment = $investment->attachment;

            if ($request->hasFile('attachment')) {
                $uploadPath = public_path('backend/dist/assets/invest');

                // Folder না থাকলে তৈরি করবে
                if (!File::exists($uploadPath)) {
                    File::makeDirectory($uploadPath, 0755, true);
                }

                // Delete old attachment
                if ($investment->attachment) {
                    $oldFile = $uploadPath . '/' . $investment->attachment;
                    if (File::exists($oldFile)) {
                        File::delete($oldFile);
                    }
                }

                // Upload new attachment
                $file = $request->file('attachment');
                $fileName = 'invest_' . time() . '_' . uniqid() . '.' .
                    $file->getClientOriginalExtension();
                $file->move($uploadPath, $fileName);

                // Database এ শুধু filename থাকবে
                $attachment = $fileName;
            }

            /* Update Investment */
            $investment->update([
                'partner_id' => $request->partner_id,
                'amount' => $request->amount,
                'attachment' => $attachment,
                'invest_type' => $request->invest_type,
                'debit_account_id' => $request->debit_account_id,
                'credit_account_id' => $request->credit_account_id,
                'reference' => $request->reference,
                'note' => $request->note,
                'date' => $request->date,
            ]);

            /* Create New Investment Journal */
            $particulars = $request->invest_type === 'capital'
                ? 'Capital Investment'
                : 'Loan Investment';
            $journalService->createJournal([
                'company_id' => auth()->user()->company_id,
                'module_type' => 'investment',
                'module_id' => $investment->id,
                'reference_no' => $request->reference,
                'date' => $request->date,
                'particulars' => $particulars,
                'items' => [
                    // Debit: টাকা Cash/Bank-এ এসেছে
                    [
                        'account' => $request->debit_account_id,
                        'debit' => $request->amount,
                        'credit' => 0,
                    ],

                    // Credit: Capital অথবা Loan
                    [
                        'account' => $request->credit_account_id,
                        'debit' => 0,
                        'credit' => $request->amount,
                    ],
                ],
            ]);
        });

        return redirect()
            ->route('account.investment.list')
            ->with('success', 'Investment and journal updated successfully!');
    }

    public function investmentDelete($id)
    {
        DB::transaction(function () use ($id) {
            // CompanyScope automatically applies company_id
            $investment = Investment::findOrFail($id);

            // Delete related journal
            JournalEntry::where('module_type', 'investment')
                ->where('module_id', $investment->id)
                ->delete();

            // Delete attachment
            if ($investment->attachment) {
                $file = storage_path(
                    'app/public/' . $investment->attachment
                );
                if (file_exists($file)) {
                    unlink($file);
                }
            }
            // Delete investment
            $investment->delete();
        });

        return redirect()
            ->route('account.investment.list')
            ->with('success', 'Investment and related journal deleted successfully!');
    }

    public function investmentReport(Request $request)
    {
        $companyId = auth()->user()->company_id;

        /* Investment Details Query */
        $query = Investment::with('partner')
            ->where('company_id', $companyId);

        // Date From
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        // Date To
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // Partner
        if ($request->filled('partner_id')) {
            $query->where('partner_id', $request->partner_id);
        }

        // Investment Type
        if (
            $request->filled('invest_type') &&
            $request->invest_type !== 'all'
        ) {
            $query->where('invest_type', $request->invest_type);
        }

        $investments = $query
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();


        /* Investment Summary */
        $totalCapital = $investments
            ->where('invest_type', 'capital')
            ->sum('amount');

        $totalPartnerLoan = $investments
            ->where('invest_type', 'loan')
            ->sum('amount');

        $totalInvestment = $totalCapital + $totalPartnerLoan;

        /* Partner Investment Ledger */
        $partnerLedger = $investments
            ->groupBy('partner_id')
            ->map(function ($partnerInvestments) {
                $partner = $partnerInvestments->first()->partner;
                $capital = $partnerInvestments
                    ->where('invest_type', 'capital')
                    ->sum('amount');
                $loan = $partnerInvestments
                    ->where('invest_type', 'loan')
                    ->sum('amount');
                return [
                    'partner_id' => $partner?->id,
                    'partner_name' => $partner?->p_name ?? 'N/A',
                    'capital' => $capital,
                    'loan' => $loan,
                    'total' => $capital + $loan,
                ];
            })
            ->values();

        /* Partners */
        $partners = Partner::where('company_id', $companyId)
            ->orderBy('p_name')
            ->get();

        return view(
            'account.investment-report',
            compact(
                'investments',
                'partnerLedger',
                'partners',
                'totalCapital',
                'totalPartnerLoan',
                'totalInvestment'
            )
        );
    }

    public function investmentReportPdf(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = Investment::with('partner')
            ->where('company_id', $companyId);

        // Date From
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        // Date To
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // Partner
        if ($request->filled('partner_id')) {
            $query->where('partner_id', $request->partner_id);
        }

        // Investment Type
        if (
            $request->filled('invest_type') &&
            $request->invest_type !== 'all'
        ) {
            $query->where('invest_type', $request->invest_type);
        }

        $investments = $query
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // Summary
        $totalCapital = $investments
            ->where('invest_type', 'capital')
            ->sum('amount');

        $totalPartnerLoan = $investments
            ->where('invest_type', 'loan')
            ->sum('amount');

        $totalInvestment = $totalCapital + $totalPartnerLoan;

        // Partner Wise Ledger
        $partnerLedger = $investments
            ->groupBy('partner_id')
            ->map(function ($partnerInvestments) {

                $partner = $partnerInvestments->first()->partner;

                $capital = $partnerInvestments
                    ->where('invest_type', 'capital')
                    ->sum('amount');

                $loan = $partnerInvestments
                    ->where('invest_type', 'loan')
                    ->sum('amount');

                return [
                    'partner_id'   => $partner?->id,
                    'partner_name' => $partner?->p_name ?? 'N/A',
                    'capital'      => $capital,
                    'loan'         => $loan,
                    'total'        => $capital + $loan,
                ];
            })
            ->values();

        // Selected Partner
        $selectedPartner = null;

        if ($request->filled('partner_id')) {
            $selectedPartner = Partner::where('company_id', $companyId)
                ->find($request->partner_id);
        }

        // Company
        $company = Company::where('id', $companyId)->first();

        // Company Logo
        $logoPath = null;

        if ($company && $company->logo) {

            $path = public_path(
                'backend/dist/assets/img/' . $company->logo
            );

            if (File::exists($path)) {
                $logoPath = $path;
            }
        }

        // Generate PDF
        $pdf = Pdf::loadView(
            'account.investment-report-pdf',
            compact(
                'investments',
                'partnerLedger',
                'totalCapital',
                'totalPartnerLoan',
                'totalInvestment',
                'selectedPartner',
                'logoPath',
                'company'
            )
        );

        return $pdf->download('investment-report.pdf');
    }
}
