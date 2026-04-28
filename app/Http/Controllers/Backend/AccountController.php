<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Expense;
use App\Models\ExpenseItem;
use App\Models\Partner;
use App\Models\User;
use App\Services\JournalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    // public function accountEntry()
    // {
    //     $accounts = Account::orderBy('ac_type', 'asc')
    //         ->orderBy('account_name', 'asc')
    //         ->get();
    //     return view('account.account-entry', compact('accounts'));
    // }

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

    // public function accountStore(Request $request)
    // {
    //     $request->validate([
    //         'account_name' => 'required|unique:accounts,account_name',
    //     ]);

    //     $account = new Account();

    //     $account->company_id = auth()->user()->company_id;
    //     $account->account_name = $request->account_name;
    //     $account->ac_cat = $request->ac_cat;
    //     $account->ac_type = $request->ac_type;
    //     $account->op_balance = $request->op_balance;

    //     $account->save();

    //     $accounts = Account::orderBy('ac_type', 'asc')
    //         ->orderBy('account_name', 'asc')
    //         ->get();
    //     return view('account.account-entry', compact('accounts'));
    // }


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
    // public function accountUpdate(Request $request, $id)
    // {
    //     $account = Account::find($id);

    //     $account->account_name = $request->account_name;
    //     $account->ac_cat = $request->ac_cat;
    //     $account->ac_type = $request->ac_type;
    //     $account->op_balance = $request->op_balance;

    //     $account->save();
    //     return redirect('/account/entry');
    // }


    public function accountDelete($id)
    {
        $account = Account::findOrFail($id);
        $account->delete();

        return back()->with('success', 'Account moved to trash');
    }

    public function accountTrashList()
    {
        $accounts = Account::onlyTrashed()->latest()->get();

        return view('item.trash', compact('items'));
    }

    public function restore($id)
    {
        $item = Item::onlyTrashed()->findOrFail($id);

        $item->restore();

        return redirect()->route('item.item-add')->with('success', 'Item restored successfully!');
    }

    public function forceDelete($id)
    {
        $item = Item::onlyTrashed()->findOrFail($id);

        $item->forceDelete(); // 🔥 permanently delete

        return back()->with('success', 'Item permanently deleted!');
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

            // 3️⃣ Create Journal Entries (Double Entry)
            // JournalService::createEntry(
            //     'expense',
            //     $expense->id,
            //     [
            //         ['account_id' => $expense->expense_account_id, 'debit' => $expense->total_amount],
            //         ['account_id' => $expense->payment_account_id, 'credit' => $expense->total_amount],
            //     ],
            //     $expense->date,
            //     'Expense payment to ' . $expense->pay_to
            // );
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
            'p_name'     => 'required|string|max:255',
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
                'ac_type' => 'Equity',
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
        $partners = Partner::get();
        $accounts = Account::get();
        return view('account.investment-create', compact('partners', 'accounts'));
    }
}
