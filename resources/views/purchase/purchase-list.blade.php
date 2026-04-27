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
            <div class="row bg-info opasity-50">
                <div class="col-sm-6">
                    <h3 class="mb-0">Purchase List</h3>
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
                            <a href="{{ route('purchase.vendorlist') }}"
                                class="{{ request()->routeIs('purchase.vendorlist') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Vendor List
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
                        <form method="GET" action="{{ url('/purchase/list') }}">
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
                        <button onclick="window.print()" class="btn btn-primary btn-sm">
                            🖨️ Print
                        </button>
                        <a href="{{ route('purchase.list.pdf', request()->all()) }}" class="btn btn-primary btn-sm">
                            📄 PDF
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
                            <div class="ms-2 pt-3">
                                <h5>Purchase List</h5>
                            </div>
                            <div class="card mt-3 ms-1 me-1 mb-3">
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
                                        <tbody id="purchaseTable">
                                            @foreach ($purchases as $purchase)
                                                <tr class="align-middle">
                                                    <td>{{ $loop->index + 1 }}</td>
                                                    <td>{{ $purchase->vendor->v_name }}</td>
                                                    <td>{{ $purchase->date->format('d-m-y') }}</td>
                                                    <td>{{ $purchase->invoice_no }}</td>
                                                    <td>
                                                        @foreach ($purchase->purchaseItems as $singleItm)
                                                            {{ $singleItm->item->item_name ?? 'N/A' }}{{ !$loop->last ? ', ' : '' }}
                                                        @endforeach
                                                    </td>
                                                    <td>{{ $purchase->grand_total }}</td>
                                                    <td style="text-align: center">
                                                        <a href="{{ url('/purchase/details/' . $purchase->id) }}"
                                                            class="btn ms-0 me-0">
                                                            <i class="bi bi-eye text-success"></i>
                                                        </a>

                                                        <a href="{{ url('/purchase/edit/' . $purchase->id) }}"
                                                            class="btn ms-0 me-0">
                                                            <i class="bi bi-pencil text-primary"></i>
                                                        </a>
                                                        <a href="{{ url('/purchase/delete/' . $purchase->id) }}"
                                                            class="btn me-0 ms-0"
                                                            onclick="return confirm('আপনি কি নিশ্চিত? Purchase এবং সব Journal Entry ডিলিট হবে!');">
                                                            <i class="bi bi-trash text-danger"></i>
                                                        </a>
                                                        {{-- <a href="#" class="btn me-0 ms-0"
                                                        onclick="event.preventDefault(); confirmDelete({{ $purchase->id }});">
                                                        <i class="bi bi-trash text-danger"></i>
                                                    </a>

                                                    <form id="deleteForm{{ $purchase->id }}"
                                                        action="{{ route('purchase.delete', $purchase->id) }}"
                                                        method="POST" style="display:none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form> --}}

                                                    </td>
                                                </tr>
                                            @endforeach
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
