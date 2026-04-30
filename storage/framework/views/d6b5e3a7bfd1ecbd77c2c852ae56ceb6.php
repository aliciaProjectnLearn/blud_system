<?php $__env->startSection('title', 'Detail Unit ' . $unit->kode_unit); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">

    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-eye mr-2 text-primary"></i>Detail Unit
            <span class="text-primary"><?php echo e($unit->kode_unit); ?></span>
        </h1>
        <div>
            <a href="<?php echo e(route('adminkantin.unit.edit', $unit->id)); ?>" class="btn btn-warning btn-sm shadow-sm mr-1">
                <i class="fas fa-pencil-alt mr-1"></i> Edit
            </a>
            <a href="<?php echo e(route('adminkantin.unit.index')); ?>" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">

        
        <div class="col-lg-5 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-primary">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-info-circle mr-1"></i> Informasi Unit
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                        <tbody>
                            <tr>
                                <td class="font-weight-bold text-gray-600" style="width:40%;">Kode Unit</td>
                                <td>
                                    <span class="badge badge-primary px-2 py-1" style="font-size:.9rem;">
                                        <?php echo e($unit->kode_unit ?? '-'); ?>

                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-gray-600">Kategori</td>
                                <td><?php echo e($unit->kategori->nama ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-gray-600">Harga Sewa</td>
                                <td>
                                    <?php if($unit->kategori && $unit->kategori->harga): ?>
                                        <strong>Rp <?php echo e(number_format($unit->kategori->harga, 0, ',', '.')); ?></strong>
                                        <small class="text-muted">/bulan</small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-gray-600">Status</td>
                                <td>
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
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-gray-600">Dibuat</td>
                                <td><?php echo e($unit->created_at->format('d M Y, H:i')); ?></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-gray-600">Diperbarui</td>
                                <td><?php echo e($unit->updated_at->format('d M Y, H:i')); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        
        <div class="col-lg-7 mb-4">

            
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history mr-1"></i> Riwayat Sewa
                    </h6>
                    <span class="badge badge-primary"><?php echo e($unit->sewaRuko->count()); ?> transaksi</span>
                </div>
                <div class="card-body p-0">
                    <?php if($unit->sewaRuko->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Penyewa</th>
                                        <th>Mulai</th>
                                        <th>Selesai</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $unit->sewaRuko->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sewa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($sewa->penyewa->name ?? '-'); ?></td>
                                            <td><?php echo e($sewa->tgl_mulai ? \Carbon\Carbon::parse($sewa->tgl_mulai)->format('d M Y') : '-'); ?></td>
                                            <td><?php echo e($sewa->tgl_selesai ? \Carbon\Carbon::parse($sewa->tgl_selesai)->format('d M Y') : '-'); ?></td>
                                            <td class="text-center">
                                                <?php
                                                    $s = $sewa->status ?? '';
                                                    $badgeMap = [
                                                        'disetujui'  => 'success',
                                                        'menunggu'   => 'warning',
                                                        'dibatalkan' => 'danger',
                                                        'selesai'    => 'secondary',
                                                    ];
                                                    $color = $badgeMap[$s] ?? 'light';
                                                ?>
                                                <span class="badge badge-<?php echo e($color); ?>">
                                                    <?php echo e(ucfirst($s ?: '-')); ?>

                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if($unit->sewaRuko->count() > 5): ?>
                            <div class="text-center py-2 small text-muted">
                                +<?php echo e($unit->sewaRuko->count() - 5); ?> transaksi lainnya
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted small">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            Belum ada riwayat sewa untuk unit ini.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-alt mr-1"></i> Dokumentasi Unit
                    </h6>
                    <span class="badge badge-info"><?php echo e($unit->dokumentasiUnit->count()); ?> file</span>
                </div>
                <div class="card-body p-0">
                    <?php if($unit->dokumentasiUnit->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0" id="tabel-dokumentasi">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Preview</th>
                                        <th>Nama Dokumen</th>
                                        <th>Tgl Upload</th>
                                        <th>Tipe</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $unit->dokumentasiUnit; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $dok): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $ext     = strtolower(pathinfo($dok->file, PATHINFO_EXTENSION));
                                            $isImage = in_array($ext, ['jpg','jpeg','png']);
                                            $judul   = $dok->judul_dokumen ?: $dok->tipe;
                                        ?>
                                        <tr id="row-dok-<?php echo e($dok->id); ?>">
                                            <td class="text-muted align-middle"><?php echo e($i + 1); ?></td>
                                            <td class="align-middle">
                                                <a href="<?php echo e(asset('storage/' . $dok->file)); ?>" target="_blank">
                                                    <?php if($isImage): ?>
                                                        <img src="<?php echo e(asset('storage/' . $dok->file)); ?>"
                                                             alt="preview"
                                                             class="img-thumbnail"
                                                             style="width:48px; height:48px; object-fit:cover;">
                                                    <?php else: ?>
                                                        <i class="fas fa-file-pdf text-danger fa-2x"></i>
                                                    <?php endif; ?>
                                                </a>
                                            </td>
                                            <td class="align-middle">
                                                <span id="judul-dok-<?php echo e($dok->id); ?>" class="font-weight-bold"><?php echo e($dok->judul_dokumen); ?></span>
                                            </td>
                                            <td class="align-middle small">
                                                <?php echo e($dok->created_at->format('d/m/Y')); ?>

                                            </td>
                                            <td class="align-middle">
                                                <span id="tipe-dok-<?php echo e($dok->id); ?>" class="badge badge-light"><?php echo e($dok->tipe); ?></span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span id="btn-lihat-wrapper-<?php echo e($dok->id); ?>">
                                                    <?php if(!empty($dok->deskripsi)): ?>
                                                        <button type="button" 
                                                                class="btn btn-info btn-sm btn-lihat-deskripsi" 
                                                                data-deskripsi="<?php echo e($dok->deskripsi); ?>" 
                                                                title="Lihat Deskripsi">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </span>
                                                <button type="button"
                                                        class="btn btn-warning btn-sm btn-edit-dok"
                                                        title="Edit detail dokumen"
                                                        data-id="<?php echo e($dok->id); ?>"
                                                        data-judul="<?php echo e($dok->judul_dokumen); ?>"
                                                        data-deskripsi="<?php echo e($dok->deskripsi); ?>"
                                                        data-tipe="<?php echo e($dok->tipe); ?>"
                                                        data-url="<?php echo e(route('adminkantin.unit.dokumen.updateDetail', $dok->id)); ?>">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                                <a href="<?php echo e(asset('storage/' . $dok->file)); ?>"
                                                   target="_blank"
                                                   class="btn btn-sm btn-outline-info"
                                                   title="Lihat file">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-3 text-muted small">
                            <i class="fas fa-folder-open fa-2x mb-2 d-block"></i>
                            Belum ada dokumentasi.
                            <a href="<?php echo e(route('adminkantin.unit.edit', $unit->id)); ?>">Upload sekarang</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

</div>




<div class="modal fade" id="modalEditDokDetail" tabindex="-1" role="dialog" aria-labelledby="modalEditDokDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-white font-weight-bold" id="modalEditDokDetailLabel">
                    <i class="fas fa-pencil-alt mr-2"></i>Edit Detail Dokumen
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit-dok-id">
                <input type="hidden" id="edit-dok-url">
                
                <div class="form-group">
                    <label for="edit-judul-dokumen" class="font-weight-bold">Nama Dokumen <span class="text-danger">*</span></label>
                    <input type="text"
                           id="edit-judul-dokumen"
                           class="form-control"
                           maxlength="150"
                           placeholder="cth: Denah Unit, Kwitansi">
                </div>

                <div class="form-group">
                    <label for="edit-deskripsi-dokumen" class="font-weight-bold">Deskripsi</label>
                    <textarea id="edit-deskripsi-dokumen" 
                              class="form-control" 
                              rows="3" 
                              placeholder="Tambahkan keterangan dokumen jika perlu..."></textarea>
                </div>

                <div class="form-group mb-0">
                    <label class="font-weight-bold d-block">Tipe File</label>
                    <span id="preview-tipe-view" class="badge badge-secondary p-2"></span>
                    <small class="text-muted d-block mt-1">Tipe diatur otomatis oleh sistem berdasarkan file asli.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Batal
                </button>
                <button type="button" class="btn btn-warning" id="btn-simpan-dok-detail">
                    <i class="fas fa-save mr-1"></i>Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function () {

    // ── 1. Tampilkan modal saat tombol edit diklik ───────────────────
    $(document).on('click', '.btn-edit-dok', function () {
        var id    = $(this).data('id');
        var judul = $(this).data('judul');
        var desk  = $(this).data('deskripsi');
        var tipe  = $(this).data('tipe');
        var url   = $(this).data('url');

        $('#edit-dok-id').val(id);
        $('#edit-dok-url').val(url);
        $('#edit-judul-dokumen').val(judul);
        $('#edit-deskripsi-dokumen').val(desk);
        $('#preview-tipe-view').text(tipe);

        $('#modalEditDokDetail').modal('show');
    });

    // ── 2. Submit form modal via AJAX ───────────────────────────────
    $('#btn-simpan-dok-detail').on('click', function () {
        var url   = $('#edit-dok-url').val();
        var id    = $('#edit-dok-id').val();
        var judul = $.trim($('#edit-judul-dokumen').val());
        var desk  = $.trim($('#edit-deskripsi-dokumen').val());

        if (judul === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Nama dokumen wajib diisi.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            return;
        }

        // Disable tombol saat request berlangsung
        $('#btn-simpan-dok-detail').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan...');

        $.ajax({
            url     : url,
            method  : 'POST',
            data    : {
                _method         : 'PATCH',
                _token          : '<?php echo e(csrf_token()); ?>',
                judul_dokumen   : judul,
                deskripsi       : desk
            },
            success : function (resp) {
                if (resp.success) {
                    // Update teks di tabel tanpa reload halaman
                    $('#judul-dok-' + id).text(resp.judul_dokumen);
                    
                    // Update tampilan tombol lihat deskripsi (tampil hanya jika ada deskripsi)
                    var wrapper = $('#btn-lihat-wrapper-' + id);
                    if (resp.deskripsi && resp.deskripsi !== '-') {
                        wrapper.html('<button type="button" class="btn btn-info btn-sm btn-lihat-deskripsi" data-deskripsi="' + resp.deskripsi + '" title="Lihat Deskripsi"><i class="fas fa-eye"></i></button>');
                    } else {
                        wrapper.empty();
                    }

                    // Update data attribute tombol edit agar sinkron
                    var $btn = $('.btn-edit-dok[data-id="' + id + '"]');
                    $btn.data('judul', resp.judul_dokumen);
                    $btn.data('deskripsi', resp.deskripsi !== '-' ? resp.deskripsi : null);

                    $('#modalEditDokDetail').modal('hide');

                    Swal.fire({
                        icon              : 'success',
                        title             : 'Berhasil!',
                        text              : resp.message,
                        toast             : true,
                        position          : 'top-end',
                        showConfirmButton : false,
                        timer             : 3000,
                        timerProgressBar  : true
                    });
                }
            },
            error   : function (xhr) {
                var msg = 'Terjadi kesalahan. Silakan coba lagi.';
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    var firstKey = Object.keys(errors)[0];
                    msg = errors[firstKey][0];
                }

                Swal.fire({
                    icon              : 'error',
                    title             : 'Gagal!',
                    text              : msg,
                    toast             : true,
                    position          : 'top-end',
                    showConfirmButton : false,
                    timer             : 4000
                });
            },
            complete: function () {
                $('#btn-simpan-dok-detail').prop('disabled', false).html('<i class="fas fa-save mr-1"></i>Simpan Perubahan');
            }
        });
    });

    // ── 3. Reset nilai input saat modal ditutup ──────────────────────
    $('#modalEditDokDetail').on('hidden.bs.modal', function () {
        $('#edit-judul-dokumen').val('');
        $('#edit-deskripsi-dokumen').val('');
        $('#edit-dok-id').val('');
        $('#edit-dok-url').val('');
    });

    // ── 4. Tampilkan Detail Deskripsi via Swal diklik ───────────────────
    $(document).on('click', '.btn-lihat-deskripsi', function () {
        var desk = $(this).data('deskripsi');
        
        Swal.fire({
            icon     : 'info',
            title    : 'Deskripsi Dokumen',
            html     : '<div class="text-left">' + desk.replace(/\n/g, "<br>") + '</div>',
            confirmButtonText: 'Tutup',
            customClass: {
                confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false
        });
    });

});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/adminkantin/unit/show.blade.php ENDPATH**/ ?>