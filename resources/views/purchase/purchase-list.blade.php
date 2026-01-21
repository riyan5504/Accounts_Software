@extends('backend.master')

@section('content')
    <!--begin::App Content Header-->
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Purchase List</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <a href="{{ url('/purchase') }}"
                                class="{{ request()->is('purchase') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Purchase
                            </a>
                        </li>

                        <li class="breadcrumb-item">
                            <a href="{{ url('/purchase/entry') }}"
                                class="{{ request()->is('purchase/entry') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Entry
                            </a>
                        </li>

                        <li class="breadcrumb-item">
                            <a href="{{ url('/purchase/list') }}"
                                class="{{ request()->is('purchase/list') ? 'text-primary fw-bold' : 'text-dark' }}">
                                List
                            </a>
                        </li>

                        <li class="breadcrumb-item">
                            <a href="{{ url('/item/list') }}"
                                class="{{ request()->is('item/list') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Item List
                            </a>
                        </li>

                        <li class="breadcrumb-item">
                            <a href="{{ url('/vendor/list') }}"
                                class="{{ request()->is('vendor/list') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Vendor List
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
            <div class="row g-4">
                <!--begin::Col-->
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="ms-2 pt-3">
                            <h5>Purchase List</h5>
                        </div>
                        <div class="card mt-3 ms-1 me-1 mb-3">
                            <div class="card-body p-0">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th style="width: 80px">Sl. No.</th>
                                            <th style="width: 180px">Vendor Name</th>
                                            <th>Date</th>
                                            <th>Invoice No</th>
                                            <th style="width: 350px">Item</th>
                                            <th>Total Amount</th>
                                            <th style="text-align: center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($purchases as $purchase)
                                            <tr class="align-middle">
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>{{ $purchase->vendor->v_name }}</td>
                                                <td>{{ $purchase->date->format('d-m-y') }}</td>
                                                <td>{{ $purchase->invoice_no }}</td>
                                                <td>
                                                    @foreach ($purchase->purchaseItems as $singleItm)
                                                        {{ $singleItm->item->item_name ?? 'N/A' }}{{ !$loop->last ? ', ' : '' }}
                                                    @endforeach
                                                </td>
                                                <td>{{ $purchase->grand_total }}</td>
                                                <td style="text-align: center">
                                                    <a href="{{ url('/purchase/details/' . $purchase->id) }}"
                                                        class="btn ms-0 me-0">
                                                        <i class="bi bi-eye text-success"></i>
                                                    </a>

                                                    <a href="{{ url('/purchase/edit/' . $purchase->id) }}"
                                                        class="btn ms-0 me-0">
                                                        <i class="bi bi-pencil text-primary"></i>
                                                    </a>
                                                    <a href="{{ url('/purchase/delete/' . $purchase->id) }}"
                                                        class="btn me-0 ms-0" onclick="return confirm('আপনি কি নিশ্চিত? Purchase এবং সব Journal Entry ডিলিট হবে!');">
                                                        <i class="bi bi-trash text-danger"></i>
                                                    </a>
                                                    {{-- <a href="#" class="btn me-0 ms-0"
                                                        onclick="event.preventDefault(); confirmDelete({{ $purchase->id }});">
                                                        <i class="bi bi-trash text-danger"></i>
                                                    </a>

                                                    <form id="deleteForm{{ $purchase->id }}"
                                                        action="{{ route('purchase.delete', $purchase->id) }}"
                                                        method="POST" style="display:none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form> --}}

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
    <!--end::App Content-->
@endsection
{{-- 
@push('script')
    <script>
        function confirmDelete(id) {
            if (confirm('আপনি কি নিশ্চিত? Purchase এবং সব Journal Entry ডিলিট হবে!')) {
                document.getElementById('deleteForm' + id).submit();
            }
        }
    </script>
@endpush --}}
