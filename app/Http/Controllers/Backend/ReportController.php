<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\InventoryLedger;
use App\Models\Investment;
use App\Models\Item;
use App\Models\Partner;
use App\Models\Purchase;
use App\Models\Transaction;
use App\Models\Vendor;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function report()
    {
        $partners = Partner::all();
        $partnerCount = $partners->count();
        $investmentCount = Investment::count();

        return view('report.report-module', compact(
            'partners',
            'partnerCount',
            'investmentCount'
        ));
    }
    public function stockReport(Request $request)
    {

        $type = $request->type ?? 'all';
        $query = Item::with('category')

            ->withSum(['inventoryLedgers as opening_stock_sum' => function ($q) {
                $q->where('module_type', 'opening');
            }], 'qty_in')

            ->withSum(['inventoryLedgers as purchase_stock_sum' => function ($q) {
                $q->where('module_type', 'purchase');
            }], 'qty_in')

            ->withSum(['inventoryLedgers as production_in_sum' => function ($q) {
                $q->where('module_type', 'production');
            }], 'qty_in')

            ->withSum(['inventoryLedgers as sales_stock_sum' => function ($q) {
                $q->where('module_type', 'sales');
            }], 'qty_out')

            ->withSum(['inventoryLedgers as consume_sum' => function ($q) {
                $q->where('module_type', 'production');
            }], 'qty_out')

            ->withSum(['inventoryLedgers as purchase_return_sum' => function ($q) {
                $q->where('module_type', 'purchase_return');
            }], 'qty_out')

            ->withSum(['inventoryLedgers as sales_return_sum' => function ($q) {
                $q->where('module_type', 'sales_return');
            }], 'qty_in');

        // 🔥 FILTER LOGIC
        if ($request->type == 'item' && $request->item_id) {
            $query->where('id', $request->item_id);
        }

        $stocks = $query->get();
        $items = Item::all();

        return view('report.stock-report', compact('stocks', 'items', 'type'));
    }

    // app/Http/Controllers/Backend/ReportController.php

    // Add this method to your existing ReportController
    public function stockReportPdf(Request $request)
    {
        $type = $request->type ?? 'all';

        $query = Item::with('category')
            ->withSum(['inventoryLedgers as opening_stock_sum' => function ($q) {
                $q->where('module_type', 'opening');
            }], 'qty_in')
            ->withSum(['inventoryLedgers as purchase_stock_sum' => function ($q) {
                $q->where('module_type', 'purchase');
            }], 'qty_in')
            ->withSum(['inventoryLedgers as production_in_sum' => function ($q) {
                $q->where('module_type', 'production');
            }], 'qty_in')
            ->withSum(['inventoryLedgers as sales_stock_sum' => function ($q) {
                $q->where('module_type', 'sales');
            }], 'qty_out')
            ->withSum(['inventoryLedgers as consume_sum' => function ($q) {
                $q->where('module_type', 'production');
            }], 'qty_out')
            ->withSum(['inventoryLedgers as purchase_return_sum' => function ($q) {
                $q->where('module_type', 'purchase_return');
            }], 'qty_out')
            ->withSum(['inventoryLedgers as sales_return_sum' => function ($q) {
                $q->where('module_type', 'sales_return');
            }], 'qty_in');

        // Filter by item
        if ($request->type == 'item' && $request->item_id) {
            $query->where('id', $request->item_id);
        }

        $stocks = $query->get();
        $companyName = Company::find(auth()->user()->company_id)->name ?? 'Company Name';
        // Generate PDF
        $pdf = Pdf::loadView('report.stock-report-pdf', [
            'stocks' => $stocks,
            'type' => $type,
            'filterDate' => now()->format('d-m-Y'),
            'companyName' => $companyName,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('Stock_Report_' . now()->format('d-m-Y') . '.pdf');
    }

    private function getItemLedgerData(Request $request)
    {
        $item = null;
        $ledgers = collect();
        $openingBalance = 0;

        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        if ($request->filled('item_id')) {

            $item = Item::findOrFail($request->item_id);

            if ($fromDate) {

                $openingBalance = InventoryLedger::where('item_id', $item->id)
                    ->whereDate('date', '<', $fromDate)
                    ->sum(DB::raw('qty_in - qty_out'));
            } else {

                $openingBalance = InventoryLedger::where('item_id', $item->id)
                    ->where('module_type', 'opening')
                    ->sum('qty_in');
            }

            $ledgers = InventoryLedger::where('item_id', $item->id)
                ->where('module_type', '!=', 'opening')
                ->when($fromDate, fn($q) => $q->whereDate('date', '>=', $fromDate))
                ->when($toDate, fn($q) => $q->whereDate('date', '<=', $toDate))
                ->orderBy('date')
                ->orderBy('id')
                ->get();
        }
        $companyName = Company::find(auth()->user()->company_id)->name ?? 'Company Name';

        return compact(
            'item',
            'ledgers',
            'openingBalance',
            'fromDate',
            'toDate',
            'companyName'
        );
    }

    public function itemLedger(Request $request)
    {
        $items = Item::orderBy('item_name')->get();

        $data = $this->getItemLedgerData($request);

        return view(
            'report.item-ledger',
            array_merge(['items' => $items], $data)
        );
    }

    public function itemLedgerPdf(Request $request)
    {
        $data = $this->getItemLedgerData($request);

        $pdf = Pdf::loadView('report.item_ledger_pdf', $data);

        return $pdf->download('item-ledger.pdf');
    }

    public function vendorDue(Request $request)
    {
        // 🔹 dropdown এর জন্য (always all vendors)
        $vendors = Vendor::all();

        // 🔹 report এর জন্য
        $query = Vendor::query();

        if ($request->type == 'supplier' && $request->vendor_id) {
            $query->where('id', $request->vendor_id);
        }

        $filteredVendors = $query->get();

        $reportData = [];

        foreach ($filteredVendors as $vendor) {

            $opening = 0;

            $bill = $vendor->purchase->sum('grand_total');
            $payment = $vendor->transactions->sum('paid_amt');
            $return = $vendor->transactions->sum('return_amt');

            $balance = ($opening + $bill) - ($payment + $return);

            $reportData[] = [
                'vendor' => $vendor,
                'opening' => $opening,
                'bill' => $bill,
                'payment' => $payment,
                'return' => $return,
                'balance' => $balance,
            ];
        }

        return view('report.vendor-due', compact('vendors', 'reportData'));
    }
    public function vendorLedger()
    {
        $vendors = Vendor::select('id', 'v_name')->get();
        return view('report.vendor-ledger', compact('vendors'));
    }
    public function vendorLedgerData(Request $request)
    {
        $vendor = Vendor::findOrFail($request->vendor_id);

        $from = $request->from_date;
        $to = $request->to_date;
        $invoice = $request->invoice;

        // =========================
        // PURCHASE (QUERY BASED)
        // =========================
        $purchases = Purchase::where('vendor_id', $vendor->id)
            ->when($invoice, fn($q) => $q->where('invoice_no', 'like', "%$invoice%"))
            ->when($from, fn($q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('date', '<=', $to))
            ->get()
            ->map(function ($item) {
                return [
                    'date' => \Carbon\Carbon::parse($item->date)->format('d-m-Y'),
                    'sort_date' => $item->date,
                    'particular' => $item->narration ?? 'Purchase',
                    'type' => 'Purchase',
                    'vch_no' => $item->invoice_no,
                    'debit' => $item->grand_total,
                    'credit' => 0,
                ];
            });

        // =========================
        // PAYMENT
        // =========================
        $payments = Transaction::where('vendor_id', $vendor->id)
            ->where('paid_amt', '>', 0)
            ->when($invoice, fn($q) => $q->where('reference_no', 'like', "%$invoice%"))
            ->when($from, fn($q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('date', '<=', $to))
            ->get()
            ->map(function ($item) {
                return [
                    'date' => \Carbon\Carbon::parse($item->date)->format('d-m-Y'),
                    'sort_date' => $item->date,
                    'particular' => 'Payment',
                    'type' => 'Payment',
                    'vch_no' => $item->reference_no,
                    'debit' => 0,
                    'credit' => $item->paid_amt,
                ];
            });

        // =========================
        // RETURN
        // =========================
        $returns = Transaction::where('vendor_id', $vendor->id)
            ->where('return_amt', '>', 0)
            ->when($invoice, fn($q) => $q->where('reference_no', 'like', "%$invoice%"))
            ->when($from, fn($q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('date', '<=', $to))
            ->get()
            ->map(function ($item) {
                return [
                    'date' => \Carbon\Carbon::parse($item->date)->format('d-m-Y'),
                    'sort_date' => $item->date,
                    'particular' => 'Return',
                    'type' => 'Return',
                    'vch_no' => $item->reference_no,
                    'debit' => 0,
                    'credit' => $item->return_amt,
                ];
            });

        // =========================
        // MERGE + SORT
        // =========================
        $ledger = collect()
            ->merge($purchases)
            ->merge($payments)
            ->merge($returns)
            ->sortBy('sort_date')
            ->values();

        // =========================
        // OPENING (FROM DB)
        // =========================
        // =========================
        // OPENING BALANCE
        // =========================
        $opening = $vendor->opening_balance ?? 0;

        if ($from) {

            // Purchase before from date
            $purchaseBefore = Purchase::where('vendor_id', $vendor->id)
                ->whereDate('date', '<', $from)
                ->sum('grand_total');

            // Payment before from date
            $paymentBefore = Transaction::where('vendor_id', $vendor->id)
                ->where('paid_amt', '>', 0)
                ->whereDate('date', '<', $from)
                ->sum('paid_amt');

            // Return before from date
            $returnBefore = Transaction::where('vendor_id', $vendor->id)
                ->where('return_amt', '>', 0)
                ->whereDate('date', '<', $from)
                ->sum('return_amt');

            $opening += $purchaseBefore;
            $opening -= $paymentBefore;
            $opening -= $returnBefore;
        }

        // =========================
        // RUNNING BALANCE
        // =========================
        $running = $opening;

        $ledger = $ledger->map(function ($row) use (&$running) {
            $running += $row['debit'] - $row['credit'];
            $row['balance'] = $running;
            return $row;
        });

        // =========================
        // TOTALS
        // =========================
        $totalDebit = $ledger->sum('debit');
        $totalCredit = $ledger->sum('credit');

        return response()->json([
            'vendor' => $vendor->v_name,
            'opening' => $opening,
            'ledger' => $ledger,
            'summary' => [
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'closing' => $running,
            ]
        ]);
    }

    public function vendorDuePdf(Request $request)
    {
        $type = $request->type ?? 'all';
        // 🔹 সব vendor নাও
        $query = Vendor::query();

        if ($request->type == 'supplier' && $request->vendor_id) {
            $query->where('id', $request->vendor_id);
        }

        $vendors = $query->get();

        $reportData = [];

        foreach ($vendors as $vendor) {

            $opening = $vendor->opening_balance ?? 0;

            $bill = Purchase::where('vendor_id', $vendor->id)->sum('grand_total');
            $payment = Transaction::where('vendor_id', $vendor->id)->sum('paid_amt');
            $return = Transaction::where('vendor_id', $vendor->id)->sum('return_amt');

            $balance = ($opening + $bill) - ($payment + $return);

            $reportData[] = [
                'vendor' => $vendor,
                'opening' => $opening,
                'bill' => $bill,
                'payment' => $payment,
                'return' => $return,
                'balance' => $balance,
            ];
        }
        $companyName = Company::find(auth()->user()->company_id)->name ?? 'Company Name';
        // 🔹 PDF
        $pdf = Pdf::loadView('report.vendor-due-pdf', [
            'reportData' => $reportData,
            'type' => $type,
            'filterDate' => now()->format('d-m-Y'),
            'companyName' => $companyName,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('vendor-due-' . now()->format('d-m-Y') . '.pdf');
    }

    public function vendorLedgerPdf(Request $request)
    {
        $vendor = Vendor::findOrFail($request->vendor_id);

        // 🔹 FIX null issue
        $from = $request->from_date == 'null' ? null : $request->from_date;
        $to = $request->to_date == 'null' ? null : $request->to_date;
        $invoice = $request->invoice == 'null' ? null : $request->invoice;

        // =========================
        // PURCHASE
        // =========================
        $purchases = Purchase::where('vendor_id', $vendor->id)
            ->when($invoice, fn($q) => $q->where('invoice_no', 'like', "%$invoice%"))
            ->when($from, fn($q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('date', '<=', $to))
            ->get();

        // =========================
        // PAYMENT
        // =========================
        $payments = Transaction::where('vendor_id', $vendor->id)
            ->where('paid_amt', '>', 0)
            ->when($from, fn($q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('date', '<=', $to))
            ->get();

        // =========================
        // RETURN
        // =========================
        $returns = Transaction::where('vendor_id', $vendor->id)
            ->where('return_amt', '>', 0)
            ->when($from, fn($q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('date', '<=', $to))
            ->get();

        // =========================
        // MERGE
        // =========================
        $ledger = collect();

        foreach ($purchases as $p) {
            $ledger->push([
                'date' => $p->date,
                'particular' => $p->narration ?? 'Purchase',
                'type' => 'Purchase',
                'vch_no' => $p->invoice_no,
                'debit' => $p->grand_total,
                'credit' => 0,
            ]);
        }

        foreach ($payments as $t) {
            $ledger->push([
                'date' => $t->date,
                'particular' => 'Payment',
                'type' => 'Payment',
                'vch_no' => $t->reference_no,
                'debit' => 0,
                'credit' => $t->paid_amt,
            ]);
        }

        foreach ($returns as $t) {
            $ledger->push([
                'date' => $t->date,
                'particular' => 'Return',
                'type' => 'Return',
                'vch_no' => $t->reference_no,
                'debit' => 0,
                'credit' => $t->return_amt,
            ]);
        }

        // =========================
        // SORT
        // =========================
        $ledger = $ledger->sortBy('date')->values();

        // =========================
        // OPENING + RUNNING (FIXED)
        // =========================
        $opening = $vendor->opening_balance ?? 0;
        $running = $opening;

        $ledger = $ledger->map(function ($row) use (&$running) {
            $running += $row['debit'] - $row['credit'];
            $row['balance'] = $running;
            return $row;
        });

        // =========================
        // TOTAL
        // =========================
        $totalDebit = $ledger->sum('debit');
        $totalCredit = $ledger->sum('credit');
        $companyName = Company::find(auth()->user()->company_id)->name ?? 'Company Name';
        // =========================
        // PDF
        // =========================
        $pdf = Pdf::loadView('report.vendor-ledger-pdf', [
            'vendor' => $vendor,
            'ledger' => $ledger,
            'opening' => $opening,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'closing' => $running,
            'from' => $from,
            'to' => $to,
            'companyName' => $companyName,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('vendor-ledger.pdf');
    }
}
