@extends('backend.master')

@section('content')
    <!--begin::App Content Header-->
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row bg-info opasity-50 rounded">
                <div class="col-sm-4">
                    <h3 class="mb-0 mt-0">Vendor Entry</h3>
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
                            <a href="{{ route('purchase.vendor.list') }}"
                                class="{{ request()->routeIs('purchase.vendorlist') ? 'text-primary fw-bold' : 'text-dark' }}">
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
            <div class="card card-primary card-outline mb-2">
                <!--begin::Form-->
                <form action="{{ route('purchase.vendor.store') }}" method="POST">
                    @csrf
                    <!--begin::Body-->
                    <div class="card-body">
                        <!--start::vendor-->
                        <div id="vendorContainer" class="border-0 shadow-sm ms-0">
                            <div
                                class="bg-success text-white d-flex justify-content-between align-vendors-center mb-3 px-1 py-1 rounded">
                                <h4 class="mb-0 ms-1">Add Vendor Details</h4>
                            </div>
                            <!-- ✅ Proper Row Structure -->
                            <div class="row g-2 align-vendor-end mb-2">
                                <div class="form-group mb-1 col-md-5">
                                    <input type="text" name="v_name" class="form-control v_name" placeholder=" " required />
                                    <label for="v_name" class="floating-label">Vendor Name</label>
                                </div>

                                <div class="form-group mb-1 col-md-3">
                                    <input type="text" name="phone" class="form-control phone" placeholder=" " required />
                                    <label for="phone" class="floating-label">Phone</label>
                                </div>
                                <div class="form-group mb-1 col-md-4">
                                    <input type="email" name="email" class="form-control email" placeholder=" "/>
                                    <label for="email" class="floating-label">Email</label>
                                </div>
                                <div class="form-group mb-1 col-md-8">
                                    <textarea name="address" class="form-control address" rows="1" placeholder=" " required></textarea>
                                    <label for="address" class="floating-label">Address</label>
                                </div>
                                <div class="form-group col-sm-4 col-md-4 mb-1">
                                    <input type="number" name="opening_balance" class="form-control opening_balance"
                                        placeholder=" " />
                                    <label for="opening_balance" class="floating-label">Opening Balance</label>
                                </div>
                            </div>
                        </div>
                        <!--begin::Footer-->
                        <div class="card-footer p-0">
                            <button type="submit" class="btn btn-primary mt-1">Save</button>
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
    <!--begin::App Content-->
        <div class="m-1">
            <!--begin::Row-->
            <div class="row g-4">
                <!--begin::Col-->
                <div class="col-md-12">
                    <div class="card card-outline">
                        <div
                            class="bg-success text-white px-1 py-1 rounded">
                            <h5>Vendor List</h5>
                        </div>
                        <div class="card">
                            <div class="card-body p-0">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 70px">Sl. No.</th>
                                            <th style="width: 200px">Vendor Name</th>
                                            <th style="width: 100px">Phone Number</th>
                                            <th style="width: 150px">Email</th>
                                            <th style="width: 220px">Address</th>
                                            <th style="width: 100px">Opening Balance</th>
                                            <th style="text-align:center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($vendors as $vendor)
                                            <tr class="align-middle">
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>{{ $vendor->v_name }}</td>
                                                <td>{{ $vendor->phone }}</td>
                                                <td>{{ $vendor->email }}</td>
                                                <td>{{ $vendor->address }}</td>
                                                <td style="text-align: end">{{ $vendor->opening_balance }}</td>
                                                <td style="text-align: center">
                                                    <a href="{{ route('purchase.vendor.edit', $vendor->id) }}"
                                                        class="btn ms-0 me-0">
                                                        <i class="bi bi-pencil text-primary"></i>
                                                    </a>
                                                    <a href="{{ route('purchase.vendor.delete', $vendor->id) }}"
                                                        class="btn me-0 ms-0">
                                                        <i class="bi bi-trash text-danger"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    </div>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
@endsection
