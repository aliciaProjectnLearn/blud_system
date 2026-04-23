@php
    $isMobile = $isMobile ?? false;
@endphp

<ul class="{{ $isMobile ? 'navbar-nav' : 'navbar-nav bg-gradient-primary sidebar sidebar-dark accordion' }}" id="{{ $isMobile ? 'mobileAccordionSidebar' : 'accordionSidebar' }}">

    {{-- Sidebar Brand --}}
    @if(!$isMobile)
    @php
        $brandLabel = 'Admin BLUD';
        $brandRoute = route('dashboard');

        if (auth()->user()->hasRole('Adminfutsal')) {
            $brandLabel = 'AdminFutsal';
            $brandRoute = route('adminfutsal.dashboard');
        } elseif (auth()->user()->hasRole('Adminac')) {
            $brandLabel = 'Admin AC';
            $brandRoute = route('adminac.dashboard');
        } elseif (auth()->user()->hasRole('Adminservis')) {
            $brandLabel = 'Admin Servis';
            $brandRoute = route('adminservis.dashboard');
        } elseif (auth()->user()->hasRole('Adminkantin')) {
            $brandLabel = 'Admin Kantin';
            $brandRoute = route('adminkantin.dashboard');
        } elseif (auth()->user()->hasRole('Teknisi')) {
            $brandLabel = 'Teknisi AC';
            $brandRoute = route('teknisi.dashboard');
        } elseif (auth()->user()->hasRole('Kasir')) {
            $brandLabel = 'Kasir Servis';
            $brandRoute = route('kasir.dashboard');
        } elseif (auth()->user()->hasRole('Pelanggan')) {
            $brandLabel = 'Sistem BLUD';
            $brandRoute = route('user.dashboard');
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
        </li>
    @elseif(auth()->user()->hasRole('Adminac'))
        <li class="nav-item {{ request()->routeIs('adminac.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminac.dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
    @elseif(auth()->user()->hasRole('Adminkantin'))
        <li class="nav-item {{ request()->routeIs('adminkantin.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminkantin.dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
    @elseif(auth()->user()->hasRole('Adminservis'))
        <li class="nav-item {{ request()->routeIs('adminservis.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminservis.dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('adminservis.profile') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminservis.profile') }}">
                <i class="fas fa-fw fa-user"></i>
                <span>Profil Admin</span>
            </a>
        </li>
    @elseif(auth()->user()->hasRole('Teknisi'))
        <li class="nav-item {{ request()->routeIs('teknisi.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('teknisi.dashboard') }}">
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

        <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('transaksi.index') }}">
                <i class="fas fa-clipboard-list"></i>
                <span>Monitoring Transaksi</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('dashboard.rekap-keuangan') }}">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Rekap Keuangan BLUD</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('dashboard.pembagian-pendapatan') }}">
                <i class="fas fa-chart-pie"></i>
                <span>Pembagian Pendapatan BLUD</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('dashboard.monitoring') }}">
                <i class="fas fa-clipboard-list"></i>
                <span>Log Activities</span>
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

        {{-- Manajemen Keuangan --}}
        <li class="nav-item {{ request()->routeIs('adminfutsal.keuangan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminfutsal.keuangan.index') }}">
                <i class="fas fa-fw fa-wallet"></i>
                <span>Manajemen Keuangan</span>
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
    {{-- ADMIN KANTIN --}}
    {{-- ================================= --}}
    @if (auth()->user()->hasRole('Adminkantin'))
        <div class="sidebar-heading">Menu Utama</div>

        {{-- Manajemen Unit --}}
        <li class="nav-item {{ request()->routeIs('adminkantin.unit.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminkantin.unit.index') }}">
                <i class="fas fa-fw fa-store"></i>
                <span>Manajemen Unit</span>
            </a>
        </li>

        {{-- Manajemen Penyewa --}}
        <li class="nav-item {{ request()->routeIs('adminkantin.penyewa.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminkantin.penyewa.index') }}">
                <i class="fas fa-fw fa-user-tie"></i>
                <span>Manajemen Penyewa</span>
            </a>
        </li>

        {{-- Manajemen Penyewaan --}}
        <li class="nav-item {{ request()->routeIs('adminkantin.penyewaan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminkantin.penyewaan.index') }}">
                <i class="fas fa-fw fa-user-tie"></i>
                <span>Manajemen Penyewaan</span>
            </a>
        </li>

        {{-- Manajemen Pembyaran --}}
        <li class="nav-item {{ request()->routeIs('adminkantin.pembayaran.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminkantin.pembayaran.index') }}">
                <i class="fas fa-fw fa-file-invoice-dollar"></i>
                <span>Manajemen Pembayaran</span>
            </a>
        </li>

        {{-- Laporan Transaksi --}}
        <li class="nav-item {{ request()->routeIs('adminkantin.laporan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminkantin.laporan.index') }}">
                <i class="fas fa-fw fa-file-pdf"></i>
                <span>Laporan Transaksi</span>
            </a>
        </li>

        {{-- Manajemen Keuangan --}}
        <li class="nav-item {{ request()->routeIs('adminkantin.keuangan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminkantin.keuangan.index') }}">
                <i class="fas fa-fw fa-wallet"></i>
                <span>Manajemen Keuangan</span>
            </a>
        </li>
    @endif

    {{-- ================================= --}}
    {{-- ADMIN AC --}}
    {{-- ================================= --}}
    @if (auth()->user()->hasRole('Adminac'))
        <div class="sidebar-heading">Menu Utama</div>

        {{-- Manajemen Layanan  --}}
        <li class="nav-item {{ request()->routeIs('adminac.layanan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminac.layanan.index') }}">
                <i class="fas fa-fw fa-wrench"></i>
                <span>Manajemen Layanan</span>
            </a>
        </li>

        {{-- Manajemen Produk --}}
        <li class="nav-item {{ request()->routeIs('adminac.produk.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminac.produk.index') }}">
                <i class="fas fa-fw fa-box"></i>
                <span>Manajemen Produk</span>
            </a>
        </li>

        {{-- Manajemen Teknisi --}}
        <li class="nav-item {{ request()->routeIs('adminac.teknisi.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminac.teknisi.index') }}">
                <i class="fas fa-fw fa-user-cog"></i>
                <span>Manajemen Teknisi</span>
            </a>
        </li>

        {{-- Manajemen Booking --}}
        <li class="nav-item {{ request()->routeIs('adminac.booking.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminac.booking.index') }}">
                <i class="fas fa-fw fa-calendar-check"></i>
                <span>Manajemen Booking</span>
            </a>
        </li>

        {{-- Manajemen Pelanggan --}}
        <li class="nav-item {{ request()->routeIs('adminac.pelanggan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminac.pelanggan.index') }}">
                <i class="fas fa-fw fa-users"></i>
                <span>Manajemen Pelanggan</span>
            </a>
        </li>

        {{-- Manajemen Keuangan --}}
        <li class="nav-item {{ request()->routeIs('adminac.keuangan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminac.keuangan.index') }}">
                <i class="fas fa-fw fa-wallet"></i>
                <span>Manajemen Keuangan</span>
            </a>
        </li>
    @endif

    {{-- ================================= --}}
    {{-- ADMIN SERVIS --}}
    {{-- ================================= --}}
    @if (auth()->user()->hasRole('Adminservis'))
        <div class="sidebar-heading">Menu Utama</div>

         {{-- Manajemen produk --}}
         <li class="nav-item {{ request()->routeIs('adminservis.produk.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminservis.produk.index') }}">
                <i class="fas fa-fw fa-tools"></i>
                <span>Manajemen Produk</span>
            </a>
        </li>

        {{-- Manajemen Layanan --}}
        <li class="nav-item {{ request()->routeIs('adminservis.layanan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminservis.layanan.index') }}">
                <i class="fas fa-fw fa-tools"></i>
                <span>Manajemen Layanan</span>
            </a>
        </li>

        {{-- Manajemen Pelanggan --}}
        <li class="nav-item {{ request()->routeIs('adminservis.pelanggan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminservis.pelanggan.index') }}">
                <i class="fas fa-fw fa-users"></i>
                <span>Manajemen Pelanggan</span>
            </a>
        </li>

        {{-- Manajemen Teknisi --}}
        <li class="nav-item {{ request()->routeIs('adminservis.teknisi.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminservis.teknisi.index') }}">
                <i class="fas fa-fw fa-user-cog"></i>
                <span>Manajemen Teknisi</span>
            </a>
        </li>

        {{-- Manajemen Booking --}}
        <li class="nav-item {{ request()->routeIs('adminservis.booking.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminservis.booking.index') }}">
                <i class="fas fa-fw fa-calendar-check"></i>
                <span>Manajemen Booking</span>
            </a>
        </li>

        {{-- Monitoring Transaksi --}}
        <li class="nav-item {{ request()->routeIs('adminservis.transaksi.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminservis.transaksi.index') }}">
                <i class="fas fa-fw fa-file-invoice-dollar"></i>
                <span>Monitoring Transaksi</span>
            </a>
        </li>

        {{-- Laporan Transaksi --}}
        <li class="nav-item {{ request()->routeIs('adminservis.laporan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminservis.laporan.index') }}">
                <i class="fas fa-fw fa-chart-bar"></i>
                <span>Laporan Transaksi</span>
            </a>
        </li>
    @endif

    {{-- ================================= --}}
    {{-- TEKNISI AC --}}
    {{-- ================================= --}}
    @if (auth()->user()->hasRole('Teknisi'))
        <div class="sidebar-heading">Menu Pekerjaan</div>

        {{-- Detail Servis --}}
        <li
            class="nav-item {{ request()->routeIs('teknisi.pekerjaan.*') || request()->routeIs('teknisi.dashboard*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('teknisi.dashboard') }}">
                <i class="fas fa-fw fa-tools"></i>
                <span>Detail Servis</span>
            </a>
        </li>

        {{-- Manajemen Transaksi --}}
        <li class="nav-item {{ request()->routeIs('adminac.transaksi.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminac.transaksi.index') }}">
                <i class="fas fa-fw fa-file-invoice-dollar"></i>
                <span>Manajemen Transaksi</span>
            </a>
        </li>

        {{-- Laporan Transaksi --}}
        <li class="nav-item {{ request()->routeIs('adminac.laporan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminac.laporan.index') }}">
                <i class="fas fa-fw fa-file-pdf"></i>
                <span>Laporan Transaksi</span>
            </a>
        </li>
    @endif

    {{-- ================================= --}}
    {{-- KASIR SERVIS MOTOR MOBIL --}}
    {{-- ================================= --}}
    @if (auth()->user()->hasRole('Kasir'))

        {{-- Manajemen Booking --}}
        <div class="sidebar-heading">Menu Pekerjaan</div>

         <li
            class="nav-item {{ request()->routeIs('kasir.booking.*') || request()->routeIs('kasir.dashboard*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('kasir.booking.index') }}">
                <i class="fas fa-fw fa-calendar-check"></i>
                <span>Manajemen Booking</span>
            </a>
        </li>
    @endif

    {{-- ================================= --}}
    {{-- PELANGGAN --}}
    {{-- ================================= --}}
    @if (auth()->user()->hasRole('Pelanggan'))
        <div class="sidebar-heading">Menu Akun</div>

        {{-- Profile --}}
        <li class="nav-item {{ request()->routeIs('user.profile.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('user.profile.index') }}">
                <i class="fas fa-fw fa-user"></i>
                <span>Profil Saya</span>
            </a>
        </li>

        {{-- Dashboard Utama --}}
        <li class="nav-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('user.dashboard') }}">
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
                    <a class="collapse-item {{ request()->routeIs('user.futsal.dashboard') ? 'active' : '' }}"
                        href="{{ route('user.futsal.dashboard') }}">
                        <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                    </a>
                    <a class="collapse-item {{ request()->routeIs('user.futsal.history') ? 'active' : '' }}"
                        href="{{ route('user.futsal.history') }}">
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
                    <a class="collapse-item {{ request()->routeIs('user.kantin.dashboard') ? 'active' : '' }}"
                        href="{{ route('user.kantin.dashboard') }}">
                        <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                    </a>
                    <a class="collapse-item {{ request()->routeIs('user.kantin.tagihan') ? 'active' : '' }}"
                        href="{{ route('user.kantin.tagihan') }}">
                        <i class="fas fa-file-invoice mr-2"></i> Tagihan
                    </a>
                    <a class="collapse-item {{ request()->routeIs('user.kantin.riwayat') ? 'active' : '' }}"
                        href="{{ route('user.kantin.riwayat') }}">
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
                    <a class="collapse-item {{ request()->routeIs('user.ac.index') ? 'active' : '' }}" href="{{ route('user.ac.index') }}">
                        <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                    </a>
                    <a class="collapse-item {{ request()->routeIs('user.ac.history') ? 'active' : '' }}" href="{{ route('user.ac.history') }}">
                        <i class="fas fa-history mr-1"></i> Histori Booking
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
