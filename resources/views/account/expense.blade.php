@extends('backend.master')
@push('style')
    <style>
        .voucher-box {
            background-color: #f8f4dc;
            font-family: "Poppins", "Segoe UI", sans-serif;
            color: #000;
            border: 1px solid #004d40;
            background: #fffbea;
            padding: 5px;
            margin: 1px auto;
            max-width: 100%;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.15);
            border-radius: 6px;
        }

        .voucher-header {
            background-color: #00695c;
            color: white;
            padding: 5px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
        }

        table input {
            padding: 5px;
            border: none;
            background: transparent;
            width: 100%;
            text-align: center;
        }

        .table-bordered td,
        .table-bordered th {
            vertical-align: middle;
        }

        .calculet-input {
            border: none;
            background: transparent;
            width: 15%;
            text-align: left;
        }

        .footer-box {
            border-top: 1px solid #006151;
            padding-top: 5px;
        }

        .form-control {
            padding: 6px 4px;
            border: none;
            border-bottom: 1px dotted #555;
            background: transparent;
            display: inline;
            width: 100%;
        }

        .btn {
            border-radius: 0;
        }

        .accept-box {
            text-align: right;
            margin-top: 30px;
        }

        .add-btn {
            background-color: #d9f4f1;
            color: rgb(37, 37, 237);
            border: none;
            padding: 2px 4px;
            border-radius: 4px;
        }

        .add-btn:hover {
            background-color: #0ad9b7;
        }
    </style>
@endpush
@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="voucher-box">
                <div class="voucher-header">
                    Accounting Voucher Creation - Expense
                </div>
                <form method="POST" action="{{ url('/expense/store') }}">
                    @csrf
                    <div class="row g-2 align-accounts-end mt-2">
                        <div class="form-group col-sm-6 col-md-2 mb-2">
                            <input type="text" name="voucher_no" value="{{ $nextNo }}"
                                class="form-control voucher_no" placeholder=" " required />
                            <label for="voucher_no" class="floating-label">Voucher No</label>
                        </div>
                        <div class="form-group col-md-2 mb-2">
                            <input type="date" name="date" class="form-control date" placeholder="" required />
                            <label for="date" class="floating-label">Date</label>
                        </div>
                        <div class="form-group col-sm-6 col-md-2 mb-2">
                            <select name="expense_account_id" class="form-control expense_account_id" required>
                                <option selected disabled>Select Expense Account</option>
                                @foreach ($accounts->where('ac_type', 'Expense') as $account)
                                    <option value="{{ $account->id }}">{{ $account->account_name }}</option>
                                @endforeach
                            </select>
                            <label class="floating-label">Expense Account</label>
                        </div>
                        <div class="form-group col-sm-6 col-md-2 mb-2">
                            <select name="payment_account_id" class="form-control payment_account_id" required>
                                <option selected disabled>Select Payment Account</option>
                                @foreach ($accounts->where('ac_type', 'Asset') as $account)
                                    <option value="{{ $account->id }}">{{ $account->account_name }}</option>
                                @endforeach
                            </select>
                            <label class="floating-label">Payment Account</label>
                        </div>
                        <div class="form-group col-sm-6 col-md-2 mb-2">
                            <input type="text" name="reference_no" class="form-control reference_no" placeholder=" " />
                            <label for="reference_no" class="floating-label">Reference No</label>
                        </div>
                        <div class="form-group col-sm-6 col-md-2 mb-2">
                            <select name="payment_method" class="form-control payment_method" placeholder=" " required>
                                <option selected disabled>Select Payment Method</option>
                                <option value="cash">Cash</option>
                                <option value="bank">Bank</option>
                                <option value="cheque">Cheque</option>
                                <option value="mobile_bank">Mobile Banking</option>
                                <option value="due">Due</option>
                            </select>
                            <label for="unit" class="floating-label">Payment Method</label>
                        </div>
                        <div class="form-group col-sm-6 col-md-2 mb-2">
                            <select name="payment_status" class="form-control payment_status" placeholder=" " required>
                                <option selected disabled>Select Payment Status</option>
                                <option value="paid">Paid</option>
                                <option value="unpaid">Unpaid</option>
                                <option value="partial">Partial</option>
                            </select>
                            <label for="unit" class="floating-label">Payment Status</label>
                        </div>
                        <div class="form-group col-sm-6 col-md-3 mb-2">
                            <input type="text" name="created_by" class="form-control created_by" placeholder=" "
                                required />
                            <label for="created_by" class="floating-label">Posting By</label>
                        </div>
                        <div class="form-group col-sm-6 col-md-3 mb-2">
                            <input type="text" name="pay_to" class="form-control pay_to" placeholder=" " required />
                            <label for="pay_to" class="floating-label">Pay To..</label>
                        </div>
                        <div class="form-group col-sm-6 col-md-2 mb-2">
                            <input type="text" name="branch_id" class="form-control branch_id" placeholder=" "/>
                            <label for="branch_id" class="floating-label">Branch Name</label>
                        </div>
                    </div>

                    <hr>

                    <div class="voucher-header d-flex justify-content-between align-items-center mb-2">
                        <h6 class="m-0"><b>Item Details</b></h6>
                        <button id="addRow" class="add-btn">+ Add Item</button>
                    </div>

                    <table class="table table-bordered table-sm text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Particulars</th>
                                <th>Quantity</th>
                                <th>Rate</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="itemTable">
                            <tr>
                                <td><input type="text" value="" name="particulars[]" class="item-name"></td>
                                <td><input type="number" value="" name="qty[]" class="qty"></td>
                                <td><input type="number" value="" name="rate[]" class="rate"></td>
                                <td><input type="number" value="" name="amount[]" class="amount" readonly></td>
                                <td><button class="btn btn-sm btn-danger remove">X</button></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="footer-box text-end">
                        <p><b>Sub Total: </b><input type="number" value="" name="sub_total"
                                class="fw-bold calculet-input sub_total" id="subtotal"></p>
                        <p><b>Output VAT <input type="number" value="" name="tax_rate" style="width: 4%"
                                    class="tax_rate" id="tax_rate">%: </b>
                            <input type="number" value="" name="tax_amount" class="calculet-input tax_amount"
                                id="vat">
                        </p>
                        <hr>
                        <h6><b>Grand Total: </b><input type="number" value="" name="total_amount"
                                class="fw-bold calculet-input total_amount" id="grandtotal"></h6>
                        <h6>Paid Amount: <input type="number" step="0.00" value="" name="paid_amount"
                                class="calculet-input paid_amount" id="paidamt"></h6>
                        <h6><b>Due Amount: </b><input type="number" value="" name="due_amount" id="dueamt"
                                class="fw-bold calculet-input due_amount"></h6>
                    </div>

                    <div class="accept-box">
                        <button class="btn btn-success px-4">Save</button>
                        <button class="btn btn-danger px-4">Cancel</button>
                        <button class="btn btn-primary px-4" id="savePDF">💾 PDF</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $('#addRow').on('click', function(e) {
            e.preventDefault();
            let newRow = `
        <tr>
            <td><input type="text" name="particulars[]" class="item-name"></td>
            <td><input type="number" name="qty[]" class="qty"></td>
            <td><input type="number" name="rate[]" class="rate"></td>
            <td><input type="number" name="amt[]" class="amount" readonly></td>
            <td><button class="btn btn-sm btn-danger remove">X</button></td>
        </tr>`;
            $('#itemTable').append(newRow);
        });

        $(document).on('click', '.remove', function(e) {
            e.preventDefault();
            $(this).closest('tr').remove();
            calcTotal();
        });
    </script>
    <script>
        $(document).on('input', '.qty, .rate, .tax_rate, .paid_amount', function() {
            calcTotal();
        });

        function calcTotal() {
            let subtotal = 0;
            $('.amount').each(function() {
                subtotal += parseFloat($(this).val()) || 0;
            });

            // Subtotal দেখানো
            $('#subtotal').val(subtotal.toFixed(2));

            // VAT হিসাব
            let vatRate = parseFloat($('#tax_rate').val()) || 0;
            let vatAmt = subtotal * vatRate / 100;
            $('#vat').val(vatAmt.toFixed(2));

            // Grand Total
            let grand = subtotal + vatAmt;
            $('#grandtotal').val(grand.toFixed(2));

            // Paid / Due হিসাব
            let paid = parseFloat($('#paidamt').val()) || 0;
            let due = grand - paid;
            $('#dueamt').val(due.toFixed(2));
        }

        $(document).on('input', '.qty, .rate', function() {
            let row = $(this).closest('tr');
            let qty = parseFloat(row.find('.qty').val()) || 0;
            let rate = parseFloat(row.find('.rate').val()) || 0;
            row.find('.amount').val((qty * rate).toFixed(2));
            calcTotal();
        });
    </script>
@endpush
