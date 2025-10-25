<nav id="sidebar" class="sidebar js-sidebar">
    <div class="sidebar-content js-simplebar">
        <a class="sidebar-brand" href="index.html">
            <span class="align-middle">Kedai Holad</span>
        </a>
        <ul class="sidebar-nav">
            <li class="sidebar-item {{ request()->is('admin/dashboard*') ? 'active' : '' }}">
                <a class="sidebar-link" href="/admin/dashboard">
                    <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Dashboard</span>
                </a>
            </li>
            
            <li class="sidebar-header">
                ORDERS
            </li>
            <li class="sidebar-item {{ request()->is('admin/order*') ? 'active' : '' }}">
                <a class="sidebar-link" href="/admin/order">
                    <i class="align-middle" data-feather="shopping-cart"></i> <span class="align-middle">Order</span>
                </a>
            </li>
            <li class="sidebar-item {{ request()->is('admin/cashier*') ? 'active' : '' }}">
                <a class="sidebar-link" href="/admin/cashier">
                    <i class="align-middle" data-feather="dollar-sign"></i> <span class="align-middle">Cashier</span>
                </a>
            </li>
            <li class="sidebar-header">
                MENUS
            </li>
            <li class="sidebar-item {{ request()->is('admin/menu*') ? 'active' : '' }}">
                <a class="sidebar-link" href="/admin/menu">
                    <i class="align-middle" data-feather="shopping-bag"></i> <span class="align-middle">Menu</span>
                </a>
            </li>
            <li class="sidebar-item {{ request()->is('admin/category*') ? 'active' : '' }}">
                <a class="sidebar-link" href="/admin/category">
                    <i class="align-middle" data-feather="book"></i> <span class="align-middle">Category</span>
                </a>
            </li>
            <li class="sidebar-item {{ request()->is('admin/option*') ? 'active' : '' }}">
                <a class="sidebar-link" href="/admin/option">
                    <i class="align-middle" data-feather="git-commit"></i> <span class="align-middle">Option</span>
                </a>
            </li>
            <li class="sidebar-header">
                ADDITIONAL HOME PAGE
            </li>
            <li class="sidebar-item {{ request()->is('admin/header*') ? 'active' : '' }}">
                <a class="sidebar-link" href="/admin/header">
                    <i class="align-middle" data-feather="layout"></i> <span class="align-middle">Header</span>
                </a>
            </li>
            <li class="sidebar-item {{ request()->is('admin/footer*') ? 'active' : '' }}">
                <a class="sidebar-link" href="/admin/footer">
                    <i class="align-middle" data-feather="columns"></i> <span class="align-middle">Footer</span>
                </a>
            </li>
            <li class="sidebar-header">
                PERSON
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="#">
                    <i class="align-middle" data-feather="log-out"></i> <span class="align-middle">Logout</span>
                </a>
            </li>
        </ul>
    </div>
</nav>