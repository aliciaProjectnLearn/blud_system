<?php $__env->startSection('title', 'Dashboard Teknisi AC'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard Teknisi AC</h1>
</div>



<div class="row">
    <!-- Jumlah Pekerjaan Selesai Bulan Ini -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Pekerjaan Selesai (Bulan Ini)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($totalSelesaiBulanIni ?? 0); ?> Tugas</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Tugas Saat Ini -->
    <div class="col-lg-12 mb-4">
        <div class="card shadow mb-4 border-left-primary">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Tugas Saat Ini (Belum Selesai)</h6>
            </div>
            <div class="card-body p-3 p-md-4">
                <!-- Desktop Table View -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Pelanggan</th>
                                <th>Layanan</th>
                                <th>Tanggal Kunjungan</th>
                                <th>Alamat</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $pekerjaanAktif; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pekerjaan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($loop->iteration); ?></td>
                                <td><?php echo e($pekerjaan->user?->nama_lengkap ?? $pekerjaan->user?->name ?? $pekerjaan->nama_pelanggan ?? 'Pelanggan Guest'); ?></td>
                                <td><?php echo e($pekerjaan->layanan->nama ?? '-'); ?></td>
                                <td><?php echo e(\Carbon\Carbon::parse($pekerjaan->tgl_kunjungan)->format('d M Y H:i')); ?></td>
                                <td><?php echo e($pekerjaan->alamat); ?></td>
                                <td>
                                    <?php if($pekerjaan->status == 'menunggu'): ?>
                                        <span class="badge badge-info">Menunggu</span>
                                    <?php elseif($pekerjaan->status == 'proses'): ?>
                                        <span class="badge badge-warning">Dalam Proses</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary"><?php echo e(ucfirst($pekerjaan->status)); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo e(route('teknisi.pekerjaan.show', $pekerjaan->id)); ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> Detail & Kerjakan
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">Belum ada tugas baru untuk Anda saat ini.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="d-block d-md-none">
                    <?php $__empty_1 = true; $__currentLoopData = $pekerjaanAktif; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pekerjaan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="card shadow-sm mb-3 border-left-primary">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="font-weight-bold text-dark"><?php echo e($pekerjaan->user?->nama_lengkap ?? $pekerjaan->user?->name ?? $pekerjaan->nama_pelanggan ?? 'Pelanggan Guest'); ?></span>
                                <?php if($pekerjaan->status == 'menunggu'): ?>
                                    <span class="badge badge-info">Menunggu</span>
                                <?php elseif($pekerjaan->status == 'proses'): ?>
                                    <span class="badge badge-warning">Dalam Proses</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary"><?php echo e(ucfirst($pekerjaan->status)); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <p class="small text-muted mb-1"><i class="fas fa-tools fa-fw mr-1"></i> <?php echo e($pekerjaan->layanan->nama ?? '-'); ?></p>
                                <p class="small text-muted mb-1"><i class="far fa-calendar-alt fa-fw mr-1"></i> <?php echo e(\Carbon\Carbon::parse($pekerjaan->tgl_kunjungan)->format('d M Y H:i')); ?></p>
                                <p class="small text-muted mb-0"><i class="fas fa-map-marker-alt fa-fw mr-1"></i> <?php echo e($pekerjaan->alamat); ?></p>
                            </div>
                            <a href="<?php echo e(route('teknisi.pekerjaan.show', $pekerjaan->id)); ?>" class="btn btn-primary btn-sm btn-block">
                                <i class="fas fa-eye"></i> Detail & Kerjakan
                            </a>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center text-muted font-italic py-3 border rounded bg-light">Belum ada tugas baru untuk Anda saat ini.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Histori Pekerjaan -->
    <div class="col-lg-12">
        <div class="card shadow mb-4 border-left-success">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">Histori Pekerjaan Selesai</h6>
            </div>
            <div class="card-body p-3 p-md-4">
                <!-- Desktop Table View -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Pelanggan</th>
                                <th>Layanan</th>
                                <th>Tanggal Selesai</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $historiPekerjaan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $histori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($loop->iteration); ?></td>
                                <td><?php echo e($histori->user?->nama_lengkap ?? $histori->user?->name ?? $histori->nama_pelanggan ?? 'Pelanggan Guest'); ?></td>
                                <td><?php echo e($histori->layanan->nama ?? '-'); ?></td>
                                <td><?php echo e($histori->updated_at->format('d M Y H:i')); ?></td>
                                <td>
                                    <span class="badge badge-success">Selesai</span>
                                </td>
                                <td>
                                    <a href="<?php echo e(route('teknisi.pekerjaan.show', $histori->id)); ?>" class="btn btn-info btn-sm mb-1">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                    <?php if(empty($histori->pembayaran) || $histori->pembayaran->status !== 'dibayar'): ?>
                                        <a href="<?php echo e(route('teknisi.pembayaran.form', $histori->id)); ?>" class="btn btn-warning btn-sm mb-1">
                                            <i class="fas fa-money-bill-wave"></i> Tagih Pembayaran
                                        </a>
                                    <?php else: ?>
                                        <span class="badge badge-primary"><i class="fas fa-check"></i> Sudah Dibayar</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada histori pekerjaan.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="d-block d-md-none">
                    <?php $__empty_1 = true; $__currentLoopData = $historiPekerjaan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $histori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="card shadow-sm mb-3 border-left-success">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="font-weight-bold text-dark"><?php echo e($histori->user?->nama_lengkap ?? $histori->user?->name ?? $histori->nama_pelanggan ?? 'Pelanggan Guest'); ?></span>
                                <span class="badge badge-success">Selesai</span>
                            </div>
                            <div class="mb-3">
                                <p class="small text-muted mb-1"><i class="fas fa-tools fa-fw mr-1"></i> <?php echo e($histori->layanan->nama ?? '-'); ?></p>
                                <p class="small text-muted mb-0"><i class="far fa-calendar-check fa-fw mr-1"></i> <?php echo e($histori->updated_at->format('d M Y H:i')); ?></p>
                            </div>
                            <div class="d-flex flex-column">
                                <a href="<?php echo e(route('teknisi.pekerjaan.show', $histori->id)); ?>" class="btn btn-info btn-sm btn-block mb-2">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                                <?php if(empty($histori->pembayaran) || $histori->pembayaran->status !== 'dibayar'): ?>
                                    <a href="<?php echo e(route('teknisi.pembayaran.form', $histori->id)); ?>" class="btn btn-warning btn-sm btn-block">
                                        <i class="fas fa-money-bill-wave"></i> Tagih Pembayaran
                                    </a>
                                <?php else: ?>
                                    <div class="bg-primary text-white text-center rounded py-2 small font-weight-bold">
                                        <i class="fas fa-check"></i> Sudah Dibayar
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center text-muted font-italic py-3 border rounded bg-light">Belum ada histori pekerjaan.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/teknisiac/dashboard/index.blade.php ENDPATH**/ ?>