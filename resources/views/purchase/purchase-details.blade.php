@extends('backend.master')

@section('content')
    <!--begin::App Content Header-->
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Purchase Invoice Details</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
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
                            <a href="{{ url('/vendor/list') }}"
                                class="{{ request()->is('vendor/list') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Vendor List
                            </a>
                        </li>
                    </ol>
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
            <div class="row g-4">
                <!--begin::Col-->
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="row">
                            <div class="col-md-8 mt-3 ms-1">
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


                            <div class="col-md-3 mt-3">
                                <h6 class="fw-bold mb-3">Invoice Details</h6>

                                <table class="table table-sm table-borderless ">
                                    <tr>
                                        <th style="width:150px">Invoice No:</th>
                                        <td>{{ $purchase->invoice_no }}</td>
                                    </tr>
                                    <tr>
                                        <th>Date:</th>
                                        <td>{{ $purchase->date->format('d-m-y') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="card mt-3 ms-1 me-1 mb-3">
                            <div class="card-body p-0">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 100px">Sl. No.</th>
                                            <th style="width: 350px">Item Name</th>
                                            <th>Pack Size</th>
                                            <th>Quentity</th>
                                            <th>Unit Price</th>
                                            <th>Amount</th>
                                            <th>Vat</th>
                                            <th class="text-end">Total Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($purchase->purchaseItems as $singleItm)
                                            <tr class="align-middle">
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>{{ $singleItm->item->item_name }}</td>
                                                <td>{{ $singleItm->item->size }} {{ $singleItm->item->unit }}</td>
                                                <td>{{ $singleItm->qty }}</td>
                                                <td>{{ $singleItm->unit_price }}</td>
                                                <td>{{ $singleItm->price }}</td>
                                                <td>{{ $singleItm->vat_amount }}</td>
                                                <td class="text-end">{{ $singleItm->total_price }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="7" class="text-end">Sub Total:</th>
                                            <th class="text-end">
                                                {{ number_format($purchase->sub_total, 2) }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="7" class="text-end">Vat Amount:</th>
                                            <th class="text-end">
                                                {{ number_format($purchase->vat_amt, 2) }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="7" class="text-end">
                                                @if (!empty($purchase->dis_percent))
                                                    Discount Amount ({{ $purchase->dis_percent }}%):
                                                @else
                                                    Discount Amount:
                                                @endif
                                            </th>

                                            <th class="text-end">
                                                {{ number_format($purchase->dis_amt, 2) }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="7" class="text-end">Grand Total:</th>
                                            <th class="text-end">
                                                {{ number_format($purchase->grand_total, 2) }}
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="ms-2 mb-1 pt-1 text-dark">
                                <h6 class="d-inline fw-bold">Paid Amount:</h6>
                                <p class="d-inline">{{ $purchase->paid_amt }}</p>
                            </div>
                            <div class="ms-2 mb-1 pt-1 text-dark">
                                <h6 class="d-inline fw-bold">Due Amount:</h6>
                                <p class="d-inline">{{ $purchase->due_amt }}</p>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <small class="text-muted fw-bold ms-2 mb-1">
                            @php
                                $inWords = ucwords(\App\Helpers\NumberHelper::numberToWords($purchase->grand_total));
                            @endphp
                            In Words: Taka {{ $inWords }} Only.
                        </small>
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
