@extends('backend.master')
@push('style')
    <!-- Styles -->
    <style>
        /* Print Style */
        @media print {
            .no-print {
                display: none !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
                border-radius: 8px;
            }

            table {
                font-size: 11px;
            }

            .table-hover tbody tr:hover {
                background-color: #f8f9fa;
            }

            .table td,
            .table th {
                vertical-align: middle;
                font-size: 13px;
                padding: 2px !important;
            }

            a:hover {
                color: #0d6efd !important;
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
    <!--begin::App Content-->
    <div class="app-content">
        <div class="container-fluid">
            <div class="card shadow-sm border-0 mt-2 mb-2 p-2 no-print">
                <div class="row d-flax align-items-end">
                    <!--begin::Filter-->
                    <div class="col-md-8">
                        <form method="GET" action="{{ url('/report/stock') }}">
                            @csrf
                            <div class="row g-2 align-items-end">
                                <!-- Filter Type -->
                                <div class="form-group col-md-2">
                                    <select name="type" id="filterType" class="form-control form-control-sm">
                                        <option value="all" {{ request('type', 'all') == 'all' ? 'selected' : '' }}>
                                            All
                                        </option>

                                        <option value="item" {{ request('type') == 'item' ? 'selected' : '' }}>
                                            By Item
                                        </option>
                                    </select>
                                    <label for="type" class="floating-label">Filter Type</label>
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

                                <!-- Button -->
                                <div class="form-group col-md-2">
                                    <button class="btn btn-primary btn-sm w-100">
                                        <i class="bi bi-funnel"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="form-group col-md-4 d-flex justify-content-end gap-1 no-print">
                        <a href="{{ route('report.stock.pdf', request()->query()) }}" class="btn btn-sm btn-outline-danger"
                            title="Download PDF">
                            <i class="bi bi-file-pdf"></i>
                        </a>
                        <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary" title="Go Back">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="ms-2 pt-1">
                    <h5>Item Stock Report</h5>
                </div>
                <div class="card m-1">
                    <div class="card-body p-0">
                        <table class="table table-hover table-bordered align-middle mb-0">
                            <thead class="table-light text-center">
                                <tr style="text-align:center;">
                                    <th style=" background:#f5bfbf; width:25px;">Sl. No.</th>
                                    <th style=" background:#f5bfbf;">Code</th>
                                    <th style=" background:#f5bfbf; width:120px;">Name</th>
                                    <th style=" background:#f5bfbf; width:140px;">Category</th>
                                    <th style=" background:#f5bfbf;">Opening Stock</th>
                                    <th style=" background:#f5bfbf;">Purchase</th>
                                    <th style=" background:#f5bfbf;">Production</th>
                                    <th style=" background:#f5bfbf;">Sales</th>
                                    <th style=" background:#f5bfbf;">Consume</th>
                                    <th style=" background:#f5bfbf;">Purchase Return</th>
                                    <th style=" background:#f5bfbf;">Sales Return</th>
                                    <th style=" background:#f5bfbf;">Damage</th>
                                    <th style=" background:#f5bfbf;">Current Stock</th>
                                    <th style=" background:#f5bfbf;">Stock Value</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php
                                    $grandStockValue = 0;
                                @endphp
                                @foreach ($stocks as $key => $item)
                                    @php
                                        $opening = $item->opening_stock_sum ?? 0;
                                        $purchase = $item->purchase_stock_sum ?? 0;
                                        $production = $item->production_in_sum ?? 0;
                                        $sales = $item->sales_stock_sum ?? 0;
                                        $consume = $item->consume_sum ?? 0;
                                        $preturn = $item->purchase_return_sum ?? 0;
                                        $sreturn = $item->sales_return_sum ?? 0;
                                        $damage = $item->damage_sum ?? 0;

                                        $currentStock =
                                            $opening +
                                            $purchase +
                                            $production +
                                            $sreturn -
                                            ($sales + $consume + $damage + $preturn);

                                        $stockValue = $currentStock * $item->unit_price;
                                        $grandStockValue += $stockValue;
                                    @endphp

                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->item_code }}</td>
                                        <td>
                                            <a href="{{ route('report.item.ledger', ['item_id' => $item->id]) }}"
                                                style="text-decoration: none; color:black">
                                                {{ $item->item_name }}
                                            </a>
                                        </td>
                                        <td>{{ $item->category->cat_name ?? '' }}</td>

                                        <td style="text-align:end;">
                                            {{ $opening > 0 ? $opening . ' ' . $item->stock_unit : '-' }}</td>
                                        <td class="text-end fw-semibold text-primary">
                                            {{ $purchase > 0 ? $purchase . ' ' . $item->stock_unit : '-' }}
                                        </td>
                                        <td style="text-align:end;">
                                            {{ $production > 0 ? $production . ' ' . $item->stock_unit : '-' }}</td>
                                        <td style="text-align:end;">
                                            {{ $sales > 0 ? $sales . ' ' . $item->stock_unit : '-' }}
                                        </td>
                                        <td style="text-align:end;">
                                            {{ $consume > 0 ? $consume . ' ' . $item->stock_unit : '-' }}</td>
                                        <td style="text-align:end;">
                                            {{ $preturn > 0 ? $preturn . ' ' . $item->stock_unit : '-' }}</td>
                                        <td style="text-align:end;">
                                            {{ $sreturn > 0 ? $sreturn . ' ' . $item->stock_unit : '-' }}</td>
                                        <td style="text-align:end;">
                                            {{ $damage > 0 ? $damage . ' ' . $item->stock_unit : '-' }}
                                        </td>

                                        <td
                                            class="text-end fw-bold {{ $currentStock < 0 ? 'text-danger' : 'text-success' }}">
                                            {{ $currentStock }} {{ $item->stock_unit }}
                                        </td>
                                        <td class="text-end fw-semibold">
                                            {{ number_format($stockValue, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="table-primary fw-bold" style="background:#d1e7dd;font-weight:bold;">
                                    <td colspan="13" class="text-end">
                                        Total Stock Value :
                                    </td>
                                    <td class="text-end">
                                        {{ number_format($grandStockValue, 2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Container-->
    </div>
@endsection

@push('script')
    <script>
        function toggleFields() {
            let type = document.getElementById('filterType').value;
            let itemField = document.getElementById('itemField');
            itemField.classList.add('d-none');

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
