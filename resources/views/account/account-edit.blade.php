@extends('backend.master')

@section('content')
    <!--begin::App Content-->
    <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Quick Example-->
            <div class="card card-primary card-outline mb-2" style="padding-bottom: 0px">
                <!--begin::Form-->
                <form action="{{ route('account.update', $account->id) }}" method="POST">
                    @csrf
                    <!--begin::Body-->
                    <div class="card-body">
                        <div id="accountsContainer" class="border-0 shadow-sm">
                            <div
                                class="bg-success text-white d-flex justify-content-between align-accounts-center mb-3 px-1 py-1 rounded">
                                <h5 class="mb-0 ms-1">Edit Account Details</h5>
                                <a href="{{route('account.trash')}}" class="btn btn-warning">
                                    <i class="fas fa-trash"></i>
                                    ({{ \App\Models\Account::onlyTrashed()->count() }})
                                </a>
                            </div>
                            <div class="account-row row g-2 align-accounts-end mb-2">
                                <div class="form-group col-sm-6 col-md-3 mb-1">
                                    <input type="text" name="account_name" value="{{$account->account_name}}" class="form-control account_name"
                                        placeholder=" " required />
                                    <label for="account_name" class="floating-label">Account Name</label>
                                </div>
                                <div class="form-group col-sm-6 col-md-3 mb-1">
                                    <select name="ac_type" class="form-control ac_type" placeholder=" ">
                                        <option value="asset" @if ($account->ac_type == 'asset')
                                            selected
                                        @endif>Asset</option>
                                        <option value="liability" @if ($account->ac_type == 'liability')
                                            selected
                                        @endif>Liability</option>
                                        <option value="equity" @if ($account->ac_type == 'equity')
                                            selected
                                        @endif>Equity</option>
                                        <option value="revenue" @if ($account->ac_type == 'revenue')
                                            selected
                                        @endif>Revenue</option>
                                        <option value="expense" @if ($account->ac_type == 'expense')
                                            selected
                                        @endif>Expense</option>
                                    </select>
                                    <label for="unit" class="floating-label">Account Type</label>
                                </div>
                                <div class="form-group col-sm-6 col-md-3 mb-1">
                                    <input type="text" name="ac_cat" class="form-control ac_cat" value="{{$account->ac_cat}}" placeholder=" " required />
                                    <label for="ac_cat" class="floating-label">Account Category</label>
                                </div>

                                <div class="form-group col-sm-6 col-md-3 mb-1">
                                    <input type="number" step="0.01" name="op_balance" value="{{$account->op_balance ?? 0}}" class="form-control op_balance" placeholder=" " required />
                                    <label for="op_balance" class="floating-label">Opening Balance</label>
                                </div>
                            </div>
                        </div>
                        <!--end::Body-->
                        <!--begin::Footer-->
                        <div class="card-footer text-center">
                            <button type="submit" class="btn btn-primary">💾 Update</button>
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
                    <div class="card card-primary card-outline">
                        <div class="bg-info ps-3">
                            <h5>Account List</h5>
                        </div>
                        <div class="card-header bg-secondary py-2 mt-1">
                            <div class="d-flex align-items-center flex-nowrap gap-2">
                                <div id="customPagination" class="toolbar-divider d-flex gap-1"></div>

                                <div class="form-check toolbar-divider mb-0">
                                    <input class="form-check-input" type="checkbox" id="showAll">
                                    <label class="form-check-label" for="showAll">Show all</label>
                                </div>

                                <div class="d-flex toolbar-divider align-items-center">
                                    <label class=" me-2 mb-0">No. of rows:</label>
                                    <select id="pageLength" class="form-select form-select-sm" style="width:75px;">
                                        <option value="10">10</option>
                                        <option value="20">20</option>
                                        <option value="30">30</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>

                                <div class="d-flex toolbar-divider align-items-center">
                                    <label class="me-2 mb-0">Filter rows:</label>
                                    <input type="text" id="customSearch"
                                        class="form-control form-control-sm d-inline-block" style="width:150px;"
                                        placeholder="Search this table">
                                </div>

                                <div class="d-flex align-items-center">
                                    <label class="me-2 mb-0">Sort by:</label>
                                    <select id="sortColumn" class="form-select form-select-sm d-inline-block"
                                        style="width:170px;">
                                        <option value="">None</option>
                                        <option value="1">Account Name</option>
                                        <option value="2">Category</option>
                                        <option value="3">Type</option>
                                        <option value="4">Opening Balance</option>
                                        <option value="5">Entry Date</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card mt-3 ms-1 me-1 mb-3">
                            <div class="card-body p-0">
                                <table class="table table-sm" id="accountTable">
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
                                                <td>{{ $account->ac_cat }}</td>
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
                    </div>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
@endsection
