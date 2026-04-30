<?php $__env->startSection('title', 'Dashboard Kasir'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Kasir</h1>
        <span class="text-muted"><?php echo e(\Carbon\Carbon::now()->translatedFormat('l, d F Y')); ?></span>
    </div>

    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Booking (Hari Ini)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($totalBookingHariIni); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Transaksi Lunas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($totalTransaksiHariIni); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Pemasukan Hari Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?php echo e(number_format($totalPemasukanHariIni, 0, ',', '.')); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body d-flex justify-content-center gap-3">
                    <a href="#" class="btn btn-primary btn-icon-split btn-lg mx-2">
                        <span class="icon text-white-50"><i class="fas fa-calendar-check"></i></span>
                        <span class="text">Menu Booking</span>
                    </a>
                    <a href="#" class="btn btn-success btn-icon-split btn-lg mx-2">
                        <span class="icon text-white-50"><i class="fas fa-money-bill-wave"></i></span>
                        <span class="text">Menu Transaksi</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tren Booking (7 Hari Terakhir)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="chartBooking"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Tren Pemasukan (7 Hari Terakhir)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="chartPemasukan"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Inisialisasi Chart (Logika tetap sama seperti sebelumnya)
    const ctxBooking = document.getElementById('chartBooking').getContext('2d');
    new Chart(ctxBooking, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($grafikBooking->pluck('label')); ?>,
            datasets: [{
                label: 'Jumlah Booking',
                data: <?php echo json_encode($grafikBooking->pluck('total')); ?>,
                backgroundColor: '#4e73df',
            }]
        },
        options: { maintainAspectRatio: false }
    });

    const ctxPemasukan = document.getElementById('chartPemasukan').getContext('2d');
    new Chart(ctxPemasukan, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($grafikPemasukan->pluck('label')); ?>,
            datasets: [{
                label: 'Pemasukan (Rp)',
                data: <?php echo json_encode($grafikPemasukan->pluck('total')); ?>,
                borderColor: '#1cc88a',
                fill: true,
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
            }]
        },
        options: { maintainAspectRatio: false }
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/kasirservis/index.blade.php ENDPATH**/ ?>