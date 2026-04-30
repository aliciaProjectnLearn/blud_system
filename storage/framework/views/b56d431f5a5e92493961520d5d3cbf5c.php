<?php $__env->startSection('title', 'Manajemen Keuangan Futsal'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Keuangan Futsal</h1>
    </div>

    <!-- Alert Success -->
    <!-- <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?> -->

    <!-- Alert Error (Validation) -->
    <!-- <?php if($errors->any()): ?>
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
    <?php endif; ?> -->

    <!-- Bagian Atas: 3 Card Ringkasan -->
    <div class="row">
        <!-- Card Total Pemasukan -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Pemasukan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?php echo e(number_format($totalPemasukan, 0, ',', '.')); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Total Pengeluaran -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Pengeluaran</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?php echo e(number_format($totalPengeluaran, 0, ',', '.')); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Saldo Akhir -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Saldo Akhir</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?php echo e(number_format($saldoAkhir, 0, ',', '.')); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bagian Tengah: Filter dan Tombol Tambah Pengeluaran -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Filter Transaksi</h6>
                    <button type="button" class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#modalTambahPengeluaran">
                        <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Tambah Pengeluaran
                    </button>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('adminfutsal.keuangan.index')); ?>" method="GET">
                        <div class="form-row align-items-end">
                            <div class="col-md-3 mb-3">
                                <label for="tgl_mulai">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="tgl_mulai" name="tgl_mulai" value="<?php echo e(request('tgl_mulai')); ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="tgl_akhir">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="tgl_akhir" name="tgl_akhir" value="<?php echo e(request('tgl_akhir')); ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="tipe_transaksi">Tipe Transaksi</label>
                                <select class="form-control" id="tipe_transaksi" name="tipe_transaksi">
                                    <option value="">-- Semua Transaksi --</option>
                                    <option value="Pemasukan" <?php echo e(request('tipe_transaksi') == 'Pemasukan' ? 'selected' : ''); ?>>Pemasukan</option>
                                    <option value="Pengeluaran" <?php echo e(request('tipe_transaksi') == 'Pengeluaran' ? 'selected' : ''); ?>>Pengeluaran</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                                <a href="<?php echo e(route('adminfutsal.keuangan.index')); ?>" class="btn btn-secondary"><i class="fas fa-sync"></i> Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bagian Bawah: DataTables Transaksi Gabungan -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Transaksi Keuangan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Kode Transaksi</th>
                            <th>Tipe</th>
                            <th>Deskripsi</th>
                            <th>Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $transaksiGabungan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $transaksi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($index + 1); ?></td>
                                <td><?php echo e(\Carbon\Carbon::parse($transaksi['tanggal_transaksi'])->format('d M Y')); ?></td>
                                <td><?php echo e($transaksi['kode_transaksi']); ?></td>
                                <td>
                                    <!-- Badge berdasarkan Tipe Transaksi -->
                                    <?php if($transaksi['tipe_transaksi'] == 'Pemasukan'): ?>
                                        <span class="badge badge-success">Pemasukan</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Pengeluaran</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($transaksi['deskripsi']); ?></td>
                                <td>
                                    <!-- Warna text nominal sesuai tipe -->
                                    <?php if($transaksi['tipe_transaksi'] == 'Pemasukan'): ?>
                                        <span class="text-success">+ Rp <?php echo e(number_format($transaksi['nominal'], 0, ',', '.')); ?></span>
                                    <?php else: ?>
                                        <span class="text-danger">- Rp <?php echo e(number_format($transaksi['nominal'], 0, ',', '.')); ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Modal Tambah Pengeluaran -->
<div class="modal fade" id="modalTambahPengeluaran" tabindex="-1" role="dialog" aria-labelledby="modalTambahPengeluaranLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="<?php echo e(route('adminfutsal.keuangan.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahPengeluaranLabel">Tambah Pengeluaran Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="tgl_pengeluaran">Tanggal Pengeluaran <span class="text-danger">*</span></label>
                        <input type="date" class="form-control <?php $__errorArgs = ['tgl_pengeluaran'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="tgl_pengeluaran" name="tgl_pengeluaran" value="<?php echo e(old('tgl_pengeluaran', date('Y-m-d'))); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="nominal">Nominal (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control <?php $__errorArgs = ['nominal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="nominal" name="nominal" placeholder="Contoh: 50000" value="<?php echo e(old('nominal')); ?>" required min="1">
                    </div>
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi Pengeluaran <span class="text-danger">*</span></label>
                        <textarea class="form-control <?php $__errorArgs = ['deskripsi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="deskripsi" name="deskripsi" rows="3" placeholder="Contoh: Beli bola futsal baru" required><?php echo e(old('deskripsi')); ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Pengeluaran</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Inisialisasi DataTables
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/adminfutsal/keuangan/index.blade.php ENDPATH**/ ?>