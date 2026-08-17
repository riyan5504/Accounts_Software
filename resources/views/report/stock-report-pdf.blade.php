<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Stock Report</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 13px;
            margin: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h2 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
        }

        .header p {
            margin: 5px 0;
            font-size: 11px;
            color: #666;
        }

        /* Company Header */
        .company-section {
            text-align: center;
            margin-bottom: 10px;
        }

        .company-section img {
            height: 60px;
            margin-bottom: 5px;
        }

        .company-title {
            font-size: 16px;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: #f5bfbf;
            padding: 6px 4px;
            border: 1px solid #999;
            font-size: 11px;
            text-align: center;
            font-weight: bold;
        }

        td {
            padding: 4px;
            border: 1px solid #ccc;
            font-size: 11px;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }

        .text-end {
            text-align: right;
        }

        .text-start {
            text-align: left;
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

        /* .footer {
            margin-top: 20px;
            font-size: 9px;
            text-align: right;
            color: #666;
        } */
        /* Footer */
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

        .total-row {
            font-weight: bold;
            background-color: #e8e8e8;
        }

        @page {
            margin: 10px;
        }
    </style>
</head>

<body onload="window.print()">
    <div class="header">
        <div class="company-section">
            <img src="{{ public_path('backend/dist/assets/img/logo02.png') }}">
            <div class="company-title">{{ $companyName ?? 'Company Name' }}</div>
            <div class="company-info">A Trusted Source of Aloe Vera & Herb Product</div>
            <div class="company-info">Mob: 01721336504</div>
            <div class="company-info">Flat-3/A, House-53, Road-14</div>
            <div class="company-info">Sector-13, Uttara, Dhaka-1230</div>
        </div>
        <div>
            <p>Stock Report:- Generated on: {{ $filterDate }}</p>
            @if ($type == 'item' && request('item_id'))
                <p>Filter: By Item</p>
            @else
                <p>Filter: All Items</p>
            @endif
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 3%;">Sl.</th>
                    <th style="width: 7%;">Code</th>
                    <th style="width: 11%;">Item Name</th>
                    <th style="width: 10%;">Category</th>
                    <th style="width: 8%;">Opening</th>
                    <th style="width: 8%;">Purchase</th>
                    <th style="width: 8%;">Production</th>
                    <th style="width: 7%;">Sales</th>
                    <th style="width: 7%;">Consume</th>
                    <th style="width: 7%;">Pur. Return</th>
                    <th style="width: 7%;">Sales Return</th>
                    <th style="width: 7%;">Current</th>
                    <th style="width: 10%;">Stock Value</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalValue = 0;
                @endphp

                @foreach ($stocks as $key => $item)
                    @php
                        $opening = $item->opening_stock_sum ?? 0;
                        $purchase = $item->purchase_stock_sum ?? 0;
                        $production = $item->production_in_sum ?? 0;
                        $sales = $item->sales_stock_sum ?? 0;
                        $consume = $item->consume_sum ?? 0;
                        $preturn = $item->purchase_return_sum ?? 0;
                        $sreturn = $item->sales_return_sum ?? 0;

                        $currentStock =
                            $opening + $purchase + $production + $sreturn - ($sales + $consume + $preturn);

                        // Stock valuation price
                        if (($item->avg_purchase_price ?? 0) > 0) {
                            // 1. Average Purchase Price
                            $stockPrice = $item->avg_purchase_price;
                        } elseif (($item->last_purchase_price ?? 0) > 0) {
                            // 2. Last Purchase Price
                            $stockPrice = $item->last_purchase_price;
                        } else {
                            // 3. Item Unit Price
                            $stockPrice = $item->unit_price ?? 0;
                        }
                        $stockValue = $currentStock * $stockPrice;
                        $totalValue += $stockValue;
                    @endphp

                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $item->item_code }}</td>
                        <td class="text-start">{{ $item->item_name }}</td>
                        <td class="text-start">{{ $item->category->cat_name ?? '' }}</td>
                        <td class="text-end">
                            {{ $opening > 0 ? number_format($opening, 2) . ' ' . $item->stock_unit : '-' }}
                        </td>
                        <td class="text-end">
                            {{ $purchase > 0 ? number_format($purchase, 2) . ' ' . $item->stock_unit : '-' }}
                        </td>
                        <td class="text-end">
                            {{ $production > 0 ? number_format($production, 2) . ' ' . $item->stock_unit : '-' }}
                        </td>
                        <td class="text-end">
                            {{ $sales > 0 ? number_format($sales, 2) . ' ' . $item->stock_unit : '-' }}
                        </td>
                        <td class="text-end">
                            {{ $consume > 0 ? number_format($consume, 2) . ' ' . $item->stock_unit : '-' }}
                        </td>
                        <td class="text-end">
                            {{ $preturn > 0 ? number_format($preturn, 2) . ' ' . $item->stock_unit : '-' }}
                        </td>
                        <td class="text-end">
                            {{ $sreturn > 0 ? number_format($sreturn, 2) . ' ' . $item->stock_unit : '-' }}
                        </td>
                        <td class="text-end" style="font-weight: bold;">{{ number_format($currentStock, 2) }}
                            {{ $item->stock_unit }}
                        </td>
                        <td class="text-end">{{ number_format($stockValue, 2) }}</td>
                    </tr>
                @endforeach

                @if (count($stocks) == 0)
                    <tr>
                        <td colspan="13" style="text-align: center; padding: 20px;">No data found</td>
                    </tr>
                @endif
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="12" style="text-align: right; font-weight: bold;">Total Stock Value:</td>
                    <td class="textend" style="font-weight: bold;">{{ number_format($totalValue, 2) }}</td>
                </tr>
            </tfoot>
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
