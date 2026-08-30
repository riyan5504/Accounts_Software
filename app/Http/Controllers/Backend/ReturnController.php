<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Company;
use App\Models\InventoryLedger;
use App\Models\Item;
use App\Models\JournalEntry;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Vendor;
use App\Services\InventoryService;
use App\Services\JournalService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    protected InventoryService $inventory;
    protected $journalService;

    public function __construct(InventoryService $inventory, JournalService $journalService)
    {
        $this->middleware('auth');

        $this->inventory = $inventory;
        $this->journalService = $journalService;
    }

    public function purchaseReturn()
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
        $lastReturn = PurchaseReturn::latest()->first();

        if ($lastReturn && $lastReturn->invoice_no) {
            // সংখ্যা অংশ বের করা (যেমন: PRC-0005 → 5)
            preg_match('/\d+/', $lastReturn->invoice_no, $matches);
            $number = isset($matches[0]) ? intval($matches[0]) : 0;
            $newNumber = $number + 1;
        } else {
            $newNumber = 1;
        }

        // নতুন invoice_no তৈরি করা (PRC-0001 ফরম্যাটে)
        $newReturnNo = 'PRTN-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        $accounts = Account::all();
        $users = User::all();
        $vendors = Vendor::select('vendors.id', 'vendors.v_name')
            ->join('purchases', 'vendors.id', '=', 'purchases.vendor_id')
            ->where('vendors.company_id', auth()->user()->company_id)
            ->where('purchases.company_id', auth()->user()->company_id)
            ->distinct()
            ->orderBy('vendors.v_name')
            ->get();
        return view('purchase.purchase-return', compact('accounts', 'users', 'newReturnNo', 'lastSerial', 'vendors'));
    }

    public function getVendorData($vendorId)
    {
        $companyId = auth()->user()->company_id;

        $vendor = Vendor::where('company_id', $companyId)
            ->findOrFail($vendorId);

        $invoices = Purchase::where('company_id', $companyId)
            ->where('vendor_id', $vendorId)
            ->orderByDesc('id')
            ->select('id', 'invoice_no', 'date', 'grand_total')
            ->get();

        return response()->json([
            'vendor'   => $vendor,
            'invoices' => $invoices,
        ]);
    }

    public function getInvoiceItems($purchaseId)
    {
        $purchase = Purchase::with([
            'vendor',
            'purchaseItems.item.category'
        ])->findOrFail($purchaseId);

        $items = [];

        foreach ($purchase->purchaseItems as $row) {
            // Purchased Qty
            $purchaseQty = $row->qty;
            // Already Returned Qty
            $returnedQty = PurchaseReturnItem::where('item_id', $row->item_id)
                ->whereHas('purchaseReturn', function ($q) use ($purchaseId) {
                    $q->where('purchase_id', $purchaseId);
                })
                ->sum('qty');
            $availableQty = $purchaseQty - $returnedQty;
            $items[] = [
                'item_id' => $row->item->id,
                'item_name' => $row->item->item_name,
                'item_code' => $row->item->item_code,
                'category' => optional($row->item->category)->cat_name,
                'size' => $row->item->size,
                'qty' => $availableQty,
                'unit_price' => $row->unit_price,
                'price' => $row->price,
                'vat_percent' => $row->vat_percent,
                'vat_amount' => $row->vat_amount,
                'total_price' => $row->total_price,
            ];
        }
        return response()->json([
            'vendor'   => $purchase->vendor,
            'purchase' => [
                'dis_percent' => $purchase->dis_percent,
                'dis_amt' => $purchase->dis_amt,
                'reference'         => $purchase->reference,
                'payment_status'    => $purchase->payment_status,
                'credit_account_id' => $purchase->credit_account_id,
                'narration'         => $purchase->narration,
            ],
            'items' => $items
        ]);
    }

    public function getAccountsByStatus($status)
    {
        if ($status == 'paid') {
            $accounts = Account::where('ac_type', 'asset')->get();
        } else {
            $accounts = Account::where('ac_type', 'liability')->get();
        }

        return response()->json($accounts);
    }

    public function returnStore(Request $request, JournalService $journalService)
    {
        // ---------- Base Validation ----------
        $request->validate([
            'grand_total' => 'required|numeric|min:0',
            'vendor_id' => 'required|exists:vendors,id',
            'item_id.*' => 'required|exists:items,id',
            'qty.*' => 'required|numeric|min:0.01',
            'unit_price.*' => 'required|numeric|min:0',
            'payment_status' => 'required|in:paid,due',
        ]);

        $grandTotal = (float) $request->grand_total;

        DB::transaction(function () use ($request, $grandTotal) {

            $vendor = Vendor::findOrFail($request->vendor_id);

            // 🔥 Step 1: Invalid item collect
            $invalidItems = [];

            foreach ($request->item_id as $itemId) {

                if (!$itemId) continue;

                $item = Item::where('id', $itemId)
                    ->first();

                $exists = PurchaseItem::where('item_id', $itemId)
                    ->whereHas('purchase', function ($q) use ($request) {
                        $q->where('vendor_id', $request->vendor_id);
                    })
                    ->exists();

                if (!$exists) {
                    $invalidItems[] = (!empty($item->item_name))
                        ? $item->item_name
                        : "Item ID {$itemId}";
                }
            }

            // 🔥 Final check
            $invalidItems = array_filter(array_unique($invalidItems));

            if (!empty($invalidItems)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'item_id' => 'এই item গুলো purchase করা হয়নি: ' . implode(', ', $invalidItems)
                ]);
            }

            // 🔹 Create return
            $return = PurchaseReturn::create([
                'company_id' => auth()->user()->company_id,
                'vendor_id' => $vendor->id,
                'purchase_id' => $request->purchase_id,
                'date' => $request->date,
                'invoice_no' => $request->invoice_no,
                'sub_total' => $request->sub_total,
                'vat_amt' => $request->vat_amt,
                'payment_status' => $request->payment_status,
                'dis_percent' => $request->dis_percent,
                'dis_amt' => $request->dis_amt,
                'grand_total' => $grandTotal,
                'reference' => $request->reference,
                'narration' => $request->narration,
                'debit_account_id' => $request->debit_account_id,
                'credit_account_id' => $request->credit_account_id,
                'created_by' => auth()->id(),
            ]);

            // 3️⃣ Transaction
            $transaction = Transaction::create([
                'company_id' => auth()->user()->company_id,
                'module_type' => 'purchase_return',
                'module_id' => $return->id,
                'vendor_id' => $return->vendor_id,
                'reference_no' => $return->invoice_no,
                'return_amt' => $grandTotal,
                'date' => $return->date,
            ]);

            // 4️⃣ Items loop (NO CREATE)
            foreach ($request->item_id as $key => $itemId) {

                $qty = (float) $request->qty[$key];
                $price = (float) $request->unit_price[$key];

                $totalPurchased = PurchaseItem::where('item_id', $itemId)
                    ->whereHas('purchase', fn($q) => $q->where('vendor_id', $request->vendor_id))
                    ->sum('qty');

                $totalReturned = PurchaseReturnItem::where('item_id', $itemId)
                    ->whereHas('purchaseReturn', fn($q) => $q->where('vendor_id', $request->vendor_id))
                    ->sum('qty');

                $availableQty = $totalPurchased - $totalReturned;

                if ($qty > $availableQty) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'qty.' . $key => "Max return qty: {$availableQty}"
                    ]);
                }

                $item = Item::findOrFail($itemId);

                PurchaseReturnItem::create([
                    'return_id' => $return->id,
                    'purchase_id' => $request->purchase_id,
                    'item_id' => $item->id,
                    'qty' => $qty,
                    'unit_price' => $price,
                    'price' => $qty * $price,
                    'vat_percent' => $request->vat_percent[$key],
                    'vat_amount' => $request->vat_amount[$key],
                    'total_price' => $request->total_price[$key],
                ]);

                // Inventory
                InventoryLedger::create([
                    'company_id' => auth()->user()->company_id,
                    'item_id' => $item->id,
                    'module_type' => 'purchase_return',
                    'module_id' => $return->id,
                    'qty_in' => 0,
                    'qty_out' => $qty,
                    'unit_cost' => $price,
                    'total_cost' => $qty * $price,
                    'date' => $return->date,
                    'created_by' => auth()->id(),
                ]);
            }

            $this->journalService->createJournal([
                'company_id'   => auth()->user()->company_id,
                'module_type'  => 'purchase_return',
                'module_id'    => $return->id,
                'reference_no' => $return->invoice_no,
                'date'         => $return->date,
                'particulars'  => $return->narration,
                'items' => [
                    // Debit
                    [
                        'account'   => $request->debit_account_id,
                        'debit'     => $grandTotal,
                        'credit'    => 0,
                        'vendor_id' => $vendor->id,
                    ],
                    // Credit
                    [
                        'account'   => $request->credit_account_id,
                        'debit'     => 0,
                        'credit'    => $grandTotal,
                        'vendor_id' => $vendor->id,
                    ],
                ]
            ]);
        });

        return back()->with('success', 'Purchase Return saved successfully!');
    }

    public function returnList(Request $request)
    {
        if ($request->type == 'all') {
            $request->merge([
                'vendor_id' => null,
                'item_id' => null
            ]);
        }
        $query = PurchaseReturn::with(['vendor', 'purchaseReturnItems.item', 'transactions']);

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
            $query->whereHas('purchaseReturnItems', function ($q) use ($request) {
                $q->where('item_id', $request->item_id);
            });
        }

        $returns = $query->get();

        $vendors = Vendor::get();
        $items = Item::get();

        return view('purchase.return-list', compact('returns', 'vendors', 'items'));
    }

    public function returnEdit($id)
    {
        // সর্বশেষ item code
        $lastItem = Item::latest('id')->first();

        if ($lastItem && preg_match('/\d+$/', $lastItem->item_code, $matches)) {
            $lastSerial = intval($matches[0]);
        } else {
            $lastSerial = 0;
        }

        // Accounts
        $accounts = Account::get();

        $vendors = Vendor::select('vendors.id', 'vendors.v_name')
            ->join('purchases', 'vendors.id', '=', 'purchases.vendor_id')
            ->where('vendors.company_id', auth()->user()->company_id)
            ->where('purchases.company_id', auth()->user()->company_id)
            ->distinct()
            ->orderBy('vendors.v_name')
            ->get();

        // Purchase Return
        $return = PurchaseReturn::with([
            'vendor',
            'purchaseReturnItems.item',
            'transactions'
        ])->findOrFail($id);

        // এই vendor-এর purchase invoices
        $invoices = Purchase::where('vendor_id', $return->vendor_id)
            ->where('company_id', auth()->user()->company_id)
            ->orderBy('id', 'desc')
            ->get();

        // Transactions
        $transactions = $return->transactions;

        return view('purchase.return-edit', compact(
            'return',
            'vendors',
            'lastSerial',
            'accounts',
            'transactions',
            'invoices'
        ));
    }

    public function returnUpdate(Request $request, $id)
    {
        $request->validate([
            'grand_total' => 'required|numeric|min:0',
            'vendor_id' => 'required|exists:vendors,id',
            'item_id.*' => 'required|exists:items,id',
            'payment_status' => 'required|in:paid,due',
        ]);

        $grandTotal = (float) $request->grand_total;

        DB::transaction(function () use ($request, $id, $grandTotal) {

            $return = PurchaseReturn::findOrFail($id);
            $vendor = Vendor::findOrFail($request->vendor_id);
            // 🔹 1. Update return
            $return->update([
                'company_id' => auth()->user()->company_id,
                'vendor_id' => $vendor->id,
                'purchase_id' => $request->purchase_id,
                'date' => $request->date,
                'invoice_no' => $request->invoice_no,
                'sub_total' => $request->sub_total,
                'vat_amt' => $request->vat_amt,
                'payment_status' => $request->payment_status,
                'dis_percent' => $request->dis_percent,
                'dis_amt' => $request->dis_amt,
                'grand_total' => $grandTotal,
                'reference' => $request->reference,
                'narration' => $request->narration,
                'debit_account_id' => $request->debit_account_id,
                'credit_account_id' => $request->credit_account_id,
                'created_by' => auth()->id(),
            ]);

            // 🔹 2. Get Transaction
            $transaction = Transaction::where('module_type', 'purchase_return')
                ->where('module_id', $return->id)
                ->firstOrFail();
            if ($transaction) {
                $transaction->delete();
            }

            $transaction = Transaction::create([
                'company_id' => auth()->user()->company_id,
                'module_type' => 'purchase_return',
                'module_id' => $return->id,
                'vendor_id' => $return->vendor_id,
                'reference_no' => $return->invoice_no,
                'return_amt' => $grandTotal,
                'date' => $return->date,
            ]);

            // 🔥 3. DELETE OLD DATA (IMPORTANT)
            PurchaseReturnItem::where('return_id', $return->id)->delete();

            InventoryLedger::where('module_type', 'purchase_return')
                ->where('module_id', $return->id)
                ->delete();

            // Journal delete (cascade will remove items)
            JournalEntry::where('module_type', 'purchase_return')
                ->where('module_id', $return->id)
                ->forceDelete();

            // 🔹 4. Recreate Items
            foreach ($request->item_id as $key => $itemId) {

                $item = Item::where('company_id', auth()->user()->company_id)
                    ->findOrFail($itemId);

                $qty = (float) $request->qty[$key];
                $price = (float) $request->unit_price[$key];

                PurchaseReturnItem::create([
                    'return_id' => $return->id,
                    'purchase_id' => $request->purchase_id,
                    'item_id' => $item->id,
                    'qty' => $qty,
                    'unit_price' => $price,
                    'price' => $qty * $price,
                    'vat_percent' => $request->vat_percent[$key],
                    'vat_amount' => $request->vat_amount[$key],
                    'total_price' => $request->total_price[$key],
                ]);

                InventoryLedger::create([
                    'company_id' => auth()->user()->company_id,
                    'item_id' => $item->id,
                    'module_type' => 'purchase_return',
                    'module_id' => $return->id,
                    'qty_in' => 0,
                    'qty_out' => $qty,
                    'unit_cost' => $price,
                    'total_cost' => $qty * $price,
                    'date' => $return->date,
                    'created_by' => auth()->id(),
                ]);
            }

            $this->journalService->createJournal([
                'company_id'   => auth()->user()->company_id,
                'module_type'  => 'purchase_return',
                'module_id'    => $return->id,
                'reference_no' => $return->invoice_no,
                'date'         => $return->date,
                'particulars'  => $return->narration,
                'items' => [
                    // Debit
                    [
                        'account'   => $request->debit_account_id,
                        'debit'     => $grandTotal,
                        'credit'    => 0,
                        'vendor_id' => $vendor->id,
                    ],
                    // Credit
                    [
                        'account'   => $request->credit_account_id,
                        'debit'     => 0,
                        'credit'    => $grandTotal,
                        'vendor_id' => $vendor->id,
                    ],
                ]
            ]);
        });

        return redirect('/purchase/return/list')->with('success', 'Return updated successfully!');
    }


    public function returnDelete($id)
    {
        DB::transaction(function () use ($id) {

            $return = PurchaseReturn::findOrFail($id);

            // 🔹 Get transaction
            $transaction = Transaction::where('module_type', 'purchase_return')
                ->where('module_id', $return->id)
                ->firstOrFail();
            if ($transaction) {
                $transaction->delete();
            }

            InventoryLedger::where('module_type', 'purchase_return')
                ->where('module_id', $return->id)
                ->delete();

            // Journal delete (cascade will remove items)
            JournalEntry::where('module_type', 'purchase_return')
                ->where('module_id', $return->id)
                ->Delete();

            PurchaseReturnItem::where('return_id', $return->id)
                ->Delete();

            // 🔹 Finally delete return
            $return->Delete();
        });

        return back()->with('success', 'Return deleted successfully!');
    }

    public function returnDetails($id)
    {
        $return = PurchaseReturn::with([
            'vendor',
            'purchaseReturnItems.item.category',
            'transactions'
        ])->findOrFail($id);

        $transactions = $return->transactions;
        $totalPaid = $transactions->sum('paid_amt');
        $due = max(0, $return->grand_total - $totalPaid);

        return view('purchase.return-details', compact('return', 'transactions', 'totalPaid', 'due'));
    }

    public function downloadPdf($id)
    {
        $return = PurchaseReturn::with(['vendor', 'purchaseReturnItems.item'])->findOrFail($id);
        $company = Company::find(auth()->user()->company_id);

        $logoPath = null;

        if ($company && $company->logo) {
            $path = public_path('backend/dist/assets/img/' . $company->logo);

            if (file_exists($path)) {
                $logoPath = $path;
            }
        }

        $pdf = Pdf::loadView('purchase.return-pdf', compact('return', 'company', 'logoPath'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Return_Invoice_' . $return->invoice_no . '.pdf');
    }

    public function downloadListPdf(Request $request)
    {
        $query = PurchaseReturn::with(['vendor', 'purchaseReturnItems.item']);

        if ($request->from_date && $request->to_date) {
            $query->whereBetween('date', [$request->from_date, $request->to_date]);
        }

        if ($request->type == 'supplier' && $request->vendor_id) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->type == 'item' && $request->item_id) {
            $query->whereHas('purchaseReturnItems', function ($q) use ($request) {
                $q->where('item_id', $request->item_id);
            });
        }

        $returns = $query->get();
        $company = Company::find(auth()->user()->company_id);

        $logoPath = null;

        if ($company && $company->logo) {
            $path = public_path('backend/dist/assets/img/' . $company->logo);

            if (file_exists($path)) {
                $logoPath = $path;
            }
        }

        // 🔥 ADD THIS
        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        $pdf = Pdf::loadView('purchase.return-list-pdf', compact(
            'returns',
            'company',
            'fromDate',
            'toDate',
            'logoPath'
        ))->setPaper('a4', 'Landscape');

        return $pdf->download('Return_List.pdf');
    }
}
