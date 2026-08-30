blade
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">

    <title>Investment Report</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #222;
        }

        .company-section {
            text-align: center;
            margin-bottom: 7px;
        }

        .company-section img {
            max-width: 80px;
            max-height: 55px;
            margin-bottom: 2px;
        }

        .company-title {
            font-size: 18px;
            font-weight: bold;
        }

        .company-info {
            font-size: 11px;
            margin-top: 2px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h2 {
            margin: 0;
            font-size: 16px;
        }

        .header p {
            margin: 5px 0;
            color: #555;
        }

        .report-info {
            margin-bottom: 7px;
        }

        .summary {
            width: 100%;
            margin-bottom: 10px;
        }

        .summary td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: center;
        }

        .summary-title {
            font-size: 10px;
            color: #666;
        }

        .summary-value {
            font-size: 15px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 3px;
        }

        th {
            background: #f2f2f2;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .grand-total {
            font-weight: bold;
            background: #f5f5f5;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin: 5px 0 8px;
        }

        .capital {
            color: green;
        }

        .loan {
            color: #b07800;
        }
    </style>
</head>

<body>

    <!-- Company Info -->
    <div class="company-section">
        @if ($logoPath)
            <img src="{{ $logoPath }}" alt="{{ $company->name }}">
        @endif
        <div class="company-title">{{ $company->name ?? 'Company Name' }}</div>
        <div class="company-info">A Trusted Source of Aloe Vera & Herb Product</div>
        <div class="company-info">{{ $company->address ?? '' }}</div>
        <div class="company-info">
            @if ($company->phone)
                Mob: {{ $company->phone }}
            @endif
        </div>
        <div class="company-info">{{ $company->email ?? '' }}</div>
    </div>

    {{-- Header --}}
    <div class="header">
        <h2>Investment Report</h2>
        @if ($selectedPartner)
            <p>
                Partner:
                <strong>{{ $selectedPartner->p_name }}</strong>
            </p>
        @else
            <p>All Partners</p>
        @endif
    </div>

    {{-- Filter Information --}}
    <div class="report-info">
        @if (request('date_from'))
            <strong>From:</strong>
            {{ \Carbon\Carbon::parse(request('date_from'))->format('d-m-Y') }}
        @endif
        @if (request('date_to'))
            &nbsp;&nbsp;
            <strong>To:</strong>
            {{ \Carbon\Carbon::parse(request('date_to'))->format('d-m-Y') }}
        @endif
        @if (request('invest_type') && request('invest_type') !== 'all')
            &nbsp;&nbsp;
            <strong>Type:</strong>
            {{ request('invest_type') === 'capital' ? 'Capital' : 'Partner Loan' }}
        @endif
    </div>

    {{-- Summary --}}
    <table class="summary">
        <tr>
            <td>
                <div class="summary-title">
                    Total Capital
                </div>
                <div class="summary-value capital">
                    {{ number_format($totalCapital, 2) }}
                </div>
            </td>
            <td>
                <div class="summary-title">
                    Total Partner Loan
                </div>
                <div class="summary-value loan">
                    {{ number_format($totalPartnerLoan, 2) }}
                </div>
            </td>
            <td>
                <div class="summary-title">
                    Total Investment
                </div>
                <div class="summary-value">
                    {{ number_format($totalInvestment, 2) }}
                </div>
            </td>
        </tr>
    </table>

    {{-- Partner Wise Ledger --}}
    <div class="section-title">
        Partner Wise Investment
    </div>

    <table>
        <thead>
            <tr>
                <th width="7%">Sl. No.</th>
                <th>Partner</th>
                <th class="text-right">Capital</th>
                <th class="text-right">Partner Loan</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($partnerLedger as $index => $ledger)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $ledger['partner_name'] }}</td>
                    <td class="text-right">{{ number_format($ledger['capital'], 2) }}</td>
                    <td class="text-right">{{ number_format($ledger['loan'], 2) }}</td>
                    <td class="text-right">
                        <strong>{{ number_format($ledger['total'], 2) }}</strong>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center"> No investment data found. </td>
                </tr>
            @endforelse
        </tbody>
        @if ($partnerLedger->count())
            <tfoot>
                <tr class="grand-total">
                    <td colspan="2" class="text-right">Grand Total</td>
                    <td class="text-right">
                        {{ number_format($totalCapital, 2) }}
                    </td>
                    <td class="text-right">
                        {{ number_format($totalPartnerLoan, 2) }}
                    </td>
                    <td class="text-right">
                        {{ number_format($totalInvestment, 2) }}
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>

    {{-- Investment Details --}}
    <div class="section-title">
        Investment Details
    </div>

    <table>
        <thead>
            <tr>
                <th width="6%">Sl. No.</th>
                <th>Date</th>
                <th>Partner</th>
                <th>Type</th>
                <th class="text-right">Amount</th>
                <th>Reference</th>
            </tr>
        </thead>
        <tbody>
            @forelse($investments as $index => $investment)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($investment->date)->format('d-m-Y') }}</td>
                    <td>{{ $investment->partner?->p_name ?? 'N/A' }}</td>
                    <td>
                        @if ($investment->invest_type === 'capital')
                            Capital
                        @else
                            Partner Loan
                        @endif
                    </td>
                    <td class="text-right">{{ number_format($investment->amount, 2) }}</td>
                    <td>{{ $investment->reference ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">
                        No investment records found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
