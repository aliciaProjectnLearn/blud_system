<?php $__env->startSection('title', 'Menu Pembayaran Kasir'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">

    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Menu Pembayaran</h1>
        <span class="badge badge-success px-3 py-2" style="font-size:0.85rem;">
            <i class="fas fa-circle fa-xs"></i> Menampilkan booking siap bayar
        </span>
    </div>

    
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo e(session('error')); ?>

            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    <?php endif; ?>

    
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="<?php echo e(route('kasir.pembayaran.index')); ?>" method="GET" class="row">
                <div class="col-md-4 mb-3">
                    <label class="small font-weight-bold">Tanggal Booking</label>
                    <input type="date" name="tanggal"
                           value="<?php echo e(request('tanggal')); ?>"
                           class="form-control">
                </div>
                <div class="col-md-5 mb-3">
                    <label class="small font-weight-bold">Nama Pelanggan</label>
                    <input type="text" name="search"
                           value="<?php echo e(request('search')); ?>"
                           placeholder="Cari nama pelanggan..."
                           class="form-control">
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">
                        <i class="fas fa-search fa-sm"></i> Cari
                    </button>
                    <a href="<?php echo e(route('kasir.pembayaran.index')); ?>"
                       class="btn btn-secondary">
                        <i class="fas fa-times fa-sm"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-money-bill-wave mr-1"></i> Booking Siap Bayar
            </h6>
            <span class="text-muted small">Total: <?php echo e($bookings->total()); ?> booking</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="px-4 py-3" width="50">No</th>
                            <th class="px-4 py-3">Pelanggan</th>
                            <th class="px-4 py-3">Kendaraan</th>
                            <th class="px-4 py-3">Layanan</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3 text-right">Total Biaya</th>
                            <th class="px-4 py-3 text-center">Status Bayar</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $totalBiaya = $booking->rincianServis->sum('subtotal');
                                $statusBayar = $booking->pembayaranServis->status_pembayaran ?? 'belum_bayar';
                            ?>
                            <tr>
                                <td class="px-4 py-3 align-middle">
                                    <?php echo e($bookings->firstItem() + $index); ?>

                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <div class="font-weight-bold text-gray-800">
                                        <?php echo e($booking->pelanggan->name ?? 'N/A'); ?>

                                    </div>
                                    <div class="small text-muted"><?php echo e($booking->kode_booking); ?></div>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <div class="text-uppercase small font-weight-bold">
                                        <?php echo e($booking->merek_kendaraan); ?> <?php echo e($booking->tipe_kendaraan); ?>

                                    </div>
                                    <span class="badge badge-dark"><?php echo e($booking->nomor_plat); ?></span>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <?php echo e($booking->layananServis->nama_layanan ?? '-'); ?>

                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <div><?php echo e($booking->tanggal_booking->translatedFormat('d M Y')); ?></div>
                                    <div class="small text-muted"><?php echo e($booking->jam_booking); ?></div>
                                </td>
                                <td class="px-4 py-3 align-middle text-right font-weight-bold">
                                    Rp <?php echo e(number_format($totalBiaya, 0, ',', '.')); ?>

                                </td>
                                <td class="px-4 py-3 align-middle text-center">
                                    <?php if($statusBayar === 'lunas'): ?>
                                        <span class="badge badge-success px-3 py-2">Lunas</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning px-3 py-2">Belum Bayar</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 align-middle text-center">
                                    <a href="<?php echo e(route('kasir.pembayaran.show', $booking->id)); ?>"
                                       class="btn btn-success btn-sm shadow-sm">
                                        <i class="fas fa-cash-register fa-sm"></i> Proses Bayar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                                    <p class="mb-0">Tidak ada booking yang menunggu pembayaran.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if($bookings->hasPages()): ?>
            <div class="card-footer bg-white">
                <?php echo e($bookings->links('pagination::bootstrap-4')); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/kasirservis/pembayaran/index.blade.php ENDPATH**/ ?>