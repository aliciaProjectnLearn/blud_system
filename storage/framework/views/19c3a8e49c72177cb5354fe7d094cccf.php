<?php $__env->startSection('title', 'Manajemen Pelanggan AC'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manajemen Pelanggan AC</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Pelanggan Aktif</h6>
        <form action="<?php echo e(route('admin.ac.pelanggan.index')); ?>" method="GET" class="form-inline">
            <div class="input-group input-group-sm">
                <input type="text" name="search" class="form-control" placeholder="Cari nama atau email..." value="<?php echo e(request('search')); ?>">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search fa-sm"></i>
                    </button>
                </div>
            </div>
            <?php if(request()->has('search') && request('search') != ''): ?>
                <a href="<?php echo e(route('admin.ac.pelanggan.index')); ?>" class="btn btn-secondary btn-sm ml-2">Reset</a>
            <?php endif; ?>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th>Nama Pelanggan</th>
                        <th>Email</th>
                        <th>No. HP</th>
                        <th class="text-center">Total Booking</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $pelanggans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $pelanggan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="text-center"><?php echo e($pelanggans->firstItem() + $index); ?></td>
                            <td>
                                <div class="font-weight-bold text-gray-800"><?php echo e($pelanggan->name); ?></div>
                            </td>
                            <td><?php echo e($pelanggan->email); ?></td>
                            <td><?php echo e($pelanggan->no_hp ?? '-'); ?></td>
                            <td class="text-center">
                                <span class="badge badge-pill badge-primary px-3">
                                    <?php echo e($pelanggan->booking_ac_count); ?> Pesanan
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="<?php echo e(route('admin.ac.pelanggan.show', $pelanggan->id)); ?>" class="btn btn-info btn-sm shadow-sm">
                                    <i class="fas fa-history mr-1"></i> Riwayat Layanan
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-users-slash fa-2x mb-3 d-block"></i>
                                Pelanggan tidak ditemukan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-4 d-flex justify-content-center">
            <?php echo e($pelanggans->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/adminac/pelanggan/index.blade.php ENDPATH**/ ?>