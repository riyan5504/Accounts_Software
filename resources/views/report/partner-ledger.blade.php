@extends('backend.master')

@section('content')
    <h4>Partner Ledger: {{ $partner->p_name }}</h4>

<table class="table table-bordered">
    <thead>
        <tr>
            <th style="width: 100px">Date</th>
            <th style="width: 200px">Account</th>
            <th style="width: 400px">Particulars</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>Balance</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($journals as $entry)
            <tr>
                <td>{{ $entry->date->format('d-m-Y') }}</td>
                <td>{{ $entry->account->account_name ?? '-' }}</td>
                <td>{{ $entry->transaction_type }}</td>
                <td>{{ number_format($entry->debit, 2) }}</td>
                <td>{{ number_format($entry->credit, 2) }}</td>
                <td>
                    {{ number_format(abs($entry->running_balance), 2) }}
                    {{ $entry->running_balance >= 0 ? 'Dr' : 'Cr' }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection