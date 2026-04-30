<?php $__env->startSection('title', 'Riwayat Pembayaran'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Riwayat Pembayaran</h1>
        <a href="<?php echo e(route('user.kantin.dashboard')); ?>" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Status Pengajuan Sewa</h6>
        </div>
        <div class="card-body">
            <?php if(isset($pengajuan) && $pengajuan->isEmpty()): ?>
                <div class="text-center py-4">
                    <p class="text-muted mb-0">Belum ada pengajuan sewa.</p>
                </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="bg-light">
                        <tr>
                            <th>Unit</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Durasi Sewa</th>
                            <th>Status Pengajuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $pengajuan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $badgeP = match($p->status) {
                                'aktif'      => 'success',
                                'pending'    => 'warning',
                                'ditolak'    => 'danger',
                                'selesai'    => 'secondary',
                                'dibatalkan' => 'dark',
                                default      => 'secondary',
                            };
                        ?>
                        <tr>
                            <td><?php echo e($p->ruko->kode_unit ?? '-'); ?></td>
                            <td><?php echo e(\Carbon\Carbon::parse($p->created_at)->format('d M Y')); ?></td>
                            <td><?php echo e(\Carbon\Carbon::parse($p->tgl_mulai)->format('d M Y')); ?> s.d <?php echo e(\Carbon\Carbon::parse($p->tgl_selesai)->format('d M Y')); ?></td>
                            <td><span class="badge badge-<?php echo e($badgeP); ?>"><?php echo e(ucfirst($p->status)); ?></span></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#modalDetailPengajuan-<?php echo e($p->id); ?>">
                                    <i class="fas fa-eye"></i> Detail
                                </button>
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
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Semua Riwayat Pembayaran</h6>
        </div>
        <div class="card-body">
            <?php if($riwayat->isEmpty()): ?>
                <div class="text-center py-5">
                    <i class="fas fa-receipt fa-4x text-gray-300 mb-3"></i>
                    <p class="text-muted">Belum ada riwayat pembayaran.</p>
                </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Unit</th>
                            <th>Termin</th>
                            <th>Jumlah Tagihan</th>
                            <th>Tanggal Bayar</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $riwayat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $badge = match($r->status) {
                                'lunas'      => 'success',
                                'verifikasi' => 'info',
                                'menunggu'   => 'warning',
                                default      => 'secondary',
                            };
                        ?>
                        <tr>
                            <td><?php echo e($r->sewaRuko->ruko->kode_unit ?? '-'); ?></td>
                            <td>Termin <?php echo e($r->termin); ?></td>
                            <td>Rp <?php echo e(number_format($r->jumlah_tagihan, 0, ',', '.')); ?></td>
                            <td><?php echo e($r->tgl_bayar ? \Carbon\Carbon::parse($r->tgl_bayar)->format('d M Y') : '-'); ?></td>
                            <td><span class="badge badge-<?php echo e($badge); ?>"><?php echo e(ucfirst($r->status)); ?></span></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>


<?php if(isset($pengajuan) && $pengajuan->isNotEmpty()): ?>
    <?php $__currentLoopData = $pengajuan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="modal fade" id="modalDetailPengajuan-<?php echo e($p->id); ?>" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-file-contract mr-2"></i>Detail Pengajuan Sewa: <?php echo e($p->ruko->kode_unit ?? '-'); ?></h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="font-weight-bold text-primary border-bottom pb-2">Informasi Penyewa</h6>
                            <p class="mb-1 text-sm"><strong>Nama Pemohon:</strong> <?php echo e(auth()->user()->nama_lengkap ?? auth()->user()->name); ?></p>
                            <p class="mb-1 text-sm"><strong>NIK:</strong> <?php echo e(auth()->user()->nik ?? '-'); ?></p>
                            <p class="mb-1 text-sm"><strong>Nama Usaha:</strong> <?php echo e($p->penyewa->nama_usaha ?? '-'); ?></p>
                            <p class="mb-1 text-sm"><strong>Alamat:</strong> <?php echo e($p->penyewa->alamat ?? '-'); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="font-weight-bold text-primary border-bottom pb-2">Detail Unit Ruko</h6>
                            <p class="mb-1 text-sm"><strong>Kode:</strong> <?php echo e($p->ruko->kode_unit ?? '-'); ?></p>
                            <p class="mb-1 text-sm"><strong>Kategori:</strong> <?php echo e($p->ruko->kategori->nama ?? '-'); ?></p>
                            <p class="mb-1 text-sm"><strong>Harga Sewa:</strong> Rp <?php echo e(number_format($p->harga_sewa_tahunan, 0, ',', '.')); ?> / Tahun</p>
                            <p class="mb-1 text-sm"><strong>Status Saat Ini:</strong> <span class="badge badge-<?php echo e(match($p->status) { 'aktif' => 'success', 'pending' => 'warning', 'ditolak' => 'danger', 'selesai' => 'secondary', 'dibatalkan' => 'dark', default => 'secondary' }); ?>"><?php echo e(ucfirst($p->status)); ?></span></p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="font-weight-bold text-primary border-bottom pb-2">Dokumen Terlampir</h6>
                            <?php if($p->dokumen && $p->dokumen->count() > 0): ?>
                                <ul class="list-group list-group-flush small">
                                    <?php $__currentLoopData = $p->dokumen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                                            <div>
                                                <i class="fas fa-paperclip text-muted mr-1"></i> 
                                                <strong><?php echo e($doc->nama_dokumen); ?></strong><br>
                                                <span class="text-muted" style="font-size:0.8rem">No MOU: <?php echo e($doc->no_mou ?? '-'); ?></span>
                                            </div>
                                            <span class="badge badge-light border"><i class="fas fa-check text-success"></i> Tersimpan</span>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted text-sm mb-0">Belum ada dokumen yang dilampirkan atau digenerate oleh sistem.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/user/kantin/riwayat.blade.php ENDPATH**/ ?>