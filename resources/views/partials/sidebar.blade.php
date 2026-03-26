{{-- Sidebar --}}
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    {{-- Sidebar Brand --}}
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-fw fa-layer-group"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Admin BLUD</div>
    </a>

    {{-- Divider --}}
    <hr class="sidebar-divider my-0">

    {{-- Dashboard --}}
    @if (auth()->user()->hasRole('Superadmin'))
        <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
    @elseif(auth()->user()->hasRole('Adminfutsal'))
        <li class="nav-item {{ request()->routeIs('adminfutsal.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminfutsal.dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
    @endif

    {{-- Divider --}}
    <hr class="sidebar-divider">

    {{-- ================================================ --}}
    {{-- MENU SUPERADMIN --}}
    {{-- ================================================ --}}
    @if (auth()->user()->hasRole('Superadmin'))
        <div class="sidebar-heading">Menu Utama</div>

        {{-- Manajemen User --}}
        <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <a class="nav-link {{ request()->routeIs('users.*') ? '' : 'collapsed' }}" href="#"
                data-toggle="collapse" data-target="#collapseUsers"
                aria-expanded="{{ request()->routeIs('users.*') ? 'true' : 'false' }}" aria-controls="collapseUsers">
                <i class="fas fa-fw fa-users"></i>
                <span>Manajemen User</span>
            </a>
            <div id="collapseUsers" class="collapse {{ request()->routeIs('users.*') ? 'show' : '' }}"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item {{ request()->routeIs('users.index') ? 'active' : '' }}"
                        href="{{ route('users.index') }}">
                        <span>Manajemen Admin</span>
                    </a>
                    <a class="collapse-item {{ request()->routeIs('users.pelanggan') ? 'active' : '' }}"
                        href="{{ route('users.pelanggan') }}">
                        <span>Manajemen Pelanggan</span>
                    </a>
                </div>
            </div>
        </li>

        {{-- Monitoring Transaksi --}}
        <li class="nav-item {{ request()->routeIs('transaksi.index') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('transaksi.index') }}">
                <i class="fas fa-fw fa-clipboard-list"></i>
                <span>Monitoring Transaksi</span>
            </a>
        </li>

        {{-- Monitoring Aktivitas --}}
        <li class="nav-item {{ request()->routeIs('monitoring.index') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('monitoring.index') }}">
                <i class="fas fa-fw fa-eye"></i>
                <span>Monitoring Aktivitas</span>
            </a>
        </li>

        {{-- ================================================ --}}
        {{-- MENU ADMINFUTSAL --}}
        {{-- ================================================ --}}
    @elseif(auth()->user()->hasRole('Adminfutsal'))
        <div class="sidebar-heading">Menu Utama</div>

        {{-- Manajemen Membership --}}
        <li
            class="nav-item {{ request()->routeIs('adminfutsal.paket-membership.*') || request()->routeIs('adminfutsal.monitoring-membership.*') ? 'active' : '' }}">
            <a class="nav-link {{ request()->routeIs('adminfutsal.paket-membership.*') || request()->routeIs('adminfutsal.monitoring-membership.*') ? '' : 'collapsed' }}"
                href="#" data-toggle="collapse" data-target="#collapseMembership"
                aria-expanded="{{ request()->routeIs('adminfutsal.paket-membership.*') || request()->routeIs('adminfutsal.monitoring-membership.*') ? 'true' : 'false' }}"
                aria-controls="collapseMembership">
                <i class="fas fa-fw fa-id-card"></i>
                <span>Manajemen Member</span>
            </a>
            <div id="collapseMembership"
                class="collapse {{ request()->routeIs('adminfutsal.paket-membership.*') || request()->routeIs('adminfutsal.monitoring-membership.*') ? 'show' : '' }}"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item {{ request()->routeIs('adminfutsal.paket-membership.*') ? 'active' : '' }}"
                        href="{{ route('adminfutsal.paket-membership.index') }}">
                        <span>Paket Membership</span>
                    </a>
                    <a class="collapse-item {{ request()->routeIs('adminfutsal.monitoring-membership.*') ? 'active' : '' }}"
                        href="{{ route('adminfutsal.monitoring-membership.index') }}">
                        <span>Monitoring Membership</span>
                    </a>
                </div>
            </div>
        </li>
    @endif

    {{-- Divider --}}
    <hr class="sidebar-divider d-none d-md-block">

    {{-- Sidebar Toggle Button --}}
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
{{-- End of Sidebar --}}
