<?php
    $isMobile = $isMobile ?? false;
?>

<ul class="<?php echo e($isMobile ? 'navbar-nav' : 'navbar-nav bg-gradient-primary sidebar sidebar-dark accordion'); ?>" id="<?php echo e($isMobile ? 'mobileAccordionSidebar' : 'accordionSidebar'); ?>">

    
    <?php if(!$isMobile): ?>
    <?php
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
    ?>

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo e($brandRoute); ?>">
        <svg class="text-white" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2v20M2 12h20"></path>
            <path d="m6 6 12 12M18 6 6 18"></path>
        </svg>
        <div class="sidebar-brand-text mx-3"><?php echo e($brandLabel); ?></div>
    </a>
    <hr class="sidebar-divider my-0">
    <?php else: ?>
    <?php
        // Status Active untuk Menu Dropdown Pelanggan (Tetap perlu di mobile)
        $futsalActive = request()->is('user/futsal*');
        $kantinActive = request()->is('user/kantin*');
        $acActive = request()->is('user/ac*');
    ?>
    <?php endif; ?>

    
    <?php if(auth()->check() && auth()->user()->hasRole('Superadmin')): ?>
        <li class="nav-item <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.dashboard')); ?>">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
    <?php elseif(auth()->check() && auth()->user()->hasRole('Adminfutsal')): ?>
        <li class="nav-item <?php echo e(request()->routeIs('admin.futsal.dashboard') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.futsal.dashboard')); ?>">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        </li>
    <?php elseif(auth()->check() && auth()->user()->hasRole('Adminac')): ?>
        <li class="nav-item <?php echo e(request()->routeIs('admin.ac.dashboard') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.ac.dashboard')); ?>">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
    <?php elseif(auth()->check() && auth()->user()->hasRole('Adminkantin')): ?>
        <li class="nav-item <?php echo e(request()->routeIs('admin.kantin.dashboard') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.kantin.dashboard')); ?>">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
    <?php elseif(auth()->check() && auth()->user()->hasRole('Adminservis')): ?>
        <li class="nav-item <?php echo e(request()->routeIs('admin.servis.dashboard') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.servis.dashboard')); ?>">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item <?php echo e(request()->routeIs('admin.servis.profile') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.servis.profile')); ?>">
                <i class="fas fa-fw fa-user"></i>
                <span>Profil Admin</span>
            </a>
        </li>
    <?php elseif(auth()->check() && auth()->user()->hasRole('Teknisi')): ?>
        <li class="nav-item <?php echo e(request()->routeIs('teknisi.dashboard') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('teknisi.dashboard')); ?>">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
    <?php elseif(auth()->check() && (auth()->user()->hasRole('Teknisi Motor') || auth()->user()->hasRole('Teknisi Mobil'))): ?>
        <li class="nav-item <?php echo e(request()->routeIs('teknisi.servis.dashboard') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('teknisi.servis.dashboard')); ?>">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
    <?php endif; ?>

    <hr class="sidebar-divider">

    
    
    
    <?php if(auth()->check() && auth()->user()->hasRole('Superadmin')): ?>
        <div class="sidebar-heading">Menu Utama</div>

        
        <li class="nav-item <?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?>">
            <a class="nav-link <?php echo e(request()->routeIs('admin.users.*') ? '' : 'collapsed'); ?>" href="#"
                data-toggle="collapse" data-target="#collapseUsers">
                <i class="fas fa-fw fa-users"></i>
                <span>Manajemen User</span>
            </a>

            <div id="collapseUsers" class="collapse <?php echo e(request()->routeIs('admin.users.*') ? 'show' : ''); ?>">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="<?php echo e(route('admin.users.index')); ?>">Manajemen Admin</a>
                    <a class="collapse-item" href="<?php echo e(route('admin.users.pelanggan')); ?>">Manajemen Pelanggan</a>
                </div>
            </div>
        </li>

        <li class="nav-item <?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.transaksi.index')); ?>">
                <i class="fas fa-clipboard-list"></i>
                <span>Monitoring Transaksi</span>
            </a>
        </li>

        <li class="nav-item <?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.dashboard.rekap-keuangan')); ?>">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Rekap Keuangan BLUD</span>
            </a>
        </li>

        <li class="nav-item <?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.dashboard.pembagian-pendapatan')); ?>">
                <i class="fas fa-chart-pie"></i>
                <span>Pembagian Pendapatan BLUD</span>
            </a>
        </li>

        <li class="nav-item <?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.dashboard.monitoring')); ?>">
                <i class="fas fa-clipboard-list"></i>
                <span>Log Activities</span>
            </a>
        </li>
    <?php endif; ?>

    
    
    
    <?php if(auth()->check() && auth()->user()->hasRole('Adminfutsal')): ?>
        <div class="sidebar-heading">Menu Utama</div>

        
        <li
            class="nav-item <?php echo e(request()->routeIs('admin.futsal.jadwal-lapangan.*') || request()->routeIs('admin.futsal.pengaturan.*') ? 'active' : ''); ?>">
            <a class="nav-link <?php echo e(request()->routeIs('admin.futsal.jadwal-lapangan.*') || request()->routeIs('admin.futsal.pengaturan.*') ? '' : 'collapsed'); ?>"
                href="#" data-toggle="collapse" data-target="#collapseJadwal">
                <i class="fas fa-fw fa-calendar-alt"></i>
                <span>Manajemen Lapangan</span>
            </a>

            <div id="collapseJadwal"
                class="collapse <?php echo e(request()->routeIs('admin.futsal.jadwal-lapangan.*') || request()->routeIs('admin.futsal.pengaturan.*') ? 'show' : ''); ?>">

                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="<?php echo e(route('admin.futsal.jadwal-lapangan.index')); ?>">Jadwal Lapangan</a>
                    <a class="collapse-item" href="<?php echo e(route('admin.futsal.pengaturan.index')); ?>">Pengaturan Dasar</a>
                    <a class="collapse-item" href="<?php echo e(route('admin.futsal.pengaturan.jam_operasional.index')); ?>">Jam Operasional</a>
                </div>
            </div>
        </li>

        
        <li class="nav-item <?php echo e(request()->routeIs('admin.futsal.transaksi.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.futsal.transaksi.index')); ?>">
                <i class="fas fa-fw fa-file-invoice-dollar"></i>
                <span>Manajemen Transaksi</span>
            </a>
        </li>

        
        <li class="nav-item <?php echo e(request()->routeIs('admin.futsal.booking.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.futsal.booking.index')); ?>">
                <i class="fas fa-fw fa-calendar-check"></i>
                <span>Manajemen Booking</span>
            </a>
        </li>

        
        <li
            class="nav-item <?php echo e(request()->routeIs('admin.futsal.paket-membership.*') || request()->routeIs('admin.futsal.monitoring-membership.*') ? 'active' : ''); ?>">
            <a class="nav-link <?php echo e(request()->routeIs('admin.futsal.paket-membership.*') || request()->routeIs('admin.futsal.monitoring-membership.*') ? '' : 'collapsed'); ?>"
                href="#" data-toggle="collapse" data-target="#collapseMembership">

                <i class="fas fa-fw fa-id-card"></i>
                <span>Manajemen Paket</span>
            </a>

            <div id="collapseMembership"
                class="collapse <?php echo e(request()->routeIs('admin.futsal.paket-membership.*') || request()->routeIs('admin.futsal.monitoring-membership.*') ? 'show' : ''); ?>">

                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="<?php echo e(route('admin.futsal.paket-membership.index')); ?>">
                        Pilihan Paket
                    </a>
                    <a class="collapse-item" href="<?php echo e(route('admin.futsal.monitoring-paket.index')); ?>">
                        Monitoring Paket
                    </a>
                </div>
            </div>
        </li>

        
        <li class="nav-item <?php echo e(request()->routeIs('admin.futsal.pelanggan.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.futsal.pelanggan.index')); ?>">
                <i class="fas fa-fw fa-users"></i>
                <span>Manajemen Pelanggan Reguler</span>
            </a>
        </li>

        
        <li class="nav-item <?php echo e(request()->routeIs('admin.futsal.keuangan.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.futsal.keuangan.index')); ?>">
                <i class="fas fa-fw fa-wallet"></i>
                <span>Manajemen Keuangan</span>
            </a>
        </li>

        
        <li class="nav-item <?php echo e(request()->routeIs('admin.futsal.laporan.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.futsal.laporan.index')); ?>">
                <i class="fas fa-fw fa-file-pdf"></i>
                <span>Laporan Transaksi</span>
            </a>
        </li>
    <?php endif; ?>

    
    
    
    <?php if(auth()->check() && auth()->user()->hasRole('Adminkantin')): ?>
        <div class="sidebar-heading">Menu Utama</div>

        
        <li class="nav-item <?php echo e(request()->routeIs('admin.kantin.unit.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.kantin.unit.index')); ?>">
                <i class="fas fa-fw fa-store"></i>
                <span>Manajemen Unit</span>
            </a>
        </li>

        
        <li class="nav-item <?php echo e(request()->routeIs('admin.kantin.penyewa.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.kantin.penyewa.index')); ?>">
                <i class="fas fa-fw fa-user-tie"></i>
                <span>Manajemen Penyewa</span>
            </a>
        </li>

        
        <li class="nav-item <?php echo e(request()->routeIs('admin.kantin.penyewaan.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.kantin.penyewaan.index')); ?>">
                <i class="fas fa-fw fa-user-tie"></i>
                <span>Manajemen Penyewaan</span>
            </a>
        </li>

        
        <li class="nav-item <?php echo e(request()->routeIs('admin.kantin.pembayaran.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.kantin.pembayaran.index')); ?>">
                <i class="fas fa-fw fa-file-invoice-dollar"></i>
                <span>Manajemen Pembayaran</span>
            </a>
        </li>

        
        <li class="nav-item <?php echo e(request()->routeIs('admin.kantin.laporan.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.kantin.laporan.index')); ?>">
                <i class="fas fa-fw fa-file-pdf"></i>
                <span>Laporan Transaksi</span>
            </a>
        </li>

        
        <li class="nav-item <?php echo e(request()->routeIs('admin.kantin.keuangan.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.kantin.keuangan.index')); ?>">
                <i class="fas fa-fw fa-wallet"></i>
                <span>Manajemen Keuangan</span>
            </a>
        </li>
    <?php endif; ?>

    
    
    
    <?php if(auth()->check() && auth()->user()->hasRole('Adminac')): ?>
        <div class="sidebar-heading">Menu Utama</div>

        
        <li class="nav-item <?php echo e(request()->routeIs('admin.ac.layanan.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.ac.layanan.index')); ?>">
                <i class="fas fa-fw fa-wrench"></i>
                <span>Manajemen Layanan</span>
            </a>
        </li>

        
        <li class="nav-item <?php echo e(request()->routeIs('admin.ac.produk.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.ac.produk.index')); ?>">
                <i class="fas fa-fw fa-box"></i>
                <span>Manajemen Produk</span>
            </a>
        </li>

        
        <li class="nav-item <?php echo e(request()->routeIs('admin.ac.teknisi.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.ac.teknisi.index')); ?>">
                <i class="fas fa-fw fa-user-cog"></i>
                <span>Manajemen Teknisi</span>
            </a>
        </li>

        
        <li class="nav-item <?php echo e(request()->routeIs('admin.ac.booking.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.ac.booking.index')); ?>">
                <i class="fas fa-fw fa-calendar-check"></i>
                <span>Manajemen Booking</span>
            </a>
        </li>

        
        <li class="nav-item <?php echo e(request()->routeIs('admin.ac.pelanggan.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.ac.pelanggan.index')); ?>">
                <i class="fas fa-fw fa-users"></i>
                <span>Manajemen Pelanggan</span>
            </a>
        </li>

        
        <li class="nav-item <?php echo e(request()->routeIs('admin.ac.keuangan.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.ac.keuangan.index')); ?>">
                <i class="fas fa-fw fa-wallet"></i>
                <span>Manajemen Keuangan</span>
            </a>
        </li>
    <?php endif; ?>

    
    
    
    <?php if(auth()->check() && auth()->user()->hasRole('Adminservis')): ?>
        <div class="sidebar-heading">Menu Utama</div>

         
         <li class="nav-item <?php echo e(request()->routeIs('admin.servis.produk.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.servis.produk.index')); ?>">
                <i class="fas fa-fw fa-tools"></i>
                <span>Manajemen Produk</span>
            </a>
        </li>

        
        <li class="nav-item <?php echo e(request()->routeIs('admin.servis.layanan.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.servis.layanan.index')); ?>">
                <i class="fas fa-fw fa-tools"></i>
                <span>Manajemen Layanan</span>
            </a>
        </li>

        
        <li class="nav-item <?php echo e(request()->routeIs('admin.servis.pelanggan.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.servis.pelanggan.index')); ?>">
                <i class="fas fa-fw fa-users"></i>
                <span>Manajemen Pelanggan</span>
            </a>
        </li>

        
        <li class="nav-item <?php echo e(request()->routeIs('admin.servis.teknisi.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.servis.teknisi.index')); ?>">
                <i class="fas fa-fw fa-user-cog"></i>
                <span>Manajemen Teknisi</span>
            </a>
        </li>

        
        <li class="nav-item <?php echo e(request()->routeIs('admin.servis.booking.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.servis.booking.index')); ?>">
                <i class="fas fa-fw fa-calendar-check"></i>
                <span>Manajemen Booking</span>
            </a>
        </li>

        
        <li class="nav-item <?php echo e(request()->routeIs('admin.servis.transaksi.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.servis.transaksi.index')); ?>">
                <i class="fas fa-fw fa-file-invoice-dollar"></i>
                <span>Monitoring Transaksi</span>
            </a>
        </li>
        
        
        <li class="nav-item <?php echo e(request()->routeIs('admin.servis.keuangan.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.servis.keuangan.index')); ?>">
                <i class="fas fa-fw fa-wallet"></i>
                <span>Manajemen Keuangan</span>
                
        
        <li class="nav-item <?php echo e(request()->routeIs('admin.servis.laporan.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.servis.laporan.index')); ?>">
                <i class="fas fa-fw fa-chart-bar"></i>
                <span>Laporan Transaksi</span>
            </a>
        </li>
    <?php endif; ?>

    
    
    
    <?php if(auth()->check() && auth()->user()->hasRole('Teknisi')): ?>
        <div class="sidebar-heading">Menu Pekerjaan</div>

        
        <li
            class="nav-item <?php echo e(request()->routeIs('teknisi.pekerjaan.*') || request()->routeIs('teknisi.dashboard*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('teknisi.dashboard')); ?>">
                <i class="fas fa-fw fa-tools"></i>
                <span>Detail Servis</span>
            </a>
        </li>

        
        <li class="nav-item <?php echo e(request()->routeIs('admin.ac.transaksi.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.ac.transaksi.index')); ?>">
                <i class="fas fa-fw fa-file-invoice-dollar"></i>
                <span>Manajemen Transaksi</span>
            </a>
        </li>

        
        <li class="nav-item <?php echo e(request()->routeIs('admin.ac.laporan.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('admin.ac.laporan.index')); ?>">
                <i class="fas fa-fw fa-file-pdf"></i>
                <span>Laporan Transaksi</span>
            </a>
        </li>
    <?php endif; ?>

    
    
    
    <?php if(auth()->check() && (auth()->user()->hasRole('Teknisi Motor') || auth()->user()->hasRole('Teknisi Mobil'))): ?>
        <div class="sidebar-heading">Menu Pekerjaan</div>

        
        <li class="nav-item <?php echo e(request()->routeIs('teknisi.servis.dashboard*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('teknisi.servis.dashboard')); ?>">
                <i class="fas fa-fw fa-tools"></i>
                <span>Pekerjaan Aktif</span>
            </a>
        </li>
    <?php endif; ?>



    
    
    
    <?php if(auth()->check() && auth()->user()->hasRole('Kasir')): ?>

        <div class="sidebar-heading">Dashboard</div>

        <li class="nav-item <?php echo e(request()->routeIs('kasir.dashboard') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('kasir.dashboard')); ?>">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>

        
        <div class="sidebar-heading">Menu Pekerjaan</div>

        <li
            class="nav-item <?php echo e(request()->routeIs('kasir.booking.*') || request()->routeIs('kasir.dashboard*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('kasir.booking.index')); ?>">
                <i class="fas fa-fw fa-calendar-check"></i>
                <span>Manajemen Booking</span>
            </a>
        </li>

        <li class="nav-item <?php echo e(request()->routeIs('kasir.pembayaran.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('kasir.pembayaran.index')); ?>">
                <i class="fas fa-fw fa-cash-register"></i>
                <span>Menu Pembayaran</span>
            </a>
        </li>

        <li class="nav-item <?php echo e(request()->routeIs('kasir.laporan.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('kasir.laporan.index')); ?>">
                <i class="fas fa-fw fa-file-pdf"></i>
                <span>Laporan Transaksi</span>
            </a>
        </li>
    <?php elseif(auth()->check() && auth()->user()->hasRole('kasirfutsal')): ?>

        
        <div class="sidebar-heading">Menu Kasir Futsal</div>

        <li class="nav-item <?php echo e(request()->routeIs('kasirfutsal.dashboard') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('kasirfutsal.dashboard')); ?>">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="nav-item <?php echo e(request()->routeIs('kasirfutsal.booking.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('kasirfutsal.booking.index')); ?>">
                <i class="fas fa-fw fa-calendar-check"></i>
                <span>Booking</span>
            </a>
        </li>

        <li class="nav-item <?php echo e(request()->routeIs('kasirfutsal.pembayaran.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('kasirfutsal.pembayaran.index')); ?>">
                <i class="fas fa-fw fa-money-bill-wave"></i>
                <span>Pembayaran</span>
            </a>
        </li>

        <li class="nav-item <?php echo e(request()->routeIs('kasirfutsal.laporan.*') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('kasirfutsal.laporan.index')); ?>">
                <i class="fas fa-fw fa-file-alt"></i>
                <span>Laporan Harian</span>
            </a>
        </li>
    <?php endif; ?>

    
    
    
    
    <?php if(!auth()->check() || (auth()->check() && auth()->user()->hasRole('Pelanggan'))): ?>
        <div class="sidebar-heading">Menu Akun</div>


        
        <li class="nav-item <?php echo e(request()->routeIs('user.gateway') ? 'active' : ''); ?>">
            <a class="nav-link" href="<?php echo e(route('user.gateway')); ?>">
                <i class="fas fa-fw fa-home"></i>
                <span>Dashboard Utama</span>
            </a>
        </li>

        <hr class="sidebar-divider">
        <div class="sidebar-heading">Layanan Futsal</div>

        
        <li class="nav-item <?php echo e($futsalActive ? 'active' : ''); ?>">
            <a class="nav-link <?php echo e($futsalActive ? '' : 'collapsed'); ?>" href="#" data-toggle="collapse"
                data-target="#collapseFutsal" aria-expanded="<?php echo e($futsalActive ? 'true' : 'false'); ?>">
                <i class="fas fa-fw fa-futbol"></i>
                <span>Sistem Futsal</span>
            </a>
            <div id="collapseFutsal" class="collapse <?php echo e($futsalActive ? 'show' : ''); ?>">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item <?php echo e(request()->routeIs('user.gateway') ? 'active' : ''); ?>"
                        href="<?php echo e(route('user.gateway')); ?>">
                        <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                    </a>
                    <a class="collapse-item <?php echo e(request()->routeIs('user.futsal.history') ? 'active' : ''); ?>"
                        href="#" >
                        <i class="fas fa-history mr-1"></i> Histori Booking
                    </a>
                </div>
            </div>
        </li>

        <hr class="sidebar-divider">
        <div class="sidebar-heading">Layanan Sewa Kantin</div>

        
        <li class="nav-item <?php echo e($kantinActive ? 'active' : ''); ?>">
            <a class="nav-link <?php echo e($kantinActive ? '' : 'collapsed'); ?>" href="#" data-toggle="collapse"
                data-target="#collapseKantin" aria-expanded="<?php echo e($kantinActive ? 'true' : 'false'); ?>">
                <i class="fas fa-fw fa-store"></i>
                <span>Sistem Sewa Kantin</span>
            </a>
            <div id="collapseKantin" class="collapse <?php echo e($kantinActive ? 'show' : ''); ?>">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item <?php echo e(request()->routeIs('user.gateway') ? 'active' : ''); ?>"
                        href="<?php echo e(route('user.gateway')); ?>">
                        <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                    </a>
                    <a class="collapse-item <?php echo e(request()->routeIs('user.kantin.tagihan') ? 'active' : ''); ?>"
                        href="<?php echo e(route('user.kantin.tagihan')); ?>">
                        <i class="fas fa-file-invoice mr-2"></i> Tagihan
                    </a>
                    <a class="collapse-item <?php echo e(request()->routeIs('user.kantin.riwayat') ? 'active' : ''); ?>"
                        href="#" >
                        <i class="fas fa-history mr-1"></i> Riwayat Pembayaran
                    </a>
                </div>
            </div>
        </li>

        <hr class="sidebar-divider">
        <div class="sidebar-heading">Layanan Servis AC</div>

        
        <li class="nav-item <?php echo e($acActive ? 'active' : ''); ?>">
            <a class="nav-link <?php echo e($acActive ? '' : 'collapsed'); ?>" href="#" data-toggle="collapse"
                data-target="#collapseAC" aria-expanded="<?php echo e($acActive ? 'true' : 'false'); ?>">
                <i class="fas fa-fw fa-tools"></i>
                <span>Sistem Servis AC</span>
            </a>
            <div id="collapseAC" class="collapse <?php echo e($acActive ? 'show' : ''); ?>" aria-labelledby="headingAC">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item <?php echo e(request()->routeIs('user.ac.index') ? 'active' : ''); ?>" href="<?php echo e(route('user.gateway')); ?>">
                        <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                    </a>
                    <a class="collapse-item <?php echo e(request()->routeIs('user.ac.history') ? 'active' : ''); ?>" href="#" >
                        <i class="fas fa-history mr-1"></i> Histori Booking
                    </a>
                </div>
            </div>
        </li>

        <hr class="sidebar-divider">
        <div class="sidebar-heading">Layanan Servis Motor & Mobil</div>

        
        <?php $servisActive = request()->is('user/servis*'); ?>
        <li class="nav-item <?php echo e($servisActive ? 'active' : ''); ?>">
            <a class="nav-link <?php echo e($servisActive ? '' : 'collapsed'); ?>" href="#" data-toggle="collapse"
                data-target="#collapseServis" aria-expanded="<?php echo e($servisActive ? 'true' : 'false'); ?>">
                <i class="fas fa-fw fa-car"></i>
                <span>Servis Motor & Mobil</span>
            </a>
            <div id="collapseServis" class="collapse <?php echo e($servisActive ? 'show' : ''); ?>">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item <?php echo e(request()->routeIs('user.servis.katalog') ? 'active' : ''); ?>" href="<?php echo e(route('user.gateway')); ?>">
                        <i class="fas fa-tachometer-alt mr-1"></i> Dashboard Servis
                    </a>
                    <a class="collapse-item <?php echo e(request()->routeIs('user.servis.history') ? 'active' : ''); ?>" href="#" >
                        <i class="fas fa-history mr-1"></i> Histori Servis
                    </a>
                </div>
            </div>
        </li>
    <?php endif; ?>

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

<?php /**PATH C:\laragon\www\blud_system\resources\views/partials/sidebar.blade.php ENDPATH**/ ?>