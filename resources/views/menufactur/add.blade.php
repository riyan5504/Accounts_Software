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
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Production Entry</li>
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
                        <form action="{{ url('/production/product/store') }}" method="POST">
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
                                    <div class="form-group col-md-2 mb-0">
                                        <input type="date" name="date" class="form-control date"
                                            value="{{ date('Y-m-d') }}" id="date" placeholder=" " required />
                                        <label for="date" class="floating-label">Production Date</label>
                                    </div>

                                    <div class="form-group col-md-2 mb-0">
                                        <input type="text" name="batch_no" class="form-control batch_no" id="batch_no"
                                            value="{{ $nextBatch }}" placeholder=" " readonly />
                                        <label for="batch_no" class="floating-label">Batch Number</label>
                                    </div>
                                    <div class="form-group col-md-1 mb-0">
                                        <input type="text" name="batch_size" class="form-control batch_size"
                                            id="batch_size" placeholder=" " readonly />
                                        <label for="batch_size" class="floating-label">Batch Size</label>
                                    </div>
                                    <div class="form-group col-md-2 mb-0">
                                        <input type="number" step="0.01" name="raw_t_price"
                                            class="form-control raw_t_price" id="raw_t_price" placeholder=" " readonly />
                                        <label for="raw_t_price" class="floating-label">Total Price</label>
                                    </div>
                                    <div class="form-group col-md-2 mb-0">
                                        <input type="number" step="0.01" name="yield_percent"
                                            class="form-control yield_percent" id="yield_percent" placeholder=" "
                                            readonly />
                                        <label for="yield_percent" class="floating-label">Extract Yield
                                            (%)</label>
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
                                    <div class="form-group col-md-2 mb-0">
                                        <input type="number" step="0.01" name="raw_u_price"
                                            class="form-control raw_u_price" id="raw_u_price" placeholder=" " required />
                                        <label for="raw_u_price" class="floating-label">Unit Price</label>
                                    </div>
                                    <div class="form-group col-md-1 mb-0">
                                        <input type="number" name="pulp" class="form-control pulp" id="pulp"
                                            placeholder=" " />
                                        <label for="pulp" class="floating-label">Pulp</label>
                                    </div>
                                    <div class="form-group col-md-1 mb-0">
                                        <select name="pulp_unit" class="form-control pulp_unit" id="pulp_unit">
                                            <option selected disabled>Select Unit</option>
                                            <option value="ml">ml</option>
                                            <option value="gm">gm</option>
                                            <option value="kg">Kg</option>
                                            <option value="ltr">Ltr</option>
                                        </select>
                                        <label for="pulp_unit" class="floating-label">Unit</label>
                                    </div>
                                    <div class="form-group col-md-2 mb-0">
                                        <input type="number" name="yield" class="form-control yield" id="yield"
                                            placeholder=" " />
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
                                    <div class="form-group col-md-2 mb-0">
                                        <input type="number" step="0.01" name="ex_qty" class="form-control ex_qty"
                                            id="ex_qty" placeholder=" " required />
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
                                                    <select name="ch_unit" class="form-control ch_unit" id="ch_unit">
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
                                                </div>
                                                <div class="form-group col-md-2 mb-1">
                                                    <input type="text" name="pack_size[]"
                                                        class="form-control pack_size" placeholder=" " />
                                                    <label class="floating-label">Size</label>
                                                </div>
                                                <div class="form-group col-md-2 mb-1">
                                                    <input type="number" name="pack_qty[]"
                                                        class="form-control pack_qty" placeholder=" " />
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
                                                    <input type="number" name="d_pay[]"
                                                        class="form-control d_pay" placeholder=" " />
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

            function initAutocomplete(row) {

                // 🔹 RAW Name autocomplete
                row.find('.raw_name').autocomplete({
                    source: "{{ route('item.search') }}",
                    minLength: 1,
                    select: function(event, ui) {
                        row.find('.raw_name').val(ui.item.value);
                        row.find('.u_price').val(ui.item.unit_price).trigger('input');
                        return false;
                    }
                });

                // 🔹 PACK Name autocomplete
                row.find('.pack_name').autocomplete({
                    source: "{{ route('item.search') }}",
                    minLength: 1,
                    select: function(event, ui) {
                        row.find('.pack_name').val(ui.item.value);
                        row.find('.pack_price').val(ui.item.unit_price).trigger('input');
                        return false;
                    }
                });
            }

            // প্রথম row
            initAutocomplete($('.raw-material-row'));
            initAutocomplete($('.pack-row'));

            // নতুন raw row add হলে
            $('#addRawMaterial').on('click', function() {
                setTimeout(function() {
                    initAutocomplete($('#rawMaterialsContainer .raw-material-row:last'));
                }, 50);
            });

            // নতুন pack row add হলে
            $('#addPack').on('click', function() {
                setTimeout(function() {
                    initAutocomplete($('#packContainer .pack-row:last'));
                }, 50);
            });

        });
    </script>


    <!-- Raw Material Main Calculation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const rawQty = document.getElementById('raw_qty');
            const rawUnitPrice = document.getElementById('raw_u_price');
            const rawTotal = document.getElementById('raw_t_price');
            const exQty = document.getElementById('ex_qty');
            const yieldPercent = document.getElementById('yield_percent');

            const container = document.getElementById('rawMaterialsContainer');
            const addBtn = document.getElementById('addRawMaterial');
            const rawGrand = document.getElementById('raw_grand_price');

            function calcRawTotal() {
                rawTotal.value = ((parseFloat(rawQty.value) || 0) * (parseFloat(rawUnitPrice.value) || 0)).toFixed(
                    2);
                calcYield();
                refreshRows();
            }

            function calcYield() {
                let r = parseFloat(rawQty.value) || 0;
                let e = parseFloat(exQty.value) || 0;
                yieldPercent.value = r ? ((e / r) * 100).toFixed(2) : '';
                refreshRows();
            }

            rawQty.addEventListener('input', calcRawTotal);
            rawUnitPrice.addEventListener('input', calcRawTotal);
            exQty.addEventListener('input', calcYield);

            // price set from dropdown
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('raw_name')) {
                    const row = e.target.closest('.raw-material-row');
                    const price = e.target.selectedOptions[0].getAttribute('data-price') || '';
                    const priceInput = row.querySelector('.u_price');
                    priceInput.value = price;
                    priceInput.dispatchEvent(new Event('input'));
                }
            });

            function attachRow(row) {
                const usedPercent = row.querySelector('.used_percent');
                const usedQty = row.querySelector('.used_qty');
                const uPrice = row.querySelector('.u_price');
                const tPrice = row.querySelector('.t_price');

                function recalc() {
                    let percent = parseFloat(usedPercent.value) || 0;
                    let ex = parseFloat(exQty.value) || 0;
                    let price = parseFloat(uPrice.value) || 0;
                    let qty = (ex * percent) / 100;
                    usedQty.value = qty.toFixed(3);
                    tPrice.value = (qty * price).toFixed(2);
                    calcGrand();
                }

                usedPercent.addEventListener('input', recalc);
                uPrice.addEventListener('input', recalc);
                exQty.addEventListener('input', recalc);
            }

            function calcGrand() {
                let total = 0;
                container.querySelectorAll('.t_price').forEach(i => {
                    total += parseFloat(i.value) || 0;
                });
                rawGrand.value = total.toFixed(2);
                $('#raw_grand_price').trigger('input');
            }

            function refreshRows() {
                container.querySelectorAll('.raw-material-row').forEach(r => {
                    r.querySelector('.used_percent').dispatchEvent(new Event('input'));
                });
            }

            addBtn.addEventListener('click', function() {
                const firstRow = container.querySelector('.raw-material-row');
                const newRow = firstRow.cloneNode(true);
                newRow.querySelectorAll('label').forEach(l => l.remove());
                newRow.querySelectorAll('input').forEach(i => i.value = '');
                firstRow.parentElement.appendChild(newRow);
                attachRow(newRow);
            });

            container.addEventListener('click', function(e) {
                if (e.target.classList.contains('removeRawMaterial')) {
                    const rows = container.querySelectorAll('.raw-material-row');
                    if (rows.length > 1) {
                        e.target.closest('.raw-material-row').remove();
                        calcGrand();
                    }
                }
            });

            attachRow(container.querySelector('.raw-material-row'));
        });
    </script>

    <!-- Labor -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('laborContainer');
            const addBtn = document.getElementById('addLabor');
            const grand = document.getElementById('labor_grand_price');

            function attach(row) {
                const day = row.querySelector('.duty_day');
                const pay = row.querySelector('.d_pay');
                const total = row.querySelector('.total_pay');

                function recalc() {
                    total.value = ((parseFloat(day.value) || 0) * (parseFloat(pay.value) || 0)).toFixed(2);
                    calcGrand();
                }
                day.addEventListener('input', recalc);
                pay.addEventListener('input', recalc);
            }

            function calcGrand() {
                let sum = 0;
                container.querySelectorAll('.total_pay').forEach(i => sum += parseFloat(i.value) || 0);
                grand.value = sum.toFixed(2);
                $('#labor_grand_price').trigger('input');
            }

            addBtn.addEventListener('click', function() {
                const first = container.querySelector('.labor-row');
                const row = first.cloneNode(true);
                row.querySelectorAll('label').forEach(l => l.remove());
                row.querySelectorAll('input').forEach(i => i.value = '');
                first.parentElement.appendChild(row);
                attach(row);
            });

            container.addEventListener('click', function(e) {
                if (e.target.classList.contains('removeLabor')) {
                    const rows = container.querySelectorAll('.labor-row');
                    if (rows.length > 1) {
                        e.target.closest('.labor-row').remove();
                        calcGrand();
                    }
                }
            });

            attach(container.querySelector('.labor-row'));
        });
    </script>

    <!-- Pack price + calculation -->
    <script>
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('pack_name')) {
                const row = e.target.closest('.pack-row');
                const price = e.target.selectedOptions[0].getAttribute('data-price') || '';
                const priceInput = row.querySelector('.pack_price');
                priceInput.value = price;
                priceInput.dispatchEvent(new Event('input'));
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('packContainer');
            const addBtn = document.getElementById('addPack');
            const grand = document.getElementById('pack_grand_price');

            function attach(row) {
                const qty = row.querySelector('.pack_qty');
                const price = row.querySelector('.pack_price');
                const total = row.querySelector('.total_price');

                function recalc() {
                    total.value = ((parseFloat(qty.value) || 0) * (parseFloat(price.value) || 0)).toFixed(2);
                    calcGrand();
                }
                qty.addEventListener('input', recalc);
                price.addEventListener('input', recalc);
            }

            function calcGrand() {
                let sum = 0;
                container.querySelectorAll('.total_price').forEach(i => sum += parseFloat(i.value) || 0);
                grand.value = sum.toFixed(2);
                $('#pack_grand_price').trigger('input');
            }

            addBtn.addEventListener('click', function() {
                const first = container.querySelector('.pack-row');
                const row = first.cloneNode(true);
                row.querySelectorAll('label').forEach(l => l.remove());
                row.querySelectorAll('input').forEach(i => i.value = '');
                first.parentElement.appendChild(row);
                attach(row);
            });

            container.addEventListener('click', function(e) {
                if (e.target.classList.contains('removePack')) {
                    const rows = container.querySelectorAll('.pack-row');
                    if (rows.length > 1) {
                        e.target.closest('.pack-row').remove();
                        calcGrand();
                    }
                }
            });

            attach(container.querySelector('.pack-row'));
        });
    </script>

    <!-- Universal cost sections -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            function setupCostSection(config) {
                const container = document.getElementById(config.containerId);
                const addBtn = document.getElementById(config.addBtnId);
                const grand = document.getElementById(config.grandId);

                function calcGrand() {
                    let sum = 0;
                    container.querySelectorAll('.' + config.amountClass).forEach(i => {
                        sum += parseFloat(i.value) || 0;
                    });
                    grand.value = sum.toFixed(2);
                    $('#' + config.grandId).trigger('input');
                }

                function attachRow(row) {
                    const amt = row.querySelector('.' + config.amountClass);
                    amt.addEventListener('input', calcGrand);
                }

                addBtn.addEventListener('click', function() {
                    const first = container.querySelector('.' + config.rowClass);
                    const row = first.cloneNode(true);
                    row.querySelectorAll('label').forEach(l => l.remove());
                    row.querySelectorAll('input').forEach(i => i.value = '');
                    first.parentElement.appendChild(row);
                    attachRow(row);
                });

                container.addEventListener('click', function(e) {
                    if (e.target.classList.contains(config.removeClass)) {
                        const rows = container.querySelectorAll('.' + config.rowClass);
                        if (rows.length > 1) {
                            e.target.closest('.' + config.rowClass).remove();
                            calcGrand();
                        }
                    }
                });

                attachRow(container.querySelector('.' + config.rowClass));
            }

            // Utility
            setupCostSection({
                containerId: 'utilityContainer',
                addBtnId: 'addUtility',
                rowClass: 'utility-row',
                amountClass: 'cost_amt',
                removeClass: 'removeUtility',
                grandId: 'utility_grand_price'
            });

            // Depreciation / Machine
            setupCostSection({
                containerId: 'machineContainer',
                addBtnId: 'addMachine',
                rowClass: 'machine-row',
                amountClass: 'machine_cost_amt',
                removeClass: 'removeMachine',
                grandId: 'depreciation_grand_price'
            });

            // Overhead
            setupCostSection({
                containerId: 'overheadContainer',
                addBtnId: 'addOverhead',
                rowClass: 'overhead-row',
                amountClass: 'fo_cost_amt',
                removeClass: 'removeOverhead',
                grandId: 'overhead_grand_price'
            });

            // Transport
            setupCostSection({
                containerId: 'transportContainer',
                addBtnId: 'addTransport',
                rowClass: 'transport-row',
                amountClass: 'transport_amt',
                removeClass: 'removeTransport',
                grandId: 'transport_grand_price'
            });

            // QC
            setupCostSection({
                containerId: 'qcContainer',
                addBtnId: 'addQc',
                rowClass: 'qc-row',
                amountClass: 'qc_amt',
                removeClass: 'removeQc',
                grandId: 'qc_grand_price'
            });

        });
    </script>


    <!-- Final Grand Total -->
    <script>
        $(document).ready(function() {

            function grandTotal() {
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

                $('#grand_total').val(total.toFixed(2));
            }

            $(document).on('input',
                '#raw_t_price,#raw_grand_price,#labor_grand_price,#pack_grand_price,#utility_grand_price,#depreciation_grand_price,#overhead_grand_price,#transport_grand_price,#qc_grand_price',
                grandTotal);
        });
    </script>

    <!-- Unit Cost -->
    <script>
        $(document).ready(function() {
            function unitCost() {
                let g = parseFloat($('#grand_total').val()) || 0;
                let q = parseFloat($('#final_qty').val()) || 0;
                $('#unit_cost').val(q ? (g / q).toFixed(2) : '');
            }
            $('#final_qty,#grand_total').on('input', unitCost);
        });
    </script>
@endpush
