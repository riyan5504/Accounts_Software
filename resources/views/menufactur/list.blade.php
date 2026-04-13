@extends('backend.master')

@section('content')
    <!--begin::App Content Header-->
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Production List</h3>
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
                            <a href="{{ url('/production/product/add') }}"
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
            <div>
                <form method="GET" class="row g-2 mb-2">
                    <div class="col-md-3">
                        <input type="text" name="product" value="{{ request('product') }}" class="form-control"
                            placeholder="Product name or Batch No">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-success w-100">Filter</button>
                    </div>

                    <div class="col-md-2">
                        <a href="{{ url('/production/list') }}" class="btn btn-secondary w-100">Reset</a>
                    </div>
                </form>
                <div class="row mb-2">
                    <div class="col-md-4">
                        <div class="alert alert-primary py-1 mb-0">
                            Total Batch: <strong>{{ $summary['total_batch'] }}</strong>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="alert alert-success py-1 mb-0">
                            Total Qty: <strong>{{ number_format($summary['total_qty'], 2) }}</strong>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="alert alert-warning py-1 mb-0">
                            Total Cost: <strong>৳ {{ number_format($summary['total_cost'], 2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!--begin::Row-->
            <div class="row g-4">
                <!--begin::Col-->
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="card mt-3 ms-1 me-1 mb-3">
                            <div class="card-body p-0">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Sl. No.</th>
                                            <th>Date</th>
                                            <th>Batch No</th>
                                            <th>Product Name</th>
                                            <th>Batch Size</th>
                                            <th>Production Quantity</th>
                                            <th>Total Amount</th>
                                            <th>Cost/Unit</th>
                                            <th style="text-align: center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($productions as $production)
                                            <tr class="align-middle">
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>{{ $production->date->format('d-m-y') }}</td>
                                                <td>{{ $production->batch_no }}</td>
                                                <td>{{ $production->name }}</td>
                                                <td>{{ $production->batch_size }}</td>
                                                <td>{{ $production->final_qty }} {{ $production->final_unit }}</td>
                                                <td>{{ $production->grand_total }}</td>
                                                <td>{{ $production->unit_cost }}</td>
                                                <td style="text-align: center">
                                                    <a href="{{ url('/production/details/' . $production->id) }}"
                                                        class="btn ms-0 me-0">
                                                        <i class="bi bi-eye text-success"></i>
                                                    </a>

                                                    <a href="{{ url('/production/edit/' . $production->id) }}"
                                                        class="btn ms-0 me-0">
                                                        <i class="bi bi-pencil text-primary"></i>
                                                    </a>
                                                    <a href="{{ url('/production/delete/' . $production->id) }}"
                                                        class="btn me-0 ms-0"
                                                        onclick="return confirm('আপনি কি নিশ্চিত? production এবং সব Journal Entry ডিলিট হবে!');">
                                                        <i class="bi bi-trash text-danger"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center text-muted py-3">
                                                    No production found
                                                </td>
                                            </tr>
                                        @endforelse
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
