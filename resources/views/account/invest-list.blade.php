@extends('backend.master')

@section('content')
    <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row g-4">
                <!--begin::Col-->
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="bg-info ps-3 rounded">
                            <h5>investment List</h5>
                        </div>
                        <div class="card mt-3 ms-1 me-1 mb-3">
                            <div class="card-body p-0">
                                <table class="table table-sm" id="investmentTable">
                                    <thead>
                                        <tr>
                                            <th>Sl. No.</th>
                                            <th>Partner Name</th>
                                            <th>Investment Type</th>
                                            <th>Amount</th>
                                            <th>Entry Date</th>
                                            <th style="text-align: center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($investments as $investment)
                                            <tr class="align-middle">
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>{{ $investment->partner->p_name }}</td>
                                                <td>{{ ucfirst($investment->invest_type) }}</td>
                                                <td>{{ $investment->amount }}</td>
                                                <td>{{ $investment->date->format('d-m-y') }}</td>
                                                <td style="text-align: center">
                                                    <a href="{{ url('/account/investment/edit/' . $investment->id) }}"
                                                        class="btn ms-0 me-0">
                                                        <i class="bi bi-pencil text-primary"></i>
                                                    </a>
                                                    <a href="{{ url('/account/investment/delete/' . $investment->id) }}"
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
@endsection