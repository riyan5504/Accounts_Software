<nav class="app-header navbar navbar-expand bg-body">
    <!--begin::Container-->
    <div class="container-fluid ps-0">
        <!--begin::Start Navbar Links-->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="#" role="button">
                    <i class="fas fa-bars" id="menuToggle" style="display: none;"></i></a>
            </li>
            <!--end::Start Navbar Links-->
        </ul>

        <div class="container-fluid d-flex align-items-center">

            <!-- LEFT MENU --> 
            <ul class="navbar-nav d-flex flex-row gap-3 flex-wrap">

                <li class="nav-item text-center">
                    <a href="{{ route('dashboard') }}" class="nav-link nav-item-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="nav-item text-center">
                    <a href="{{ route('purchase.index') }}"
                        class="nav-link nav-item-custom {{ request()->routeIs('purchase.*') ? 'active' : '' }}">
                        <i class="fas fa-box"></i>
                        <span class="nav-text">Purchase</span>
                    </a>
                </li>

                <li class="nav-item text-center">
                    <a href="{{ route('production.index') }}"
                        class="nav-link nav-item-custom {{ request()->routeIs('production.*') ? 'active' : '' }}">
                        <i class="fas fa-industry"></i>
                        <span>Manufacturing</span>
                    </a>
                </li>

                <li class="nav-item text-center">
                    <a href="#" class="nav-link nav-item-custom">
                        <i class="fas fa-quote-right text-success"></i>
                        <span>Quotation</span>
                    </a>
                </li>

                <li class="nav-item text-center">
                    <a href="#" class="nav-link nav-item-custom">
                        <i class="fas fa-shopping-cart text-danger"></i>
                        <span>Sales</span>
                    </a>
                </li>

                <li class="nav-item text-center">
                    <a href="#" class="nav-link nav-item-custom">
                        <i class="fas fa-warehouse"></i>
                        <span>Inventory</span>
                    </a>
                </li>

                <li class="nav-item text-center">
                    <a href="#" class="nav-link nav-item-custom">
                        <i class="fas fa-users text-primary"></i>
                        <span>HR & Payroll</span>
                    </a>
                </li>

                <li class="nav-item text-center">
                    <a href="{{route('account.index')}}" class="nav-link nav-item-custom {{ request()->routeIs('account.*') ? 'active' : '' }}">
                        <i class="fas fa-calculator"></i>
                        <span>Accounts</span>
                    </a>
                </li>

                <li class="nav-item text-center">
                    <a href="{{route('report.stock.report')}}" class="nav-link nav-item-custom text-danger {{ request()->routeIs('report.*') ? 'active' : '' }}">
                        <i class="fas fa-file-alt"></i>
                        <span>Reports</span>
                    </a>
                </li>

                {{-- <li class="nav-item text-center">
                    <a href="#" class="nav-link nav-item-custom">
                        <i class="fas fa-shopping-bag"></i></i>
                        <span>Order</span>
                    </a>
                </li> --}}

                <li class="nav-item text-center">
                    <a href="#" class="nav-link nav-item-custom">
                        <i class="fas fa-tools text-warning"></i></i>
                        <span>Service</span>
                    </a>
                </li>

                <li class="nav-item text-center">
                    <a href="#" class="nav-link nav-item-custom">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </li>

            </ul>

            <!-- RIGHT SIDE -->
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a href="{{ url('/admin/logout') }}" class="nav-link logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Log Out
                    </a>
                </li>
            </ul>

        </div>
    </div>
    <!--end::Container-->
</nav>
