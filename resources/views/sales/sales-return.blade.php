@extends('backend.master')

@section('content')
    <!--begin::App Content Header-->
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row bg-info opacity-50 rounded">
                <div class="col-sm-4">
                    <h4 class="mb-0 mt-0">Sales Entry</h4>
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
        </div>
    </div>
    <!--end::App Content Header-->


    <!--begin::App Content-->
    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <!--begin::Form-->
                <form id="salesForm" action="{{ route('sales.store') }}" method="POST">
                    @csrf
                    <!--begin::Body-->
                    <div class="card-body">
                        <!-- Hidden URLs -->
                        <input type="hidden" id="itemSearchUrl" value="{{ route('search.item') }}">
                        <input type="hidden" id="customerSearchUrl" value="{{ route('search.customer') }}">

                        <!-- CUSTOMER DETAILS -->
                        <div id="customerContainer" class="border-0 shadow-sm ms-0">
                            <div
                                class="bg-success text-white d-flex justify-content-between align-items-center mb-3 px-1 py-1 rounded">
                                <h6 class="mb-0 ms-1">
                                    Customer Details
                                </h6>
                            </div>

                            <div class="row g-2 align-items-end mb-1">
                                <div class="form-group col-md-5 mb-1 position-relative">
                                    <input type="text" name="c_name" class="form-control c_name" id="c_name"
                                        placeholder=" " required>
                                    <label for="c_name" class="floating-label">Customer Name</label>
                                    <input type="hidden" name="customer_id" class="customer_id" id="customer_id">
                                </div>
                                <div class="form-group col-md-3 mb-1">
                                    <input type="text" name="phone" class="form-control phone" id="phone"
                                        placeholder=" " required>
                                    <label for="phone" class="floating-label">Phone</label>
                                </div>
                                <div class="form-group col-md-4 mb-1">
                                    <input type="email" name="email" class="form-control email" id="email"
                                        placeholder=" ">
                                    <label for="email" class="floating-label">Email</label>
                                </div>
                                <div class="form-group col-md-8 mb-1">
                                    <textarea name="address" class="form-control address" id="address" rows="1" placeholder=" " required></textarea>
                                    <label for="address" class="floating-label">Address</label>
                                </div>
                                <div class="form-group col-md-2 mb-1">
                                    <input type="date" name="date" class="form-control date" id="date"
                                        value="{{ date('Y-m-d') }}" required>
                                    <label for="date" class="floating-label">Date</label>
                                </div>
                                <div class="form-group col-md-2 mb-1">
                                    <input type="text" name="invoice_no" class="form-control invoice_no"
                                        id="invoice_no"value="{{ $newReturnNo }}" placeholder=" " required>
                                    <label for="invoice_no" class="floating-label">Invoice Number</label>
                                </div>
                            </div>
                        </div>

                        <!-- ITEM DETAILS -->
                        <div id="salesItemContainer" class="border-0 shadow-sm mt-2">
                            <div
                                class="bg-success text-white d-flex justify-content-between align-items-center mb-3 px-1 py-1 rounded">
                                <h6 class="mb-0 ms-1">Item Details</h6>
                                <button type="button" id="addItem" class="btn btn-light btn-sm text-dark fw-bold">
                                    + Add Item
                                </button>
                            </div>
                            <div class="sales-item-row row g-2 align-items-end mb-2">
                                <div class="form-group col-md-2 mb-1">
                                    <input type="text" name="item_name[]" class="form-control item_name"
                                        placeholder=" " required>
                                    <label class="floating-label">Item Name</label>
                                    <input type="hidden" name="item_id[]" class="item_id">
                                </div>
                                <div class="form-group col-md-1 mb-1">
                                    <input type="text" name="item_code[]" class="form-control item_code"
                                        placeholder=" " readonly>
                                    <label class="floating-label">Item Code</label>
                                </div>
                                <div class="form-group col-md-1 mb-1">
                                    <input type="text" name="cat_name[]" class="form-control cat_name"
                                        placeholder=" " readonly>
                                    <label class="floating-label">Category</label>
                                </div>
                                <div class="form-group col-md-1 mb-1">
                                    <input type="text" name="size[]" class="form-control size" placeholder=" "
                                        readonly>
                                    <label class="floating-label">Pack Size</label>
                                </div>
                                <div class="form-group col-md-1 mb-1">
                                    <input type="number" step="0.01" name="qty[]" class="form-control qty"
                                        placeholder=" " required>
                                    <label class="floating-label">Quantity</label>
                                </div>
                                <div class="form-group col-md-1 mb-1">
                                    <input type="number" step="0.01" name="sales_price[]"
                                        class="form-control sales_price" placeholder=" " required>
                                    <label class="floating-label">Sales Price</label>
                                </div>
                                <div class="form-group col-md-1 mb-1">
                                    <input type="number" step="0.01" name="price[]" class="form-control price"
                                        placeholder=" " readonly>
                                    <label class="floating-label">Price</label>
                                </div>
                                <div class="form-group col-md-1 mb-1">
                                    <input type="number" step="0.01" name="item_vat_percent[]"
                                        class="form-control item_vat_percent" placeholder=" ">
                                    <label class="floating-label">VAT(%)</label>
                                </div>
                                <div class="form-group col-md-1 mb-1">
                                    <input type="number" step="0.01" name="item_vat_amt[]"
                                        class="form-control item_vat_amt" placeholder=" ">
                                    <label class="floating-label">VAT Amount</label>
                                </div>
                                <div class="form-group col-md-2 d-flex align-items-end mb-1">
                                    <div class="w-100">
                                        <input type="number" step="0.01" name="total_price[]"
                                            class="form-control total_price" placeholder=" " readonly>
                                        <label class="floating-label">Total Amount</label>
                                    </div>
                                    <button type="button" class="btn btn-danger btn-sm ms-2 removeSalesItem">
                                        ×
                                    </button>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <!-- TOTAL SECTION -->
                        <div class="row g-2">
                            <div class="form-group col-md-1 mt-2 mb-2">
                                <input type="number" step="0.001" name="dis_percent" class="form-control dis_percent"
                                    id="dis_percent" placeholder=" ">
                                <label class="floating-label">Discount(%)</label>
                            </div>
                            <div class="form-group col-md-1 mt-2 mb-2">
                                <input type="number" step="0.01" name="dis_amt"
                                    class="form-control dis_amt"id="dis_amt" placeholder=" " readonly>
                                <label class="floating-label">Discount Amt</label>
                            </div>
                            <div class="form-group col-md-2 mt-2 mb-2">
                                <input type="number" step="0.01" name="sub_total"
                                    class="form-control sub_total"id="sub_total" placeholder=" " readonly>
                                <label class="floating-label">Sub Total</label>
                            </div>
                            <div class="form-group col-md-2 mt-2 mb-2">
                                <input type="number" step="0.01" name="vat_amt" class="form-control vat_amt"
                                    id="vat_amt" placeholder=" " readonly>
                                <label class="floating-label">VAT Amount</label>
                            </div>
                            <div class="form-group col-md-2 mt-2 mb-2">
                                <input type="number" step="0.01" name="grand_total" class="form-control grand_total"
                                    id="grand_total" placeholder=" " readonly>
                                <label class="floating-label">Grand Total</label>
                            </div>
                            <div class="form-group col-md-2 mt-2 mb-2">
                                <input type="number" step="0.01" name="paid_amt" class="form-control paid_amt"
                                    id="paid_amt" placeholder=" ">
                                <label class="floating-label">Received Amount</label>
                            </div>
                            <div class="form-group col-md-2 mt-2 mb-2">
                                <input type="number" step="0.01" name="due_amt" class="form-control due_amt"
                                    id="due_amt" placeholder=" " readonly>
                                <label class="floating-label">Due</label>
                            </div>
                            <div class="form-group col-md-2 mb-2">
                                <input type="text" name="reference" class="form-control reference" placeholder=" ">
                                <label class="floating-label">Reference</label>
                            </div>
                            <div class="form-group col-md-2 mb-2">
                                <input type="text" name="pay_receive" class="form-control pay_receive"
                                    placeholder=" ">
                                <label class="floating-label">Pay Received</label>
                            </div>
                            <div class="form-group col-md-2 mb-2">
                                <select name="payment_method" class="form-control payment_method" required>
                                    <option selected disabled>Select Payment Method</option>
                                    <option value="cash">Cash</option>
                                    <option value="bank">Bank</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="mobile_bank">Mobile Banking</option>
                                    <option value="due">Due</option>
                                </select>
                                <label class="floating-label">Payment Method</label>
                            </div>
                            <div class="form-group col-md-2 mb-2">
                                <select name="payment_status" class="form-control payment_status" required>
                                    <option selected disabled>Select Payment Status</option>
                                    <option value="paid">Paid</option>
                                    <option value="unpaid">Unpaid</option>
                                    <option value="partial">Partial</option>
                                </select>
                                <label class="floating-label">Payment Status</label>
                            </div>

                            <div class="form-group col-md-2 mb-2">
                                <select name="payment_account_id" class="form-control payment_account_id">
                                    <option value="">Select Payment Account</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}">
                                            {{ $account->account_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <label class="floating-label">Payment Account</label>
                            </div>
                        </div>

                        <!-- FOOTER -->
                        <div class="card-footer d-flex align-items-end justify-content-between gap-3 p-0 m-0">
                            <div class="form-group flex-grow-1 mt-2">
                                <textarea name="narration" class="form-control narration" rows="1" placeholder=" " required></textarea>
                                <label class="floating-label">Narration</label>
                            </div>
                            <button type="submit" class="btn btn-primary ms-auto">
                                Save
                            </button>
                        </div>
                    </div>
                    <!--end::Body-->
                </form>
                <!--end::Form-->
            </div>
        </div>
    </div>
    <!--end::App Content-->
@endsection


@push('script')
    <script>
        // public/backend/dist/js/Sales-calculations.js
        $(document).ready(function() {

            console.log('=== Sales Calculator Loading ===');
            console.log('jQuery loaded:', typeof $ !== 'undefined');
            console.log('jQuery UI Autocomplete:', typeof $.fn.autocomplete);
            console.log('Container exists:', $('#salesItemContainer').length > 0);

            // Check if container exists
            if (!$('#salesItemContainer').length) {
                console.log('No sales form on this page, skipping...');
                return;
            }

            // Check if autocomplete is available
            if (typeof $.fn.autocomplete === 'undefined') {
                console.error('ERROR: jQuery UI Autocomplete not loaded!');
                alert('jQuery UI Autocomplete is required but not loaded.');
                return;
            }

            console.log('Initializing Sales Calculator...');

            // ============ VARIABLES ============
            const container = $('#salesItemContainer');
            const searchUrl = $('#itemSearchUrl').val() || '/search/item';
            const customerSearchUrl = $('#customerSearchUrl').val() || '/search/customer';
            let salesItemSerial = parseInt($('#salesItemSerial').val()) || 1;

            console.log('Search URL:', searchUrl);
            console.log('Customer Search URL:', customerSearchUrl);
            console.log('Item Serial:', salesItemSerial);

            // ============ ITEM CODE GENERATOR ============
            function generateItemCode(salesItemName) {
                if (!salesItemName) return '';

                const words = salesItemName.trim().split(/\s+/);
                let prefix = '';

                if (words.length >= 2) {
                    prefix = (words[0][0] + words[1][0]).toUpperCase();
                } else if (words[0].length >= 2) {
                    prefix = words[0].substring(0, 2).toUpperCase();
                } else {
                    prefix = words[0][0].toUpperCase() + 'X';
                }

                return prefix + String(salesItemSerial).padStart(2, '0');
            }

            // ============ ROW CALCULATIONS ============
            function recalcRow(row) {
                const qty = parseFloat(row.find('.qty').val()) || 0;
                const salesPrice = parseFloat(row.find('.sales_price').val()) || 0;
                const vatPercent = parseFloat(row.find('.item_vat_percent').val()) || 0;

                const price = qty * salesPrice;
                const vatAmount = price * (vatPercent / 100);
                const totalPrice = price + vatAmount;

                row.find('.price').val(price.toFixed(2));
                row.find('.item_vat_amt').val(vatAmount.toFixed(2));
                row.find('.total_price').val(totalPrice.toFixed(2));
            }

            function calculateTotals() {
                let subTotal = 0;
                let totalVat = 0;

                container.find('.price').each(function() {
                    subTotal += parseFloat($(this).val()) || 0;
                });

                container.find('.item_vat_amt').each(function() {
                    totalVat += parseFloat($(this).val()) || 0;
                });

                const disPercent = parseFloat($('.dis_percent').val()) || 0;
                const disAmt = subTotal * (disPercent / 100);
                const paidAmt = parseFloat($('.paid_amt').val()) || 0;
                const grandTotal = subTotal - disAmt + totalVat;
                const dueAmt = Math.max(0, grandTotal - paidAmt);

                $('.sub_total').val(subTotal.toFixed(2));
                $('.vat_amt').val(totalVat.toFixed(2));
                $('.dis_amt').val(disAmt.toFixed(2));
                $('.grand_total').val(grandTotal.toFixed(2));
                $('.due_amt').val(dueAmt.toFixed(2));
            }

            // ============ ITEM AUTOCOMPLETE ============
            function setupItemAutocomplete(row) {
                const salesItemNameInput = row.find('.item_name');

                console.log('Setting up autocomplete for:', salesItemNameInput.attr('name'));

                // Destroy existing autocomplete
                if (salesItemNameInput.hasClass('ui-autocomplete-input')) {
                    console.log('Destroying existing autocomplete');
                    salesItemNameInput.autocomplete('destroy');
                }

                salesItemNameInput.autocomplete({
                    source: function(request, response) {
                        console.log('Searching:', request.term);

                        $.ajax({
                            url: searchUrl,
                            dataType: 'json',
                            data: {
                                term: request.term
                            },
                            success: function(data) {
                                console.log('Results:', data.length, 'items found');
                                response(data);
                            },
                            error: function(xhr, status, error) {
                                console.error('Search failed:', status, error);
                                response([]);
                            }
                        });
                    },
                    minLength: 1,
                    select: function(event, ui) {
                        console.log('✅ Item selected:', ui.item);

                        if (!ui.item) return false;
                        salesItemNameInput.val(ui.item.value || ui.item.label);
                        // Fill all fields
                        row.find('.item_id').val(ui.item.item_id || ui.item.id || '');
                        row.find('.item_code').val(ui.item.item_code || '');
                        row.find('.cat_name').val(ui.item.cat_name || '');
                        row.find('.size').val(ui.item.size || '');
                        row.find('.sales_price').val(ui.item.sales_price || '');

                        row.data('selected', true);

                        recalcRow(row);
                        calculateTotals();

                        return false;
                    },
                    response: function(event, ui) {
                        if (ui.content.length === 0) {
                            const val = $(this).val().trim();
                            if (val) {
                                const code = generateItemCode(val);
                                row.find('.item_code').val(code);
                                console.log('Generated code for new item:', code);
                            }
                        }
                    },
                    open: function() {
                        row.data('selected', false);
                    }
                });

                // Manual input
                salesItemNameInput.off('input').on('input', function() {
                    if (!row.data('selected')) {
                        row.find('.item_id').val('');
                        row.find('.cat_name').val('');
                        row.find('.size').val('');
                        row.find('.sales_price').val('');
                    }

                    const val = $(this).val().trim();
                    row.find('.item_code').val(val ? generateItemCode(val) : '');
                });
            }

            // ============ SETUP ALL ROWS ============
            const rows = container.find('.sales-item-row');
            console.log('Found', rows.length, 'sales item rows');

            rows.each(function(index) {
                console.log('Setting up row', index + 1);
                setupItemAutocomplete($(this));

                $(this).find('.qty, .sales_price, .item_vat_percent').on('input', function() {
                    recalcRow($(this).closest('.sales-item-row'));
                    calculateTotals();
                });
            });
            // ADD ITEM
            const addItemButton = document.getElementById('addItem');
            if (addItemButton) {
                addItemButton.onclick = null;
                addItemButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    console.log('ADD ITEM CLICKED');
                    const firstRow = container.find('.sales-item-row').first();
                    if (!firstRow.length) {
                        console.error('No sales item row found!');
                        return;
                    }

                    const newRow = firstRow.clone(false, false);
                    newRow.find('input').each(function() {
                        $(this).val('');
                    });

                    newRow.find('select').each(function() {
                        $(this).prop('selectedIndex', 0);
                    });

                    const itemInput = newRow.find('.item_name');
                    if (itemInput.hasClass('ui-autocomplete-input')) {
                        try {
                            itemInput.autocomplete('destroy');
                        } catch (error) {
                            console.log('Autocomplete destroy skipped');
                        }
                    }
                    itemInput
                        .removeClass('ui-autocomplete-input')
                        .removeAttr('autocomplete')
                        .removeAttr('aria-autocomplete')
                        .removeAttr('aria-controls')
                        .removeAttr('aria-haspopup');

                    newRow.find('[id]').removeAttr('id');
                    newRow.removeData('selected');
                    container.append(newRow);
                    console.log(
                        'Rows after add:',
                        container.find('.sales-item-row').length
                    );
                    salesItemSerial++;

                    setupItemAutocomplete(newRow);
                    newRow.find('.qty, .sales_price, .item_vat_percent')
                        .off('input.salesCalculation')
                        .on('input.salesCalculation', function() {
                            const currentRow = $(this)
                                .closest('.sales-item-row');
                            recalcRow(currentRow);
                            calculateTotals();
                        });

                    newRow.find('.item_name').trigger('focus');
                });
            }

            container
                .off('click.salesRemoveItem', '.removeSalesItem')
                .on('click.salesRemoveItem', '.removeSalesItem', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const rows = container.find('.sales-item-row');
                    if (rows.length <= 1) {
                        return;
                    }
                    $(this)
                        .closest('.sales-item-row')
                        .remove();
                    calculateTotals();
                });


            // ============ DISCOUNT & PAID ============
            $('.dis_percent, .paid_amt').on('input', calculateTotals);

            // ============ Customer AUTOCOMPLETE ============
            if ($('.c_name').length) {

                // Destroy existing autocomplete if any
                if ($('.c_name').hasClass('ui-autocomplete-input')) {
                    $('.c_name').autocomplete('destroy');
                }
                $('.c_name').autocomplete({
                    source: function(request, response) {
                        $.ajax({
                            url: '/search/customer',
                            dataType: 'json',
                            data: {
                                term: request.term
                            },
                            success: function(data) {
                                console.log('customers found:', data.length);
                                response(data);
                            },
                            error: function(xhr, status, error) {
                                console.error('customer search error:', error);
                                response([]);
                            }
                        });
                    },
                    minLength: 1,
                    select: function(event, ui) {
                        console.log('Selected:', ui.item);

                        // ✅ CRITICAL: Set the input value using $(this)
                        $(this).val(ui.item.value);

                        // Set customer ID
                        $('.customer_id').val(ui.item.customer_id || ui.item.id);

                        // Fill other customer fields
                        $('.phone').val(ui.item.phone || '');
                        $('.email').val(ui.item.email || '');
                        $('.address').val(ui.item.address || '');

                        return false;
                    }
                });

                // Reset customer_id when user types manually
                $('.c_name').on('input', function() {
                    $('.customer_id').val('');
                });
            }

            // ==========================================
            // PAYMENT ACCOUNT CONTROL
            // ==========================================

            function updatePaymentAccount() {

                const status = $('.payment_status').val();
                const paymentMethod = $('.payment_method').val();
                const paymentAccount = $('.payment_account_id');

                if (!paymentAccount.length) {
                    return;
                }

                // ==========================================
                // UNPAID / DUE
                // ==========================================
                if (status === 'unpaid' || paymentMethod === 'due') {

                    // Clear selected account
                    paymentAccount.val('');

                    // Disable payment account
                    paymentAccount
                        .prop('disabled', true)
                        .prop('required', false);

                    console.log('Payment Account disabled: Unpaid/Due');

                    return;
                }

                // ==========================================
                // PAID / PARTIAL
                // ==========================================
                if (status === 'paid' || status === 'partial') {

                    paymentAccount
                        .prop('disabled', false)
                        .prop('required', true);

                    console.log('Payment Account enabled:', status);

                    return;
                }

                // ==========================================
                // DEFAULT
                // ==========================================
                paymentAccount
                    .val('')
                    .prop('disabled', true)
                    .prop('required', false);
            }


            // ==========================================
            // PAYMENT STATUS CHANGE
            // ==========================================

            $('.payment_status').on('change', function() {

                const status = $(this).val();

                console.log('Payment Status changed:', status);

                // If Unpaid, automatically select Due
                if (status === 'unpaid') {

                    $('.payment_method')
                        .val('due')
                        .trigger('change');

                    $('.payment_account_id')
                        .val('')
                        .prop('disabled', true)
                        .prop('required', false);
                }

                updatePaymentAccount();
            });


            // ==========================================
            // PAYMENT METHOD CHANGE
            // ==========================================

            $('.payment_method').on('change', function() {

                const method = $(this).val();

                console.log('Payment Method changed:', method);

                // If Due, force payment status to Unpaid
                if (method === 'due') {

                    $('.payment_status')
                        .val('unpaid');

                    $('.payment_account_id')
                        .val('')
                        .prop('disabled', true)
                        .prop('required', false);
                }

                updatePaymentAccount();
            });


            // ==========================================
            // FORM SUBMIT SAFETY
            // ==========================================

            $('#salesForm').on('submit', function() {

                const status = $('.payment_status').val();
                const paymentMethod = $('.payment_method').val();
                const paymentAccount = $('.payment_account_id');

                // Unpaid / Due
                if (status === 'unpaid' || paymentMethod === 'due') {

                    // Make absolutely sure no payment account is submitted
                    paymentAccount.val('');

                    console.log('Submitting Unpaid/Due sale without payment account.');
                }
            });


            // ==========================================
            // INITIAL STATE
            // ==========================================

            updatePaymentAccount();

            console.log('✅ Purchase Calculator initialized successfully!');
        });
    </script>
    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                Swal.fire({
                    icon: 'error',
                    title: 'Production Failed',
                    text: @json(session('error')),
                    confirmButtonText: 'OK'
                });

            });
        </script>
    @endif
@endpush
