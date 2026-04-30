<?php $__env->startSection('title', 'Detail Pelanggan: ' . $pelanggan->name); ?>

<?php $__env->startSection('content'); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-user-tag mr-2 text-primary"></i>Detail Pelanggan: <strong><?php echo e($pelanggan->name); ?></strong>
    </h1>
    <a href="<?php echo e(route('adminac.pelanggan.index')); ?>" class="btn btn-secondary btn-sm shadow-sm hover-shadow">
        <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali ke Daftar
    </a>
</div>

<div class="row">
    
    <div class="col-xl-4 col-lg-5 mb-4">
        <div class="card shadow border-left-primary h-100">
            <div class="card-header py-3 bg-white d-flex align-items-center">
                <i class="fas fa-info-circle text-primary mr-2"></i>
                <h6 class="m-0 font-weight-bold text-primary">Profil Pelanggan</h6>
            </div>
            <div class="card-body text-center pt-4">
                <div class="position-relative d-inline-block mb-3">
                    <i class="fas fa-user-circle fa-6x text-gray-200"></i>
                    <div class="position-absolute" style="bottom: 5px; right: 5px;">
                        <span class="badge badge-success badge-pill border border-white p-2" title="Pelanggan Aktif">
                            <i class="fas fa-check fa-xs"></i>
                        </span>
                    </div>
                </div>
                <h5 class="font-weight-bold text-gray-900 mb-0"><?php echo e($pelanggan->name); ?></h5>
                <p class="text-muted text-xs mb-4">ID Pelanggan: #CUST-<?php echo e(str_pad($pelanggan->id, 5, '0', STR_PAD_LEFT)); ?></p>

                <div class="text-left px-3">
                    <div class="mb-3 pb-2 border-bottom border-light">
                        <label class="text-xs font-weight-bold text-primary text-uppercase mb-1 d-block">Username</label>
                        <div class="h6 font-weight-bold text-gray-800"><?php echo e($pelanggan->username ?? '-'); ?></div>
                    </div>
                    <div class="mb-3 pb-2 border-bottom border-light">
                        <label class="text-xs font-weight-bold text-primary text-uppercase mb-1 d-block">Email</label>
                        <div class="h6 font-weight-bold text-gray-800"><?php echo e($pelanggan->email); ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="text-xs font-weight-bold text-primary text-uppercase mb-1 d-block">No. HP</label>
                        <div class="h6 font-weight-bold text-gray-800">
                            <?php if($pelanggan->no_hp): ?>
                                <a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', $pelanggan->no_hp)); ?>" target="_blank" class="text-decoration-none">
                                    <?php echo e($pelanggan->no_hp); ?> <i class="fab fa-whatsapp ml-1 text-success"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-xl-8 col-lg-7 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-history text-primary mr-2"></i>
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Booking AC</h6>
                </div>
                <span class="badge badge-primary badge-pill px-3"><?php echo e($pelanggan->bookingAc->count()); ?> Total Booking</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" width="100%" cellspacing="0">
                        <thead class="bg-light">
                            <tr>
                                <th class="pl-4 border-top-0" width="20%">Tanggal</th>
                                <th class="border-top-0">Jenis Jasa & AC</th>
                                <th class="text-center border-top-0" width="20%">Status</th>
                                <th class="pr-4 text-center border-top-0" width="15%">Booking ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $pelanggan->bookingAc; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $statusClass = 'secondary';
                                    $statusIcon = 'clock';
                                    $status = strtolower($booking->status);

                                    if($status == 'selesai') {
                                        $statusClass = 'success';
                                        $statusIcon = 'check-circle';
                                    } elseif($status == 'proses') {
                                        $statusClass = 'info';
                                        $statusIcon = 'sync';
                                    } elseif($status == 'pending') {
                                        $statusClass = 'warning';
                                        $statusIcon = 'exclamation-circle';
                                    } elseif($status == 'batal' || $status == 'cancel') {
                                        $statusClass = 'danger';
                                        $statusIcon = 'times-circle';
                                    }
                                ?>
                                <tr>
                                    <td class="pl-4 align-middle">
                                        <div class="font-weight-bold text-gray-800">
                                            <?php echo e(\Carbon\Carbon::parse($booking->tgl_kunjungan)->translatedFormat('d M Y')); ?>

                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="font-weight-bold text-gray-900"><?php echo e($booking->layanan->nama ?? '-'); ?></div>
                                        <small class="text-muted"><i class="fas fa-info-circle mr-1 fa-xs"></i><?php echo e($booking->merek_ac ?? '-'); ?></small>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="badge badge-pill badge-<?php echo e($statusClass); ?> px-3 py-1 shadow-sm">
                                            <i class="fas fa-<?php echo e($statusIcon); ?> fa-xs mr-1"></i><?php echo e(ucfirst($status)); ?>

                                        </span>
                                    </td>
                                    <td class="pr-4 text-center align-middle">
                                        <span class="text-xs font-weight-bold py-1 px-2 bg-gray-100 rounded text-gray-600">
                                            #<?php echo e($booking->booking_id ?? $booking->id); ?>

                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-clipboard-list fa-3x mb-3 opacity-25"></i>
                                            <p class="mb-0">Belum ada riwayat pemesanan untuk pelanggan ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .hover-shadow:hover {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.155) !important;
        transform: translateY(-1px);
        transition: all 0.2s ease-in-out;
    }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/adminac/pelanggan/show.blade.php ENDPATH**/ ?>