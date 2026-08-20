@extends('backend.master')

@push('style')
    <style>
        table td,
        table th {
            padding: 4px !important;
            font-size: 13px;
            vertical-align: middle;
        }

        p {
            margin-bottom: 4px;
        }

        .section-title {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .summary-table td,
        .summary-table th {
            padding: 5px !important;
        }

        .history-table td,
        .history-table th {
            font-size: 12px;
            padding: 4px !important;
        }

        .amount-success {
            color: #198754;
        }

        .amount-warning {
            color: #fd7e14;
        }

        .status-badge {
            font-size: 12px;
            padding: 4px 8px;
        }

        @media print {

            .no-print {
                display: none !important;
            }

            body {
                background: #fff !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }

            .container-fluid {
                width: 100% !important;
                max-width: 100% !important;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            .history-section {
                page-break-inside: avoid;
            }
        }
    </style>
@endpush

@section('content')
    <div class="app-content-header">
        @php
            $hasReturns = $returnedQtyByItem->sum() > 0;

            $initialPaymentRows = $transactions->filter(function ($transaction) {
                return (float) $transaction->paid_amt > 0;
            });

            $hasInitialPayment = $initialPaymentRows->isNotEmpty();

            $hasVendorPayment = $vendorPaymentHistory
                ->filter(function ($detail) {
                    return (float) $detail->paid_amount > 0;
                })
                ->isNotEmpty();
        @endphp
        <div class="container-fluid">
            <div class="row bg-info opacity-75 rounded p-1">
                <div class="col-sm-8">
                    <ol class="breadcrumb float-sm mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ url('/purchase') }}"
                                class="{{ request()->is('purchase') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Purchase
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ url('/purchase/entry') }}"
                                class="{{ request()->is('purchase/entry') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Purchase Entry
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ url('/purchase/list') }}"
                                class="{{ request()->is('purchase/list') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Purchase List
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ url('/item/add') }}"
                                class="{{ request()->is('item/add') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Item Entry
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ url('/purchase/return/list') }}"
                                class="{{ request()->is('purchase/return/list') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Return List
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('purchase.vendor.list') }}"
                                class="{{ request()->routeIs('purchase.vendor.list') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Vendor List
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('vendor-payment.create') }}"
                                class="{{ request()->routeIs('vendor-payment.create') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Vendor Payment
                            </a>
                        </li>
                    </ol>
                </div>

                <!-- ACTION BUTTONS -->
                <div class="col-sm-4 text-end no-print mt-1">
                    <a href="{{ route('purchase.pdf', $purchase->id) }}" class="btn btn-outline-danger btn-sm"
                        title="Download PDF">
                        <i class="bi bi-file-pdf"></i>
                    </a>
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-warning" title="Go Back">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="row">
                            <div class="text-center mt-2">
                                <h5 class="fw-bold mb-1">
                                    PURCHASE INVOICE
                                </h5>
                                @if ($paymentStatus === 'Paid')
                                    <span class="badge bg-success status-badge">PAID</span>
                                @elseif ($paymentStatus === 'Partial')
                                    <span class="badge bg-warning text-dark status-badge">PARTIAL</span>
                                @elseif ($paymentStatus === 'Credit')
                                    <span class="badge bg-info status-badge">CREDIT</span>
                                @else
                                    <span class="badge bg-danger status-badge">UNPAID</span>
                                @endif
                            </div>
                            <!-- VENDOR INFORMATION -->
                            <div class="col-md-8 ms-1 mt-2">
                                <h6 class="fw-bold mb-2">Vendor Information</h6>

                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th style="width:150px">Vendor Name:</th>
                                        <td>{{ $purchase->vendor->v_name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Address:</th>
                                        <td>{{ $purchase->vendor->address ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Phone:</th>
                                        <td>{{ $purchase->vendor->phone ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email:</th>
                                        <td>{{ $purchase->vendor->email ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <!-- INVOICE INFORMATION -->
                            <div class="col-md-3 mt-2">
                                <h6 class="fw-bold">Invoice Details</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th style="width:150px">Invoice No:</th>
                                        <td>{{ $purchase->invoice_no }}</td>
                                    </tr>
                                    <tr>
                                        <th>Date:</th>
                                        <td>{{ $purchase->date->format('d-m-Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Purchase By:</th>
                                        <td>{{ optional($purchase->user)->name ?? (auth()->user()->name ?? 'Admin') }}</td>
                                    </tr>
                                    @if ($purchase->reference)
                                        <tr>
                                            <th>Reference:</th>
                                            <td>{{ $purchase->reference }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        <div class="card-body p-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="fw-bold mb-0">
                                    Purchase Items
                                </h6>
                                @if ($returnCount > 0)
                                    <span class="badge bg-danger">
                                        {{ $returnCount }} Return(s)
                                    </span>
                                @endif
                            </div>

                            <table class="table table-bordered table-sm align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:50px">Sl. No.</th>
                                        <th style="width:300px">Item Name</th>
                                        <th>Pack Size</th>
                                        <th>Purchased Qty</th>
                                        <th>Unit Price (TK)</th>
                                        <th>Amount (TK)</th>
                                        <th>VAT (TK)</th>
                                        <th class="text-end">Total (TK)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($purchase->purchaseItems as $singleItm)
                                        <tr>
                                            <td class="text-center">
                                                {{ $loop->iteration }}
                                            </td>
                                            <td>
                                                {{ $singleItm->item->item_name ?? 'N/A' }}
                                            </td>
                                            <td class="text-center">
                                                {{ $singleItm->item->size ?? '' }}
                                            </td>
                                            <td class="text-center">
                                                {{ number_format($singleItm->qty, 2) }}
                                                {{ $singleItm->item->stock_unit ?? '' }}
                                            </td>
                                            <td class="text-end">
                                                {{ number_format((float) $singleItm->unit_price, 2) }}
                                            </td>
                                            <td class="text-end">
                                                {{ number_format((float) $singleItm->price, 2) }}
                                            </td>
                                            <td class="text-end">
                                                {{ number_format((float) $singleItm->vat_amount, 2) }}
                                            </td>
                                            <td class="text-end fw-bold">
                                                {{ number_format((float) $singleItm->total_price, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center text-muted">
                                                No purchase items found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="container mb-2">
                            <div class="row">
                                <div class="col-md-6 mt-3">
                                    @if ($hasVendorPayment)
                                        <div class="section-title mt-3">
                                            Later Vendor Payments
                                        </div>
                                        <table class="table table-bordered table-sm history-table">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>SL</th>
                                                    <th>Voucher</th>
                                                    <th>Date</th>
                                                    <th>Method</th>
                                                    <th class="text-end">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($vendorPaymentHistory as $detail)
                                                    @php
                                                        $vp = $detail->vendorPayment;
                                                        $allocatedAmount = (float) $detail->paid_amount;
                                                    @endphp
                                                    @if ($allocatedAmount > 0)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $vp->voucher_no ?? 'N/A' }}</td>
                                                            <td>
                                                                @if ($vp && $vp->date)
                                                                    {{ \Carbon\Carbon::parse($vp->date)->format('d-m-Y') }}
                                                                @else
                                                                    N/A
                                                                @endif
                                                            </td>
                                                            <td>{{ ucfirst(str_replace('_', ' ', $vp->payment_method ?? 'N/A')) }}
                                                            </td>
                                                            <td class="text-end text-success">
                                                                {{ number_format($allocatedAmount, 2) }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-light">
                                                    <th colspan="4">Vendor Payment Total</th>
                                                    <th class="text-end">
                                                        {{ number_format($vendorPayment, 2) }}
                                                    </th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    @endif
                                </div>

                                <div class="col-md-6 mt-3">
                                    <table class="table table-sm summary-table">
                                        <tr>
                                            <td>Quantity Total:</td>
                                            <td class="text-end">
                                                {{ number_format($purchase->purchaseItems->sum('qty'), 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Sub Total:</td>
                                            <td class="text-end">
                                                {{ number_format((float) $purchase->sub_total, 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>VAT Amount:</td>
                                            <td class="text-end">
                                                {{ number_format((float) $purchase->vat_amt, 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                @if (!empty($purchase->dis_percent))
                                                    Discount Amount
                                                    ({{ $purchase->dis_percent }}%):
                                                @else
                                                    Discount Amount:
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                {{ number_format((float) $purchase->dis_amt, 2) }}
                                            </td>
                                        </tr>

                                        <!-- ORIGINAL PURCHASE -->
                                        <tr class="border-top">
                                            <th>Original Grand Total:</th>
                                            <th class="text-end">{{ number_format($originalPurchase, 2) }}</th>
                                        </tr>

                                        <!-- RETURN -->
                                        @if ($hasReturns)
                                            <tr>
                                                <td class="text-danger">Purchase Return:</td>
                                                <td class="text-end text-danger">
                                                    @if ($totalReturn > 0)
                                                        - {{ number_format($totalReturn, 2) }}
                                                    @else
                                                        0.00
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                        <!-- INITIAL PAYMENT -->
                                        @if ($hasInitialPayment)
                                            <tr>
                                                <td>Initial Payment:</td>
                                                <td class="text-end">
                                                    {{ number_format($initialPayment, 2) }}
                                                </td>
                                            </tr>
                                        @endif
                                        <!-- VENDOR PAYMENT -->
                                        @if ($hasVendorPayment)
                                            <tr>
                                                <td>Later Vendor Payment:</td>
                                                <td class="text-end">{{ number_format($vendorPayment, 2) }}</td>
                                            </tr>
                                        @endif
                                        <!-- TOTAL PAID -->
                                        @if ($hasInitialPayment || $hasVendorPayment)
                                            <tr class="border-top">
                                                <th>Total Paid:</th>
                                                <th class="text-end">{{ number_format($totalPaid, 2) }}</th>
                                            </tr>
                                        @endif

                                        <!-- DUE -->
                                        @if ($hasReturns || $hasInitialPayment || $hasVendorPayment)
                                            <tr class="border-top">
                                                <th class="{{ $due > 0 ? 'text-danger' : 'text-success' }}">
                                                    Remaining Due:
                                                </th>
                                                <th class="text-end {{ $due > 0 ? 'text-danger' : 'text-success' }}">
                                                    {{ number_format($due, 2) }}</th>
                                            </tr>
                                        @endif

                                        <!-- SUPPLIER CREDIT -->
                                        @if ($supplierCredit > 0)
                                            <tr>
                                                <th class="text-danger">Supplier Credit:</th>
                                                <th class="text-end text-danger">
                                                    -{{ number_format($supplierCredit, 2) }}
                                                </th>
                                            </tr>
                                        @endif
                                        <!-- STATUS -->
                                        <tr class="border-top">
                                            <th>Payment Status:</th>
                                            <th class="text-end">
                                                @if ($paymentStatus === 'Paid')
                                                    <span class="badge bg-success">
                                                        PAID
                                                    </span>
                                                @elseif ($paymentStatus === 'Partial')
                                                    <span class="badge bg-warning text-dark">
                                                        PARTIAL
                                                    </span>
                                                @elseif ($paymentStatus === 'Credit')
                                                    <span class="badge bg-danger">
                                                        CREDIT
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        UNPAID
                                                    </span>
                                                @endif
                                            </th>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <!-- In Words -->
                            <p>
                                <strong>In Words:</strong>
                                @if ($due > 0)
                                    Taka {{ ucwords(\App\Helpers\NumberHelper::numberToWords($due)) }} Only
                                @else
                                    Taka {{ ucwords(\App\Helpers\NumberHelper::numberToWords($originalPurchase)) }}
                                    Only
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
