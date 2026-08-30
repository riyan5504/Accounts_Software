<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Return List</title>

    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 13px;
            color: #333;
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
            margin-bottom: 7px;
        }

        .company-section img {
            max-width: 80px;
            max-height: 55px;
            margin-bottom: 2px;
        }

        .company-title {
            font-size: 18px;
            font-weight: bold;
        }

        .company-info {
            font-size: 11px;
            margin-top: 2px;
        }

        /* Title */
        .report-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin: 10px 0;
            border-bottom: 1px solid #000;
            display: inline-block;
            padding-bottom: 2px;
        }

        .report-info {
            margin-top: 5px;
            font-size: 12px;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th {
            background: #f2f2f2;
            border: 1px solid #999;
            padding: 6px;
            font-size: 12px;
        }

        td {
            border: 1px solid #ccc;
            padding: 6px;
        }

        tbody tr:nth-child(even) {
            background: #fafafa;
        }

        /* Amount Column */
        .amount {
            text-align: right;
            font-weight: bold;
        }

        /* Footer */
        .footer {
            margin-top: 60px;
        }

        .signature td {
            border: none;
            padding-top: 40px;
            font-size: 12px;
        }
    </style>
</head>

<body>

    <!-- Company Info -->
    <div class="company-section">
        @if ($logoPath)
            <img src="{{ $logoPath }}" alt="{{ $company->name }}">
        @endif
        <div class="company-title">{{ $company->name ?? 'Company Name' }}</div>
        <div class="company-info">A Trusted Source of Aloe Vera & Herb Product</div>
        <div class="company-info">{{ $company->address ?? '' }}</div>
        <div class="company-info">
            @if ($company->phone)
                Mob: {{ $company->phone }}
            @endif
        </div>
        <div class="company-info">{{ $company->email ?? '' }}</div>
    </div>

    <!-- Title -->
    <div class="text-center">
        <div class="report-title">Return List</div>
    </div>

    <div class="report-info">
        <strong>Report Period:</strong>

        {{ $fromDate ? date('d-m-Y', strtotime($fromDate)) : 'Beginning' }}
        To
        {{ $toDate ? date('d-m-Y', strtotime($toDate)) : date('d-m-Y') }}
    </div>

    <!-- Table -->
    <table>
        <thead>
            <tr>
                <th style="width:5%">SL</th>
                <th style="width:20%">Vendor</th>
                <th style="width:12%">Date</th>
                <th style="width:15%">Invoice</th>
                <th>Item</th>
                <th style="width:12%">Total TK</th>
            </tr>
        </thead>

        <tbody>
            @php $total = 0; @endphp

            @foreach ($returns as $return)
                @php $total += $return->grand_total; @endphp

                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $return->vendor->v_name }}</td>
                    <td>{{ $return->date->format('d-m-Y') }}</td>
                    <td>{{ $return->invoice_no }}</td>

                    <td>
                        @foreach ($return->purchaseReturnItems as $item)
                            {{ $item->item->item_name }}{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    </td>

                    <td class="amount">{{ number_format($return->grand_total, 2) }}</td>
                </tr>
            @endforeach

            <!-- Grand Total -->
            <tr style="background:#e9ecef; font-weight:bold;">
                <td colspan="5" class="text-right">Grand Total</td>
                <td class="amount">{{ number_format($total, 2) }}</td>
            </tr>

        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <table width="100%" class="signature">
            <tr>
                <td class="text-left">
                    -------------------<br>
                    Checked By
                </td>
                <td class="text-right">
                    -------------------<br>
                    Authorized By
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
