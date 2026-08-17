<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Company;
use App\Models\Customer;
use App\Models\InventoryLedger;
use App\Models\Item;
use App\Models\Sales;
use App\Models\SalesItems;
use App\Models\Transaction;
use App\Services\InventoryService;
use App\Services\JournalService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    protected InventoryService $inventoryService;
    protected JournalService $journalService;

    public function __construct(InventoryService $inventoryService, JournalService $journalService)
    {
        $this->middleware('auth');
        $this->inventoryService = $inventoryService;
        $this->journalService = $journalService;
    }

    public function sales()
    {
        return view('sales.sales-module');
    }

    public function salesEntry()
    {
        $accounts = Account::where('company_id', auth()->user()->company_id)
            ->where('ac_type', 'asset')
            ->get();

        // Generate new invoice number
        $newSalesNo = $this->generateInvoiceNumber();

        // Get last serial for item code
        $lastSerial = $this->getLastItemSerial();

        return view('sales.sales-add', compact('accounts', 'newSalesNo', 'lastSerial'));
    }

    public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'date' => 'required|date',
            'invoice_no' => 'required|string|max:50',
            'item_id.*' => 'required|exists:items,id',
            'qty.*' => 'required|numeric|min:0.01',
            'sales_price.*' => 'required|numeric|min:0',
            'grand_total' => 'required|numeric|min:0',
            'paid_amt' => 'nullable|numeric|min:0',
            'payment_status' => 'required|in:paid,partial,unpaid',
            'payment_account_id' => 'nullable|exists:accounts,id',
            'payment_method' => 'required|in:cash,bank,cheque,mobile_bank,due',
        ]);

        $paymentAccountId = null;

        if (in_array($request->payment_status, ['paid', 'partial'])) {

            if (!$request->payment_account_id) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'payment_account_id' => 'Payment account is required for paid or partial sales.'
                ]);
            }

            $paymentAccount = Account::where('company_id', auth()->user()->company_id)
                ->where('id', $request->payment_account_id)
                ->where('ac_type', 'asset')
                ->first();

            if (!$paymentAccount) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'payment_account_id' => 'Invalid payment account.'
                ]);
            }

            $paymentAccountId = $paymentAccount->id;
        }

        // Business validation
        $grandTotal = (float) $request->grand_total;
        $paid = (float) ($request->paid_amt ?? 0);
        $status = $request->payment_status;

        $this->validatePaymentAmounts($grandTotal, $paid, $status);

        // Check duplicate invoice (BEFORE transaction)
        if (Sales::where('invoice_no', $request->invoice_no)->exists()) {
            return back()
                ->withErrors(['invoice_no' => 'Invoice already exists: ' . $request->invoice_no])
                ->withInput();
        }

        // Calculate due
        $due = max(0, $grandTotal - $paid);

        try {
            // Save everything in transaction
            DB::transaction(function () use ($request, $grandTotal, $paid, $due, $paymentAccountId) {
                // 1. Find customer
                $customer = Customer::where('company_id', auth()->user()->company_id)
                    ->where('id', $request->customer_id)
                    ->firstOrFail();

                // 2. Create sales
                $sales = Sales::create([
                    'company_id' => auth()->user()->company_id,
                    'customer_id' => $customer->id,
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
                    'payment_status' => $request->payment_status,
                    'pay_receive' => $request->pay_receive,
                    'payment_account_id' => $paymentAccountId,
                    'created_by' => auth()->id(),
                ]);

                // 3. Transaction
                $transaction = Transaction::create([
                    'module_type' => 'sales',
                    'module_id' => $sales->id,
                    'customer_id' => $customer->id,
                    'reference_no' => $sales->invoice_no,
                    'payment_method' => $request->payment_method,
                    'paid_amt' => $paid,
                    'date' => $sales->date,
                ]);

                // 4. Save sales items and calculate COGS
                $totalCogs = $this->saveSalesItems($sales, $request);

                // Recalculate inventory cost for all sold items
                $itemIds = collect($request->item_id)
                    ->filter()
                    ->unique();
                foreach ($itemIds as $itemId) {
                    $this->recalculateSalesInventoryCost(
                        (int) $itemId
                    );
                }

                // 5. Create sales Journal

                $this->createSalesJournal($sales, $transaction);
            });

            return back()->with('success', 'sales saved successfully!');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function salesList(Request $request)
    {
        $query = Sales::with(['customer', 'salesItems.item', 'transactions']);

        // Apply filters
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->byDateRange($request->from_date, $request->to_date);
        }

        if ($request->type === 'customer' && $request->filled('customer_id')) {
            $query->byCustomer($request->customer_id);
        }

        if ($request->type === 'item' && $request->filled('item_id')) {
            $query->whereHas('salesItems', function ($q) use ($request) {
                $q->where('item_id', $request->item_id);
            });
        }

        $sales = $query->get();
        $customers = Customer::all();
        $items = Item::all();

        return view('sales.sales-list', compact('sales', 'customers', 'items'));
    }

    public function salesEdit($id)
    {
        $sales = Sales::with([
            'customer',
            'user',
            'salesItems.item.category',
            'transactions'
        ])->findOrFail($id);

        $accounts = Account::all();
        $lastSerial = $this->getLastItemSerial();
        $totalPaid = $sales->total_paid;
        $due = $sales->remaining_due;

        return view('sales.sales-edit', compact(
            'sales',
            'accounts',
            'lastSerial',
            'totalPaid',
            'due'
        ));
    }

    public function salesUpdate(Request $request, $id)
    {
        $validated = $request->validate([

            'customer_id' => 'required|exists:customers,id',

            'date' => 'required|date',

            'invoice_no' => 'required|string|max:50',

            'item_id.*' => 'required|exists:items,id',

            'qty.*' => 'required|numeric|min:0.01',

            'sales_price.*' => 'required|numeric|min:0',

            'grand_total' => 'required|numeric|min:0',

            'paid_amt' => 'nullable|numeric|min:0',

            'payment_status' =>
            'required|in:paid,partial,unpaid',

            'payment_account_id' =>
            'nullable|exists:accounts,id',

            'payment_method' =>
            'required|in:cash,bank,cheque,mobile_bank,due',
        ]);


        $companyId = auth()->user()->company_id;


        $sales = Sales::where('company_id', $companyId)
            ->where('id', $id)
            ->firstOrFail();


        // ============================================================
        // OLD ITEM IDs
        //
        // Must remember before deleting old sales items
        // ============================================================

        $oldItemIds = SalesItems::where(
            'sales_id',
            $sales->id
        )
            ->pluck('item_id')
            ->unique()
            ->values();


        // ============================================================
        // PAYMENT ACCOUNT
        // ============================================================

        $paymentAccountId = null;

        if (
            in_array(
                $request->payment_status,
                ['paid', 'partial']
            )
        ) {

            if (!$request->payment_account_id) {

                throw \Illuminate\Validation\ValidationException::withMessages([
                    'payment_account_id' =>
                    'Payment account is required for paid or partial sales.'
                ]);
            }


            $paymentAccount = Account::where(
                'company_id',
                $companyId
            )
                ->where(
                    'id',
                    $request->payment_account_id
                )
                ->where(
                    'ac_type',
                    'asset'
                )
                ->first();


            if (!$paymentAccount) {

                throw \Illuminate\Validation\ValidationException::withMessages([
                    'payment_account_id' =>
                    'Invalid payment account.'
                ]);
            }


            $paymentAccountId = $paymentAccount->id;
        }


        // ============================================================
        // PAYMENT
        // ============================================================

        $grandTotal = (float) $request->grand_total;

        $paid = (float) ($request->paid_amt ?? 0);

        $status = $request->payment_status;


        $this->validatePaymentAmounts(
            $grandTotal,
            $paid,
            $status
        );


        $due = max(
            0,
            $grandTotal - $paid
        );


        // ============================================================
        // DUPLICATE INVOICE
        // ============================================================

        $invoiceExists = Sales::where(
            'company_id',
            $companyId
        )
            ->where(
                'invoice_no',
                $request->invoice_no
            )
            ->where(
                'id',
                '!=',
                $sales->id
            )
            ->exists();


        if ($invoiceExists) {

            return back()
                ->withErrors([
                    'invoice_no' =>
                    'Invoice already exists: '
                        . $request->invoice_no
                ])
                ->withInput();
        }


        try {

            DB::transaction(function () use (
                $request,
                $sales,
                $companyId,
                $grandTotal,
                $paid,
                $due,
                $paymentAccountId,
                $oldItemIds
            ) {


                // ====================================================
                // NEW CUSTOMER
                // ====================================================

                $customer = Customer::where(
                    'company_id',
                    $companyId
                )
                    ->where(
                        'id',
                        $request->customer_id
                    )
                    ->firstOrFail();


                // ====================================================
                // DELETE OLD INVENTORY LEDGER
                // ====================================================

                InventoryLedger::where(
                    'company_id',
                    $companyId
                )
                    ->where(
                        'module_type',
                        'sales'
                    )
                    ->where(
                        'module_id',
                        $sales->id
                    )
                    ->delete();


                // ====================================================
                // DELETE OLD SALES ITEMS
                // ====================================================

                SalesItems::where(
                    'sales_id',
                    $sales->id
                )->delete();


                // ====================================================
                // DELETE OLD TRANSACTION
                // ====================================================

                Transaction::where(
                    'module_type',
                    'sales'
                )
                    ->where(
                        'module_id',
                        $sales->id
                    )
                    ->delete();


                // ====================================================
                // DELETE OLD JOURNAL
                // ====================================================

                $journalIds = DB::table(
                    'journal_entries'
                )
                    ->where(
                        'company_id',
                        $companyId
                    )
                    ->where(
                        'module_type',
                        'sales'
                    )
                    ->where(
                        'module_id',
                        $sales->id
                    )
                    ->pluck('id');


                if ($journalIds->isNotEmpty()) {

                    DB::table('journal_items')
                        ->whereIn(
                            'journal_entry_id',
                            $journalIds
                        )
                        ->delete();


                    DB::table('journal_entries')
                        ->whereIn(
                            'id',
                            $journalIds
                        )
                        ->delete();
                }


                // ====================================================
                // UPDATE SALES
                // ====================================================

                $sales->update([

                    'customer_id' =>
                    $customer->id,

                    'date' =>
                    $request->date,

                    'invoice_no' =>
                    $request->invoice_no,

                    'sub_total' =>
                    (float) ($request->sub_total ?? 0),

                    'vat_amt' =>
                    (float) ($request->vat_amt ?? 0),

                    'dis_percent' =>
                    (float) ($request->dis_percent ?? 0),

                    'dis_amt' =>
                    (float) ($request->dis_amt ?? 0),

                    'grand_total' =>
                    $grandTotal,

                    'due_amt' =>
                    $due,

                    'reference' =>
                    $request->reference,

                    'narration' =>
                    $request->narration,

                    'payment_status' =>
                    $request->payment_status,

                    'pay_receive' =>
                    $request->pay_receive,

                    'payment_account_id' =>
                    $paymentAccountId,

                ]);


                // ====================================================
                // NEW TRANSACTION
                // ====================================================

                $transaction = Transaction::create([

                    'module_type' => 'sales',

                    'module_id' => $sales->id,

                    'customer_id' =>
                    $customer->id,

                    'reference_no' =>
                    $sales->invoice_no,

                    'payment_method' =>
                    $request->payment_method,

                    'paid_amt' =>
                    $paid,

                    'date' =>
                    $sales->date,
                ]);


                // ====================================================
                // NEW SALES ITEMS + INVENTORY
                // ====================================================

                $this->saveSalesItems(
                    $sales,
                    $request
                );


                // ====================================================
                // AFFECTED ITEM IDS
                // ====================================================

                $newItemIds = collect(
                    $request->item_id
                )
                    ->filter()
                    ->unique();


                $affectedItemIds = $oldItemIds
                    ->merge($newItemIds)
                    ->unique();


                // ====================================================
                // RECALCULATE INVENTORY COST
                // ====================================================

                foreach ($affectedItemIds as $itemId) {

                    $this->recalculateSalesInventoryCost(
                        (int) $itemId
                    );
                }


                // ====================================================
                // CREATE JOURNAL
                // ====================================================

                $this->createSalesJournal(
                    $sales,
                    $transaction
                );
            });


            return redirect()
                ->route('sales.list')
                ->with(
                    'success',
                    'Sales updated successfully!'
                );
        } catch (\Throwable $e) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }

    public function salesDelete($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $companyId = auth()->user()->company_id;

                $sales = Sales::where('company_id', $companyId)
                    ->where('id', $id)
                    ->firstOrFail();

                $affectedItemIds = SalesItems::where('sales_id', $sales->id)
                    ->pluck('item_id')
                    ->unique()
                    ->values();

                InventoryLedger::where('company_id', $companyId)
                    ->where('module_type', 'sales')
                    ->where('module_id', $sales->id)
                    ->delete();

                SalesItems::where('sales_id', $sales->id)->delete();

                Transaction::where('module_type', 'sales')
                    ->where('module_id', $sales->id)
                    ->delete();

                $journalIds = DB::table('journal_entries')
                    ->where('company_id', $companyId)
                    ->where('module_type', 'sales')
                    ->where('module_id', $sales->id)
                    ->pluck('id');

                if ($journalIds->isNotEmpty()) {
                    DB::table('journal_items')
                        ->whereIn('journal_entry_id', $journalIds)
                        ->delete();

                    DB::table('journal_entries')
                        ->whereIn('id', $journalIds)
                        ->delete();
                }

                $sales->delete();

                foreach ($affectedItemIds as $itemId) {
                    $this->recalculateSalesInventoryCost(
                        (int) $itemId
                    );
                }
            });

            return redirect()
                ->route('sales.list')
                ->with(
                    'success',
                    'Sales deleted successfully!'
                );
        } catch (\Throwable $e) {
            return back()
                ->with(
                    'error',
                    'Sales delete failed: ' . $e->getMessage()
                );
        }
    }

    public function salesDetails($id)
    {
        $sales = Sales::with([
            'customer',
            'salesItems.item.category',
            'transactions',
            'customerPaymentDetails.customerPayment',
            'salesReturns.salesReturnItems.item',
            'user',
        ])->findOrFail($id);

        $originalSales = (float) $sales->grand_total;
        $totalReturn = (float) $sales->salesReturns->sum('grand_total');
        $netSales = max(0, $originalSales - $totalReturn);
        $initialPayment = (float) $sales->transactions
            ->where('module_type', 'sales')
            ->sum('paid_amt');
        $customerPayment = (float) $sales->customerPaymentDetails()
            ->sum('paid_amount');
        $totalPaid = $initialPayment + $customerPayment;
        $supplierCredit = max(0, $totalPaid - $netSales);
        $due = max(0, $netSales - $totalPaid);

        if ($supplierCredit > 0) {
            $paymentStatus = 'Credit';
        } elseif ($due <= 0 && $netSales > 0) {
            $paymentStatus = 'Paid';
        } elseif ($totalPaid > 0) {
            $paymentStatus = 'Partial';
        } else {
            $paymentStatus = 'Unpaid';
        }
        $transactions = $sales->transactions;
        $returnCount = $sales->salesReturns->count();
        $customerPaymentCount = $sales->customerPaymentDetails->count();
        $returnedItems = $sales->salesReturns
            ->flatMap(function ($salesReturn) {
                return $salesReturn->salesReturnItems;
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
        $returnHistory = $sales->salesReturns
            ->sortByDesc('date');
        $customerPaymentHistory = $sales->customerPaymentDetails
            ->sortByDesc(function ($detail) {
                return optional($detail->customerPayment)->date;
            });
        return view('sales.sales-details', compact(
            'sales',
            'transactions',
            'originalSales',
            'totalReturn',
            'netSales',
            'initialPayment',
            'customerPayment',
            'totalPaid',
            'due',
            'supplierCredit',
            'paymentStatus',
            'returnCount',
            'customerPaymentCount',
            'returnedItems',
            'returnedQtyByItem',
            'returnedAmountByItem',
            'returnHistory',
            'customerPaymentHistory'
        ));
    }

    public function downloadListPdf(Request $request)
    {
        $query = sales::with(['customer', 'salesItems.item', 'transactions']);

        // Apply filters
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('date', [$request->from_date, $request->to_date]);
        }

        if ($request->type === 'customer' && $request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->type === 'item' && $request->filled('item_id')) {
            $query->whereHas('salesItems', function ($q) use ($request) {
                $q->where('item_id', $request->item_id);
            });
        }

        $sales = $query->latest('date')->get();

        // Get company name
        $companyName = Company::find(auth()->user()->company_id)->name ?? 'Company Name';

        $pdf = Pdf::loadView('sales.sales-list-pdf', [
            'sales' => $sales,
            'companyName' => $companyName,
            'fromDate' => $request->from_date,
            'toDate' => $request->to_date,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('sales_List_' . now()->format('d-m-Y') . '.pdf');
    }

    public function downloadPdf($id)
    {
        $sales = Sales::with([
            'customer',
            'salesItems.item.category',
            'transactions',
            'customerPaymentDetails.customerPayment',
            'salesReturns.salesReturnItems.item',
            'user',
        ])->findOrFail($id);

        $originalSales = (float) $sales->grand_total;
        $totalReturn = (float) $sales->salesReturns
            ->sum('grand_total');
        $netSales = max(0, $originalSales - $totalReturn);

        $initialPaymentRows = $sales->transactions
            ->where('module_type', 'sales')
            ->filter(function ($transaction) {
                return (float) $transaction->paid_amt > 0;
            })
            ->values();
        $initialPayment = (float) $initialPaymentRows->sum('paid_amt');
        $hasInitialPayment = $initialPayment > 0;

        $customerPaymentHistory = $sales->customerPaymentDetails
            ->filter(function ($detail) {
                return (float) $detail->paid_amount > 0;
            })
            ->sortByDesc(function ($detail) {
                return optional($detail->customerPayment)->date;
            })
            ->values();
        $customerPayment = (float) $customerPaymentHistory->sum('paid_amount');
        $hasCustomerPayment = $customerPaymentHistory->isNotEmpty();
        $totalPaid = $initialPayment + $customerPayment;
        $supplierCredit = max(0, $totalPaid - $netSales);
        $due = max(0, $netSales - $totalPaid);

        if ($supplierCredit > 0) {
            $paymentStatus = 'Credit';
        } elseif ($due <= 0 && $netSales > 0) {
            $paymentStatus = 'Paid';
        } elseif ($totalPaid > 0) {
            $paymentStatus = 'Partial';
        } else {
            $paymentStatus = 'Unpaid';
        }

        $transactions = $sales->transactions;
        $returnCount = $sales->salesReturns->count();
        $customerPaymentCount = $customerPaymentHistory->count();
        $returnedItems = $sales->salesReturns
            ->flatMap(function ($salesReturn) {
                return $salesReturn->salesReturnItems;
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
        $returnHistory = $sales->salesReturns
            ->sortByDesc('date')
            ->values();
        $hasReturns = $returnedQtyByItem->sum() > 0;
        $companyName = Company::find(auth()->user()->company_id)->name ?? 'Company Name';
        $pdf = Pdf::loadView('sales.invoice-pdf', compact(
            'sales',
            'originalSales',
            'totalReturn',
            'netSales',
            'initialPaymentRows',
            'initialPayment',
            'hasInitialPayment',
            'customerPaymentHistory',
            'customerPayment',
            'hasCustomerPayment',
            'totalPaid',
            'due',
            'supplierCredit',
            'paymentStatus',
            'transactions',
            'returnCount',
            'customerPaymentCount',
            'returnedItems',
            'returnedQtyByItem',
            'returnedAmountByItem',
            'returnHistory',
            'hasReturns',
            'companyName'
        ));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download(
            'Sales-Invoice-' . $sales->invoice_no . '.pdf'
        );
    }

    private function createSalesJournal(Sales $sales, Transaction $transaction): void
    {
        $grandTotal = (float) $sales->grand_total;
        $paidAmount = (float) ($transaction->paid_amt ?? 0);
        $dueAmount = max(0, $grandTotal - $paidAmount);
        $subTotal = (float) ($sales->sub_total ?? 0);
        $discount = (float) ($sales->dis_amt ?? 0);
        $vatAmount = (float) ($sales->vat_amt ?? 0);
        $netSales = $subTotal - $discount;
        $journalItems = [];

        // SALES REVENUE ACCOUNT
        $salesRevenueAccountId = Account::where('company_id', $sales->company_id)
            ->where('account_name', 'Sales Revenue')
            ->value('id');

        if (!$salesRevenueAccountId) {
            throw new \Exception(
                'Sales Revenue account not found.'
            );
        }

        // CUSTOMER RECEIVABLE ACCOUNT
        $customerReceivableAccountId = Account::where('company_id', $sales->company_id)
            ->where('account_name', 'Customer Receivable')
            ->value('id');

        if (!$customerReceivableAccountId) {
            throw new \Exception(
                'Customer Receivable account not found.'
            );
        }

        // SALES REVENUE
        if ($netSales > 0) {
            $journalItems[] = [
                'account' => $salesRevenueAccountId,
                'debit' => 0,
                'credit' => $netSales,
            ];
        }

        // PAID
        if ($sales->payment_status === 'paid') {
            if (!$sales->payment_account_id) {
                throw new \Exception(
                    'Payment account is required.'
                );
            }
            $journalItems[] = [
                'account' => $sales->payment_account_id,
                'debit' => $grandTotal,
                'credit' => 0,
                'customer_id' => $sales->customer_id,
            ];
        }

        // UNPAID
        elseif ($sales->payment_status === 'unpaid') {
            $journalItems[] = [
                'account' => $customerReceivableAccountId,
                'debit' => $grandTotal,
                'credit' => 0,
                'customer_id' => $sales->customer_id,
            ];
        }

        // PARTIAL
        elseif ($sales->payment_status === 'partial') {
            if (!$sales->payment_account_id) {
                throw new \Exception(
                    'Payment account is required for partial payment.'
                );
            }

            // Paid portion
            if ($paidAmount > 0) {
                $journalItems[] = [
                    'account' => $sales->payment_account_id,
                    'debit' => $paidAmount,
                    'credit' => 0,
                    'customer_id' => $sales->customer_id,
                ];
            }

            // Due portion
            if ($dueAmount > 0) {
                $journalItems[] = [
                    'account' => $customerReceivableAccountId,
                    'debit' => $dueAmount,
                    'credit' => 0,
                    'customer_id' => $sales->customer_id,
                ];
            }
        }

        // VAT PAYABLE
        if ($vatAmount > 0) {
            $vatPayableAccountId = Account::where('company_id', $sales->company_id)
                ->where('account_name', 'VAT Payable')
                ->value('id');
            if (!$vatPayableAccountId) {
                throw new \Exception(
                    'VAT Payable account not found.'
                );
            }
            $journalItems[] = [
                'account' => $vatPayableAccountId,
                'debit' => 0,
                'credit' => $vatAmount,
            ];
        }

        // SAVE JOURNAL
        $this->journalService->createJournal([
            'company_id' => $sales->company_id,
            'module_type' => 'sales',
            'module_id' => $sales->id,
            'reference_no' => $sales->invoice_no,
            'date' => $sales->date,
            'particulars' => $sales->narration ?? 'Sales Entry',
            'items' => $journalItems,
        ]);
    }

    private function generateInvoiceNumber(): string
    {
        $lastSales = Sales::where('company_id', auth()->user()->company_id)
            ->latest('id')
            ->first();

        $number = 1;

        if ($lastSales && $lastSales->invoice_no) {
            preg_match('/\d+/', $lastSales->invoice_no, $matches);
            if (isset($matches[0])) {
                $number = (int) $matches[0] + 1;
            }
        }
        do {
            $invoiceNo = 'SEL-' . str_pad($number, 4, '0', STR_PAD_LEFT);
            $exists = Sales::where('company_id', auth()->user()->company_id)
                ->where('invoice_no', $invoiceNo)
                ->exists();

            if ($exists) {
                $number++;
            }
        } while ($exists);

        return $invoiceNo;
    }

    private function getLastItemSerial(): int
    {
        $lastItem = Item::latest('id')->first();

        if ($lastItem && preg_match('/\d+$/', $lastItem->item_code, $matches)) {
            return (int) $matches[0];
        }

        return 0;
    }

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

    private function saveSalesItems(Sales $sales, Request $request): float
    {
        $totalCogs = 0;
        foreach ($request->item_id as $key => $itemId) {
            if (!$itemId) {
                continue;
            }
            $item = Item::where('company_id', auth()->user()->company_id)
                ->where('id', $itemId)
                ->firstOrFail();

            $qty = (float) $request->qty[$key];
            $salesPrice = (float) $request->sales_price[$key];

            /*SAVE SALES ITEM*/
            SalesItems::create([
                'sales_id' => $sales->id,
                'item_id' => $item->id,
                'qty' => $qty,
                'sales_price' => $salesPrice,
                'price' => $request->price[$key] ?? 0,
                'item_vat_percent' => $request->item_vat_percent[$key] ?? 0,
                'item_vat_amt' => $request->item_vat_amt[$key] ?? 0,
                'total_price' => $request->total_price[$key] ?? 0,
            ]);

            /*INVENTORY STOCK OUT*/
            InventoryLedger::create([
                'company_id' => auth()->user()->company_id,
                'item_id' => $item->id,
                'module_type' => 'sales',
                'module_id' => $sales->id,
                'qty_in' => 0,
                'qty_out' => $qty,

                /* IMPORTANT: This is COST PRICE, not SALES PRICE.*/
                'unit_cost' => 0,
                'total_cost' => 0,
                'date' => $sales->date,
                'created_by' => auth()->id(),
            ]);
        }
        return $totalCogs;
    }

    private function recalculateSalesInventoryCost(int $itemId): void
    {
        $companyId = auth()->user()->company_id;

        // ============================================================
        // STOCK STATE
        // ============================================================

        $availableQty = 0.0;
        $availableCost = 0.0;


        // ============================================================
        // GET ALL INVENTORY LEDGERS
        //
        // sales / Production / other stock IN
        // + Sales stock OUT
        //
        // Chronological order
        // ============================================================

        $ledgers = InventoryLedger::where('company_id', $companyId)
            ->where('item_id', $itemId)
            ->orderBy('date')
            ->orderBy('id')
            ->get();


        foreach ($ledgers as $ledger) {

            // ========================================================
            // STOCK IN
            // ========================================================

            if ((float) $ledger->qty_in > 0) {

                $qtyIn = (float) $ledger->qty_in;
                $costIn = (float) $ledger->total_cost;

                $availableQty += $qtyIn;
                $availableCost += $costIn;

                continue;
            }


            // ========================================================
            // STOCK OUT
            // ========================================================
            if ((float) $ledger->qty_out <= 0) {
                continue;
            }

            $outQty = (float) $ledger->qty_out;

            if ($ledger->module_type === 'sales') {
                if ($availableQty < $outQty) {
                    throw new \Exception(
                        "Insufficient stock while recalculating item ID: {$itemId}. "
                            . "Required: {$outQty}, Available: {$availableQty}"
                    );
                }

                // Current weighted average cost
                $unitCost = $availableQty > 0
                    ? ($availableCost / $availableQty)
                    : 0;
                $totalCost = $outQty * $unitCost;

                // Update sales ledger
                $ledger->update([
                    'unit_cost' => round($unitCost, 6),
                    'total_cost' => round($totalCost, 2),
                ]);

                // Reduce stock
                $availableQty -= $outQty;
                $availableCost -= $totalCost;
            } else {
                $totalCost = (float) $ledger->total_cost;
                $availableQty -= $outQty;
                $availableCost -= $totalCost;
            }

            if ($availableQty < 0.000001) {
                $availableQty = 0;
            }
            if ($availableCost < 0.000001) {
                $availableCost = 0;
            }
        }
    }
}
