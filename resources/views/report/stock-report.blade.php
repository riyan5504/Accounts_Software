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

            .table-container {
                width: 100%;
                max-width: 100%;
                overflow: hidden;
            }

            .table-responsive {
                width: 100%;
                max-width: 100%;
                overflow-x: auto;
                overflow-y: hidden;
            }

            #stockTable {
                width: 100% !important;
                max-width: 100%;
                margin: 0 !important;
            }

            #stockTable th,
            #stockTable td {
                white-space: normal;
                word-break: normal;
                overflow-wrap: break-word;
                vertical-align: middle;
            }

            .dataTables_wrapper {
                width: 100%;
                max-width: 100%;
            }

            .dataTables_scroll {
                width: 100%;
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
                <div class="card-header bg-secondary py-2 mt-1">
                    <div class="d-flex align-items-center flex-nowrap gap-2">
                        <div id="customPagination" class="toolbar-divider d-flex gap-1"></div>

                        <div class="form-check toolbar-divider mb-0">
                            <input class="form-check-input" type="checkbox" id="showAll">
                            <label class="form-check-label" for="showAll">Show all</label>
                        </div>

                        <div class="d-flex toolbar-divider align-items-center">
                            <label class=" me-2 mb-0">No. of rows:</label>
                            <select id="pageLength" class="form-select form-select-sm" style="width:75px;">
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="30">30</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>

                        <div class="d-flex toolbar-divider align-items-center">
                            <label class="me-2 mb-0">Filter rows:</label>
                            <input type="text" id="customSearch" class="form-control form-control-sm d-inline-block"
                                style="width:150px;" placeholder="Search this table">
                        </div>
                        <div class="d-flex align-items-center">
                            <label class="me-2 mb-0">Sort by:</label>
                            <select id="sortColumn" class="form-select form-select-sm d-inline-block" style="width:170px;">
                                <option value="">None</option>
                                <option value="1">Item Code</option>
                                <option value="2">Item Name</option>
                                <option value="3">Category</option>
                                <option value="4">Opening Stock</option>
                                <option value="5">Purchase</option>
                                <option value="6">Production</option>
                                <option value="7">Sales</option>
                                <option value="8">Consume</option>
                                <option value="9">Purchase Return</option>
                                <option value="10">Sales Return</option>
                                <option value="11">Damage</option>
                                <option value="12">Current Stock</option>
                                <option value="13">Stock Value</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card m-1">
                    <div class="card-body p-0 table-container">
                        <div class="table-responsive">
                            <table id="stockTable" class="table table-hover table-bordered align-middle mb-0">
                                <thead class="table-light text-center">
                                    <tr style="text-align:center;">
                                        <th style=" background:#f5bfbf; width: 4%">Sl. No.</th>
                                        <th style=" background:#f5bfbf;">Code</th>
                                        <th style=" background:#f5bfbf; width: 12%">Name</th>
                                        <th style=" background:#f5bfbf; width: 12%">Category</th>
                                        <th style=" background:#f5bfbf;">Opening Stock</th>
                                        <th style=" background:#f5bfbf;">Purchase</th>
                                        <th style=" background:#f5bfbf;">Production</th>
                                        <th style=" background:#f5bfbf; width: 7%">Sales</th>
                                        <th style=" background:#f5bfbf;">Consume</th>
                                        <th style=" background:#f5bfbf;">Purchase Return</th>
                                        <th style=" background:#f5bfbf;">Sales Return</th>
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

                                            $currentStock =
                                                $opening +
                                                $purchase +
                                                $production +
                                                $sreturn -
                                                ($sales + $consume + $preturn);

                                            // Stock valuation price
                                            if (($item->avg_purchase_price ?? 0) > 0) {
                                                // 1. Average Purchase Price
                                                $stockPrice = $item->avg_purchase_price;
                                            } elseif (($item->last_purchase_price ?? 0) > 0) {
                                                // 2. Last Purchase Price
                                                $stockPrice = $item->last_purchase_price;
                                            } else {
                                                // 3. Item Unit Price
                                                $stockPrice = $item->unit_price ?? 0;
                                            }
                                            $stockValue = $currentStock * $stockPrice;
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
                                                {{ $opening > 0 ? $opening . ' ' . $item->stock_unit : '-' }}
                                            </td>

                                            <td class="text-end fw-semibold text-primary">
                                                {{ $purchase > 0 ? $purchase . ' ' . $item->stock_unit : '-' }}
                                            </td>
                                            <td style="text-align:end;">
                                                {{ $production > 0 ? $production . ' ' . $item->stock_unit : '-' }}
                                            </td>
                                            <td style="text-align:end;">
                                                {{ $sales > 0 ? $sales . ' ' . $item->stock_unit : '-' }}
                                            </td>
                                            <td style="text-align:end;">
                                                {{ $consume > 0 ? $consume . ' ' . $item->stock_unit : '-' }}
                                            </td>
                                            <td style="text-align:end;">
                                                {{ $preturn > 0 ? $preturn . ' ' . $item->stock_unit : '-' }}
                                            </td>
                                            <td style="text-align:end;">
                                                {{ $sreturn > 0 ? $sreturn . ' ' . $item->stock_unit : '-' }}
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
                                </tbody>
                                <tfoot>
                                    <tr class="table-primary fw-bold" style="background:#d1e7dd;font-weight:bold;">
                                        <td colspan="12" class="text-end">
                                            Total Stock Value :
                                        </td>
                                        <td class="text-end">
                                            {{ number_format($grandStockValue, 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
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
        document.getElementById('filterType').addEventListener('change',
            toggleFields);
        window.onload = function() {
            toggleFields();
        };
    </script>
    <script>
        if (!$.fn.DataTable.isDataTable('#stockTable')) {

            var table = $('#stockTable').DataTable({
                pageLength: 10,
                dom: 'rtip'
            });

            $('#customSearch').on('keyup', function() {
                table.search(this.value).draw();
            });

            $('#pageLength').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            $('#showAll').on('change', function() {

                if (this.checked) {
                    table.page.len(-1).draw();
                } else {
                    table.page.len($('#pageLength').val()).draw();
                }

            });

            $('#sortColumn').on('change', function() {

                if ($(this).val() == "") {
                    table.order([]).draw();
                } else {
                    table.order([$(this).val(), 'asc']).draw();
                }

            });
            // নিচের pagination লুকিয়ে দিন
            $('.dataTables_paginate').hide();

            function drawPagination() {

                let info = table.page.info();

                let current = info.page;
                let total = info.pages;

                let html = '';

                // First
                if (current > 0) {
                    html += '<button class="first">&lt;&lt;</button>';
                }

                // Previous
                if (current > 0) {
                    html += '<button class="prev">&lt;</button>';
                }

                // Current Page
                html += '<button class="active">' + (current + 1) + '</button>';

                // Next
                if (current < total - 1) {
                    html += '<button class="next">&gt;</button>';
                }

                // Last
                if (current < total - 1) {
                    html += '<button class="last">&gt;&gt;</button>';
                }

                $('#customPagination').html(html);

                $('.first').click(function() {
                    table.page('first').draw('page');
                });

                $('.prev').click(function() {
                    table.page('previous').draw('page');
                });

                $('.next').click(function() {
                    table.page('next').draw('page');
                });

                $('.last').click(function() {
                    table.page('last').draw('page');
                });

            }

            drawPagination();

            table.on('draw', function() {
                drawPagination();
            });
        }
    </script>
@endpush
