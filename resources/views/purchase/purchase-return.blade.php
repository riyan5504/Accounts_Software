@extends('backend.master')

@section('content')
    <!--begin::App Content Header-->
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row bg-info opasity-50 rounded">
                <div class="col-sm-4">
                    <h4 class="mb-0 mt-0">Purchase Return Entry</h4>
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
                <form action="{{ url('/purchase/return/store') }}" method="POST">
                    @csrf
                    <!--begin::Body-->
                    <div class="card-body">
                        <!--start::vendor-->
                        <div id="vendorContainer" class="border-0 shadow-sm ms-0">
                            <div
                                class="bg-success text-white d-flex justify-content-between align-vendors-center mb-3 px-1 py-1 rounded">
                                <h6 class="mb-0 ms-1 p-1">Vendor Details</h6>
                            </div>
                            <!-- ✅ Proper Row Structure -->
                            <div class="row g-2 align-vendor-end mb-1">
                                <div class="form-group col-md-4 mb-1 position-relative">
                                    <input type="text" name="vendor_name" class="form-control vendor_name"
                                        id="vendor_name" placeholder=" " required />
                                    <label for="vendor_name" class="floating-label">Vendor Name</label>
                                    <input type="hidden" name="vendor_id" class="form-control vendor_id" placeholder=" " />
                                </div>
                                <div class="form-group col-md-2 mb-1">
                                    <select name="purchase_id" id="purchase_id" class="form-control purchase_id" required>
                                        <option value="">
                                            Select Invoice
                                        </option>
                                    </select>
                                    <label for="purchase_id" class="floating-label">Purchase Invoice</label>
                                </div>

                                <div class="form-group col-md-3 mb-1">
                                    <input type="text" name="phone" class="form-control phone" placeholder=" "
                                        required />
                                    <label for="phone" class="floating-label">Phone</label>
                                </div>
                                <div class="form-group col-md-3 mb-1">
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
                                        value="{{ $newReturnNo }}" placeholder=" " readonly />
                                    <label for="invoice_no" class="floating-label">Invoice Number</label>
                                </div>
                            </div>
                        </div>
                        <!--start::vendor-->
                        <div id="itemContainer" class="border-0 shadow-sm">
                            <div
                                class="bg-success text-white d-flex justify-content-between align-items-center mb-3 px-1 py-1 rounded">
                                <h6 class="mb-0 ms-1">Item Details</h6>
                                <button type="button" id="addItem" class="btn btn-light btn-sm text-dark fw-bold">
                                    + Add Item
                                </button>
                            </div>

                            <!-- ✅ Proper Row Structure -->
                            <div class="item-row row g-2 align-items-end mb-2">
                                <div class="form-group col-md-2 mb-1">
                                    <input type="text" name="item_name[]" class="form-control item_name"
                                        placeholder=" " required />
                                    <label for="item_name" class="floating-label">Item Name</label>
                                    <input type="hidden" name="item_id[]" class="item_id">
                                </div>
                                <div class="form-group col-md-1 mb-1">
                                    <input type="text" name="item_code[]" class="form-control item_code"
                                        placeholder=" " readonly />
                                    <label for="item_code" class="floating-label">Item Code</label>
                                </div>
                                <div class="form-group col-md-1 mb-1">
                                    <input type="text" name="cat_name[]" class="form-control cat_name"
                                        placeholder=" " required readonly />
                                    <label for="cat_name" class="floating-label">Category</label>
                                </div>
                                <div class="form-group col-md-1 mb-1">
                                    <input type="text" name="size[]" class="form-control size" placeholder=" "
                                        readonly />
                                    <label for="size" class="floating-label">Pack Size</label>
                                </div>
                                <div class="form-group col-md-1 mb-1">
                                    <input type="number" name="qty[]" class="form-control qty" placeholder=" "
                                        required />
                                    <label for="qty" class="floating-label">Quantity</label>
                                </div>
                                <div class="form-group col-md-1 mb-1">
                                    <input type="number" name="unit_price[]" class="form-control unit_price"
                                        placeholder=" " required readonly />
                                    <label for="unit_price" class="floating-label">Unit Price</label>
                                </div>
                                <div class="form-group col-md-1 mb-1">
                                    <input type="number" name="price[]" class="form-control price" placeholder=" "
                                        required readonly />
                                    <label for="price" class="floating-label">Price</label>
                                </div>
                                <div class="form-group col-md-1 mb-1">
                                    <input type="number" name="vat_percent[]" class="form-control vat_percent"
                                        placeholder=" " />
                                    <label for="vat_percent" class="floating-label">Vat(%)</label>
                                </div>
                                <div class="form-group col-md-1 mb-1">
                                    <input type="number" name="vat_amount[]" class="form-control vat_amount"
                                        placeholder=" " readonly />
                                    <label for="vat_amount" class="floating-label">Vat Amount</label>
                                </div>
                                <div class="form-group col-md-2 d-flex align-items-end mb-1">
                                    <div class="w-100">
                                        <input type="number" name="total_price[]" class="form-control total_price"
                                            placeholder=" " readonly>
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
                            <div class="form-group col-md-2 mt-2 mb-2">
                                <input type="number" name="dis_percent" class="form-control dis_percent"
                                    placeholder=" ">
                                <label for="dis_percent" class="floating-label">Discount(%)</label>
                            </div>
                            <div class="form-group col-md-2 mt-2 mb-2">
                                <input type="number" name="dis_amt" class="form-control dis_amt" placeholder=" "
                                    readonly>
                                <label for="dis_amt" class="floating-label">Discount Amount</label>
                            </div>
                            <div class="form-group col-md-2 mt-2 mb-2">
                                <input type="number" name="vat_amt" class="form-control vat_amt" placeholder=" "
                                    readonly>
                                <label for="vat_amt" class="floating-label">VAT Amount</label>
                            </div>
                            <div class="form-group col-md-2 mt-2 mb-2">
                                <input type="number" name="sub_total" class="form-control sub_total" placeholder=" "
                                    readonly>
                                <label for="sub_total" class="floating-label">Sub Total</label>
                            </div>
                            <div class="form-group col-md-2 mt-2 mb-2">
                                <input type="number" name="grand_total" class="form-control grand_total"
                                    placeholder=" " readonly>
                                <label for="grand_total" class="floating-label">Grand Total</label>
                            </div>
                            <div class="form-group col-md-2 mb-2">
                                <input type="text" name="reference" class="form-control reference" placeholder=" " />
                                <label for="reference" class="floating-label">Reference</label>
                            </div>
                            <div class="form-group col-md-2 mb-2">
                                <select name="payment_status" class="form-control payment_status" placeholder=" ">
                                    <option selected disabled>Select Payment Status</option>
                                    <option value="paid">Paid</option>
                                    <option value="due">Due</option>
                                </select>
                                <label for="payment_status" class="floating-label">Payment Status</label>
                            </div>
                            <div class="form-group col-md-2 mb-2">
                                <select name="debit_account_id" class="form-control debit_account_id" required>
                                    <option selected disabled>Select Debit Account</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}">
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
                                        <option value="{{ $account->id }}"
                                            data-ac_type="{{ strtolower($account->ac_type) }}">
                                            {{ $account->account_name }}</option>
                                    @endforeach
                                </select>
                                <label class="floating-label">Credit Account</label>
                            </div>
                        </div>
                        <!--end::Body-->
                        <!--begin::Footer-->
                        <div class="card-footer d-flex align-items-end justify-content-between gap-3 p-0 m-0">

                            <div class="form-group flex-grow-1 mt-2">
                                <textarea name="narration" class="form-control narration" rows="1" placeholder=" " required></textarea>
                                <label for="narration" class="floating-label">Narration</label>
                            </div>

                            <button type="submit" class="btn btn-primary ms-auto">
                                Save
                            </button>

                        </div>
                        <!--end::Footer-->
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
        let selectedDebitAccount = null;
        $(".payment_status").on("change", function() {
            let status = $(this).val();
            let accountSelect = $(".debit_account_id");
            accountSelect.prop("disabled", true);
            accountSelect.html('<option value="">Loading...</option>');
            $.ajax({
                url: "{{ route('search.accounts.by-status', ':status') }}".replace(':status', status),
                type: "GET",
                success: function(data) {
                    accountSelect.prop("disabled", false);
                    accountSelect.empty();
                    accountSelect.append('<option value="">Select Debit Account</option>');
                    $.each(data, function(i, acc) {
                        accountSelect.append(
                            `<option value="${acc.id}">${acc.account_name}</option>`
                        );
                    });

                    // Invoice থেকে আসা Debit Account Select
                    if (selectedDebitAccount) {
                        accountSelect.val(selectedDebitAccount);
                    }
                }
            });
        });

        //==============================
        // Clear All Return Form
        //==============================
        function clearReturnForm() {

            // Vendor Information
            $(".phone").val('');
            $(".email").val('');
            $(".address").val('');

            // Purchase Invoice
            $("#purchase_id").html('<option value="">Select Invoice</option>');

            // Remove all rows except first
            $("#itemContainer .item-row").not(":first").remove();

            // Clear first row
            let row = $("#itemContainer .item-row:first");

            row.find("input[type=text]").val('');
            row.find("input[type=number]").val('');
            row.find("input[type=hidden]").val('');

            // Summary
            $(".dis_percent").val(0);
            $(".dis_amt").val(0);
            $(".vat_amt").val(0);
            $(".sub_total").val(0);
            $(".grand_total").val(0);

            // Others
            $(".reference").val('');
            $(".payment_status").prop("selectedIndex", 0);
            $(".debit_account_id").prop("selectedIndex", 0);
            $(".credit_account_id").prop("selectedIndex", 0);
            $(".narration").val('');
        }


        //==============================
        // Vendor Autocomplete
        //==============================
        let vendors = @json($vendors).map(v => ({
            label: v.v_name,
            value: v.v_name,
            id: v.id
        }));

        $("#vendor_name").autocomplete({
            source: vendors,
            select: function(event, ui) {
                clearReturnForm();
                $(".vendor_id").val(ui.item.id);
                $.ajax({
                    url: "/purchase/return/vendor/" + ui.item.id,
                    type: "GET",
                    success: function(res) {
                        $(".phone").val(res.vendor.phone);
                        $(".email").val(res.vendor.email);
                        $(".address").val(res.vendor.address);
                        let html = '<option value="">Select Invoice</option>';
                        $.each(res.invoices, function(i, row) {
                            html += `
                        <option value="${row.id}">
                            ${row.invoice_no}
                        </option>
                    `;
                        });
                        $("#purchase_id").html(html);
                    }
                });
            }
        });

        // যদি Vendor Name পরিবর্তন করে টাইপ করে
        $("#vendor_name").on("input", function() {
            if ($(this).val() == "") {
                $(".vendor_id").val("");
                clearReturnForm();
            }
        });

        //==============================
        // Purchase Invoice Change
        //==============================
        $("#purchase_id").change(function() {
            let id = $(this).val();
            if (id == "") return;
            $.get("/purchase/return/invoice/" + id, function(res) {
                $(".phone").val(res.vendor.phone);
                $(".email").val(res.vendor.email);
                $(".address").val(res.vendor.address);
                selectedDebitAccount = res.purchase.debit_account_id;
                $(".payment_status").val(res.purchase.payment_status == "paid" ? "paid" : "due").trigger("change");
                $(".credit_account_id").val(res.purchase.credit_account_id);
                $(".reference").val(res.purchase.reference);
                $(".narration").val(res.purchase.narration);
                $(".dis_percent").val(res.purchase.dis_percent);
                calculateReturn();
                $("#itemContainer .item-row").remove();
                $.each(res.items, function(i, item) {
                    $("#itemContainer").append(`
                    <div class="item-row row g-2 align-items-end mb-2">
                        <div class="form-group col-md-2 mb-1">
                            <input type="text" name="item_name[]" class="form-control item_name" value="${item.item_name}" placeholder=" " required />
                            <label for="item_name" class="floating-label">Item Name</label>
                            <input type="hidden" name="item_id[]" class="item_id" value="${item.item_id}">
                        </div>
                        <div class="form-group col-md-1 mb-1">
                            <input type="text" name="item_code[]" class="form-control item_code" value="${item.item_code}" placeholder=" " readonly />
                            <label for="item_code" class="floating-label">Item Code</label>
                        </div>
                        <div class="form-group col-md-1 mb-1">
                            <input type="text" name="cat_name[]" class="form-control cat_name" value="${item.category}" placeholder=" " required readonly />
                            <label for="cat_name" class="floating-label">Category</label>
                        </div>
                        <div class="form-group col-md-1 mb-1">
                            <input type="text" name="size[]" class="form-control size" value="${item.size}" placeholder=" " readonly />
                            <label for="size" class="floating-label">Pack Size</label>
                        </div>
                        <div class="form-group col-md-1 mb-1">
                            <input type="number" name="qty[]" class="form-control qty" max="${item.qty}" value="${item.qty}" placeholder=" " required />
                            <label for="qty" class="floating-label">Quantity</label>
                        </div>
                        <div class="form-group col-md-1 mb-1">
                            <input type="number" name="unit_price[]" class="form-control unit_price" value="${item.unit_price}" placeholder=" " required readonly />
                            <label for="unit_price" class="floating-label">Unit Price</label>
                        </div>
                        <div class="form-group col-md-1 mb-1">
                            <input type="number" name="price[]" class="form-control price" value="${item.price}" placeholder=" " required readonly />
                            <label for="price" class="floating-label">Price</label>
                        </div>
                        <div class="form-group col-md-1 mb-1">
                            <input type="number" name="vat_percent[]" class="form-control vat_percent" value="${item.vat_percent}" placeholder=" " />
                            <label for="vat_percent" class="floating-label">Vat(%)</label>
                        </div>
                        <div class="form-group col-md-1 mb-1">
                            <input type="number" name="vat_amount[]" class="form-control vat_amount" value="${item.vat_amount}" placeholder=" " readonly />
                            <label for="vat_amount" class="floating-label">Vat Amount</label>
                        </div>
                        <div class="form-group col-md-2 d-flex align-items-end mb-1">
                            <div class="w-100">
                                <input type="number" name="total_price[]" class="form-control total_price" value="${item.total_price}" placeholder=" " readonly>
                                <label for="total_price" class="floating-label">Total Amount</label>
                            </div>
                            <button type="button" class="btn btn-danger btn-sm ms-2 removeItem">
                                ×
                            </button>
                        </div>
                    `);
                });
                calculateReturn();
            });
        });

        //==============================
        // Calculate Function
        //==============================
        function calculateReturn() {
            let subTotal = 0;
            let vatTotal = 0;
            $(".item-row").each(function() {
                let qty = parseFloat($(this).find(".qty").val()) || 0;
                let unit = parseFloat($(this).find(".unit_price").val()) || 0;
                let vat = parseFloat($(this).find(".vat_percent").val()) || 0;
                let price = qty * unit;
                let vatAmount = price * vat / 100;
                let total = price + vatAmount;
                $(this).find(".price").val(price.toFixed(2));
                $(this).find(".vat_amount").val(vatAmount.toFixed(2));
                $(this).find(".total_price").val(total.toFixed(2));
                subTotal += price;
                vatTotal += vatAmount;
            });
            let disPercent = parseFloat($(".dis_percent").val()) || 0;
            let disAmount = subTotal * disPercent / 100;
            let grandTotal = (subTotal + vatTotal) - disAmount;
            $(".sub_total").val(subTotal.toFixed(2));
            $(".vat_amt").val(vatTotal.toFixed(2));
            $(".dis_amt").val(disAmount.toFixed(2));
            $(".grand_total").val(grandTotal.toFixed(2));
        }
        // Qty Change
        $(document).on("input", ".qty", function() {
            calculateReturn();
        });

        // VAT Change
        $(document).on("input", ".vat_percent", function() {
            calculateReturn();
        });
        // Discount Change
        $(document).on("input", ".dis_percent", function() {
            calculateReturn();
        });
    </script>
@endpush
