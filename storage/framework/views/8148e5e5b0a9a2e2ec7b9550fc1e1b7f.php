<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Manajemen Penyewaan</h1>

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Pencarian</h6>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('adminkantin.penyewaan.index')); ?>" method="GET" class="row">
                <div class="col-md-3 mb-3">
                    <label>Status Sewa</label>
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="aktif" <?php echo e(request('status') == 'aktif' ? 'selected' : ''); ?>>Aktif</option>
                        <option value="selesai" <?php echo e(request('status') == 'selesai' ? 'selected' : ''); ?>>Selesai</option>
                        <option value="dibatalkan" <?php echo e(request('status') == 'dibatalkan' ? 'selected' : ''); ?>>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Unit Ruko</label>
                    <select name="ruko_id" class="form-control">
                        <option value="">Semua Unit</option>
                        <?php $__currentLoopData = $rukos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ruko): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($ruko->id); ?>" <?php echo e(request('ruko_id') == $ruko->id ? 'selected' : ''); ?>>
                                <?php echo e($ruko->kode_unit); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Nama Penyewa</label>
                    <select name="penyewa_id" class="form-control">
                        <option value="">Semua Penyewa</option>
                        <?php $__currentLoopData = $penyewas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($p->id); ?>" <?php echo e(request('penyewa_id') == $p->id ? 'selected' : ''); ?>>
                                <?php echo e($p->user->nama_lengkap ?? $p->nama_usaha); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">Cari</button>
                    <a href="<?php echo e(route('adminkantin.penyewaan.index')); ?>" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Penyewaan Ruko / Kantin</h6>
        </div>
        <div class="card-body">
            <?php if(session('success')): ?>
                <div class="alert alert-success"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-bordered" width="100%">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Penyewa</th>
                            <th>Kode Unit</th>
                            <th>Jenis Unit</th>
                            <th>Tgl Mulai</th>
                            <th>Tgl Selesai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>
                            <td><?php echo e($item->penyewa->user->nama_lengkap ?? $item->penyewa->nama_usaha); ?></td>
                            <td><?php echo e($item->ruko->kode_unit); ?></td>
                            <td><?php echo e($item->ruko->kategori->nama ?? '-'); ?></td>
                            <td><?php echo e(\Carbon\Carbon::parse($item->tgl_mulai)->format('d/m/Y')); ?></td>
                            <td><?php echo e(\Carbon\Carbon::parse($item->tgl_selesai)->format('d/m/Y')); ?></td>
                            <td>
                                <?php if($item->status == 'aktif'): ?>
                                    <span class="badge badge-success">Aktif</span>
                                <?php elseif($item->status == 'selesai'): ?>
                                    <span class="badge badge-secondary">Selesai</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Dibatalkan</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo e(route('adminkantin.penyewaan.show', $item->id)); ?>" class="btn btn-info btn-sm">Detail</a>
                                
                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal<?php echo e($item->id); ?>">
                                    Edit
                                </button>

                                <?php if($item->status != 'aktif'): ?>
                                <form action="<?php echo e(route('adminkantin.penyewaan.destroy', $item->id)); ?>" method="POST" style="display:inline;" class="delete-form">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirmDelete(event)">Hapus</button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal<?php echo e($item->id); ?>" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <form action="<?php echo e(route('adminkantin.penyewaan.update', $item->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Penyewaan: <?php echo e($item->ruko->kode_unit); ?></h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Tanggal Mulai</label>
                                                <input type="date" name="tgl_mulai" class="form-control" value="<?php echo e($item->tgl_mulai); ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Tanggal Selesai</label>
                                                <input type="date" name="tgl_selesai" class="form-control" value="<?php echo e($item->tgl_selesai); ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Total Biaya Tahunan</label>
                                                <input type="number" name="total_biaya_tahunan" class="form-control" value="<?php echo e($item->total_biaya_tahunan); ?>">
                                            </div>
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="status" class="form-control" required>
                                                    <option value="aktif" <?php echo e($item->status == 'aktif' ? 'selected' : ''); ?>>Aktif</option>
                                                    <option value="selesai" <?php echo e($item->status == 'selesai' ? 'selected' : ''); ?>>Selesai</option>
                                                    <option value="dibatalkan" <?php echo e($item->status == 'dibatalkan' ? 'selected' : ''); ?>>Dibatalkan</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data penyewaan</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(event) {
        event.preventDefault();
        const form = event.target.closest('form');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data penyewaan akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/adminkantin/penyewaan/index.blade.php ENDPATH**/ ?>