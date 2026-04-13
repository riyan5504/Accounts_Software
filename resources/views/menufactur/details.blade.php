@extends('backend.master')

@section('content')
    <!--begin::App Content Header-->
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Production Details</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <a href="{{ url('/production') }}"
                                class="{{ request()->is('production') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Production
                            </a>
                        </li>

                        <li class="breadcrumb-item">
                            <a href="{{ url('/production/product/add') }}"
                                class="{{ request()->is('production/entry') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Entry
                            </a>
                        </li>

                        <li class="breadcrumb-item">
                            <a href="{{ url('/production/list') }}"
                                class="{{ request()->is('production/list') ? 'text-primary fw-bold' : 'text-dark' }}">
                                List
                            </a>
                        </li>
                    </ol>
                </div>

            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::App Content Header-->

    <!--begin::App Content-->
    <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div>
                <!--begin::Col-->
                <div>
                    @php $section = 1; @endphp
                    <div class="card card-primary card-outline">
                        <div class="row g-3 mt-1 ms-1 mb-2">
                            <h6 class="fw-bold">{{ $section++ }}. Batch Basic Info</h6>
                            <div class="col-md-4 mt-0">
                                <div class="card bg-success">
                                    <div class="card-body">
                                        <table class="table table-sm">
                                            <tr>
                                                <th>Date:</th>
                                                <td>{{ $production->date->format('d-m-y') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Batch No:</th>
                                                <td>{{ $production->batch_no }}</td>
                                            </tr>
                                            <tr>
                                                <th style="width:150px">Product Name:</th>
                                                <td>{{ $production->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Batch Size:</th>
                                                <td>{{ $production->batch_size }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-4">
                                <div class="card bg-success">
                                    <div class="card-body text-center">
                                        <small class="text-muted">Total Batch Cost</small>
                                        <h4 class="fw-bold text-white mb-0">
                                            ৳ {{ number_format($production->grand_total, 2) }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 mt-4">
                                <div class="card bg-warning">
                                    <div class="card-body text-center">
                                        <small class="text-muted">Cost Per Unit</small>
                                        <h5 class="fw-bold text-white mb-1">
                                            ৳ {{ number_format($costPerUnit, 2) }}
                                        </h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 mt-4">
                                <div class="card bg-info">
                                    <div class="card-body text-center">
                                        <small class="text-muted">Highest Cost Head</small>
                                        <h6 class="fw-bold text-danger mb-2">
                                            {{ $highestCostHead }}
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if ($production->raw_t_price > 0)
                        <h6 class="fw-bold ms-2 mt-1">{{ $section++ }}. Raw Material Cost</h6>
                        <div class="card ms-1 me-1 mb-1">
                            <div class="card-body p-0">
                                <table class="table table-bordered table-sm align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width:80px">Sl. No.</th>
                                            <th style="width:180px">Materials Name</th>
                                            <th class="text-center" style="width:100px">Quantity</th>
                                            <th class="text-center" style="width:100px">Rate</th>
                                            <th class="text-center" style="width:100px">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <tr class="align-middle">
                                            <td class="text-center">{{ 1 }}</td>
                                            <td>{{ $production->items->item_name }}</td>
                                            <td class="text-center">{{ $production->raw_qty }} {{ $production->raw_unit }}
                                            </td>
                                            <td class="text-center">{{ $production->raw_u_price }}</td>
                                            <td class="text-end">{{ $production->raw_t_price }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr class="table-success">
                                            <th colspan="4" class="text-end">Sub Total:</th>
                                            <th class="text-end">
                                                {{ number_format($production->raw_t_price, 2) }}
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    @endif
                    @if ($production->chemicals->isNotEmpty())
                        <h6 class="fw-bold ms-2 mt-1">{{ $section++ }}. Chemical Cost</h6>
                        <div class="card ms-1 me-1 mb-1">
                            <div class="card-body p-0">
                                <table class="table table-bordered table-sm align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width:80px">Sl. No.</th>
                                            <th style="width:180px">Chemicals Name</th>
                                            <th class="text-center" style="width:100px">Quantity</th>
                                            <th class="text-center" style="width:100px">Rate</th>
                                            <th class="text-center" style="width:100px">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($production->chemicals as $chemical)
                                            <tr class="align-middle">
                                                <td class="text-center">{{ $loop->index + 1 }}</td>
                                                <td>{{ $chemical->items->item_name }}</td>
                                                <td class="text-center">{{ $chemical->used_qty }}
                                                    {{ $chemical->ch_unit }}
                                                </td>
                                                <td class="text-center">{{ $chemical->u_price }}</td>
                                                <td class="text-end">{{ $chemical->t_price }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr class="table-success">
                                            <th colspan="4" class="text-end">Total Chemical Cost</th>
                                            <th class="text-end">{{ $production->sectionTotalCost->raw_grand_price }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    @endif
                    @if ($production->packagingMaterial->isNotEmpty())
                        <h6 class="fw-bold ms-2 mt-1">{{ $section++ }}. Packaging Material Cost</h6>
                        <div class="card ms-1 me-1 mb-1">
                            <div class="card-body p-0">
                                <table class="table table-bordered table-sm align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width:80px">Sl. No.</th>
                                            <th style="width:180px">Materials Name</th>
                                            <th class="text-center" style="width:100px">Quantity</th>
                                            <th class="text-center" style="width:100px">Rate</th>
                                            <th class="text-center" style="width:100px">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($production->packagingMaterial as $pack)
                                            <tr class="align-middle">
                                                <td class="text-center">{{ $loop->index + 1 }}</td>
                                                <td>{{ $pack->items->item_name }}</td>
                                                <td class="text-center">{{ $pack->pack_qty }} nos</td>
                                                <td class="text-center">{{ $pack->pack_price }}</td>
                                                <td class="text-end">{{ $pack->total_price }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr class="table-success">
                                            <th colspan="4" class="text-end">Total Packaging Cost</th>
                                            <th class="text-end">{{ $production->sectionTotalCost->pack_grand_price }}
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    @endif
                    @if ($production->laborCost->isNotEmpty())
                        <h6 class="fw-bold ms-2 mt-1">{{ $section++ }}. Labor Cost</h6>
                        <div class="card ms-1 me-1 mb-1">
                            <div class="card-body p-0">
                                <table class="table table-bordered table-sm align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width:80px">Sl. No.</th>
                                            <th style="width:180px">Labor Name</th>
                                            <th class="text-center" style="width:100px">Duty Day</th>
                                            <th class="text-center" style="width:100px">Rate</th>
                                            <th class="text-center" style="width:100px">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($production->laborCost as $labor)
                                            <tr class="align-middle">
                                                <td class="text-center">{{ $loop->index + 1 }}</td>
                                                <td>{{ $labor->labor_name }}</td>
                                                <td class="text-center">{{ $labor->duty_day }} day
                                                </td>
                                                <td class="text-center">{{ $labor->d_pay }}</td>
                                                <td class="text-end">{{ $labor->total_pay }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr class="table-success">
                                            <th colspan="4" class="text-end">Total Labor Cost</th>
                                            <th class="text-end">{{ $production->sectionTotalCost->labor_grand_price }}
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    @endif
                    @if ($production->utilityCost->isNotEmpty())
                        <h6 class="fw-bold ms-2 mt-1">{{ $section++ }}. Utility Cost</h6>
                        <div class="card ms-1 me-1 mb-1">
                            <div class="card-body p-0">
                                <table class="table table-bordered table-sm align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width:80px">Sl. No.</th>
                                            <th style="width:590px">Head</th>
                                            <th class="text-center">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($production->utilityCost as $utility)
                                            <tr class="align-middle">
                                                <td class="text-center">{{ $loop->index + 1 }}</td>
                                                <td>{{ $utility->utility_name }}</td>
                                                <td class="text-end">{{ $utility->cost_amt }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr class="table-success">
                                            <th colspan="2" class="text-end">Total Utility Cost</th>
                                            <th class="text-end">{{ $production->sectionTotalCost->utility_grand_price }}
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    @endif
                    @if ($production->depreciation->isNotEmpty())
                        <h6 class="fw-bold ms-2 mt-1">{{ $section++ }}. Depreciation Cost</h6>
                        <div class="card ms-1 me-1 mb-1">
                            <div class="card-body p-0">
                                <table class="table table-bordered table-sm align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width:80px">Sl. No.</th>
                                            <th style="width:590px">Head</th>
                                            <th class="text-center">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($production->depreciation as $dep)
                                            <tr class="align-middle">
                                                <td class="text-center">{{ $loop->index + 1 }}</td>
                                                <td>{{ $dep->machine_name }}</td>
                                                <td class="text-end">{{ $dep->machine_cost_amt }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr class="table-success">
                                            <th colspan="2" class="text-end">Total Depreciation Cost</th>
                                            <th class="text-end">
                                                {{ $production->sectionTotalCost->depreciation_grand_price }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    @endif
                    @if ($production->overHeadCost->isNotEmpty())
                        <h6 class="fw-bold ms-2 mt-1">{{ $section++ }}. Overhead Cost</h6>
                        <div class="card ms-1 me-1 mb-1">
                            <div class="card-body p-0">
                                <table class="table table-bordered table-sm align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width:80px">Sl. No.</th>
                                            <th style="width:590px">Head</th>
                                            <th class="text-center">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($production->overHeadCost as $overhead)
                                            <tr class="align-middle">
                                                <td class="text-center">{{ $loop->index + 1 }}</td>
                                                <td>{{ $overhead->overhead_type }}</td>
                                                <td class="text-end">{{ $overhead->fo_cost_amt }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr class="table-success">
                                            <th colspan="2" class="text-end">Total Overhead Cost</th>
                                            <th class="text-end">{{ $production->sectionTotalCost->overhead_grand_price }}
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    @endif
                    @if ($production->transportCost->isNotEmpty())
                        <h6 class="fw-bold ms-2 mt-1">{{ $section++ }}. Transport Cost</h6>
                        <div class="card ms-1 me-1 mb-1">
                            <div class="card-body p-0">
                                <table class="table table-bordered table-sm align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width:80px">Sl. No.</th>
                                            <th style="width:590px">Head</th>
                                            <th class="text-center">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($production->transportCost as $transport)
                                            <tr class="align-middle">
                                                <td class="text-center">{{ $loop->index + 1 }}</td>
                                                <td>{{ $transport->transport_type }}</td>
                                                <td class="text-end">{{ $transport->transport_amt }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr class="table-success">
                                            <th colspan="2" class="text-end">Total Transport Cost</th>
                                            <th class="text-end">
                                                {{ $production->sectionTotalCost->transport_grand_price }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    @endif
                    @if ($production->qcCost->isNotEmpty())
                        <h6 class="fw-bold ms-2 mt-1">{{ $section++ }}. QC Cost</h6>
                        <div class="card ms-1 me-1 mb-1">
                            <div class="card-body p-0">
                                <table class="table table-bordered table-sm align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width:80px">Sl. No.</th>
                                            <th style="width:590px">Head</th>
                                            <th class="text-center">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($production->qcCost as $qc)
                                            <tr class="align-middle">
                                                <td class="text-center">{{ $loop->index + 1 }}</td>
                                                <td>{{ $qc->test_name }}</td>
                                                <td class="text-end">{{ $qc->qc_amt }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr class="table-success">
                                            <th colspan="2" class="text-end">Total QC Cost</th>
                                            <th class="text-end">{{ $production->sectionTotalCost->qc_grand_price }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    @endif
                </div>
                {{-- Summary --}}
                <h5 class="fw-bold mt-2 mb-2">Batch Cost Summary</h5>

                <table class="table table-bordered table-sm">
                    @if ($production->raw_t_price > 0)
                        <tr>
                            <th>Raw Material Cost</th>
                            <td class="text-end"> {{ number_format($production->raw_t_price, 2) }}</td>
                        </tr>
                    @endif
                    @if (optional($production->sectionTotalCost)->raw_grand_price > 0)
                        <tr>
                            <th>Chemical Cost</th>
                            <td class="text-end">{{ number_format($production->sectionTotalCost->raw_grand_price, 2) }}
                            </td>
                        </tr>
                    @endif
                    @if (optional($production->sectionTotalCost)->pack_grand_price > 0)
                        <tr>
                            <th>Packaging Cost</th>
                            <td class="text-end">{{ number_format($production->sectionTotalCost->pack_grand_price, 2) }}
                            </td>
                        </tr>
                    @endif
                    @if (optional($production->sectionTotalCost)->labor_grand_price > 0)
                        <tr>
                            <th>Labor Cost</th>
                            <td class="text-end">{{ number_format($production->sectionTotalCost->labor_grand_price, 2) }}
                            </td>
                        </tr>
                    @endif
                    @if (optional($production->sectionTotalCost)->utility_grand_price > 0)
                        <tr>
                            <th>Utility Cost</th>
                            <td class="text-end">
                                {{ number_format($production->sectionTotalCost->utility_grand_price, 2) }}</td>
                        </tr>
                    @endif
                    @if (optional($production->sectionTotalCost)->depreciation_grand_price > 0)
                        <tr>
                            <th>Depreciation Cost</th>
                            <td class="text-end">
                                {{ number_format($production->sectionTotalCost->depreciation_grand_price, 2) }}</td>
                        </tr>
                    @endif
                    @if (optional($production->sectionTotalCost)->overhead_grand_price > 0)
                        <tr>
                            <th>Overhead Cost</th>
                            <td class="text-end">
                                {{ number_format($production->sectionTotalCost->overhead_grand_price, 2) }}</td>
                        </tr>
                    @endif
                    @if (optional($production->sectionTotalCost)->transport_grand_price > 0)
                        <tr>
                            <th>Transport Cost</th>
                            <td class="text-end">
                                {{ number_format($production->sectionTotalCost->transport_grand_price, 2) }}</td>
                        </tr>
                    @endif
                    @if (optional($production->sectionTotalCost)->qc_grand_price > 0)
                        <tr>
                            <th>QC Cost</th>
                            <td class="text-end">{{ number_format($production->sectionTotalCost->qc_grand_price, 2) }}
                            </td>
                        </tr>
                    @endif

                    <tr class="table-success">
                        <th>Total Batch Cost</th>
                        <th class="text-end">{{ number_format($production->grand_total, 2) }}</th>
                    </tr>
                </table>
            </div>
            <!--end::Col-->
        </div>
        <!--end::Row-->
    </div>
    <!--end::Container-->
    </div>
    <!--end::App Content-->
@endsection
