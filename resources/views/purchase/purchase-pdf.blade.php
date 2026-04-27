<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice</title>

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
            padding-top: 240px;
            text-align: center;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <table class="header">
        <tr>
            <td class="text-center">
                <div class="company-name">Veshoz Village Private Limited</div>
                <div class="small">A Trusuted Source of Aloe Vera & Herb Product</div>
                <div class="small">Mob: 01721336504</div>
                <div class="small">Flat-3/A, House-53, Road-14</div>
                <div class="small">Sector-13, Uttara, Dhaka-1230.</div>
            </td>
        </tr>
    </table>

    <hr>

    <!-- INVOICE INFO -->
    <h5 class="text-center fw-bold mt-1">PURCHASE INVOICE</h5>
    <table>
        <tr>
            <td class="text-start">
                <strong>Vendor Name:</strong> {{ $purchase->vendor->v_name }}<br>
                <strong>Address:</strong> {{ $purchase->vendor->address }}<br>
                <strong>Phone:</strong> {{ $purchase->vendor->phone }}<br>
                <strong>Email:</strong> {{ $purchase->vendor->email }}
            </td>

            <td class="text-end">
                <strong>Invoice Number:</strong> {{ $purchase->invoice_no }}<br>
                <strong>Date:</strong> {{ $purchase->date->format('d M Y') }}<br>
                <strong>Time:</strong> {{ now()->format('h:i A') }}
            </td>
        </tr>
    </table>

    <!-- ITEMS -->
    <table class="border" style="margin-top:10px;">
        <thead>
            <tr class="text-center">
                <th>Sl. No.</th>
                <th>Item Name</th>
                <th>Pack Size</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Amount</th>
                <th>Vat</th>
                <th>Total Amount</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($purchase->purchaseItems as $singleItm)
                <tr class="align-middle">
                    <td class="text-center">{{ $loop->index + 1 }}</td>
                    <td class="text-start">{{ $singleItm->item->item_name }}</td>
                    <td class="text-center">{{ $singleItm->item->size }}
                        {{ $singleItm->item->unit }}</td>
                    <td class="text-center">{{ $singleItm->qty }}</td>
                    <td class="text-end">{{ number_format($singleItm->unit_price, 2) }}</td>
                    <td class="text-end">{{ number_format($singleItm->price, 2) }}</td>
                    <td class="text-end">{{ number_format($singleItm->vat_amount, 2) }}</td>
                    <td class="text-end">{{ number_format($singleItm->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- SUMMARY -->
    <table>
        <tr>
            <td style="width:60%"></td>
            <td>
                <table>
                    <tr>
                        <td>Sub Total</td>
                        <td class="text-end">{{ number_format($purchase->sub_total, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Vat Amount:</td>
                        <td class="text-end">{{ number_format($purchase->vat_amt, 2) }}</td>
                    </tr>
                    <tr>
                        <td>
                            @if (!empty($purchase->dis_percent))
                                Discount Amount ({{ $purchase->dis_percent }}%):
                            @else
                                Discount Amount:
                            @endif
                        </td>
                        <td class="text-end">{{ number_format($purchase->dis_amt, 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Grand Total:</strong></td>
                        <td class="text-end"><strong>{{ number_format($purchase->grand_total, 2) }}</strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- PAYMENT -->
    <p>
        <strong>Payment Status:</strong>
        {{ $purchase->due_amt == 0 ? 'Paid' : 'Due' }}
    </p>

    <!-- IN WORDS -->
    <p>
        <strong>In Words:</strong>
        Taka {{ ucwords(\App\Helpers\NumberHelper::numberToWords($purchase->grand_total)) }} Only
    </p>

    <div class="footer">
        <!-- SIGNATURE -->
        <table class="signature">
            <tr>
                <td>
                    ----------------------<br>
                    Customer Signature
                </td>
                <td>
                    The sales product will be returnable within 15 days
                </td>
                <td>
                    ----------------------<br>
                    Seller Signature
                </td>
            </tr>
        </table>

        <hr>

        <p class="text-center">
            Thank you for your business! Need any information? Call us.
        </p>

    </div>

</body>

</html>
