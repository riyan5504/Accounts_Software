<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Purchase List</title>
    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 12px;
            position: relative;
            min-height: 100vh;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .text-start {
            text-align: left;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 3px;
        }

        /* Header */
        .header td {
            border: none;
        }

        .company-name {
            font-size: 17px;
            font-weight: bold;
            
        }

        .small {
            font-size: 10px;
        }

        /* Table */
        .border th,
        .border td {
            border: 1px solid #2e2e2e;
        }

        .footer {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
        }

        /* Signature */
        .signature td {
            border: none;
            padding-top: 40px;
            text-align: left;
        }
    </style>
</head>

<body>
    <table class="header">
        <tr>
            <td class="text-center">
                <div class="company-name">
                    <img src="{{ public_path('backend/dist/assets/img/logo02.png') }}" width="60">
                    <span>Veshoz Village Private Limited</span>
                </div>
                <div class="small">A Trusuted Source of Aloe Vera & Herb Product</div>
                <div class="small">Mob: 01721336504</div>
                <div class="small">Flat-3/A, House-53, Road-14</div>
                <div class="small">Sector-13, Uttara, Dhaka-1230.</div>
            </td>
        </tr>
    </table>
    <h3 style="text-align:center;">Purchase List</h3>

    <table width="100%" border="1" cellspacing="0" cellpadding="5">
        <thead>
            <tr>
                <th>SL</th>
                <th>Vendor</th>
                <th>Date</th>
                <th>Invoice</th>
                <th>Item</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchases as $purchase)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td>{{ $purchase->vendor->v_name }}</td>
                    <td>{{ $purchase->date->format('d-m-y') }}</td>
                    <td>{{ $purchase->invoice_no }}</td>
                    <td>
                        @foreach ($purchase->purchaseItems as $item)
                            {{ $item->item->item_name }}{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    </td>
                    <td>{{ $purchase->grand_total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <!-- SIGNATURE -->
        <table class="signature">
            <tr>
                <td>
                    -------------------<br>
                    Received By
                </td>
                <td style="text-align: right">
                    -------------------<br>
                    Authorized By
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
