<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Production List</title>

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

        /* Company Section */
        .company-section {
            text-align: center;
            margin-bottom: 8px;
        }

        .company-section img {
            width: 50px;
            margin-bottom: 3px;
        }

        .company-title {
            font-size: 16px;
            font-weight: bold;
        }

        .company-info {
            font-size: 11px;
            margin-top: 1px;
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
            margin-top: 10px;
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
        <img src="{{ public_path('backend/dist/assets/img/logo02.png') }}">
        <div class="company-title">{{ $companyName ?? 'Company Name' }}</div>
        <div class="company-info">A Trusted Source of Aloe Vera & Herb Product</div>
        <div class="company-info">Mob: 01721336504</div>
        <div class="company-info">Flat-3/A, House-53, Road-14</div>
        <div class="company-info">Sector-13, Uttara, Dhaka-1230</div>
    </div>

    <!-- Title -->
    <div class="text-center">
        <div class="report-title">Production List</div>
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
                <th>Sl. No.</th>
                <th>Date</th>
                <th>Batch No</th>
                <th>Product</th>
                <th>Batch Size</th>
                <th>Final Qty</th>
                <th>Total Cost</th>
                <th>Unit Cost</th>
            </tr>
        </thead>

        <tbody>
             @foreach ($productions as $key => $row)
                <tr>

                    <td>{{ $key + 1 }}</td>

                    <td>{{ date('d-m-Y', strtotime($row->date)) }}</td>

                    <td>{{ $row->batch_no }}</td>

                    <td>{{ $row->name }}</td>

                    <td>{{ $row->batch_size }}</td>

                    <td>{{ $row->final_qty }} {{ $row->final_unit }}</td>

                    <td>{{ number_format($row->grand_total, 2) }}</td>

                    <td>{{ number_format($row->unit_cost, 2) }}</td>

                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5">
                    Total Batch : {{ $summary['total_batch'] }}
                </th>

                <th>
                    {{ number_format($summary['total_qty'], 2) }}
                </th>

                <th>
                    {{ number_format($summary['total_cost'], 2) }}
                </th>

                <th></th>
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

