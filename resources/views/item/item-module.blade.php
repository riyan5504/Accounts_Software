@extends('backend.master')

@section('content')
    <!--begin::App Content Header-->
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Item</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Item</li>
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
            <div class="row">
                <!--begin::Col-->
                <div class="col-lg-3 col-6">
                    <!--begin::Small Box Widget 1-->
                    <div class="small-box text-bg-primary">
                        <div class="inner">
                            <a href="{{ url('/item/add') }}"><p>Item Entry</p></a>
                        </div>
                        <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path
                                d="M2.25 2.25a.75.75 0 000 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 00-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 000-1.5H5.378A2.25 2.25 0 017.5 15h11.218a.75.75 0 00.674-.421 60.358 60.358 0 002.96-7.228.75.75 0 00-.525-.965A60.864 60.864 0 005.68 4.509l-.232-.867A1.875 1.875 0 003.636 2.25H2.25zM3.75 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM16.5 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z">
                            </path>
                        </svg>
                        <a href="{{ url('/item/add') }}"
                            class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            Entry Here <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                    <!--end::Small Box Widget 1-->
                </div>
                <!--end::Col-->
               
                <div class="col-lg-3 col-6">
                    <!--begin::Small Box Widget 2-->
                    <div class="small-box text-bg-warning">
                        <div class="inner">
                            <a href="{{ url('/item/list') }}"><p>Item List</p></a>
                        </div>
                        <svg class="small-box-icon" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 24 24" width="24" height="24" aria-hidden="true">
                            <path d="M3 3h18v4H3V3zm0 6h18v4H3V9zm0 6h18v4H3v-4z" />
                        </svg>
                        <a href="{{ url('/item/list') }}"
                            class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            Show List <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                    <!--end::Small Box Widget 2-->
                </div>
                {{-- <div class="col-lg-3 col-6">
                    <!--begin::Small Box Widget 2-->
                    <div class="small-box text-bg-info">
                        <div class="inner">
                            <a href="{{ url('/vendor/list') }}"><p>Vendor List</p></a>
                        </div>
                        <svg class="small-box-icon" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 24 24" width="24" height="24" aria-hidden="true">
                            <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5C15 14.17 10.33 13 8 13zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                        </svg>
                        <a href="{{ url('/vendor/list') }}"
                            class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            Show List <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                    <!--end::Small Box Widget 2-->
                </div> --}}
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::App Content-->
@endsection
