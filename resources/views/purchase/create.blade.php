@extends('backend.master')

@section('content')
    <!--begin::App Content Header-->
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0 mt-0">Purchase Entry</h3>
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
        </div>
        <!--end::Container-->
    </div>
    <!--end::App Content Header-->
    <!--begin::App Content-->
    <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Quick Example-->
            <div class="card card-primary card-outline mb-2">
                <!--begin::Header-->
                <div class="card-header">
                    <div class="card-title">Entry Purchase Item</div>
                </div>
                <!--end::Header-->
                <!--begin::Form-->
                <form action="{{ url('/purchase/store') }}" method="POST">
                    @csrf
                    <!--begin::Body-->
                    <div class="card-body">
                        <!--start::vendor-->
                        <div id="vendorContainer" class="border-0 shadow-sm ms-0">
                            <div
                                class="bg-success text-white d-flex justify-content-between align-vendors-center mb-3 px-1 py-1 rounded">
                                <h6 class="mb-0 ms-1">Vendor Details</h6>
                            </div>
                            <!-- ✅ Proper Row Structure -->
                            <div class="row g-2 align-vendor-end mb-1">
                                <div class="form-group col-md-5 mb-1">
                                    <input type="text" name="v_name" class="form-control v_name" placeholder=" "
                                        required />
                                    <label for="v_name" class="floating-label">Vendor Name</label>
                                </div>

                                <div class="form-group col-md-3 mb-1">
                                    <input type="text" name="phone" class="form-control phone" placeholder=" "
                                        required />
                                    <label for="phone" class="floating-label">Phone</label>
                                </div>
                                <div class="form-group col-md-4 mb-1">
                                    <input type="email" name="email" class="form-control email" placeholder=" " />
                                    <label for="email" class="floating-label">Email</label>
                                </div>
                                <div class="form-group col-md-8 mb-1">
                                    <textarea name="address" class="form-control address" rows="1" placeholder=" " required></textarea>
                                    <label for="address" class="floating-label">Address</label>
                                </div>
                                <div class="form-group col-md-2 mb-1">
                                    <input type="date" value="{{ date('Y-m-d') }}" name="date"
                                        class="form-control date" placeholder="" required />
                                    <label for="date" class="floating-label">Date</label>
                                </div>

                                <div class="form-group col-md-2 mb-1">
                                    <input type="text" name="invoice_no" class="form-control invoice_no" id="invoice_no"
                                        value="{{ $newPurchaseNo }}" placeholder=" " readonly />
                                    <label for="invoice_no" class="floating-label">Invoice Number</label>
                                </div>
                            </div>
                        </div>
                        <!--start::vendor-->
                        <div id="itemsContainer" class="border-0 shadow-sm">
                            <div
                                class="bg-success text-white d-flex justify-content-between align-items-center mb-3 px-1 py-1 rounded">
                                <h6 class="mb-0 ms-1">Item Details</h6>
                                <button type="button" id="addItem" class="btn btn-light btn-sm text-dark fw-bold">
                                    + Add Item
                                </button>
                            </div>

                            <!-- ✅ Proper Row Structure -->
                            <div class="item-row row g-2 align-items-end mb-2">
                                <div class="form-group col-sm-6 col-md-2 mb-1">
                                    <input type="text" name="item_name[]" class="form-control item_name" placeholder=" "
                                        required />
                                    <label for="item_name" class="floating-label">Item Name</label>
                                </div>
                                <div class="form-group col-sm-6 col-md-1 mb-1">
                                    <input type="text" name="item_code[]" class="form-control item_code" placeholder=" "
                                        readonly />
                                    <label for="item_code" class="floating-label">Item Code</label>
                                </div>
                                <div class="form-group col-sm-6 col-md-1 mb-1">
                                    <input type="text" name="cat_name[]" class="form-control cat_name"
                                        placeholder=" " required />
                                    <label for="cat_name" class="floating-label">Category</label>
                                </div>
                                <div class="form-group col-sm-6 col-md-1 mb-1">
                                    <input type="text" name="size[]" class="form-control size" placeholder=" " />
                                    <label for="size" class="floating-label">Pack Size</label>
                                </div>
                                <div class="form-group col-sm-6 col-md-1 mb-1">
                                    <input type="number" step="0.01" name="qty[]" class="form-control qty"
                                        placeholder=" " required />
                                    <label for="qty" class="floating-label">Quantity</label>
                                </div>
                                <div class="form-group col-sm-6 col-md-1 mb-1">
                                    <input type="number" step="0.01" name="unit_price[]"
                                        class="form-control unit_price" placeholder=" " required />
                                    <label for="unit_price" class="floating-label">Unit Price</label>
                                </div>
                                <div class="form-group col-sm-6 col-md-1 mb-1">
                                    <input type="number" step="0.01" name="price[]" class="form-control price"
                                        placeholder=" " required />
                                    <label for="price" class="floating-label">Price</label>
                                </div>
                                <div class="form-group col-sm-6 col-md-1 mb-1">
                                    <input type="number" step="0.01" name="vat_percent[]"
                                        class="form-control vat_percent" placeholder=" " />
                                    <label for="vat_percent" class="floating-label">Vat(%)</label>
                                </div>
                                <div class="form-group col-sm-6 col-md-1 mb-1">
                                    <input type="number" step="0.01" name="vat_amount[]"
                                        class="form-control vat_amount" placeholder=" " readonly />
                                    <label for="vat_amount" class="floating-label">Vat Amount</label>
                                </div>
                                <div class="form-group col-sm-6 col-md-2 d-flex align-items-end mb-1">
                                    <div class="w-100">
                                        <input type="number" step="0.01" name="total_price[]"
                                            class="form-control total_price" placeholder=" " readonly>
                                        <label for="total_price" class="floating-label">Total Amount</label>
                                    </div>
                                    <button type="button" class="btn btn-danger btn-sm ms-2 removeItem">
                                        ×
                                    </button>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row g-2">
                            <div class="form-group col-2 mt-2 mb-2">
                                <input type="number" name="dis_percent" class="form-control dis_percent"
                                    placeholder=" ">
                                <label for="dis_percent" class="floating-label">Discount(%)</label>
                            </div>
                            <div class="form-group col-2 mt-2 mb-2">
                                <input type="number" step="0.01" name="dis_amt" class="form-control dis_amt"
                                    placeholder=" " readonly>
                                <label for="dis_amt" class="floating-label">Discount Amount</label>
                            </div>
                            <div class="form-group col-2 mt-2 mb-2">
                                <input type="number" step="0.01" name="vat_amt" class="form-control vat_amt"
                                    placeholder=" " readonly>
                                <label for="vat_amt" class="floating-label">Total VAT Amount</label>
                            </div>
                            <div class="form-group col-2 mt-2 mb-2">
                                <input type="number" step="0.01" name="sub_total" class="form-control sub_total"
                                    placeholder=" " readonly>
                                <label for="sub_total" class="floating-label">Sub Total</label>
                            </div>
                            <div class="form-group col-2 mt-2 mb-2">
                                <input type="number" step="0.01" name="grand_total" class="form-control grand_total"
                                    placeholder=" " readonly>
                                <label for="grand_total" class="floating-label">Grand Total</label>
                            </div>
                            <div class="form-group col-2 mt-2 mb-2">
                                <input type="number" step="0.01" name="paid_amt" class="form-control paid_amt"
                                    placeholder=" ">
                                <label for="paid_amt" class="floating-label">Paid Amount</label>
                            </div>
                            <div class="form-group col-3 mt-2 mb-2">
                                <input type="number" step="0.01" name="due_amt" class="form-control due_amt"
                                    placeholder=" " readonly>
                                <label for="due_amt" class="floating-label">Due Amount</label>
                            </div>

                            <div class="form-group col-sm-6 col-md-3 mb-2">
                                <input type="text" name="reference_no" class="form-control reference_no"
                                    placeholder=" " />
                                <label for="reference_no" class="floating-label">Reference</label>
                            </div>

                            <div class="form-group col-sm-6 col-md-3 mb-2">
                                <input type="text" name="created_by" class="form-control created_by"
                                    placeholder=" " />
                                <label for="created_by" class="floating-label">Posting By</label>
                            </div>
                            <div class="form-group col-sm-6 col-md-3 mb-2">
                                <input type="text" name="pay_to" class="form-control pay_to" placeholder=" " />
                                <label for="pay_to" class="floating-label">Pay To..</label>
                            </div>
                            <div class="form-group col-sm-6 col-md-2 mb-2">
                                <select id="purchase_type" name="purchase_type" class="form-control" required>
                                    <option selected disabled>Select Purchase Type</option>
                                    <option value="expense">Expense Purchase</option>
                                    <option value="asset">Asset Purchase</option>
                                    <option value="inventory">Inventory/Stock Purchase</option>
                                    <option value="prepaid">Prepaid Purchase</option>
                                    <option value="cwip">Capital Work In Progress (CWIP)</option>
                                </select>
                                <label for="purchase_type" class="floating-label">Purchase Type</label>
                            </div>
                            <div class="form-group col-sm-6 col-md-2 mb-2">
                                @php
                                    $cats = $accounts->pluck('ac_cat')->unique();
                                @endphp

                                <select name="account_cat" class="form-control account_cat">
                                    <option selected disabled>Select Account Category</option>

                                    @foreach ($cats as $cat)
                                        <option value="{{ $cat }}">{{ $cat }}</option>
                                    @endforeach
                                </select>

                                <label class="floating-label">Account Category</label>
                            </div>
                            <div class="form-group col-sm-6 col-md-2 mb-2">
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
                            <div class="form-group col-sm-6 col-md-2 mb-2">
                                <select name="payment_status" class="form-control payment_status" placeholder=" "
                                    required>
                                    <option selected disabled>Select Payment Status</option>
                                    <option value="paid">Paid</option>
                                    <option value="unpaid">Unpaid</option>
                                    <option value="partial">Partial</option>
                                </select>
                                <label for="payment_status" class="floating-label">Payment Status</label>
                            </div>
                            <div class="form-group col-sm-6 col-md-2 mb-2">
                                <select name="debit_account_id" class="form-control debit_account_id" required>
                                    <option selected disabled>Select Debit Account</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}" data-ac_cat="{{ $account->ac_cat }}">
                                            {{ $account->account_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <label class="floating-label">Debit Account</label>
                            </div>
                            <div class="form-group col-sm-6 col-md-2 mb-2">
                                <select name="payment_account_id" class="form-control payment_account_id" required>
                                    <option selected disabled>Select Credit Account</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->account_name }}</option>
                                    @endforeach
                                </select>
                                <label class="floating-label">Credit Account</label>
                            </div>
                        </div>
                        <!--end::Body-->
                        <!--begin::Footer-->
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                        <!--end::Footer-->
                </form>
                <!--end::Form-->
            </div>
            <!--end::Quick Example-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::App Content-->
@endsection
@push('script')
    <script>
        $(document).ready(function() {

            const container = $('#itemsContainer');
            const addButton = $('#addItem');
            const subTotalInput = $('.sub_total');
            const totalVatInput = $('.vat_amount');
            const grandTotalInput = $('.grand_total');
            const dueInput = $('.due_amt');

            // ✅ Database থেকে আসা সর্বশেষ serial
            let itemSerial = {{ $lastSerial + 1 }};

            // ✅ Smart item code generator (Duplicate prefix এড়াতে)
            function generateItemCode(itemName, serial) {
                if (!itemName) return '';
                const words = itemName.trim().split(/\s+/);
                let prefix = '';

                if (words.length >= 2) {
                    prefix = words[0][0].toUpperCase() + words[1][0].toUpperCase();
                } else if (words[0].length >= 2) {
                    prefix = words[0].substring(0, 2).toUpperCase();
                } else {
                    prefix = words[0][0].toUpperCase() + 'X';
                }

                let existingPrefixes = [];
                $('.item_code').each(function() {
                    const val = $(this).val();
                    if (val && val.length >= 2) {
                        existingPrefixes.push(val.substring(0, 2));
                    }
                });

                if (existingPrefixes.includes(prefix)) {
                    const name = words.join('').toUpperCase();
                    for (let i = 1; i < name.length; i++) {
                        const newPrefix = prefix[0] + name[i];
                        if (!existingPrefixes.includes(newPrefix)) {
                            prefix = newPrefix;
                            break;
                        }
                    }
                }

                const code = prefix + String(serial).padStart(2, '0');
                return code;
            }

            // ✅ Subtotal হিসাব করা
            function calculateSubTotal() {
                let subTotal = 0;

                container.find('.price').each(function() {
                    subTotal += parseFloat($(this).val()) || 0;
                });

                $('.sub_total').val(subTotal.toFixed(2));

                calculateGrandTotal();
            }


            // ✅ totalVat হিসাব করা
            function calculateTotalVat() {
                let vatTotal = 0;

                container.find('.vat_amount').each(function() {
                    vatTotal += parseFloat($(this).val()) || 0;
                });

                $('.vat_amt').val(vatTotal.toFixed(2));

                calculateGrandTotal();
            }


            /* =========================
           DISCOUNT + GRAND TOTAL
        ==========================*/
            function calculateGrandTotal() {

                const subTotal = parseFloat($('.sub_total').val()) || 0;
                const vatAmt = parseFloat($('.vat_amt').val()) || 0;
                const disPercent = parseFloat($('.dis_percent').val()) || 0;
                const paidAmt = parseFloat($('.paid_amt').val()) || 0;

                // discount amount
                const disAmt = subTotal * (disPercent / 100);
                $('.dis_amt').val(disAmt.toFixed(2));

                // final total
                const grandTotal = subTotal - disAmt + vatAmt;
                $('.grand_total').val(grandTotal.toFixed(2));

                // due
                const due = grandTotal - paidAmt;
                $('.due_amt').val(due.toFixed(2));
            }


            // ✅ প্রতিটি row এর জন্য event attach
            function attachRowListeners(row) {
                    const qtyInput = row.find('.qty');
                    const priceInput = row.find('.unit_price');
                    const vatInput = row.find('.vat_percent');
                    const totalVatInput = row.find('.vat_amount');
                    const totalPriceInput = row.find('.price');
                    const totalAmountInput = row.find('.total_price');
                    const itemNameInput = row.find('.item_name');
                    const itemCodeInput = row.find('.item_code');
                    const catNameInput = row.find('.cat_name');
                    const sizeInput = row.find('.size');
                const priceField = row.find('.unit_price');

                // 🔹 Quantity বা Price পরিবর্তন হলে total হিসাব করো
                function recalc() {
                    const qty = parseFloat(qtyInput.val()) || 0;
                    const unitPrice = parseFloat(priceInput.val()) || 0;
                    const vatPercent = parseFloat(vatInput.val()) || 0;
                    const unitTotalPrice = parseFloat(totalPriceInput.val()) || 0;
                    const vatAmt = parseFloat(totalVatInput.val()) || 0;

                    // মূল দাম
                    const price = qty * unitPrice;
                    totalPriceInput.val(price.toFixed(2));

                    // এই item এর VAT হিসাব
                    const vatAmount = price * (vatPercent / 100);
                    totalVatInput.val(vatAmount.toFixed(2));

                    const totalPrice = price + vatAmount;
                    totalAmountInput.val(totalPrice.toFixed(2));

                    // আপডেট totals
                    calculateSubTotal();
                    calculateTotalVat();
                }

                qtyInput.on('input', recalc);
                priceInput.on('input', recalc);
                vatInput.on('input', recalc);

                // 🔹 Item Name Autocomplete + Dynamic Code Generation
                if (!itemNameInput.data('ui-autocomplete')) {
                    itemNameInput.autocomplete({
                        source: "{{ route('item.search') }}",
                        minLength: 1,
                        select: function(event, ui) {
                            if (ui.item && ui.item.id) {
                                row.find('.item_name').val(ui.item.value);
                                row.find('.item_code').val(ui.item.item_code);
                                row.find('.cat_name').val(ui.item.cat_name);
                                row.find('.size').val(ui.item.size);
                                row.find('.unit_price').val(ui.item.unit_price);
                                row.data('selected', true);
                                recalc(); // দাম বসার সাথে সাথে হিসাব আপডেট
                            }
                        },
                        response: function(event, ui) {
                            if (ui.content.length === 0) {
                                const val = $(this).val().trim();
                                if (val) {
                                    const newCode = generateItemCode(val, itemSerial);
                                    row.find('.item_code').val(newCode);
                                }
                            }
                        },
                        open: function() {
                            row.data('selected', false);
                        }
                    });

                    // 🔹 নতুন নাম লিখলে পুরনো ডেটা clear করে দাও
                    itemNameInput.on('input', function() {
                        const val = $(this).val().trim();
                        if (!row.data('selected')) {
                            itemCodeInput.val('');
                            catNameInput.val('');
                            sizeInput.val('');
                            priceField.val('');
                        }
                        if (val) {
                            const code = generateItemCode(val, itemSerial);
                            itemCodeInput.val(code);
                        } else {
                            itemCodeInput.val('');
                        }
                    });
                }
            }

            // ✅ প্রথম row এ listener attach করা
            attachRowListeners(container.find('.item-row').first());

            // ✅ নতুন row add করা
            addButton.on('click', function() {
                const firstRow = container.find('.item-row').first();
                const newRow = firstRow.clone();

                newRow.find('input').val('');
                newRow.find('select').prop('selectedIndex', 0);
                newRow.find('[id]').remove();

                container.append(newRow);
                itemSerial++;
                attachRowListeners(newRow);
            });

            // ✅ Row remove
            container.on('click', '.removeItem', function() {
                const rows = container.find('.item-row');
                if (rows.length > 1) {
                    $(this).closest('.item-row').remove();
                    calculateSubTotal();
                } else {
                    alert('At least one item row is required!');
                }
            });

            // ✅ Discount বা VAT পরিবর্তন হলে হিসাব চালু হবে
            $('.dis_percent, .vat, .paid_amt').on('input', function() {
                calculateGrandTotal();
            });
        });
    </script>

    <script>
        $('#purchase_type').on('change', function() {

            let type = $(this).val();
            let accountSelect = $('select[name="account_cat"]');

            accountSelect.html('<option selected disabled>Loading...</option>');

            $.ajax({
                url: `/get-accounts-by-purchase-type/${type}`,
                type: 'GET',
                success: function(data) {

                    accountSelect.empty()
                        .append('<option selected disabled>Select Account Category</option>');

                    data.forEach(acc => {
                        accountSelect.append(
                            `<option value="${acc.ac_cat}">${acc.ac_cat}</option>`
                        );
                    });

                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const accountCat = document.querySelector('.account_cat');
            const debitAccount = document.querySelector('.debit_account_id');

            accountCat.addEventListener('change', function() {
                const selectedCat = this.value;

                // reset debit dropdown
                debitAccount.value = 'Select Debit Account';

                Array.from(debitAccount.options).forEach(option => {
                    if (!option.dataset.ac_cat) return; // skip placeholder

                    if (option.dataset.ac_cat === selectedCat) {
                        option.style.display = 'block';
                    } else {
                        option.style.display = 'none';
                    }
                });
            });
        });
    </script>

    <script>
        $('.payment_status').on('change', function() {

            let status = $(this).val(); // paid / unpaid / partial
            let accountSelect = $('.payment_account_id');

            accountSelect
                .prop('disabled', true)
                .html('<option selected disabled>Loading...</option>');

            $.ajax({
                url: `/get-accounts-by-status/${status}`,
                type: 'GET',
                success: function(data) {

                    accountSelect
                        .prop('disabled', false)
                        .empty()
                        .append('<option selected disabled>Select Credit Account</option>');

                    data.forEach(acc => {
                        accountSelect.append(
                            `<option value="${acc.id}">${acc.account_name}</option>`
                        );
                    });
                },
                error: function() {
                    accountSelect.html('<option selected disabled>Error loading accounts</option>');
                }
            });

        });
    </script>
@endpush
