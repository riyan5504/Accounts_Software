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

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        a:hover {
            color: #0d6efd;
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
    <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="card shadow-sm border-0 mt-2 mb-2 p-2 no-print">
                <div class="row align-items-end">
                    <!--begin::Filter-->
                    <div class="col-md-9">
                        <form method="GET" action="{{ url('/report/vendor-due') }}">
                            @csrf
                            <div class="row g-2 align-items-end">
                                <!-- Filter Type -->
                                <div class="form-group col-md-2">
                                    <select name="type" id="filterType" class="form-control form-control-sm">
                                        <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>All
                                        </option>
                                        <option value="supplier" {{ request('type') == 'supplier' ? 'selected' : '' }}>By
                                            Vendor
                                        </option>
                                    </select>
                                    <label for="type" class="floating-label">Filter Type</label>
                                </div>

                                <!-- Supplier -->
                                <div class="form-group col-md-3 d-none" id="supplierField">
                                    <select name="vendor_id" class="form-control form-control-sm">
                                        <option selected disabled>Select Vendor</option>
                                        @foreach ($vendors as $vendor)
                                            <option value="{{ $vendor->id }}"
                                                {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                                {{ $vendor->v_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="vendor_id" class="floating-label">Vendor Name</label>
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

                    <div class="form-group col-md-3 d-flex justify-content-end gap-1">
                        <a href="{{ route('report.vendor-due.pdf', request()->query()) }}"
                            class="btn btn-sm btn-outline-danger" title="Download PDF">
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
                                <h5>Vendor Due Balance</h5>
                            </div>
                            <div class="card m-1">
                                <div class="card-body p-0">
                                    <table class="table table-hover table-bordered align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="background:#f5bfbf; width: 40px">Sl. No.</th>
                                                <th style="background:#f5bfbf; width: 180px">Vendor Name</th>
                                                <th style="background:#f5bfbf; width: 100px">Contact No.</th>
                                                <th style="background:#f5bfbf; width: 270px">Address</th>
                                                <th style="background:#f5bfbf; width: 80px">Opening Balance</th>
                                                <th style="background:#f5bfbf; width: 90px">Bill Amount</th>
                                                <th style="background:#f5bfbf; width: 90px">Paid Amount</th>
                                                <th style="background:#f5bfbf; width: 90px">Return Amount</th>
                                                <th style="background:#f5bfbf; width: 90px">Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $totalOpening = 0;
                                                $totalBill = 0;
                                                $totalPayment = 0;
                                                $totalReturn = 0;
                                                $totalBalance = 0;
                                            @endphp
                                            @forelse ($reportData as $key => $data)
                                                @php
                                                    $totalOpening += $data['opening'];
                                                    $totalBill += $data['bill'];
                                                    $totalPayment += $data['payment'];
                                                    $totalReturn += $data['return'];
                                                    $totalBalance += $data['balance'];
                                                @endphp
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>
                                                        <a href="{{ route('report.vendor-ledger', ['vendor_id' => $data['vendor']->id]) }}"
                                                            style="text-decoration: none; color:black">
                                                            {{ $data['vendor']->v_name }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $data['vendor']->phone ?? '-' }}</td>
                                                    <td>{{ $data['vendor']->address ?? '-' }}</td>

                                                    <td style="text-align: end">{{ number_format($data['opening'], 2) }}
                                                    </td>
                                                    <td class="text-end fw-semibold text-primary">
                                                        {{ number_format($data['bill'], 2) }}
                                                    </td>
                                                    <td style="text-align: end">{{ number_format($data['payment'], 2) }}
                                                    </td>
                                                    <td style="text-align: end">{{ number_format($data['return'], 2) }}
                                                    </td>
                                                    <td
                                                        class="text-end fw-bold {{ $data['balance'] < 0 ? 'text-danger' : 'text-success' }}">
                                                        <strong>{{ number_format($data['balance'], 2) }}</strong>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="text-center text-danger">
                                                        No Data Found
                                                    </td>
                                                </tr>
                                            @endforelse
                                            @if (count($reportData) > 0)
                                                <tr class="table-primary fw-bold" style="background: #e6f0ff; font-weight: bold;">
                                                    <td colspan="4" style="text-align: right;">Grand Total</td>

                                                    <td style="text-align: end">{{ number_format($totalOpening, 2) }}</td>
                                                    <td style="text-align: end">{{ number_format($totalBill, 2) }}</td>
                                                    <td style="text-align: end">{{ number_format($totalPayment, 2) }}</td>
                                                    <td style="text-align: end">{{ number_format($totalReturn, 2) }}</td>
                                                    <td
                                                        style="text-align: end; color: {{ $totalBalance < 0 ? 'red' : 'green' }}">
                                                        {{ number_format($totalBalance, 2) }}
                                                    </td>
                                                </tr>
                                            @endif
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
        function toggleFields() {
            let type = document.getElementById('filterType').value;

            let supplierField = document.getElementById('supplierField');

            supplierField.classList.add('d-none');

            if (type === 'supplier') {
                supplierField.classList.remove('d-none');
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
