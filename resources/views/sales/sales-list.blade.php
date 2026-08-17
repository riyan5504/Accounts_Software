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
                display: none !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }

            table {
                font-size: 11px;
            }

            body * {
                visibility: hidden;
            }

            #printArea,
            #printArea * {
                visibility: visible;
            }

            #printArea {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
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
            <div class="row bg-info opasity-50 rounded">
                <div class="col-sm-4">
                    <h3 class="mb-0">Sales List</h3>
                </div>
                <div class="col-sm-8">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <a href="{{ url('/sales') }}"
                                class="{{ request()->is('sales') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Sales
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('sales.entry') }}"
                                class="{{ request()->routeIs('sales.entry') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Sales Entry
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('sales.list') }}"
                                class="{{ request()->routeIs('sales.list') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Sales List
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('sales.return.list') }}"
                                class="{{ request()->routeIs('sales.return.list') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Return List
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('sales.customer.list') }}"
                                class="{{ request()->routeIs('sales.customer.list') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Customer List
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('sales.customer.received.create') }}"
                                class="{{ request()->routeIs('sales.customer.received.create') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Costomer Received
                            </a>
                        </li>
                    </ol>
                </div>

            </div>
            <!--end::Row-->

            <div class="card-body mt-2">
                <div class="row align-items-end">
                    <!--begin::Filter-->
                    <div class="col-md-9">
                        <form method="GET" action="{{ url('/sales/list') }}">
                            <div class="row g-2 align-items-end">
                                <!-- Filter Type -->
                                <div class="form-group col-md-2">
                                    <select name="type" id="filterType" class="form-control form-control-sm">
                                        <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>All
                                        </option>
                                        <option value="customer" {{ request('type') == 'customer' ? 'selected' : '' }}>By
                                            Customer
                                        </option>
                                        <option value="item" {{ request('type') == 'item' ? 'selected' : '' }}>By Item
                                        </option>
                                    </select>
                                    <label for="type" class="floating-label">Filter Type</label>
                                </div>

                                <!-- customer -->
                                <div class="form-group col-md-3 d-none" id="customerField">
                                    <select name="customer_id" class="form-control form-control-sm">
                                        <option value="">Select customer</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}"
                                                {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->c_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="customer_id" class="floating-label">Customer</label>
                                </div>

                                <!-- Item -->
                                <div class="form-group col-md-3 d-none" id="itemField">
                                    <select name="item_id" class="form-control form-control-sm">
                                        <option value="">Select Item</option>
                                        @foreach ($items as $item)
                                            <option value="{{ $item->id }}"
                                                {{ request('item_id') == $item->id ? 'selected' : '' }}>
                                                {{ $item->item_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="item_id" class="floating-label">Item Name</label>
                                </div>

                                <!-- Date -->
                                <div class="form-group col-md-2">
                                    <input type="date" name="from_date" value="{{ request('from_date') }}"
                                        class="form-control form-control-sm" placeholder=" ">
                                    <label for="from_date" class="floating-label">From</label>
                                </div>

                                <div class="form-group col-md-2">
                                    <input type="date" name="to_date" value="{{ request('to_date') }}"
                                        class="form-control form-control-sm to_date" placeholder=" ">
                                    <label for="to_date" class="floating-label">To</label>
                                </div>

                                <!-- Button -->
                                <div class="form-group col-md-2">
                                    <button class="btn btn-primary btn-sm w-100">Filter</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!--end::Filter-->
                    <div class="col-sm-3 text-end no-print mt-1">
                        <a href="{{ route('sales.list.pdf', request()->all()) }}" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-file-pdf"></i>
                        </a>
                        <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary" title="Go Back">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::App Content Header-->
    <!--begin::App Content-->
    <div class="app-content mt-0">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row g-4">
                <!--begin::Col-->
                <div class="col-md-12">
                    <div id="printArea">
                        <div class="card card-primary card-outline">
                            <div class="ms-2 pt-1">
                                <h5>Sales List</h5>
                            </div>
                            <div class="card m-1">
                                <div class="card-body p-0">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width: 80px">Sl. No.</th>
                                                <th style="width: 180px">Customer Name</th>
                                                <th>Date</th>
                                                <th>Invoice No</th>
                                                <th style="width: 350px">Item</th>
                                                <th>Total Amount</th>
                                                <th style="text-align: center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="salesTable">
                                            @forelse ($sales as $sale)
                                                <tr class="align-middle">
                                                    <td>{{ $loop->index + 1 }}</td>
                                                    <td>{{ $sale->customer->c_name }}</td>
                                                    <td>{{ $sale->date->format('d-m-y') }}</td>
                                                    <td>{{ $sale->invoice_no }}</td>
                                                    <td>
                                                        @foreach ($sale->salesItems as $singleItm)
                                                            {{ $singleItm->item->item_name ?? 'N/A' }}{{ !$loop->last ? ', ' : '' }}
                                                        @endforeach
                                                    </td>
                                                    <td>{{ $sale->grand_total }}</td>
                                                    <td style="text-align: center">
                                                        <a href="{{ url('/sales/details/' . $sale->id) }}"
                                                            class="btn ms-0 me-0">
                                                            <i class="bi bi-eye text-success"></i>
                                                        </a>

                                                        <a href="{{ url('/sales/edit/' . $sale->id) }}"
                                                            class="btn ms-0 me-0">
                                                            <i class="bi bi-pencil text-primary"></i>
                                                        </a>
                                                        <a href="{{ url('/sales/delete/' . $sale->id) }}"
                                                            class="btn me-0 ms-0"
                                                            onclick="return confirm('আপনি কি নিশ্চিত? sales এবং সব Journal Entry ডিলিট হবে!');">
                                                            <i class="bi bi-trash text-danger"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center">No Data Found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
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

@push('script')
    <script>
        function confirmDelete(id) {
            if (confirm('আপনি কি নিশ্চিত? sales এবং সব Journal Entry ডিলিট হবে!')) {
                document.getElementById('deleteForm' + id).submit();
            }
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const filterType = document.getElementById('filterType');
            const customerField = document.getElementById('customerField');
            const itemField = document.getElementById('itemField');

            const customerSelect = document.querySelector('select[name="customer_id"]');
            const itemSelect = document.querySelector('select[name="item_id"]');

            const fromDate = document.querySelector('input[name="from_date"]');
            const toDate = document.querySelector('input[name="to_date"]');

            function updateFilterFields() {

                // Hide both fields first
                customerField.classList.add('d-none');
                itemField.classList.add('d-none');

                if (filterType.value === 'customer') {

                    customerField.classList.remove('d-none');

                } else if (filterType.value === 'item') {

                    itemField.classList.remove('d-none');

                } else if (filterType.value === 'all') {

                    // All selected হলে সব filter value clear
                    customerSelect.value = '';
                    itemSelect.value = '';
                }
            }

            // Type change
            filterType.addEventListener('change', function() {

                if (this.value === 'all') {

                    // সব filter clear করে সরাসরি সব invoice দেখাবে
                    customerSelect.value = '';
                    itemSelect.value = '';

                    // Form submit
                    this.form.submit();

                } else {

                    updateFilterFields();
                }
            });

            // Page load
            updateFilterFields();

        });
    </script>
@endpush
