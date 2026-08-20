<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Purchase - {{ $purchase->invoice_no }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #222;
            margin: 0;
            padding: 0;
        }

        @page {
            margin: 15px;
            margin-bottom: 85px;
        }

        /* COMMON */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            font-size: 11px;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .fw-bold {
            font-weight: bold;
        }

        /* COMPANY HEADER */
        .company-section {
            text-align: center;
            margin-bottom: 5px;
        }

        .company-logo {
            width: 55px;
            height: auto;
            margin-bottom: 3px;
        }

        .company-title {
            font-size: 16px;
            font-weight: bold;
        }

        .company-info {
            font-size: 10.5px;
            line-height: 13px;
            margin-top: 2px;
        }

        /* WATERMARK */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            width: 400px;
            transform: translate(-50%, -50%);
            opacity: 0.08;
            z-index: -1;
        }

        .watermark img {
            width: 100%;
            height: auto;
        }

        /* INVOICE TITLE */
        .invoice-title-wrapper {
            text-align: center;
            margin: 8px 0 10px 0;
        }

        .invoice-title {
            display: inline-block;
            font-size: 14px;
            font-weight: bold;
            border-bottom: 1px solid #222;
            padding-bottom: 2px;
        }

        /* vendor / INVOICE INFO */
        .info-table {
            width: 100%;
            margin-bottom: 10px;
            table-layout: fixed;
        }

        .info-table>tbody>tr>td {
            border: none;
            vertical-align: top;
            padding: 0;
        }

        .vendor-info {
            width: 55%;
            padding-right: 10px !important;
        }

        .invoice-info {
            width: 45%;
            padding-left: 10px !important;
        }

        .info-inner-table {
            width: 100%;
        }

        .info-inner-table td {
            border: none;
            padding: 2px 3px;
            vertical-align: top;
        }

        .info-label {
            width: 90px;
            font-weight: bold;
            white-space: nowrap;
        }

        /* Purchase ITEMS TABLE */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            table-layout: fixed;
        }

        .items-table th {
            background: #f2f2f2;
            border: 1px solid #999;
            padding: 5px 4px;
            font-size: 10.5px;
            font-weight: bold;
        }

        .items-table td {
            border: 1px solid #ccc;
            padding: 4px;
            font-size: 10.5px;
            vertical-align: middle;
        }

        .items-table tbody tr {
            page-break-inside: avoid;
        }

        /* BOTTOM SECTION LEFT  = vendor PAYMENT RIGHT = SUMMARY */
        .bottom-layout {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            margin: 4px 0 0 0;
            padding: 0;
        }

        .bottom-layout>tbody>tr>td {
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
            vertical-align: top;
        }

        /* LEFT COLUMN - vendor PAYMENT */
        .payment-column {
            width: 48%;
            padding: 0 10px 0 0 !important;
            margin: 0 !important;
            vertical-align: top;
        }

        /* RIGHT COLUMN - SUMMARY IMPORTANT: */
        .summary-column {
            width: 48%;
            padding: 0 !important;
            margin: 0 !important;
            vertical-align: top;
        }

        /* SUMMARY BOX */
        .summary-box {
            width: 100%;
            margin: 0 !important;
            padding: 6px;
            border: 1px solid #999;
            box-sizing: border-box;
        }

        /* SUMMARY TABLE */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            padding: 0;
        }

        .summary-table td,
        .summary-table th {
            border: none;
            padding: 3px 2px;
            font-size: 10.5px;
            background: transparent;
        }

        .summary-label {
            width: 65%;
            text-align: left;
        }

        .summary-value {
            width: 35%;
            text-align: right;
            white-space: nowrap;
        }

        /* SUMMARY SEPARATORS */
        .summary-border-top td,
        .summary-border-top th {
            border-top: 1px solid #777;
            padding-top: 5px;
        }

        /* GRAND TOTAL */
        .grand-total-row td,
        .grand-total-row th {
            font-weight: bold;
            font-size: 11.5px;
        }

        /* RETURN / PAYMENT / DUE / CREDIT */
        .return-row td {
            color: #dc3545;
        }

        .payment-row td {
            color: #198754;
        }

        .due-row td,
        .credit-row td {
            font-weight: bold;
        }

        .due-danger {
            color: #dc3545;
        }

        .due-success {
            color: #198754;
        }

        .credit-danger {
            color: #dc3545;
        }


        /* PAYMENT STATUS */
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 2px;
        }

        .status-paid {
            background: #198754;
            color: #fff;
        }

        .status-partial {
            background: #ffc107;
            color: #222;
        }

        .status-credit {
            background: #dc3545;
            color: #fff;
        }

        .status-unpaid {
            background: #dc3545;
            color: #fff;
        }

        /* FOOTER */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 65px;
            text-align: center;
        }

        .signature-table {
            width: 100%;
        }

        .signature-table td {
            border: none;
            padding-top: 20px;
            font-size: 10.5px;
        }

        .footer hr {
            border: 0;
            border-top: 1px solid #aaa;
            margin: 3px 0;
        }

        .note {
            font-size: 9.5px;
            color: #666;
            text-align: center;
        }

        /* PAGE BREAK */
        tr {
            page-break-inside: avoid;
        }

        .history-section {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>

    {{-- ======= COMPANY HEADER ====== --}}
    <div class="company-section">
        <img class="company-logo" src="{{ public_path('backend/dist/assets/img/logo02.png') }}" alt="Company Logo">
        <div class="company-title">
            {{ $companyName ?? 'Company Name' }}
        </div>
        <div class="company-info">
            Lakshmipur Kholabaria, Natore Sadar,<br>
            Natore-6400, Bangladesh
            <br>
            Email: ponnoobd@gmail.com
            <br>
            Hotline: 01721336504
        </div>
    </div>

    {{-- ===== WATERMARK ======= --}}
    <div class="watermark">
        <img src="{{ public_path('backend/dist/assets/img/logo02.png') }}" alt="Company Logo">
    </div>

    {{-- ===== INVOICE TITLE==== --}}
    <div class="invoice-title-wrapper">
        <span class="invoice-title">PURCHASE INVOICE</span>
    </div>

    {{-- ======= vendor + INVOICE INFORMATION ========= --}}
    <table class="info-table">
        <tr>
            {{-- vendor INFORMATION --}}
            <td class="vendor-info">
                <table class="info-inner-table">
                    <tr>
                        <td class="info-label">Vendor Name:</td>
                        <td>{{ $purchase->vendor->v_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Address:</td>
                        <td style="line-height: 14px;">{{ $purchase->vendor->address ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Phone:</td>
                        <td>{{ $purchase->vendor->phone ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Email:</td>
                        <td>{{ $purchase->vendor->email ?? 'N/A' }}</td>
                    </tr>
                </table>
            </td>

            {{-- INVOICE INFORMATION --}}
            <td class="invoice-info">
                <table class="info-inner-table">
                    <tr>
                        <td class="text-right">
                            <strong>Invoice No:</strong>
                            {{ $purchase->invoice_no }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-right">
                            <strong>Date:</strong>
                            {{ $purchase->date ? $purchase->date->format('d M Y') : 'N/A' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-right">
                            <strong>purchase By:</strong>
                            {{ optional($purchase->user)->name ?? 'Admin' }}
                        </td>
                    </tr>
                    @if ($purchase->reference)
                        <tr>
                            <td class="text-right">
                                <strong>Reference:</strong>
                                {{ $purchase->reference }}
                            </td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    {{-- ==== purchase ITEMS ======= --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:5px;">SL</th>
                <th style="width:23%;">Item Name</th>
                <th style="width:12%;">Pack Size</th>
                <th style="width:12%;">Purchase Qty</th>
                <th style="width:12%;">Unit Price</th>
                <th style="width:12%;">Amount</th>
                <th style="width:10%;">VAT</th>
                <th style="width:14%;" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($purchase->purchaseItems as $singleItm)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $singleItm->item->item_name ?? 'N/A' }}</td>
                    <td class="text-center">{{ $singleItm->item->size ?? '' }}</td>
                    <td class="text-center">
                        {{ number_format((float) $singleItm->qty, 2) }}
                        {{ $singleItm->item->stock_unit ?? '' }}
                    </td>
                    <td class="text-right">{{ number_format((float) $singleItm->purchase_price, 2) }}</td>
                    <td class="text-right">{{ number_format((float) $singleItm->price, 2) }}</td>
                    <td class="text-right">{{ number_format((float) $singleItm->item_vat_amt, 2) }}</td>
                    <td class="text-right fw-bold">{{ number_format((float) $singleItm->total_price, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="color:#777;">
                        No purchase items found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- =====PAYMENT + SUMMARY ===== --}}
    <table class="bottom-layout">
        <tr>
            <td class="payment-column">
                @if ($hasVendorPayment)
                    <div class="payment-section-title">
                        Later Vendor Payments
                    </div>
                    <table class="payment-history-table">
                        <thead>
                            <tr>
                                <th style="width:35px;">SL</th>
                                <th>Voucher</th>
                                <th>Date</th>
                                <th>Method</th>
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($vendorPaymentHistory as $detail)
                                @php
                                    $vp = $detail->vendorPayment;
                                    $allocatedAmount = (float) $detail->paid_amount;
                                @endphp
                                @if ($allocatedAmount > 0)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $vp->voucher_no ?? 'N/A' }}</td>
                                        <td>
                                            @if ($vp && $vp->date)
                                                {{ \Carbon\Carbon::parse($vp->date)->format('d-m-Y') }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $vp->payment_method ?? 'N/A')) }}</td>
                                        <td class="text-right payment-amount">
                                            {{ number_format($allocatedAmount, 2) }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="payment-total-row">
                                <th colspan="4" class="text-right">
                                    Vendor Payment Total
                                </th>
                                <th class="text-right payment-amount">
                                    {{ number_format($vendorPayment, 2) }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                @endif
            </td>

            <td></td>

            {{-- ==== RIGHT COLUMN SUMMARY==== --}}
            <td class="summary-column">
                <table class="summary-table">
                    <tr>
                        <td class="summary-label">Quantity Total:</td>
                        <td class="summary-value">
                            {{ number_format($purchase->purchaseItems->sum('qty'), 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="summary-label">Sub Total:</td>
                        <td class="summary-value">
                            {{ number_format((float) $purchase->sub_total, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="summary-label">VAT Amount:</td>
                        <td class="summary-value">
                            {{ number_format((float) $purchase->vat_amt, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="summary-label">
                            @if (!empty($purchase->dis_percent))
                                Discount
                                ({{ $purchase->dis_percent }}%):
                            @else
                                Discount:
                            @endif
                        </td>
                        <td class="summary-value">
                            {{ number_format((float) $purchase->dis_amt, 2) }}
                        </td>
                    </tr>
                    <tr class="summary-border-top grand-total-row">
                        <th class="summary-label">Original Grand Total:</th>
                        <th class="summary-value">
                            {{ number_format($originalPurchase, 2) }}
                        </th>
                    </tr>
                    @if ($hasReturns)
                        <tr class="return-row">
                            <td class="summary-label">Purchase Return:</td>
                            <td class="summary-value">
                                @if ($totalReturn > 0)
                                    -{{ number_format($totalReturn, 2) }}
                                @else
                                    0.00
                                @endif
                            </td>
                        </tr>
                    @endif
                    @if ($hasInitialPayment)
                        <tr class="payment-row">
                            <td class="summary-label">Initial Payment:</td>
                            <td class="summary-value">
                                {{ number_format($initialPayment, 2) }}
                            </td>
                        </tr>
                    @endif
                    @if ($hasVendorPayment)
                        <tr class="payment-row">
                            <td class="summary-label">Vendor Payment:</td>
                            <td class="summary-value">
                                {{ number_format($vendorPayment, 2) }}
                            </td>
                        </tr>
                    @endif
                    @if ($hasInitialPayment || $hasVendorPayment)
                        <tr class="summary-border-top">
                            <th class="summary-label">Total Paid:</th>
                            <th class="summary-value">
                                {{ number_format($totalPaid, 2) }}
                            </th>
                        </tr>
                    @endif
                    @if ($hasReturns || $hasInitialPayment || $hasVendorPayment)
                        <tr class="summary-border-top due-row">
                            <th
                                class="summary-label
                                    {{ $due > 0 ? 'due-danger' : 'due-success' }}">
                                Remaining Due:
                            </th>
                            <th
                                class="summary-value
                                    {{ $due > 0 ? 'due-danger' : 'due-success' }}">
                                {{ number_format($due, 2) }}
                            </th>
                        </tr>
                    @endif
                    @if ($supplierCredit > 0)
                        <tr class="credit-row">
                            <th class="summary-label credit-danger">
                                Vendor Credit:
                            </th>
                            <th class="summary-value credit-danger">
                                -{{ number_format($supplierCredit, 2) }}
                            </th>
                        </tr>
                    @endif
                    <tr class="summary-border-top">
                        <th class="summary-label">Payment Status:</th>
                        <th class="summary-value">
                            @if ($paymentStatus === 'Paid')
                                <span class="status-badge status-paid">PAID</span>
                            @elseif ($paymentStatus === 'Partial')
                                <span class="status-badge status-partial">PARTIAL</span>
                            @elseif ($paymentStatus === 'Credit')
                                <span class="status-badge status-credit">CREDIT</span>
                            @else
                                <span class="status-badge status-unpaid">UNPAID</span>
                            @endif
                        </th>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- In Words -->
    <p>
        <strong>In Words:</strong>
        @if ($due > 0)
            Taka {{ ucwords(\App\Helpers\NumberHelper::numberToWords($due)) }} Only
        @else
           Taka {{ ucwords(\App\Helpers\NumberHelper::numberToWords($originalPurchase)) }} Only 
        @endif        
    </p>


    {{-- ==== FOOTER ======= --}}
    <div class="footer">
        <table class="signature-table">
            <tr>
                <td class="text-left"> ----------------------------<br>
                    Vendor Signature
                </td>


                <td class="text-right"> ----------------------------<br>
                    Authorized Signature
                </td>
            </tr>
        </table>
        <hr>
        <div class="note">
            Thank you for your business!
            &nbsp;|&nbsp;
            Call us: 01721336504
        </div>
    </div>
</body>

</html>
