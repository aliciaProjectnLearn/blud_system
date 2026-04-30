<?php $__env->startSection('title', 'Dashboard Admin AC'); ?>

<?php $__env->startSection('content'); ?>

    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Admin AC</h1>
    </div>

    
    <div class="row">

        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Transaksi AC</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($totalTransaksi); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Pendapatan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp
                                <?php echo e(number_format($totalPendapatan, 0, ',', '.')); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Transaksi Hari Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($transaksiHariIni); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pembayaran Menunggu</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($statusMenunggu); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    
    <div class="row">

        
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Layanan AC</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($totalLayanan); ?> Layanan</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-snowflake fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Produk / Alat</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($totalProduk); ?> Produk</div>
                            <div class="text-xs text-muted mt-1">Total stok: <?php echo e($totalStok); ?> unit</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Teknisi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($totalTeknisi); ?> Teknisi</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-cog fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    

    
    <div class="row">

        
        <div class="col-lg-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Pendapatan Per Bulan (<?php echo e(now()->year); ?>)</h6>
                </div>
                <div class="card-body">
                    <canvas id="chartPendapatan" height="100"></canvas>
                </div>
            </div>
        </div>

        
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Booking AC</h6>
                </div>
                <div class="card-body">
                    <?php $totalBooking = $bookingMenunggu + $bookingProses + $bookingSelesai; ?>

                    <h4 class="small font-weight-bold">
                        Selesai <span class="float-right"><?php echo e($bookingSelesai); ?></span>
                    </h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-success" role="progressbar"
                            style="width: <?php echo e($totalBooking > 0 ? ($bookingSelesai / $totalBooking) * 100 : 0); ?>%"></div>
                    </div>

                    <h4 class="small font-weight-bold">
                        Proses <span class="float-right"><?php echo e($bookingProses); ?></span>
                    </h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-info" role="progressbar"
                            style="width: <?php echo e($totalBooking > 0 ? ($bookingProses / $totalBooking) * 100 : 0); ?>%"></div>
                    </div>

                    <h4 class="small font-weight-bold">
                        Menunggu <span class="float-right"><?php echo e($bookingMenunggu); ?></span>
                    </h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-warning" role="progressbar"
                            style="width: <?php echo e($totalBooking > 0 ? ($bookingMenunggu / $totalBooking) * 100 : 0); ?>%"></div>
                    </div>

                    <h4 class="small font-weight-bold">
                        Pembayaran Berhasil <span class="float-right"><?php echo e($statusVerifikasi); ?></span>
                    </h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-primary" role="progressbar"
                            style="width: <?php echo e($totalTransaksi > 0 ? ($statusVerifikasi / $totalTransaksi) * 100 : 0); ?>%"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Transaksi Terbaru</h6>
                </div>
                <div class="card-body">
                    <?php if($transaksiTerbaru->isEmpty()): ?>
                        <p class="text-center text-muted">Belum ada data transaksi.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Pelanggan</th>
                                        <th>Layanan</th>
                                        <th>Total Biaya</th>
                                        <th>Status</th>
                                        <th>Tanggal Bayar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $transaksiTerbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($t->nama_user); ?></td>
                                            <td><?php echo e($t->nama_layanan); ?></td>
                                            <td>Rp <?php echo e(number_format($t->total_harga, 0, ',', '.')); ?></td>
                                            <td>
                                                <?php if($t->status === 'dibayar'): ?>
                                                    <span class="badge badge-success"><?php echo e($t->status); ?></span>
                                                <?php elseif($t->status === 'pending'): ?>
                                                    <span class="badge badge-warning"><?php echo e($t->status); ?></span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger"><?php echo e($t->status); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e($t->tgl_bayar ?? '-'); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Jadwal Kunjungan Hari Ini — <?php echo e(\Carbon\Carbon::today()->translatedFormat('l, d F Y')); ?>

                    </h6>
                </div>
                <div class="card-body">
                    <?php if($jadwalHariIni->isEmpty()): ?>
                        <p class="text-center text-muted">Tidak ada jadwal kunjungan hari ini.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Pelanggan</th>
                                        <th>Layanan</th>
                                        <th>Tanggal Kunjungan</th>
                                        <th>Alamat</th>
                                        <th>Merek AC</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $jadwalHariIni; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($j->nama_user); ?></td>
                                            <td><?php echo e($j->nama_layanan); ?></td>
                                            <td><?php echo e(\Carbon\Carbon::parse($j->tgl_kunjungan)->format('d/m/Y')); ?></td>
                                            <td><?php echo e($j->alamat); ?></td>
                                            <td><?php echo e($j->merek_ac ?? '-'); ?></td>
                                            <td>
                                                <?php if($j->status === 'selesai'): ?>
                                                    <span class="badge badge-success"><?php echo e($j->status); ?></span>
                                                <?php elseif($j->status === 'proses'): ?>
                                                    <span class="badge badge-info"><?php echo e($j->status); ?></span>
                                                <?php elseif($j->status === 'menunggu'): ?>
                                                    <span class="badge badge-warning"><?php echo e($j->status); ?></span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary"><?php echo e($j->status); ?></span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>



<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('vendor/chart.js/Chart.min.js')); ?>"></script>
    <script>
        const ctx = document.getElementById('chartPendapatan').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labelBulan); ?>,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: <?php echo json_encode($dataPendapatan); ?>,
                    backgroundColor: 'rgba(28, 200, 138, 0.5)',
                    borderColor: 'rgba(28, 200, 138, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/adminac/index.blade.php ENDPATH**/ ?>