<?php $__env->startSection('title', 'Manajemen Teknisi'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-4">
    <h1 class="h3 mb-3 mb-sm-0 text-gray-800">Manajemen Teknisi AC</h1>
    <a href="<?php echo e(route('admin.ac.teknisi.create')); ?>" class="btn btn-primary btn-sm shadow-sm w-100 w-sm-auto">
        <i class="fas fa-plus fa-sm text-white-50 mr-2"></i> Tambah Teknisi
    </a>
</div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Kinerja Teknisi</h6>
            
            
            <form method="GET" action="<?php echo e(route('admin.ac.teknisi.index')); ?>" class="form-inline">
                <label for="bulan_filter" class="mr-2 small">Bulan:</label>
                <select name="bulan_filter" id="bulan_filter" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                    <?php $__currentLoopData = range(1, 12); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($m); ?>" <?php echo e(request('bulan_filter', \Carbon\Carbon::today()->month) == $m ? 'selected' : ''); ?>>
                            <?php echo e(\Carbon\Carbon::create()->month($m)->translatedFormat('F')); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php if(request('search') || request('status')): ?>
                    <input type="hidden" name="search" value="<?php echo e(request('search')); ?>">
                    <input type="hidden" name="status" value="<?php echo e(request('status')); ?>">
                <?php endif; ?>
            </form>
        </div>
        <div class="card-body">
            <?php if($dataTeknisi->isEmpty()): ?>
                <p class="text-center text-muted">Belum ada data teknisi terdaftar.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nama Teknisi</th>
                                <th>Telepon / NIK</th>
                                <th>Pekerjaan Selesai (Sesuai Filter Bulan)</th>
                                <th>Tugas Aktif (Proses)</th>
                                <th>Status Ketersediaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $dataTeknisi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teknisi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($teknisi->nama_lengkap ?? $teknisi->name); ?></td>
                                    <td><?php echo e($teknisi->no_hp ?? '-'); ?><br><small class="text-muted"><?php echo e($teknisi->nik ?? ''); ?></small></td>
                                    <td class="text-center"><strong><?php echo e($teknisi->total_selesai_bulan_ini); ?></strong> Tugas</td>
                                    <td class="text-center"><?php echo e($teknisi->total_aktif); ?> Tugas</td>
                                    <td>
                                        <?php if($teknisi->total_aktif > 0): ?>
                                            <span class="badge badge-warning">Sedang Bertugas</span>
                                        <?php else: ?>
                                            <span class="badge badge-success">Tersedia (Kosong)</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary mb-2 mb-md-0">Daftar Teknisi</h6>
        <form action="<?php echo e(route('admin.ac.teknisi.index')); ?>" method="GET" class="form-inline w-100 w-md-auto d-flex flex-column flex-sm-row flex-wrap gap-2">
            <div class="input-group input-group-sm flex-grow-1 mb-2 mb-sm-0">
                <input type="text" name="search" class="form-control" placeholder="Cari nama, email, no HP..." value="<?php echo e(request('search')); ?>">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search fa-sm"></i>
                    </button>
                </div>
            </div>
            <div class="d-flex w-100 w-sm-auto mb-2 mb-sm-0 gap-2">
                <select name="status" class="form-control form-control-sm w-100 w-sm-auto" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="tersedia" <?php echo e(request('status') === 'tersedia' ? 'selected' : ''); ?>>Tersedia</option>
                    <option value="sibuk" <?php echo e(request('status') === 'sibuk' ? 'selected' : ''); ?>>Sibuk</option>
                </select>
                <?php if(request('search') || request('status')): ?>
                    <a href="<?php echo e(route('admin.ac.teknisi.index')); ?>" class="btn btn-secondary btn-sm flex-shrink-0">Reset</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>No. Handphone</th>
                        <th>Status</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $teknisis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $teknisi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($teknisis->firstItem() + $index); ?></td>
                            <td><?php echo e($teknisi->nama_lengkap); ?></td>
                            <td><?php echo e($teknisi->email); ?></td>
                            <td><?php echo e($teknisi->no_hp ?? '-'); ?></td>
                            <td>
                                <?php if($teknisi->status_dinamis === 'tersedia'): ?>
                                    <span class="badge badge-success">Tersedia</span>
                                <?php else: ?>
                                    <span class="badge badge-warning text-white">Sibuk</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo e(route('admin.ac.teknisi.edit', $teknisi->id)); ?>" class="btn btn-info btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal<?php echo e($teknisi->id); ?>" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Delete Modal -->
                        <div class="modal fade" id="deleteModal<?php echo e($teknisi->id); ?>" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                                        <button type="button" class="close text-white" data-dismiss="modal">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="<?php echo e(route('admin.ac.teknisi.destroy', $teknisi->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <div class="modal-body">
                                            <p>Apakah Anda yakin ingin menghapus teknisi <strong><?php echo e($teknisi->nama_lengkap); ?></strong>?</p>
                                            <?php if($teknisi->status_dinamis === 'sibuk'): ?>
                                                <div class="alert alert-warning mb-0">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                                    Teknisi ini sedang menangani booking dan <strong>tidak dapat dihapus</strong>.
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger" <?php echo e($teknisi->status_dinamis === 'sibuk' ? 'disabled' : ''); ?>>Hapus</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center">Data teknisi belum tersedia.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            <?php echo e($teknisis->appends(request()->query())->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/adminac/teknisi/index.blade.php ENDPATH**/ ?>