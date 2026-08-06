@extends('backend.master')

@push('style')
    <style>
        
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        table td,
        table th {
            padding: 4px !important;
            font-size: 12px;
            vertical-align: middle;
        }

        .summary-box {
            background: #f8f9fa;
            padding: 8px;
            border: 1px solid #eee;
            border-radius: 6px;
            font-size: 13px;
        }

        /* Print */
        @media print {
            .no-print {
                display: none !important;
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
    <div class="app-content">
        <div class="container-fluid">
            {{-- 🔹 FILTER CARD --}}
            <div class="card shadow-sm border-0 mt-2 p-1 no-print">
                <div class="row g-2 align-items-end">
                    {{-- Vendor --}}
                    <div class="form-group col-md-3">
                        <select id="vendor_id" class="form-control form-control-sm">
                            <option value="">Select Vendor</option>
                            @foreach ($vendors as $v)
                                <option value="{{ $v->id }}">
                                    {{ $v->v_name }}
                                </option>
                            @endforeach
                        </select>
                        <label class="floating-label">Vendor</label>
                    </div>

                    {{-- From --}}
                    <div class="form-group col-md-2">
                        <input type="date" id="from_date" class="form-control form-control-sm">
                        <label class="floating-label">From</label>
                    </div>

                    {{-- To --}}
                    <div class="form-group col-md-2">
                        <input type="date" id="to_date" class="form-control form-control-sm">
                        <label class="floating-label">To</label>
                    </div>

                    {{-- Invoice --}}
                    <div class="form-group col-md-3">
                        <input type="text" id="invoice" class="form-control form-control-sm" placeholder=" ">
                        <label class="floating-label">Invoice</label>
                    </div>

                    {{-- Button --}}
                    <div class="form-group col-md-2">
                        <button class="btn btn-primary btn-sm w-100" onclick="loadLedger()">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                    </div>

                </div>
            </div>

            {{-- 🔹 PRINT AREA --}}
            <div id="printArea">
                <div class="card text-white bg-success opacity-75 mt-1 mb-1 p-1 rounded">
                    <div class="row">
                        {{-- 🔹 TITLE --}}
                        <div class="col-md-6">
                            <h5 id="vendorTitle" class="mb-2"></h5>
                        </div>
                        {{-- 🔹 PRINT BUTTON --}}
                        <div class="col-md-6 text-end no-print">
                            <button onclick="downloadPDF()" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-file-pdf"></i>
                            </button>
                            <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-warning" title="Go Back">
                                <i class="bi bi-arrow-left"></i>
                            </a>
                        </div>
                    </div>
                </div>
                {{-- 🔹 TABLE --}}
                <div class="card d-none" id="ledgerTable">
                    <div class="card-body p-0">
                        <table class="table table-hover table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="background:#f5bfbf;">Sl. No</th>
                                    <th style="background:#f5bfbf;">Date</th>
                                    <th style="background:#f5bfbf;">Particular</th>
                                    <th style="background:#f5bfbf;">VCH Type</th>
                                    <th style="background:#f5bfbf;">VCH No.</th>
                                    <th style="background:#f5bfbf; text-align:right">Debit</th>
                                    <th style="background:#f5bfbf; text-align:right">Credit</th>
                                    <th style="background:#f5bfbf; text-align:right">Balance</th>
                                </tr>
                            </thead>
                            <tbody id="ledgerBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $('#vendor_id').select2({
            placeholder: "Select Vendor",
            allowClear: true
        });

        function loadLedger() {

            let vendor = $('#vendor_id').val();

            if (!vendor) {
                alert('Please select a vendor');
                return;
            }

            $.ajax({
                url: "{{ route('report.vendor-ledger.data') }}",
                type: "GET",
                data: {
                    vendor_id: vendor,
                    from_date: $('#from_date').val(),
                    to_date: $('#to_date').val(),
                    invoice: $('#invoice').val(),
                },

                success: function(res) {

                    $('#ledgerTable').removeClass('d-none');
                    $('#summary').removeClass('d-none');

                    $('#vendorTitle').text(res.vendor + " Ledger");

                    $('#sum_bill').text(Number(res.summary.bill).toFixed(2));
                    $('#sum_payment').text(Number(res.summary.payment).toFixed(2));
                    $('#sum_balance').text(Number(res.summary.balance).toFixed(2));

                    let rows = '';
                    let sl = 1;

                    // =========================
                    // OPENING BALANCE
                    // =========================
                    rows += `<tr style="background:#e9ecef; font-weight:bold;">
                        <td>${sl++}</td>
                        <td colspan="6">Opening Balance:</td>
                        <td style="text-align:right">${res.opening}</td>
                    </tr>`;

                    // =========================
                    // LEDGER ROWS
                    // =========================
                    res.ledger.forEach(r => {

                        if (r.type === 'Opening') return;

                        rows += `<tr class="table-secondary fw-semibold">
                            <td>${sl++}</td>
                            <td>${r.date}</td>
                            <td>${r.particular}</td>
                            <td>${r.type}</td>
                            <td>${r.vch_no}</td>
                            <td class="text-end fw-semibold text-primary">${Number(r.debit).toFixed(2)}</td>
                            <td style="text-align:right">${Number(r.credit).toFixed(2)}</td>
                            <td class="text-end fw-bold ${r.balance < 0 ? 'text-danger' : 'text-success'}">${Number(r.balance).toFixed(2)}</td>
                        </tr>`;
                    });

                    rows += `<tr class="table-primary fw-bold">
                        <td colspan="5" style="text-align:right;">Grand Total:</td>
                        <td style="text-align:right">${Number(res.summary.total_debit).toFixed(2)}</td>
                        <td style="text-align:right">${Number(res.summary.total_credit).toFixed(2)}</td>                        
            </tr>`;

                    rows += `<tr style="font-size:15px; font-weight:bold;">
                <td colspan="5" style="text-align:right; color:${res.summary.closing < 0 ? 'red' : 'green'}">Closing Balance:</td>
                <td></td>
                <td style="text-align:right; color:${res.summary.closing < 0 ? 'red' : 'green'}">${Number(res.summary.closing).toFixed(2)}</td>
            </tr>`;

                    $('#ledgerBody').html(rows);
                }
            });
        }
    </script>
    <script>
        $(document).ready(function() {

            let urlParams = new URLSearchParams(window.location.search);
            let vendorId = urlParams.get('vendor_id');

            if (vendorId) {
                $('#vendor_id').val(vendorId).trigger('change.select2');

                // 🔥 auto load ledger
                loadLedger();
            }

        });
    </script>
    <script>
        function downloadPDF() {

            let vendor = $('#vendor_id').val();

            if (!vendor) {
                alert('Select vendor first');
                return;
            }

            let url = "{{ route('report.vendor-ledger.pdf') }}";

            url +=
                `?vendor_id=${vendor}&from_date=${$('#from_date').val()}&to_date=${$('#to_date').val()}&invoice=${$('#invoice').val()}`;

            window.open(url, '_blank');
        }
    </script>
@endpush
