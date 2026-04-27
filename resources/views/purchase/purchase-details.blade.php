@extends('backend.master')
@push('style')
    <!-- Styles -->
    <style>
        table td,
        table th {
            padding: 3px !important;
            font-size: 12px;
        }

        p {
            margin-bottom: 3px;
        }

        /* Print Style */
        @media print {
            .no-print {
                display: none;
            }

            body {
                background: #fff;
            }

        }
    </style>
@endpush
@section('content')
    <!--begin::App Content Header-->
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row g-0">

                <div class="col-sm-8">
                    <ol class="breadcrumb float-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ url('/purchase') }}"
                                class="{{ request()->is('purchase') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Purchase
                            </a>
                        </li>

                        <li class="breadcrumb-item">
                            <a href="{{ url('/purchase/entry') }}"
                                class="{{ request()->is('purchase/entry') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Entry
                            </a>
                        </li>

                        <li class="breadcrumb-item">
                            <a href="{{ url('/purchase/list') }}"
                                class="{{ request()->is('purchase/list') ? 'text-primary fw-bold' : 'text-dark' }}">
                                List
                            </a>
                        </li>

                        <li class="breadcrumb-item">
                            <a href="{{ url('/item/list') }}"
                                class="{{ request()->is('item/list') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Item List
                            </a>
                        </li>

                        <li class="breadcrumb-item">
                            <a href="{{ route('purchase.vendorlist') }}"
                                class="{{ request()->routeIs('purchase.vendorlist') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Vendor List
                            </a>
                        </li>
                    </ol>
                </div>
                <!-- Print Button -->
                <div class="col-sm-4 text-end no-print mt-1">
                    <button onclick="window.print()" class="btn btn-primary btn-sm">
                        🖨️ Print Invoice
                    </button>
                    <a href="{{ route('purchase.invoice.pdf', $purchase->id) }}" class="btn btn-primary btn-sm">
                        📄 Download PDF
                    </a>
                </div>
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::App Content Header-->
    <!--begin::App Content-->
    <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <!--begin::Col-->
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="row">
                            <div class="text-center mt-1">
                                <h5 class="fw-bold">PURCHASE INVOICE</h5>
                            </div>
                            <div class="col-md-8 ms-1">
                                <h6 class="fw-bold mb-3">Vendor Information</h6>

                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th style="width:150px">Vendor Name:</th>
                                        <td>{{ $purchase->vendor->v_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Address:</th>
                                        <td>{{ $purchase->vendor->address }}</td>
                                    </tr>
                                    <tr>
                                        <th>Phone:</th>
                                        <td>{{ $purchase->vendor->phone }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email:</th>
                                        <td>{{ $purchase->vendor->email }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-3">
                                <h6 class="fw-bold">Invoice Details</h6>

                                <table class="table table-sm table-borderless ">
                                    <tr>
                                        <th style="width:150px">Invoice No:</th>
                                        <td>{{ $purchase->invoice_no }}</td>
                                    </tr>
                                    <tr>
                                        <th>Date:</th>
                                        <td>{{ $purchase->date->format('d-m-y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Purchase By:</th>
                                        <td>{{ auth()->user()->name ?? 'Admin' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div>
                            <div class="card-body p-1">
                                <table class="table table-bordered table-sm align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 100px">Sl. No.</th>
                                            <th style="width: 350px">Item Name</th>
                                            <th>Pack Size</th>
                                            <th>Quantity</th>
                                            <th>Unit Price</th>
                                            <th>Amount</th>
                                            <th>Vat</th>
                                            <th class="text-end">Total Amount</th>
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
                            </div>
                            <div class="container mb-2">
                                <!-- Summary + Payment -->
                                <div class="row">
                                    <!-- Payment Left -->
                                    <div class="col-6 mt-3">
                                        <p class="mb-0"><strong>Paid - Payment From Accounts</strong></p>
                                        <table class="table table-sm w-75">
                                            <thead class="table-light">
                                                <tr>
                                                    <td>SL</td>
                                                    <td>Cash/Bank Account</td>
                                                    <td class="text-end">Amount</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>Cash</td>
                                                    <td class="text-end">{{ number_format($purchase->paid_amt, 2) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Summary Right -->
                                    <div class="col-6">
                                        <table class="table table-sm">
                                            <tr>
                                                <td>Quantity Total:</td>
                                                <td class="text-end">
                                                    {{ $purchase->purchaseItems->sum('qty') }}
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Sub Total:</td>
                                                <td class="text-end">
                                                    {{ number_format($purchase->sub_total, 2) }}
                                                </td>
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
                                                <th>Grand Total:</th>
                                                <th class="text-end">
                                                    {{ number_format($purchase->grand_total, 2) }}
                                                </th>
                                            </tr>

                                            <tr>
                                                <td>Paid:</td>
                                                <td class="text-end">
                                                    {{ number_format($purchase->paid_amt, 2) }}
                                                </td>
                                            </tr>

                                            <tr>
                                                <th>Due:</th>
                                                <th class="text-end">
                                                    {{ number_format($purchase->due_amt, 2) }}
                                                </th>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <!-- In Words -->
                                <div>
                                    <p>
                                        <strong>In Word:</strong>
                                        Taka
                                        {{ ucwords(\App\Helpers\NumberHelper::numberToWords($purchase->grand_total)) }}
                                        Only.
                                    </p>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    </div>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::App Content-->
@endsection
