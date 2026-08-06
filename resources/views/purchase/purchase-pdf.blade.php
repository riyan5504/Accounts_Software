{{-- resources/views/purchase/purchase-pdf.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $purchase->invoice_no }}</title>
    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 12px;
            color: #333;
            position: relative;
        }

        .watermark img {
            width: 100%;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .company-section {
            text-align: center;
            margin-bottom: 5px;
            position: relative;
        }

        .company-section img {
            width: 55px;
            margin-bottom: 3px;
        }

        .company-title {
            font-size: 16px;
            font-weight: bold;
        }

        .company-info {
            font-size: 9px;
            margin-top: 1px;
        }

        .invoice-title {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            margin: 10px 0;
            border-bottom: 1px solid #000;
            display: inline-block;
            padding-bottom: 2px;
        }

        .info-table td {
            border: none;
            padding: 3px;
            vertical-align: top;
        }

        .info-table .label {
            width: 85px;
            white-space: nowrap;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f2f2f2;
            border: 1px solid #999;
            padding: 6px;
            font-size: 10px;
        }

        td {
            border: 1px solid #ccc;
            padding: 5px;
            font-size: 10px;
        }

        tbody tr:nth-child(even) {
            background: #fafafa;
        }

        .summary-table td {
            border: none;
            padding: 3px 5px;
            background: transparent;
        }

        .summary-box {
            border: 1px solid #999;
            padding: 8px;
            margin-top: 3px;
            background: transparent;
        }

        .grand-total {
            font-weight: bold;
            font-size: 13px;
            background: transparent;
        }

        .grand-total td {
            border-color: #999;
            border-top: 1px solid #393838;
            background: transparent;
        }

        .info-inner-table td {
            border: none;
            padding: 2px 5px;
            vertical-align: top;
        }

        @page {
            margin: 15px;
            margin-bottom: 100px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 80px;
            text-align: center;
        }

        .signature td {
            border: none;
            padding-top: 40px;
            font-size: 11px;
        }

        .note {
            margin-top: 10px;
            font-size: 10px;
            text-align: center;
            color: #666;
        }
    </style>
</head>

<body>
    <!-- Company -->
    <div class="company-section">
        <img src="{{ public_path('backend/dist/assets/img/logo02.png') }}">
        <div class="company-title">{{ $companyName ?? 'Veshoz Village Private Limited' }}</div>
        <div class="company-info">A Trusted Source of Aloe Vera & Herb Product</div>
        <div class="company-info">Flat-3/A, House-53, Road-14, Sector-13, Uttara, Dhaka-1230</div>
        <div class="company-info">Mob: 01721336504</div>
    </div>

    <!-- Title -->
    <div class="text-center">
        <div class="invoice-title">INVOICE</div>
    </div>

    <!-- Info -->
    <table class="info-table">
        <tr>
            <td class="text-left" style="width: 55%;">
                <table class="info-inner-table">
                    <tr>
                        <td class="label"><strong>Customer Name:</strong></td>
                        <td>{{ $purchase->vendor->v_name }}</td>
                    </tr>
                    <tr>
                        <td class="label"><strong>Address:</strong></td>
                        <td style="line-height: 14px;">{{ $purchase->vendor->address }}</td>
                    </tr>
                    <tr>
                        <td class="label"><strong>Phone:</strong></td>
                        <td>{{ $purchase->vendor->phone }}</td>
                    </tr>
                    <tr>
                        <td class="label"><strong>Email:</strong></td>
                        <td>{{ $purchase->vendor->email ?: 'N/A' }}</td>
                    </tr>
                </table>
            </td>
            <td class="text-right" style="width: 45%;">
                <table class="info-inner-table">
                    <tr>
                        <td style="text-align: right;">
                            <strong>Invoice No:</strong> {{ $purchase->invoice_no }}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">
                            <strong>Date:</strong> {{ $purchase->date->format('d M Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">
                            <strong>Time:</strong> {{ now()->format('h:i A') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Items Table -->
    <table style="margin-top: 10px;">
        <thead>
            <tr class="text-center">
                <th style="width: 5%;">Sl</th>
                <th style="width: 25%;">Item Name</th>
                <th style="width: 10%;">Pack</th>
                <th style="width: 10%;">Qty</th>
                <th style="width: 12%;">Unit Price</th>
                <th style="width: 12%;">Amount</th>
                <th style="width: 12%;">Vat</th>
                <th style="width: 14%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchase->purchaseItems as $singleItm)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $singleItm->item->item_name }}</td>
                    <td class="text-center">{{ $singleItm->item->size }}</td>
                    <td class="text-center">{{ $singleItm->qty }} {{ $singleItm->item->stock_unit }}</td>
                    <td class="text-right">{{ number_format($singleItm->unit_price, 2) }}</td>
                    <td class="text-right">{{ number_format($singleItm->price, 2) }}</td>
                    <td class="text-right">{{ number_format($singleItm->vat_amount, 2) }}</td>
                    <td class="text-right">{{ number_format($singleItm->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary -->
    <table style="margin-left: 5px;">
        <tr>
            <td style="width: 55%; border: none;"></td>
            <td style="width: 45%; border: none;">
                <div class="summary-box">
                    <table class="summary-table">
                        <tr>
                            <td>Sub Total</td>
                            <td class="text-right">{{ number_format($purchase->sub_total, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Vat Amount</td>
                            <td class="text-right">{{ number_format($purchase->vat_amt, 2) }}</td>
                        </tr>
                        <tr>
                            <td>
                                @if (!empty($purchase->dis_percent))
                                    Discount ({{ $purchase->dis_percent }}%)
                                @else
                                    Discount
                                @endif
                            </td>
                            <td class="text-right">{{ number_format($purchase->dis_amt, 2) }}</td>
                        </tr>
                        <tr class="grand-total">
                            <td>Grand Total</td>
                            <td class="text-right">{{ number_format($purchase->grand_total, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- Payment Status -->
    {{-- <p>
        <strong>Payment Status:</strong>
        @if ($purchase->due_amt == 0)
            <span style="color: green;">Paid</span>
        @elseif($purchase->due_amt < $purchase->grand_total)
            <span style="color: orange;">Partial Paid</span>
        @else
            <span style="color: red;">Due</span>
        @endif
    </p> --}}

    <!-- In Words -->
    <p>
        <strong>In Words:</strong>
        Taka {{ ucwords(\App\Helpers\NumberHelper::numberToWords($purchase->grand_total)) }} Only
    </p>

    <!-- Footer -->
    <div class="footer">
        <table width="100%" class="signature">
            <tr>
                <td class="text-left">
                    ----------------------------<br>
                    Customer Signature
                </td>
                {{-- <td class="text-center">
                    The sales product will be returnable within 15 days
                </td> --}}
                <td class="text-right">
                    ----------------------------<br>
                    Seller Signature
                </td>
            </tr>
        </table>
        <hr>
        <div class="note">
            Thank you for your business! | Call us: 01721336504
        </div>
    </div>

</body>

</html>
