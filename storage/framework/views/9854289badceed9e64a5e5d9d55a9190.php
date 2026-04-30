<?php $__env->startSection('title', 'Laporan Kasir Servis Kendaraan'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Laporan Kasir Servis Kendaraan</h1>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <!-- Total Booking -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Booking Servis</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($totalBooking); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Transaksi Lunas -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Transaksi Lunas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($totalLunas); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Pendapatan -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Pendapatan Lunas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?php echo e(number_format($totalPendapatan, 0, ',', '.')); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary mb-3 mb-sm-0">Filter Data Laporan</h6>
            <div class="d-flex flex-wrap gap-2">
                <a href="<?php echo e(route('kasir.laporan.export.pdf', request()->all())); ?>" class="btn btn-danger btn-sm shadow-sm mr-2 mb-2 mb-sm-0" target="_blank">
                    <i class="fas fa-file-pdf fa-sm"></i> Export PDF
                </a>
                <a href="<?php echo e(route('kasir.laporan.export.excel', request()->all())); ?>" class="btn btn-success btn-sm shadow-sm mb-2 mb-sm-0">
                    <i class="fas fa-file-excel fa-sm"></i> Export Excel
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('kasir.laporan.index')); ?>" method="GET" class="row">
                <div class="col-md-3 mb-3">
                    <label class="small font-weight-bold">Dari Tanggal</label>
                    <input type="date" name="tanggal_dari" value="<?php echo e(request('tanggal_dari')); ?>" class="form-control">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="small font-weight-bold">Sampai Tanggal</label>
                    <input type="date" name="tanggal_sampai" value="<?php echo e(request('tanggal_sampai')); ?>" class="form-control">
                </div>
                <div class="col-md-2 mb-3">
                    <label class="small font-weight-bold">Metode Bayar</label>
                    <select name="metode_pembayaran" class="form-control">
                        <option value="">Semua</option>
                        <option value="tunai" <?php echo e(request('metode_pembayaran') == 'tunai' ? 'selected' : ''); ?>>Tunai</option>
                        <option value="transfer" <?php echo e(request('metode_pembayaran') == 'transfer' ? 'selected' : ''); ?>>Transfer</option>
                        <option value="qris" <?php echo e(request('metode_pembayaran') == 'qris' ? 'selected' : ''); ?>>QRIS</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="small font-weight-bold">Status</label>
                    <select name="status" class="form-control">
                        <option value="">Semua</option>
                        <option value="lunas" <?php echo e(request('status') == 'lunas' ? 'selected' : ''); ?>>Lunas</option>
                        <option value="belum_bayar" <?php echo e(request('status') == 'belum_bayar' ? 'selected' : ''); ?>>Belum Bayar</option>
                        <option value="dibatalkan" <?php echo e(request('status') == 'dibatalkan' ? 'selected' : ''); ?>>Batal</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3 d-flex flex-column justify-content-end">
                    <div class="d-flex w-100 mt-2 mt-md-0">
                        <button type="submit" class="btn btn-primary shadow-sm flex-grow-1">
                            <i class="fas fa-filter fa-sm"></i> Terapkan
                        </button>
                        <?php if(request()->anyFilled(['tanggal_dari', 'tanggal_sampai', 'metode_pembayaran', 'status'])): ?>
                            <a href="<?php echo e(route('kasir.laporan.index')); ?>" class="btn btn-light shadow-sm ml-2" title="Reset Filter">
                                <i class="fas fa-sync-alt"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">Detail Transaksi Pembayaran</h6>
        </div>
        <div class="card-body p-0">
            <!-- Tampilan Desktop (Tabel) -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover mb-0">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="px-4 py-3" width="50">No</th>
                            <th class="px-4 py-3">Kode / Tanggal</th>
                            <th class="px-4 py-3">Pelanggan</th>
                            <th class="px-4 py-3">Layanan Servis</th>
                            <th class="px-4 py-3 text-right">Total Biaya</th>
                            <th class="px-4 py-3 text-center">Metode</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $laporan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-4 py-3 align-middle"><?php echo e($laporan->firstItem() + $index); ?></td>
                                <td class="px-4 py-3 align-middle">
                                    <div class="font-weight-bold text-primary"><?php echo e($item->bookingServis->kode_booking ?? '-'); ?></div>
                                    <div class="small text-muted"><?php echo e($item->tanggal_bayar ? $item->tanggal_bayar->format('d/m/Y H:i') : '-'); ?></div>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <div class="font-weight-bold text-gray-800"><?php echo e($item->bookingServis->pelanggan->name ?? '-'); ?></div>
                                    <div class="small text-muted"><?php echo e($item->bookingServis->nomor_plat ?? '-'); ?></div>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <?php echo e($item->bookingServis->layananServis->nama_layanan ?? '-'); ?>

                                </td>
                                <td class="px-4 py-3 align-middle text-right font-weight-bold">
                                    Rp <?php echo e(number_format($item->total_biaya, 0, ',', '.')); ?>

                                </td>
                                <td class="px-4 py-3 align-middle text-center">
                                    <?php if($item->tipe_pembayaran): ?>
                                        <span class="badge badge-light px-2 py-1 border shadow-sm text-uppercase">
                                            <?php echo e($item->tipe_pembayaran); ?>

                                        </span>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 align-middle text-center">
                                    <?php
                                        $badgeClass = 'secondary';
                                        if($item->status_pembayaran == 'lunas') $badgeClass = 'success';
                                        elseif($item->status_pembayaran == 'belum_bayar') $badgeClass = 'warning';
                                        elseif($item->status_pembayaran == 'dibatalkan') $badgeClass = 'danger';
                                    ?>
                                    <span class="badge badge-<?php echo e($badgeClass); ?> px-3 py-2 text-uppercase">
                                        <?php echo e(str_replace('_', ' ', $item->status_pembayaran)); ?>

                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 text-gray-300"></i>
                                    <p>Tidak ada data laporan yang ditemukan.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Tampilan Mobile (Card List) -->
            <div class="d-md-none">
                <?php $__empty_1 = true; $__currentLoopData = $laporan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="card border-bottom-0 border-left-0 border-right-0 rounded-0 p-3 <?php echo e($loop->last ? '' : 'border-bottom'); ?>">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge badge-primary mr-1">#<?php echo e($laporan->firstItem() + $index); ?></span>
                                <span class="font-weight-bold text-primary"><?php echo e($item->bookingServis->kode_booking ?? '-'); ?></span>
                            </div>
                            <div class="small text-muted"><?php echo e($item->tanggal_bayar ? $item->tanggal_bayar->format('d/m/Y H:i') : '-'); ?></div>
                        </div>
                        
                        <div class="mb-2">
                            <div class="font-weight-bold text-gray-800">
                                <i class="fas fa-user text-gray-400 mr-1"></i> <?php echo e($item->bookingServis->pelanggan->name ?? '-'); ?> 
                                <span class="text-muted font-weight-normal">(<?php echo e($item->bookingServis->nomor_plat ?? '-'); ?>)</span>
                            </div>
                            <div class="small text-muted mt-1">
                                <i class="fas fa-tools text-gray-400 mr-1"></i> <?php echo e($item->bookingServis->layananServis->nama_layanan ?? '-'); ?>

                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-end mt-3 border-top pt-2">
                            <div>
                                <div class="small text-muted mb-1">Total Biaya</div>
                                <div class="font-weight-bold text-gray-900">Rp <?php echo e(number_format($item->total_biaya, 0, ',', '.')); ?></div>
                            </div>
                            <div class="text-right">
                                <?php if($item->tipe_pembayaran): ?>
                                    <span class="badge badge-light border shadow-sm text-uppercase d-block mb-1"><?php echo e($item->tipe_pembayaran); ?></span>
                                <?php endif; ?>
                                
                                <?php
                                    $badgeClass = 'secondary';
                                    if($item->status_pembayaran == 'lunas') $badgeClass = 'success';
                                    elseif($item->status_pembayaran == 'belum_bayar') $badgeClass = 'warning';
                                    elseif($item->status_pembayaran == 'dibatalkan') $badgeClass = 'danger';
                                ?>
                                <span class="badge badge-<?php echo e($badgeClass); ?> px-2 py-1 text-uppercase">
                                    <?php echo e(str_replace('_', ' ', $item->status_pembayaran)); ?>

                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-inbox fa-3x mb-3 text-gray-300"></i>
                        <p>Tidak ada data laporan yang ditemukan.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php if($laporan->hasPages()): ?>
            <div class="card-footer bg-white">
                <?php echo e($laporan->links('pagination::bootstrap-4')); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/kasirservis/laporan/index.blade.php ENDPATH**/ ?>