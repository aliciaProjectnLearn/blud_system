<?php $__env->startSection('title', 'Manajemen Unit Kantin'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">

    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-store mr-2 text-primary"></i>Manajemen Unit Kantin
        </h1>
        <a href="<?php echo e(route('adminkantin.unit.create')); ?>" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-plus fa-sm mr-1"></i> Tambah Unit
        </a>
    </div>


    
    <div class="row mb-3">
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Unit</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($units->total()); ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-store fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Unit Terisi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo e($units->getCollection()->where('status_unit', 'terisi')->count()); ?>

                            </div>
                        </div>
                        <div class="col-auto"><i class="fas fa-door-closed fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Unit Kosong</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo e($units->getCollection()->where('status_unit', 'kosong')->count()); ?>

                            </div>
                        </div>
                        <div class="col-auto"><i class="fas fa-door-open fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card shadow mb-4">

        
        <div class="card-header py-3">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list mr-1"></i> Daftar Unit Kantin
                    </h6>
                </div>
                
                <div class="col-md-8">
                    <form method="GET" action="<?php echo e(route('adminkantin.unit.index')); ?>" class="form-inline justify-content-md-end">
                        
                        <div class="form-group mr-2 mb-0">
                            <label class="mr-1 text-xs font-weight-bold text-gray-600">Kategori</label>
                            <select name="kategori_id" class="form-control form-control-sm" onchange="this.form.submit()">
                                <option value="">-- Semua --</option>
                                <?php $__currentLoopData = $kategoris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($kat->id); ?>" <?php echo e(request('kategori_id') == $kat->id ? 'selected' : ''); ?>>
                                        <?php echo e($kat->nama); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        
                        <div class="form-group mr-2 mb-0">
                            <label class="mr-1 text-xs font-weight-bold text-gray-600">Status</label>
                            <select name="status_unit" class="form-control form-control-sm" onchange="this.form.submit()">
                                <option value="">-- Semua --</option>
                                <option value="terisi"  <?php echo e(request('status_unit') === 'terisi'  ? 'selected' : ''); ?>>Terisi</option>
                                <option value="kosong"  <?php echo e(request('status_unit') === 'kosong'  ? 'selected' : ''); ?>>Kosong</option>
                            </select>
                        </div>

                        
                        <?php if(request()->hasAny(['kategori_id', 'status_unit'])): ?>
                            <a href="<?php echo e(route('adminkantin.unit.index')); ?>" class="btn btn-sm btn-outline-secondary mb-0">
                                <i class="fas fa-times"></i> Reset
                            </a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTableUnit" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center" style="width:5%;">No</th>
                            <th style="width:12%;">Kode Unit</th>
                            <th style="width:20%;">Nama / Kategori</th>
                            <th class="text-right" style="width:18%;">Harga Sewa</th>
                            <th class="text-center" style="width:10%;">Dokumen</th>
                            <th class="text-center" style="width:12%;">Status</th>
                            <th class="text-center" style="width:23%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                
                                <td class="text-center"><?php echo e($units->firstItem() + $i); ?></td>

                                
                                <td>
                                    <span class="font-weight-bold text-primary">
                                        <?php echo e($unit->kode_unit ?? '-'); ?>

                                    </span>
                                </td>

                                
                                <td>
                                    <div class="font-weight-bold"><?php echo e($unit->kategori->nama ?? '-'); ?></div>
                                    <small class="text-muted">Unit #<?php echo e($unit->id); ?></small>
                                </td>

                                
                                <td class="text-right">
                                    <?php if($unit->harga): ?>
                                        <span class="font-weight-bold">
                                            Rp <?php echo e(number_format($unit->harga, 0, ',', '.')); ?>

                                        </span>
                                        <br><small class="text-muted">/tahun</small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>

                                
                                <td class="text-center">
                                    <?php if($unit->dokumentasiUnit->count() > 0): ?>
                                        <span class="badge badge-info">
                                            <?php echo e($unit->dokumentasiUnit->count()); ?> file
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>

                                
                                <td class="text-center">
                                    <?php if($unit->status_unit === 'terisi'): ?>
                                        <span class="badge badge-success px-2 py-1">
                                            <i class="fas fa-circle fa-xs mr-1"></i>Terisi
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary px-2 py-1">
                                            <i class="fas fa-circle fa-xs mr-1"></i>Kosong
                                        </span>
                                    <?php endif; ?>
                                </td>

                                
                                <td class="text-center">
                                    
                                    <a href="<?php echo e(route('adminkantin.unit.show', $unit->id)); ?>"
                                       class="btn btn-info btn-sm" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    
                                    <a href="<?php echo e(route('adminkantin.unit.edit', $unit->id)); ?>"
                                       class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>

                                    
                                    <?php if($unit->status_unit === 'kosong'): ?>
                                        <button type="button"
                                                class="btn btn-danger btn-sm btn-hapus"
                                                title="Hapus"
                                                data-id="<?php echo e($unit->id); ?>"
                                                data-kode="<?php echo e($unit->kode_unit); ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                        
                                        <form id="form-hapus-<?php echo e($unit->id); ?>"
                                              action="<?php echo e(route('adminkantin.unit.destroy', $unit->id)); ?>"
                                              method="POST" class="d-none">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                        </form>
                                    <?php else: ?>
                                        
                                        <button class="btn btn-danger btn-sm" disabled title="Tidak bisa dihapus saat Terisi">
                                            <i class="fas fa-lock"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    Belum ada data unit.
                                    <a href="<?php echo e(route('adminkantin.unit.create')); ?>">Tambah sekarang</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            
            <?php if($units->hasPages()): ?>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted small">
                        Menampilkan <?php echo e($units->firstItem()); ?>–<?php echo e($units->lastItem()); ?>

                        dari <?php echo e($units->total()); ?> unit
                    </div>
                    <?php echo e($units->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>

</div>


<div class="modal fade" id="modalHapus" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Konfirmasi Hapus
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-1">Apakah Anda yakin ingin menghapus unit:</p>
                <p class="font-weight-bold text-danger mb-0" id="kodeUnitTarget">-</p>
                <small class="text-muted">Semua file dokumentasi terkait juga akan ikut dihapus.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Batal
                </button>
                <button type="button" class="btn btn-danger btn-sm" id="btnKonfirmasiHapus">
                    <i class="fas fa-trash mr-1"></i>Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Tangkap semua tombol hapus → tampilkan modal konfirmasi
    let targetFormId = null;

    document.querySelectorAll('.btn-hapus').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id   = this.dataset.id;
            const kode = this.dataset.kode;

            targetFormId = 'form-hapus-' + id;
            document.getElementById('kodeUnitTarget').textContent = kode;
            $('#modalHapus').modal('show');
        });
    });

    // Submit form hapus setelah konfirmasi
    document.getElementById('btnKonfirmasiHapus').addEventListener('click', function () {
        if (targetFormId) {
            document.getElementById(targetFormId).submit();
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/adminkantin/unit/index.blade.php ENDPATH**/ ?>