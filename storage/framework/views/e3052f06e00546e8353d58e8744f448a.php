<?php $__env->startSection('content'); ?>
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Pembayaran Sewa</h1>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo e(session('success')); ?> <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo e(session('error')); ?> <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary mb-2 mb-md-0">Daftar Pembayaran</h6>
                <form method="GET" action="<?php echo e(route('adminkantin.pembayaran.index')); ?>">
                    <div class="row g-2">
                        <div class="col-12 col-md-auto">
                            <input type="text" name="search" class="form-control form-control-sm"
                                placeholder="Cari nama usaha..." value="<?php echo e(request('search')); ?>">
                        </div>
                        <div class="col-6 col-md-auto">
                            <select name="termin" class="form-control form-control-sm">
                                <option value="">-- Semua Termin --</option>
                                <option value="1" <?php echo e(request('termin') == '1' ? 'selected' : ''); ?>>Termin 1</option>
                                <option value="2" <?php echo e(request('termin') == '2' ? 'selected' : ''); ?>>Termin 2</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-auto">
                            <select name="status" class="form-control form-control-sm">
                                <option value="">-- Semua Status --</option>
                                <option value="menunggu" <?php echo e(request('status') === 'menunggu' ? 'selected' : ''); ?>>Menunggu</option>
                                <option value="verifikasi" <?php echo e(request('status') === 'verifikasi' ? 'selected' : ''); ?>>Verifikasi</option>
                                <option value="lunas" <?php echo e(request('status') === 'lunas' ? 'selected' : ''); ?>>Lunas</option>
                                <option value="dibatalkan" <?php echo e(request('status') === 'dibatalkan' ? 'selected' : ''); ?>>Dibatalkan</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-auto d-flex gap-1">
                            <button type="submit" class="btn btn-sm btn-secondary mr-1">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <?php if(request()->filled('search') || request()->filled('status') || request()->filled('termin')): ?>
                                <a href="<?php echo e(route('adminkantin.pembayaran.index')); ?>" class="btn btn-sm btn-light">Reset</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%">
                    <thead>
                        <tr>
                            <th class="d-none d-sm-table-cell">No</th>
                            <th>Penyewa</th>
                            <th>Kode Unit</th>
                            <th>Termin</th>
                            <th class="d-none d-md-table-cell">Jatuh Tempo</th>
                            <th>Jumlah Tagihan</th>
                            <th class="d-none d-lg-table-cell">Tgl Bayar</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $pembayarans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $terlambat = $p->isTerlambat(); ?>
                        <tr class="<?php echo e($terlambat ? 'table-danger' : ''); ?>">
                            <td class="d-none d-sm-table-cell"><?php echo e($pembayarans->firstItem() + $i); ?></td>
                            <td>
                                <strong><?php echo e($p->sewaRuko->penyewa->nama_usaha ?? '-'); ?></strong>
                                <?php if($terlambat): ?>
                                    <span class="badge badge-danger ml-1">Menunggak</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($p->sewaRuko->ruko->kode_unit ?? '-'); ?></td>
                            <td>Termin <?php echo e($p->termin); ?></td>
                            <td class="d-none d-md-table-cell">
                                <?php echo e($p->tgl_jatuh_tempo
                                    ? \Carbon\Carbon::parse($p->tgl_jatuh_tempo)->format('d M Y')
                                    : '-'); ?>

                            </td>
                            <td>Rp <?php echo e(number_format($p->jumlah_tagihan, 0, ',', '.')); ?></td>
                            <td class="d-none d-lg-table-cell">
                                <?php echo e($p->tgl_bayar
                                    ? \Carbon\Carbon::parse($p->tgl_bayar)->format('d M Y')
                                    : '-'); ?>

                            </td>
                            <td>
                                <?php if($p->status === 'lunas'): ?>
                                    <span class="badge badge-success">Lunas</span>
                                <?php elseif($p->status === 'verifikasi'): ?>
                                    <span class="badge badge-info">Verifikasi</span>
                                <?php elseif($p->status === 'menunggu'): ?>
                                    <span class="badge badge-warning">Menunggu</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Dibatalkan</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo e(route('adminkantin.pembayaran.show', $p)); ?>"
                                    class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if($p->status === 'verifikasi'): ?>
                                    <a href="<?php echo e(route('adminkantin.pembayaran.kwitansi', $p)); ?>"
                                        class="btn btn-success btn-sm">
                                        <i class="fas fa-download"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="9" class="text-center">Belum ada data pembayaran.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end">
                <?php echo e($pembayarans->withQueryString()->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/adminkantin/pembayaran/index.blade.php ENDPATH**/ ?>