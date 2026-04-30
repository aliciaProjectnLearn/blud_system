<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Layanan Servis</h1>
        <a href="<?php echo e(route('adminservis.layanan.create')); ?>" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Layanan
        </a>
    </div>

    
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo e(session('error')); ?>

            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <form method="GET" action="<?php echo e(route('adminservis.layanan.index')); ?>" 
                  class="d-flex gap-2 align-items-center flex-wrap">
                <input type="text" name="search" class="form-control form-control-sm w-auto"
                    placeholder="Cari nama layanan..." value="<?php echo e(request('search')); ?>">
                <select name="tipe_kendaraan" class="form-control form-control-sm w-auto">
                    <option value="">-- Semua Tipe --</option>
                    <option value="motor" <?php echo e(request('tipe_kendaraan') == 'motor' ? 'selected' : ''); ?>>Motor</option>
                    <option value="mobil" <?php echo e(request('tipe_kendaraan') == 'mobil' ? 'selected' : ''); ?>>Mobil</option>
                    <option value="keduanya" <?php echo e(request('tipe_kendaraan') == 'keduanya' ? 'selected' : ''); ?>>Semua</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="<?php echo e(route('adminservis.layanan.index')); ?>" class="btn btn-secondary btn-sm">Reset</a>
            </form>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Layanan</th>
                            <th>Tipe Kendaraan</th>
                            <th>Deskripsi</th>
                            <th>Harga Estimasi</th>
                            <th>Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $layanan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($layanan->firstItem() + $index); ?></td>
                                <td><?php echo e($item->nama_layanan); ?></td>
                                <td>
                                    <?php if($item->tipe_kendaraan === 'motor'): ?>
                                        <span class="badge badge-warning">Motor</span>
                                    <?php elseif($item->tipe_kendaraan === 'mobil'): ?>
                                        <span class="badge badge-primary">Mobil</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Semua</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($item->deskripsi ?? '-'); ?></td>
                                <td>Rp <?php echo e(number_format($item->harga_estimasi, 0, ',', '.')); ?></td>
                                <td>
                                    <?php if($item->is_active): ?>
                                        <span class="badge badge-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Non-Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo e(route('adminservis.layanan.edit', $item->id)); ?>"
                                        class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?php echo e(route('adminservis.layanan.destroy', $item->id)); ?>"
                                          method="POST" class="d-inline form-hapus">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="button" class="btn btn-danger btn-sm btn-hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">Data layanan tidak ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <?php echo e($layanan->links()); ?>

            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    document.querySelectorAll('.btn-hapus').forEach(function(btn) {
        btn.addEventListener('click', function() {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: 'Layanan yang sudah digunakan dalam transaksi tidak dapat dihapus.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e3342f',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.closest('form').submit();
                }
            });
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/adminservis/layanan/index.blade.php ENDPATH**/ ?>