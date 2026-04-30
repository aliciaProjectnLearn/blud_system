<?php $__env->startSection('title', 'Manajemen Transaksi'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Transaksi Futsal</h1>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Data Transaksi Pembayaran</h6>
            
            <!-- Filter Form -->
            <form method="GET" action="<?php echo e(route('adminfutsal.transaksi.index')); ?>" class="form-inline">
                <select name="jenis_transaksi" class="form-control form-control-sm mr-2">
                    <option value="">-- Semua Jenis --</option>
                    <option value="booking" <?php echo e(request('jenis_transaksi') == 'booking' ? 'selected' : ''); ?>>Booking</option>
                    <option value="membership" <?php echo e(request('jenis_transaksi') == 'membership' ? 'selected' : ''); ?>>Membership</option>
                    <option value="event" <?php echo e(request('jenis_transaksi') == 'event' ? 'selected' : ''); ?>>Event</option>
                    <option value="guest" <?php echo e(request('jenis_transaksi') == 'guest' ? 'selected' : ''); ?>>Guest</option>
                </select>

                <select name="status" class="form-control form-control-sm mr-2">
                    <option value="">-- Semua Status --</option>
                    <option value="menunggu" <?php echo e(request('status') == 'menunggu' ? 'selected' : ''); ?>>Menunggu</option>
                    <option value="verifikasi" <?php echo e(request('status') == 'verifikasi' ? 'selected' : ''); ?>>Verifikasi</option>
                    <option value="dibatalkan" <?php echo e(request('status') == 'dibatalkan' ? 'selected' : ''); ?>>Dibatalkan</option>
                </select>
                
                <button type="submit" class="btn btn-sm btn-secondary"><i class="fas fa-filter"></i> Filter</button>
                <?php if(request()->filled('jenis_transaksi') || request()->filled('status')): ?>
                    <a href="<?php echo e(route('adminfutsal.transaksi.index')); ?>" class="btn btn-sm btn-light ml-1">Reset</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Kode Pembayaran</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Jenis Transaksi</th>
                            <th>Total Harga</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $transaksis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $trx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>
                            <td><strong><?php echo e($trx->kode_pembayaran); ?></strong></td>
                            <td><?php echo e(\Carbon\Carbon::parse($trx->tgl_bayar ?? $trx->created_at)->format('d M Y H:i')); ?></td>
                            <td><?php echo e($trx->booking->user->name ?? 'User Tidak Diketahui'); ?></td>
                            <td>
                                <?php if($trx->jenis_transaksi == 'membership'): ?>
                                    <span class="badge badge-info shadow-sm"><i class="fas fa-id-card"></i> Membership</span>
                                <?php elseif($trx->jenis_transaksi == 'event'): ?>
                                    <span class="badge badge-warning shadow-sm"><i class="fas fa-calendar-check"></i> Event</span>
                                <?php elseif($trx->jenis_transaksi == 'guest'): ?>
                                    <span class="badge badge-secondary shadow-sm">Guest</span>
                                <?php else: ?>
                                    <span class="badge badge-primary shadow-sm"><i class="fas fa-calendar-check"></i> Booking</span>
                                <?php endif; ?>
                            </td>
                            <td>Rp <?php echo e(number_format($trx->jumlah_bayar, 0, ',', '.')); ?></td>
                            <td>
                                <?php if($trx->status == 'verifikasi'): ?>
                                    <span class="badge badge-primary" style="font-size: 0.9em;">Verifikasi</span>
                                <?php elseif($trx->status == 'menunggu'): ?>
                                    <span class="badge badge-warning text-dark" style="font-size: 0.9em;">Menunggu</span>
                                <?php elseif($trx->status == 'dibatalkan'): ?>
                                    <span class="badge badge-danger" style="font-size: 0.9em;">Dibatalkan</span>
                                <?php else: ?>
                                    <span class="badge badge-success" style="font-size: 0.9em;"><?php echo e(ucfirst($trx->status)); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo e(route('adminfutsal.transaksi.show', $trx->id)); ?>" class="btn btn-info btn-sm btn-circle" title="Detail Transaksi">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center">Belum ada data transaksi.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/adminfutsal/transaksi/index.blade.php ENDPATH**/ ?>