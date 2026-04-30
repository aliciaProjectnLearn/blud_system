<?php $__env->startSection('content'); ?>
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Pembayaran</h1>
        <a href="<?php echo e(route('adminkantin.pembayaran.index')); ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo e(session('success')); ?> <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo e(session('error')); ?> <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <div class="row">

        
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Data Penyewa</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td width="40%">Nama Usaha</td>
                            <td>: <strong><?php echo e($pembayaran->sewaRuko->penyewa->nama_usaha ?? '-'); ?></strong></td>
                        </tr>
                        <tr>
                            <td>Nama Pemilik</td>
                            <td>: <?php echo e($pembayaran->sewaRuko->penyewa->user->name ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <td>NIK</td>
                            <td>: <?php echo e($pembayaran->sewaRuko->penyewa->user->nik ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td>: <?php echo e($pembayaran->sewaRuko->penyewa->alamat ?? '-'); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Data Sewa</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td width="40%">Kode Unit</td>
                            <td>: <strong><?php echo e($pembayaran->sewaRuko->ruko->kode_unit ?? '-'); ?></strong></td>
                        </tr>
                        <tr>
                            <td>Kategori</td>
                            <td>: <?php echo e($pembayaran->sewaRuko->ruko->kategori->nama ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <td>Periode Sewa</td>
                            <td>:
                                <?php echo e(\Carbon\Carbon::parse($pembayaran->sewaRuko->tgl_mulai)->format('d M Y')); ?>

                                s/d
                                <?php echo e(\Carbon\Carbon::parse($pembayaran->sewaRuko->tgl_selesai)->format('d M Y')); ?>

                            </td>
                        </tr>
                        <tr>
                            <td>No. MOU</td>
                            <td>: <?php echo e($pembayaran->sewaRuko->dokumen->first()?->no_mou ?? '-'); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

    </div>

    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Detail Pembayaran — Termin <?php echo e($pembayaran->termin); ?></h6>
            <?php if($pembayaran->status === 'lunas'): ?>
                <a href="<?php echo e(route('adminkantin.pembayaran.kwitansi', $pembayaran)); ?>"
                    class="btn btn-success btn-sm">
                    <i class="fas fa-download mr-1"></i> Download Kwitansi
                </a>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td width="40%">No. Kwitansi</td>
                            <td>: <?php echo e($pembayaran->no_kwitansi ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <td>Jumlah Tagihan</td>
                            <td>: <strong>Rp <?php echo e(number_format($pembayaran->jumlah_tagihan, 0, ',', '.')); ?></strong></td>
                        </tr>
                        <tr>
                            <td>Jatuh Tempo</td>
                            <td>:
                                <?php echo e($pembayaran->tgl_jatuh_tempo
                                    ? \Carbon\Carbon::parse($pembayaran->tgl_jatuh_tempo)->format('d M Y')
                                    : '-'); ?>

                                <?php if($pembayaran->isTerlambat()): ?>
                                    <span class="badge badge-danger ml-1">Terlambat</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Tgl Bayar</td>
                            <td>:
                                <?php echo e($pembayaran->tgl_bayar
                                    ? \Carbon\Carbon::parse($pembayaran->tgl_bayar)->format('d M Y')
                                    : '-'); ?>

                            </td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>:
                                <?php if($pembayaran->status === 'lunas'): ?>
                                    <span class="badge badge-success">Lunas</span>
                                <?php elseif($pembayaran->status === 'verifikasi'): ?>
                                    <span class="badge badge-info">Menunggu Verifikasi</span>
                                <?php elseif($pembayaran->status === 'menunggu'): ?>
                                    <span class="badge badge-warning">Menunggu</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Dibatalkan</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php if($pembayaran->path_bukti): ?>
                        <tr>
                            <td>Bukti Bayar</td>
                            <td>: 
                                <a href="<?php echo e(asset('storage/' . $pembayaran->path_bukti)); ?>" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-2">
                                    <i class="fas fa-eye mr-1"></i> Lihat Bukti
                                </a>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

           
            <?php if($pembayaran->status === 'lunas'): ?>
            <hr>
            <div class="alert alert-success">
                <i class="fas fa-check-circle mr-1"></i>
                Pembayaran ini sudah <strong>Lunas</strong>. No. Kwitansi: <strong><?php echo e($pembayaran->no_kwitansi); ?></strong>
            </div>

            <?php elseif($pembayaran->status === 'verifikasi'): ?>
            <hr>
            <div class="alert alert-info mb-3">
                <i class="fas fa-clock mr-1"></i>
                <?php if($pembayaran->tipe_pembayaran_id == 2): ?>
                    Konfirmasi bahwa penyewa telah melakukan pembayaran secara tunai, dan pastikan uang yang diterima sesuai dengan nominal tagihan.
                <?php else: ?>
                    Penyewa sudah mengupload bukti pembayaran. Silakan verifikasi dan konfirmasi sebagai lunas.
                <?php endif; ?>
            </div>
            <h6 class="font-weight-bold text-gray-700 mb-3">Konfirmasi Pembayaran</h6>
            <form id="formKonfirmasiPembayaran" action="<?php echo e(route('adminkantin.pembayaran.update', $pembayaran)); ?>" method="POST">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tanggal Bayar <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_bayar" class="form-control <?php $__errorArgs = ['tgl_bayar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                value="<?php echo e(old('tgl_bayar', now()->format('Y-m-d'))); ?>" required>
                            <?php $__errorArgs = ['tgl_bayar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tipe Pembayaran <span class="text-danger">*</span></label>
                            <select name="tipe_pembayaran_id" class="form-control <?php $__errorArgs = ['tipe_pembayaran_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="">-- Pilih Tipe --</option>
                                <?php $__currentLoopData = \App\Models\TipePembayaran::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($tipe->id); ?>" <?php echo e(old('tipe_pembayaran_id', $pembayaran->tipe_pembayaran_id) == $tipe->id ? 'selected' : ''); ?>>
                                        <?php echo e($tipe->nama); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['tipe_pembayaran_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-group w-100">
                            <button type="button" id="btnKonfirmasiLunas" class="btn btn-primary btn-block">
                                <i class="fas fa-check mr-1"></i> Konfirmasi Lunas
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <?php elseif($pembayaran->status === 'menunggu'): ?>
            <hr>
            <div class="alert alert-warning">
                <i class="fas fa-hourglass-half mr-1"></i>
                Menunggu penyewa mengupload bukti pembayaran.
            </div>
            <?php endif; ?>
        </div>
    </div>

</div>
<?php $__env->startPush('scripts'); ?>
<script>
document.getElementById('btnKonfirmasiLunas')
    ?.addEventListener('click', function(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Konfirmasi Pembayaran',
        html: 'Tandai pembayaran ini sebagai <strong>LUNAS</strong>?<br><small class="text-muted">Kwitansi akan digenerate otomatis.</small>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4e73df',
        cancelButtonColor: '#858796',
        confirmButtonText: '<i class="fas fa-check mr-1"></i> Ya, Konfirmasi',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formKonfirmasiPembayaran').submit();
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/adminkantin/pembayaran/show.blade.php ENDPATH**/ ?>