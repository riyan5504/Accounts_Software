@extends('backend.master')

@section('content')
    <!--begin::App Content Header-->
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row bg-info opasity-50 rounded">
                <div class="col-sm-4">
                    <h3 class="mb-0 mt-0">Customer Entry</h3>
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
                            <a href="{{ url('/sales/entry') }}"
                                class="{{ request()->is('sales/entry') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Sales Entry
                            </a>
                        </li>

                        <li class="breadcrumb-item">
                            <a href="{{ url('/sales/list') }}"
                                class="{{ request()->is('sales/list') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Sales List
                            </a>
                        </li>

                        <li class="breadcrumb-item">
                            <a href="{{ route('sales.customer.list') }}"
                                class="{{ request()->routeIs('customer.list') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Customer List
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('sales.customer.received.create') }}"
                                class="{{ request()->routeIs('customer.received.create') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Customer Received
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
                <form action="{{ route('sales.customer.store') }}" method="POST">
                    @csrf
                    <!--begin::Body-->
                    <div class="card-body">
                        <!--start::customer-->
                        <div id="customerContainer" class="border-0 shadow-sm ms-0">
                            <div
                                class="bg-success text-white d-flex justify-content-between align-customers-center mb-3 px-1 py-1 rounded">
                                <h4 class="mb-0 ms-1">Add Customer Details</h4>
                            </div>
                            <!-- ✅ Proper Row Structure -->
                            <div class="row g-2 align-customer-end mb-2">
                                <div class="form-group mb-1 col-md-5">
                                    <input type="text" name="c_name" class="form-control c_name" placeholder=" " required />
                                    <label for="c_name" class="floating-label">Customer Name</label>
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
                            <h5>Customer List</h5>
                        </div>
                        <div class="card">
                            <div class="card-body p-0">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 6%">Sl. No.</th>
                                            <th style="width: 16%">Customer Name</th>
                                            <th style="width: 11%">Phone Number</th>
                                            <th style="width: 16%">Email</th>
                                            <th style="width: 30%">Address</th>
                                            <th style="width: 10%">Opening Balance</th>
                                            <th style="text-align:center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($customers as $customer)
                                            <tr class="align-middle">
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>{{ $customer->c_name }}</td>
                                                <td>{{ $customer->phone }}</td>
                                                <td>{{ $customer->email }}</td>
                                                <td>{{ $customer->address }}</td>
                                                <td style="text-align: end">{{ $customer->opening_balance }}</td>
                                                <td style="text-align: center">
                                                    <a href="{{ route('sales.customer.edit', $customer->id) }}"
                                                        class="btn ms-0 me-0">
                                                        <i class="bi bi-pencil text-primary"></i>
                                                    </a>
                                                    <a href="{{ route('sales.customer.delete', $customer->id) }}"
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
