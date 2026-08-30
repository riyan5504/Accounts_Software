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
        try {
            DB::transaction(function () use (
                $request,
                $grandTotal,
                $paid,
                $due
            ) {
                // 1. Find vendor
                $vendor = Vendor::findOrFail(
                    $request->vendor_id
                );

                // 2. Create Purchase
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

                // 3. Transaction
                $transaction = Transaction::create([
                    'module_type' => 'purchase',
                    'module_id' => $purchase->id,
                    'vendor_id' => $vendor->id,
                    'reference_no' => $purchase->invoice_no,
                    'payment_method' => $request->payment_method,
                    'paid_amt' => $paid,
                    'date' => $purchase->date,
                ]);

                // 4. Save Purchase Items
                $this->savePurchaseItems($purchase, $request);

                // 5. Create Purchase Journal

                $grandTotal = (float) $purchase->grand_total;
                $paidAmount = (float) ($transaction->paid_amt ?? 0);
                $dueAmount = max(0, $grandTotal - $paidAmount);
                $journalItems = [];

                // Debit Purchase / Inventory
                $journalItems[] = [
                    'account' => $purchase->debit_account_id,
                    'debit' => $grandTotal,
                    'credit' => 0,
                    'vendor_id' => $purchase->vendor_id,
                ];

                // PAID
                if ($purchase->payment_status === 'paid') {
                    $journalItems[] = [
                        'account' => $purchase->credit_account_id,
                        'debit' => 0,
                        'credit' => $grandTotal,
                        'vendor_id' => $purchase->vendor_id,
                    ];
                }

                // UNPAID
                elseif ($purchase->payment_status === 'unpaid') {
                    $payableAccountId =
                        Account::where('company_id', $purchase->company_id)
                        ->where('account_name', JournalService::ACCOUNT_SUPPLIER_PAYABLE)
                        ->value('id');

                    if (!$payableAccountId) {
                        throw new \Exception('Supplier Payable account not found.');
                    }
                    $journalItems[] = [
                        'account' => $payableAccountId,
                        'debit' => 0,
                        'credit' => $grandTotal,
                        'vendor_id' => $purchase->vendor_id,
                    ];
                }

                // PARTIAL
                elseif ($purchase->payment_status === 'partial') {
                    // Paid portion
                    if ($paidAmount > 0) {
                        $journalItems[] = [
                            'account' => $purchase->credit_account_id,
                            'debit' => 0,
                            'credit' => $paidAmount,
                            'vendor_id' => $purchase->vendor_id,
                        ];
                    }
                    // Due portion
                    if ($dueAmount > 0) {
                        $payableAccountId =
                            Account::where('company_id', $purchase->company_id)
                            ->where('account_name', JournalService::ACCOUNT_SUPPLIER_PAYABLE)
                            ->value('id');

                        if (!$payableAccountId) {
                            throw new \Exception('Supplier Payable account not found.');
                        }
                        $journalItems[] = [
                            'account' => $payableAccountId,
                            'debit' => 0,
                            'credit' => $dueAmount,
                            'vendor_id' => $purchase->vendor_id,
                        ];
                    }
                } else {
                    throw new \Exception('Invalid purchase payment status: ' . $purchase->payment_status);
                }
                // Save Journal
                $this->journalService->createJournal([
                    'company_id' => $purchase->company_id,
                    'module_type' => 'purchase',
                    'module_id' => $purchase->id,
                    'reference_no' => $purchase->invoice_no,
                    'date' => $purchase->date,
                    'particulars' => $purchase->narration ?? 'Purchase Entry',
                    'items' => $journalItems,
                ]);
            });

            return back()->with('success', 'Purchase saved successfully!');
        } catch (\Throwable $e) {

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
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

            // Get old item IDs before deleting old purchase data
            $oldItemIds = $purchase->purchaseItems()
                ->pluck('item_id')
                ->unique()
                ->values()
                ->toArray();

            // Delete old relations
            $this->deletePurchaseRelations($purchase);

            // Recalculate averages of old items
            foreach ($oldItemIds as $oldItemId) {
                $this->recalculateAveragePurchasePrice($oldItemId);
            }

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

            // 5. Create Purchase Journal
            $grandTotal = (float) $purchase->grand_total;
            $paidAmount = (float) ($transaction->paid_amt ?? 0);
            $dueAmount = max(0, $grandTotal - $paidAmount);
            $journalItems = [];

            // Debit Purchase / Inventory
            $journalItems[] = [
                'account' => $purchase->debit_account_id,
                'debit' => $grandTotal,
                'credit' => 0,
                'vendor_id' => $purchase->vendor_id,
            ];

            // PAID
            if ($purchase->payment_status === 'paid') {
                $journalItems[] = [
                    'account' => $purchase->credit_account_id,
                    'debit' => 0,
                    'credit' => $grandTotal,
                    'vendor_id' => $purchase->vendor_id,
                ];
            }

            // UNPAID
            elseif ($purchase->payment_status === 'unpaid') {
                $payableAccountId =
                    Account::where('company_id', $purchase->company_id)
                    ->where('account_name', JournalService::ACCOUNT_SUPPLIER_PAYABLE)
                    ->value('id');

                if (!$payableAccountId) {
                    throw new \Exception('Supplier Payable account not found.');
                }
                $journalItems[] = [
                    'account' => $payableAccountId,
                    'debit' => 0,
                    'credit' => $grandTotal,
                    'vendor_id' => $purchase->vendor_id,
                ];
            }

            // PARTIAL
            elseif ($purchase->payment_status === 'partial') {
                // Paid portion
                if ($paidAmount > 0) {
                    $journalItems[] = [
                        'account' => $purchase->credit_account_id,
                        'debit' => 0,
                        'credit' => $paidAmount,
                        'vendor_id' => $purchase->vendor_id,
                    ];
                }
                // Due portion
                if ($dueAmount > 0) {
                    $payableAccountId =
                        Account::where('company_id', $purchase->company_id)
                        ->where('account_name', JournalService::ACCOUNT_SUPPLIER_PAYABLE)
                        ->value('id');

                    if (!$payableAccountId) {
                        throw new \Exception('Supplier Payable account not found.');
                    }
                    $journalItems[] = [
                        'account' => $payableAccountId,
                        'debit' => 0,
                        'credit' => $dueAmount,
                        'vendor_id' => $purchase->vendor_id,
                    ];
                }
            } else {
                throw new \Exception('Invalid purchase payment status: ' . $purchase->payment_status);
            }
            // Save Journal
            $this->journalService->createJournal([
                'company_id' => $purchase->company_id,
                'module_type' => 'purchase',
                'module_id' => $purchase->id,
                'reference_no' => $purchase->invoice_no,
                'date' => $purchase->date,
                'particulars' => $purchase->narration ?? 'Purchase Entry',
                'items' => $journalItems,
            ]);
        });

        return redirect('/purchase/list')->with('success', 'Purchase updated successfully!');
    }
    
    public function purchaseDelete($id)
    {
        DB::transaction(function () use ($id) {
            $purchase = Purchase::findOrFail($id);
            $itemIds = $purchase->purchaseItems()
                ->pluck('item_id')
                ->unique()
                ->values()
                ->toArray();

            // Delete purchase relations
            $this->deletePurchaseRelations($purchase);

            // Recalculate average cost for affected items
            foreach ($itemIds as $itemId) {
                $this->recalculateAveragePurchasePrice($itemId);
            }
            
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
            'vendorPaymentDetails.vendorPayment',
            'purchaseReturns.purchaseReturnItems.item',
            'debitAccount',
            'creditAccount',
            'user',
        ])->findOrFail($id);

        $originalPurchase = (float) $purchase->grand_total;
        $totalReturn = (float) $purchase->purchaseReturns->sum('grand_total');
        $netPurchase = max(0, $originalPurchase - $totalReturn);
        $initialPayment = (float) $purchase->transactions
            ->where('module_type', 'purchase')
            ->sum('paid_amt');
        $vendorPayment = (float) $purchase->vendorPaymentDetails
            ->sum('paid_amount');
        $totalPaid = $initialPayment + $vendorPayment;
        $supplierCredit = max(0, $totalPaid - $netPurchase);
        $due = max(0, $netPurchase - $totalPaid);

        if ($supplierCredit > 0) {
            $paymentStatus = 'Credit';
        } elseif ($due <= 0 && $netPurchase > 0) {
            $paymentStatus = 'Paid';
        } elseif ($totalPaid > 0) {
            $paymentStatus = 'Partial';
        } else {
            $paymentStatus = 'Unpaid';
        }
        $transactions = $purchase->transactions;
        $returnCount = $purchase->purchaseReturns->count();
        $vendorPaymentCount = $purchase->vendorPaymentDetails->count();
        $returnedItems = $purchase->purchaseReturns
            ->flatMap(function ($purchaseReturn) {
                return $purchaseReturn->purchaseReturnItems;
            });
        $returnedQtyByItem = $returnedItems
            ->groupBy('item_id')
            ->map(function ($items) {
                return $items->sum('qty');
            });
        $returnedAmountByItem = $returnedItems
            ->groupBy('item_id')
            ->map(function ($items) {
                return $items->sum('total_price');
            });
        $returnHistory = $purchase->purchaseReturns
            ->sortByDesc('date');
        $vendorPaymentHistory = $purchase->vendorPaymentDetails
            ->sortByDesc(function ($detail) {
                return optional($detail->vendorPayment)->date;
            });
        return view('purchase.purchase-details', compact(
            'purchase',
            'transactions',
            'originalPurchase',
            'totalReturn',
            'netPurchase',
            'initialPayment',
            'vendorPayment',
            'totalPaid',
            'due',
            'supplierCredit',
            'paymentStatus',
            'returnCount',
            'vendorPaymentCount',
            'returnedItems',
            'returnedQtyByItem',
            'returnedAmountByItem',
            'returnHistory',
            'vendorPaymentHistory'
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

        $company = Company::find(auth()->user()->company_id);

        $logoPath = null;

        if ($company && $company->logo) {
            $path = public_path('backend/dist/assets/img/' . $company->logo);

            if (file_exists($path)) {
                $logoPath = $path;
            }
        }

        $pdf = Pdf::loadView('purchase.purchase-list-pdf', [
            'purchases' => $purchases,
            'company' => $company,
            'logoPath' => $logoPath,
            'fromDate' => $request->from_date,
            'toDate' => $request->to_date,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('Purchase_List_' . now()->format('d-m-Y') . '.pdf');
    }

    public function downloadPdf($id)
    {
        $purchase = Purchase::with([
            'vendor',
            'purchaseItems.item.category',
            'transactions',
            'vendorPaymentDetails.vendorPayment',
            'purchaseReturns.purchaseReturnItems.item',
            'debitAccount',
            'creditAccount',
            'user',
        ])->findOrFail($id);

        $originalPurchase = (float) $purchase->grand_total;
        $totalReturn = (float) $purchase->purchaseReturns
            ->sum('grand_total');
        $netPurchase = max(0, $originalPurchase - $totalReturn);

        $initialPaymentRows = $purchase->transactions
            ->where('module_type', 'purchase')
            ->filter(function ($transaction) {
                return (float) $transaction->paid_amt > 0;
            })
            ->values();
        $initialPayment = (float) $initialPaymentRows->sum('paid_amt');
        $hasInitialPayment = $initialPayment > 0;

        $vendorPaymentHistory = $purchase->vendorPaymentDetails
            ->filter(function ($detail) {
                return (float) $detail->paid_amount > 0;
            })
            ->sortByDesc(function ($detail) {
                return optional($detail->vendorPayment)->date;
            })
            ->values();
        $vendorPayment = (float) $vendorPaymentHistory->sum('paid_amount');
        $hasVendorPayment = $vendorPaymentHistory->isNotEmpty();
        $totalPaid = $initialPayment + $vendorPayment;
        $supplierCredit = max(0, $totalPaid - $netPurchase);
        $due = max(0, $netPurchase - $totalPaid);

        if ($supplierCredit > 0) {
            $paymentStatus = 'Credit';
        } elseif ($due <= 0 && $netPurchase > 0) {
            $paymentStatus = 'Paid';
        } elseif ($totalPaid > 0) {
            $paymentStatus = 'Partial';
        } else {
            $paymentStatus = 'Unpaid';
        }

        $transactions = $purchase->transactions;
        $returnCount = $purchase->purchaseReturns->count();
        $vendorPaymentCount = $vendorPaymentHistory->count();
        $returnedItems = $purchase->purchaseReturns
            ->flatMap(function ($purchaseReturn) {
                return $purchaseReturn->purchaseReturnItems;
            });
        $returnedQtyByItem = $returnedItems
            ->groupBy('item_id')
            ->map(function ($items) {
                return $items->sum('qty');
            });
        $returnedAmountByItem = $returnedItems
            ->groupBy('item_id')
            ->map(function ($items) {
                return $items->sum('total_price');
            });
        $returnHistory = $purchase->purchaseReturns
            ->sortByDesc('date')
            ->values();
        $hasReturns = $returnedQtyByItem->sum() > 0;
        $company = Company::find(auth()->user()->company_id);

        $logoPath = null;

        if ($company && $company->logo) {
            $path = public_path('backend/dist/assets/img/' . $company->logo);

            if (file_exists($path)) {
                $logoPath = $path;
            }
        }

        $pdf = Pdf::loadView('purchase.purchase-pdf', compact(
            'purchase',
            'originalPurchase',
            'totalReturn',
            'netPurchase',
            'initialPaymentRows',
            'initialPayment',
            'hasInitialPayment',
            'vendorPaymentHistory',
            'vendorPayment',
            'hasVendorPayment',
            'totalPaid',
            'due',
            'supplierCredit',
            'paymentStatus',
            'transactions',
            'returnCount',
            'vendorPaymentCount',
            'returnedItems',
            'returnedQtyByItem',
            'returnedAmountByItem',
            'returnHistory',
            'hasReturns',
            'company',
            'logoPath'
        ));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download(
            'Purchase-Invoice-' . $purchase->invoice_no . '.pdf'
        );
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

    // Save purchase items and inventory
    private function savePurchaseItems(Purchase $purchase, Request $request): void
    {
        $invoiceItemTotal = 0;
        foreach ($request->item_id as $key => $itemId) {
            if (!$itemId) {
                continue;
            }
            $invoiceItemTotal += (float) ($request->total_price[$key] ?? 0);
        }

        $discountPercent = (float) ($request->dis_percent ?? 0);
        $invoiceDiscount = ($invoiceItemTotal * $discountPercent) / 100;

        foreach ($request->item_id as $key => $itemId) {
            if (!$itemId) {
                continue;
            }
            $item = Item::where('company_id', auth()->user()->company_id)
                ->findOrFail($itemId);
            $qty = (float) ($request->qty[$key] ?? 0);
            $price = (float) ($request->unit_price[$key] ?? 0);
            $itemTotal = (float) ($request->total_price[$key] ?? 0);
            $itemDiscount = 0;

            if ($invoiceItemTotal > 0 && $invoiceDiscount > 0) {
                $itemDiscount = ($itemTotal / $invoiceItemTotal) * $invoiceDiscount;
            }
            $effectivePurchaseValue = $itemTotal - $itemDiscount;
            $effectiveUnitCost = $qty > 0 ? $effectivePurchaseValue / $qty : 0;

            //Save Purchase Item
            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'item_id' => $item->id,
                'qty' => $qty,
                'unit_price' => $price,
                'price' => $request->price[$key] ?? 0,
                'vat_percent' => $request->vat_percent[$key] ?? 0,
                'vat_amount' => $request->vat_amount[$key] ?? 0,
                'total_price' => $itemTotal,
            ]);

            //Inventory Stock In
            InventoryLedger::create([
                'company_id' => auth()->user()->company_id,
                'item_id' => $item->id,
                'module_type' => 'purchase',
                'module_id' => $purchase->id,
                'qty_in' => $qty,
                'qty_out' => 0,
                'unit_cost' => round($effectiveUnitCost, 4),
                'total_cost' => round($effectivePurchaseValue, 4),
                'date' => $purchase->date,
                'created_by' => auth()->id(),
            ]);

            //Update Item Last Purchase Price
            $item->update([
                'item_code' => $request->item_code[$key] ?? $item->item_code,
                'unit_price' => $price,
                'last_purchase_price' => round($effectiveUnitCost, 4),
                'size' => $request->size[$key] ?? $item->size,
            ]);

            $this->recalculateAveragePurchasePrice($item->id);
        }
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

    private function recalculateAveragePurchasePrice(int $itemId): void
    {
        $companyId = auth()->user()->company_id;
        $ledgers = InventoryLedger::where('company_id', $companyId)
            ->where('item_id', $itemId)
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        $stockQty = 0;
        $stockValue = 0;

        foreach ($ledgers as $ledger) {
            $qtyIn = (float) $ledger->qty_in;
            $qtyOut = (float) $ledger->qty_out;
            $unitCost = (float) $ledger->unit_cost;

            if ($qtyIn > 0) {
                $stockQty += $qtyIn;
                $stockValue += ($qtyIn * $unitCost);
            }

            if ($qtyOut > 0) {
                $stockQty -= $qtyOut;
                $stockValue -= ($qtyOut * $unitCost);
            }
        }

        if ($stockQty <= 0 || $stockValue <= 0) {
            $avgPurchasePrice = 0;
        } else {
            $avgPurchasePrice = $stockValue / $stockQty;
        }
        Item::where('company_id', $companyId)
            ->where('id', $itemId)
            ->update([
                'avg_purchase_price' => round($avgPurchasePrice, 4),
            ]);
    }
}
