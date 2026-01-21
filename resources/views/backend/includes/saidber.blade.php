<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!--begin::Sidebar Brand-->
    <div class="sidebar-brand">
        <!--begin::Brand Link-->
        <a href="{{route('dashboard')}}" class="brand-link">
            <!--begin::Brand Image-->
            <img src="{{asset('backend/dist/assets/img/AdminLTELogo.png')}}" alt="AdminLTE Logo" class="brand-image opacity-75 shadow" />
            <!--end::Brand Image-->
            <!--begin::Brand Text-->
            <span class="brand-text fw-light">VV Accounts</span>
            <!--end::Brand Text-->
        </a>
        <!--end::Brand Link-->
    </div>
    <!--end::Sidebar Brand-->
    <!--begin::Sidebar Wrapper-->
    <div class="sidebar-wrapper" style="font-size: 13px">
        <nav class="mt-2">
            <!--begin::Sidebar Menu-->
            <ul class="nav nav-sidebar sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="true">
                <li class="nav-item">
                    <a href="{{url('/purchase')}}" class="nav-link">
                        <i class="nav-icon bi bi-cart-check text-primary"></i>
                        <p>
                            Purchase
                            <i class="nav-arrow bi bi-chevron-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview ms-3">
                        <li class="nav-item">
                            <a href="{{url('/purchase/entry')}}" class="nav-link">
                                <i class="nav-icon bi bi-plus-circle"></i>
                                <p>Add Purchase Item</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{url('/purchase/list')}}" class="nav-link">
                                <i class="nav-icon bi bi-card-list"></i>
                                <p>Purchase List</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="{{url('/item')}}" class="nav-link">
                        <i class="nav-icon bi bi-box text-muted"></i>
                        <p>
                            Item
                            <i class="nav-arrow bi bi-chevron-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview ms-3">
                        <li class="nav-item">
                            <a href="{{url('/item/add')}}" class="nav-link">
                                <i class="nav-icon bi bi-plus-circle"></i>
                                <p>Add New Item</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{url('/item/list')}}" class="nav-link">
                                <i class="nav-icon bi bi-card-list"></i>
                                <p>Item List</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-people text-success"></i>
                        <p>
                            Vendor
                            <i class="nav-arrow bi bi-chevron-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview ms-3">
                        <li class="nav-item">
                            <a href="{{url('/vendor/add')}}" class="nav-link">
                                <i class="nav-icon bi bi-plus-circle"></i>
                                <p>Add New Vendor</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{url('/vendor/list')}}" class="nav-link">
                                <i class="nav-icon bi bi-card-list"></i>
                                <p>Vendor List</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="{{url('/production')}}" class="nav-link">
                        <i class="nav-icon bi bi-building-fill-gear text-info"></i>
                        <p>
                            Menufacturing
                            <i class="nav-arrow bi bi-chevron-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview ms-4">
                        <li class="nav-item">
                            <a href="{{url('/production/product/add')}}" class="nav-link">
                                <i class="nav-icon bi bi-plus-circle"></i>
                                <p>Production Add</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="./index2.html" class="nav-link">
                                <i class="nav-icon bi bi-card-list"></i>
                                <p>Item List</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="{{url('/account')}}" class="nav-link">
                        <i class="nav-icon bi bi-wallet2 text-muted"></i>
                        <p>
                            Account
                            <i class="nav-arrow bi bi-chevron-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview ms-3">
                        <li class="nav-item">
                            <a href="{{url('/account/entry')}}" class="nav-link">
                                <i class="nav-icon bi bi-plus-circle"></i>
                                <p>Account Entry</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{url('/expense/entry')}}" class="nav-link">
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
                
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-box-seam-fill"></i>
                        <p>
                            Widgets
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="./widgets/small-box.html" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Small Box</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="./widgets/info-box.html" class="nav-link">
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
            </ul>
            <!--end::Sidebar Menu-->
        </nav>
    </div>
    <!--end::Sidebar Wrapper-->
</aside>
