@php
    $module = match (true) {
        request()->routeIs('dashboard') => 'all',
        request()->routeIs('purchase*') => 'purchase',
        request()->routeIs('item*') => 'item',
        request()->routeIs('vendor*') => 'purchase',

        request()->routeIs('account*') => 'account',
        request()->routeIs('expense*') => 'account',

        request()->routeIs('production*') => 'production',
        request()->routeIs('report*') => 'report',

        default => '',
    };
@endphp
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!--begin::Sidebar Brand-->
    <div class="sidebar-brand">
        <!--begin::Brand Link-->
        <a href="{{ route('dashboard') }}" class="brand-link">
            <!--begin::Brand Image-->
            <img src="{{ asset('backend/dist/assets/img/logo02.png') }}" alt="AdminLTE Logo"
                class="brand-image opacity-75 shadow" />
            <!--end::Brand Image-->
            <!--begin::Brand Text-->
            <span class="brand-text fw-light">VV Accounts</span>
            <!--end::Brand Text-->
        </a>
        <!--end::Brand Link-->
        <div class="mr-1">
            <i class="fas fa-chevron-left text-info" id="sidebarCollapse"></i>
        </div>
    </div>
    <!--end::Sidebar Brand-->
    <!--begin::Sidebar Wrapper-->
    <div class="sidebar-wrapper" style="font-size: 13px">
        <nav class="mt-2">
            <!--begin::Sidebar Menu-->
            <ul class="nav nav-sidebar sidebar-menu flex-column" data-lte-toggle="treeview" role="menu"
                data-accordion="true">
                @if ($module == 'purchase' || $module == 'all')
                    <li class="nav-item {{ $module == 'purchase' ? 'menu-open' : '' }}">
                        <a href="{{ route('purchase.index') }}" class="nav-link">
                            <i class="nav-icon bi bi-cart-check text-primary"></i>
                            <p>
                                Purchase Module
                                <i class="nav-arrow bi bi-chevron-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview ms-3">
                            <li class="nav-item">
                                <a href="{{ route('purchase.entry') }}" class="nav-link">
                                    <i class="nav-icon bi bi-plus-circle"></i>
                                    <p>Purchase Entry</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('purchase.list') }}" class="nav-link">
                                    <i class="nav-icon bi bi-card-list"></i>
                                    <p>Purchase List</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('purchase.vendoradd') }}" class="nav-link">
                                    <i class="nav-icon bi bi-plus-circle"></i>
                                    <p>Vendor Entry</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('purchase.vendorlist') }}" class="nav-link">
                                    <i class="nav-icon bi bi-card-list"></i>
                                    <p>Vendor List</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                @if ($module == 'item' || $module == 'all')
                <li class="nav-item {{ $module == 'item' ? 'menu-open' : '' }}">
                    <a href="{{ route('item.index') }}" class="nav-link">
                        <i class="nav-icon bi bi-box text-muted"></i>
                        <p>
                            Item
                            <i class="nav-arrow bi bi-chevron-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview ms-3">                        
                        <li class="nav-item">
                            <a href="{{ route('item.category-add') }}" class="nav-link">
                                <i class="nav-icon bi bi-tags"></i>
                                <p>Category Entry</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('item.item-add') }}" class="nav-link">
                                <i class="nav-icon bi bi-plus-circle"></i>
                                <p>Item Entry</p>
                            </a>
                        </li>
                        {{-- <li class="nav-item">
                            <a href="{{ route('item.item-list') }}" class="nav-link">
                                <i class="nav-icon bi bi-card-list"></i>
                                <p>Item List</p>
                            </a>
                        </li> --}}
                    </ul>
                </li>
                @endif

                @if ($module == 'production' || $module == 'all')
                    <li class="nav-item {{ $module == 'production' ? 'menu-open' : '' }}">
                        <a href="{{ route('production.index') }}" class="nav-link">
                            <i class="nav-icon bi bi-building-fill-gear text-info"></i>
                            <p>
                                Menufacturing
                                <i class="nav-arrow bi bi-chevron-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview ms-4">
                            <li class="nav-item">
                                <a href="{{ route('production.add') }}" class="nav-link">
                                    <i class="nav-icon bi bi-plus-circle"></i>
                                    <p>Production Add</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('production.list') }}" class="nav-link">
                                    <i class="nav-icon bi bi-card-list"></i>
                                    <p>Production List</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                @if ($module == 'account' || $module == 'all')
                    <li class="nav-item {{ $module == 'account' ? 'menu-open' : '' }}">
                        <a href="{{ url('/account') }}" class="nav-link">
                            <i class="nav-icon bi bi-wallet2 text-muted"></i>
                            <p>
                                Account
                                <i class="nav-arrow bi bi-chevron-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview ms-3">
                            <li class="nav-item">
                                <a href="{{ url('/account/entry') }}" class="nav-link">
                                    <i class="nav-icon bi bi-plus-circle"></i>
                                    <p>Account Entry</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('/expense/entry') }}" class="nav-link">
                                    <i class="nav-icon bi bi-plus-circle"></i>
                                    <p>Expense Entry</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon bi bi-card-list"></i>
                                    <p>voucher List</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                @if ($module == 'report' || $module == 'all')
                    <li class="nav-item {{ $module == 'report' ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-box-seam-fill"></i>
                            <p>
                                Reports
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('/stock/report') }}" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Item Stock Report</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>info Box</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="./widgets/cards.html" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Cards</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
            </ul>
            <!--end::Sidebar Menu-->
        </nav>
    </div>
    <!--end::Sidebar Wrapper-->
</aside>
