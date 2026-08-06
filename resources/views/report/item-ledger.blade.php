@extends('backend.master')
@push('style')
    <style>
        .form-group {
            position: relative;
            margin-bottom: .5rem;
        }

        .form-control {
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 4px 2px;
            width: 100%;
            transition: all 0.2s ease;
        }

        .floating-label {
            position: absolute;
            left: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            padding: 3px 3px;
            margin: 0 4px;
            color: #5e5c5c;
            font-size: 10px;
            pointer-events: none;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: none;
        }

        .form-control:focus+.floating-label,
        .form-control:not(:placeholder-shown)+.floating-label {
            top: 0;
            transform: translateY(-50%) scale(0.9);
            color: #094bae;
            font-size: 10px;
            filter: blur(0.05px);
            /* focus বা filled হলে হালকা blur */
        }

        /* border cut effect */
        .form-control:focus+.floating-label::before,
        .form-control:not(:placeholder-shown)+.floating-label::before {
            content: '';
            position: absolute;
            left: 8px;
            right: 0;
            height: 2px;
            top: 50%;
            background: #fff;
            z-index: -1;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
        .closing {
            font-weight: bold;
            background: #9fe2f3;
        }
    </style>
@endpush
@section('content')
    <div class="app-content">
        <div class="container-fluid">
            <div class="card shadow-sm border-0 mt-2 p-1 no-print">
                <form method="GET" action="{{ route('report.item.ledger') }}">
                    <div class="row align-items-end">
                        <div class="form-group col-md-4">
                            <select name="item_id" class="form-control select2">
                                <option value="">
                                    Select Item
                                </option>

                                @foreach ($items as $row)
                                    <option value="{{ $row->id }}"
                                        {{ request('item_id') == $row->id ? 'selected' : '' }}>
                                        {{ $row->item_name }}
                                    </option>
                                @endforeach
                            </select>
                            <label class="floating-label">Item</label>
                        </div>

                        <div class="form-group col-md-3">
                            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                            <label class="floating-label">From Date</label>
                        </div>
                        <div class="form-group col-md-3">
                            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                            <label class="floating-label">To Date</label>
                        </div>

                        <div class="form-group col-md-2">
                            <button class="btn btn-primary w-100">
                                <i class="bi bi-funnel"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            @if ($item)
                <div class="card text-white bg-success opacity-75 mt-1 mb-1 p-1 rounded">
                    <div class="row">
                        {{-- 🔹 TITLE --}}
                        <div class="col-md-6">
                            <h4>
                                Item Ledger :
                                {{ $item->item_name }}
                            </h4>
                        </div>
                        {{-- 🔹 PRINT BUTTON --}}
                        <div class="col-md-6 text-end no-print">
                            <a href="{{ route('report.item-ledger.pdf', request()->query()) }}"
                                class="btn btn-sm btn-outline-danger" title="Download PDF">
                                <i class="bi bi-file-pdf"></i>
                            </a>
                            <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-warning" title="Go Back">
                                <i class="bi bi-arrow-left"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body p-0">
                        @php
                            $runningBalance = $openingBalance;
                            $unitPrice = $item->unit_price ?? 0;
                            $sl = 1;
                            $typeNames = [
                                'opening' => 'Opening Stock',
                                'purchase' => 'Purchase',
                                'purchase_return' => 'Purchase Return',
                                'sale' => 'Sale',
                                'sales' => 'Sale',
                                'sale_return' => 'Sale Return',
                                'production_in' => 'Production',
                                'consume' => 'Consumption',
                                'damage' => 'Damage',
                            ];
                        @endphp

                        <table class="table table-hover table-bordered align-middle mb-0">
                            <thead class="table-light text-center">
                                <tr>
                                    <th style=" background:#f5bfbf;" width="60">SL</th>
                                    <th style=" background:#f5bfbf;" width="120">Date</th>
                                    <th style=" background:#f5bfbf;">Type</th>
                                    <th style=" background:#f5bfbf;" width="120">Qty In</th>
                                    <th style=" background:#f5bfbf;" width="120">Qty Out</th>
                                    <th style=" background:#f5bfbf;" width="150">Balance</th>
                                </tr>

                            </thead>
                            <tbody>
                                {{-- Opening Balance --}}
                                @if ($fromDate)
                                    <tr class="table-warning fw-bold">
                                        <td>{{ $sl++ }}</td>
                                        <td>{{ date('d-m-Y', strtotime($fromDate)) }}</td>
                                        <td>Opening Balance</td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-end">
                                            {{ number_format($openingBalance, 3) }}
                                        </td>
                                    </tr>
                                @else
                                    <tr style="background:#fff3cd;">
                                        <td>{{ $sl++ }}</td>
                                        <td>
                                            {{ $fromDate ? date('d-m-Y', strtotime($fromDate)) : '' }}
                                        </td>
                                        <td> <strong>Opening Balance</strong> </td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-end">
                                            <strong> {{ number_format($openingBalance, 3) }} </strong>
                                        </td>
                                    </tr>
                                @endif
                                @forelse($ledgers as $row)
                                    @php
                                        $runningBalance += $row->qty_in;
                                        $runningBalance -= $row->qty_out;
                                    @endphp
                                    <tr>
                                        <td>{{ $sl++ }}</td>
                                        <td>
                                            {{ date('d-m-Y', strtotime($row->date)) }}
                                        </td>
                                        <td>
                                            {{ $typeNames[$row->module_type] ?? ucwords(str_replace('_', ' ', $row->module_type)) }}
                                        </td>
                                        <td class="text-end">
                                            {{ $row->qty_in > 0 ? number_format($row->qty_in, 2) . ' ' . $item->stock_unit : '-' }}
                                        </td>
                                        <td class="text-end">
                                            {{ $row->qty_out > 0 ? number_format($row->qty_out, 2) . ' ' . $item->stock_unit : '-' }}
                                        </td>

                                        <td class="text-end">
                                            {{ number_format($runningBalance, 2) . ' ' . $item->stock_unit }}
                                        </td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            No Data Found
                                        </td>
                                    </tr>
                                @endforelse
                                {{-- Closing Balance --}}
                                <tr class="closing">
                                    <td colspan="5" class="text-right">
                                        Current Stock
                                    </td>

                                    <td class="text-right">
                                        {{ number_format($runningBalance, 2) . ' ' . $item->stock_unit }}
                                    </td>
                                </tr>

                                <tr style="background:#b1c7f1;font-weight:bold;">
                                    <td colspan="5" class="text-right">
                                        Stock Value
                                        ({{ number_format($unitPrice, 2) }} ×
                                        {{ number_format($runningBalance, 2) . ' ' . $item->stock_unit }})
                                    </td>

                                    <td class="text-right">
                                       {{ number_format($runningBalance * $unitPrice, 2). ' '. 'TK' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>



@endsection
@push('script')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%'
            });
        });
    </script>
    <script>
        function downloadPDF() {

            let item_id = "{{ request('item_id') }}";
            let from_date = "{{ request('from_date') }}";
            let to_date = "{{ request('to_date') }}";

            let url =
                "{{ route('report.item-ledger.pdf') }}" +
                "?item_id=" + item_id +
                "&from_date=" + from_date +
                "&to_date=" + to_date;

            window.open(url, '_blank');
        }
    </script>
@endpush
