@extends('backend.master')

@section('content')
    <!--begin::App Content Header-->
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row bg-info opasity-50 rounded">
                <div class="col-sm-4">
                    <h4 class="mb-0 mt-0">Vendor Payment Entry</h4>
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
                            <a href="{{ url('/item/add') }}"
                                class="{{ request()->is('/item/add') ? 'text-primary fw-bold' : 'text-dark' }}">
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
            <!--begin::Quick Example-->
            <div class="card card-primary card-outline">
                <!--begin::Form-->
                <form action="{{ route('vendor-payment.store') }}" method="POST" id="paymentForm">
                    @csrf
                    <!--begin::Body-->
                    <div class="card-body">
                        <!--start::vendor-->
                        <input type="hidden" id="vendorSearchUrl" value="{{ route('search.vendor') }}">
                        <input type="hidden" id="accountStatusUrl" value="{{ url('/search/accounts-by-status') }}">

                        <div id="vendorPaymentContainer" class="border-0 shadow-sm ms-0">
                            <div class="card-header bg-primary text-white mb-2">
                                <strong>Payment Information</strong>
                            </div>
                            <!-- ✅ Proper Row Structure -->
                            <div class="row g-2 align-vendor-end mb-1">
                                <div class="form-group col-md-6 mb-1 position-relative">
                                    <input type="text" name="v_name" class="form-control v_name" id="v_name"
                                        placeholder=" " required />
                                    <label for="v_name" class="floating-label">Vendor Name</label>
                                    <input type="hidden" name="vendor_id" class="form-control vendor_id" id="vendor_id"
                                        placeholder=" " />
                                </div>
                                {{-- <div class="form-group col-md-2 mb-2">
                                    <select name="purchase_id" class="form-control purchase_id" id="purchase_id" required>
                                        <option selected disabled>Select Invoice</option>
                                    </select>
                                    <label class="floating-label">Purchase Invoice</label>
                                </div> --}}
                                <div class="form-group col-md-2 mb-1">
                                    <input type="date" value="{{ date('Y-m-d') }}" name="date"
                                        class="form-control date" placeholder="" required />
                                    <label for="date" class="floating-label">Date</label>
                                </div>
                                <div class="form-group col-md-2 mb-1">
                                    <input type="text" name="voucher_no" class="form-control voucher_no" id="voucher_no"
                                        value="{{ $voucherNo ?? 'Auto Generate' }}" placeholder=" " readonly />
                                    <label for="voucher_no" class="floating-label">Voucher No</label>
                                </div>
                                <div class="form-group col-md-2 mt-2 mb-2">
                                    <input type="number" name="total_payment" class="form-control total_payment"
                                        id="total_payment" placeholder=" " readonly>
                                    <label for="total_payment" class="floating-label">Total Payment</label>
                                </div>
                                <div class="form-group col-md-2 mb-2">
                                    <input type="text" name="reference" class="form-control reference" placeholder=" " />
                                    <label for="reference" class="floating-label">Reference</label>
                                </div>
                                <div class="form-group col-md-2 mb-2">
                                    <input type="text" name="pay_to" class="form-control pay_to" placeholder=" " />
                                    <label for="pay_to" class="floating-label">Pay To..</label>
                                </div>
                                <div class="form-group col-md-2 mb-2">
                                    <select name="payment_method" class="form-control payment_method" placeholder=" "
                                        required>
                                        <option selected disabled>Select Payment Method</option>
                                        <option value="cash">Cash</option>
                                        <option value="bank">Bank</option>
                                        <option value="cheque">Cheque</option>
                                        <option value="mobile_bank">Mobile Banking</option>
                                        <option value="due">Due</option>
                                    </select>
                                    <label for="payment_method" class="floating-label">Payment Method</label>
                                </div>
                                <div class="form-group col-md-2 mb-2">
                                    <select name="debit_account_id" class="form-control debit_account_id" required>
                                        <option selected disabled>Select Debit Account</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}"
                                                data-ac_type="{{ strtolower($account->ac_type) }}">
                                                {{ $account->account_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label class="floating-label">Debit Account</label>
                                </div>
                                <div class="form-group col-md-2 mb-2">
                                    <select name="credit_account_id" class="form-control credit_account_id" required>
                                        <option selected disabled>Select Credit Account</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->account_name }}</option>
                                        @endforeach
                                    </select>
                                    <label class="floating-label">Credit Account</label>
                                </div>
                            </div>
                            <div class="form-group flex-grow-1 mt-2">
                                <textarea name="narration" class="form-control narration" rows="2" placeholder=" "></textarea>
                                <label for="narration" class="floating-label">Narration</label>
                            </div>
                        </div>
                        <!--start::vendor-->
                        <div id="infoContainer" class="border-0 shadow-sm">
                            <div class="card-header bg-info text-white mb-2">
                                <strong>Invoice Information</strong>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="60">Sl No.</th>
                                        <th>Invoice No.</th>
                                        <th>Purchase Amount</th>
                                        <th>Previous Paid</th>
                                        <th>Return Amount</th>
                                        <th>Due Amount</th>
                                        <th width="150">New Pay Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="invoiceTable">
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            Select Vendor
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6"></div>
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th width="45%">
                                                    Total Selected Invoice
                                                </th>
                                                <td>
                                                    <span id="selectedInvoice">
                                                        0
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>
                                                    Total Payment
                                                </th>
                                                <td>
                                                    <strong>
                                                        <span id="showTotalPayment">
                                                            0.00
                                                        </span>
                                                    </strong>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <hr>
                            <div class="text-right">
                                <button type="reset" class="btn btn-warning" id="resetBtn">
                                    <i class="fas fa-redo"></i>
                                    Reset
                                </button>
                                <button type="submit" class="btn btn-success" id="submitBtn" disabled>
                                    <i class="fas fa-save"></i>
                                    Save Vendor Payment
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <!--end::Form-->
            </div>
            <!--end::Quick Example-->
        </div>
        <!--end::Container-->
    </div>
@endsection
@push('script')
    <script>
        $(function() {
            let vendors = @json($vendors);

            //==========================
            // Vendor Autocomplete
            //==========================
            $("#v_name").autocomplete({
                minLength: 0,
                source: function(request, response) {
                    let result = $.grep(vendors, function(item) {
                        return item.v_name.toLowerCase().indexOf(request.term.toLowerCase()) !==
                            -1;
                    });
                    response($.map(result, function(item) {
                        return {
                            label: item.v_name,
                            value: item.v_name,
                            id: item.id
                        }
                    }));
                },
                select: function(event, ui) {
                    $("#vendor_id").val(ui.item.id);
                    loadPurchase(ui.item.id);
                }
            });

            //===================================
            // Vendor Change
            //===================================

            $("#v_name").on("keyup", function() {
                $("#vendor_id").val("");
                clearTable();
            });

            //===================================
            // Clear Table
            //===================================

            function clearTable() {
                $("#invoiceTable").html(`
            <tr>
                <td colspan="7" class="text-center">
                    Select Vendor
                </td>
            </tr>
        `);
                $("#selectedInvoice").text(0);
                $("#showTotalPayment").text("0.00");
                $("#total_payment").val(0);
                $("#submitBtn").prop("disabled", true);
            }

            //===================================
            // Load Invoice
            //===================================

            function loadPurchase(vendorId) {
                $("#invoiceTable").html(`
            <tr>
                <td colspan="7" class="text-center">
                    Loading...
                </td>
            </tr>
        `);
                $.ajax({
                    url: "{{ route('vendor-payment.get.purchase', '') }}/" + vendorId,
                    type: "GET",
                    dataType: "json",
                    success: function(res) {
                        if (res.length == 0) {
                            $("#invoiceTable").html(`
                        <tr>
                            <td colspan="7" class="text-center text-danger">
                                No Due Invoice Found
                            </td>
                        </tr>
                    `);
                            Swal.fire({
                                icon: 'warning',
                                title: 'No Due Invoice',
                                text: 'This vendor has no due invoice.'
                            });
                            return;
                        }
                        let html = "";
                        $.each(res, function(index, row) {
                            html += `
                    <tr>
                        <td>${index+1}</td>
                        <td>
                            ${row.invoice_no}
                            <input type="hidden"
                                name="purchase_id[]"
                                value="${row.id}">
                        </td>
                        <td class="text-end">
                            ${parseFloat(row.grand_total).toFixed(2)}
                        </td>
                        <td class="text-end">
                            ${parseFloat(row.already_paid).toFixed(2)}
                        </td>
                        <td class="text-end">
                            ${parseFloat(row.return_amt).toFixed(2)}
                        </td>
                        <td class="text-end dueAmount">
                            ${parseFloat(row.due_amt).toFixed(2)}
                        </td>
                        <td>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                max="${row.due_amt}"
                                class="form-control payAmount"
                                name="paid_amount[]">
                        </td>
                    </tr>
                    `;
                        });
                        $("#invoiceTable").html(html);
                    }
                });
            }

            //===================================
            // Payment Calculation
            //===================================

            $(document).on("keyup change", ".payAmount", function() {
                let total = 0;
                let count = 0;
                let error = false;
                $(".payAmount").each(function() {
                    let pay = parseFloat($(this).val()) || 0;
                    let due = parseFloat($(this).closest("tr").find(".dueAmount").text());
                    if (pay > 0) {
                        count++;
                    }
                    if (pay > due) {
                        error = true;
                        $(this).val("");
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid Payment',
                            text: 'Payment cannot exceed Due Amount.'
                        });
                    }
                    total += pay;
                });
                $("#selectedInvoice").text(count);
                $("#showTotalPayment").text(total.toFixed(2));
                $("#total_payment").val(total.toFixed(2));
                if (total > 0 && !error) {
                    $("#submitBtn").prop("disabled", false);
                } else {
                    $("#submitBtn").prop("disabled", true);
                }
            });

            // Reset

            $("#resetBtn").click(function() {
                clearTable();
                $("#v_name").val("");
                $("#vendor_id").val("");
            });
        });
    </script>
@endpush
