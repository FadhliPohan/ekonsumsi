<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title">Menu</li>

                <!-- Dashboard -->
                <li>
                    <a href="{{ route('dashboard') }}" class="waves-effect">
                        <i class="bx bx-home-circle"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Event -->
                <li class="menu-title">Event</li>

                {{-- departemen --}}
                <li>
                    <a href="{{ route('event.index') }}" class="waves-effect">
                        <i class="bx bx-calendar-event"></i>
                        <span>Seluruh Event</span>
                    </a>
                </li>
                <!-- Saldo -->
                @can('manage saldo')
                    <li class="menu-title">Saldo</li>
                    <li>
                        <a href="{{ route('saldo.index') }}" class="waves-effect">
                            <i class="bx bx-wallet"></i>
                            <span>Transaksi Saldo</span>
                        </a>
                    </li>
                @endcan
                <!-- Master Data -->
                <li class="menu-title">Master Data</li>

                {{-- departemen --}}
                <li>
                    <a href="{{ route('departemen.index') }}" class="waves-effect">
                        <i class="bx bx-photo-album"></i>
                        <span>Departemen</span>
                    </a>
                </li>

                {{-- Makanan --}}
                <li>
                    <a href="{{ route('food.index') }}" class="waves-effect">
                        <i class="bx bx-food-menu"></i>
                        <span>Makanan</span>
                    </a>
                </li>

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
