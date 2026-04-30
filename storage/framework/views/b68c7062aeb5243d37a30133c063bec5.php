<?php $__env->startSection('title', 'Manajemen Booking Servis'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Booking Servis</h1>
    </div>

    <!-- Alert Success/Error -->
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo e(session('error')); ?>

            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="<?php echo e(route('kasir.booking.index')); ?>" method="GET" class="row">
                <div class="col-md-3 mb-3">
                    <label class="small font-weight-bold">Status Booking</label>
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="menunggu" <?php echo e(request('status') == 'menunggu' ? 'selected' : ''); ?>>Menunggu</option>
                        <option value="diproses" <?php echo e(request('status') == 'diproses' ? 'selected' : ''); ?>>Diproses</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="small font-weight-bold">Tanggal Booking</label>
                    <input type="date" name="tanggal" value="<?php echo e(request('tanggal')); ?>" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="small font-weight-bold">Nama Pelanggan</label>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari nama..." class="form-control">
                </div>
                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search fa-sm"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Status Slot Component -->
    <?php echo $__env->make('adminservis.booking.partials.slot_summary', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Booking Masuk</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="px-4 py-3" width="50">No</th>
                            <th class="px-4 py-3">Pelanggan</th>
                            <th class="px-4 py-3">Kendaraan</th>
                            <th class="px-4 py-3">Tanggal Booking</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-4 py-3 align-middle"><?php echo e($bookings->firstItem() + $index); ?></td>
                                <td class="px-4 py-3 align-middle">
                                    <div class="font-weight-bold text-gray-800"><?php echo e($booking->pelanggan->name ?? 'N/A'); ?></div>
                                    <div class="small text-muted"><?php echo e($booking->kode_booking); ?></div>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <div class="text-uppercase small font-weight-bold"><?php echo e($booking->merek_kendaraan); ?> <?php echo e($booking->tipe_kendaraan); ?></div>
                                    <div class="badge badge-dark"><?php echo e($booking->nomor_plat); ?></div>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <div><?php echo e($booking->tanggal_booking->translatedFormat('d M Y')); ?></div>
                                    <div class="small text-muted"><?php echo e($booking->jam_booking); ?></div>
                                </td>
                                <td class="px-4 py-3 align-middle text-center">
                                    <?php
                                        $badgeClass = 'secondary';
                                        if($booking->status == 'menunggu') $badgeClass = 'warning';
                                        elseif($booking->status == 'diproses') $badgeClass = 'primary';
                                        elseif($booking->status == 'selesai') $badgeClass = 'success';
                                        elseif($booking->status == 'batal') $badgeClass = 'danger';
                                    ?>
                                    <span class="badge badge-<?php echo e($badgeClass); ?> px-3 py-2 text-uppercase"><?php echo e($booking->status); ?></span>
                                </td>
                                <td class="px-4 py-3 align-middle text-center">
                                    <a href="<?php echo e(route('kasir.booking.show', $booking->id)); ?>" class="btn btn-info btn-sm shadow-sm">
                                        <i class="fas fa-wrench fa-sm"></i> Proses Rincian
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                    <p>Tidak ada booking yang ditemukan.</p>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/kasirservis/booking/index.blade.php ENDPATH**/ ?>