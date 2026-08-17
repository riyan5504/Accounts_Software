<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Production Batch - {{ $production->batch_no }}</title>
    <style>
        @page {
            margin: 13mm 12mm 13mm 12mm;
        }

        body {
            font-family: DejaVu Sans;
            font-size: 13px;
            color: #333;
            line-height: 1.45;
            margin: 0;
            padding: 0;
        }

        * {
            box-sizing: border-box;
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

        /*==========================
        Company Header
    ==========================*/

        .company-section {
            text-align: center;
            border-bottom: 2px solid #999;
            padding-bottom: 10px;
            margin-bottom: 18px;
        }

        .company-section img {
            width: 55px;
            margin-bottom: 5px;
        }

        .company-title {
            font-size: 18px;
            font-weight: bold;
            color: #000;
        }

        .company-info {
            font-size: 10px;
            color: #555;
            line-height: 1.3;
        }

        .invoice-title {
            display: inline-block;
            margin-top: 12px;
            padding-bottom: 4px;
            font-size: 15px;
            font-weight: bold;
            border-bottom: 2px solid #000;
        }

        /*==========================
        Information Table
    ==========================*/

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .info-table td {
            border: none;
            padding: 3px 4px;
            vertical-align: top;
        }

        .label {
            width: 90px;
            font-weight: bold;
            white-space: nowrap;
        }

        /*==========================
        General Table
    ==========================*/

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 12px;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }

        th {
            background: #f2f2f2;
            border: 1px solid #999;
            padding: 6px 1px;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            word-wrap: break-word;
            word-break: break-word;
        }

        td {
            border: 1px solid #ccc;
            padding: 1px;
            font-size: 12px;
            vertical-align: top;
            word-wrap: break-word;
            word-break: break-word;
            overflow-wrap: break-word;
        }

        tbody tr:nth-child(even) {
            background: #fafafa;
        }

        /*==========================
        Section
    ==========================*/

        .section-title {
            margin-top: 14px;
            margin-bottom: 5px;
            padding: 6px 8px;
            background: #ececec;
            border-left: 4px solid #555;
            font-size: 12px;
            font-weight: bold;
        }

        /*==========================
        Summary Box
    ==========================*/

        .summary-box {
            width: 45%;
            margin-left: auto;
            border: 1px solid #999;
            padding: 8px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td {
            border: none;
            padding: 4px 2px;
            background: none;
        }

        .grand-total td {
            font-weight: bold;
            font-size: 13px;
            border-top: 1px solid #666;
        }

        /*==========================
        Inner Table
    ==========================*/

        .info-inner-table {
            width: 100%;
        }

        .info-inner-table td {
            border: none;
            padding: 2px 4px;
        }

        /*==========================
        Signature
    ==========================*/

        .signature {
            width: 100%;
            margin-top: 40px;
        }

        .signature td {
            border: none;
            padding-top: 40px;
            font-size: 12px;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 150px;
            margin: 0 auto 5px;
        }

        /*==========================
        Footer
    ==========================*/

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }

        .note {
            margin-top: 8px;
            font-size: 12px;
            color: #777;
            text-align: center;
        }
    </style>
</head>

<body>

    <!-- Company -->
    <div class="company-section">
        <img src="{{ public_path('backend/dist/assets/img/logo1.png') }}">
        <div class="company-title">{{ $companyName ?? 'Veshoz Village Private Limited' }}</div>
        <div class="company-info">A Trusted Source of Aloe Vera & Herb Product</div>
        <div class="company-info">Flat-3/A, House-53, Road-14, Sector-13, Uttara, Dhaka-1230</div>
        <div class="company-info">Mob: 01721336504</div>
    </div>

    <div class="title">
        Production Details
    </div>

    {{-- Production Information --}}

    <div class="section-title">
        Production Information
    </div>

    <table>
        <tr>
            <th width="25%">Production Date</th>
            <td>{{ date('d-m-Y', strtotime($production->date)) }}</td>

            <th width="25%">Batch No</th>
            <td>{{ $production->batch_no }}</td>
        </tr>
        <tr>
            <th>Product Name</th>
            <td>{{ $production->name }}</td>

            <th>Batch Size</th>
            <td>{{ $production->batch_size }}</td>
        </tr>
        <tr>
            <th>Yield</th>
            <td>{{ $production->yield ?? '-' }}</td>
            <th>Final Quantity</th>
            <td>{{ $production->final_qty }} {{ $production->final_unit }}</td>
        </tr>
    </table>

    {{-- Raw Material --}}

    <div class="section-title">
        Raw Material
    </div>

    <table class="table table-bordered table-sm align-middle">
        <thead class="table-light">
            <tr>
                <th style="width:80px">Sl. No.</th>
                <th style="width:180px">Materials Name</th>
                <th style="width:100px">Quantity</th>
                <th style="width:100px">Rate</th>
                <th style="width:100px">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr class="align-middle">
                <td class="text-center">{{ 1 }}</td>
                <td>{{ $production->item->item_name }}</td>
                <td class="text-center">{{ $production->raw_qty }} {{ $production->raw_unit }}
                </td>
                <td class="text-center">{{ $production->raw_u_price }}</td>
                <td class="text-end">{{ $production->raw_t_price }}</td>
            </tr>
        </tbody>
    </table>



    {{-- Chemical --}}

    @if ($production->chemicals->count())

        <div class="section-title">
            Chemicals
        </div>

        <table class="table table-bordered table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width:80px">Sl. No.</th>
                    <th style="width:180px">Chemicals Name</th>
                    <th class="text-center" style="width:100px">Quantity</th>
                    <th class="text-center" style="width:100px">Rate</th>
                    <th class="text-center" style="width:100px">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($production->chemicals as $chemical)
                    <tr class="align-middle">
                        <td class="text-center">{{ $loop->index + 1 }}</td>
                        <td>{{ $chemical->item->item_name }}</td>
                        <td class="text-center">{{ $chemical->used_qty }}
                            {{ $chemical->ch_unit }}
                        </td>
                        <td class="text-center">{{ $chemical->u_price }}</td>
                        <td class="text-end">{{ $chemical->t_price }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr class="table-success">
                    <th colspan="4" class="text-end">Total Chemical Cost</th>
                    <th class="text-end">{{ $production->sectionTotalCost->raw_grand_price }}</th>
                </tr>
            </tfoot>
        </table>

    @endif

    @if ($production->packagingMaterial->count())

        <div class="section-title">
            Packaging Material
        </div>

        <table class="table table-bordered table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width:80px">Sl. No.</th>
                    <th style="width:180px">Materials Name</th>
                    <th class="text-center" style="width:100px">Quantity</th>
                    <th class="text-center" style="width:100px">Rate</th>
                    <th class="text-center" style="width:100px">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($production->packagingMaterial as $pack)
                    <tr class="align-middle">
                        <td class="text-center">{{ $loop->index + 1 }}</td>
                        <td>{{ $pack->item->item_name }}</td>
                        <td class="text-center">{{ $pack->pack_qty }} nos</td>
                        <td class="text-center">{{ $pack->pack_price }}</td>
                        <td class="text-end">{{ $pack->total_price }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr class="table-success">
                    <th colspan="4" class="text-end">Total Packaging Cost</th>
                    <th class="text-end">{{ $production->sectionTotalCost->pack_grand_price }}
                    </th>
                </tr>
            </tfoot>
        </table>
    @endif

    @if ($production->laborCost->count())

        <div class="section-title">
            Labor Cost
        </div>

        <table class="table table-bordered table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width:80px">Sl. No.</th>
                    <th style="width:180px">Labor Name</th>
                    <th class="text-center" style="width:100px">Duty Day</th>
                    <th class="text-center" style="width:100px">Rate</th>
                    <th class="text-center" style="width:100px">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($production->laborCost as $labor)
                    <tr class="align-middle">
                        <td class="text-center">{{ $loop->index + 1 }}</td>
                        <td>{{ $labor->labor_name }}</td>
                        <td class="text-center">{{ $labor->duty_day }} day
                        </td>
                        <td class="text-center">{{ $labor->d_pay }}</td>
                        <td class="text-end">{{ $labor->total_pay }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr class="table-success">
                    <th colspan="4" class="text-end">Total Labor Cost</th>
                    <th class="text-end">{{ $production->sectionTotalCost->labor_grand_price }}
                    </th>
                </tr>
            </tfoot>
        </table>
    @endif

    @if ($production->utilityCost->count())

        <div class="section-title">
            Utility Cost
        </div>
        <table class="table table-bordered table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width:80px">Sl. No.</th>
                    <th style="width:590px">Head</th>
                    <th class="text-center">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($production->utilityCost as $utility)
                    <tr class="align-middle">
                        <td class="text-center">{{ $loop->index + 1 }}</td>
                        <td>{{ $utility->utility_name }}</td>
                        <td class="text-end">{{ $utility->cost_amt }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr class="table-success">
                    <th colspan="2" class="text-end">Total Utility Cost</th>
                    <th class="text-end">{{ $production->sectionTotalCost->utility_grand_price }}
                    </th>
                </tr>
            </tfoot>
        </table>
    @endif

    @if ($production->depreciation->count())

        <div class="section-title">
            Depreciation
        </div>
        <table class="table table-bordered table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width:80px">Sl. No.</th>
                    <th style="width:590px">Head</th>
                    <th class="text-center">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($production->depreciation as $dep)
                    <tr class="align-middle">
                        <td class="text-center">{{ $loop->index + 1 }}</td>
                        <td>{{ $dep->machine_name }}</td>
                        <td class="text-end">{{ $dep->machine_cost_amt }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr class="table-success">
                    <th colspan="2" class="text-end">Total Depreciation Cost</th>
                    <th class="text-end">
                        {{ $production->sectionTotalCost->depreciation_grand_price }}</th>
                </tr>
            </tfoot>
        </table>
    @endif

    @if ($production->overHeadCost->count())
        <div class="section-title">
            Over Head Cost
        </div>

        <table class="table table-bordered table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width:80px">Sl. No.</th>
                    <th style="width:590px">Head</th>
                    <th class="text-center">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($production->overHeadCost as $overhead)
                    <tr class="align-middle">
                        <td class="text-center">{{ $loop->index + 1 }}</td>
                        <td>{{ $overhead->overhead_type }}</td>
                        <td class="text-end">{{ $overhead->fo_cost_amt }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr class="table-success">
                    <th colspan="2" class="text-end">Total Overhead Cost</th>
                    <th class="text-end">{{ $production->sectionTotalCost->overhead_grand_price }}
                    </th>
                </tr>
            </tfoot>
        </table>
    @endif

    @if ($production->transportCost->count())
        <div class="section-title">
            Transport Cost
        </div>
        <table class="table table-bordered table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width:80px">Sl. No.</th>
                    <th style="width:590px">Head</th>
                    <th class="text-center">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($production->transportCost as $transport)
                    <tr class="align-middle">
                        <td class="text-center">{{ $loop->index + 1 }}</td>
                        <td>{{ $transport->transport_type }}</td>
                        <td class="text-end">{{ $transport->transport_amt }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr class="table-success">
                    <th colspan="2" class="text-end">Total Transport Cost</th>
                    <th class="text-end">
                        {{ $production->sectionTotalCost->transport_grand_price }}</th>
                </tr>
            </tfoot>
        </table>
    @endif

    @if ($production->qcCost->count())
        <div class="section-title">
            QC Cost
        </div>
        <table class="table table-bordered table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width:80px">Sl. No.</th>
                    <th style="width:590px">Head</th>
                    <th class="text-center">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($production->qcCost as $qc)
                    <tr class="align-middle">
                        <td class="text-center">{{ $loop->index + 1 }}</td>
                        <td>{{ $qc->test_name }}</td>
                        <td class="text-end">{{ $qc->qc_amt }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr class="table-success">
                    <th colspan="2" class="text-end">Total QC Cost</th>
                    <th class="text-end">{{ $production->sectionTotalCost->qc_grand_price }}</th>
                </tr>
            </tfoot>
        </table>
    @endif

    {{-- Cost Summary --}}

    <div class="section-title">
        Section Wise Cost
    </div>

    <table>
        <tr>
            <th>Cost Head</th>
            <th width="25%">Amount (Tk)</th>
        </tr>
        <tr>
            <td>Raw Material</td>
            <td class="right">{{ number_format($production->raw_t_price, 2) }}</td>
        </tr>
        <tr>
            <td>Chemical</td>
            <td class="right">{{ number_format($production->sectionTotalCost->raw_grand_price ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>Packaging</td>
            <td class="right">{{ number_format($production->sectionTotalCost->pack_grand_price ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>Labor</td>
            <td class="right">{{ number_format($production->sectionTotalCost->labor_grand_price ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>Utility</td>
            <td class="right">{{ number_format($production->sectionTotalCost->utility_grand_price ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>Depreciation</td>
            <td class="right">{{ number_format($production->sectionTotalCost->depreciation_grand_price ?? 0, 2) }}
            </td>
        </tr>
        <tr>
            <td>Overhead</td>
            <td class="right">{{ number_format($production->sectionTotalCost->overhead_grand_price ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>Transport</td>
            <td class="right">{{ number_format($production->sectionTotalCost->transport_grand_price ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>QC</td>
            <td class="right">{{ number_format($production->sectionTotalCost->qc_grand_price ?? 0, 2) }}</td>
        </tr>
        <tr class="bold">
            <td>Total Production Cost</td>
            <td class="right">{{ number_format($production->grand_total, 2) }}</td>
        </tr>
        <tr class="bold">
            <td>Cost Per Unit</td>
            <td class="right">{{ number_format($costPerUnit, 2) }}</td>
        </tr>
        <tr>
            <td>Highest Cost Head</td>
            <td>{{ $highestCostHead }}</td>
        </tr>
    </table>

    {{-- Signature --}}
    <div class="footer">
        <table width="100%" class="signature">
            <tr>
                <td class="text-left">
                    ----------------------------<br>
                    Customer Signature
                </td>
                <td class="text-right">
                    ----------------------------<br>
                    Seller Signature
                </td>
            </tr>
        </table>
        <hr>
        <div class="note">
            This is a computer generated production report.
        </div>
    </div>
    <script type="text/php">
        if(isset($pdf)){
            $pdf->page_text(
                520, 815, "Page {PAGE_NUM} / {PAGE_COUNT}",
                null, 9,
                array(0,0,0)
            );
        }
    </script>
</body>

</html>
