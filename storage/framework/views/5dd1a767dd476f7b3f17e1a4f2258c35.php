<?php $__env->startSection('title', 'Sistem Booking Futsal'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    /* ── Hero Banner ── */
    .hero-futsal {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 60%, #1a3a9c 100%);
        border-radius: 16px;
        padding: 48px 40px;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 32px;
        box-shadow: 0 8px 32px rgba(78,115,223,0.30);
    }
    .hero-futsal::before {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 280px; height: 280px;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
    }
    .hero-futsal::after {
        content: '';
        position: absolute;
        bottom: -80px; left: 40%;
        width: 200px; height: 200px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
    }
    .hero-futsal .hero-icon {
        font-size: 5rem;
        opacity: 0.18;
        position: absolute;
        right: 48px; top: 50%;
        transform: translateY(-50%);
    }
    .hero-futsal h1 {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 10px;
        letter-spacing: -0.5px;
    }
    .hero-futsal p.lead {
        font-size: 1rem;
        opacity: 0.88;
        max-width: 520px;
    }
    .hero-btn-group .btn {
        font-weight: 700;
        padding: 10px 28px;
        border-radius: 8px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .hero-btn-group .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.18);
    }

    /* ── Membership Banner ── */
    .membership-active-banner {
        background: linear-gradient(90deg, #1cc88a 0%, #17a673 100%);
        border-radius: 12px;
        padding: 20px 28px;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 20px;
        box-shadow: 0 4px 14px rgba(28,200,138,0.25);
        margin-bottom: 28px;
    }
    .membership-active-banner .quota-ring {
        width: 70px; height: 70px;
        border-radius: 50%;
        background: rgba(255,255,255,0.15);
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        flex-shrink: 0;
        border: 3px solid rgba(255,255,255,0.4);
    }
    .membership-active-banner .quota-ring span.num {
        font-size: 1.5rem;
        font-weight: 900;
        line-height: 1;
    }
    .membership-active-banner .quota-ring span.lbl {
        font-size: 0.6rem;
        opacity: 0.85;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .membership-no-banner {
        background: #fff;
        border-left: 5px solid #f6c23e;
        border-radius: 10px;
        padding: 18px 24px;
        margin-bottom: 28px;
        display: flex;
        align-items: flex-start;
        gap: 16px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    }
    .membership-no-banner .adv-list li {
        font-size: 0.88rem;
        margin-bottom: 4px;
        color: #5a5c69;
    }

    /* ── Section Title ── */
    .section-title {
        font-size: 1.2rem;
        font-weight: 800;
        color: #4e73df;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .section-title::after {
        content: '';
        flex: 1;
        height: 2px;
        background: linear-gradient(90deg, #4e73df22, transparent);
        border-radius: 2px;
    }
    .section-subtitle {
        color: #858796;
        font-size: 0.88rem;
        margin-bottom: 20px;
    }

    /* ── Lapangan Card ── */
    .lapangan-card {
        border: none;
        border-radius: 14px;
        overflow: hidden;
        transition: transform 0.25s, box-shadow 0.25s;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        height: 100%;
    }
    .lapangan-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 28px rgba(78,115,223,0.18);
    }
    .lapangan-card .card-img-top {
        height: 180px;
        object-fit: cover;
        background: #e9eaf3;
    }
    .lapangan-card .img-placeholder {
        height: 180px;
        background: linear-gradient(135deg, #4e73df22, #4e73df11);
        display: flex; align-items: center; justify-content: center;
    }
    .lapangan-card .img-placeholder i {
        font-size: 3.5rem;
        color: #4e73df55;
    }
    .lapangan-card .card-title {
        font-weight: 800;
        font-size: 1rem;
        color: #2d3a6e;
    }
    .lapangan-card .card-text {
        font-size: 0.82rem;
        color: #858796;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .lapangan-card .spek-badge {
        background: #eef0ff;
        color: #4e73df;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .lapangan-card .btn-booking {
        font-weight: 700;
        font-size: 0.85rem;
        border-radius: 8px;
        transition: all 0.2s;
    }

    /* ── Paket Card ── */
    .paket-card {
        border: 2px solid #e3e6f0;
        border-radius: 14px;
        padding: 28px 22px;
        text-align: center;
        transition: all 0.25s;
        background: #fff;
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    .paket-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: linear-gradient(90deg, #4e73df, #36b9cc);
    }
    .paket-card:hover {
        border-color: #4e73df;
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(78,115,223,0.16);
    }
    .paket-card .paket-icon {
        width: 60px; height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4e73df, #224abe);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 16px;
        box-shadow: 0 4px 12px rgba(78,115,223,0.35);
    }
    .paket-card .paket-icon i {
        font-size: 1.5rem;
        color: #fff;
    }
    .paket-card .paket-nama {
        font-weight: 800;
        font-size: 1.05rem;
        color: #2d3a6e;
        margin-bottom: 8px;
    }
    .paket-card .paket-kuota {
        font-size: 2.2rem;
        font-weight: 900;
        color: #4e73df;
        line-height: 1;
        margin-bottom: 2px;
    }
    .paket-card .paket-kuota-lbl {
        font-size: 0.8rem;
        color: #858796;
        margin-bottom: 14px;
    }
    .paket-card .paket-harga {
        font-size: 1.15rem;
        font-weight: 800;
        color: #1cc88a;
        margin-bottom: 18px;
    }
    .paket-card .paket-divider {
        height: 1px;
        background: #e3e6f0;
        margin: 14px 0;
    }

    /* ── Stats Bar ── */
    .stats-bar {
        background: #fff;
        border-radius: 12px;
        padding: 18px 24px;
        display: flex;
        gap: 0;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        margin-bottom: 28px;
        overflow: hidden;
    }
    .stats-bar .stat-item {
        flex: 1;
        text-align: center;
        padding: 8px 12px;
        border-right: 1px solid #e3e6f0;
    }
    .stats-bar .stat-item:last-child { border-right: none; }
    .stats-bar .stat-num {
        font-size: 1.6rem;
        font-weight: 900;
        color: #4e73df;
    }
    .stats-bar .stat-lbl {
        font-size: 0.78rem;
        color: #858796;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* ── Scrolling Carousel ── */
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(16px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-in {
        animation: slideIn 0.4s ease forwards;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>




<div class="hero-futsal animate-in">
    <i class="fas fa-futbol hero-icon d-none d-md-block"></i>
    <div style="position:relative; z-index:1">
        <p class="text-uppercase mb-2" style="font-size:0.75rem; letter-spacing:2px; opacity:0.75;">
            <i class="fas fa-map-marker-alt mr-1"></i> Pusat Olahraga Blud
        </p>
        <h1>Sistem Booking Futsal</h1>
        <p class="lead mb-4">
            Pesan lapangan futsal kapan saja, lihat jadwal tersedia secara real-time,
            dan nikmati kemudahan bermain dengan atau tanpa membership.
        </p>
        <div class="hero-btn-group d-flex flex-wrap gap-2" style="gap: 12px;">
            <a href="<?php echo e(route('user.futsal.booking.form')); ?>"
               class="btn btn-light text-primary font-weight-bold shadow-sm">
                <i class="fas fa-calendar-plus mr-2"></i> Booking Sekarang
            </a>
            <a href="<?php echo e(route('user.futsal.history')); ?>"
               class="btn btn-outline-light font-weight-bold">
                <i class="fas fa-history mr-2"></i> Lihat Histori
            </a>
            <a href="<?php echo e(route('user.futsal.dashboard')); ?>"
               class="btn btn-outline-light font-weight-bold">
                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard Futsal
            </a>
        </div>
    </div>
</div>




<div class="stats-bar">
    <div class="stat-item">
        <div class="stat-num"><?php echo e($lapangans->count()); ?></div>
        <div class="stat-lbl">Lapangan Tersedia</div>
    </div>
    <div class="stat-item">
        <div class="stat-num"><?php echo e($paketMemberships->count()); ?></div>
        <div class="stat-lbl">Paket Membership</div>
    </div>
    <div class="stat-item">
        <div class="stat-num">3</div>
        <div class="stat-lbl">Maks. Durasi (Jam)</div>
    </div>
    <div class="stat-item d-none d-md-block">
        <div class="stat-num" style="color:#1cc88a;">24/7</div>
        <div class="stat-lbl">Booking Online</div>
    </div>
</div>




<?php if($membershipAktif): ?>
<div class="membership-active-banner">
    <div class="quota-ring">
        <span class="num"><?php echo e($membershipAktif->sisa_kuota); ?></span>
        <span class="lbl">Kuota</span>
    </div>
    <div style="flex:1">
        <div class="font-weight-bold" style="font-size:1rem;">
            <i class="fas fa-check-circle mr-1" style="opacity:0.85;"></i>
            Membership Aktif — <?php echo e($membershipAktif->paket->nama_paket ?? 'Paket Membership'); ?>

        </div>
        <div style="opacity:0.88; font-size:0.88rem;" class="mt-1">
            Sisa kuota: <strong><?php echo e($membershipAktif->sisa_kuota); ?></strong> jam
            dari <strong><?php echo e($membershipAktif->total_kuota); ?></strong> jam total.
            Gunakan kuota Anda saat booking untuk kemudahan pembayaran.
        </div>
    </div>
    <a href="<?php echo e(route('user.futsal.booking.form')); ?>"
       class="btn btn-light text-success font-weight-bold btn-sm flex-shrink-0" style="white-space:nowrap;">
        <i class="fas fa-bolt mr-1"></i> Booking Pakai Membership
    </a>
</div>
<?php else: ?>
<div class="membership-no-banner">
    <div style="flex-shrink:0; margin-top:2px;">
        <i class="fas fa-star fa-2x" style="color:#f6c23e;"></i>
    </div>
    <div style="flex:1">
        <div class="font-weight-bold text-gray-800 mb-1">Belum Punya Membership?</div>
        <p class="mb-1 text-muted" style="font-size:0.88rem;">
            Dapatkan keuntungan bermain futsal dengan program membership kami:
        </p>
        <ul class="adv-list list-unstyled mb-0">
            <li><i class="fas fa-check text-success mr-1"></i> Bayar sekali, booking berkali-kali sesuai kuota</li>
            <li><i class="fas fa-check text-success mr-1"></i> Tidak perlu bayar tiap sesi booking</li>
            <li><i class="fas fa-check text-success mr-1"></i> Pilihan paket sesuai kebutuhan</li>
        </ul>
    </div>
    <div class="flex-shrink-0 text-center d-none d-md-block ml-3">
        <small class="text-muted d-block mb-1">Lihat paket di bawah ↓</small>
    </div>
</div>
<?php endif; ?>




<div class="mb-2 d-flex align-items-center">
    <div>
        <div class="section-title">
            <i class="fas fa-map-marker-alt text-primary"></i>
            Daftar Lapangan
        </div>
        <p class="section-subtitle mb-0">Pilih lapangan yang sesuai dengan kebutuhanmu</p>
    </div>
</div>

<?php if($lapangans->isEmpty()): ?>
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body text-center py-5">
        <i class="fas fa-futbol fa-3x text-gray-300 mb-3"></i>
        <p class="text-muted mb-0">Belum ada lapangan yang tersedia saat ini.</p>
    </div>
</div>
<?php else: ?>
<div class="row mb-4">
    <?php $__currentLoopData = $lapangans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lap): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card lapangan-card shadow-sm">
            
            <?php if($lap->foto): ?>
                <img src="<?php echo e(asset('storage/' . $lap->foto)); ?>"
                     class="card-img-top"
                     alt="<?php echo e($lap->nama); ?>"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="img-placeholder" style="display:none;">
                    <i class="fas fa-futbol"></i>
                </div>
            <?php else: ?>
                <div class="img-placeholder">
                    <i class="fas fa-futbol"></i>
                </div>
            <?php endif; ?>

            <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <h6 class="card-title mb-0"><?php echo e($lap->nama); ?></h6>
                    <?php if($lap->ukuran ?? $lap->spesifikasi ?? null): ?>
                    <span class="spek-badge ml-2 flex-shrink-0">
                        <i class="fas fa-ruler-combined"></i>
                        <?php echo e($lap->ukuran ?? $lap->spesifikasi); ?>

                    </span>
                    <?php endif; ?>
                </div>

                <?php if($lap->deskripsi): ?>
                <p class="card-text flex-grow-1"><?php echo e($lap->deskripsi); ?></p>
                <?php else: ?>
                <p class="card-text flex-grow-1 text-muted fst-italic">Lapangan futsal standar.</p>
                <?php endif; ?>

                <div class="mt-auto pt-3 d-flex gap-2" style="gap:8px;">
                    <a href="<?php echo e(route('user.futsal.booking.form')); ?>?lapangan_id=<?php echo e($lap->id); ?>"
                       class="btn btn-primary btn-sm btn-booking flex-grow-1">
                        <i class="fas fa-calendar-plus mr-1"></i> Booking Lapangan Ini
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>




<div class="mb-2">
    <div class="section-title">
        <i class="fas fa-id-card text-primary"></i>
        Paket Membership
    </div>
    <p class="section-subtitle mb-0">Bergabung dan nikmati kemudahan booking dengan kuota fleksibel</p>
</div>

<?php if($paketMemberships->isEmpty()): ?>
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body text-center py-5">
        <i class="fas fa-id-card fa-3x text-gray-300 mb-3"></i>
        <p class="text-muted mb-0">Belum ada paket membership yang tersedia saat ini.</p>
    </div>
</div>
<?php else: ?>
<div class="row mb-4">
    <?php $__currentLoopData = $paketMemberships; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $paket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="paket-card shadow-sm">
            
            <div class="paket-icon">
                <?php
                    $icons = ['fa-star', 'fa-crown', 'fa-gem', 'fa-trophy', 'fa-bolt'];
                    $icon = $icons[$idx % count($icons)];
                ?>
                <i class="fas <?php echo e($icon); ?>"></i>
            </div>

            <div class="paket-nama"><?php echo e($paket->nama_paket); ?></div>

            
            <div class="paket-kuota"><?php echo e($paket->jumlah_kuota); ?></div>
            <div class="paket-kuota-lbl">Jam Kuota Main</div>

            <div class="paket-divider"></div>

            
            <div class="paket-harga">
                Rp <?php echo e(number_format($paket->harga, 0, ',', '.')); ?>

            </div>

            
            <?php if($membershipAktif && $membershipAktif->paket_membership_id == $paket->id): ?>
            <div class="alert alert-success py-2 px-3 mb-0" style="font-size:0.82rem; border-radius:8px;">
                <i class="fas fa-check-circle mr-1"></i> Paket Anda Saat Ini
            </div>
            <?php else: ?>
                <?php if($membershipAktif): ?>
                    <div class="text-muted" style="font-size:0.8rem;">
                        <i class="fas fa-info-circle mr-1"></i>
                        Selesaikan membership aktif Anda terlebih dahulu
                    </div>
                <?php else: ?>
                    <a href="<?php echo e(route('user.futsal.membership.form')); ?>" 
                    class="btn btn-primary btn-sm btn-block font-weight-bold" 
                    style="border-radius:8px;">
                        <i class="fas fa-shopping-cart mr-1"></i> Beli Membership
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>




<div class="card shadow-sm border-0 mb-4" style="border-radius:14px; overflow:hidden;">
    <div class="card-header py-3" style="background: linear-gradient(90deg, #4e73df, #224abe); border:none;">
        <h6 class="m-0 font-weight-bold text-white">
            <i class="fas fa-lightbulb mr-2"></i> Cara Booking Lapangan
        </h6>
    </div>
    <div class="card-body">
        <div class="row text-center">
            <?php
            $steps = [
                ['icon' => 'fa-mouse-pointer', 'color' => '#4e73df', 'num' => '1', 'title' => 'Pilih Lapangan', 'desc' => 'Pilih lapangan yang ingin Anda gunakan dari daftar di atas'],
                ['icon' => 'fa-calendar-alt', 'color' => '#1cc88a', 'num' => '2', 'title' => 'Pilih Tanggal & Jam', 'desc' => 'Tentukan tanggal main dan pilih slot jam yang masih tersedia'],
                ['icon' => 'fa-hourglass-half', 'color' => '#f6c23e', 'num' => '3', 'title' => 'Tentukan Durasi', 'desc' => 'Pilih durasi bermain 1 hingga 3 jam sesuai kebutuhan'],
                ['icon' => 'fa-credit-card', 'color' => '#36b9cc', 'num' => '4', 'title' => 'Pilih Pembayaran', 'desc' => 'Bayar reguler (tunai/transfer) atau gunakan kuota membership'],
                ['icon' => 'fa-check-circle', 'color' => '#e74a3b', 'num' => '5', 'title' => 'Konfirmasi', 'desc' => 'Submit booking, tunggu konfirmasi dari admin, dan bermain!'],
            ];
            ?>
            <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-6 col-md-4 col-lg mb-3 mb-lg-0">
                <div style="width:52px; height:52px; border-radius:50%; background:<?php echo e($step['color']); ?>18; border:2px solid <?php echo e($step['color']); ?>; display:flex; align-items:center; justify-content:center; margin:0 auto 12px; position:relative;">
                    <i class="fas <?php echo e($step['icon']); ?>" style="color:<?php echo e($step['color']); ?>; font-size:1.1rem;"></i>
                    <span style="position:absolute; top:-6px; right:-6px; width:18px; height:18px; background:<?php echo e($step['color']); ?>; color:#fff; border-radius:50%; font-size:0.65rem; font-weight:900; display:flex; align-items:center; justify-content:center;"><?php echo e($step['num']); ?></span>
                </div>
                <div class="font-weight-bold text-gray-800 mb-1" style="font-size:0.9rem;"><?php echo e($step['title']); ?></div>
                <div class="text-muted" style="font-size:0.78rem; line-height:1.4;"><?php echo e($step['desc']); ?></div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <div class="card-footer text-center border-0 py-3" style="background:#f8f9fc;">
        <a href="<?php echo e(route('user.futsal.booking.form')); ?>" class="btn btn-primary font-weight-bold px-4">
            <i class="fas fa-calendar-plus mr-2"></i> Mulai Booking Sekarang
        </a>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/user/futsal/landing.blade.php ENDPATH**/ ?>