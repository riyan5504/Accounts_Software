<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!--begin::Sidebar Brand-->
    <div class="sidebar-brand">
        <!--begin::Brand Link-->
        <a href="{{route('dashboard')}}" class="brand-link">
            <!--begin::Brand Image-->
            <img src="{{asset('backend/dist/assets/img/AdminLTELogo.png')}}" alt="AdminLTE Logo" class="brand-image opacity-75 shadow" />
            <!--end::Brand Image-->
            <!--begin::Brand Text-->
            <span class="brand-text fw-light">Accounts</span>
            <!--end::Brand Text-->
        </a>
        <!--end::Brand Link-->
    </div>
    <!--end::Sidebar Brand-->
    <!--begin::Sidebar Wrapper-->
    <div class="sidebar-wrapper">
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
                    <ul class="nav nav-treeview ms-4">
                        <li class="nav-item">
                            <a href="./index.html" class="nav-link">
                                <i class="nav-icon bi bi-plus-circle"></i>
                                <p>Add Item</p>
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
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-building-fill-gear text-info"></i>
                        <p>
                            Menufacturing
                            <i class="nav-arrow bi bi-chevron-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview ms-4">
                        <li class="nav-item">
                            <a href="./index.html" class="nav-link">
                                <i class="nav-icon bi bi-plus-circle"></i>
                                <p>Add Item</p>
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
