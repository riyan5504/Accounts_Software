@extends('backend.master')

@section('content')
    <!--begin::App Content Header-->
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row bg-info opasity-50 rounded">
                <div class="col-sm-6">
                    <h3 class="mb-0">Customer List</h3>
                </div>
                <div class="col-sm-6">
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
                                Entry
                            </a>
                        </li>

                        <li class="breadcrumb-item">
                            <a href="{{ url('/sales/list') }}"
                                class="{{ request()->is('sales/list') ? 'text-primary fw-bold' : 'text-dark' }}">
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
                            <a href="{{ route('sales.customer.list') }}"
                                class="{{ request()->routeIs('sales.customer.list') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Customer List
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('sales.customer.received.create') }}"
                                class="{{ request()->routeIs('sales.customer.received.create') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Customer Payment
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
                    <div class="card card-primary card-outline">
                        <div
                            class="bg-success text-white d-flex justify-content-between align-items-center px-1 py-1 rounded">
                            <h5>Customer List</h5>
                            <a href="{{ route('sales.customer.add') }}" class="btn btn-light btn-sm text-dark fw-bold">
                                + Add Customer
                            </a>
                        </div>
                        <div class="card mt-3 ms-1 me-1 mb-3">
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
                                                <td>{{ $customer->opening_balance }}</td>
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
    </div>
    <!--end::App Content-->
@endsection
