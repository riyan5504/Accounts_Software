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
use App\Models\Transaction;
use App\Models\Vendor;
use App\Services\InventoryService;
use App\Services\JournalService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    protected InventoryService $inventoryService;
    protected JournalService $journalService;

    public function __construct(InventoryService $inventoryService, JournalService $journalService)
    {
        $this->middleware('auth');
        $this->inventoryService = $inventoryService;
        $this->journalService = $journalService;
    }

    public function purchase()
    {
        return view('purchase.purchase-modiul');
    }

    public function purchaseEntry()
    {
        $accounts = Account::all();

        // Generate new invoice number
        $newPurchaseNo = $this->generateInvoiceNumber();

        // Get last serial for item code
        $lastSerial = $this->getLastItemSerial();

        return view('purchase.create', compact('accounts', 'newPurchaseNo', 'lastSerial'));
    }

    public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'date' => 'required|date',
            'invoice_no' => 'required|string|max:50',
            'item_id.*' => 'required|exists:items,id',
            'qty.*' => 'required|numeric|min:0.01',
            'unit_price.*' => 'required|numeric|min:0',
            'grand_total' => 'required|numeric|min:0',
            'paid_amt' => 'nullable|numeric|min:0',
            'payment_status' => 'required|in:paid,partial,unpaid',
            'debit_account_id' => 'required|exists:accounts,id',
            'credit_account_id' => 'required|exists:accounts,id',
            'payment_method' => 'required|in:cash,bank,cheque,mobile_bank,due',
        ]);

        // Business validation
        $grandTotal = (float) $request->grand_total;
        $paid = (float) ($request->paid_amt ?? 0);
        $status = $request->payment_status;

        $this->validatePaymentAmounts($grandTotal, $paid, $status);

        // Check duplicate invoice (BEFORE transaction)
        if (Purchase::where('invoice_no', $request->invoice_no)->exists()) {
            return back()
                ->withErrors(['invoice_no' => 'Invoice already exists: ' . $request->invoice_no])
                ->withInput();
        }

        // Calculate due
        $due = max(0, $grandTotal - $paid);

        // Save everything in transaction
        DB::transaction(function () use ($request, $grandTotal, $paid, $due) {
            // 1. Find vendor
            $vendor = Vendor::findOrFail($request->vendor_id);

            // 2. Create purchase
            $purchase = Purchase::create([
                'vendor_id' => $vendor->id,
                'date' => $request->date,
                'invoice_no' => $request->invoice_no,
                'sub_total' => $request->sub_total,
                'vat_amt' => $request->vat_amt ?? 0,
                'dis_percent' => $request->dis_percent ?? 0,
                'dis_amt' => $request->dis_amt ?? 0,
                'grand_total' => $grandTotal,
                'due_amt' => $due,
                'reference' => $request->reference,
                'narration' => $request->narration,
                'pay_to' => $request->pay_to,
                'payment_status' => $request->payment_status,
                'debit_account_id' => $request->debit_account_id,
                'credit_account_id' => $request->credit_account_id,
                'created_by' => auth()->id(),
            ]);

            // 3. Create transaction
            $transaction = Transaction::create([
                'module_type' => 'purchase',
                'module_id' => $purchase->id,
                'vendor_id' => $vendor->id,
                'reference_no' => $purchase->invoice_no,
                'payment_method' => $request->payment_method,
                'paid_amt' => $paid,
                'date' => $purchase->date,
            ]);

            // 4. Save items & inventory
            $this->savePurchaseItems($purchase, $request);

            // 5. Create journal entries
            $journal = $this->createJournalEntry($purchase);
            $this->journalService->createPurchaseJournal($journal->id, $purchase, $transaction);
        });

        return back()->with('success', 'Purchase saved successfully!');
    }

    public function purchaseList(Request $request)
    {
        $query = Purchase::with(['vendor', 'purchaseItems.item', 'transactions']);

        // Apply filters
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->byDateRange($request->from_date, $request->to_date);
        }

        if ($request->type === 'supplier' && $request->filled('vendor_id')) {
            $query->byVendor($request->vendor_id);
        }

        if ($request->type === 'item' && $request->filled('item_id')) {
            $query->whereHas('purchaseItems', function ($q) use ($request) {
                $q->where('item_id', $request->item_id);
            });
        }

        $purchases = $query->get();
        $vendors = Vendor::all();
        $items = Item::all();

        return view('purchase.purchase-list', compact('purchases', 'vendors', 'items'));
    }

    public function purchaseEdit($id)
    {
        $purchase = Purchase::with([
            'vendor',
            'user',
            'purchaseItems.item.category',
            'transactions'
        ])->findOrFail($id);

        $accounts = Account::all();
        $lastSerial = $this->getLastItemSerial();
        $totalPaid = $purchase->total_paid;
        $due = $purchase->remaining_due;

        return view('purchase.purchase-edit', compact(
            'purchase',
            'accounts',
            'lastSerial',
            'totalPaid',
            'due'
        ));
    }

    public function purchaseUpdate(Request $request, $id)
    {
        $purchase = Purchase::findOrFail($id);

        // Validation
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'date' => 'required|date',
            'invoice_no' => 'required|string|max:50',
            'item_id.*' => 'required|exists:items,id',
            'qty.*' => 'required|numeric|min:0.01',
            'unit_price.*' => 'required|numeric|min:0',
            'grand_total' => 'required|numeric|min:0',
            'paid_amt' => 'nullable|numeric|min:0',
            'payment_status' => 'required|in:paid,partial,unpaid',
            'debit_account_id' => 'required|exists:accounts,id',
            'credit_account_id' => 'required|exists:accounts,id',
            'payment_method' => 'required|in:cash,bank,cheque,mobile_bank,due',
        ]);

        $grandTotal = (float) $request->grand_total;
        $paid = (float) ($request->paid_amt ?? 0);
        $status = $request->payment_status;

        $this->validatePaymentAmounts($grandTotal, $paid, $status);

        $due = max(0, $grandTotal - $paid);

        DB::transaction(function () use ($request, $purchase, $grandTotal, $paid, $due) {
            // Update purchase
            $purchase->update([
                'vendor_id' => $request->vendor_id,
                'date' => $request->date,
                'invoice_no' => $request->invoice_no,
                'sub_total' => $request->sub_total,
                'vat_amt' => $request->vat_amt ?? 0,
                'dis_percent' => $request->dis_percent ?? 0,
                'dis_amt' => $request->dis_amt ?? 0,
                'grand_total' => $grandTotal,
                'due_amt' => $due,
                'reference' => $request->reference,
                'narration' => $request->narration,
                'pay_to' => $request->pay_to,
                'payment_status' => $request->payment_status,
                'debit_account_id' => $request->debit_account_id,
                'credit_account_id' => $request->credit_account_id,
            ]);

            // Delete old relations
            $this->deletePurchaseRelations($purchase);

            // Recreate transaction
            $transaction = Transaction::create([
                'module_type' => 'purchase',
                'module_id' => $purchase->id,
                'vendor_id' => $purchase->vendor_id,
                'reference_no' => $purchase->invoice_no,
                'payment_method' => $request->payment_method,
                'paid_amt' => $paid,
                'date' => $purchase->date,
            ]);

            // Recreate items & inventory
            $this->savePurchaseItems($purchase, $request);

            // Recreate journal
            $journal = $this->createJournalEntry($purchase);
            $this->journalService->createPurchaseJournal($journal->id, $purchase, $transaction);
        });

        return redirect('/purchase/list')->with('success', 'Purchase updated successfully!');
    }

    public function purchaseDelete($id)
    {
        DB::transaction(function () use ($id) {
            $purchase = Purchase::findOrFail($id);
            $this->deletePurchaseRelations($purchase);
            $purchase->forceDelete();
        });

        return back()->with('success', 'Purchase deleted successfully!');
    }

    public function purchaseDetails($id)
    {
        $purchase = Purchase::with([
            'vendor',
            'purchaseItems.item.category',
            'transactions',
            'debitAccount',
            'creditAccount',
            'user',
        ])->findOrFail($id);

        $transactions = $purchase->transactions;
        $totalPaid = $purchase->total_paid;
        $due = $purchase->remaining_due;

        return view('purchase.purchase-details', compact(
            'purchase',
            'transactions',
            'totalPaid',
            'due'
        ));
    }

    public function downloadListPdf(Request $request)
    {
        $query = Purchase::with(['vendor', 'purchaseItems.item', 'transactions']);

        // Apply filters
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('date', [$request->from_date, $request->to_date]);
        }

        if ($request->type === 'supplier' && $request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->type === 'item' && $request->filled('item_id')) {
            $query->whereHas('purchaseItems', function ($q) use ($request) {
                $q->where('item_id', $request->item_id);
            });
        }

        $purchases = $query->latest('date')->get();

        // Get company name
        $companyName = Company::find(auth()->user()->company_id)->name ?? 'Company Name';

        $pdf = Pdf::loadView('purchase.purchase-list-pdf', [
            'purchases' => $purchases,
            'companyName' => $companyName,
            'fromDate' => $request->from_date,
            'toDate' => $request->to_date,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('Purchase_List_' . now()->format('d-m-Y') . '.pdf');
    }

    public function downloadPdf($id)
    {
        $purchase = Purchase::with([
            'vendor',
            'purchaseItems.item',
            'transactions'
        ])->findOrFail($id);

        $companyName = Company::find(auth()->user()->company_id)->name ?? 'Company Name';
        $pdf = Pdf::loadView('purchase.purchase-pdf', [
            'purchase' => $purchase,
            'companyName' => $companyName,
        ])
            ->setPaper('a4', 'portrait');

        return $pdf->download('Purchase_Invoice_' . $purchase->invoice_no . '.pdf');
    }

    /**
     * Generate new invoice number
     */
    private function generateInvoiceNumber(): string
    {
        $lastPurchase = Purchase::latest('id')->first();

        $number = 1;
        if ($lastPurchase && $lastPurchase->invoice_no) {
            preg_match('/\d+/', $lastPurchase->invoice_no, $matches);
            $number = isset($matches[0]) ? (int) $matches[0] + 1 : 1;
        }

        $invoiceNo = 'PRC-' . str_pad($number, 4, '0', STR_PAD_LEFT);

        // Ensure uniqueness
        while (Purchase::where('invoice_no', $invoiceNo)->exists()) {
            $number++;
            $invoiceNo = 'PRC-' . str_pad($number, 4, '0', STR_PAD_LEFT);
        }

        return $invoiceNo;
    }

    /**
     * Get last item serial number
     */
    private function getLastItemSerial(): int
    {
        $lastItem = Item::latest('id')->first();

        if ($lastItem && preg_match('/\d+$/', $lastItem->item_code, $matches)) {
            return (int) $matches[0];
        }

        return 0;
    }

    /**
     * Validate payment amounts based on status
     */
    private function validatePaymentAmounts(float $grandTotal, float $paid, string $status): void
    {
        if ($status === 'paid' && $paid != $grandTotal) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'paid_amt' => 'Full payment required for Paid status'
            ]);
        }

        if ($status === 'partial' && ($paid <= 0 || $paid >= $grandTotal)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'paid_amt' => 'Partial payment must be between 0 and total amount'
            ]);
        }

        if ($status === 'unpaid' && $paid != 0) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'paid_amt' => 'Payment must be 0 for Unpaid status'
            ]);
        }
    }

    /**
     * Save purchase items and inventory
     */
    // PurchaseController.php

    private function savePurchaseItems(Purchase $purchase, Request $request): void
    {
        foreach ($request->item_id as $key => $itemId) {
            if (!$itemId) continue;

            $item = Item::findOrFail($itemId);
            $qty = (float) $request->qty[$key];
            $price = (float) $request->unit_price[$key];
            $totalPrice = $qty * $price;

            // ========== 1. UPDATE ITEM TABLE ==========
            $item->update([
                'item_code' => $request->item_code[$key] ?? $item->item_code,
                'unit_price' => $price ?: $item->unit_price,
                'size' => $request->size[$key] ?? $item->size,
                'stock_unit' => $request->stock_unit[$key] ?? $item->stock_unit,
            ]);

            // Save purchase item
            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'item_id' => $item->id,
                'qty' => $qty,
                'unit_price' => $price,
                'price' => $totalPrice,
                'vat_percent' => $request->vat_percent[$key] ?? 0,
                'vat_amount' => $request->vat_amount[$key] ?? 0,
                'total_price' => $request->total_price[$key] ?? $totalPrice,
            ]);

            // Update inventory (stock in)
            InventoryLedger::create([
                'company_id' => auth()->user()->company_id,
                'item_id' => $item->id,
                'module_type' => 'purchase',
                'module_id' => $purchase->id,
                'qty_in' => $qty,
                'qty_out' => 0,
                'unit_cost' => $price,
                'total_cost' => $qty * $price,
                'date' => $purchase->date,
                'created_by' => auth()->id(),
            ]);
        }
    }

    /**
     * Create journal entry header
     */
    private function createJournalEntry(Purchase $purchase): \App\Models\JournalEntry
    {
        return JournalEntry::create([
            'module_type' => 'purchase',
            'module_id' => $purchase->id,
            'reference_no' => $purchase->invoice_no,
            'date' => $purchase->date,
            'particulars' => $purchase->narration ?? 'Purchase Entry',
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Delete all related records for a purchase
     */
    private function deletePurchaseRelations(Purchase $purchase): void
    {
        // Delete transactions
        Transaction::where('module_type', 'purchase')
            ->where('module_id', $purchase->id)
            ->delete();

        // Delete inventory
        InventoryLedger::where('module_type', 'purchase')
            ->where('module_id', $purchase->id)
            ->forceDelete();

        // Delete journal entries
        JournalEntry::where('module_type', 'purchase')
            ->where('module_id', $purchase->id)
            ->delete(); // Cascade will delete journal items

        // Delete purchase items
        PurchaseItem::where('purchase_id', $purchase->id)->forceDelete();
    }
}
