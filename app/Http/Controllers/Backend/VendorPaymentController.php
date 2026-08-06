<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Purchase;
use App\Models\Transaction;
use App\Models\Vendor;
use App\Models\VendorPayment;
use App\Models\VendorPaymentDetails;
use App\Services\JournalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function paymentCreate()
    {
        $accounts = Account::orderBy('account_name')->get();

        // শুধুমাত্র যাদের Due Invoice আছে
        $vendors = Vendor::select('id', 'v_name')
            ->whereHas('purchase', function ($query) {

                $query->where(function ($q) {

                    $q->whereRaw("
                    grand_total >
                    (
                        SELECT
                            COALESCE(SUM(
                                CASE
                                    WHEN module_type='purchase'
                                    THEN paid_amt
                                    ELSE 0
                                END
                            ),0)
                            +
                            COALESCE(SUM(
                                CASE
                                    WHEN module_type='vendor_payment'
                                    THEN paid_amt
                                    ELSE 0
                                END
                            ),0)
                            +
                            COALESCE(SUM(
                                CASE
                                    WHEN module_type='purchase_return'
                                    THEN return_amt
                                    ELSE 0
                                END
                            ),0)

                        FROM transactions
                        WHERE transactions.module_id = purchases.id
                    )
                ");
                });
            })
            ->orderBy('v_name')
            ->get();

        // Voucher No
        $lastVoucher = Transaction::where('module_type', 'vendor_payment')
            ->latest('id')
            ->first();

        if ($lastVoucher) {

            $number = (int) preg_replace('/[^0-9]/', '', $lastVoucher->reference_no);

            $voucherNo = 'VP-' . str_pad($number + 1, 5, '0', STR_PAD_LEFT);
        } else {

            $voucherNo = 'VP-00001';
        }

        return view(
            'vendor.vendor-payment-create',
            compact(
                'accounts',
                'vendors',
                'voucherNo'
            )
        );
    }

    public function getVendorPurchase($vendorId)
    {
        $purchases = Purchase::select(
            'purchases.id',
            'purchases.invoice_no',
            'purchases.grand_total'
        )

            ->selectRaw("
            COALESCE((
                SELECT SUM(paid_amt)
                FROM transactions
                WHERE module_type='purchase'
                AND module_id=purchases.id
            ),0) as purchase_paid
        ")

            ->selectRaw("
            COALESCE((
                SELECT SUM(paid_amount)
                FROM vendor_payment_details
                WHERE purchase_id=purchases.id
            ),0) as vendor_paid
        ")

            ->selectRaw("
            COALESCE((
                SELECT SUM(return_amt)
                FROM transactions
                WHERE module_type='purchase_return'
                AND module_id=purchases.id
            ),0) as return_amt
        ")

            ->where('vendor_id', $vendorId)
            ->orderBy('date')
            ->get();

        $result = [];

        foreach ($purchases as $purchase) {

            $alreadyPaid = $purchase->purchase_paid + $purchase->vendor_paid;

            $due = $purchase->grand_total
                - $alreadyPaid
                - $purchase->return_amt;

            if ($due > 0) {

                $result[] = [

                    'id'            => $purchase->id,
                    'invoice_no'    => $purchase->invoice_no,
                    'grand_total'   => round($purchase->grand_total, 2),
                    'already_paid'  => round($alreadyPaid, 2),
                    'return_amt'    => round($purchase->return_amt, 2),
                    'due_amt'       => round($due, 2),

                ];
            }
        }

        return response()->json($result);
    }

    public function getPurchaseInfo($id)
    {
        $purchase = Purchase::select(
            'id',
            'grand_total'
        )

            ->selectRaw("
            COALESCE((
                SELECT SUM(paid_amt)
                FROM transactions
                WHERE module_type='purchase'
                AND module_id=purchases.id
            ),0) as purchase_paid
        ")

        ->selectRaw("
            COALESCE((
                SELECT SUM(paid_amount)
                FROM vendor_payment_details
                WHERE purchase_id=purchases.id
            ),0) as vendor_paid
        ")

            ->selectRaw("
            COALESCE((
                SELECT SUM(return_amt)
                FROM transactions
                WHERE module_type='purchase_return'
                AND module_id=purchases.id
            ),0) as return_amt
        ")

            ->findOrFail($id);

        $alreadyPaid = $purchase->purchase_paid + $purchase->vendor_paid;

        $due = $purchase->grand_total
            - $alreadyPaid
            - $purchase->return_amt;

        return response()->json([

            'grand_total'  => round($purchase->grand_total, 2),
            'already_paid' => round($alreadyPaid, 2),
            'return_amt'   => round($purchase->return_amt, 2),
            'due_amt'      => round($due, 2),

        ]);
    }

    public function paymentStore(Request $request, JournalService $journalService)
    {
        $request->validate([
            'vendor_id'         => 'required|exists:vendors,id',
            'date'              => 'required|date',
            'voucher_no'        => 'required',
            'payment_method'    => 'required',
            'debit_account_id'  => 'required|exists:accounts,id',
            'credit_account_id' => 'required|exists:accounts,id',
            'purchase_id'       => 'required|array',
            'purchase_id.*'     => 'required|exists:purchases,id',
            'paid_amount'          => 'required|array',
            'paid_amount.*'        => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $companyId = auth()->user()->company_id;

            $totalPayment = collect($request->paid_amount)
                ->filter(fn($v) => $v > 0)
                ->sum();

            if ($totalPayment <= 0) {
                return back()
                    ->withErrors([
                        'paid_amount' => 'Please enter payment amount.'
                    ])
                    ->withInput();
            }

            $payment = VendorPayment::create([
                'company_id'        => $companyId,
                'vendor_id'         => $request->vendor_id,
                'date'              => $request->date,
                'voucher_no'        => $request->voucher_no,
                'reference'         => $request->reference,
                'remarks'         => $request->narration,
                'payment_method'    => $request->payment_method,
                'debit_account_id'  => $request->debit_account_id,
                'credit_account_id' => $request->credit_account_id,
                'paid_amount'     => $totalPayment,
                'created_by'        => auth()->id(),
            ]);

            foreach ($request->purchase_id as $key => $purchaseId) {
                $paid = (float)($request->paid_amount[$key] ?? 0);
                if ($paid <= 0) {
                    continue;
                }
                VendorPaymentDetails::create([
                    'company_id'        => $companyId,
                    'vendor_payment_id' => $payment->id,
                    'purchase_id'       => $purchaseId,
                    'paid_amount'          => $paid,
                ]);
            }
            Transaction::create([
                'company_id'      => $companyId,
                'module_type'     => 'vendor_payment',
                // ERP Standard
                'module_id'       => $payment->id,
                'reference_no'    => $payment->voucher_no,
                'vendor_id'       => $payment->vendor_id,
                'payment_method'  => $payment->payment_method,
                'paid_amt'        => $payment->paid_amount,
                'date'            => $payment->date,
            ]);

            $journalService->createJournal([
                'company_id'   => $companyId,
                'module_type'  => 'vendor_payment',
                'module_id'    => $payment->id,
                'reference_no' => $payment->voucher_no,
                'date'         => $payment->date,
                'particulars'  => 'Vendor Payment',
                'items'        => [
                    [
                        'account'   => JournalService::ACCOUNT_SUPPLIER_PAYABLE,
                        'debit'     => $totalPayment,
                        'credit'    => 0,
                        'vendor_id' => $payment->vendor_id,
                    ],
                    [
                        'account'   => $payment->credit_account_id,
                        'debit'     => 0,
                        'credit'    => $totalPayment,
                    ],
                ],
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Vendor Payment Saved Successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, VendorPayment $payment, JournalService $journalService)
    {
        $request->validate([

            'vendor_id'         => 'required|exists:vendors,id',
            'date'              => 'required|date',
            'payment_method'    => 'required',
            'debit_account_id'  => 'required|exists:accounts,id',
            'credit_account_id' => 'required|exists:accounts,id',
            'purchase_id'       => 'required|array',
            'purchase_id.*'     => 'exists:purchases,id',
            'paid_amt'          => 'required|array',
        ]);

        DB::beginTransaction();

        try {

            $companyId = auth()->user()->company_id;

            //----------------------------------
            // Total Payment
            //----------------------------------

            $totalPayment = collect($request->paid_amt)
                ->filter(fn($v) => $v > 0)
                ->sum();
            $payment->update([
                'vendor_id'         => $request->vendor_id,
                'date'              => $request->date,
                'reference'         => $request->reference,
                'narration'         => $request->narration,
                'payment_method'    => $request->payment_method,
                'debit_account_id'  => $request->debit_account_id,
                'credit_account_id' => $request->credit_account_id,
                'total_payment'     => $totalPayment,
                'pay_to'            => $request->pay_to,
            ]);

            VendorPaymentDetails::where(
                'vendor_payment_id',
                $payment->id
            )->delete();

            foreach ($request->purchase_id as $key => $purchaseId) {
                $paid = (float)($request->paid_amt[$key] ?? 0);
                if ($paid <= 0) {
                    continue;
                }
                VendorPaymentDetails::create([
                    'company_id' => $companyId,
                    'vendor_payment_id' => $payment->id,
                    'purchase_id' => $purchaseId,
                    'paid_amt' => $paid,
                ]);
            }

            Transaction::where(
                'module_type',
                'vendor_payment'
            )
                ->where('module_id', $payment->id)
                ->delete();

            Transaction::create([
                'company_id' => $companyId,
                'module_type' => 'vendor_payment',
                'module_id' => $payment->id,
                'reference_no' => $payment->voucher_no,
                'vendor_id' => $payment->vendor_id,
                'payment_method' => $payment->payment_method,
                'paid_amt' => $payment->total_payment,
                'date' => $payment->date,
            ]);

            $journalService->createJournal([
                'company_id' => $companyId,
                'module_type' => 'vendor_payment',
                'module_id' => $payment->id,
                'reference_no' => $payment->voucher_no,
                'date' => $payment->date,
                'particulars' => 'Vendor Payment',
                'items' => [
                    [
                        'account' => JournalService::ACCOUNT_SUPPLIER_PAYABLE,
                        'debit' => $payment->total_payment,
                        'credit' => 0,
                        'vendor_id' => $payment->vendor_id,
                    ],
                    [
                        'account' => $payment->credit_account_id,
                        'debit' => 0,
                        'credit' => $payment->total_payment,
                    ],
                ]
            ]);
            DB::commit();
            return redirect()
                ->route('vendor-payment.list')
                ->with(
                    'success',
                    'Vendor Payment Updated Successfully.'
                );
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }
}
