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
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Purchase Entry</li>
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
                <form>
                    <!--begin::Body-->
                    <div class="card-body">
                        <!--start::vendor-->
                        <div id="vendorsContainer" class="border-0 shadow-sm ms-0">
                            <div
                                class="bg-success text-white d-flex justify-content-between align-vendors-center mb-2 px-1 py-1 rounded">
                                <h4 class="mb-0 ms-1">Vendor Details</h4>
                            </div>
                            <!-- ✅ Proper Row Structure -->
                            <div class="row g-2 align-vendors-end mb-2">
                                <div class="col-md-5">
                                    <label for="vendor_name" class="form-label">Vendor Name</label>
                                    <input type="text" name="vendor_name" class="form-control vendor_name"
                                        id="vendor_name" required>
                                </div>

                                <div class="col-md-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control phone" id="phone">
                                </div>
                                <div class="col-md-4">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control email" id="email">
                                </div>
                                <div class="col-md-8">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea name="address" class="form-control address" id="address" rows="1" required></textarea>
                                </div>
                                <div class="col-md-2">
                                    <label for="date" class="form-label">Date</label>
                                    <input type="date" name="date" class="form-control date" id="date" required>
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label for="purchase_no" class="form-label">Invoice Number</label>
                                    <input type="text" name="purchase_no" class="form-control" id="purchase_no"
                                        value="{{ $newPurchaseNo ?? '' }}" readonly />
                                </div>
                            </div>
                        </div>
                        <!--start::vendor-->
                        <div id="itemsContainer" class="border-0 shadow-sm">
                            <div
                                class="bg-success text-white d-flex justify-content-between align-items-center mb-2 px-1 py-1 rounded">
                                <h4 class="mb-0 ms-1">Item Details</h4>
                                <button type="button" id="addItem" class="btn btn-light btn-sm text-dark fw-bold">
                                    + Add Item
                                </button>
                            </div>

                            <!-- ✅ Proper Row Structure -->
                            <div class="item-row row g-2 align-items-end mb-2">
                                <div class="col-sm-6 col-md-3">
                                    <label for="exampleInputEmail1" class="form-label">Email address</label>
                                    <input type="" class="form-control" id="exampleInputEmail1"
                                        aria-describedby="emailHelp" />
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputPassword1" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="exampleInputPassword1" />
                                </div>
                                <div class="input-group mb-3">
                                    <input type="file" class="form-control" id="inputGroupFile02" />
                                    <label class="input-group-text" for="inputGroupFile02">Upload</label>
                                </div>
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="exampleCheck1" />
                                    <label class="form-check-label" for="exampleCheck1">Check me out</label>
                                </div>
                            </div>
                        </div>
                        <!--end::Body-->
                        <!--begin::Footer-->
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
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
