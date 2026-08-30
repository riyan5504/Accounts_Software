@extends('backend.master')

@section('content')
    <div class="app-content">
        <div class="container-fluid">

            {{-- PAGE HEADER --}}
            <div class="row g-4">
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="row bg-info m-1 ps-1 rounded">
                            <div class="col-md-8">
                                <h5 class="py-2 mb-0">Investment Report</h5>
                            </div>
                            <div class="col-sm-4 text-end no-print mt-1">
                                <a href="{{ route('account.investment.report.pdf', request()->query()) }}"
                                    class="btn btn-outline-danger btn-sm" title="Download PDF">
                                    <i class="bi bi-file-pdf"></i>
                                </a>
                                <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-warning" title="Go Back">
                                    <i class="bi bi-arrow-left"></i>
                                </a>
                            </div>
                        </div>

                        {{-- FILTER --}}
                        <div class="card-body">
                            <form method="GET" action="{{ route('account.investment.report') }}">
                                <div class="row g-3">
                                    <div class="form-group col-md-2">
                                        <input type="date" name="date_from" class="form-control date_from"
                                            value="{{ request('date_from') }}">
                                        <label class="floating-label form-label">Date From</label>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <input type="date" name="date_to" class="form-control"
                                            value="{{ request('date_to') }}">
                                        <label class="floating-label form-label">Date To</label>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <select name="partner_id" class="form-control">
                                            <option value="">All Partners</option>
                                            @foreach ($partners as $partner)
                                                <option value="{{ $partner->id }}"
                                                    {{ request('partner_id') == $partner->id ? 'selected' : '' }}>
                                                    {{ $partner->p_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <label class="floating-label form-label">Partner</label>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <select name="invest_type" class="form-control">
                                            <option value="all"
                                                {{ request('invest_type', 'all') == 'all' ? 'selected' : '' }}>
                                                All
                                            </option>
                                            <option value="capital"
                                                {{ request('invest_type') == 'capital' ? 'selected' : '' }}>
                                                Capital
                                            </option>
                                            <option value="loan"
                                                {{ request('invest_type') == 'loan' ? 'selected' : '' }}>
                                                Loan
                                            </option>
                                        </select>
                                        <label class="floating-label form-label">Investment Type</label>
                                    </div>

                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary me-2">
                                            <i class="bi bi-search"></i>
                                            Search
                                        </button>
                                        <a href="{{ route('account.investment.report') }}" class="btn btn-secondary">
                                            Reset
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- INVESTMENT SUMMARY --}}
            <div class="row g-3 mt-1">

                {{-- Total Capital --}}
                <div class="col-md-4">
                    <div class="card border-success">
                        <div class="card-body">
                            <h6 class="text-muted">Total Capital</h6>
                            <h3 class="mb-0 text-success">
                                {{ number_format($totalCapital, 2) }}
                            </h3>
                        </div>
                    </div>
                </div>

                {{-- Total Partner Loan --}}
                <div class="col-md-4">
                    <div class="card border-warning">
                        <div class="card-body">
                            <h6 class="text-muted">Total Partner Loan</h6>
                            <h3 class="mb-0 text-warning">
                                {{ number_format($totalPartnerLoan, 2) }}
                            </h3>
                        </div>
                    </div>
                </div>

                {{-- Total Investment --}}
                <div class="col-md-4">
                    <div class="card border-primary">
                        <div class="card-body">
                            <h6 class="text-muted">Total Investment</h6>
                            <h3 class="mb-0 text-primary">
                                {{ number_format($totalInvestment, 2) }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PARTNER INVESTMENT LEDGER --}}
            <div class="row g-4 mt-1">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Partner Investment Ledger</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Partner Name</th>
                                            <th class="text-end">Capital Balance</th>
                                            <th class="text-end">Loan Balance</th>
                                            <th class="text-end">Payment Amount</th>
                                            <th class="text-end">Balance Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($partnerLedger as $ledger)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $ledger['partner_name'] }}</td>
                                                <td class="text-end">{{ number_format($ledger['capital'], 2) }}</td>
                                                <td class="text-end">{{ number_format($ledger['loan'], 2) }}</td>
                                                <td class="text-end">0.00</td>
                                                <td class="text-end fw-bold">{{ number_format($ledger['total'], 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4">
                                                    No partner investment records found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>

                                    @if ($partnerLedger->count())
                                        <tfoot>
                                            <tr class="fw-bold">
                                                <td colspan="2" class="text-end">Grand Total</td>
                                                <td class="text-end">
                                                    {{ number_format($partnerLedger->sum('capital'), 2) }}
                                                </td>
                                                <td class="text-end">
                                                    {{ number_format($partnerLedger->sum('loan'), 2) }}
                                                </td>
                                                <td class="text-end">0.00</td>
                                                <td class="text-end">
                                                    {{ number_format($partnerLedger->sum('total'), 2) }}
                                                </td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- INVESTMENT DETAILS --}}
            <div class="row g-4 mt-1">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Investment Details</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Date</th>
                                            <th>Partner Name</th>
                                            <th>Invest Type</th>
                                            <th class="text-end">Amount</th>
                                            <th class="text-center">Reference</th>
                                            <th class="text-center">Attachment</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($investments as $investment)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ \Carbon\Carbon::parse($investment->date)->format('d-m-Y') }}</td>
                                                <td>{{ $investment->partner->p_name ?? 'N/A' }}</td>
                                                <td>
                                                    @if ($investment->invest_type === 'capital')
                                                        <span class="badge bg-success">Capital</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">Loan</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">{{ number_format($investment->amount, 2) }}</td>
                                                <td class="text-center">{{ $investment->reference ?? '-' }}</td>
                                                <td class="text-center">
                                                    @if ($investment->attachment)
                                                        <a href="{{ asset('backend/dist/assets/invest/' . $investment->attachment) }}"
                                                            target="_blank">
                                                            View Attachment
                                                        </a>
                                                    @else
                                                        <span>-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4">
                                                    No investment records found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>

                                    @if ($investments->count())
                                        <tfoot>
                                            <tr class="fw-bold">
                                                <td colspan="4" class="text-end">Total</td>
                                                <td class="text-end">{{ number_format($totalInvestment, 2) }}</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
