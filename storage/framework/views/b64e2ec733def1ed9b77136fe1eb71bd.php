<?php $__env->startSection('title', 'Form Pengajuan Sewa'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Form Pengajuan Sewa</h1>
        <a href="<?php echo e(route('user.kantin.katalog')); ?>" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Katalog
        </a>
    </div>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Info Unit -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Unit</h6>
                </div>
                <div class="card-body text-center">
                    <?php
                        // Ambil relasi foto pertama
                        $photo = $ruko->dokumentasiUnit->first();
                        // Ambil path-nya, atau null jika tidak ada relasi
                        $path = $photo->file ?? null;
                    ?>

                    <?php if($path): ?>
                        
                        
                        <img src="<?php echo e(asset('storage/' . str_replace('\\', '/', $path))); ?>" 
                             class="img-fluid rounded mb-3" 
                             alt="Foto Unit" 
                             style="width: 100%; object-fit: cover; height: 200px;">
                    <?php else: ?>
                        
                        
                        <img src="<?php echo e(asset('assets/img/no-image.png')); ?>" 
                             class="img-fluid rounded mb-3 shadow-sm" 
                             alt="Foto Tidak Tersedia" 
                             style="width: 100%; object-fit: cover; height: 200px;">
                    <?php endif; ?>
                    <h4 class="font-weight-bold"><?php echo e($ruko->kode_unit ?? ($ruko->no_unit ?? 'Unit')); ?></h4>
                    <p class="text-muted"><?php echo e($ruko->kategori->nama ?? '-'); ?></p>
                    <h3 class="text-success font-weight-bold">Rp <?php echo e(number_format($ruko->harga, 0, ',', '.')); ?><small class="text-muted text-sm">/tahun</small></h3>
                    <hr>
                    <div class="text-left small mb-0">
                        <p class="mb-1"><strong>Durasi Sewa Minimum:</strong> 1 Tahun</p>
                        <p class="mb-1 text-info"><i class="fas fa-info-circle"></i> Setelah diajukan, permohonan maksimal diproses 2x24 jam oleh Admin.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Pengajuan -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Profil & Dokumen</h6>
                </div>
                <div class="card-body">
                    <form id="formBookingUnit" action="<?php echo e(route('user.kantin.store_booking', $ruko->id)); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        
                        <h6 class="font-weight-bold mb-3">Informasi Pemohon</h6>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Nama Pemohon</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control bg-light" value="<?php echo e($user->name); ?>" readonly>
                            </div>
                        </div>

                        <!-- Jika belum jadi penyewa, minta NIK dan Nama Usaha -->
                        <?php if(!$penyewa): ?>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">NIK <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="nik" class="form-control <?php $__errorArgs = ['nik'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required placeholder="Masukkan NIK 16 digit" value="<?php echo e(old('nik', $user->nik)); ?>">
                                    <?php $__errorArgs = ['nik'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Nama Usaha/Toko <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="nama_usaha" class="form-control <?php $__errorArgs = ['nama_usaha'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required placeholder="Contoh: Kedai Kopi Maju" value="<?php echo e(old('nama_usaha')); ?>">
                                    <?php $__errorArgs = ['nama_usaha'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <textarea name="alamat" class="form-control <?php $__errorArgs = ['alamat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required placeholder="Masukkan Alamat Usaha/Tinggal"><?php echo e(old('alamat', $user->alamat)); ?></textarea>
                                    <?php $__errorArgs = ['alamat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">NIK</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control bg-light" value="<?php echo e($user->nik); ?>" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Nama Usaha</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control bg-light" value="<?php echo e($penyewa->nama_usaha); ?>" readonly>
                                </div>
                            </div>
                        <?php endif; ?>

                        <hr>
                        <h6 class="font-weight-bold mb-3">Rencana Sewa & Pembayaran</h6>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="date" name="tgl_mulai" class="form-control <?php $__errorArgs = ['tgl_mulai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required value="<?php echo e(old('tgl_mulai', date('Y-m-d'))); ?>" min="<?php echo e(date('Y-m-d')); ?>">
                                <small class="text-muted">Sewa berlaku selama 1 tahun sejak tanggal mulai.</small>
                                <?php $__errorArgs = ['tgl_mulai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div class="alert alert-info border-0 shadow-sm">
                            <div class="font-weight-bold mb-2 small"><i class="fas fa-calculator mr-1"></i> Estimasi Cicilan 2 Termin:</div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small">Termin 1 (Saat ini)</span>
                                <span class="font-weight-bold">Rp <?php echo e(number_format($ruko->harga / 2, 0, ',', '.')); ?></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="small">Termin 2 (Bulan ke-6)</span>
                                <span class="font-weight-bold text-gray-700">Rp <?php echo e(number_format($ruko->harga / 2, 0, ',', '.')); ?></span>
                            </div>
                        </div>

                        <hr>
                        <h6 class="font-weight-bold mb-3">Persyaratan Dokumen</h6>
                        
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Upload KTP <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <div class="custom-file">
                                    <input type="file" name="dokumen_ktp" class="custom-file-input" id="dokumen_ktp" required accept=".pdf,.jpg,.jpeg,.png">
                                    <label class="custom-file-label" for="dokumen_ktp">Pilih file (PDF/JPG/PNG max 2MB)...</label>
                                </div>
                                <?php $__errorArgs = ['dokumen_ktp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small mt-1"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <small class="text-muted">KTP digunakan untuk verifikasi Identitas dan MOU Penyewaan.</small>
                            </div>
                        </div>

                        <div class="form-group text-right mt-4">
                            <button type="button" id="btnAjukanSewa" class="btn btn-primary px-4 py-2">
                                <i class="fas fa-paper-plane mr-1"></i> Ajukan Permohonan Sewa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // To show file name on custom-file-input
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });

    // SweetAlert2 Confirmation
    document.getElementById('btnAjukanSewa')?.addEventListener('click', function(e) {
        e.preventDefault();

        // Validasi dasar HTML5 before SWAL
        const form = document.getElementById('formBookingUnit');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Pengajuan',
            text: 'Apakah Anda yakin ingin mengajukan permohonan sewa untuk unit ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#858796',
            confirmButtonText: '<i class="fas fa-check mr-1"></i> Ya, Ajukan',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/user/kantin/booking.blade.php ENDPATH**/ ?>