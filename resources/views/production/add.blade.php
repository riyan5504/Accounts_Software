@extends('backend.master')

@section('content')
    <!--begin::App Content Header-->
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Production Entry</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <a href="{{ url('/production') }}"
                                class="{{ request()->is('production') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Production
                            </a>
                        </li>

                        <li class="breadcrumb-item">
                            <a href="{{ url('/production/add') }}"
                                class="{{ request()->is('production/entry') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Entry
                            </a>
                        </li>

                        <li class="breadcrumb-item">
                            <a href="{{ url('/production/list') }}"
                                class="{{ request()->is('production/list') ? 'text-primary fw-bold' : 'text-dark' }}">
                                List
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
            <!--begin::Row-->
            <div class="row g-4">
                <!--begin::Col-->
                <div class="col-md-12">
                    <!--begin::Quick Example-->
                    <div class="card card-primary card-outline mb-4">
                        <!--begin::Form-->
                        <form action="{{ url('/production/store') }}" method="POST">
                            @csrf
                            <!--begin::Product-->
                            <div class="card-body">
                                <div class="row g-2">
                                    <div
                                        class="bg-success text-white d-flex justify-content-between align-items-center px-1 py-1 rounded">
                                        <h6 class="mb-0">Production</h6>
                                    </div>
                                    <div class="form-group col-md-3 mb-0">
                                        <input type="text" name="name" class="form-control name" id="name"
                                            placeholder=" " required />
                                        <label for="name" class="floating-label">Product Name</label>
                                    </div>
                                    <div class="form-group col-md-3 mb-0">
                                        <input type="date" name="date" class="form-control date"
                                            value="{{ date('Y-m-d') }}" id="date" placeholder=" " required />
                                        <label for="date" class="floating-label">Production Date</label>
                                    </div>

                                    <div class="form-group col-md-3 mb-0">
                                        <input type="text" name="batch_no" class="form-control batch_no" id="batch_no"
                                            value="{{ $nextBatch }}" placeholder=" " readonly />
                                        <label for="batch_no" class="floating-label">Batch Number</label>
                                    </div>
                                    <div class="form-group col-md-3 mb-0">
                                        <input type="text" name="batch_size" class="form-control batch_size"
                                            id="batch_size" placeholder=" " readonly />
                                        <label for="batch_size" class="floating-label">Batch Size</label>
                                    </div>
                                    <div
                                        class="bg-success text-white d-flex justify-content-between align-items-center mb-0 px-1 py-1 rounded">
                                        <h6 class="mb-0">Raw Material</h6>
                                    </div>
                                    <div class="ra_row row align-items-start g-2">
                                        <div class="form-group col-md-2 mb-0">
                                            <input type="text" name="ra_name" class="form-control ra_name" id="ra_name"
                                                placeholder=" " required />
                                            <label for="ra_name" class="floating-label">Raw Name</label>
                                            <input type="hidden" name="ra_item_id" class="ra_item_id">
                                        </div>
                                        <div class="form-group col-md-1 mb-0">
                                            <input type="number" name="raw_qty" class="form-control raw_qty" id="raw_qty"
                                                placeholder=" " required />
                                            <label for="raw_qty" class="floating-label">Raw Qty</label>
                                        </div>
                                        <div class="form-group col-md-1 mb-0">
                                            <select name="raw_unit" class="form-control raw_unit" id="raw_unit">
                                                <option selected disabled>Select Unit</option>
                                                <option value="ml">ml</option>
                                                <option value="gm">gm</option>
                                                <option value="kg">Kg</option>
                                                <option value="ltr">Ltr</option>
                                            </select>
                                            <label for="raw_unit" class="floating-label">Unit</label>
                                        </div>
                                        <div class="form-group col-md-1 mb-0">
                                            <input type="number" step="0.01" name="raw_u_price"
                                                class="form-control raw_u_price" id="raw_u_price" placeholder=" "
                                                required />
                                            <label for="raw_u_price" class="floating-label">Unit Price</label>
                                        </div>
                                        <div class="form-group col-md-2 mb-0">
                                            <input type="number" step="0.01" name="raw_t_price"
                                                class="form-control raw_t_price" id="raw_t_price" placeholder=" "
                                                readonly />
                                            <label for="raw_t_price" class="floating-label">Total Price</label>
                                        </div>

                                        <div class="form-group col-md-1 mb-0">
                                            <input type="number" name="yield" class="form-control yield"
                                                id="yield" placeholder=" " />
                                            <label for="yield" class="floating-label">Yield Qty</label>
                                        </div>
                                        <div class="form-group col-md-1 mb-0">
                                            <select name="yield_unit" class="form-control yield_unit" id="yield_unit">
                                                <option selected disabled>Select Unit</option>
                                                <option value="ml">ml</option>
                                                <option value="gm">gm</option>
                                                <option value="kg">Kg</option>
                                                <option value="ltr">Ltr</option>
                                            </select>
                                            <label for="yield_unit" class="floating-label">Unit</label>
                                        </div>
                                        <div class="form-group col-md-1 mb-0">
                                            <input type="number" step="0.01" name="ex_qty"
                                                class="form-control ex_qty" id="ex_qty" placeholder=" " required />
                                            <label for="ex_qty" class="floating-label">Extract Qty</label>
                                        </div>
                                        <div class="form-group col-md-1 mb-0">
                                            <select name="ex_unit" class="form-control ex_unit" id="ex_unit">
                                                <option selected disabled>Select Unit</option>
                                                <option value="ml">ml</option>
                                                <option value="gm">gm</option>
                                                <option value="kg">Kg</option>
                                                <option value="ltr">Ltr</option>
                                            </select>
                                            <label for="ex_unit" class="floating-label">Unit</label>
                                        </div>
                                        <div class="form-group col-md-1 mb-0">
                                            <input type="number" step="0.01" name="yield_percent"
                                                class="form-control yield_percent" id="yield_percent" placeholder=" "
                                                readonly />
                                            <label for="yield_percent" class="floating-label">Extract (%)</label>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Product-->

                                <!--begin::Raw Material-->
                                <div class="border-0 shadow-sm mt-2" id="rawMaterialsContainer">
                                    <div
                                        class="bg-success text-white d-flex justify-content-between align-items-center mb-2 px-1 py-1 rounded">
                                        <h6 class="mb-0">Chemicals</h6>
                                        <button type="button" id="addRawMaterial"
                                            class="btn btn-light btn-sm text-dark">+ Add New Chemicals</button>
                                    </div>

                                    <!-- ✅ First Row (with labels) -->
                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="raw-material-row row g-2 align-items-start">
                                                <div class="form-group mb-1 col-md-3">
                                                    <input type="text" name="raw_name[]" class="form-control raw_name"
                                                        placeholder=" " required>
                                                    <label class="floating-label">Chemical Name</label>
                                                    <input type="hidden" name="raw_item_id[]" class="raw_item_id">
                                                </div>
                                                <div class="form-group mb-1 col-md-1">
                                                    <input type="number" step="0.01" name="used_percent[]"
                                                        class="form-control used_percent" placeholder=" " required />
                                                    <label class="floating-label">Used (%)</label>
                                                </div>
                                                <div class="form-group mb-1 col-md-2">
                                                    <input type="number" step="0.01" name="used_qty[]"
                                                        class="form-control used_qty" placeholder=" " readonly />
                                                    <label class="floating-label">Used Qty</label>
                                                </div>
                                                <div class="form-group col-md-1 mb-0">
                                                    <select name="ch_unit[]" class="form-control ch_unit" id="ch_unit">
                                                        <option selected disabled>Select Unit</option>
                                                        <option value="ml">ml</option>
                                                        <option value="gm">gm</option>
                                                        <option value="kg">Kg</option>
                                                        <option value="ltr">Ltr</option>
                                                    </select>
                                                    <label for="ch_unit" class="floating-label">Unit</label>
                                                </div>
                                                <div class="form-group mb-1 col-md-2">
                                                    <input type="number" step="0.01" name="u_price[]"
                                                        class="form-control u_price" placeholder=" " readonly />
                                                    <label class="floating-label">Unit Price</label>
                                                </div>
                                                <div class="form-group mb-1 col-md-3 d-flex">
                                                    <div class="w-100">
                                                        <input type="number" step="0.01" name="t_price[]"
                                                            class="form-control t_price" placeholder=" " readonly />
                                                        <label class="floating-label">Total Price</label>
                                                    </div>
                                                    <button type="button"
                                                        class="btn btn-danger btn-sm ms-2 removeRawMaterial mt-auto">×</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mb-1">
                                                <input type="number" step="0.01" name="raw_grand_price"
                                                    class="form-control raw_grand_price" id="raw_grand_price"
                                                    placeholder=" " readonly />
                                                <label for="raw_grand_price" class="floating-label">Total Chemical
                                                    Cost</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Raw Material-->

                                <!--begin::Packaging Material-->
                                <div class="border-0 shadow-sm mt-1" id="packContainer">
                                    <div
                                        class="bg-success text-white d-flex justify-content-between align-items-center mb-2 px-1 py-1 rounded">
                                        <h6 class="mb-0">Packaging Material</h6>
                                        <button type="button" id="addPack" class="btn btn-light btn-sm text-dark">+
                                            Add Packaging</button>
                                    </div>

                                    <!-- ✅ First Row (with labels) -->
                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="pack-row row align-items-start g-2">
                                                <div class="form-group col-md-3 mb-1">
                                                    <input type="text" name="pack_name[]"
                                                        class="form-control pack_name" placeholder=" " required>
                                                    <label class="floating-label">Name</label>
                                                    <input type="hidden" name="pack_item_id[]" class="pack_item_id">
                                                </div>
                                                <div class="form-group col-md-2 mb-1">
                                                    <input type="text" name="pack_size[]"
                                                        class="form-control pack_size" placeholder=" " />
                                                    <label class="floating-label">Size</label>
                                                </div>
                                                <div class="form-group col-md-2 mb-1">
                                                    <input type="number" name="pack_qty[]" class="form-control pack_qty"
                                                        placeholder=" " />
                                                    <label class="floating-label">Quantity</label>
                                                </div>
                                                <div class="form-group col-md-2 mb-1">
                                                    <input type="number" step="0.01" name="pack_price[]"
                                                        class="form-control pack_price" placeholder=" " readonly />
                                                    <label class="floating-label">Unit Price</label>
                                                </div>
                                                <div class="form-group col-md-3 mb-1 d-flex">
                                                    <div class="w-100">
                                                        <input type="number" step="0.01" name="total_price[]"
                                                            class="form-control total_price" placeholder=" " readonly />
                                                        <label class="floating-label">Total Price</label>
                                                    </div>
                                                    <button type="button"
                                                        class="btn btn-danger btn-sm ms-2 removePack mt-auto">×</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 mb-1">
                                            <div class="form-group">
                                                <input type="number" step="0.01" name="pack_grand_price"
                                                    class="form-control pack_grand_price" id="pack_grand_price"
                                                    placeholder=" " readonly />
                                                <label for="pack_grand_price" class="floating-label">Total Packaging
                                                    Cost</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!--end::Packaging Material-->

                                <!--begin::Labor Cost-->
                                <div class="border-0 shadow-sm mt-1" id="laborContainer">
                                    <div
                                        class="bg-success text-white d-flex justify-content-between align-items-center mb-2 px-1 py-1 rounded">
                                        <h6 class="mb-0">Labor Cost</h6>
                                        <button type="button" id="addLabor" class="btn btn-light btn-sm text-dark">+
                                            Add Labor</button>
                                    </div>

                                    <!-- ✅ First Row (with labels) -->
                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="labor-row row g-2 align-items-start">
                                                <div class="form-group col-md-4 mb-1">
                                                    <input type="text" name="labor_name[]"
                                                        class="form-control labor_name" placeholder=" " />
                                                    <label class="floating-label">Name</label>
                                                </div>
                                                <div class="form-group col-md-2 mb-1">
                                                    <input type="number" name="duty_day[]" class="form-control duty_day"
                                                        placeholder=" " />
                                                    <label class="floating-label">Total Duty Day</label>
                                                </div>
                                                <div class="form-group col-md-2 mb-1">
                                                    <input type="number" name="d_pay[]" class="form-control d_pay"
                                                        placeholder=" " />
                                                    <label class="floating-label">Daily Pay</label>
                                                </div>
                                                <div class="form-group col-md-4 mb-1 d-flex">
                                                    <div class="w-100">
                                                        <input type="number" name="total_pay[]"
                                                            class="form-control total_pay" placeholder=" " readonly />
                                                        <label class="floating-label">Total Pay</label>
                                                    </div>
                                                    <button type="button"
                                                        class="btn btn-danger btn-sm ms-2 removeLabor mt-auto">×</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 mb-1 ms-0">
                                            <div class="form-group">
                                                <input type="number" step="0.01" name="labor_grand_price"
                                                    class="form-control labor_grand_price" id="labor_grand_price"
                                                    placeholder=" " readonly />
                                                <label for="labor_grand_price" class="floating-label">Total Labor
                                                    Cost</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!--end::Labor Cost-->

                                <!--begin::Machinery Depreciation Cost-->
                                <div class="border-0 shadow-sm mt-1" id="machineContainer">
                                    <div
                                        class="bg-success text-white d-flex justify-content-between align-items-center mb-2 px-1 py-1 rounded">
                                        <h6 class="mb-0">Machinery Depreciation Cost</h6>
                                        <button type="button" id="addMachine" class="btn btn-light btn-sm text-dark">+
                                            Add Depreciation Cost</button>
                                    </div>

                                    <!-- ✅ First Row (with labels) -->
                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="machine-row row align-items-start g-2">
                                                <div class="form-group col-md-7 mb-1">
                                                    <input type="text" name="machine_name[]"
                                                        class="form-control machine_name" placeholder=" " />
                                                    <label class="floating-label">Machine Name</label>
                                                </div>
                                                <div class="form-group col-md-5 mb-1 d-flex">
                                                    <div class="w-100">
                                                        <input type="number" step="0.01" name="machine_cost_amt[]"
                                                            class="form-control machine_cost_amt" placeholder=" " />
                                                        <label class="floating-label">Cost
                                                            Amount</label>
                                                    </div>
                                                    <button type="button"
                                                        class="btn btn-danger btn-sm ms-2 removeMachine mt-auto">×</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 mb-1">
                                            <div class="form-group">
                                                <input type="number" step="0.01" name="depreciation_grand_price"
                                                    class="form-control depreciation_grand_price"
                                                    id="depreciation_grand_price" placeholder=" " readonly />
                                                <label for="depreciation_grand_price" class="floating-label">Total
                                                    Depreciation
                                                    Cost</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!--end::Machinery Depreciation Cost-->

                                <!--begin::Utility Cost-->
                                <div class="border-0 shadow-sm mt-1" id="utilityContainer">
                                    <div
                                        class="bg-success text-white d-flex justify-content-between align-items-center mb-2 px-1 py-1 rounded">
                                        <h6 class="mb-0">Utility Cost</h6>
                                        <button type="button" id="addUtility" class="btn btn-light btn-sm text-dark">+
                                            Add Utility Cost</button>
                                    </div>

                                    <!-- ✅ First Row (with labels) -->
                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="utility-row row align-items-start g-2">
                                                <div class="form-group col-md-7 mb-1">
                                                    <input type="text" name="utility_name[]"
                                                        class="form-control utility_name" placeholder=" " />
                                                    <label class="floating-label">Name</label>
                                                </div>
                                                <div class="form-group col-md-5 mb-1 d-flex">
                                                    <div class="w-100">
                                                        <input type="number" step="0.01" name="cost_amt[]"
                                                            class="form-control cost_amt" placeholder=" " />
                                                        <label class="floating-label">Cost Amount</label>
                                                    </div>
                                                    <button type="button"
                                                        class="btn btn-danger btn-sm ms-2 removeUtility mt-auto">×</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 mb-1">
                                            <div class="form-group">
                                                <input type="number" step="0.01" name="utility_grand_price"
                                                    class="form-control utility_grand_price" id="utility_grand_price"
                                                    placeholder=" " readonly />
                                                <label for="utility_grand_price" class="floating-label">Total Utility
                                                    Cost</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!--end::Utility Cost-->

                                <!--begin::Factory Overhead Cost-->
                                <div class="border-0 shadow-sm mt-1" id="overheadContainer">
                                    <div
                                        class="bg-success text-white d-flex justify-content-between align-items-center mb-2 px-1 py-1 rounded">
                                        <h6 class="mb-0">Factory Overhead Cost</h6>
                                        <button type="button" id="addOverhead" class="btn btn-light btn-sm text-dark">+
                                            Add Overhead Cost</button>
                                    </div>

                                    <!-- ✅ First Row (with labels) -->
                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="overhead-row row align-items-start g-2">
                                                <div class="form-group col-md-9 mb-1">
                                                    <textarea name="overhead_type[]" class="form-control overhead_type" rows="1" placeholder=" "></textarea>
                                                    <label class="floating-label">Description</label>
                                                </div>
                                                <div class="form-group col-md-3 mb-1 d-flex">
                                                    <div class="w-100">
                                                        <input type="number" step="0.01" name="fo_cost_amt[]"
                                                            class="form-control fo_cost_amt" placeholder=" " />
                                                        <label class="floating-label">Cost Amount</label>
                                                    </div>
                                                    <button type="button"
                                                        class="btn btn-danger btn-sm ms-2 removeOverhead mt-auto">×</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class=" col-md-2 mb-1">
                                            <div class="form-group">
                                                <input type="number" step="0.01" name="overhead_grand_price"
                                                    class="form-control overhead_grand_price" id="overhead_grand_price"
                                                    placeholder=" " readonly />
                                                <label for="overhead_grand_price" class="floating-label">Total Overhead
                                                    Cost</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!--end::Factory Overhead Cost-->

                                <!--begin::Transport Cost-->
                                <div class="border-0 shadow-sm mt-1" id="transportContainer">
                                    <div
                                        class="bg-success text-white d-flex justify-content-between align-items-center mb-2 px-1 py-1 rounded">
                                        <h6 class="mb-0">Transport Cost</h6>
                                        <button type="button" id="addTransport" class="btn btn-light btn-sm text-dark">+
                                            Add Transport Cost</button>
                                    </div>

                                    <!-- ✅ First Row (with labels) -->
                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="transport-row row align-items-start g-2">
                                                <div class="form-group col-md-9 mb-1">
                                                    <input type="text" name="transport_type[]"
                                                        class="form-control transport_type" placeholder=" " />
                                                    <label class="floating-label">Transport Type</label>
                                                </div>
                                                <div class="form-group col-md-3 mb-1 d-flex">
                                                    <div class="w-100">
                                                        <input type="number" step="0.01" name="transport_amt[]"
                                                            class="form-control transport_amt" placeholder=" " />
                                                        <label class="floating-label">Cost Amount</label>
                                                    </div>
                                                    <button type="button"
                                                        class="btn btn-danger btn-sm ms-2 removeTransport mt-auto">×</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 mb-1">
                                            <div class="form-group">
                                                <input type="number" step="0.01" name="transport_grand_price"
                                                    class="form-control transport_grand_price" id="transport_grand_price"
                                                    placeholder=" " readonly />
                                                <label for="transport_grand_price" class="floating-label">Total Transport
                                                    Cost</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!--end::transport Cost-->

                                <!--begin::Quality Control Cost-->
                                <div class="border-0 shadow-sm mt-1" id="qcContainer">
                                    <div
                                        class="bg-success text-white d-flex justify-content-between align-items-center mb-2 px-1 py-1 rounded">
                                        <h6 class="mb-0">Quality Control Cost</h6>
                                        <button type="button" id="addQc" class="btn btn-light btn-sm text-dark">+
                                            Add QC Cost</button>
                                    </div>

                                    <!-- ✅ First Row (with labels) -->
                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="qc-row row g-2 align-items-start">
                                                <div class="form-group col-md-8 mb-1">
                                                    <input type="text" name="test_name[]"
                                                        class="form-control test_name" placeholder=" " />
                                                    <label class="floating-label">Test Name</label>
                                                </div>
                                                <div class="form-group col-md-4 mb-1 d-flex">
                                                    <div class="w-100">
                                                        <input type="number" step="0.01" name="qc_amt[]"
                                                            class="form-control qc_amt" placeholder=" " />
                                                        <label class="floating-label">Cost Amount</label>
                                                    </div>
                                                    <button type="button"
                                                        class="btn btn-danger btn-sm ms-2 removeQc mt-auto">×</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 mb-1">
                                            <div class="form-group">
                                                <input type="number" step="0.01" name="qc_grand_price"
                                                    class="form-control qc_grand_price" id="qc_grand_price"
                                                    placeholder=" " readonly />
                                                <label for="qc_grand_price" class="floating-label">Total QC
                                                    Cost</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Quality Control Cost-->
                            </div>
                            <!--end::Quality Control Cost-->

                            <hr style="border:0; border-top:3px solid rgb(9, 48, 0); margin: 0 0;">

                            <div class="row mt-2">
                                <div class="form-group col-md-3 ms-3 mt-1 mb-2">
                                    <input type="number" step="0.01" name="grand_total"
                                        class="form-control grand_total" id="grand_total" placeholder=" " readonly />
                                    <label for="grand_total" class="floating-label">Grand Total</label>
                                </div>
                                <div class="form-group col-md-3 mt-1 mb-2">
                                    <input type="number" step="0.01" name="final_qty" class="form-control final_qty"
                                        id="final_qty" placeholder=" " required />
                                    <label for="final_qty" class="floating-label">Actual Production Qty</label>
                                </div>
                                <div class="form-group col-md-2 mt-1 mb-2">
                                    <select name="final_unit" class="form-control" id="final_unit">
                                        <option selected disabled>Select Unit</option>
                                        <option value="ml">ml</option>
                                        <option value="gm">gm</option>
                                        <option value="kg">Kg</option>
                                        <option value="ltr">Ltr</option>
                                    </select>
                                    <label for="final_unit" class="floating-label">Unit</label>
                                </div>
                                <div class="form-group col-md-3 mt-1 mb-2">
                                    <input type="number" step="0.01" name="unit_cost" class="form-control unit_cost"
                                        id="unit_cost" placeholder=" " readonly />
                                    <label for="unit_cost" class="floating-label">Cost per Unit</label>
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
    <!-- Batch Number -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.getElementById('date');
            const batchInput = document.getElementById('batch_no');
            const serial = "{{ $newSerial }}";

            dateInput.addEventListener('change', function() {
                if (this.value) {
                    const d = new Date(this.value);
                    const dd = String(d.getDate()).padStart(2, '0');
                    const mm = String(d.getMonth() + 1).padStart(2, '0');
                    const yy = String(d.getFullYear()).slice(-2);
                    batchInput.value = `${dd}${mm}${yy}${serial}`;
                }
            });
        });
    </script>

    <!-- Batch Size -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const exQty = document.getElementById('ex_qty');
            const exUnit = document.getElementById('ex_unit');
            const batchSize = document.getElementById('batch_size');

            function update() {
                batchSize.value = exQty.value && exUnit.value ? exQty.value + ' ' + exUnit.value : '';
            }

            exQty.addEventListener('input', update);
            exUnit.addEventListener('change', update);
        });
    </script>
    <script>
        $(document).ready(function() {

            // ===============================
            // 🔥 AUTOCOMPLETE INIT
            // ===============================
            function initAutocomplete(row) {

                row.find('.ra_name').autocomplete({
                    source: "{{ route('search.item') }}",
                    minLength: 1,
                    select: function(event, ui) {
                        $('.ra_name').val(ui.item.value);
                        $('.ra_item_id').val(ui.item.id);
                        $('.raw_u_price').val(ui.item.unit_price).trigger('input');

                        return false;
                    }
                });

                row.find('.raw_name').autocomplete({
                    source: "{{ route('search.item') }}",
                    minLength: 1,
                    select: function(event, ui) {
                        let currentRow = $(this).closest('.raw-material-row');
                        currentRow.find('.raw_name').val(ui.item.value);
                        currentRow.find('.raw_item_id').val(ui.item.id);
                        currentRow.find('.u_price').val(ui.item.unit_price).trigger('input');

                        return false;
                    }
                });

                row.find('.pack_name').autocomplete({
                    source: "{{ route('search.item') }}",
                    minLength: 1,
                    select: function(event, ui) {
                        let currentRow = $(this).closest('.pack-row');
                        currentRow.find('.pack_name').val(ui.item.value);
                        currentRow.find('.pack_item_id').val(ui.item.id);
                        currentRow.find('.pack_price').val(ui.item.unit_price).trigger('input');

                        return false;
                    }
                });
            }
            // Raw Material Total Price

            $(document).on('input', '#raw_qty, #raw_u_price', function() {

                let qty = parseFloat($('#raw_qty').val()) || 0;
                let price = parseFloat($('#raw_u_price').val()) || 0;

                $('#raw_t_price').val((qty * price).toFixed(2)).trigger('input');
            });

            // Yield %

            $(document).on('input', '#raw_qty, #ex_qty', function() {

                let rawQty = parseFloat($('#raw_qty').val()) || 0;
                let exQty = parseFloat($('#ex_qty').val()) || 0;

                let percent = rawQty > 0 ?
                    (exQty / rawQty) * 100 :
                    0;

                $('#yield_percent').val(percent.toFixed(2));
            });

            initAutocomplete($(document));

            // ===============================
            // 🔥 RAW MATERIAL
            // ===============================

            function calcRawRow(row) {
                let percent = parseFloat(row.find('.used_percent').val()) || 0;
                let ex = parseFloat($('#ex_qty').val()) || 0;
                let price = parseFloat(row.find('.u_price').val()) || 0;

                let qty = (ex * percent) / 100;
                row.find('.used_qty').val(qty.toFixed(3));
                row.find('.t_price').val((qty * price).toFixed(2));
            }

            function calcRawGrand() {
                let total = 0;
                $('.t_price').each(function() {
                    total += parseFloat($(this).val()) || 0;
                });
                $('#raw_grand_price').val(total.toFixed(2)).trigger('input');
            }

            // delegation
            $(document).on('input', '.used_percent,.u_price,#ex_qty', function() {
                let row = $(this).closest('.raw-material-row');
                calcRawRow(row);
                calcRawGrand();
                calculateGrandTotal();
            });

            // add row
            $(document).off('click', '#addRawMaterial').on('click', '#addRawMaterial', function() {

                let row = $('.raw-material-row:first').clone();
                row.find('input').val('');
                $('#rawMaterialsContainer')
                    .find('.raw-material-row:last')
                    .after(row);

                initAutocomplete(row);
            });

            // remove
            $(document).on('click', '.removeRawMaterial', function() {
                if ($('.raw-material-row').length > 1) {
                    $(this).closest('.raw-material-row').remove();
                    calcRawGrand();
                    calculateGrandTotal();
                }
            });


            // ===============================
            // 🔥 PACKAGING
            // ===============================

            function calcPackRow(row) {
                let qty = parseFloat(row.find('.pack_qty').val()) || 0;
                let price = parseFloat(row.find('.pack_price').val()) || 0;
                row.find('.total_price').val((qty * price).toFixed(2));
            }

            function calcPackGrand() {
                let total = 0;
                $('.total_price').each(function() {
                    total += parseFloat($(this).val()) || 0;
                });
                $('#pack_grand_price').val(total.toFixed(2)).trigger('input');
            }

            $(document).on('input', '.pack_qty,.pack_price', function() {
                let row = $(this).closest('.pack-row');
                calcPackRow(row);
                calcPackGrand();
                calculateGrandTotal();
            });

            $(document).off('click', '#addPack').on('click', '#addPack', function() {

                let row = $('.pack-row:first').clone();
                row.find('input').val('');
                $('#packContainer')
                    .find('.pack-row:last')
                    .after(row);
                initAutocomplete(row);
            });

            $(document).on('click', '.removePack', function() {
                if ($('.pack-row').length > 1) {
                    $(this).closest('.pack-row').remove();
                    calcPackGrand();
                    calculateGrandTotal();
                }
            });


            // ===============================
            // 🔥 LABOR
            // ===============================

            function calcLaborRow(row) {
                let d = parseFloat(row.find('.duty_day').val()) || 0;
                let p = parseFloat(row.find('.d_pay').val()) || 0;
                row.find('.total_pay').val((d * p).toFixed(2));
            }

            function calcLaborGrand() {
                let total = 0;
                $('.total_pay').each(function() {
                    total += parseFloat($(this).val()) || 0;
                });
                $('#labor_grand_price').val(total.toFixed(2)).trigger('input');
            }

            $(document).on('input', '.duty_day,.d_pay', function() {
                let row = $(this).closest('.labor-row');
                calcLaborRow(row);
                calcLaborGrand();
                calculateGrandTotal();
            });

            $(document).off('click', '#addLabor').on('click', '#addLabor', function() {

                let row = $('.labor-row:first').clone();
                row.find('input').val('');
                $('#laborContainer')
                    .find('.labor-row:last')
                    .after(row);
            });

            $(document).on('click', '.removeLabor', function() {
                if ($('.labor-row').length > 1) {
                    $(this).closest('.labor-row').remove();
                    calcLaborGrand();
                    calculateGrandTotal();
                }
            });


            // ===============================
            // 🔥 UNIVERSAL COST (utility, overhead, etc)
            // ===============================

            function setupCost(section, amountClass, grandId) {

                const cap = section.charAt(0).toUpperCase() + section.slice(1);

                // =========================
                // Calculate Total Cost
                // =========================
                $(document)
                    .off('input', '.' + amountClass)
                    .on('input', '.' + amountClass, function() {

                        let total = 0;

                        $('.' + amountClass).each(function() {
                            total += parseFloat($(this).val()) || 0;
                        });

                        $('#' + grandId).val(total.toFixed(2));
                        calculateGrandTotal();
                    });

                // =========================
                // Add New Row
                // =========================
                $(document)
                    .off('click', '#add' + cap)
                    .on('click', '#add' + cap, function() {

                        let row = $('.' + section + '-row:first').clone();

                        row.find('input').val('');
                        row.find('textarea').val('');

                        $('#' + section + 'Container')
                            .find('.' + section + '-row:last')
                            .after(row);
                    });

                // =========================
                // Remove Row
                // =========================
                $(document)
                    .off('click', '.remove' + cap)
                    .on('click', '.remove' + cap, function() {

                        if ($('.' + section + '-row').length > 1) {

                            $(this)
                                .closest('.' + section + '-row')
                                .remove();

                            let total = 0;

                            $('.' + amountClass).each(function() {
                                total += parseFloat($(this).val()) || 0;
                            });

                            $('#' + grandId).val(total.toFixed(2));
                            calculateGrandTotal();
                        }
                    });
            }


            // =========================
            // Initialize All Sections
            // =========================
            setupCost('utility', 'cost_amt', 'utility_grand_price');
            setupCost('machine', 'machine_cost_amt', 'depreciation_grand_price');
            setupCost('overhead', 'fo_cost_amt', 'overhead_grand_price');
            setupCost('transport', 'transport_amt', 'transport_grand_price');
            setupCost('qc', 'qc_amt', 'qc_grand_price');
            // ===============================
            // 🔥 GRAND TOTAL
            // ===============================

            function calculateGrandTotal() {

                let total =
                    (+$('#raw_t_price').val() || 0) +
                    (+$('#raw_grand_price').val() || 0) +
                    (+$('#labor_grand_price').val() || 0) +
                    (+$('#pack_grand_price').val() || 0) +
                    (+$('#utility_grand_price').val() || 0) +
                    (+$('#depreciation_grand_price').val() || 0) +
                    (+$('#overhead_grand_price').val() || 0) +
                    (+$('#transport_grand_price').val() || 0) +
                    (+$('#qc_grand_price').val() || 0);

                $('#grand_total').val(total.toFixed(2)).trigger('input');
            }


            // ===============================
            // 🔥 UNIT COST
            // ===============================
            $(document).on('input', '#final_qty,#grand_total', function() {
                let g = parseFloat($('#grand_total').val()) || 0;
                let q = parseFloat($('#final_qty').val()) || 0;
                $('#unit_cost').val(q ? (g / q).toFixed(2) : '');
            });

        });
    </script>
@endpush
