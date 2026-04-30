<?php $__env->startSection('title', 'Manajemen Booking AC'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">

    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Booking AC</h1>
    </div>

    
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo e(session('error')); ?>

            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Booking AC</h6>

            
            <form method="GET" action="<?php echo e(route('admin.ac.booking.index')); ?>" class="form-inline">
                <select name="status" class="form-control form-control-sm mr-2">
                    <option value="">-- Semua Status --</option>
                    <?php $__currentLoopData = $statusList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($s); ?>" <?php echo e(request('status') == $s ? 'selected' : ''); ?>>
                            <?php echo e(ucfirst($s)); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <button type="submit" class="btn btn-sm btn-secondary">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <?php if(request()->filled('status')): ?>
                    <a href="<?php echo e(route('admin.ac.booking.index')); ?>" class="btn btn-sm btn-light ml-1">Reset</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal Booking</th>
                            <th>Pelanggan</th>
                            <th>Layanan</th>
                            <th>Tgl Kunjungan</th>
                            <th>Teknisi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($bookings->firstItem() + $i); ?></td>
                            <td><?php echo e(\Carbon\Carbon::parse($b->created_at)->format('d M Y H:i')); ?></td>
                            <td>
                                <?php echo e($b->user->name ?? '-'); ?><br>
                                <small class="text-muted"><?php echo e($b->user->no_hp ?? ''); ?></small>
                            </td>
                            <td><?php echo e($b->layanan->nama ?? '-'); ?></td>
                            <td><?php echo e(\Carbon\Carbon::parse($b->tgl_kunjungan)->format('d M Y')); ?></td>
                            <td>
                                <?php if($b->teknisi): ?>
                                    <?php echo e($b->teknisi->name); ?>

                                <?php else: ?>
                                    <span class="text-muted">Belum ditugaskan</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                    $badgeColor = match($b->status) {
                                        'menunggu' => 'warning',
                                        'proses'   => 'info',
                                        'selesai'  => 'success',
                                        default    => 'secondary',
                                    };
                                ?>
                                <span class="badge badge-<?php echo e($badgeColor); ?>"><?php echo e(ucfirst($b->status)); ?></span>
                            </td>
                            <td>
                                
                                <button type="button" class="btn btn-info btn-circle btn-sm" title="Detail"
                                    data-toggle="modal" data-target="#detailModal<?php echo e($b->id); ?>">
                                    <i class="fas fa-info-circle"></i>
                                </button>

                                
                                <?php if(in_array($b->status, ['menunggu', 'pending'])): ?>
                                    <button type="button" class="btn btn-success btn-circle btn-sm" title="Approve"
                                        data-toggle="modal" data-target="#approveModal<?php echo e($b->id); ?>">
                                        <i class="fas fa-check"></i>
                                    </button>
                                <?php endif; ?>

                                
                                <?php if($b->status === 'proses'): ?>
                                    <button type="button" class="btn btn-primary btn-circle btn-sm" title="Selesai"
                                        data-toggle="modal" data-target="#selesaiModal<?php echo e($b->id); ?>">
                                        <i class="fas fa-check-double"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>

                        
                        <div class="modal fade" id="detailModal<?php echo e($b->id); ?>" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-info text-white">
                                        <h5 class="modal-title"><i class="fas fa-info-circle"></i> Detail Booking</h5>
                                        <button class="close text-white" type="button" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-left">
                                        <p><strong>Pelanggan:</strong> <?php echo e($b->user->name ?? '-'); ?></p>
                                        <p><strong>No. HP:</strong> <?php echo e($b->user->no_hp ?? '-'); ?></p>
                                        <p><strong>Layanan:</strong> <?php echo e($b->layanan->nama ?? '-'); ?></p>
                                        <p><strong>Tgl Kunjungan:</strong> <?php echo e(\Carbon\Carbon::parse($b->tgl_kunjungan)->format('d F Y')); ?></p>
                                        <p><strong>Alamat:</strong> <?php echo e($b->alamat); ?></p>
                                        <p><strong>Merek AC:</strong> <?php echo e($b->merek_ac ?? '-'); ?></p>
                                        <p><strong>Keluhan:</strong> <?php echo e($b->detail_keluhan ?? '-'); ?></p>
                                        <p><strong>Teknisi:</strong> <?php echo e($b->teknisi->name ?? '-'); ?></p>
                                        <p><strong>Status:</strong>
                                            <span class="badge badge-<?php echo e($badgeColor); ?>"><?php echo e(ucfirst($b->status)); ?></span>
                                        </p>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <?php if(in_array($b->status, ['menunggu', 'pending'])): ?>
                        <div class="modal fade" id="approveModal<?php echo e($b->id); ?>" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title"><i class="fas fa-check"></i> Approve & Tugaskan Teknisi</h5>
                                        <button class="close text-white" type="button" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <form action="<?php echo e(route('admin.ac.booking.approve', $b->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <div class="modal-body text-left">
                                            <p><strong>Pelanggan:</strong> <?php echo e($b->user->name ?? '-'); ?></p>
                                            <p><strong>Layanan:</strong> <?php echo e($b->layanan->nama ?? '-'); ?></p>
                                            <p><strong>Tgl Kunjungan:</strong> <?php echo e(\Carbon\Carbon::parse($b->tgl_kunjungan)->format('d F Y')); ?></p>
                                            <hr>
                                            <?php if($teknisiTersedia->isEmpty()): ?>
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    Tidak ada teknisi tersedia. Booking tetap dalam antrian.
                                                </div>
                                            <?php else: ?>
                                                <div class="form-group">
                                                    <label><strong>Pilih Teknisi Tersedia</strong></label>
                                                    <select name="teknisi_id" class="form-control" required>
                                                        <option value="">-- Pilih Teknisi --</option>
                                                        <?php $__currentLoopData = $teknisiTersedia; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($t->id); ?>"><?php echo e($t->name); ?> — <?php echo e($t->no_hp); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                                            <?php if(!$teknisiTersedia->isEmpty()): ?>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-check"></i> Approve & Tugaskan
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        
                        <?php if($b->status === 'proses'): ?>
                        <div class="modal fade" id="selesaiModal<?php echo e($b->id); ?>" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title"><i class="fas fa-check-double"></i> Tandai Selesai</h5>
                                        <button class="close text-white" type="button" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-left">
                                        <p>Tandai booking ini sebagai selesai?</p>
                                        <p><strong>Pelanggan:</strong> <?php echo e($b->user->name ?? '-'); ?></p>
                                        <p><strong>Teknisi:</strong> <?php echo e($b->teknisi->name ?? '-'); ?></p>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                                        <form action="<?php echo e(route('admin.ac.booking.selesai', $b->id)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PATCH'); ?>
                                            <button type="submit" class="btn btn-primary">Ya, Selesai</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">Belum ada data booking.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php echo e($bookings->links()); ?>

        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/adminac/booking/index.blade.php ENDPATH**/ ?>