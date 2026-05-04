@php
    $isMobile = $isMobile ?? false;
@endphp

<ul class="{{ $isMobile ? 'navbar-nav' : 'navbar-nav bg-gradient-primary sidebar sidebar-dark accordion' }}" id="{{ $isMobile ? 'mobileAccordionSidebar' : 'accordionSidebar' }}">

    {{-- Sidebar Brand --}}
    @if(!$isMobile)
    @php
        $brandLabel = 'Sistem BLUD';
        $brandRoute = route('user.gateway');

        if (auth()->check()) {
            $user = auth()->user();
            if ($user->hasRole('Adminfutsal')) {
                $brandLabel = 'AdminFutsal';
                $brandRoute = route('admin.futsal.dashboard');
            } elseif ($user->hasRole('Adminac')) {
                $brandLabel = 'Admin AC';
                $brandRoute = route('admin.ac.dashboard');
            } elseif ($user->hasRole('Adminservis')) {
                $brandLabel = 'Admin Servis';
                $brandRoute = route('admin.servis.dashboard');
            } elseif ($user->hasRole('Adminkantin')) {
                $brandLabel = 'Admin Kantin';
                $brandRoute = route('admin.kantin.dashboard');
            } elseif ($user->hasRole('Teknisi')) {
                $brandLabel = 'Teknisi AC';
                $brandRoute = route('teknisi.dashboard');
            } elseif ($user->hasRole('Teknisi Motor') || $user->hasRole('Teknisi Mobil')) {
                $brandLabel = 'Teknisi Servis';
                $brandRoute = route('teknisi.servis.dashboard');
            } elseif ($user->hasRole('Kasir')) {
                $brandLabel = 'Kasir Servis';
                $brandRoute = route('kasir.dashboard');
            } elseif ($user->hasRole('kasirfutsal')) {
                $brandLabel = 'Kasir Futsal';
                $brandRoute = route('kasirfutsal.dashboard');
            }
        }

        // Status Active untuk Menu Dropdown Pelanggan
        $futsalActive = request()->is('user/futsal*');
        $kantinActive = request()->is('user/kantin*');
        $acActive = request()->is('user/ac*');
    @endphp

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ $brandRoute }}">
        <svg class="text-white" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2v20M2 12h20"></path>
            <path d="m6 6 12 12M18 6 6 18"></path>
        </svg>
        <div class="sidebar-brand-text mx-3">{{ $brandLabel }}</div>
    </a>
    <hr class="sidebar-divider my-0">
    @else
    @php
        // Status Active untuk Menu Dropdown Pelanggan (Tetap perlu di mobile)
        $futsalActive = request()->is('user/futsal*');
        $kantinActive = request()->is('user/kantin*');
        $acActive = request()->is('user/ac*');
    @endphp
    @endif

    {{-- DASHBOARD --}}
    @if (auth()->check() && auth()->user()->hasRole('Superadmin'))
        <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
    @elseif(auth()->check() && auth()->user()->hasRole('Adminfutsal'))
        <li class="nav-item {{ request()->routeIs('admin.futsal.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.futsal.dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        </li>
    @elseif(auth()->check() && auth()->user()->hasRole('Adminac'))
        <li class="nav-item {{ request()->routeIs('admin.ac.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.ac.dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
    @elseif(auth()->check() && auth()->user()->hasRole('Adminkantin'))
        <li class="nav-item {{ request()->routeIs('admin.kantin.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.kantin.dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
    @elseif(auth()->check() && auth()->user()->hasRole('Adminservis'))
        <li class="nav-item {{ request()->routeIs('admin.servis.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.servis.dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('admin.servis.profile') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.servis.profile') }}">
                <i class="fas fa-fw fa-user"></i>
                <span>Profil Admin</span>
            </a>
        </li>
    @elseif(auth()->check() && auth()->user()->hasRole('Teknisi'))
        <li class="nav-item {{ request()->routeIs('teknisi.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('teknisi.dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
    @elseif(auth()->check() && (auth()->user()->hasRole('Teknisi Motor') || auth()->user()->hasRole('Teknisi Mobil')))
        <li class="nav-item {{ request()->routeIs('teknisi.servis.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('teknisi.servis.dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
    @endif

    <hr class="sidebar-divider">

    {{-- ================================= --}}
    {{-- SUPERADMIN ONLY --}}
    {{-- ================================= --}}
    @if (auth()->check() && auth()->user()->hasRole('Superadmin'))
        <div class="sidebar-heading">Menu Utama</div>

        {{-- Manajemen User --}}
        <li class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <a class="nav-link {{ request()->routeIs('admin.users.*') ? '' : 'collapsed' }}" href="#"
                data-toggle="collapse" data-target="#collapseUsers">
                <i class="fas fa-fw fa-users"></i>
                <span>Manajemen User</span>
            </a>

            <div id="collapseUsers" class="collapse {{ request()->routeIs('admin.users.*') ? 'show' : '' }}">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="{{ route('admin.users.index') }}">Manajemen Admin</a>
                    <a class="collapse-item" href="{{ route('admin.users.pelanggan') }}">Manajemen Pelanggan</a>
                </div>
            </div>
        </li>

        <li class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.transaksi.index') }}">
                <i class="fas fa-clipboard-list"></i>
                <span>Monitoring Transaksi</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.dashboard.rekap-keuangan') }}">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Rekap Keuangan BLUD</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.dashboard.pembagian-pendapatan') }}">
                <i class="fas fa-chart-pie"></i>
                <span>Pembagian Pendapatan BLUD</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.dashboard.monitoring') }}">
                <i class="fas fa-clipboard-list"></i>
                <span>Log Activities</span>
            </a>
        </li>
    @endif

    {{-- ================================= --}}
    {{-- ADMIN FUTSAL --}}
    {{-- ================================= --}}
    @if (auth()->check() && auth()->user()->hasRole('Adminfutsal'))
        <div class="sidebar-heading">Menu Utama</div>

        {{-- Jadwal Lapangan --}}
        <li
            class="nav-item {{ request()->routeIs('admin.futsal.jadwal-lapangan.*') || request()->routeIs('admin.futsal.pengaturan.*') || request()->routeIs('admin.futsal.lapangan.*') ? 'active' : '' }}">
            <a class="nav-link {{ request()->routeIs('admin.futsal.jadwal-lapangan.*') || request()->routeIs('admin.futsal.pengaturan.*') || request()->routeIs('admin.futsal.lapangan.*') ? '' : 'collapsed' }}"
                href="#" data-toggle="collapse" data-target="#collapseJadwal">
                <i class="fas fa-fw fa-calendar-alt"></i>
                <span>Manajemen Lapangan</span>
            </a>

            <div id="collapseJadwal"
                class="collapse {{ request()->routeIs('admin.futsal.jadwal-lapangan.*') || request()->routeIs('admin.futsal.pengaturan.*') || request()->routeIs('admin.futsal.lapangan.*') ? 'show' : '' }}">

                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item {{ request()->routeIs('admin.futsal.lapangan.*') ? 'active' : '' }}" href="{{ route('admin.futsal.lapangan.index') }}">Daftar Lapangan</a>
                    <a class="collapse-item {{ request()->routeIs('admin.futsal.jadwal-lapangan.*') ? 'active' : '' }}" href="{{ route('admin.futsal.jadwal-lapangan.index') }}">Jadwal Lapangan</a>
                    <a class="collapse-item {{ request()->routeIs('admin.futsal.pengaturan.*') ? 'active' : '' }}" href="{{ route('admin.futsal.pengaturan.index') }}">Pengaturan Dasar</a>
                </div>
            </div>
        </li>

        {{-- Transaksi --}}
        <li class="nav-item {{ request()->routeIs('admin.futsal.transaksi.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.futsal.transaksi.index') }}">
                <i class="fas fa-fw fa-file-invoice-dollar"></i>
                <span>Manajemen Transaksi</span>
            </a>
        </li>

        {{-- Booking --}}
        <li class="nav-item {{ request()->routeIs('admin.futsal.booking.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.futsal.booking.index') }}">
                <i class="fas fa-fw fa-calendar-check"></i>
                <span>Manajemen Booking</span>
            </a>
        </li>

        {{-- Membership --}}
        <li
            class="nav-item {{ request()->routeIs('admin.futsal.paket-membership.*') || request()->routeIs('admin.futsal.monitoring-membership.*') ? 'active' : '' }}">
            <a class="nav-link {{ request()->routeIs('admin.futsal.paket-membership.*') || request()->routeIs('admin.futsal.monitoring-membership.*') ? '' : 'collapsed' }}"
                href="#" data-toggle="collapse" data-target="#collapseMembership">

                <i class="fas fa-fw fa-id-card"></i>
                <span>Manajemen Paket</span>
            </a>

            <div id="collapseMembership"
                class="collapse {{ request()->routeIs('admin.futsal.paket-membership.*') || request()->routeIs('admin.futsal.monitoring-membership.*') ? 'show' : '' }}">

                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="{{ route('admin.futsal.paket-membership.index') }}">
                        Pilihan Paket
                    </a>
                    <a class="collapse-item" href="{{ route('admin.futsal.monitoring-paket.index') }}">
                        Monitoring Paket
                    </a>
                </div>
            </div>
        </li>

        {{-- Manajemen Pelanggan Reguler --}}
        <li class="nav-item {{ request()->routeIs('admin.futsal.pelanggan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.futsal.pelanggan.index') }}">
                <i class="fas fa-fw fa-users"></i>
                <span>Manajemen Pelanggan Reguler</span>
            </a>
        </li>

        {{-- Manajemen Keuangan --}}
        <li class="nav-item {{ request()->routeIs('admin.futsal.keuangan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.futsal.keuangan.index') }}">
                <i class="fas fa-fw fa-wallet"></i>
                <span>Manajemen Keuangan</span>
            </a>
        </li>

        {{-- Laporan Transaksi --}}
        <li class="nav-item {{ request()->routeIs('admin.futsal.laporan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.futsal.laporan.index') }}">
                <i class="fas fa-fw fa-file-pdf"></i>
                <span>Laporan Transaksi</span>
            </a>
        </li>
    @endif

    {{-- ================================= --}}
    {{-- ADMIN KANTIN --}}
    {{-- ================================= --}}
    @if (auth()->check() && auth()->user()->hasRole('Adminkantin'))
        <div class="sidebar-heading">Menu Utama</div>

        {{-- Manajemen Unit --}}
        <li class="nav-item {{ request()->routeIs('admin.kantin.unit.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.kantin.unit.index') }}">
                <i class="fas fa-fw fa-store"></i>
                <span>Manajemen Unit</span>
            </a>
        </li>

        {{-- Manajemen Penyewa --}}
        <li class="nav-item {{ request()->routeIs('admin.kantin.penyewa.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.kantin.penyewa.index') }}">
                <i class="fas fa-fw fa-user-tie"></i>
                <span>Manajemen Penyewa</span>
            </a>
        </li>

        {{-- Manajemen Penyewaan --}}
        <li class="nav-item {{ request()->routeIs('admin.kantin.penyewaan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.kantin.penyewaan.index') }}">
                <i class="fas fa-fw fa-user-tie"></i>
                <span>Manajemen Penyewaan</span>
            </a>
        </li>

        {{-- Manajemen Pembyaran --}}
        <li class="nav-item {{ request()->routeIs('admin.kantin.pembayaran.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.kantin.pembayaran.index') }}">
                <i class="fas fa-fw fa-file-invoice-dollar"></i>
                <span>Manajemen Pembayaran</span>
            </a>
        </li>

        {{-- Laporan Transaksi --}}
        <li class="nav-item {{ request()->routeIs('admin.kantin.laporan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.kantin.laporan.index') }}">
                <i class="fas fa-fw fa-file-pdf"></i>
                <span>Laporan Transaksi</span>
            </a>
        </li>

        {{-- Manajemen Keuangan --}}
        <li class="nav-item {{ request()->routeIs('admin.kantin.keuangan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.kantin.keuangan.index') }}">
                <i class="fas fa-fw fa-wallet"></i>
                <span>Manajemen Keuangan</span>
            </a>
        </li>

        {{-- Audit Log --}}
        <li class="nav-item {{ request()->routeIs('admin.kantin.audit.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.kantin.audit.index') }}">
                <i class="fas fa-fw fa-history"></i>
                <span>Audit Log</span>
            </a>
        </li>
    @endif

    {{-- ================================= --}}
    {{-- ADMIN AC --}}
    {{-- ================================= --}}
    @if (auth()->check() && auth()->user()->hasRole('Adminac'))
        <div class="sidebar-heading">Menu Utama</div>

        {{-- Manajemen Layanan  --}}
        <li class="nav-item {{ request()->routeIs('admin.ac.layanan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.ac.layanan.index') }}">
                <i class="fas fa-fw fa-wrench"></i>
                <span>Manajemen Layanan</span>
            </a>
        </li>

        {{-- Manajemen Produk --}}
        <li class="nav-item {{ request()->routeIs('admin.ac.produk.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.ac.produk.index') }}">
                <i class="fas fa-fw fa-box"></i>
                <span>Manajemen Produk</span>
            </a>
        </li>

        {{-- Manajemen Teknisi --}}
        <li class="nav-item {{ request()->routeIs('admin.ac.teknisi.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.ac.teknisi.index') }}">
                <i class="fas fa-fw fa-user-cog"></i>
                <span>Manajemen Teknisi</span>
            </a>
        </li>

        {{-- Manajemen Booking --}}
        <li class="nav-item {{ request()->routeIs('admin.ac.booking.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.ac.booking.index') }}">
                <i class="fas fa-fw fa-calendar-check"></i>
                <span>Manajemen Booking</span>
            </a>
        </li>

        {{-- Manajemen Pelanggan --}}
        <li class="nav-item {{ request()->routeIs('admin.ac.pelanggan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.ac.pelanggan.index') }}">
                <i class="fas fa-fw fa-users"></i>
                <span>Manajemen Pelanggan</span>
            </a>
        </li>

        {{-- Manajemen Keuangan --}}
        <li class="nav-item {{ request()->routeIs('admin.ac.keuangan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.ac.keuangan.index') }}">
                <i class="fas fa-fw fa-wallet"></i>
                <span>Manajemen Keuangan</span>
            </a>
        </li>
    @endif

    {{-- ================================= --}}
    {{-- ADMIN SERVIS --}}
    {{-- ================================= --}}
    @if (auth()->check() && auth()->user()->hasRole('Adminservis'))
        <div class="sidebar-heading">Menu Utama</div>

         {{-- Manajemen produk --}}
         <li class="nav-item {{ request()->routeIs('admin.servis.produk.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.servis.produk.index') }}">
                <i class="fas fa-fw fa-tools"></i>
                <span>Manajemen Produk</span>
            </a>
        </li>

        {{-- Manajemen Layanan --}}
        <li class="nav-item {{ request()->routeIs('admin.servis.layanan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.servis.layanan.index') }}">
                <i class="fas fa-fw fa-tools"></i>
                <span>Manajemen Layanan</span>
            </a>
        </li>

        {{-- Manajemen Pelanggan --}}
        <li class="nav-item {{ request()->routeIs('admin.servis.pelanggan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.servis.pelanggan.index') }}">
                <i class="fas fa-fw fa-users"></i>
                <span>Manajemen Pelanggan</span>
            </a>
        </li>

        {{-- Manajemen Teknisi --}}
        <li class="nav-item {{ request()->routeIs('admin.servis.teknisi.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.servis.teknisi.index') }}">
                <i class="fas fa-fw fa-user-cog"></i>
                <span>Manajemen Teknisi</span>
            </a>
        </li>

        {{-- Manajemen Booking --}}
        <li class="nav-item {{ request()->routeIs('admin.servis.booking.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.servis.booking.index') }}">
                <i class="fas fa-fw fa-calendar-check"></i>
                <span>Manajemen Booking</span>
            </a>
        </li>

        {{-- Monitoring Transaksi --}}
        <li class="nav-item {{ request()->routeIs('admin.servis.transaksi.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.servis.transaksi.index') }}">
                <i class="fas fa-fw fa-file-invoice-dollar"></i>
                <span>Monitoring Transaksi</span>
            </a>
        </li>
        {{-- Data Kendaraan --}}
        @php
            $kendaraanActive = request()->routeIs('admin.servis.merek.*') || request()->routeIs('admin.servis.model.*');
        @endphp
        <li class="nav-item {{ $kendaraanActive ? 'active' : '' }}">
            <a class="nav-link {{ $kendaraanActive ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseKendaraan">
                <i class="fas fa-fw fa-car"></i>
                <span>Data Kendaraan</span>
            </a>
            <div id="collapseKendaraan" class="collapse {{ $kendaraanActive ? 'show' : '' }}">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item {{ request()->routeIs('admin.servis.merek.*') ? 'active' : '' }}" href="{{ route('admin.servis.merek.index') }}">
                        Merek Kendaraan
                    </a>
                    <a class="collapse-item {{ request()->routeIs('admin.servis.model.*') ? 'active' : '' }}" href="{{ route('admin.servis.model.index') }}">
                        Model Kendaraan
                    </a>
                </div>
            </div>
        </li>

        {{-- Manajemen Keuangan --}}
        <li class="nav-item {{ request()->routeIs('admin.servis.keuangan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.servis.keuangan.index') }}">
                <i class="fas fa-fw fa-wallet"></i>
                <span>Manajemen Keuangan</span>
            </a>
        </li>
                
        {{-- Laporan Transaksi --}}
        <li class="nav-item {{ request()->routeIs('admin.servis.laporan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.servis.laporan.index') }}">
                <i class="fas fa-fw fa-chart-bar"></i>
                <span>Laporan Transaksi</span>
            </a>
        </li>
    @endif

    {{-- ================================= --}}
    {{-- TEKNISI AC --}}
    {{-- ================================= --}}
    @if (auth()->check() && auth()->user()->hasRole('Teknisi'))
        <div class="sidebar-heading">Menu Pekerjaan</div>

        {{-- Detail Servis --}}
        <li
            class="nav-item {{ request()->routeIs('teknisi.pekerjaan.*') || request()->routeIs('teknisi.dashboard*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('teknisi.dashboard') }}">
                <i class="fas fa-fw fa-tools"></i>
                <span>Detail Servis</span>
            </a>
        </li>

    @endif

    {{-- ================================= --}}
    {{-- TEKNISI SERVIS (MOTOR & MOBIL) --}}
    {{-- ================================= --}}
    @if (auth()->check() && (auth()->user()->hasRole('Teknisi Motor') || auth()->user()->hasRole('Teknisi Mobil')))
        <div class="sidebar-heading">Menu Pekerjaan</div>

        {{-- Pekerjaan Aktif --}}
        <li class="nav-item {{ request()->routeIs('teknisi.servis.dashboard*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('teknisi.servis.dashboard') }}">
                <i class="fas fa-fw fa-tools"></i>
                <span>Pekerjaan Aktif</span>
            </a>
        </li>
    @endif



    {{-- ================================= --}}
    {{-- KASIR SERVIS MOTOR MOBIL --}}
    {{-- ================================= --}}
    @if (auth()->check() && auth()->user()->hasRole('Kasir'))

        <div class="sidebar-heading">Dashboard</div>

        <li class="nav-item {{ request()->routeIs('kasir.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('kasir.dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>

        {{-- Manajemen Booking --}}
        <div class="sidebar-heading">Menu Pekerjaan</div>

        <li
            class="nav-item {{ request()->routeIs('kasir.booking.*') || request()->routeIs('kasir.dashboard*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('kasir.booking.index') }}">
                <i class="fas fa-fw fa-calendar-check"></i>
                <span>Manajemen Booking</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('kasir.pembayaran.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('kasir.pembayaran.index') }}">
                <i class="fas fa-fw fa-cash-register"></i>
                <span>Menu Pembayaran</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('kasir.laporan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('kasir.laporan.index') }}">
                <i class="fas fa-fw fa-file-pdf"></i>
                <span>Laporan Transaksi</span>
            </a>
        </li>
    @elseif (auth()->check() && auth()->user()->hasRole('kasirfutsal'))

        {{-- Menu Kasir Futsal --}}
        <div class="sidebar-heading">Menu Kasir Futsal</div>

        <li class="nav-item {{ request()->routeIs('kasirfutsal.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('kasirfutsal.dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('kasirfutsal.booking.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('kasirfutsal.booking.index') }}">
                <i class="fas fa-fw fa-calendar-check"></i>
                <span>Booking</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('kasirfutsal.pembayaran.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('kasirfutsal.pembayaran.index') }}">
                <i class="fas fa-fw fa-money-bill-wave"></i>
                <span>Pembayaran</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('kasirfutsal.laporan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('kasirfutsal.laporan.index') }}">
                <i class="fas fa-fw fa-file-alt"></i>
                <span>Laporan Harian</span>
            </a>
        </li>
    @endif

    {{-- ================================= --}}
    {{-- PELANGGAN --}}
    {{-- ================================= --}}
    {{-- MENU LAYANAN PELANGGAN (Dapat diakses tanpa login) --}}
    @if (!auth()->check() || (auth()->check() && auth()->user()->hasRole('Pelanggan')))
        <div class="sidebar-heading">Menu Akun</div>


        {{-- Dashboard Utama --}}
        <li class="nav-item {{ request()->routeIs('user.gateway') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('user.gateway') }}">
                <i class="fas fa-fw fa-home"></i>
                <span>Dashboard Utama</span>
            </a>
        </li>

        <hr class="sidebar-divider">
        <div class="sidebar-heading">Layanan Futsal</div>

        {{-- Sistem Futsal --}}
        <li class="nav-item {{ $futsalActive ? 'active' : '' }}">
            <a class="nav-link {{ $futsalActive ? '' : 'collapsed' }}" href="#" data-toggle="collapse"
                data-target="#collapseFutsal" aria-expanded="{{ $futsalActive ? 'true' : 'false' }}">
                <i class="fas fa-fw fa-futbol"></i>
                <span>Sistem Futsal</span>
            </a>
            <div id="collapseFutsal" class="collapse {{ $futsalActive ? 'show' : '' }}">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item {{ request()->routeIs('user.gateway') ? 'active' : '' }}"
                        href="{{ route('user.gateway') }}">
                        <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                    </a>
                    <a class="collapse-item {{ request()->routeIs('user.futsal.history') ? 'active' : '' }}"
                        href="#" {{-- TODO: ganti ke token-based route --}}>
                        <i class="fas fa-history mr-1"></i> Histori Booking
                    </a>
                </div>
            </div>
        </li>

        <hr class="sidebar-divider">
        <div class="sidebar-heading">Layanan Sewa Kantin</div>

        {{-- Sistem Sewa Kantin --}}
        <li class="nav-item {{ $kantinActive ? 'active' : '' }}">
            <a class="nav-link {{ $kantinActive ? '' : 'collapsed' }}" href="#" data-toggle="collapse"
                data-target="#collapseKantin" aria-expanded="{{ $kantinActive ? 'true' : 'false' }}">
                <i class="fas fa-fw fa-store"></i>
                <span>Sistem Sewa Kantin</span>
            </a>
            <div id="collapseKantin" class="collapse {{ $kantinActive ? 'show' : '' }}">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item {{ request()->routeIs('user.gateway') ? 'active' : '' }}"
                        href="{{ route('user.gateway') }}">
                        <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                    </a>
                    <a class="collapse-item {{ request()->routeIs('user.kantin.tagihan') ? 'active' : '' }}"
                        href="{{ route('user.kantin.tagihan') }}">
                        <i class="fas fa-file-invoice mr-2"></i> Tagihan
                    </a>
                    <a class="collapse-item {{ request()->routeIs('user.kantin.riwayat') ? 'active' : '' }}"
                        href="#" {{-- TODO: ganti ke token-based route --}}>
                        <i class="fas fa-history mr-1"></i> Riwayat Pembayaran
                    </a>
                </div>
            </div>
        </li>

        <hr class="sidebar-divider">
        <div class="sidebar-heading">Layanan Servis AC</div>

        {{-- Sistem Servis AC --}}
        <li class="nav-item {{ $acActive ? 'active' : '' }}">
            <a class="nav-link {{ $acActive ? '' : 'collapsed' }}" href="#" data-toggle="collapse"
                data-target="#collapseAC" aria-expanded="{{ $acActive ? 'true' : 'false' }}">
                <i class="fas fa-fw fa-tools"></i>
                <span>Sistem Servis AC</span>
            </a>
            <div id="collapseAC" class="collapse {{ $acActive ? 'show' : '' }}" aria-labelledby="headingAC">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item {{ request()->routeIs('user.ac.index') ? 'active' : '' }}" href="{{ route('user.gateway') }}">
                        <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                    </a>
                    <a class="collapse-item {{ request()->routeIs('user.ac.history') ? 'active' : '' }}" href="#" {{-- TODO: ganti ke token-based route --}}>
                        <i class="fas fa-history mr-1"></i> Histori Booking
                    </a>
                </div>
            </div>
        </li>

        <hr class="sidebar-divider">
        <div class="sidebar-heading">Layanan Servis Motor & Mobil</div>

        {{-- Sistem Servis --}}
        @php $servisActive = request()->is('user/servis*'); @endphp
        <li class="nav-item {{ $servisActive ? 'active' : '' }}">
            <a class="nav-link {{ $servisActive ? '' : 'collapsed' }}" href="#" data-toggle="collapse"
                data-target="#collapseServis" aria-expanded="{{ $servisActive ? 'true' : 'false' }}">
                <i class="fas fa-fw fa-car"></i>
                <span>Servis Motor & Mobil</span>
            </a>
            <div id="collapseServis" class="collapse {{ $servisActive ? 'show' : '' }}">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item {{ request()->routeIs('user.servis.katalog') ? 'active' : '' }}" href="{{ route('user.gateway') }}">
                        <i class="fas fa-tachometer-alt mr-1"></i> Dashboard Servis
                    </a>
                    <a class="collapse-item {{ request()->routeIs('user.servis.history') ? 'active' : '' }}" href="#" {{-- TODO: ganti ke token-based route --}}>
                        <i class="fas fa-history mr-1"></i> Histori Servis
                    </a>
                </div>
            </div>
        </li>
    @endif

    <hr class="sidebar-divider d-none d-md-block">

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Force show semua collapse yang seharusnya terbuka
            document.querySelectorAll('.collapse.show').forEach(function(el) {
                el.style.display = 'block';
                el.style.height = '';
            });

            // Pastikan toggle state benar jika ada yang terbuka
            document.querySelectorAll('.collapse.show').forEach(function(el) {
                var toggle = document.querySelector('[data-target="#' + el.id + '"]');
                if (toggle) {
                    toggle.classList.remove('collapsed');
                    toggle.setAttribute('aria-expanded', 'true');
                }
            });
        });
    </script>

</ul>
{{-- End Sidebar --}}
