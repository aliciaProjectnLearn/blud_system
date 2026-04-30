<?php $__env->startSection('title', 'Rekap Keuangan BLUD'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .card-sistem { border-left: 4px solid; }
    .card-futsal  { border-left-color: #1cc88a; }
    .card-kantin  { border-left-color: #f6c23e; }
    .card-ac      { border-left-color: #4e73df; }
    .badge-pemasukan  { background-color: #1cc88a; color: #fff; }
    .badge-pengeluaran { background-color: #e74a3b; color: #fff; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">

    <h1 class="h3 mb-1 text-gray-800">Rekap Keuangan BLUD</h1>
    <p class="mb-4 text-muted">Ringkasan keuangan seluruh sistem — Futsal, Kantin, dan AC.</p>

    
    <div class="card shadow mb-4">
        <div class="card-body py-3">
            <form method="GET" class="form-inline">
                <label class="mr-2 font-weight-bold">Periode:</label>
                <input type="date" name="start_date" class="form-control form-control-sm mr-2"
                       value="<?php echo e($startDate); ?>">
                <span class="mr-2">s/d</span>
                <input type="date" name="end_date" class="form-control form-control-sm mr-2"
                       value="<?php echo e($endDate); ?>">
                <button class="btn btn-primary btn-sm mr-2">Filter</button>
                <a href="<?php echo e(route('dashboard.rekap-keuangan')); ?>" class="btn btn-secondary btn-sm">Reset</a>
            </form>
        </div>
    </div>

    
    <div class="row mb-4">

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Pemasukan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp <?php echo e(number_format($totalPemasukan ?? 0, 0, ',', '.')); ?>

                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-success"></i>
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
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Total Pengeluaran
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp <?php echo e(number_format($totalPengeluaran ?? 0, 0, ',', '.')); ?>

                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-danger"></i>
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
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Saldo Akhir
                            </div>
                            <div class="h5 mb-0 font-weight-bold <?php echo e(($saldoAkhir ?? 0) >= 0 ? 'text-success' : 'text-danger'); ?>">
                                Rp <?php echo e(number_format($saldoAkhir ?? 0, 0, ',', '.')); ?>

                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    
    <div class="row mb-4">

        <?php $__currentLoopData = $breakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

        <?php
            $warna = [
                'futsal' => 'success',
                'kantin' => 'warning',
                'ac' => 'primary'
            ][$key];
        ?>

        <div class="col-lg-4 mb-4">
            <div class="card shadow card-sistem card-<?php echo e($key); ?> h-100">
                <div class="card-header py-3 d-flex align-items-center">
                    <span class="badge badge-<?php echo e($warna); ?> mr-2">
                        <?php echo e(ucfirst($key)); ?>

                    </span>
                    <h6 class="m-0 font-weight-bold text-<?php echo e($warna); ?>">
                        Sistem <?php echo e(ucfirst($key)); ?>

                    </h6>
                </div>

                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">

                        <tr>
                            <td class="text-muted">Pemasukan</td>
                            <td class="text-right font-weight-bold text-success">
                                Rp <?php echo e(number_format($item['pemasukan'], 0, ',', '.')); ?>

                            </td>
                        </tr>

                        <tr>
                            <td class="text-muted">Pengeluaran</td>
                            <td class="text-right font-weight-bold text-danger">
                                Rp <?php echo e(number_format($item['pengeluaran'], 0, ',', '.')); ?>

                            </td>
                        </tr>

                        <tr class="border-top">
                            <td class="font-weight-bold">Saldo</td>
                            <td class="text-right font-weight-bold <?php echo e($item['saldo'] >= 0 ? 'text-success' : 'text-danger'); ?>">
                                Rp <?php echo e(number_format($item['saldo'], 0, ',', '.')); ?>

                            </td>
                        </tr>

                    </table>
                </div>
            </div>
        </div>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </div>

    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Grafik Pemasukan vs Pengeluaran
            </h6>
        </div>

        <div class="card-body">
            <div style="height:350px">
                <canvas id="grafikKeuangan"></canvas>
            </div>
        </div>
    </div>

    
    <div class="card shadow mb-4">

        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Daftar Transaksi Gabungan
            </h6>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-sm">

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Sistem</th>
                            <th>Tipe</th>
                            <th>Deskripsi</th>
                            <th>Nominal</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php $__empty_1 = true; $__currentLoopData = $daftarTransaksi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                        <tr>

                            <td>
                                <?php echo e(($daftarTransaksi->firstItem() ?? 0) + $loop->index); ?>

                            </td>

                            <td>
                                <span class="badge badge-<?php echo e($t->sistem == 'Futsal' ? 'success' : ($t->sistem == 'Kantin' ? 'warning' : 'primary')); ?>">
                                    <?php echo e($t->sistem); ?>

                                </span>
                            </td>

                            <td>
                                <span class="badge <?php echo e($t->tipe == 'Pemasukan' ? 'badge-pemasukan' : 'badge-pengeluaran'); ?>">
                                    <?php echo e($t->tipe); ?>

                                </span>
                            </td>

                            <td><?php echo e($t->deskripsi); ?></td>

                            <td class="<?php echo e($t->tipe == 'Pemasukan' ? 'text-success' : 'text-danger'); ?> font-weight-bold">
                                <?php echo e($t->tipe == 'Pemasukan' ? '+' : '-'); ?>

                                Rp <?php echo e(number_format($t->nominal, 0, ',', '.')); ?>

                            </td>

                            <td>
                                <?php echo e($t->tanggal ? \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y') : '-'); ?>

                            </td>

                        </tr>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                Tidak ada data
                            </td>
                        </tr>

                        <?php endif; ?>

                    </tbody>

                </table>
            </div>

            <div class="mt-3">
                <?php echo e($daftarTransaksi->links()); ?>

            </div>

        </div>

    </div>

</div>
<?php $__env->stopSection(); ?>


<?php $__env->startPush('scripts'); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const grafik = <?php echo json_encode($grafik, 15, 512) ?>

new Chart(document.getElementById('grafikKeuangan'), {

    type: 'bar',

    data: {
        labels: grafik.map(g => g.bulan),
        datasets: [

            {
                label: 'Pemasukan',
                data: grafik.map(g => g.pemasukan),
                backgroundColor: '#1cc88a'
            },

            {
                label: 'Pengeluaran',
                data: grafik.map(g => g.pengeluaran),
                backgroundColor: '#e74a3b'
            }

        ]
    },

    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                ticks: {
                    callback: val => 'Rp ' + val.toLocaleString('id-ID')
                }
            }
        }
    }

})

</script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/dashboard/rekap-keuangan.blade.php ENDPATH**/ ?>