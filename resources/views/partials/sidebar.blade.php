{{-- Sidebar --}}
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    {{-- Sidebar Brand --}}
    @php
        $brandLabel = 'Admin BLUD';
        $brandRoute = route('dashboard');

        if (auth()->user()->hasRole('Adminfutsal')) {
            $brandLabel = 'AdminFutsal';
            $brandRoute = route('adminfutsal.dashboard');
        } elseif (auth()->user()->hasRole('Adminac')) {
            $brandLabel = 'Admin AC';
            $brandRoute = route('dashboard'); // sesuaikan jika ada route adminac
        } elseif (auth()->user()->hasRole('Adminkantin')) {
            $brandLabel = 'Admin Ruko/Kantin';
            $brandRoute = route('dashboard'); // sesuaikan jika ada route adminkantin
        }
    @endphp

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ $brandRoute }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-fw fa-layer-group"></i>
        </div>
        <div class="sidebar-brand-text mx-3">{{ $brandLabel }}</div>
    </a>
    <hr class="sidebar-divider my-0">

    {{-- DASHBOARD --}}
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

    <hr class="sidebar-divider">

    {{-- ================================= --}}
    {{-- SUPERADMIN ONLY --}}
    {{-- ================================= --}}
    @if (auth()->user()->hasRole('Superadmin'))
        <div class="sidebar-heading">Menu Utama</div>

        {{-- Manajemen User --}}
        <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <a class="nav-link {{ request()->routeIs('users.*') ? '' : 'collapsed' }}" href="#"
                data-toggle="collapse" data-target="#collapseUsers">
                <i class="fas fa-fw fa-users"></i>
                <span>Manajemen User</span>
            </a>

            <div id="collapseUsers" class="collapse {{ request()->routeIs('users.*') ? 'show' : '' }}">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="{{ route('users.index') }}">Manajemen Admin</a>
                    <a class="collapse-item" href="{{ route('users.pelanggan') }}">Manajemen Pelanggan</a>
                </div>
            </div>
        </li>

        <li class="nav-item {{ request()->routeIs('transaksi.index') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('transaksi.index') }}">
                <i class="fas fa-clipboard-list"></i>
                <span>Monitoring Transaksi</span>
            </a>
        </li>
    @endif

    {{-- ================================= --}}
    {{-- ADMIN FUTSAL --}}
    {{-- ================================= --}}
    @if (auth()->user()->hasRole('Adminfutsal'))
        <div class="sidebar-heading">Menu Utama</div>

        {{-- Jadwal Lapangan --}}
        <li
            class="nav-item {{ request()->routeIs('adminfutsal.jadwal-lapangan.*') || request()->routeIs('adminfutsal.pengaturan.*') ? 'active' : '' }}">
            <a class="nav-link {{ request()->routeIs('adminfutsal.jadwal-lapangan.*') || request()->routeIs('adminfutsal.pengaturan.*') ? '' : 'collapsed' }}"
                href="#" data-toggle="collapse" data-target="#collapseJadwal">
                <i class="fas fa-fw fa-calendar-alt"></i>
                <span>Manajemen Lapangan</span>
            </a>

            <div id="collapseJadwal"
                class="collapse {{ request()->routeIs('adminfutsal.jadwal-lapangan.*') || request()->routeIs('adminfutsal.pengaturan.*') ? 'show' : '' }}">

                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="{{ route('adminfutsal.jadwal-lapangan.index') }}"> Jadwal
                        Lapangan</a>
                    <a class="collapse-item" href="{{ route('adminfutsal.pengaturan.index') }}">Jam Operasional</a>
                </div>
            </div>
        </li>

        {{-- Transaksi --}}
        <li class="nav-item {{ request()->routeIs('adminfutsal.transaksi.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminfutsal.transaksi.index') }}">
                <i class="fas fa-fw fa-file-invoice-dollar"></i>
                <span>Manajemen Transaksi</span>
            </a>
        </li>

        {{-- Booking --}}
        <li class="nav-item {{ request()->routeIs('adminfutsal.booking.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminfutsal.booking.index') }}">
                <i class="fas fa-fw fa-calendar-check"></i>
                <span>Manajemen Booking</span>
            </a>
        </li>

        {{-- Membership --}}
        <li
            class="nav-item {{ request()->routeIs('adminfutsal.paket-membership.*') || request()->routeIs('adminfutsal.monitoring-membership.*') ? 'active' : '' }}">
            <a class="nav-link {{ request()->routeIs('adminfutsal.paket-membership.*') || request()->routeIs('adminfutsal.monitoring-membership.*') ? '' : 'collapsed' }}"
                href="#" data-toggle="collapse" data-target="#collapseMembership">

                <i class="fas fa-fw fa-id-card"></i>
                <span>Manajemen Membership</span>
            </a>

            <div id="collapseMembership"
                class="collapse {{ request()->routeIs('adminfutsal.paket-membership.*') || request()->routeIs('adminfutsal.monitoring-membership.*') ? 'show' : '' }}">

                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="{{ route('adminfutsal.paket-membership.index') }}">
                        Paket Membership
                    </a>
                    <a class="collapse-item" href="{{ route('adminfutsal.monitoring-membership.index') }}">
                        Monitoring Membership
                    </a>
                </div>
            </div>
        </li>

        {{-- Manajemen Pelanggan Reguler --}}
        <li class="nav-item {{ request()->routeIs('adminfutsal.pelanggan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminfutsal.pelanggan.index') }}">
                <i class="fas fa-fw fa-users"></i>
                <span>Manajemen Pelanggan Reguler</span>
            </a>
        </li>

        {{-- Laporan Transaksi --}}
        <li class="nav-item {{ request()->routeIs('adminfutsal.laporan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminfutsal.laporan.index') }}">
                <i class="fas fa-fw fa-file-pdf"></i>
                <span>Laporan Transaksi</span>
            </a>
        </li>
    @endif

    {{-- ================================= --}}
    {{-- GLOBAL MENU (SEMUA ROLE) --}}
    {{-- ================================= --}}

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
{{-- End Sidebar --}}
