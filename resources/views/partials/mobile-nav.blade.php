@php
    $futsalActive = request()->is('user/futsal*');
    $kantinActive = request()->is('user/kantin*');
    $acActive     = request()->is('user/ac*');
@endphp

{{-- ══ SUPERADMIN ══ --}}
@if(auth()->user()->hasRole('Superadmin'))
    <div class="mobile-nav-heading">Menu Utama</div>
    <a href="{{ route('dashboard') }}" class="mobile-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>
    <div class="mobile-nav-divider"></div>
    <div class="mobile-nav-heading">Manajemen</div>
    <a href="{{ route('users.index') }}" class="mobile-nav-item {{ request()->routeIs('users.index') ? 'active' : '' }}">
        <i class="fas fa-users"></i> Manajemen Admin
    </a>
    <a href="{{ route('users.pelanggan') }}" class="mobile-nav-item {{ request()->routeIs('users.pelanggan') ? 'active' : '' }}">
        <i class="fas fa-user"></i> Manajemen Pelanggan
    </a>
    <a href="{{ route('transaksi.index') }}" class="mobile-nav-item {{ request()->routeIs('transaksi.index') ? 'active' : '' }}">
        <i class="fas fa-clipboard-list"></i> Monitoring Transaksi
    </a>
@endif

{{-- ══ ADMIN FUTSAL ══ --}}
@if(auth()->user()->hasRole('Adminfutsal'))
    <a href="{{ route('adminfutsal.dashboard') }}" class="mobile-nav-item {{ request()->routeIs('adminfutsal.dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>
    <div class="mobile-nav-divider"></div>
    <div class="mobile-nav-heading">Menu Utama</div>

    <button class="mobile-nav-toggle {{ request()->routeIs('adminfutsal.jadwal-lapangan.*','adminfutsal.pengaturan.*') ? '' : 'collapsed' }}"
        data-target="#mn-lapangan">
        <i class="fas fa-calendar-alt"></i> Manajemen Lapangan
        <i class="fas fa-chevron-down toggle-icon"></i>
    </button>
    <div id="mn-lapangan" class="mobile-nav-submenu" style="{{ request()->routeIs('adminfutsal.jadwal-lapangan.*','adminfutsal.pengaturan.*') ? '' : 'display:none' }}">
        <a href="{{ route('adminfutsal.jadwal-lapangan.index') }}" class="mobile-nav-subitem">Jadwal Lapangan</a>
        <a href="{{ route('adminfutsal.pengaturan.index') }}" class="mobile-nav-subitem">Jam Operasional</a>
    </div>

    <a href="{{ route('adminfutsal.transaksi.index') }}" class="mobile-nav-item {{ request()->routeIs('adminfutsal.transaksi.*') ? 'active' : '' }}">
        <i class="fas fa-file-invoice-dollar"></i> Manajemen Transaksi
    </a>
    <a href="{{ route('adminfutsal.booking.index') }}" class="mobile-nav-item {{ request()->routeIs('adminfutsal.booking.*') ? 'active' : '' }}">
        <i class="fas fa-calendar-check"></i> Manajemen Booking
    </a>

    <button class="mobile-nav-toggle {{ request()->routeIs('adminfutsal.paket-membership.*','adminfutsal.monitoring-membership.*') ? '' : 'collapsed' }}"
        data-target="#mn-membership">
        <i class="fas fa-id-card"></i> Manajemen Membership
        <i class="fas fa-chevron-down toggle-icon"></i>
    </button>
    <div id="mn-membership" class="mobile-nav-submenu" style="{{ request()->routeIs('adminfutsal.paket-membership.*','adminfutsal.monitoring-membership.*') ? '' : 'display:none' }}">
        <a href="{{ route('adminfutsal.paket-membership.index') }}" class="mobile-nav-subitem">Paket Membership</a>
        <a href="{{ route('adminfutsal.monitoring-membership.index') }}" class="mobile-nav-subitem">Monitoring Membership</a>
    </div>

    <a href="{{ route('adminfutsal.pelanggan.index') }}" class="mobile-nav-item {{ request()->routeIs('adminfutsal.pelanggan.*') ? 'active' : '' }}">
        <i class="fas fa-users"></i> Manajemen Pelanggan
    </a>
    <a href="{{ route('adminfutsal.laporan.index') }}" class="mobile-nav-item {{ request()->routeIs('adminfutsal.laporan.*') ? 'active' : '' }}">
        <i class="fas fa-file-pdf"></i> Laporan Transaksi
    </a>
@endif

{{-- ══ ADMIN KANTIN ══ --}}
@if(auth()->user()->hasRole('Adminkantin'))
    <a href="{{ route('adminkantin.dashboard') }}" class="mobile-nav-item {{ request()->routeIs('adminkantin.dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>
    <div class="mobile-nav-divider"></div>
    <div class="mobile-nav-heading">Menu Utama</div>
    <a href="{{ route('adminkantin.unit.index') }}" class="mobile-nav-item {{ request()->routeIs('adminkantin.unit.*') ? 'active' : '' }}">
        <i class="fas fa-store"></i> Manajemen Unit
    </a>
    <a href="{{ route('adminkantin.penyewa.index') }}" class="mobile-nav-item {{ request()->routeIs('adminkantin.penyewa.*') ? 'active' : '' }}">
        <i class="fas fa-user-tie"></i> Manajemen Penyewa
    </a>
    <a href="{{ route('adminkantin.penyewaan.index') }}" class="mobile-nav-item {{ request()->routeIs('adminkantin.penyewaan.*') ? 'active' : '' }}">
        <i class="fas fa-user-tie"></i> Manajemen Penyewaan
    </a>
    <a href="{{ route('adminkantin.pembayaran.index') }}" class="mobile-nav-item {{ request()->routeIs('adminkantin.pembayaran.*') ? 'active' : '' }}">
        <i class="fas fa-file-invoice-dollar"></i> Manajemen Pembayaran
    </a>
    <a href="{{ route('adminkantin.laporan.index') }}" class="mobile-nav-item {{ request()->routeIs('adminkantin.laporan.*') ? 'active' : '' }}">
        <i class="fas fa-file-pdf"></i> Laporan Transaksi
    </a>
    <a href="{{ route('adminkantin.keuangan.index') }}" class="mobile-nav-item {{ request()->routeIs('adminkantin.keuangan.*') ? 'active' : '' }}">
        <i class="fas fa-wallet"></i> Manajemen Keuangan
    </a>
@endif

{{-- ══ ADMIN AC ══ --}}
@if(auth()->user()->hasRole('Adminac'))
    <a href="{{ route('adminac.dashboard') }}" class="mobile-nav-item {{ request()->routeIs('adminac.dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>
    <div class="mobile-nav-divider"></div>
    <div class="mobile-nav-heading">Menu Utama</div>
    <a href="{{ route('adminac.layanan.index') }}" class="mobile-nav-item {{ request()->routeIs('adminac.layanan.*') ? 'active' : '' }}">
        <i class="fas fa-wrench"></i> Manajemen Layanan
    </a>
    <a href="{{ route('adminac.produk.index') }}" class="mobile-nav-item {{ request()->routeIs('adminac.produk.*') ? 'active' : '' }}">
        <i class="fas fa-box"></i> Manajemen Produk
    </a>
    <a href="{{ route('adminac.teknisi.index') }}" class="mobile-nav-item {{ request()->routeIs('adminac.teknisi.*') ? 'active' : '' }}">
        <i class="fas fa-user-cog"></i> Manajemen Teknisi
    </a>
    <a href="{{ route('adminac.booking.index') }}" class="mobile-nav-item {{ request()->routeIs('adminac.booking.*') ? 'active' : '' }}">
        <i class="fas fa-calendar-check"></i> Manajemen Booking
    </a>
    <a href="{{ route('adminac.pelanggan.index') }}" class="mobile-nav-item {{ request()->routeIs('adminac.pelanggan.*') ? 'active' : '' }}">
        <i class="fas fa-users"></i> Manajemen Pelanggan
    </a>
    <a href="{{ route('adminac.keuangan.index') }}" class="mobile-nav-item {{ request()->routeIs('adminac.keuangan.*') ? 'active' : '' }}">
        <i class="fas fa-wallet"></i> Manajemen Keuangan
    </a>
@endif

{{-- ══ ADMIN SERVIS ══ --}}
@if(auth()->user()->hasRole('Adminservis'))
    <a href="{{ route('adminservis.dashboard') }}" class="mobile-nav-item {{ request()->routeIs('adminservis.dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>
    <a href="{{ route('adminservis.profile') }}" class="mobile-nav-item {{ request()->routeIs('adminservis.profile') ? 'active' : '' }}">
        <i class="fas fa-user"></i> Profil Admin
    </a>
    <div class="mobile-nav-divider"></div>
    <div class="mobile-nav-heading">Layanan Servis</div>
    <button class="mobile-nav-toggle collapsed" data-target="#mn-servis">
        <i class="fas fa-car"></i> Manajemen Servis
        <i class="fas fa-chevron-down toggle-icon"></i>
    </button>
    <div id="mn-servis" class="mobile-nav-submenu" style="display:none">
        <a href="#" class="mobile-nav-subitem">Data Booking</a>
        <a href="#" class="mobile-nav-subitem">Riwayat Servis</a>
    </div>
@endif

{{-- ══ TEKNISI ══ --}}
@if(auth()->user()->hasRole('Teknisi'))
    <a href="{{ route('teknisi.dashboard') }}" class="mobile-nav-item {{ request()->routeIs('teknisi.dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>
    <div class="mobile-nav-divider"></div>
    <div class="mobile-nav-heading">Menu Pekerjaan</div>
    <a href="{{ route('teknisi.dashboard') }}" class="mobile-nav-item">
        <i class="fas fa-tools"></i> Detail Servis
    </a>
    <a href="{{ route('adminac.transaksi.index') }}" class="mobile-nav-item {{ request()->routeIs('adminac.transaksi.*') ? 'active' : '' }}">
        <i class="fas fa-file-invoice-dollar"></i> Manajemen Transaksi
    </a>
    <a href="{{ route('adminac.laporan.index') }}" class="mobile-nav-item {{ request()->routeIs('adminac.laporan.*') ? 'active' : '' }}">
        <i class="fas fa-file-pdf"></i> Laporan Transaksi
    </a>
@endif

{{-- ══ PELANGGAN ══ --}}
@if(auth()->user()->hasRole('Pelanggan'))
    <div class="mobile-nav-heading">Menu Akun</div>
    <a href="{{ route('user.profile.index') }}" class="mobile-nav-item {{ request()->routeIs('user.profile.*') ? 'active' : '' }}">
        <i class="fas fa-user"></i> Profil Saya
    </a>
    <a href="{{ route('user.dashboard') }}" class="mobile-nav-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
        <i class="fas fa-home"></i> Dashboard Utama
    </a>
    <div class="mobile-nav-divider"></div>
    <div class="mobile-nav-heading">Layanan Futsal</div>

    <button class="mobile-nav-toggle {{ $futsalActive ? '' : 'collapsed' }}" data-target="#mn-futsal">
        <i class="fas fa-futbol"></i> Sistem Futsal
        <i class="fas fa-chevron-down toggle-icon"></i>
    </button>
    <div id="mn-futsal" class="mobile-nav-submenu" style="{{ $futsalActive ? '' : 'display:none' }}">
        <a href="{{ route('user.futsal.dashboard') }}" class="mobile-nav-subitem {{ request()->routeIs('user.futsal.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
        </a>
        <a href="{{ route('user.futsal.history') }}" class="mobile-nav-subitem {{ request()->routeIs('user.futsal.history') ? 'active' : '' }}">
            <i class="fas fa-history mr-1"></i> Histori Booking
        </a>
    </div>

    <div class="mobile-nav-divider"></div>
    <div class="mobile-nav-heading">Layanan Sewa Kantin</div>

    <button class="mobile-nav-toggle {{ $kantinActive ? '' : 'collapsed' }}" data-target="#mn-kantin">
        <i class="fas fa-store"></i> Sistem Sewa Kantin
        <i class="fas fa-chevron-down toggle-icon"></i>
    </button>
    <div id="mn-kantin" class="mobile-nav-submenu" style="{{ $kantinActive ? '' : 'display:none' }}">
        <a href="{{ route('user.kantin.dashboard') }}" class="mobile-nav-subitem {{ request()->routeIs('user.kantin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
        </a>
        <a href="{{ route('user.kantin.riwayat') }}" class="mobile-nav-subitem {{ request()->routeIs('user.kantin.riwayat') ? 'active' : '' }}">
            <i class="fas fa-history mr-1"></i> Riwayat Pembayaran
        </a>
    </div>

    <div class="mobile-nav-divider"></div>
    <div class="mobile-nav-heading">Layanan Servis AC</div>

    <button class="mobile-nav-toggle {{ $acActive ? '' : 'collapsed' }}" data-target="#mn-ac">
        <i class="fas fa-tools"></i> Sistem Servis AC
        <i class="fas fa-chevron-down toggle-icon"></i>
    </button>
    <div id="mn-ac" class="mobile-nav-submenu" style="{{ $acActive ? '' : 'display:none' }}">
        <a href="{{ route('user.ac.index') }}" class="mobile-nav-subitem {{ request()->routeIs('user.ac.index') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
        </a>
        <a href="{{ route('user.ac.history') }}" class="mobile-nav-subitem {{ request()->routeIs('user.ac.history') ? 'active' : '' }}">
            <i class="fas fa-history mr-1"></i> Histori Booking
        </a>
    </div>
@endif
