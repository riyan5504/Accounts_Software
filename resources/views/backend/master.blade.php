<!doctype html>
<html lang="en">
<!--begin::Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Dashboard</title>
    @include('backend.includes.style')
    @stack('style')
</head>
<!--end::Head-->
<!--begin::Body-->

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <!--begin::Header-->
        @include('backend.includes.nevber')
        <!--end::Header-->
        <!--begin::Sidebar-->
        @include('backend.includes.saidber')
        <!--end::Sidebar-->
        <!--begin::App Main-->
        <main class="app-main">
            @yield('content')
        </main>
        <!--end::App Main-->
        <!--begin::Footer-->
        @include('backend.includes.footer')
        <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    @include('backend.includes.script')
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">

    @if (session('success'))
        <div class="toast align-items-center text-bg-success border-0 fade">
            <div class="d-flex">
                <div class="toast-body">
                    {{ session('success') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="toast align-items-center text-bg-danger border-0 fade">
            <div class="d-flex">
                <div class="toast-body">
                    {{ $errors->first() }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif

</div>
    <!--end::Script-->
    @stack('script')
</body>
<!--end::Body-->

</html>
