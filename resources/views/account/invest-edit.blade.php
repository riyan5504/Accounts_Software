@extends('backend.master')

@section('content')
    <!--begin::App Content-->
    <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Quick Example-->
            <div class="card card-primary card-outline">
                <!--begin::Form-->
                <form action="{{ route('account.investment.update', $investment->id) }}" method="POST"
                    enctype="multipart/form-data">
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
                                        value="{{ optional($investment->date)->format('Y-m-d') }}" placeholder=" " required>
                                    <label for="date" class="floating-label">Date</label>
                                </div>

                                <div class="form-group col-md-6 mb-1">
                                    <select name="partner_id" class="form-control partner_id" required>
                                        <option value="">Select Partner</option>
                                        @foreach ($partners as $partner)
                                            <option value="{{ $partner->id }}" {{ old('partner_id', $investment->partner_id) == $partner->id ? 'selected' : '' }}>
                                                {{$partner->p_name}}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="partner_id" class="floating-label">Partner</label>
                                </div>

                                <div class="form-group col-md-6 mb-1">
                                    <input type="number" name="amount" class="form-control amount"
                                        value="{{ old('amount', $investment->amount) }}" placeholder=" " required>
                                    <label for="amount" class="floating-label">Amount</label>
                                </div>

                                <div class="col-md-6 d-flex align-items-center mb-1">
                                    <label class="me-2">Type:</label>
                                    <input type="radio" name="invest_type" value="capital" id="invest_capital" class="ms-1"
                                        {{ old('invest_type', $investment->invest_type) == 'capital' ? 'checked' : '' }}>
                                    <label for="invest_capital" class="ms-1">Capital</label>

                                    <input type="radio" name="invest_type" value="loan" id="invest_loan" class="ms-2"
                                        required {{ old('invest_type', $investment->invest_type) == 'loan' ? 'checked' : '' }}>
                                    <label for="invest_loan" class="ms-1">Loan</label>
                                    @if ($investment->attachment)
                                        <small class="text-muted d-block mt-1">
                                            Current file:
                                            <a href="{{ asset('storage/' . $investment->attachment) }}" target="_blank">
                                                View Attachment
                                            </a>
                                        </small>
                                    @endif

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

                                        <option value="" disabled {{ !$investment->debit_account_id ? 'selected' : '' }}>
                                            Select Debit Account
                                        </option>

                                        @foreach ($accounts->where('ac_type', 'asset') as $account)

                                            <option value="{{ $account->id }}" {{ old('debit_account_id', $investment->debit_account_id) == $account->id ? 'selected' : '' }}>

                                                {{ $account->account_name }}

                                            </option>

                                        @endforeach

                                    </select>

                                    <label class="floating-label">
                                        Debit Account
                                    </label>

                                    <button class="add-btn" type="button" data-bs-toggle="modal"
                                        data-bs-target="#addAccountModal">

                                        <i class="bi bi-plus"></i>

                                    </button>

                                </div>

                                <div class="form-group col-md-6 mb-1">

                                    <select name="credit_account_id" class="form-control credit_account_id" required>

                                        <option value="" disabled {{ !$investment->credit_account_id ? 'selected' : '' }}>
                                            Select Credit Account
                                        </option>

                                        @foreach ($accounts as $account)

                                            <option value="{{ $account->id }}" data-type="{{ strtolower($account->ac_type) }}"
                                                {{ old('credit_account_id', $investment->credit_account_id) == $account->id ? 'selected' : '' }}>

                                                {{ $account->account_name }}

                                            </option>

                                        @endforeach

                                    </select>

                                    <label class="floating-label">
                                        Credit Account
                                    </label>

                                </div>

                                <div class="form-group mb-1">
                                    <input type="text" name="reference" class="form-control reference"
                                        value="{{ old('reference', $investment->reference) }}" placeholder=" ">
                                    <label for="reference" class="floating-label">Reference</label>
                                </div>
                                <div class="form-group mb-1">
                                    <textarea name="note" rows="1"
                                        class="form-control note">{{ old('note', $investment->note) }}</textarea>
                                    <label for="note" class="floating-label">Note</label>
                                </div>
                            </div>
                        </div>

                        <div><button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i>
                                Update Investment
                            </button>

                            <a href="{{ route('account.investment.list') }}" class="btn btn-secondary">
                                Cancel
                            </a>
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
                            <div class="form-group col-sm-6 col-md-3 mb-1">
                                <input type="text" name="account_name" class="form-control account_name" placeholder=" "
                                    required />
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
                                <input type="text" name="ac_cat" class="form-control ac_cat" placeholder=" " required />
                                <label for="ac_cat" class="floating-label">Account Category</label>
                            </div>

                            <div class="form-group col-sm-6 col-md-3 mb-1">
                                <input type="number" step="0.01" name="op_balance" class="form-control op_balance" value="0"
                                    placeholder=" " required />
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
        document.addEventListener("DOMContentLoaded", function () {
            const radios = document.querySelectorAll(
                'input[name="invest_type"]'
            );
            const creditSelect = document.querySelector(
                '.credit_account_id'
            );

            /* Filter Credit Accounts
            | Capital = Equity
            | Loan    = Liability */
            function filterCreditAccounts(investType) {
                const currentValue = creditSelect.value;
                const options = creditSelect.querySelectorAll(
                    'option[data-type]'
                );
                options.forEach(function (option) {
                    const accountType = option.dataset.type;
                    if (
                        (investType === 'capital' &&
                            accountType === 'equity') ||

                        (investType === 'loan' &&
                            accountType === 'liability')
                    ) {
                        option.hidden = false;
                        option.disabled = false;
                    } else {
                        option.hidden = true;
                        option.disabled = true;
                    }
                });

                /* Keep existing investment credit account */
                const existingOption = creditSelect.querySelector(
                    'option[value="' + currentValue + '"]'
                );
                if (
                    existingOption &&
                    !existingOption.disabled &&
                    !existingOption.hidden
                ) {
                    creditSelect.value = currentValue;
                } else {
                    /* Don't automatically select first account */
                    creditSelect.value = "";
                }
            }

            /* Radio Change */
            radios.forEach(function (radio) {
                radio.addEventListener('change', function () {
                    filterCreditAccounts(this.value);
                });
            });

            /* EDIT PAGE INITIAL LOAD */
            const checkedRadio = document.querySelector(
                'input[name="invest_type"]:checked'
            );

            if (checkedRadio) {
                filterCreditAccounts(
                    checkedRadio.value
                );
            }
        });
    </script>
    @if ($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                var myModal = new bootstrap.Modal(document.getElementById('addAccountModal'));
                myModal.show();
            });
        </script>
    @endif
@endpush