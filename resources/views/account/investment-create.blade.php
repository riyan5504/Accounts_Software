@extends('backend.master')

@section('content')
    <!--begin::App Content-->
    <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Quick Example-->
            <div class="card card-primary card-outline">
                <!--begin::Form-->
                <form action="{{ route('account.investment-store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body row">
                        <div
                            class="bg-success text-white d-flex justify-content-between align-partners-center mb-3 px-1 py-1 rounded">
                            <h4 class="mb-0 ms-1">Capital Entry</h4>
                        </div>
                        <!-- Left -->
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <input type="date" name="date" class="form-control date" value="{{ date('Y-m-d') }}"
                                    placeholder=" " required>
                                <label for="date" class="floating-label">Date</label>
                            </div>

                            <div class="form-group mb-2">
                                <select name="partner_id" class="form-control partner_id" required>
                                    <option value="">Select Partner</option>
                                    @foreach ($partners as $partner)
                                        <option value="{{ $partner->id }}">{{ $partner->p_name }}</option>
                                    @endforeach
                                </select>
                                <label for="partner_id" class="floating-label">Partner</label>
                            </div>

                            <div class="form-group mb-2">
                                <input type="number" name="amount" class="form-control amount" placeholder=" " required>
                                <label for="amount" class="floating-label">Amount</label>
                            </div>
                        </div>
                        <!-- Right -->
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <select name="account_id" class="form-control account_id" required>
                                    <option value="">Select Account</option>
                                    @foreach ($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->account_name }}</option>
                                    @endforeach
                                </select>
                                <label for="account_id" class="floating-label">Deposit To (Bank/Cash)</label>
                            </div>

                            <div class="form-group mb-2">
                                <input type="text" name="reference" class="form-control reference" placeholder=" ">
                                <label for="reference" class="floating-label">Reference</label>
                            </div>

                            <div class="form-group mb-2">
                                <textarea name="note" class="form-control note"></textarea>
                                <label for="note" class="floating-label">Note</label>
                            </div>

                            <div class="form-group mb-2">
                                <input type="file" name="attachment" class="form-control attachment">
                                <label for="attachment" class="floating-label">Attachment</label>
                            </div>
                        </div>
                        <div><button class="btn btn-primary">Save Investment</button></div>
                    </div>
                </form>
                <!--end::Form-->
            </div>
            <!--end::Quick Example-->
        </div>
        <!--end::Container-->
    </div>
@endsection
