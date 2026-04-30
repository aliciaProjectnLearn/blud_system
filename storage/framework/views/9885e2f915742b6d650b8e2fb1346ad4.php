<?php $__env->startSection('title', 'Pembagian Pendapatan'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    /* ── Palet & Variabel ─────────────────────── */
    :root {
        --ac-color:     #4e73df;
        --kantin-color: #f6c23e;
        --futsal-color: #1cc88a;
        --jurusan-color:#6f42c1;
        --aplikasi-color:#e83e8c;
        --blud-color:   #17a2b8;
        --bersih-color: #28a745;
    }

    /* ── Card sistem ─────────────────────────── */
    .sistem-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        transition: transform .2s, box-shadow .2s;
        overflow: hidden;
    }
    .sistem-card:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(0,0,0,0.13); }

    .sistem-card.card-ac     { border-top: 4px solid var(--ac-color); }
    .sistem-card.card-kantin { border-top: 4px solid var(--kantin-color); }
    .sistem-card.card-futsal { border-top: 4px solid var(--futsal-color); }

    /* ── Badge penerima ──────────────────────── */
    .badge-jurusan  { background: var(--jurusan-color);  color:#fff; }
    .badge-aplikasi { background: var(--aplikasi-color); color:#fff; }
    .badge-blud     { background: var(--blud-color);     color:#fff; }
    .badge-bersih   { background: var(--bersih-color);   color:#fff; }

    /* ── Progress bar penerima ───────────────── */
    .penerima-bar { height: 10px; border-radius: 5px; }
    .bar-jurusan  { background: var(--jurusan-color); }
    .bar-aplikasi { background: var(--aplikasi-color); }
    .bar-blud     { background: var(--blud-color); }
    .bar-bersih   { background: var(--bersih-color); }

    /* ── Rekap card ──────────────────────────── */
    .rekap-card {
        border-radius: 10px;
        border: none;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
    }

    /* ── Konfigurasi panel ───────────────────── */
    .config-panel {
        background: #f8f9fc;
        border-radius: 10px;
        border: 1px solid #e3e6f0;
        padding: 1.2rem;
    }
    .persen-input {
        width: 80px;
        text-align: center;
        font-weight: 700;
        border-radius: 8px;
        border: 2px solid #e3e6f0;
        transition: border-color .2s;
    }
    .persen-input:focus { border-color: #4e73df; outline: none; }
    .total-badge {
        font-size: 0.85rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
    }
    .total-ok      { background: #d4edda; color: #155724; }
    .total-not-ok  { background: #f8d7da; color: #721c24; }

    /* ── Saldo highlight ─────────────────────── */
    .saldo-highlight {
        background: linear-gradient(135deg, #f8f9fc 0%, #e8ecf8 100%);
        border-radius: 8px;
        padding: 10px 14px;
        margin-bottom: 16px;
    }

    /* ── Donut chart placeholder ─────────────── */
    .donut-wrap { position: relative; }
    .donut-center {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%,-50%);
        text-align: center;
        pointer-events: none;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">

    <div class="d-flex align-items-center mb-1">
        <h1 class="h3 mb-0 text-gray-800 mr-3">Pembagian Pendapatan</h1>
        <span class="badge badge-primary">Super Admin</span>
    </div>
    <p class="mb-4 text-muted">Kalkulasi dan konfigurasi distribusi pendapatan bersih per sistem.</p>

    
    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-2"></i><?php echo e(session('success')); ?>

        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i><?php echo e($errors->first()); ?>

        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
    <?php endif; ?>

    
    <div class="card shadow mb-4">
        <div class="card-body py-3">
            <form method="GET" class="form-inline">
                <label class="mr-2 font-weight-bold"><i class="fas fa-calendar-alt mr-1 text-primary"></i> Periode:</label>
                <input type="date" name="start_date" class="form-control form-control-sm mr-2" value="<?php echo e($startDate); ?>">
                <span class="mr-2">s/d</span>
                <input type="date" name="end_date" class="form-control form-control-sm mr-2" value="<?php echo e($endDate); ?>">
                <button class="btn btn-primary btn-sm mr-2"><i class="fas fa-filter mr-1"></i>Filter</button>
                <a href="<?php echo e(route('dashboard.pembagian-pendapatan')); ?>" class="btn btn-secondary btn-sm">Reset</a>
            </form>
        </div>
    </div>

    
    <div class="row mb-4">
        <?php
            $ikonPenerima = [
                'jurusan'  => ['icon' => 'fa-graduation-cap', 'color' => 'jurusan',  'label' => 'Jurusan'],
                'aplikasi' => ['icon' => 'fa-laptop-code',    'color' => 'aplikasi', 'label' => 'Aplikasi'],
                'blud'     => ['icon' => 'fa-hospital',       'color' => 'blud',     'label' => 'BLUD'],
                'bersih'   => ['icon' => 'fa-hand-holding-usd','color'=> 'bersih',   'label' => 'Bersih'],
            ];
        ?>

        <?php $__currentLoopData = $rekapPenerima; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $penerima => $totalNominal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $meta = $ikonPenerima[$penerima] ?? ['icon'=>'fa-circle','color'=>'secondary','label'=>ucfirst($penerima)]; ?>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="rekap-card card h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: var(--<?php echo e($meta['color']); ?>-color)">
                                <?php echo e($meta['label']); ?>

                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp <?php echo e(number_format($totalNominal, 0, ',', '.')); ?>

                            </div>
                            <small class="text-muted">Total dari semua sistem</small>
                        </div>
                        <div class="col-auto">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:52px;height:52px;background:var(--<?php echo e($meta['color']); ?>-color);opacity:.15;">
                            </div>
                            <i class="fas <?php echo e($meta['icon']); ?> fa-2x position-absolute" style="margin-top:-42px;margin-left:14px;color:var(--<?php echo e($meta['color']); ?>-color)"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <?php
        $sistemMeta = [
            'ac'     => ['label'=>'Sistem AC',     'color'=>'ac',     'icon'=>'fa-snowflake',    'saldo' => $saldoAc],
            'kantin' => ['label'=>'Sistem Kantin',  'color'=>'kantin', 'icon'=>'fa-store',        'saldo' => $saldoKantin],
            'futsal' => ['label'=>'Sistem Futsal',  'color'=>'futsal', 'icon'=>'fa-futbol',       'saldo' => $saldoFutsal],
        ];
        $urutan = ['ac','kantin','futsal'];
    ?>

    <div class="row mb-4">
    <?php $__currentLoopData = $urutan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sistem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $meta  = $sistemMeta[$sistem];
        $data  = $pembagian[$sistem];
        $cfg   = $konfigurasi->get($sistem, collect());
    ?>

    <div class="col-lg-4 mb-4">
        <div class="card sistem-card card-<?php echo e($sistem); ?> h-100">

            
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="fas <?php echo e($meta['icon']); ?> mr-2" style="color:var(--<?php echo e($meta['color']); ?>-color)"></i>
                    <h6 class="m-0 font-weight-bold" style="color:var(--<?php echo e($meta['color']); ?>-color)">
                        <?php echo e($meta['label']); ?>

                    </h6>
                </div>
                <button class="btn btn-sm btn-outline-secondary" data-toggle="collapse"
                        data-target="#config-<?php echo e($sistem); ?>">
                    <i class="fas fa-cog"></i>
                </button>
            </div>

            <div class="card-body">

                
                <div class="saldo-highlight d-flex justify-content-between align-items-center">
                    <span class="text-muted small font-weight-bold text-uppercase">Saldo Bersih</span>
                    <span class="font-weight-bold" style="font-size:1.1rem; color:var(--<?php echo e($meta['color']); ?>-color)">
                        Rp <?php echo e(number_format($data['saldo_bersih'], 0, ',', '.')); ?>

                    </span>
                </div>

                
                <div class="mb-3">
                    <?php $__currentLoopData = $data['detail']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $penerima => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $pm = $ikonPenerima[$penerima] ?? ['icon'=>'fa-circle','color'=>'secondary','label'=>ucfirst($penerima)];
                    ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="d-flex align-items-center">
                                <span class="badge badge-<?php echo e($pm['color']); ?> mr-2"><?php echo e($pm['label']); ?></span>
                                <span class="text-muted small"><?php echo e(number_format($info['persentase'], 1)); ?>%</span>
                            </div>
                            <span class="font-weight-bold text-gray-800 small">
                                Rp <?php echo e(number_format($info['nominal'], 0, ',', '.')); ?>

                            </span>
                        </div>
                        <div class="progress" style="height:8px;border-radius:4px;">
                            <div class="progress-bar bar-<?php echo e($pm['color']); ?> penerima-bar"
                                 style="width: <?php echo e($info['persentase']); ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                
                <div class="collapse" id="config-<?php echo e($sistem); ?>">
                    <div class="config-panel mt-2">
                        <p class="text-xs text-muted font-weight-bold text-uppercase mb-2">
                            <i class="fas fa-sliders-h mr-1"></i>Atur Persentase
                        </p>
                        <form method="POST" action="<?php echo e(route('dashboard.pembagian-pendapatan.update')); ?>"
                              id="form-<?php echo e($sistem); ?>">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <input type="hidden" name="sistem" value="<?php echo e($sistem); ?>">

                            <?php $__currentLoopData = $data['detail']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $penerima => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $pm2 = $ikonPenerima[$penerima] ?? ['icon'=>'fa-circle','color'=>'secondary','label'=>ucfirst($penerima)]; ?>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="mb-0 small font-weight-bold" style="color:var(--<?php echo e($pm2['color']); ?>-color)">
                                    <i class="fas <?php echo e($pm2['icon']); ?> mr-1"></i><?php echo e($pm2['label']); ?>

                                </label>
                                <div class="input-group input-group-sm" style="width:100px">
                                    <input type="number"
                                           name="persentase[<?php echo e($penerima); ?>]"
                                           class="form-control persen-input persen-field"
                                           data-sistem="<?php echo e($sistem); ?>"
                                           value="<?php echo e(number_format($info['persentase'], 2, '.', '')); ?>"
                                           min="0" max="100" step="0.01">
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="total-badge total-indicator-<?php echo e($sistem); ?>">
                                    Total: <span class="total-val-<?php echo e($sistem); ?>">0</span>%
                                </span>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-save mr-1"></i>Simpan
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table mr-2"></i>Ringkasan Pembagian Pendapatan
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>Sistem</th>
                            <th class="text-right">Saldo Bersih</th>
                            <?php $__currentLoopData = array_keys($rekapPenerima); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $pm3 = $ikonPenerima[$p] ?? ['label'=>ucfirst($p)]; ?>
                            <th class="text-right"><?php echo e($pm3['label']); ?></th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $urutan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sistem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $meta2 = $sistemMeta[$sistem];
                            $data2 = $pembagian[$sistem];
                        ?>
                        <tr>
                            <td>
                                <i class="fas <?php echo e($meta2['icon']); ?> mr-1" style="color:var(--<?php echo e($meta2['color']); ?>-color)"></i>
                                <strong><?php echo e($meta2['label']); ?></strong>
                            </td>
                            <td class="text-right font-weight-bold" style="color:var(--<?php echo e($meta2['color']); ?>-color)">
                                Rp <?php echo e(number_format($data2['saldo_bersih'], 0, ',', '.')); ?>

                            </td>
                            <?php $__currentLoopData = array_keys($rekapPenerima); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <td class="text-right">
                                <?php if(isset($data2['detail'][$p])): ?>
                                    <span class="text-success font-weight-bold">
                                        Rp <?php echo e(number_format($data2['detail'][$p]['nominal'], 0, ',', '.')); ?>

                                    </span>
                                    <br><small class="text-muted"><?php echo e(number_format($data2['detail'][$p]['persentase'], 1)); ?>%</small>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <tfoot class="font-weight-bold" style="background:#f8f9fc">
                        <tr>
                            <td>TOTAL</td>
                            <td class="text-right">
                                Rp <?php echo e(number_format($saldoAc + $saldoKantin + $saldoFutsal, 0, ',', '.')); ?>

                            </td>
                            <?php $__currentLoopData = array_keys($rekapPenerima); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <td class="text-right text-success">
                                Rp <?php echo e(number_format($rekapPenerima[$p], 0, ',', '.')); ?>

                            </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Hitung total persentase realtime per sistem
document.querySelectorAll('.persen-field').forEach(input => {
    input.addEventListener('input', function() {
        const sistem = this.dataset.sistem;
        const fields = document.querySelectorAll(`.persen-field[data-sistem="${sistem}"]`);
        let total = 0;
        fields.forEach(f => total += parseFloat(f.value) || 0);
        total = Math.round(total * 100) / 100;

        const badge    = document.querySelector(`.total-indicator-${sistem}`);
        const valEl    = document.querySelector(`.total-val-${sistem}`);
        valEl.textContent = total.toFixed(2);

        if (Math.abs(total - 100) < 0.01) {
            badge.className = `total-badge total-ok total-indicator-${sistem}`;
        } else {
            badge.className = `total-badge total-not-ok total-indicator-${sistem}`;
        }
    });
});

// Init total on load
['ac','kantin','futsal'].forEach(sistem => {
    const fields = document.querySelectorAll(`.persen-field[data-sistem="${sistem}"]`);
    let total = 0;
    fields.forEach(f => total += parseFloat(f.value) || 0);
    total = Math.round(total * 100) / 100;

    const badge = document.querySelector(`.total-indicator-${sistem}`);
    const valEl = document.querySelector(`.total-val-${sistem}`);
    if (valEl) valEl.textContent = total.toFixed(2);
    if (badge) {
        badge.className = `total-badge ${Math.abs(total - 100) < 0.01 ? 'total-ok' : 'total-not-ok'} total-indicator-${sistem}`;
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/dashboard/pembagian-pendapatan.blade.php ENDPATH**/ ?>