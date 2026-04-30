<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Produk Servis</h1>
        <button class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#modalTambah">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Produk
        </button>
    </div>

    
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo e(session('error')); ?>

            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($err); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <form method="GET" action="<?php echo e(route('adminservis.produk.index')); ?>" 
                  class="d-flex gap-2 align-items-center flex-wrap">
                <input type="text" name="search" class="form-control form-control-sm w-auto"
                    placeholder="Cari nama produk..." value="<?php echo e(request('search')); ?>">
                <select name="tipe_kendaraan" class="form-control form-control-sm w-auto">
                    <option value="">-- Semua Tipe --</option>
                    <option value="motor" <?php echo e(request('tipe_kendaraan') == 'motor' ? 'selected' : ''); ?>>Motor</option>
                    <option value="mobil" <?php echo e(request('tipe_kendaraan') == 'mobil' ? 'selected' : ''); ?>>Mobil</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="<?php echo e(route('adminservis.produk.index')); ?>" class="btn btn-secondary btn-sm">Reset</a>
            </form>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Produk</th>
                            <th>Tipe Kendaraan</th>
                            <th>Kode Part / Merk</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $produks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($produks->firstItem() + $index); ?></td>
                                <td><?php echo e($item->nama_produk); ?></td>
                                <td>
                                    <?php if($item->tipe_kendaraan === 'motor'): ?>
                                        <span class="badge badge-warning">Motor</span>
                                    <?php elseif($item->tipe_kendaraan === 'mobil'): ?>
                                        <span class="badge badge-primary">Mobil</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($item->kode_part ?? '-'); ?> / <?php echo e($item->merk ?? '-'); ?></td>
                                <td>Rp <?php echo e(number_format($item->harga, 2, ',', '.')); ?></td>
                                <td><?php echo e($item->stok); ?> <?php echo e($item->satuan); ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalEdit<?php echo e($item->id); ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="<?php echo e(route('adminservis.produk.destroy', $item->id)); ?>"
                                          method="POST" class="d-inline form-hapus">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="button" class="btn btn-danger btn-sm btn-hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Modal Edit -->
                            <div class="modal fade" id="modalEdit<?php echo e($item->id); ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <form action="<?php echo e(route('adminservis.produk.update', $item->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Produk</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Nama Produk <span class="text-danger">*</span></label>
                                                    <input type="text" name="nama_produk" class="form-control" value="<?php echo e($item->nama_produk); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Tipe Kendaraan <span class="text-danger">*</span></label>
                                                    <select name="tipe_kendaraan" class="form-control" required>
                                                        <option value="motor" <?php echo e($item->tipe_kendaraan == 'motor' ? 'selected' : ''); ?>>Motor</option>
                                                        <option value="mobil" <?php echo e($item->tipe_kendaraan == 'mobil' ? 'selected' : ''); ?>>Mobil</option>
                                                    </select>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 form-group">
                                                        <label>Harga <span class="text-danger">*</span></label>
                                                        <input type="number" step="0.01" name="harga" class="form-control" value="<?php echo e($item->harga); ?>" required>
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                        <label>Stok <span class="text-danger">*</span></label>
                                                        <input type="number" name="stok" class="form-control" value="<?php echo e($item->stok); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 form-group">
                                                        <label>Kode Part</label>
                                                        <input type="text" name="kode_part" class="form-control" value="<?php echo e($item->kode_part); ?>">
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                        <label>Merk</label>
                                                        <input type="text" name="merk" class="form-control" value="<?php echo e($item->merk); ?>">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 form-group">
                                                        <label>Satuan</label>
                                                        <input type="text" name="satuan" class="form-control" value="<?php echo e($item->satuan); ?>" placeholder="pcs">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Deskripsi</label>
                                                    <textarea name="deskripsi" class="form-control" rows="3"><?php echo e($item->deskripsi); ?></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">Data produk tidak ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <?php echo e($produks->links()); ?>

            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="<?php echo e(route('adminservis.produk.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Produk Servis</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="nama_produk" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Tipe Kendaraan <span class="text-danger">*</span></label>
                        <select name="tipe_kendaraan" class="form-control" required>
                            <option value="motor">Motor</option>
                            <option value="mobil">Mobil</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Harga <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="harga" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Stok <span class="text-danger">*</span></label>
                            <input type="number" name="stok" class="form-control" value="0" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Kode Part</label>
                            <input type="text" name="kode_part" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Merk</label>
                            <input type="text" name="merk" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Satuan</label>
                            <input type="text" name="satuan" class="form-control" value="pcs">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    document.querySelectorAll('.btn-hapus').forEach(function(btn) {
        btn.addEventListener('click', function() {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: 'Produk yang sudah digunakan dalam transaksi tidak dapat dihapus.',
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/adminservis/produk/index.blade.php ENDPATH**/ ?>