```php
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Item Ledger Report</title>

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
            background: #b3deef;
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

    @php
        $runningBalance = $openingBalance;
        $unitPrice = $item->unit_price ?? 0;

        $sl = 1;

        $typeNames = [
            'opening' => 'Opening Stock',
            'purchase' => 'Purchase',
            'purchase_return' => 'Purchase Return',
            'sale' => 'Sale',
            'sales' => 'Sale',
            'sale_return' => 'Sale Return',
            'production_in' => 'Production',
            'consume' => 'Consumption',
            'damage' => 'Damage',
        ];
    @endphp

    <!-- Header -->
    <div class="header">

        <!-- Company Info -->
        <div class="company-section">
            <img src="{{ public_path('backend/dist/assets/img/logo02.png') }}">
            <div class="company-title">{{ $companyName ?? 'Company Name' }}</div>
            <div class="company-info">A Trusted Source of Aloe Vera & Herb Product</div>
            <div class="company-info">Mob: 01721336504</div>
            <div class="company-info">Flat-3/A, House-53, Road-14</div>
            <div class="company-info">Sector-13, Uttara, Dhaka-1230</div>
        </div>
        <div class="text-center">
            <div class="report-title">ITEM LEDGER REPORT</div>
        </div>

        <div class="report-info">
            <strong>Item Name:</strong>
            {{ $item->item_name ?? '' }}
            <br>

            <strong>Period :</strong>

            {{ $fromDate ? date('d-m-Y', strtotime($fromDate)) : 'Beginning' }}
            To
            {{ $toDate ? date('d-m-Y', strtotime($toDate)) : date('d-m-Y') }}
        </div>
    </div>

    <!-- Ledger Table -->
    <table>
        <thead>
            <tr>
                <th width="8%">SL</th>
                <th width="15%">Date</th>
                <th width="30%">Particulars</th>
                <th width="15%">Qty In</th>
                <th width="15%">Qty Out</th>
                <th width="17%">Balance</th>
            </tr>
        </thead>

        <tbody>

            <!-- Opening Balance -->
            <tr class="opening">
                <td class="text-center">{{ $sl++ }}</td>

                <td>
                    {{ $fromDate ? date('d-m-Y', strtotime($fromDate)) : '' }}
                </td>

                <td>Opening Balance</td>

                <td></td>
                <td></td>

                <td class="text-right">
                    {{ number_format($openingBalance, 3) }}
                </td>
            </tr>

            @forelse($ledgers as $row)
                @php
                    $runningBalance += $row->qty_in;
                    $runningBalance -= $row->qty_out;
                @endphp

                <tr>

                    <td class="text-center">
                        {{ $sl++ }}
                    </td>

                    <td>
                        {{ date('d-m-Y', strtotime($row->date)) }}
                    </td>

                    <td>
                        {{ $typeNames[$row->module_type] ?? ucwords(str_replace('_', ' ', $row->module_type)) }}
                    </td>

                    <td class="text-right">
                        {{ $row->qty_in > 0 ? number_format($row->qty_in, 2) . ' ' . $item->stock_unit : '-' }}
                    </td>

                    <td class="text-right">
                        {{ $row->qty_out > 0 ? number_format($row->qty_out, 2) . ' ' . $item->stock_unit : '-' }}
                    </td>

                    <td class="text-right">
                        {{ number_format($runningBalance, 2) . ' ' . $item->stock_unit }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="6" class="text-center">
                        No Data Found
                    </td>
                </tr>
            @endforelse

            <!-- Closing Balance -->
            <tr class="closing">
                <td colspan="5" class="text-left">
                    Current Stock
                </td>

                <td class="text-left">
                    {{ number_format($runningBalance, 2) . ' ' . $item->stock_unit }}
                </td>
            </tr>

            <tr style="background:#def4cf;font-weight:bold;">
                <td colspan="5" class="text-left">
                    Stock Value
                    ({{ number_format($unitPrice, 2) }} ×
                    {{ number_format($runningBalance, 2) . ' ' . $item->stock_unit }})
                </td>

                <td class="text-left">
                    {{ number_format($runningBalance * $unitPrice, 2) . ' ' . 'TK' }}
                </td>
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
```
