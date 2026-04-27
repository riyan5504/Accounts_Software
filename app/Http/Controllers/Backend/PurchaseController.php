<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Category;
use App\Models\InventoryLedger;
use App\Models\Item;
use App\Models\JournalEntry;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\User;
use App\Models\Vendor;
use App\Services\InventoryService;
use App\Services\JournalService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    protected InventoryService $inventory;
    protected JournalService $journal;

    public function __construct(InventoryService $inventory, JournalService $journal)
    {
        $this->middleware('auth');
        $this->inventory = $inventory;
        $this->journal   = $journal;
    }

    public function purchase()
    {
        return view('purchase.purchase-modiul');
    }

    public function purchaseEntry()
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
        $lastPurchase = Purchase::latest()->first();

        if ($lastPurchase && $lastPurchase->invoice_no) {
            // সংখ্যা অংশ বের করা (যেমন: PRC-0005 → 5)
            preg_match('/\d+/', $lastPurchase->invoice_no, $matches);
            $number = isset($matches[0]) ? intval($matches[0]) : 0;
            $newNumber = $number + 1;
        } else {
            $newNumber = 1;
        }

        // নতুন invoice_no তৈরি করা (PRC-0001 ফরম্যাটে)
        $newPurchaseNo = 'PRC-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        $accounts = Account::all();
        $users = User::all();

        return view('purchase.create', compact('accounts', 'users', 'newPurchaseNo', 'lastSerial'));
    }

    public function getAccountsByPurchaseType($type)
    {
        switch ($type) {

            case 'expense':
                $accounts = Account::where('ac_type', 'Expense')
                    ->select('ac_cat')
                    ->distinct()
                    ->get();
                break;

            case 'asset':
            case 'inventory':
            case 'prepaid':
            case 'cwip':
                $accounts = Account::where('ac_type', 'Asset')
                    ->select('ac_cat')
                    ->distinct()
                    ->get();
                break;

            default:
                $accounts = collect();
        }

        return response()->json($accounts);
    }



    public function store(Request $request)
    {
        // ---------- Base Validation ----------
        $request->validate([
            'grand_total' => 'required|numeric|min:0',
            'paid_amt' => 'nullable|numeric|min:0',
            'payment_status' => 'required|in:paid,partial,unpaid',
        ]);

        $grandTotal = floatval($request->grand_total);
        $paid = floatval($request->paid_amt ?? 0);
        $status = $request->payment_status;

        // ---------- Business Rules ----------
        if ($status === 'paid' && $paid != $grandTotal) {
            return back()->withErrors(['paid_amt' => 'Paid হলে সম্পূর্ণ টাকা দিতে হবে'])
                ->withInput();
        }

        if ($status === 'partial' && ($paid <= 0 || $paid >= $grandTotal)) {
            return back()->withErrors(['paid_amt' => 'Partial হলে Paid amount total এর চেয়ে কম এবং 0 এর বেশি হতে হবে'])
                ->withInput();
        }

        if ($status === 'unpaid' && $paid != 0) {
            return back()->withErrors(['paid_amt' => 'Unpaid হলে Paid amount অবশ্যই 0 হতে হবে'])->withInput();
        }

        // ---------- Server-side Due Calculation ----------
        $due = $grandTotal - $paid;


        DB::transaction(function () use ($request, $paid, $grandTotal, $due) {

            // Vendor create / update
            $vendor = Vendor::updateOrCreate(
                ['v_name' => $request->v_name, 'phone' => $request->phone],
                [
                    'email' => $request->email,
                    'address' => $request->address,
                ]
            );

            // Purchase create
            $purchase = Purchase::create([
                'vendor_id' => $vendor->id,
                'date' => $request->date,
                'invoice_no' => $request->invoice_no,
                'sub_total' => $request->sub_total,
                'dis_percent' => $request->dis_percent,
                'vat_amt' => $request->vat_amt,
                'dis_amt' => $request->dis_amt,
                'grand_total' => $grandTotal,
                'paid_amt' => $paid,
                'due_amt' => $due,
                'reference_no' => $request->reference_no,
                'purchase_type' => $request->purchase_type,
                'account_cat' => $request->account_cat,
                'debit_account_id' => $request->debit_account_id,
                'payment_account_id' => $request->payment_account_id,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_status,
                'pay_to' => $request->pay_to,
                'created_by' => auth()->id(),
            ]);


            // Item Loop
            foreach ($request->item_name as $key => $itemName) {

                $category = Category::firstOrCreate([
                    'cat_name' => $request->cat_name[$key],
                ]);

                $item = Item::updateOrCreate(
                    [
                        'item_name' => $itemName,
                        'cat_id' => $category->id,
                    ],
                    [
                        'item_code' => $request->item_code[$key] ?? null,
                        'size' => $request->size[$key],
                        'unit_price' => $request->unit_price[$key],
                    ]
                );

                $qty   = (int) $request->qty[$key];
                $unit_price = (float) $request->unit_price[$key];


                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'cat_id' => $category->id,
                    'item_id' => $item->id,
                    'qty' => $qty,
                    'unit_price' => $unit_price,
                    'price' => $qty * $unit_price,
                    'vat_percent' => $request->vat_percent[$key],
                    'vat_amount' => $request->vat_amount[$key],
                    'total_price' => $request->total_price[$key],

                ]);
            }

            // ✅ Journal
            $this->journal->createPurchaseJournal($purchase);

            // ✅ Inventory
            $this->inventory->stockInFromPurchase($purchase->load('purchaseItems'), auth()->id());
        });


        return redirect()->back()->with('success', 'Purchase saved successfully!');
    }

    public function purchaseList(Request $request)
    {
        if ($request->type == 'all') {
            $request->merge([
                'vendor_id' => null,
                'item_id' => null
            ]);
        }
        $query = Purchase::with(['vendor', 'purchaseItems.item']);

        // Date filter
        if ($request->from_date && $request->to_date) {
            $query->whereBetween('date', [$request->from_date, $request->to_date]);
        }

        // Supplier filter
        if ($request->type == 'supplier' && $request->vendor_id) {
            $query->where('vendor_id', $request->vendor_id);
        }

        // Item filter
        if ($request->type == 'item' && $request->item_id) {
            $query->whereHas('purchaseItems', function ($q) use ($request) {
                $q->where('item_id', $request->item_id);
            });
        }

        $purchases = $query->latest()->get();

        $vendors = Vendor::all();
        $items = Item::all();

        return view('purchase.purchase-list', compact('purchases', 'vendors', 'items'));
    }

    public function purchaseEdit($id)
    {
        // সর্বশেষ item এর code বের করুন
        $lastItem = Item::latest('id')->first();

        // যদি কিছু না থাকে, তাহলে 0 থেকে শুরু হবে
        if ($lastItem && preg_match('/\d+$/', $lastItem->item_code, $matches)) {
            $lastSerial = intval($matches[0]);
        } else {
            $lastSerial = 0;
        }
        $accounts = Account::all();
        $purchase = Purchase::with(['vendor', 'user', 'purchaseItems.item.category'])->find($id);
        return view('purchase.purchase-edit', compact('purchase', 'lastSerial', 'accounts'));
    }

    public function purchaseUpdate(Request $request, $id)
    {
        // ---------- Base Validation ----------
        $request->validate([
            'grand_total' => 'required|numeric|min:0',
            'paid_amt' => 'nullable|numeric|min:0',
            'payment_status' => 'required|in:paid,partial,unpaid',
            'item_name.*' => 'required|string',
            'qty.*'       => 'required|numeric|min:0.01',
            'price.*'     => 'required|numeric|min:0.01',

        ]);

        $grandTotal = floatval($request->grand_total);
        $paid = floatval($request->paid_amt ?? 0);
        $status = $request->payment_status;

        // ---------- Business Rules ----------
        if ($status === 'paid' && $paid != $grandTotal) {
            return back()->withErrors(['paid_amt' => 'Paid হলে সম্পূর্ণ টাকা দিতে হবে'])
                ->withInput();
        }

        if ($status === 'partial' && ($paid <= 0 || $paid >= $grandTotal)) {
            return back()->withErrors(['paid_amt' => 'Partial হলে Paid amount total এর চেয়ে কম এবং 0 এর বেশি হতে হবে'])
                ->withInput();
        }

        if ($status === 'unpaid' && $paid != 0) {
            return back()->withErrors(['paid_amt' => 'Unpaid হলে Paid amount অবশ্যই 0 হতে হবে'])->withInput();
        }

        // ---------- Server-side Due Calculation ----------
        $due = $grandTotal - $paid;


        DB::transaction(function () use ($request, $id, $paid, $grandTotal, $due) {

            $purchase = Purchase::find($id);

            $vendor = Vendor::updateOrCreate(
                ['v_name' => $request->v_name, 'phone' => $request->phone],
                [
                    'email' => $request->email,
                    'address' => $request->address,
                ]
            );

            $purchase->update([
                'vendor_id' => $vendor->id,
                'date' => $request->date,
                'invoice_no' => $request->invoice_no,
                'sub_total' => $request->sub_total,
                'dis_percent' => $request->dis_percent,
                'vat_amt' => $request->vat_amt,
                'dis_amt' => $request->dis_amt,
                'grand_total' => $grandTotal,
                'paid_amt' => $paid,
                'due_amt' => $due,
                'reference_no' => $request->reference_no,
                'purchase_type' => $request->purchase_type,
                'account_cat' => $request->account_cat,
                'debit_account_id' => $request->debit_account_id,
                'payment_account_id' => $request->payment_account_id,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_status,
                'pay_to' => $request->pay_to,
                'created_by' => auth()->id(),
            ]);

            // পুরনো item মুছে ফেলি
            $purchase->purchaseItems()->delete();


            // আবার নতুন item create করি
            foreach ($request->item_name as $key => $itemName) {

                $category = Category::firstOrCreate([
                    'cat_name' => $request->cat_name[$key],
                ]);

                $item = Item::updateOrCreate(
                    [
                        'item_name' => $itemName,
                        'cat_id' => $category->id,
                    ],
                    [
                        'item_code' => $request->item_code[$key] ?? null,
                        'size' => $request->size[$key],
                        'unit_price' => $request->unit_price[$key],
                    ]
                );

                $qty   = (int) $request->qty[$key];
                $unit_price = (float) $request->unit_price[$key];


                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'cat_id' => $category->id,
                    'item_id' => $item->id,
                    'qty' => $qty,
                    'unit_price' => $unit_price,
                    'price' => $qty * $unit_price,
                    'vat_percent' => $request->vat_percent[$key],
                    'vat_amount' => $request->vat_amount[$key],
                    'total_price' => $request->total_price[$key],
                ]);
            }

            $this->journal->createPurchaseJournal($purchase);
            $this->inventory->stockInFromPurchase($purchase, auth()->id());
        });

        return redirect('/purchase/list');
    }


    public function purchaseDelete($id)
    {
        DB::transaction(function () use ($id) {

            $purchase = Purchase::with('purchaseItems')->findOrFail($id);

            // 1️⃣ Related journal entries delete
            $this->journal->removeOldEntries('purchase', $purchase->id);
            $this->inventory->removeOldStock('purchase', $purchase->id);


            // 2️⃣ Purchase items delete
            $purchase->purchaseItems()->delete();


            // 3️⃣ Purchase delete
            $purchase->delete();
        });

        return redirect()->back()->with('success', 'Purchase & related journal deleted successfully');
    }


    public function purchaseDetails($id)
    {
        $purchase = Purchase::with(['vendor', 'purchaseItems.item.category'])->find($id);
        return view('purchase.purchase-details', compact('purchase'));
    }

    public function downloadPdf($id)
    {
        $purchase = Purchase::with(['vendor', 'purchaseItems.item'])->findOrFail($id);

        $pdf = Pdf::loadView('purchase.purchase-pdf', compact('purchase'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Purchase_Invoice_' . $purchase->invoice_no . '.pdf');
    }

    public function downloadListPdf(Request $request)
    {
        $query = Purchase::with(['vendor', 'purchaseItems.item']);

        // filter same logic
        if ($request->from_date && $request->to_date) {
            $query->whereBetween('date', [$request->from_date, $request->to_date]);
        }

        if ($request->type == 'supplier' && $request->vendor_id) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->type == 'item' && $request->item_id) {
            $query->whereHas('purchaseItems', function ($q) use ($request) {
                $q->where('item_id', $request->item_id);
            });
        }

        $purchases = $query->get();

        $pdf = Pdf::loadView('purchase.purchase-list-pdf', compact('purchases'))
            ->setPaper('a4', 'Landscape');

        return $pdf->download('Purchase_List.pdf');
    }
}
