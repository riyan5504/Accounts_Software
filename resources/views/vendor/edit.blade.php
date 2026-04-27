@extends('backend.master')

@section('content')
    <!--begin::App Content Header-->
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0 mt-0">Vendor Edit</h3>
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
                            <a href="{{ route('purchase.vendorlist') }}"
                                class="{{ request()->routeIs('purchase.vendorlist') ? 'text-primary fw-bold' : 'text-dark' }}">
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
            <!--begin::Quick Example-->
            <div class="card card-primary card-outline mb-2">
                <!--begin::Header-->
                <div class="card-header">
                    <div class="card-title">Edit Vendor Informetion</div>
                </div>
                <!--end::Header-->
                <!--begin::Form-->
                <form action="{{ route('purchase.vendorupdate', $vendor->id) }}" method="POST">
                    @csrf
                    <!--begin::Body-->
                    <div class="card-body">
                        <!--start::vendor-->
                        <div id="vendorContainer" class="border-0 shadow-sm ms-0">
                            <div
                                class="bg-success text-white d-flex justify-content-between align-vendors-center mb-3 px-1 py-1 rounded">
                                <h4 class="mb-0 ms-1">Vendor Details</h4>
                            </div>
                            <!-- ✅ Proper Row Structure -->
                            <div class="row g-2 align-vendor-end mb-2">
                                <div class="form-group col-md-5">
                                    <input type="text" value="{{$vendor->v_name}}" name="v_name" class="form-control v_name" required />
                                    <label for="v_name" class="floating-label">Vendor Name</label>
                                </div>

                                <div class="form-group col-md-3">
                                    <input type="text" value="{{$vendor->phone}}" name="phone" class="form-control phone" required />
                                    <label for="phone" class="floating-label">Phone</label>
                                </div>
                                <div class="form-group col-md-4">
                                    <input type="email" value="{{$vendor->email}}" name="email" class="form-control email" />
                                    <label for="email" class="floating-label">Email</label>
                                </div>
                                <div class="form-group col-md-8">
                                    <textarea name="address" class="form-control address" rows="1" required>{{$vendor->address}}</textarea>
                                    <label for="address" class="floating-label">Address</label>
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

            // ✅ Vendor Name Autocomplete
            $('.v_name').autocomplete({
                source: "{{ route('vendor.search') }}",
                minLength: 1,
                select: function(event, ui) {
                    // যখন vendor select হবে
                    $('.v_name').val(ui.item.value);
                    $('.phone').val(ui.item.phone);
                    $('.email').val(ui.item.email);
                    $('.address').val(ui.item.address);
                }
            });

        });
    </script>
@endpush
