@extends('backend.master')

@section('content')
    <!--begin::App Content Header-->
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row bg-info opasity-50 rounded">
                <div class="col-sm-4">
                    <h4 class="mb-0 mt-0">Purchase Entry</h4>
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
                <form action="{{ url('/purchase/store') }}" method="POST">
                    @csrf
                    <!--begin::Body-->
                    <div class="card-body">
                        <!--start::vendor-->
                        <input type="hidden" id="itemSerial" value="{{ $lastSerial + 1 }}">
                        <input type="hidden" id="itemSearchUrl" value="{{ route('search.item') }}">
                        <input type="hidden" id="vendorSearchUrl" value="{{ route('search.vendor') }}">
                        <input type="hidden" id="accountStatusUrl" value="{{ url('/search/accounts-by-status') }}">

                        <div id="vendorContainer" class="border-0 shadow-sm ms-0">
                            <div
                                class="bg-success text-white d-flex justify-content-between align-vendors-center mb-3 px-1 py-1 rounded">
                                <h6 class="mb-0 ms-1">Vendor Details</h6>
                            </div>
                            <!-- ✅ Proper Row Structure -->
                            <div class="row g-2 align-vendor-end mb-1">
                                <div class="form-group col-md-5 mb-1 position-relative">
                                    <input type="text" name="v_name" class="form-control v_name" placeholder=" "
                                        required />
                                    <label for="v_name" class="floating-label">Vendor Name</label>
                                    <input type="hidden" name="vendor_id" class="form-control vendor_id" placeholder=" " />
                                    <button class="add-btn" type="button" data-bs-toggle="modal"
                                        data-bs-target="#addVendorModal">
                                        <i class="bi bi-plus"></i>
                                    </button>
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
                                        value="{{ $newPurchaseNo }}" placeholder=" "/>
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
                                        placeholder=" " required />
                                    <label for="cat_name" class="floating-label">Category</label>
                                </div>
                                <div class="form-group col-md-1 mb-1">
                                    <input type="text" name="size[]" class="form-control size" placeholder=" " />
                                    <label for="size" class="floating-label">Pack Size</label>
                                </div>
                                <div class="form-group col-md-1 mb-1">
                                    <input type="number" step="0.01" name="qty[]" class="form-control qty"
                                        placeholder=" " required />
                                    <label for="qty" class="floating-label">Quantity</label>
                                </div>
                                <div class="form-group col-md-1 mb-1">
                                    <input type="number" step="0.01" name="unit_price[]"
                                        class="form-control unit_price" placeholder=" " required />
                                    <label for="unit_price" class="floating-label">Unit Price</label>
                                </div>
                                <div class="form-group col-md-1 mb-1">
                                    <input type="number" step="0.01" name="price[]" class="form-control price"
                                        placeholder=" " required />
                                    <label for="price" class="floating-label">Price</label>
                                </div>
                                <div class="form-group col-md-1 mb-1">
                                    <input type="number" step="0.01" name="vat_percent[]"
                                        class="form-control vat_percent" placeholder=" " />
                                    <label for="vat_percent" class="floating-label">Vat(%)</label>
                                </div>
                                <div class="form-group col-md-1 mb-1">
                                    <input type="number" step="0.01" name="vat_amount[]"
                                        class="form-control vat_amount" placeholder=" " readonly />
                                    <label for="vat_amount" class="floating-label">Vat Amount</label>
                                </div>
                                <div class="form-group col-md-2 d-flex align-items-end mb-1">
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
                            <div class="form-group col-md-1 mt-2 mb-2">
                                <input type="number" step="0.001" name="dis_percent" class="form-control dis_percent"
                                    placeholder=" ">
                                <label for="dis_percent" class="floating-label">Discount(%)</label>
                            </div>
                            <div class="form-group col-md-2 mt-2 mb-2">
                                <input type="number" step="0.01" name="dis_amt" class="form-control dis_amt"
                                    placeholder=" " readonly>
                                <label for="dis_amt" class="floating-label">Discount Amount</label>
                            </div>
                            <div class="form-group col-md-1 mt-2 mb-2">
                                <input type="number" step="0.01" name="vat_amt" class="form-control vat_amt"
                                    placeholder=" " readonly>
                                <label for="vat_amt" class="floating-label">VAT Amount</label>
                            </div>
                            <div class="form-group col-md-2 mt-2 mb-2">
                                <input type="number" step="0.01" name="sub_total" class="form-control sub_total"
                                    placeholder=" " readonly>
                                <label for="sub_total" class="floating-label">Sub Total</label>
                            </div>
                            <div class="form-group col-md-2 mt-2 mb-2">
                                <input type="number" step="0.01" name="grand_total" class="form-control grand_total"
                                    placeholder=" " readonly>
                                <label for="grand_total" class="floating-label">Grand Total</label>
                            </div>
                            <div class="form-group col-md-2 mt-2 mb-2">
                                <input type="number" step="0.01" name="paid_amt" class="form-control paid_amt"
                                    placeholder=" ">
                                <label for="paid_amt" class="floating-label">Paid Amount</label>
                            </div>
                            <div class="form-group col-md-2 mt-2 mb-2">
                                <input type="number" step="0.01" name="due_amt" class="form-control due_amt"
                                    placeholder=" " readonly>
                                <label for="due_amt" class="floating-label">Due Amount</label>
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
                                <select name="payment_status" class="form-control payment_status" placeholder=" "
                                    required>
                                    <option selected disabled>Select Payment Status</option>
                                    <option value="paid">Paid</option>
                                    <option value="unpaid">Unpaid</option>
                                    <option value="partial">Partial</option>
                                </select>
                                <label for="payment_status" class="floating-label">Payment Status</label>
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

    <!--end::App Content-->
    <div class="modal fade" id="addVendorModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('purchase.vendor.store') }}" method="POST">
                    @csrf
                    <!--begin::Body-->
                    <div class="card-body">
                        <!--start::vendor-->
                        <div id="vendorContainer" class="border-0 shadow-sm ms-0">
                            <div
                                class="bg-success text-white d-flex justify-content-between align-vendors-center mb-3 px-1 py-1 rounded">
                                <h4 class="mb-0 ms-1">Add Vendor Details</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <!-- ✅ Proper Row Structure -->
                            <div class="row g-2 align-vendor-end mb-2">
                                <div class="form-group mb-1 col-md-5">
                                    <input type="text" name="v_name" class="form-control v_name" placeholder=" "
                                        required />
                                    <label for="v_name" class="floating-label">Vendor Name</label>
                                </div>

                                <div class="form-group mb-1 col-md-3">
                                    <input type="text" name="phone" class="form-control phone" placeholder=" "
                                        required />
                                    <label for="phone" class="floating-label">Phone</label>
                                </div>
                                <div class="form-group mb-1 col-md-4">
                                    <input type="email" name="email" class="form-control email" placeholder=" " />
                                    <label for="email" class="floating-label">Email</label>
                                </div>
                                <div class="form-group mb-1 col-md-12">
                                    <textarea name="address" class="form-control address" rows="1" placeholder=" " required></textarea>
                                    <label for="address" class="floating-label">Address</label>
                                </div>
                                <div class="form-group col-sm-4 col-md-2 mb-1">
                                    <input type="number" name="opening_balance" class="form-control opening_balance"
                                        placeholder=" " />
                                    <label for="opening_balance" class="floating-label">Opening Balance</label>
                                </div>
                            </div>
                        </div>
                        <!--begin::Footer-->
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                        <!--end::Footer-->
                </form>
            </div>
        </div>
    </div>
@endsection

