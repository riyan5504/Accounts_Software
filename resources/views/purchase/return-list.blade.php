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
                    <h3 class="mb-0">Purchase Return List</h3>
                </div>
                <div class="col-sm-8">
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

            </div>
            <!--end::Row-->

            <div class="card-body mt-2">
                <div class="row align-items-end">
                    <!--begin::Filter-->
                    <div class="col-md-9">
                        <form method="GET" action="{{ url('/purchase/return/list') }}">
                            @csrf
                            <div class="row g-2 align-items-end">
                                <!-- Filter Type -->
                                <div class="form-group col-md-2">
                                    <select name="type" id="filterType" class="form-control form-control-sm">
                                        <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>All
                                        </option>
                                        <option value="supplier" {{ request('type') == 'supplier' ? 'selected' : '' }}>By
                                            Supplier
                                        </option>
                                        <option value="item" {{ request('type') == 'item' ? 'selected' : '' }}>By Item
                                        </option>
                                    </select>
                                    <label for="type" class="floating-label">Filter Type</label>
                                </div>

                                <!-- Supplier -->
                                <div class="form-group col-md-3 d-none" id="supplierField">
                                    <select name="vendor_id" class="form-control form-control-sm">
                                        <option value="">Select Supplier</option>
                                        @foreach ($vendors as $vendor)
                                            <option value="{{ $vendor->id }}"
                                                {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                                {{ $vendor->v_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="vendor_id" class="floating-label">Supplier</label>
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
                        <a href="{{ route('purchase.return.list.pdf', request()->query()) }}"
                            class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-file-pdf"></i>
                        </a>
                        <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-warning" title="Go Back">
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
                                <h5>Return List</h5>
                            </div>
                            <div class="card m-1">
                                <div class="card-body p-0">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width: 80px">Sl. No.</th>
                                                <th style="width: 180px">Vendor Name</th>
                                                <th>Date</th>
                                                <th>Invoice No</th>
                                                <th style="width: 350px">Item</th>
                                                <th>Total Amount</th>
                                                <th style="text-align: center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="returnTable">
                                            @forelse ($returns as $return)
                                                <tr class="align-middle">
                                                    <td>{{ $loop->index + 1 }}</td>
                                                    <td>{{ $return->vendor->v_name }}</td>
                                                    <td>{{ $return->date->format('d-m-y') }}</td>
                                                    <td>{{ $return->invoice_no }}</td>
                                                    <td>
                                                        @foreach ($return->purchaseReturnItems as $singleItm)
                                                            {{ $singleItm->item->item_name ?? 'N/A' }}{{ !$loop->last ? ', ' : '' }}
                                                        @endforeach
                                                    </td>
                                                    <td>{{ $return->grand_total }}</td>
                                                    <td style="text-align: center">
                                                        <a href="{{ url('/purchase/return/details/' . $return->id) }}"
                                                            class="btn ms-0 me-0">
                                                            <i class="bi bi-eye text-success"></i>
                                                        </a>

                                                        <a href="{{ url('/purchase/return/edit/' . $return->id) }}"
                                                            class="btn ms-0 me-0">
                                                            <i class="bi bi-pencil text-primary"></i>
                                                        </a>
                                                        <a href="{{ url('/purchase/return/delete/' . $return->id) }}"
                                                            class="btn me-0 ms-0"
                                                            onclick="return confirm('আপনি কি নিশ্চিত? Purchase Return এবং সব Journal Entry ডিলিট হবে!');">
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
            if (confirm('আপনি কি নিশ্চিত? Purchase এবং সব Journal Entry ডিলিট হবে!')) {
                document.getElementById('deleteForm' + id).submit();
            }
        }
    </script>
    <script>
        function toggleFields() {
            let type = document.getElementById('filterType').value;

            let supplierField = document.getElementById('supplierField');
            let itemField = document.getElementById('itemField');

            supplierField.classList.add('d-none');
            itemField.classList.add('d-none');

            if (type === 'supplier') {
                supplierField.classList.remove('d-none');
            }

            if (type === 'item') {
                itemField.classList.remove('d-none');
            }
        }

        // 🔁 change হলে
        document.getElementById('filterType').addEventListener('change', toggleFields);

        // 🔥 page load এ (MAIN FIX)
        window.onload = function() {
            toggleFields();
        };
    </script>
@endpush
