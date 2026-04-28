<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title">Menu</li>

                @can('view dashboard')
                    <li>
                        <a href="{{ route('dashboard') }}" class="waves-effect">
                            <i class="bx bx-home-circle"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                @endcan

                @can('view events')
                    <li class="menu-title">Event</li>
                    <li>
                        <a href="{{ route('event.index') }}" class="waves-effect">
                            <i class="bx bx-calendar-event"></i>
                            <span>Seluruh Event</span>
                        </a>
                    </li>
                @endcan

                @canany(['view saldo', 'create saldo transaction'])
                    <li class="menu-title">Saldo</li>
                    <li>
                        <a href="{{ route('saldo.index') }}" class="waves-effect">
                            <i class="bx bx-wallet"></i>
                            <span>Transaksi Saldo</span>
                        </a>
                    </li>
                @endcanany

                @canany(['view departemen', 'view food'])
                    <li class="menu-title">Master Data</li>

                    @can('view departemen')
                        <li>
                            <a href="{{ route('departemen.index') }}" class="waves-effect">
                                <i class="bx bx-photo-album"></i>
                                <span>Departemen</span>
                            </a>
                        </li>
                    @endcan

                    @can('view food')
                        <li>
                            <a href="{{ route('food.index') }}" class="waves-effect">
                                <i class="bx bx-food-menu"></i>
                                <span>Makanan</span>
                            </a>
                        </li>
                    @endcan
                @endcanany

                <!-- User Management -->
                @canany(['view users', 'view roles', 'view permissions'])
                    <li class="menu-title">User Management</li>

                    <!-- Users -->
                    @can('view users')
                        <li>
                            <a href="{{ route('users.index') }}" class="waves-effect">
                                <i class="bx bx-user"></i>
                                <span>Users</span>
                            </a>
                        </li>
                    @endcan

                    <!-- Roles -->
                    @can('view roles')
                        <li>
                            <a href="{{ route('roles.index') }}" class="waves-effect">
                                <i class="bx bx-shield"></i>
                                <span>Roles</span>
                            </a>
                        </li>
                    @endcan

                    <!-- Permissions -->
                    @can('view permissions')
                        <li>
                            <a href="{{ route('permissions.index') }}" class="waves-effect">
                                <i class="bx bx-key"></i>
                                <span>Permissions</span>
                            </a>
                        </li>
                    @endcan
                @endcanany

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
