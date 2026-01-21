@extends('backend.master')

@section('content')
    <!--begin::App Content-->
    <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Quick Example-->
            <div class="card card-primary card-outline mb-2" style="padding-bottom: 0px">
                <!--begin::Header-->
                <div class="card-header">
                    <div class="card-title">Account Entry</div>
                </div>
                <!--end::Header-->
                <!--begin::Form-->
                <form action="{{ url('/account/store') }}" method="POST">
                    @csrf
                    <!--begin::Body-->
                    <div class="card-body">
                        <div id="accountsContainer" class="border-0 shadow-sm">
                            <div
                                class="bg-success text-white d-flex justify-content-between align-accounts-center mb-3 px-1 py-1 rounded">
                                <h4 class="mb-0 ms-1">Entry Account Details</h4>
                            </div>
                            <div class="account-row row g-2 align-accounts-end mb-2">
                                {{-- <div class="form-group col-sm-6 col-md-2 mb-1">
                                    <input type="text" name="account_code" class="form-control account_code"
                                        placeholder=" " />
                                    <label for="account_code" class="floating-label">Account Code</label>
                                </div> --}}
                                <div class="form-group col-sm-6 col-md-3 mb-1">
                                    <input type="text" name="account_name" class="form-control account_name"
                                        placeholder=" " required />
                                    <label for="account_name" class="floating-label">Account Name</label>
                                </div>
                                <div class="form-group col-sm-6 col-md-3 mb-1">
                                    <input type="text" name="ac_cat" class="form-control ac_cat"
                                        placeholder=" " required />
                                    <label for="ac_cat" class="floating-label">Account Category</label>
                                </div>
                                <div class="form-group col-sm-6 col-md-3 mb-1">
                                    <select name="ac_type" class="form-control ac_type" placeholder=" ">
                                        <option selected disabled>Select Account Type</option>
                                        <option value="asset">Asset</option>
                                        <option value="liability">Liability</option>
                                        <option value="equity">Equity</option>
                                        <option value="revenue">Revenue</option>
                                        <option value="expense">Expense</option>
                                        <option value="production">Production</option>
                                    </select>
                                    <label for="unit" class="floating-label">Account Type</label>
                                </div>
                                <div class="form-group col-sm-6 col-md-3 mb-1">
                                    <input type="number" step="0.01" name="op_balance" class="form-control op_balance"
                                       value="0" placeholder=" " required />
                                    <label for="op_balance" class="floating-label">Opening Balance</label>
                                </div>
                            </div>
                        </div>
                        <!--end::Body-->
                        <!--begin::Footer-->
                        <div class="card-footer text-center">
                            <button type="submit" class="btn btn-primary">💾 Save</button>
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
    <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row g-4">
                <!--begin::Col-->
                <div class="col-md-12">
                    <dev class="card card-primary card-outline">
                        <dev class="ms-2 pt-3">
                            <h5>Account List</h5>
                        </dev>
                        <div class="card mt-3 ms-1 me-1 mb-3">
                            <div class="card-body p-0">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Sl. No.</th>
                                            <th>Account Name</th>
                                            <th>Account Category</th>
                                            <th>Account Type</th>
                                            <th>Opening Balance</th>
                                            <th>Entry Date</th>
                                            <th style="text-align: center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($accounts as $account)
                                            <tr class="align-middle">
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>{{ $account->account_name }}</td>
                                                <td>{{ $account->ac_cat}}</td>
                                                <td>{{ ucfirst($account->ac_type) }}</td>
                                                <td>{{ $account->op_balance }}</td>
                                                <td>{{ $account->created_at->format('d-m-y') }}</td>
                                                <td style="text-align: center">
                                                    <a href="{{ url('/account/edit/' . $account->id) }}"
                                                        class="btn ms-0 me-0">
                                                        <i class="bi bi-pencil text-primary"></i>
                                                    </a>
                                                    <a href="{{ url('/account/delete/' . $account->id) }}"
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
                    </dev>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
@endsection
