<?php $__env->startSection('title', 'Invoice ' . $transaksi->invoice_no); ?>

<?php $__env->startSection('content'); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4 no-print">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-file-invoice mr-2 text-primary"></i>Detail Transaksi
    </h1>
    <div>
        <a href="<?php echo e(route('adminac.transaksi.index')); ?>" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali
        </a>
        <button onclick="window.print()" class="btn btn-outline-primary btn-sm shadow-sm ml-2">
            <i class="fas fa-print fa-sm mr-1"></i> Print / Download PDF
        </button>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        
        
        <div class="card shadow mb-4 no-print border-left-info">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="m-0 font-weight-bold text-info">
                            <i class="fas fa-edit mr-1"></i> Update Status Pembayaran
                        </h6>
                        <small class="text-muted">Kelola status pembayaran dari pelanggan.</small>
                    </div>
                    <div class="col-md-6">
                        <form action="<?php echo e(route('adminac.transaksi.update_status', $transaksi->id)); ?>" method="POST" class="form-inline justify-content-end">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                            <select name="status" class="form-control form-control-sm mr-2 py-0" style="height: 31px;">
                                <option value="pending" <?php echo e($transaksi->status == 'pending' ? 'selected' : ''); ?>>Pending</option>
                                <option value="dibayar" <?php echo e($transaksi->status == 'dibayar' ? 'selected' : ''); ?>>Dibayar</option>
                                <option value="ditolak" <?php echo e($transaksi->status == 'ditolak' ? 'selected' : ''); ?>>Ditolak</option>
                            </select>
                            <button type="submit" class="btn btn-info btn-sm">Update Status</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="card shadow mb-4 border-0 invoice-card">
            <div class="card-body p-5 p-sm-5">
                
                <div class="row mb-5 mb-sm-5">
                    <div class="col-sm-6">
                        <div class="mb-5 mb-sm-5 d-flex align-items-center">
                            <div class="bg-primary text-white p-3 rounded mr-3 shadow">
                                <i class="fas fa-snowflake fa-2x"></i>
                            </div>
                            <div>
                                <h4 class="font-weight-bold text-dark mb-0">BLUD AC SERVICE</h4>
                                <small class="text-muted text-uppercase tracking-wider">Layanan Perbaikan & Perawatan AC</small>
                            </div>
                        </div>
                        <div class="mb-4 mb-sm-0">
                            <h6 class="font-weight-bold text-gray-800 text-uppercase mb-2">Ditujukan Kepada:</h6>
                            <p class="text-dark mb-1 font-weight-bold"><?php echo e($transaksi->bookingAc->user->name ?? 'Pelanggan Umum'); ?></p>
                            <p class="text-muted text-sm mb-0">Email: <?php echo e($transaksi->bookingAc->user->email ?? '-'); ?></p>
                            <p class="text-muted text-sm mb-0">Alamat: <?php echo e($transaksi->bookingAc->alamat ?? '-'); ?></p>
                        </div>
                    </div>
                    <div class="col-sm-6 text-sm-right mt-4 mt-sm-0">
                        <h2 class="font-weight-bold text-primary mb-3">INVOICE</h2>
                        <div class="mb-3">
                            <div class="h6 font-weight-bold text-gray-800 text-uppercase mb-1">Nomor:</div>
                            <div class="h5 font-weight-bold text-dark border-bottom border-primary d-inline-block"><?php echo e($transaksi->invoice_no); ?></div>
                        </div>
                        <div class="row no-gutters justify-content-end mb-1">
                            <div class="col-7 col-sm-auto px-2 text-muted">Tanggal:</div>
                            <div class="col-5 col-sm-auto px-2 font-weight-bold text-dark"><?php echo e($transaksi->created_at->translatedFormat('d M Y')); ?></div>
                        </div>
                        <div class="row no-gutters justify-content-end">
                            <div class="col-7 col-sm-auto px-2 text-muted">Metode:</div>
                            <div class="col-5 col-sm-auto px-2 font-weight-bold text-dark"><?php echo e($transaksi->tipePembayaran->nama ?? 'Transfer Bank'); ?></div>
                        </div>
                    </div>
                </div>

                <hr class="mb-5 border-light-dark">

                
                <div class="row mb-5 align-items-center">
                    <div class="col-6 col-sm-4 mb-3 mb-sm-0">
                        <div class="p-3 bg-light rounded text-center border">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Status Pembayaran</div>
                            <?php
                                $statusClass = 'secondary';
                                if($transaksi->status == 'dibayar') $statusClass = 'success';
                                elseif($transaksi->status == 'pending') $statusClass = 'warning';
                                elseif($transaksi->status == 'ditolak') $statusClass = 'danger';
                            ?>
                            <div class="h6 mb-0 font-weight-bold text-<?php echo e($statusClass); ?> text-uppercase">
                                <i class="fas fa-circle fa-xs mr-1"></i> <?php echo e($transaksi->status); ?>

                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4 mb-3 mb-sm-0">
                        <div class="p-3 bg-light rounded text-center border">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Teknisi</div>
                            <div class="h6 mb-0 font-weight-bold text-dark">
                                <?php echo e($transaksi->bookingAc->teknisi->name ?? 'Belum Ditugaskan'); ?>

                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <div class="p-3 bg-light rounded text-center border">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Jenis Layanan</div>
                            <div class="h6 mb-0 font-weight-bold text-dark">
                                <?php echo e($transaksi->bookingAc->layanan->nama ?? 'N/A'); ?>

                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="table-responsive mb-5">
                    <table class="table table-bordered table-striped border">
                        <thead class="bg-primary text-white text-center">
                            <tr>
                                <th width="5%">#</th>
                                <th class="text-left py-3">Deskripsi Produk / Jasa</th>
                                <th width="15%" class="py-3">Kuantitas</th>
                                <th width="20%" class="text-right py-3">Harga Satuan</th>
                                <th width="20%" class="text-right py-3 pr-4">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="text-dark">
                            <?php $__empty_1 = true; $__currentLoopData = $transaksi->detailServis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="text-center"><?php echo e($i + 1); ?></td>
                                    <td>
                                        <div class="font-weight-bold text-gray-900"><?php echo e($item->item); ?></div>
                                        <?php if($item->catatan): ?>
                                            <small class="text-muted italic"><?php echo e($item->catatan); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?php echo e((float)$item->quantity); ?> <small><?php echo e($item->satuan); ?></small></td>
                                    <td class="text-right text-muted italic small">Rp <?php echo e(number_format($item->harga, 0, ',', '.')); ?></td>
                                    <td class="text-right font-weight-bold pr-4">Rp <?php echo e(number_format($item->subtotal, 0, ',', '.')); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Tidak ada detail item.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-right py-3 text-uppercase font-weight-bold text-gray-800">Total Tagihan</th>
                                <th class="text-right py-3 pr-4 h4 font-weight-bold text-primary">Rp <?php echo e(number_format($transaksi->total_harga, 0, ',', '.')); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                
                <div class="row">
                    <div class="col-sm-6 mb-4 mb-sm-0">
                        <h6 class="font-weight-bold text-gray-800 text-uppercase mb-3">Informasi Pembayaran:</h6>
                        <div class="p-3 border rounded border-dashed bg-very-light">
                            <p class="text-sm text-dark mb-1 font-weight-bold">Bank Transfer:</p>
                            <p class="text-sm text-muted mb-1">Bank Mandiri</p>
                            <p class="text-sm text-muted mb-1">A/N BLUD AC SERVICE</p>
                            <h5 class="font-weight-bold text-primary mt-2">123-4567-890-X</h5>
                        </div>
                    </div>
                    <div class="col-sm-5 offset-sm-1 text-center">
                        <div class="mb-5 h-50"></div>
                        <p class="mb-0 text-dark font-weight-bold">( _______________________ )</p>
                        <small class="text-muted font-weight-bold text-uppercase">Tanda Tangan Adm. AC Service</small>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light p-4 text-center text-muted small">
                <i class="fas fa-info-circle mr-1"></i> Invoice ini dihasilkan secara otomatis oleh sistem BLUD System pada <?php echo e(now()->translatedFormat('d F Y H:i')); ?>.
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .no-print {
            display: none !important;
        }
        .card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
        .invoice-card {
            border: none !important;
        }
        body {
            background-color: white !important;
        }
        .container-fluid {
            padding: 0 !important;
        }
    }
    .invoice-card {
        background-color: #fff;
    }
    .bg-very-light {
        background-color: #fcfcfc;
    }
    .border-dashed {
        border-style: dashed !important;
    }
    .italic {
        font-style: italic;
    }
    .tracking-wider {
        letter-spacing: 0.1em;
    }
    .border-light-dark {
        border-color: #eee !important;
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/adminac/transaksi/show.blade.php ENDPATH**/ ?>