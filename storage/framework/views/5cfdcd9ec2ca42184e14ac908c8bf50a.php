<?php $__env->startSection('title', 'Manajemen Transaksi Servis'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Manajemen Transaksi Servis</h1>
            <p class="mb-0 text-muted small">Fitur Read-Only — Admin hanya dapat memantau dan menganalisis transaksi.</p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow mb-4 border-left-primary">
        <div class="card-body">
            <form action="<?php echo e(route('adminservis.transaksi.index')); ?>" method="GET" id="filterForm">
                <div class="row align-items-end">
                    <div class="col-md-3 mb-3">
                        <label class="form-label small font-weight-bold">Dari Tanggal</label>
                        <input type="date" name="tanggal_dari" value="<?php echo e(request('tanggal_dari')); ?>" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label small font-weight-bold">Sampai Tanggal</label>
                        <input type="date" name="tanggal_sampai" value="<?php echo e(request('tanggal_sampai')); ?>" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label small font-weight-bold">Status</label>
                        <select name="status" class="form-control form-control-sm">
                            <option value="">Semua Status</option>
                            <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                            <option value="dibayar" <?php echo e(request('status') == 'dibayar' ? 'selected' : ''); ?>>Dibayar</option>
                            <option value="gagal" <?php echo e(request('status') == 'gagal' ? 'selected' : ''); ?>>Gagal</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label small font-weight-bold">Cari Pelanggan</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Nama pelanggan..." class="form-control">
                        </div>
                    </div>
                    <div class="col-md-1 mb-3">
                        <div class="btn-group w-100">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                            <a href="<?php echo e(route('adminservis.transaksi.index')); ?>" class="btn btn-secondary btn-sm">
                                <i class="fas fa-undo fa-sm"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card shadow mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 text-xs font-weight-bold text-uppercase" width="50">No</th>
                            <th class="px-4 py-3 text-xs font-weight-bold text-uppercase">Nama Pelanggan</th>
                            <th class="px-4 py-3 text-xs font-weight-bold text-uppercase">Tanggal Transaksi</th>
                            <th class="px-4 py-3 text-xs font-weight-bold text-uppercase text-right">Total Pembayaran</th>
                            <th class="px-4 py-3 text-xs font-weight-bold text-uppercase text-center">Metode</th>
                            <th class="px-4 py-3 text-xs font-weight-bold text-uppercase text-center">Status</th>
                            <th class="px-4 py-3 text-xs font-weight-bold text-uppercase text-center" width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $transaksi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-4 py-3 align-middle text-muted small"><?php echo e($transaksi->firstItem() + $index); ?></td>
                                <td class="px-4 py-3 align-middle font-weight-bold text-gray-800">
                                    <?php echo e($item->bookingServis->pelanggan->name ?? 'N/A'); ?>

                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <?php echo e($item->tanggal_bayar ? \Carbon\Carbon::parse($item->tanggal_bayar)->translatedFormat('d M Y') : '-'); ?>

                                </td>
                                <td class="px-4 py-3 align-middle text-right font-weight-bold text-primary">
                                    Rp <?php echo e(number_format($item->total_biaya, 0, ',', '.')); ?>

                                </td>
                                <td class="px-4 py-3 align-middle text-center text-uppercase small font-weight-bold text-muted">
                                    <?php echo e($item->tipe_pembayaran); ?>

                                </td>
                                <td class="px-4 py-3 align-middle text-center">
                                    <?php
                                        $statusClass = 'secondary';
                                        $statusLabel = $item->status_pembayaran;
                                        
                                        if (in_array($item->status_pembayaran, ['belum_bayar', 'dp'])) {
                                            $statusClass = 'warning';
                                            $statusLabel = 'Pending';
                                        } elseif ($item->status_pembayaran == 'lunas') {
                                            $statusClass = 'success';
                                            $statusLabel = 'Dibayar';
                                        }
                                    ?>
                                    <span class="badge badge-<?php echo e($statusClass); ?> px-2 py-1 text-uppercase" style="font-size: 0.7rem;">
                                        <?php echo e($statusLabel); ?>

                                    </span>
                                </td>
                                <td class="px-4 py-3 align-middle text-center">
                                    <a href="<?php echo e(route('adminservis.transaksi.show', $item->id)); ?>" class="btn btn-info btn-sm btn-circle shadow-sm" title="Lihat Detail">
                                        <i class="fas fa-eye fa-sm"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-receipt fa-3x mb-3 opacity-25"></i>
                                        <p class="mb-0">Tidak ada data transaksi yang ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Pagination -->
        <?php if($transaksi->hasPages()): ?>
            <div class="card-footer bg-white py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="small text-muted">
                        Menampilkan <?php echo e($transaksi->firstItem()); ?> hingga <?php echo e($transaksi->lastItem()); ?> dari <?php echo e($transaksi->total()); ?> entri
                    </div>
                    <div>
                        <?php echo e($transaksi->links('pagination::bootstrap-4')); ?>

                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .text-xs { font-size: 0.75rem; }
    .opacity-25 { opacity: 0.25; }
    .btn-circle {
        width: 30px;
        height: 30px;
        padding: 6px 0;
        border-radius: 15px;
        text-align: center;
        font-size: 12px;
        line-height: 1.42857;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(78, 115, 223, 0.03);
    }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/adminservis/transaksi/index.blade.php ENDPATH**/ ?>