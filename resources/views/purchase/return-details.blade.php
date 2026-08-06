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
            <div class="row bg-info opasity-50 rounded p-1">
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
                                class="{{ request()->is('/purchase/return/list') ? 'text-primary fw-bold' : 'text-dark' }}">
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
                <!-- Print Button -->

                <div class="col-sm-4 text-end no-print mt-1">
                    <a href="{{ route('purchase.return.pdf', $return->id) }}" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-file-pdf"></i>
                    </a>
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-warning" title="Go Back">
                        <i class="bi bi-arrow-left"></i>
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
                                <h5 class="fw-bold">RETURN INVOICE</h5>
                            </div>
                            <div class="col-md-8 ms-1">
                                <h6 class="fw-bold mb-3">Vendor Information</h6>

                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th style="width:150px">Vendor Name:</th>
                                        <td>{{ $return->vendor->v_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Address:</th>
                                        <td>{{ $return->vendor->address }}</td>
                                    </tr>
                                    <tr>
                                        <th>Phone:</th>
                                        <td>{{ $return->vendor->phone }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email:</th>
                                        <td>{{ $return->vendor->email }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-3">
                                <h6 class="fw-bold">Invoice Details</h6>

                                <table class="table table-sm table-borderless ">
                                    <tr>
                                        <th style="width:150px">Invoice No:</th>
                                        <td>{{ $return->invoice_no }}</td>
                                    </tr>
                                    <tr>
                                        <th>Date:</th>
                                        <td>{{ $return->date->format('d-m-y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Return By:</th>
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
                                            <th>Unit Price (TK)</th>
                                            <th>Amount (TK)</th>
                                            <th>Vat (TK)</th>
                                            <th class="text-end">Total Amount (TK)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($return->purchaseReturnItems as $singleItm)
                                            <tr class="align-middle">
                                                <td class="text-center">{{ $loop->index + 1 }}</td>
                                                <td class="text-start">{{ $singleItm->item->item_name }}</td>
                                                <td class="text-center">{{ $singleItm->item->size }}</td>
                                                <td class="text-center">{{ $singleItm->qty }}
                                                    {{ $singleItm->item->stock_unit }}</td>
                                                <td class="text-end">{{ number_format($singleItm->unit_price, 2) }}</td>
                                                <td class="text-end">{{ number_format($singleItm->price, 2) }}</td>
                                                <td class="text-end">{{ number_format($singleItm->vat_amount, 2) }}</td>
                                                <td class="text-end">{{ number_format($singleItm->total_price, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{-- Summery........ --}}
                            <div class="container mb-2">
                                <div class="d-flex justify-content-end mb-2">
                                    <div style="width:350px">
                                        <table class="table table-sm">
                                            <tr>
                                                <td>Quantity Total:</td>
                                                <td class="text-end">
                                                    {{ $return->purchaseReturnItems->sum('qty') }}
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Sub Total:</td>
                                                <td class="text-end">
                                                    {{ number_format($return->sub_total, 2) }}
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Vat Amount:</td>
                                                <td class="text-end">{{ number_format($return->vat_amt, 2) }}</td>
                                            </tr>

                                            <tr>
                                                <td>
                                                    @if (!empty($return->dis_percent))
                                                        Discount Amount ({{ $return->dis_percent }}%):
                                                    @else
                                                        Discount Amount:
                                                    @endif
                                                </td>
                                                <td class="text-end">{{ number_format($return->dis_amt, 2) }}</td>
                                            </tr>

                                            <tr>
                                                <th>Grand Total:</th>
                                                <th class="text-end">
                                                    {{ number_format($return->grand_total, 2) }}
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
                                        {{ ucwords(\App\Helpers\NumberHelper::numberToWords($return->grand_total)) }}
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
