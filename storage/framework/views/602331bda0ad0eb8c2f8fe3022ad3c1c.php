<?php $__env->startSection('title', 'Manajemen Transaksi AC'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-file-invoice-dollar mr-2 text-primary"></i>Manajemen Transaksi
    </h1>
    <div>
        <a href="<?php echo e(route('adminac.transaksi.history')); ?>" class="btn btn-outline-primary btn-sm shadow-sm">
            <i class="fas fa-history fa-sm mr-1"></i> Histori Pembayaran
        </a>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col-md-8">
                <form action="<?php echo e(route('adminac.transaksi.index')); ?>" method="GET" class="form-inline">
                    <div class="input-group input-group-sm mr-2">
                        <input type="text" name="search" class="form-control" placeholder="Cari invoice atau pelanggan..." value="<?php echo e(request('search')); ?>">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary px-3">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                    
                    <select name="status" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                        <option value="dibayar" <?php echo e(request('status') == 'dibayar' ? 'selected' : ''); ?>>Dibayar</option>
                        <option value="ditolak" <?php echo e(request('status') == 'ditolak' ? 'selected' : ''); ?>>Ditolak</option>
                    </select>

                    <?php if(request()->hasAny(['search', 'status'])): ?>
                        <a href="<?php echo e(route('adminac.transaksi.index')); ?>" class="btn btn-secondary btn-sm">Reset</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" width="100%" cellspacing="0">
                <thead class="bg-light text-dark">
                    <tr>
                        <th class="pl-4" width="5%">No</th>
                        <th>No. Invoice</th>
                        <th>Pelanggan</th>
                        <th class="text-right">Total Tagihan</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Tgl. Dibuat</th>
                        <th class="pr-4 text-center" width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $transaksis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $transaksi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $statusClass = 'secondary';
                            if($transaksi->status == 'dibayar') $statusClass = 'success';
                            elseif($transaksi->status == 'pending') $statusClass = 'warning';
                            elseif($transaksi->status == 'ditolak') $statusClass = 'danger';
                        ?>
                        <tr>
                            <td class="pl-4 align-middle font-weight-bold"><?php echo e($transaksis->firstItem() + $index); ?></td>
                            <td class="align-middle">
                                <span class="badge badge-light p-2 border font-weight-bold text-primary">
                                    <?php echo e($transaksi->invoice_no); ?>

                                </span>
                            </td>
                            <td class="align-middle">
                                <div class="font-weight-bold"><?php echo e($transaksi->bookingAc->user->name ?? 'N/A'); ?></div>
                                <small class="text-muted">Booking #<?php echo e($transaksi->booking_id); ?></small>
                            </td>
                            <td class="align-middle text-right font-weight-bold text-dark">
                                Rp <?php echo e(number_format($transaksi->total_harga, 0, ',', '.')); ?>

                            </td>
                            <td class="align-middle text-center">
                                <span class="badge badge-pill badge-<?php echo e($statusClass); ?> px-3 py-1 font-weight-bold text-uppercase" style="font-size: 0.7rem;">
                                    <?php echo e($transaksi->status); ?>

                                </span>
                            </td>
                            <td class="align-middle text-center small text-muted">
                                <?php echo e($transaksi->created_at->translatedFormat('d M Y')); ?>

                            </td>
                            <td class="pr-4 align-middle text-center">
                                <a href="<?php echo e(route('adminac.transaksi.show', $transaksi->id)); ?>" class="btn btn-outline-info btn-sm shadow-sm" title="Lihat Invoice">
                                    <i class="fas fa-file-invoice mr-1"></i> Invoice
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-receipt fa-3x mb-3 text-gray-300 d-block"></i>
                                <span class="text-muted">Tidak ada data transaksi yang ditemukan.</span>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($transaksis->hasPages()): ?>
    <div class="card-footer bg-white border-top-0">
        <div class="d-flex justify-content-center">
            <?php echo e($transaksis->links()); ?>

        </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/adminac/transaksi/index.blade.php ENDPATH**/ ?>