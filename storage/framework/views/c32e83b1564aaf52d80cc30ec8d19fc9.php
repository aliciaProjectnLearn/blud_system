<?php $__env->startSection('title', 'Manajemen Keuangan Servis'); ?>

<?php $__env->startPush('styles'); ?>
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Keuangan Servis Kendaraan</h1>
        <button class="d-none d-sm-inline-block btn btn-sm btn-danger shadow-sm" data-toggle="modal" data-target="#modalPengeluaran">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Pengeluaran
        </button>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    
    <div class="row">
        
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Pemasukan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp <?php echo e(number_format($totalPemasukan, 0, ',', '.')); ?>

                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Pengeluaran</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp <?php echo e(number_format($totalPengeluaran, 0, ',', '.')); ?>

                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Saldo Akhir</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp <?php echo e(number_format($saldoAkhir, 0, ',', '.')); ?>

                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Data</h6>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('adminservis.keuangan.index')); ?>" method="GET" class="form-inline">
                <div class="form-group mb-2 mr-sm-2">
                    <label for="start_date" class="mr-2">Mulai</label>
                    <input type="date" class="form-control" name="start_date" id="start_date" value="<?php echo e(request('start_date')); ?>">
                </div>
                <div class="form-group mb-2 mr-sm-2">
                    <label for="end_date" class="mr-2">Sampai</label>
                    <input type="date" class="form-control" name="end_date" id="end_date" value="<?php echo e(request('end_date')); ?>">
                </div>
                <div class="form-group mb-2 mr-sm-2">
                    <label for="tipe" class="mr-2">Tipe</label>
                    <select class="form-control" name="tipe" id="tipe">
                        <option value="">Semua</option>
                        <option value="pemasukan" <?php echo e(request('tipe') == 'pemasukan' ? 'selected' : ''); ?>>Pemasukan</option>
                        <option value="pengeluaran" <?php echo e(request('tipe') == 'pengeluaran' ? 'selected' : ''); ?>>Pengeluaran</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mb-2 mr-2"><i class="fas fa-filter"></i> Filter</button>
                <a href="<?php echo e(route('adminservis.keuangan.index')); ?>" class="btn btn-secondary mb-2"><i class="fas fa-sync"></i> Reset</a>
            </form>

            <?php if(request('start_date') && request('end_date')): ?>
                <div class="alert alert-info mt-3 mb-0">
                    <i class="fas fa-info-circle"></i> Menampilkan data untuk periode: <strong><?php echo e(\Carbon\Carbon::parse(request('start_date'))->translatedFormat('d F Y')); ?></strong> sampai <strong><?php echo e(\Carbon\Carbon::parse(request('end_date'))->translatedFormat('d F Y')); ?></strong>
                </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Transaksi (Pemasukan & Pengeluaran)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTableKeuangan" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="15%">Tanggal</th>
                            <th width="15%">Tipe</th>
                            <th width="45%">Deskripsi / Keterangan</th>
                            <th width="25%">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $transaksi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td data-sort="<?php echo e(\Carbon\Carbon::parse($item->tanggal)->format('Y-m-d H:i:s')); ?>">
                                    <?php echo e(\Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y')); ?>

                                </td>
                                <td>
                                    <?php if($item->tipe === 'pemasukan'): ?>
                                        <span class="badge badge-success px-2 py-1">Pemasukan</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger px-2 py-1">Pengeluaran</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($item->deskripsi); ?></td>
                                <td class="font-weight-bold <?php echo e($item->tipe === 'pemasukan' ? 'text-success' : 'text-danger'); ?>">
                                    <?php if($item->tipe === 'pemasukan'): ?> + <?php else: ?> - <?php endif; ?> 
                                    Rp <?php echo e(number_format($item->nominal, 0, ',', '.')); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="modalPengeluaran" tabindex="-1" role="dialog" aria-labelledby="modalPengeluaranTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="<?php echo e(route('adminservis.keuangan.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalPengeluaranTitle">Tambah Pengeluaran Baru</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="tanggal">Tanggal Pengeluaran</label>
                            <input type="date" class="form-control" name="tanggal" id="tanggal" required value="<?php echo e(date('Y-m-d')); ?>">
                        </div>
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi / Keterangan</label>
                            <input type="text" class="form-control" name="keterangan" id="deskripsi" required placeholder="Contoh: Beli bensin teknisi...">
                        </div>
                        <div class="form-group">
                            <label for="nominal">Nominal (Rp)</label>
                            <input type="number" class="form-control" name="jumlah" id="nominal" required min="0" placeholder="Contoh: 50000">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <button class="btn btn-danger" type="submit">Simpan Pengeluaran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#dataTableKeuangan').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json"
                },
                "order": [[ 0, "desc" ]], // Order by date descending by default
                "pageLength": 10
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/adminservis/keuangan/index.blade.php ENDPATH**/ ?>