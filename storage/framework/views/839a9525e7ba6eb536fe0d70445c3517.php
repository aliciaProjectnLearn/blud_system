<?php $__env->startSection('title', 'Manajemen Keuangan AC'); ?>

<?php $__env->startSection('content'); ?>

    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Keuangan AC</h1>
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
            <form action="<?php echo e(route('admin.ac.keuangan.index')); ?>" method="GET" class="form-inline">
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
                <a href="<?php echo e(route('admin.ac.keuangan.index')); ?>" class="btn btn-secondary mb-2"><i class="fas fa-sync"></i> Reset</a>
            </form>
        </div>
    </div>

    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Transaksi (Pemasukan & Pengeluaran)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Kategori</th>
                            <th>Deskripsi / Keterangan</th>
                            <th>Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $transaksi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e(\Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y')); ?></td>
                                <td>
                                    <?php if($item->tipe === 'pemasukan'): ?>
                                        <span class="badge badge-success px-2 py-1">Pemasukan</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger px-2 py-1">Pengeluaran</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($item->tipe === 'pengeluaran'): ?>
                                        <span class="badge badge-secondary"><?php echo e(ucfirst($item->kategori)); ?></span>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($item->deskripsi); ?></td>
                                <td class="font-weight-bold <?php echo e($item->tipe === 'pemasukan' ? 'text-success' : 'text-danger'); ?>">
                                    <?php if($item->tipe === 'pemasukan'): ?> + <?php else: ?> - <?php endif; ?> 
                                    Rp <?php echo e(number_format($item->nominal, 0, ',', '.')); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada data transaksi.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-danger d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-users-cog"></i> Pembayaran Gaji Teknisi (Belum Dibayar)</h6>
            <form action="<?php echo e(route('admin.ac.keuangan.index')); ?>" method="GET" class="form-inline">
                <select name="teknisi_id" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                    <option value="">-- Semua Teknisi --</option>
                    <?php $__currentLoopData = $teknisis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teknisi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($teknisi->id); ?>" <?php echo e(request('teknisi_id') == $teknisi->id ? 'selected' : ''); ?>><?php echo e($teknisi->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <noscript><button type="submit" class="btn btn-sm btn-light">Filter</button></noscript>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Tgl Pekerjaan</th>
                            <th>Teknisi</th>
                            <th>Layanan / Pekerjaan</th>
                            <th>Status Pekerjaan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $pekerjaanBelumDibayar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pekerjaan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e(\Carbon\Carbon::parse($pekerjaan->updated_at)->format('d/m/Y H:i')); ?></td>
                            <td class="font-weight-bold"><?php echo e($pekerjaan->teknisi->name ?? 'Unknown'); ?></td>
                            <td><?php echo e($pekerjaan->layanan->nama ?? '-'); ?></td>
                            <td><span class="badge badge-success">Selesai</span></td>
                            <td>
                                <form action="<?php echo e(route('admin.ac.keuangan.bayarGaji', $pekerjaan->id)); ?>" method="POST" class="form-inline">
                                    <?php echo csrf_field(); ?>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="number" name="nominal_gaji" class="form-control" placeholder="Nominal Gaji" required min="1000">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary" onclick="return confirm('Bayar gaji teknisi ini dan catat pengeluaran?')">Bayar</button>
                                        </div>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">Tidak ada tagihan gaji teknisi yang tertunda.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="modalPengeluaran" tabindex="-1" role="dialog" aria-labelledby="modalPengeluaranTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="<?php echo e(route('admin.ac.keuangan.store')); ?>" method="POST">
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
                            <label for="kategori">Kategori</label>
                            <select class="form-control" name="kategori" id="kategori" required>
                                <option value="sparepart">Pembelian Sparepart</option>
                                <option value="gaji">Gaji / Upah</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi / Keterangan</label>
                            <input type="text" class="form-control" name="deskripsi" id="deskripsi" required placeholder="Contoh: Beli freon...">
                        </div>
                        <div class="form-group">
                            <label for="nominal">Nominal (Rp)</label>
                            <input type="number" class="form-control" name="nominal" id="nominal" required min="0" placeholder="Contoh: 50000">
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/adminac/keuangan/index.blade.php ENDPATH**/ ?>