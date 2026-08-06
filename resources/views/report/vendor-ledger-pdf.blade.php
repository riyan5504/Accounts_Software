<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Vendor Ledger</title>

    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 10px;
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

        /* Company Section */
        .company-section {
            text-align: center;
            margin-bottom: 5px;
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

        /* Title */
        .report-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin: 10px 0 5px;
            border-bottom: 1px solid #000;
            display: inline-block;
            padding-bottom: 2px;
        }

        .vendor-info {
            margin-top: 5px;
            font-size: 11px;
        }
        
        .report-info {
            margin-top: 5px;
            font-size: 11px;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th {
            background: #f2f2f2;
            border: 1px solid #999;
            padding: 5px;
            font-size: 10px;
        }

        td {
            border: 1px solid #ccc;
            padding: 5px;
        }

        tbody tr:nth-child(even) {
            background: #fafafa;
        }

        /* Highlight rows */
        .opening {
            font-weight: bold;
            background: #eef5ff;
        }

        .total {
            font-weight: bold;
            background: #e9ecef;
        }

        .closing {
            font-weight: bold;
            background: #dff0d8;
        }

        /* Footer */
        .footer {
            margin-top: 60px;
        }

        .signature td {
            border: none;
            padding-top: 40px;
            font-size: 11px;
        }
    </style>
</head>

<body>

    <!-- Company Info -->
    <div class="company-section">
        <img src="{{ public_path('backend/dist/assets/img/logo02.png') }}">
        <div class="company-title">{{ $companyName ?? 'Company Name' }}</div>
        <div class="company-info">A Trusted Source of Aloe Vera & Herb Product</div>
        <div class="company-info">Mob: 01721336504</div>
        <div class="company-info">Flat-3/A, House-53, Road-14</div>
        <div class="company-info">Sector-13, Uttara, Dhaka-1230</div>
    </div>

    <!-- Title -->
    <div class="text-center">
        <div class="report-title">Vendor Ledger</div>
    </div>

    <!-- Vendor Info -->
    <div class="vendor-info">
        <strong>Vendor Name:</strong> {{ $vendor->v_name }}
    </div>
    <div class="report-info">
        <strong>Report Period:</strong>

        {{ $from ? date('d-m-Y', strtotime($from)) : 'Beginning' }}
        To
        {{ $to ? date('d-m-Y', strtotime($to)) : date('d-m-Y') }}
    </div>

    <!-- Ledger Table -->
    <table>
        <thead>
            <tr>
                <th>Sl</th>
                <th>Date</th>
                <th>Particular</th>
                <th>VCH Type</th>
                <th>VCH No.</th>
                <th>Debit</th>
                <th>Credit</th>
                <th>Balance</th>
            </tr>
        </thead>

        <tbody>

            <!-- Opening -->
            <tr class="opening">
                <td>1</td>
                <td colspan="6">Opening Balance</td>
                <td class="text-right">{{ number_format($opening, 2) }}</td>
            </tr>

            @php $sl = 2; @endphp

            @foreach ($ledger as $row)
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ \Carbon\Carbon::parse($row['date'])->format('d-m-Y') }}</td>
                    <td>{{ $row['particular'] }}</td>
                    <td>{{ $row['type'] }}</td>
                    <td>{{ $row['vch_no'] }}</td>
                    <td class="text-right">{{ number_format($row['debit'], 2) }}</td>
                    <td class="text-right">{{ number_format($row['credit'], 2) }}</td>
                    <td class="text-right">{{ number_format($row['balance'], 2) }}</td>
                </tr>
            @endforeach

            <!-- Total -->
            <tr class="total">
                <td colspan="5" class="text-right">Grand Total</td>
                <td class="text-right">{{ number_format($totalDebit, 2) }}</td>
                <td class="text-right">{{ number_format($totalCredit, 2) }}</td>
                <td></td>
            </tr>

            <!-- Closing -->
            <tr class="closing">
                <td colspan="7" class="text-right">Closing Balance</td>
                <td class="text-right">{{ number_format($closing, 2) }}</td>
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