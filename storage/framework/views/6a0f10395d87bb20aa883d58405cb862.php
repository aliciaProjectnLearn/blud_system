<?php $__env->startSection('title', 'Monitoring Aktivitas'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Monitoring Aktivitas Super Admin</h1>

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('dashboard.monitoring')); ?>">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="sistem">Sistem</label>
                        <select name="sistem" id="sistem" class="form-control">
                            <option value="">Semua Sistem</option>
                            <option value="Futsal" <?php echo e(request('sistem') == 'Futsal' ? 'selected' : ''); ?>>Futsal</option>
                            <option value="AC" <?php echo e(request('sistem') == 'AC' ? 'selected' : ''); ?>>AC</option>
                            <option value="Ruko" <?php echo e(request('sistem') == 'Ruko' ? 'selected' : ''); ?>>Ruko</option>
                            <option value="Auth" <?php echo e(request('sistem') == 'Auth' ? 'selected' : ''); ?>>Auth</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="start_date">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo e(request('start_date')); ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="end_date">Tanggal Akhir</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo e(request('end_date')); ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="search">Pencarian</label>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Nama user / deskripsi" value="<?php echo e(request('search')); ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="per_page">Tampilkan</label>
                        <select name="per_page" id="per_page" class="form-control">
                            <option value="5" <?php echo e(request('per_page') == 5 ? 'selected' : ''); ?>>5</option>
                            <option value="10" <?php echo e(request('per_page') == 10 ? 'selected' : ''); ?>>10</option>
                            <option value="25" <?php echo e(request('per_page') == 25 ? 'selected' : ''); ?>>25</option>
                            <option value="50" <?php echo e(request('per_page') == 50 ? 'selected' : ''); ?>>50</option>
                        </select>
                    </div>
                    <div class="col-md-9 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary mr-2">Filter</button>
                        <a href="<?php echo e(route('dashboard.monitoring')); ?>" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Log -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Aktivitas</h6>
        </div>
        <div class="card-body">
            <?php if($logs->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama User</th>
                                <th>Sistem</th>
                                <th>Aktivitas</th>
                                <th>Deskripsi</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($logs->firstItem() + $index); ?></td>
                                <td><?php echo e($log->nama_user); ?></td>
                                <td><?php echo e($log->sistem); ?></td>
                                <td><?php echo e($log->aktivitas); ?></td>
                                <td><?php echo e($log->deskripsi_aktivitas); ?></td>
                                <td><?php echo e($log->created_at->format('d/m/Y H:i:s')); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center">
                    <div>Menampilkan <?php echo e($logs->firstItem()); ?> - <?php echo e($logs->lastItem()); ?> dari <?php echo e($logs->total()); ?> data</div>
                    <div><?php echo e($logs->appends(request()->query())->links()); ?></div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    Tidak ada data aktivitas.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/dashboard/monitoring.blade.php ENDPATH**/ ?>