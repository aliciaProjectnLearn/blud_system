<?php $__env->startSection('title', 'Detail Transaksi'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Transaksi</h1>
        <a href="<?php echo e(route('adminfutsal.transaksi.index')); ?>" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Pembayaran</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="30%"><strong>Kode Pembayaran</strong></td>
                            <td width="5%">:</td>
                            <td><strong class="text-primary"><?php echo e($transaksi->kode_pembayaran); ?></strong></td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Transaksi</strong></td>
                            <td>:</td>
                            <td><?php echo e(\Carbon\Carbon::parse($transaksi->created_at)->format('d F Y H:i')); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Jenis Transaksi</strong></td>
                            <td>:</td>
                            <td>
                                <?php if($transaksi->jenis_transaksi == 'membership'): ?>
                                    <span class="badge badge-info shadow-sm"><i class="fas fa-id-card"></i> Membership</span>
                                <?php elseif($transaksi->jenis_transaksi == 'event'): ?>
                                    <span class="badge badge-warning shadow-sm text-dark"><i class="fas fa-calendar-alt"></i> Event</span>
                                <?php elseif($transaksi->jenis_transaksi == 'guest'): ?>
                                    <span class="badge badge-secondary shadow-sm">Guest</span>
                                <?php else: ?>
                                    <span class="badge badge-primary shadow-sm"><i class="fas fa-calendar-check"></i> Booking</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Nama Pelanggan</strong></td>
                            <td>:</td>
                            <td><?php echo e($transaksi->booking->user->name ?? 'Guest/Unknown'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Nama Lapangan</strong></td>
                            <td>:</td>
                            <td><?php echo e($transaksi->booking->bookingFutsal->lapangan->nama ?? '-'); ?></td>
                        </tr>
                        
                        
                        <tr>
                            <td><strong>Jadwal Main</strong></td>
                            <td>:</td>
                            <td>
                                <?php if($transaksi->booking && $transaksi->booking->bookingFutsal): ?>
                                    <?php $bf = $transaksi->booking->bookingFutsal; ?>
                                    
                                    <?php if($bf->type === 'event'): ?>
                                        <span class="text-warning font-weight-bold">
                                            <?php echo e(\Carbon\Carbon::parse($bf->start_datetime)->format('d M Y')); ?> s/d <?php echo e(\Carbon\Carbon::parse($bf->end_datetime)->format('d M Y')); ?>

                                        </span>
                                        <br><small class="text-muted">(Multi-hari / Full Day)</small>
                                    <?php else: ?>
                                        <?php echo e(\Carbon\Carbon::parse($bf->start_datetime)->format('d M Y')); ?><br>
                                        <small class="text-muted">
                                            <i class="fas fa-clock"></i> Jam: <?php echo e(\Carbon\Carbon::parse($bf->start_datetime)->format('H:i')); ?> - <?php echo e(\Carbon\Carbon::parse($bf->end_datetime)->format('H:i')); ?> WIB
                                        </small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>

                        <tr>
                            <td><strong>Total Pembayaran</strong></td>
                            <td>:</td>
                            <td class="text-success" style="font-size: 1.1em;">
                                <strong>Rp <?php echo e(number_format($transaksi->jumlah_bayar, 0, ',', '.')); ?></strong>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Status</strong></td>
                            <td>:</td>
                            <td>
                                <?php if($transaksi->status == 'verifikasi'): ?>
                                    <span class="badge badge-primary">Verifikasi (Berhasil)</span>
                                <?php elseif($transaksi->status == 'menunggu'): ?>
                                    <span class="badge badge-warning text-dark">Menunggu</span>
                                <?php elseif($transaksi->status == 'dibatalkan'): ?>
                                    <span class="badge badge-danger">Dibatalkan</span>
                                <?php else: ?>
                                    <span class="badge badge-success"><?php echo e(ucfirst($transaksi->status)); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>

                    
                    <?php if($transaksi->status == 'menunggu'): ?>
                        <hr>
                        <form action="<?php echo e(route('adminfutsal.transaksi.konfirmasi', $transaksi->id)); ?>" method="POST" 
                            onsubmit="return confirm('Apakah Anda yakin ingin memverifikasi pembayaran ini?');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                            
                            <?php if($transaksi->jenis_transaksi !== 'membership'): ?>
                                <div class="form-group">
                                    <label for="jumlah_bayar"><strong>Nominal Pembayaran (Rp)</strong></label>
                                    <input type="number" name="jumlah_bayar" id="jumlah_bayar"
                                        class="form-control" placeholder="Contoh: 100000" min="1" 
                                        value="<?php echo e($transaksi->jumlah_bayar > 0 ? $transaksi->jumlah_bayar : ''); ?>"
                                        <?php echo e($transaksi->jenis_transaksi == 'event' ? 'readonly' : 'required'); ?>>
                                    
                                    <?php if($transaksi->jenis_transaksi == 'event'): ?>
                                        <small class="text-warning font-weight-bold mt-1 d-block">
                                            <i class="fas fa-lock"></i> Harga event sudah dikunci sistem.
                                        </small>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> 
                                    Harga membership akan otomatis diambil dari paket yang dipilih pelanggan.
                                </div>
                            <?php endif; ?>

                            <button type="submit" class="btn btn-success btn-block py-2">
                                <i class="fas fa-check-circle"></i> Konfirmasi Pembayaran Berhasil
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bukti Pembayaran</h6>
                </div>
                <div class="card-body text-center">
                    <?php if($transaksi->bukti): ?>
                        <img src="<?php echo e(asset('storage/' . $transaksi->bukti)); ?>" alt="Bukti Pembayaran"
                            class="img-fluid rounded border p-1 mb-3"
                            style="max-height: 400px; object-fit: contain; width: 100%;">
                        <div>
                            <a href="<?php echo e(asset('storage/' . $transaksi->bukti)); ?>" target="_blank"
                                class="btn btn-sm btn-outline-primary">Lihat Gambar Penuh</a>
                        </div>
                    <?php else: ?>
                        <div class="py-5 text-muted">
                            <i class="fas fa-images fa-4x mb-3 text-gray-300"></i>
                            <p>Belum ada bukti pembayaran yang diunggah.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/adminfutsal/transaksi/show.blade.php ENDPATH**/ ?>