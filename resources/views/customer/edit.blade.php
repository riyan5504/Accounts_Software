@extends('backend.master')

@section('content')
    <!--begin::App Content Header-->
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row bg-info opasity-50 rounded">
                <div class="col-sm-6">
                    <h3 class="mb-0 mt-0">Customer Edit</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <a href="{{ url('/sales') }}"
                                class="{{ request()->is('sales') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Sales
                            </a>
                        </li>

                        <li class="breadcrumb-item">
                            <a href="{{ url('/sales/entry') }}"
                                class="{{ request()->is('sales/entry') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Entry
                            </a>
                        </li>

                        <li class="breadcrumb-item">
                            <a href="{{ url('/sales/list') }}"
                                class="{{ request()->is('sales/list') ? 'text-primary fw-bold' : 'text-dark' }}">
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
                            <a href="{{ route('sales.customer.list') }}"
                                class="{{ request()->routeIs('sales.customer.list') ? 'text-primary fw-bold' : 'text-dark' }}">
                                customer List
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('sales.customer.received.create') }}"
                                class="{{ request()->routeIs('sales.customer.received.create') ? 'text-primary fw-bold' : 'text-dark' }}">
                                customer Payment
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
            <!--begin::Quick Example-->
            <div class="card card-primary card-outline mb-2">
                <!--begin::Header-->
                <div class="card-header">
                    <div class="card-title">Edit Customer Informetion</div>
                </div>
                <!--end::Header-->
                <!--begin::Form-->
                <form action="{{ route('sales.customer.update', $customer->id) }}" method="POST">
                    @csrf
                    <!--begin::Body-->
                    <div class="card-body">
                        <!--start::customer-->
                        <div id="customerContainer" class="border-0 shadow-sm ms-0">
                            <div
                                class="bg-success text-white d-flex justify-content-between align-customers-center mb-3 px-1 py-1 rounded">
                                <h4 class="mb-0 ms-1">Customer Details</h4>
                            </div>
                            <!-- ✅ Proper Row Structure -->
                            <div class="row g-2 align-customer-end mb-2">
                                <div class="form-group col-md-5">
                                    <input type="text" value="{{$customer->c_name}}" name="c_name" class="form-control c_name" required />
                                    <label for="c_name" class="floating-label">Customer Name</label>
                                </div>

                                <div class="form-group col-md-3">
                                    <input type="text" value="{{$customer->phone}}" name="phone" class="form-control phone" required />
                                    <label for="phone" class="floating-label">Phone</label>
                                </div>
                                <div class="form-group col-md-4">
                                    <input type="email" value="{{$customer->email}}" name="email" class="form-control email" />
                                    <label for="email" class="floating-label">Email</label>
                                </div>
                                <div class="form-group col-md-8">
                                    <textarea name="address" class="form-control address" rows="1" required>{{$customer->address}}</textarea>
                                    <label for="address" class="floating-label">Address</label>
                                </div>
                                <div class="form-group col-sm-4 col-md-4 mb-1">
                                    <input type="number" name="opening_balance" value="{{$customer->opening_balance}}" class="form-control opening_balance"
                                        placeholder=" " />
                                    <label for="opening_balance" class="floating-label">Opening Balance</label>
                                </div>
                            </div>
                        </div>
                        <!--begin::Footer-->
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Update</button>
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
@endsection
@push('script')
    <script>
        $(document).ready(function() {

            // ✅ customer Name Autocomplete
            $('.c_name').autocomplete({
                source: "{{ route('search.customer') }}",
                minLength: 1,
                select: function(event, ui) {
                    // যখন customer select হবে
                    $('.c_name').val(ui.item.value);
                    $('.phone').val(ui.item.phone);
                    $('.email').val(ui.item.email);
                    $('.address').val(ui.item.address);
                }
            });

        });
    </script>
@endpush
