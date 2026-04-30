<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
</div>


<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pelanggan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($totalPelanggan); ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Transaksi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($totalTransaksi); ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-clipboard-list fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Pendapatan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            Rp <?php echo e(number_format($totalPendapatan, 0, ',', '.')); ?>

                        </div>
                    </div>
                    <div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Booking Menunggu</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($totalBookingPending); ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-clock fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Progres Pendapatan</h6>
            </div>
            <div class="card-body">
                <h4 class="small font-weight-bold">
                    Sistem Cuci AC
                    <span class="float-right">Rp <?php echo e(number_format($pendapatanAC, 0, ',', '.')); ?></span>
                </h4>
                <div class="progress mb-4">
                    <div class="progress-bar bg-primary" role="progressbar"
                         style="width: <?php echo e($persenAC); ?>%" aria-valuenow="<?php echo e($persenAC); ?>"
                         aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <h4 class="small font-weight-bold">
                    Booking Lapangan Futsal
                    <span class="float-right">Rp <?php echo e(number_format($pendapatanFutsal, 0, ',', '.')); ?></span>
                </h4>
                <div class="progress mb-4">
                    <div class="progress-bar bg-success" role="progressbar"
                         style="width: <?php echo e($persenFutsal); ?>%" aria-valuenow="<?php echo e($persenFutsal); ?>"
                         aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <h4 class="small font-weight-bold">
                    Sewa Ruko Kantin
                    <span class="float-right">Rp <?php echo e(number_format($pendapatanRuko, 0, ',', '.')); ?></span>
                </h4>
                <div class="progress">
                    <div class="progress-bar bg-warning" role="progressbar"
                         style="width: <?php echo e($persenRuko); ?>%" aria-valuenow="<?php echo e($persenRuko); ?>"
                         aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Notifikasi Terbaru</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php $__empty_1 = true; $__currentLoopData = $aktivitas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <i class="fas fa-bell text-primary mr-2"></i>
                                <strong><?php echo e($a->nama); ?></strong>
                                <span class="text-muted"><?php echo e($a->aktivitas); ?></span>
                            </div>
                            <small class="text-gray-500 ml-2 text-nowrap">
                                <?php echo e(\Carbon\Carbon::parse($a->created_at)->diffForHumans()); ?>

                            </small>
                        </div>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li class="list-group-item text-center text-muted">Tidak ada aktivitas terbaru.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Transaksi Terbaru</h6>
                <a href="<?php echo e(route('transaksi.index')); ?>" class="btn btn-sm btn-outline-primary">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Layanan</th>
                                <th>Nama Pelanggan</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                                <th class="d-none d-md-table-cell">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $transaksiTerbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($t->layanan); ?></td>
                                <td><?php echo e($t->nama_user); ?></td>
                                <td>Rp <?php echo e(number_format($t->jumlah_bayar, 0, ',', '.')); ?></td>
                                <td><span class="badge badge-info"><?php echo e($t->status); ?></span></td>
                                <td class="d-none d-md-table-cell"><?php echo e($t->tgl_bayar ?? '-'); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada transaksi.</td>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/dashboard/index.blade.php ENDPATH**/ ?>