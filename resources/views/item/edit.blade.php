@extends('backend.master')

@section('content')
    <!--begin::App Content Header-->
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0 mt-0">Edit Item</h3>
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
            <!--begin::Quick Example-->
            <div class="card card-primary card-outline mb-2">
                <!--begin::Header-->
                <div class="card-header">
                    <div class="card-title">Update Item Data</div>
                </div>
                <!--end::Header-->
                <!--begin::Form-->
                <form action="{{ url('/item/update/' . $item->id) }}" method="POST">
                    @csrf
                    <!--begin::Body-->
                    <div class="card-body">
                        <div id="itemsContainer" class="border-0 shadow-sm">
                            <div
                                class="bg-success text-white d-flex justify-content-between align-items-center mb-3 px-1 py-1 rounded">
                                <h4 class="mb-0 ms-1">Edit Item Details</h4>
                            </div>
                            <div class="item-row row g-2 align-items-end mb-2">
                                <div class="form-group col-sm-6 col-md-4">
                                    <input type="text" value="{{ $item->item_name }}" name="item_name"
                                        class="form-control item_name" placeholder=" " required />
                                    <label for="item_name" class="floating-label">Item Name</label>
                                </div>
                                <div class="form-group col-sm-6 col-md-4">
                                    <input type="text" value="{{ $item->item_code }}" name="item_code"
                                        class="form-control item_code" placeholder=" "/>
                                    <label for="item_code" class="floating-label">Item Code</label>
                                </div>
                                <div class="form-group col-sm-6 col-md-4">
                                    <select name="cat_id" class="form-control cat_id" required>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                @if ($item->cat_id == $category->id) selected @endif>
                                                {{ $category->cat_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="cat_id" class="floating-label">Category</label>
                                </div>
                                <div class="form-group col-sm-6 col-md-4">
                                    <input type="text" value="{{ $item->size }}" name="size"
                                        class="form-control size" placeholder=" "/>
                                    <label for="size" class="floating-label">Size</label>
                                </div>
                                <div class="form-group col-sm-6 col-md-4">
                                    <input type="number" step="0.01" value="{{ $item->unit_price }}" name="unit_price"
                                        class="form-control unit_price" placeholder=" " required />
                                    <label for="unit_price" class="floating-label">Unit Price</label>
                                </div>
                            </div>
                        </div>
                        <!--end::Body-->
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
