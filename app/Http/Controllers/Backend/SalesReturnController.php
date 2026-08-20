<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Item;
use App\Models\SalesReturn;
use App\Models\User;
use App\Services\InventoryService;
use App\Services\JournalService;
use Illuminate\Http\Request;

class SalesReturnController extends Controller
{
    protected InventoryService $inventoryService;
    protected JournalService $journalService;

    public function __construct(InventoryService $inventoryService, JournalService $journalService)
    {
        $this->middleware('auth');
        $this->inventoryService = $inventoryService;
        $this->journalService = $journalService;
    }

    public function salesReturn()
    {
        // সর্বশেষ item এর code বের করুন
        $lastItem = Item::latest('id')->first();

        // যদি কিছু না থাকে, তাহলে 0 থেকে শুরু হবে
        if ($lastItem && preg_match('/\d+$/', $lastItem->item_code, $matches)) {
            $lastSerial = intval($matches[0]);
        } else {
            $lastSerial = 0;
        }

        // সর্বশেষ invoice_no বের করা
        $lastReturn = SalesReturn::latest()->first();

        if ($lastReturn && $lastReturn->invoice_no) {
            // সংখ্যা অংশ বের করা (যেমন: PRC-0005 → 5)
            preg_match('/\d+/', $lastReturn->invoice_no, $matches);
            $number = isset($matches[0]) ? intval($matches[0]) : 0;
            $newNumber = $number + 1;
        } else {
            $newNumber = 1;
        }

        // নতুন invoice_no তৈরি করা (PRC-0001 ফরম্যাটে)
        $newReturnNo = 'RTN-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        $accounts = Account::all();
        $users = User::all();
        $customers = Customer::select('customers.id', 'customers.c_name')
            ->join('sales', 'customers.id', '=', 'sales.customer_id')
            ->where('customers.company_id', auth()->user()->company_id)
            ->where('sales.company_id', auth()->user()->company_id)
            ->distinct()
            ->orderBy('customers.c_name')
            ->get();
        return view('sales.sales-return', compact('accounts', 'users', 'newReturnNo', 'lastSerial', 'customers'));
    }
}
