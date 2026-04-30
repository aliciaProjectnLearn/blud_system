<?php $__env->startSection('title', 'Laporan Transaksi Futsal'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .border-left-purple {
        border-left: .25rem solid #6f42c1 !important;
    }
    .text-purple {
        color: #6f42c1 !important;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">

    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Laporan Transaksi Booking Futsal</h1>
    </div>

    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Laporan</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('adminfutsal.laporan.index')); ?>">
                <div class="form-row align-items-end">
                    
                    
                    <div class="col-md-4 mb-3">
                        <label for="tanggal_dari">Dari Tanggal</label>
                        <input type="date" name="tanggal_dari" id="tanggal_dari" value="<?php echo e(request('tanggal_dari')); ?>" class="form-control">
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label for="tanggal_sampai">Sampai Tanggal</label>
                        <input type="date" name="tanggal_sampai" id="tanggal_sampai" value="<?php echo e(request('tanggal_sampai')); ?>" class="form-control">
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <button type="submit" class="btn btn-primary mr-2">
                            <i class="fas fa-search fa-sm"></i> Cari
                        </button>
                        <a href="<?php echo e(route('adminfutsal.laporan.index')); ?>" class="btn btn-secondary">
                            <i class="fas fa-undo fa-sm"></i> Reset
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    
    <div class="row mb-4">
        
        <div class="col-xl-6 col-md-6 mb-4 mb-md-0">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Pendapatan (Status Verifikasi)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp <?php echo e(number_format($totalPendapatan, 0, ',', '.')); ?>

                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-check-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-xl-6 col-md-6 d-flex justify-content-end align-items-center">
            <a href="<?php echo e(route('adminfutsal.laporan.export.pdf', request()->all())); ?>" target="_blank" class="btn btn-danger mr-2 shadow-sm">
                <i class="fas fa-file-pdf fa-sm text-white-50"></i> Export PDF
            </a>
            <a href="<?php echo e(route('adminfutsal.laporan.export.excel', request()->all())); ?>" target="_blank" class="btn btn-success shadow-sm">
                <i class="fas fa-file-excel fa-sm text-white-50"></i> Export Excel
            </a>
        </div>
    </div>

    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Transaksi</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama Pelanggan</th>
                            <th>Tgl Booking</th>
                            <th>Tgl Pembayaran</th>
                            <th>Jenis Pembayaran</th>
                            <th class="text-right">Jumlah Bayar</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $laporan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="text-center align-middle"><?php echo e($index + 1); ?></td>
                                <td class="align-middle"><?php echo e($item->booking->user->name ?? '-'); ?></td>
                                <td class="align-middle">
                                    <?php $bf = $item->booking->bookingFutsal; ?>
                                    <?php if($bf): ?>
                                        <?php if($bf->type === 'event'): ?>
                                            <?php echo e(\Carbon\Carbon::parse($bf->start_datetime)->format('d-m-Y')); ?> s/d <?php echo e(\Carbon\Carbon::parse($bf->end_datetime)->format('d-m-Y')); ?>

                                        <?php else: ?>
                                            <?php echo e(\Carbon\Carbon::parse($bf->start_datetime)->format('d-m-Y H:i')); ?>

                                        <?php endif; ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle"><?php echo e(\Carbon\Carbon::parse($item->tgl_bayar)->format('d-m-Y H:i')); ?></td>
                                <td class="align-middle">
                                    <?php echo e($item->booking->bookingFutsal->jenis_pembayaran ?? ($item->tipePembayaran->nama_tipe ?? '-')); ?>

                                </td>
                                <td class="align-middle text-right font-weight-bold">
                                    Rp <?php echo e(number_format($item->jumlah_bayar, 0, ',', '.')); ?>

                                </td>
                                <td class="align-middle text-center">
                                    <?php if($item->status == \App\Models\PembayaranFutsal::STATUS_VERIFIKASI): ?>
                                        <span class="badge badge-success px-2 py-1">Verifikasi</span>
                                    <?php elseif($item->status == \App\Models\PembayaranFutsal::STATUS_MENUNGGU): ?>
                                        <span class="badge badge-warning px-2 py-1">Menunggu</span>
                                    <?php elseif($item->status == \App\Models\PembayaranFutsal::STATUS_DIBATALKAN): ?>
                                        <span class="badge badge-danger px-2 py-1">Dibatalkan</span>
                                    <?php else: ?>
                                        
                                        <span class="badge badge-secondary px-2 py-1"><?php echo e($item->status ?? 'Unknown'); ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 mt-2"></i>
                                    <h5>Data laporan tidak ditemukan</h5>
                                    <p>Silakan ubah filter untuk mencari data transaksi.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
        </div>
        <?php if($laporan->hasPages()): ?>
        <div class="card-footer">
            <?php echo e($laporan->links()); ?>

        </div>
        <?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/adminfutsal/laporan/index.blade.php ENDPATH**/ ?>