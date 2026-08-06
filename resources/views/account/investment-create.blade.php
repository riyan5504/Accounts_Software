@extends('backend.master')

@section('content')
    <!--begin::App Content-->
    <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Quick Example-->
            <div class="card card-primary card-outline">
                <!--begin::Form-->
                <form action="{{ route('account.investment.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body row">
                        <div
                            class="bg-success text-white d-flex justify-content-between align-partners-center mb-3 px-1 py-1 rounded">
                            <h4 class="mb-0 ms-1">Capital Entry</h4>
                        </div>
                        <!-- Left -->
                        <div class="col-md-6">
                            <div class="row g-2">
                                <div class="form-group col-md-6 mb-1">
                                    <input type="date" name="date" class="form-control date"
                                        value="{{ date('Y-m-d') }}" placeholder=" " required>
                                    <label for="date" class="floating-label">Date</label>
                                </div>

                                <div class="form-group col-md-6 mb-1">
                                    <select name="partner_id" class="form-control partner_id" required>
                                        <option value="">Select Partner</option>
                                        @foreach ($partners as $partner)
                                            <option value="{{ $partner->id }}">{{ $partner->p_name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="partner_id" class="floating-label">Partner</label>
                                </div>

                                <div class="form-group col-md-6 mb-1">
                                    <input type="number" name="amount" class="form-control amount" placeholder=" "
                                        required>
                                    <label for="amount" class="floating-label">Amount</label>
                                </div>
                                <div class="col-md-5 d-flex align-items-center ms-1 mb-1">
                                    <label class="me-1">Type:</label>
                                    <input type="radio" name="invest_type" value="capital" class="ms-1">
                                    <span class="ms-1">Capital</span>
                                    <input type="radio" name="invest_type" value="loan" required class="ms-2">
                                    <span class="ms-1">Loan</span>
                                </div>
                                <div class="form-group mb-1">
                                    <input type="file" name="attachment" class="form-control attachment">
                                    <label for="attachment" class="floating-label">Attachment</label>
                                </div>
                            </div>
                        </div>
                        <!-- Right -->
                        <div class="col-md-6">
                            <div class="row g-2">
                                <div class="form-group col-md-6 mb-1 position-relative">
                                    <select name="debit_account_id" class="form-control debit_account_id" required>
                                        <option selected disabled>Select Debit Account</option>
                                        @foreach ($accounts->where('ac_type', 'asset') as $account)
                                            <option value="{{ $account->id }}" data-ac_cat="{{ $account->ac_cat }}">
                                                {{ $account->account_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label class="floating-label">Debit Account</label>
                                    <button class="add-btn" type="button" data-bs-toggle="modal"
                                        data-bs-target="#addAccountModal">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                                <div class="form-group col-md-6 mb-1">
                                    <select name="credit_account_id" class="form-control credit_account_id" required>
                                        <option selected disabled>Select Credit Account</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}" data-type="{{ $account->ac_type }}">
                                                {{ $account->account_name }}</option>
                                        @endforeach
                                    </select>
                                    <label class="floating-label">Credit Account</label>
                                </div>
                                <div class="form-group mb-1">
                                    <input type="text" name="reference" class="form-control reference" placeholder=" ">
                                    <label for="reference" class="floating-label">Reference</label>
                                </div>
                                <div class="form-group mb-1">
                                    <textarea name="note" rows="1" class="form-control note"></textarea>
                                    <label for="note" class="floating-label">Note</label>
                                </div>
                            </div>
                        </div>

                        <div><button class="btn btn-primary">Save Investment</button></div>
                    </div>

            </div>
            </form>
            <!--end::Form-->
        </div>
        <!--end::Quick Example-->
    </div>
    <!--end::Container-->
    </div>

    <div class="modal fade" id="addAccountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form action="{{ url('/account/store') }}" method="POST">
                    @csrf
                    <!--begin::Body-->
                        <div class="modal-body border-0 shadow-sm">
                            <div class="modal-header">
                                <h5 class="modal-title">Entry Account Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                                    <select name="ac_type" class="form-control ac_type" placeholder=" ">
                                        <option selected disabled>Select Account Type</option>
                                        <option value="asset">Asset</option>
                                        <option value="liability">Liability</option>
                                        <option value="equity">Equity</option>
                                        <option value="revenue">Revenue</option>
                                        <option value="expense">Expense</option>
                                    </select>
                                    <label for="unit" class="floating-label">Account Type</label>
                                </div>
                                <div class="form-group col-sm-6 col-md-3 mb-1">
                                    <input type="text" name="ac_cat" class="form-control ac_cat" placeholder=" "
                                        required />
                                    <label for="ac_cat" class="floating-label">Account Category</label>
                                </div>

                                <div class="form-group col-sm-6 col-md-3 mb-1">
                                    <input type="number" step="0.01" name="op_balance"
                                        class="form-control op_balance" value="0" placeholder=" " required />
                                    <label for="op_balance" class="floating-label">Opening Balance</label>
                                </div>
                            </div>
                        </div>
                        <!--end::Body-->
                        <!--begin::Footer-->
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">💾 Save</button>
                        </div>
                        <!--end::Footer-->
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const radios = document.querySelectorAll('input[name="invest_type"]');
            const creditSelect = document.querySelector('.credit_account_id');
            const options = creditSelect.querySelectorAll('option');

            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    let selectedType = this.value;

                    options.forEach(option => {
                        // 👉 default option সবসময় show থাকবে
                        if (!option.dataset.type) {
                            option.style.display = 'block';
                            return;
                        }

                        if (selectedType === 'capital' && option.dataset.type ===
                            'equity') {
                            option.style.display = 'block';
                        } else if (selectedType === 'loan' && option.dataset.type ===
                            'liability') {
                            option.style.display = 'block';
                        } else {
                            option.style.display = 'none';
                        }
                    });

                    // 👉 dropdown reset করে default option select করাও
                    creditSelect.selectedIndex = 0;
                });
            });
        });
    </script>
    @if ($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('addAccountModal'));
                myModal.show();
            });
        </script>
    @endif
@endpush
