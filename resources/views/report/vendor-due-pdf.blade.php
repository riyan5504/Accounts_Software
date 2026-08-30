<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-size: 13px;
            font-family: Arial, sans-serif;
            color: #333;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* Company Header */
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

        /* Report Title */
        .title {
            text-align: center;
            font-size: 16px;
            margin: 15px 0;
            font-weight: bold;
            border-bottom: 2px solid #000;
            display: inline-block;
            padding-bottom: 3px;
        }

        /* Table Style */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th, table td {
            border: 1px solid #ccc;
            padding: 6px;
        }

        th {
            background: #f0f0f0;
            font-weight: bold;
        }

        tbody tr:nth-child(even) {
            background: #fafafa;
        }

        /* Grand Total */
        .grand-total {
            background: #e9ecef;
            font-weight: bold;
        }

        /* Footer */
        .footer {
            margin-top: 60px;
        }

        .footer td {
            border: none;
            font-size: 12px;
        }

        .signature {
            margin-top: 40px;
        }

    </style>
</head>
<body>

    <!-- Company Info Centered -->
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
        <div class="title">Vendor Due Balance Report</div>
    </div>
    <div>
        <p>Generated on: {{ $filterDate }}</p>
        @if($type == 'supplier' && request('vendor_id'))
            <p>Filter: By Vendor</p>
        @else
            <p>Filter: All vendors</p>
        @endif
    </div>

    <!-- Table -->
    <table>
        <thead>
            <tr>
                <th>Sl</th>
                <th>Vendor</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Opening</th>
                <th>Bill</th>
                <th>Payment</th>
                <th>Return</th>
                <th>Balance</th>
            </tr>
        </thead>

        <tbody>
            @php
                $totalOpening = 0;
                $totalBill = 0;
                $totalPayment = 0;
                $totalReturn = 0;
                $totalBalance = 0;
            @endphp

            @foreach ($reportData as $key => $data)

                @php
                    $totalOpening += $data['opening'];
                    $totalBill += $data['bill'];
                    $totalPayment += $data['payment'];
                    $totalReturn += $data['return'];
                    $totalBalance += $data['balance'];
                @endphp

                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $data['vendor']->v_name }}</td>
                    <td>{{ $data['vendor']->phone ?? '-' }}</td>
                    <td>{{ $data['vendor']->address ?? '-' }}</td>

                    <td class="text-right">{{ number_format($data['opening'], 2) }}</td>
                    <td class="text-right">{{ number_format($data['bill'], 2) }}</td>
                    <td class="text-right">{{ number_format($data['payment'], 2) }}</td>
                    <td class="text-right">{{ number_format($data['return'], 2) }}</td>
                    <td class="text-right">{{ number_format($data['balance'], 2) }}</td>
                </tr>

            @endforeach

            <!-- Grand Total -->
            <tr class="grand-total">
                <td colspan="4" class="text-right">Grand Total</td>
                <td class="text-right">{{ number_format($totalOpening, 2) }}</td>
                <td class="text-right">{{ number_format($totalBill, 2) }}</td>
                <td class="text-right">{{ number_format($totalPayment, 2) }}</td>
                <td class="text-right">{{ number_format($totalReturn, 2) }}</td>
                <td class="text-right">{{ number_format($totalBalance, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Footer (No Box, Clean) -->
    <div class="footer">
        <table width="100%">
            <tr>
                <td class="text-left signature">
                    -------------------<br>
                    Checked By
                </td>
                <td class="text-right signature">
                    -------------------<br>
                    Authorized By
                </td>
            </tr>
        </table>
    </div>

</body>
</html>