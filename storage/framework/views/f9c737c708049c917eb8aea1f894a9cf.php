<?php $__env->startSection('title', 'Dashboard Servis Kendaraan'); ?>

<?php $__env->startPush('styles'); ?>
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        .card-summary:hover {
            transform: translateY(-5px);
            transition: all 0.3s;
        }
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<!-- Page Heading -->
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-4">
    <h1 class="h3 mb-2 mb-sm-0 text-gray-800">Dashboard — Servis Kendaraan</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent p-0 mb-0">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item d-none d-md-inline">Admin Servis</li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>
</div>

<!-- Summary Cards -->
<div class="row">
    <!-- Total Transaksi -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2 card-summary">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Transaksi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e(number_format($totalTransaksi)); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Pendapatan -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2 card-summary">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Pendapatan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?php echo e(number_format($totalPendapatan, 0, ',', '.')); ?></div>
                        <div class="text-xs mt-1 text-muted">Bulan Ini</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-wallet fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Servis Hari Ini -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2 card-summary">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Servis Hari Ini</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($servisHariIni); ?></div>
                        <div class="text-xs mt-1 text-muted">Booking Hari Ini</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tools fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Teknisi -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2 card-summary">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Teknisi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($totalTeknisi); ?></div>
                        <div class="text-xs mt-1 text-muted">Teknisi Aktif</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-cog fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row: Grafik & Ringkasan -->
<div class="row">
    <!-- Area Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-bar mr-1"></i> Grafik Pendapatan Per Bulan</h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="incomeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Box -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Ringkasan Pendapatan</h6>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                        Tertinggi
                        <span class="font-weight-bold text-success">Rp 7.200.000 (Nov)</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                        Terendah
                        <span class="font-weight-bold text-danger">Rp 3.200.000 (Jan)</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                        Rata-rata
                        <span class="font-weight-bold text-primary">Rp 5.233.333 / bln</span>
                    </li>
                </ul>
                <hr>
                <div class="mt-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-xs font-weight-bold">Pencapaian Target Bulan Ini</span>
                        <span class="text-xs font-weight-bold">78%</span>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 78%" aria-valuenow="78" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary mb-3 mb-md-0"><i class="fas fa-table mr-1"></i> Transaksi Terbaru</h6>
        <div class="d-flex flex-wrap align-items-center" style="gap: 10px;">
            <select id="filterStatus" class="form-control form-control-sm" style="width: auto; min-width: 130px;">
                <option value="">Semua Status</option>
                <option value="Selesai">Selesai</option>
                <option value="Proses">Proses</option>
                <option value="Menunggu">Menunggu</option>
                <option value="Dibatalkan">Dibatalkan</option>
            </select>
            <select id="filterKendaraan" class="form-control form-control-sm" style="width: auto; min-width: 130px;">
                <option value="">Semua Kendaraan</option>
                <option value="Motor">Motor</option>
                <option value="Mobil">Mobil</option>
            </select>
            <button id="btnResetTransaksi" class="btn btn-outline-secondary btn-sm d-none">
                <i class="fas fa-undo mr-1"></i> Reset
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm" id="tabelTransaksi" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Transaksi</th>
                        <th>Pelanggan</th>
                        <th>Kendaraan</th>
                        <th>Jenis Servis</th>
                        <th>Teknisi</th>
                        <th>Biaya</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $transaksiTerbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($index + 1); ?></td>
                        <td><code><?php echo e($item->kode_booking); ?></code></td>
                        <td><?php echo e($item->user->nama_lengkap ?? $item->user->name ?? '-'); ?></td>
                        <td><?php echo e(ucfirst($item->tipe_kendaraan)); ?></td>
                        <td><?php echo e($item->layananServis->nama_layanan ?? '-'); ?></td>
                        <td><?php echo e($item->teknisi->name ?? '-'); ?></td>
                        <td>Rp <?php echo e(number_format(optional($item->pembayaranServis)->total_biaya ?? 0, 0, ',', '.')); ?></td>
                        <td>
                            <?php
                                $status = strtolower($item->status);
                                $badge = 'secondary';
                                if($status == 'selesai') $badge = 'success';
                                elseif($status == 'proses') $badge = 'primary';
                                elseif($status == 'menunggu') $badge = 'warning';
                                elseif($status == 'dibatalkan') $badge = 'danger';
                            ?>
                            <span class="badge badge-<?php echo e($badge); ?>"><?php echo e(ucfirst($item->status)); ?></span>
                        </td>
                        <td>
                            <?php
                                $detailData = json_encode([
                                    'kode'          => $item->kode_booking,
                                    'nama_pelanggan'=> $item->user->nama_lengkap ?? $item->user->name ?? '-',
                                    'no_hp'         => $item->user->no_hp ?? '-',
                                    'email'         => $item->user->email ?? '-',
                                    'kendaraan'     => ucfirst($item->tipe_kendaraan) . ' - ' . ($item->merek_kendaraan ?? '-'),
                                    'no_polisi'     => $item->nomor_plat,
                                    'tanggal'       => $item->tanggal_booking->translatedFormat('d F Y'),
                                    'jam'           => \Carbon\Carbon::parse($item->jam_booking)->format('H:i'),
                                    'layanan'       => $item->layananServis->nama_layanan ?? '-',
                                    'teknisi'       => $item->teknisi->name ?? '-',
                                    'status'        => ucfirst($item->status),
                                    'biaya'         => 'Rp ' . number_format(optional($item->pembayaranServis)->total_biaya ?? 0, 0, ',', '.'),
                                    'status_bayar'  => ucfirst(optional($item->pembayaranServis)->status_pembayaran ?? 'Belum Bayar'),
                                    'rincian'       => $item->rincianServis->map(function($r) {
                                        return [
                                            'nama'     => $r->nama_item,
                                            'qty'      => $r->jumlah,
                                            'harga'    => number_format($r->harga_satuan, 0, ',', '.'),
                                            'subtotal' => number_format($r->subtotal, 0, ',', '.'),
                                        ];
                                    })->toArray(),
                                ]);
                            ?>
                            <div class="d-inline-flex align-items-center" style="gap: 5px;">
                                <button class="btn btn-primary btn-sm btn-detail" 
                                    data-item="<?php echo e($detailData); ?>"
                                    data-toggle="tooltip" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="<?php echo e(route('adminservis.transaksi.invoice', $item->kode_booking)); ?>" 
                                    target="_blank" class="btn btn-secondary btn-sm" 
                                    data-toggle="tooltip" title="Cetak">
                                    <i class="fas fa-print"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detail Transaksi -->
<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-info-circle mr-2"></i> Detail Transaksi Servis</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6 border-right">
                        <h6 class="font-weight-bold text-primary mb-3">Informasi Pelanggan</h6>
                        <table class="table table-borderless table-sm">
                            <tr><td width="35%">Nama</td><td>: <span id="det-nama">-</span></td></tr>
                            <tr><td>No. HP</td><td>: <span id="det-hp">-</span></td></tr>
                            <tr><td>Email</td><td>: <span id="det-email">-</span></td></tr>
                        </table>
                        <h6 class="font-weight-bold text-primary mt-4 mb-3">Informasi Kendaraan</h6>
                        <table class="table table-borderless table-sm">
                            <tr><td width="35%">Kendaraan</td><td>: <span id="det-kendaraan">-</span></td></tr>
                            <tr><td>No. Polisi</td><td>: <span id="det-plat">-</span></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold text-primary mb-3">Rincian Booking</h6>
                        <table class="table table-borderless table-sm">
                            <tr><td width="35%">Kode</td><td>: <code id="det-kode">-</code></td></tr>
                            <tr><td>Tanggal</td><td>: <span id="det-tgl">-</span></td></tr>
                            <tr><td>Jam</td><td>: <span id="det-jam">-</span></td></tr>
                            <tr><td>Layanan</td><td>: <span id="det-layanan">-</span></td></tr>
                            <tr><td>Teknisi</td><td>: <span id="det-teknisi">-</span></td></tr>
                            <tr><td>Status</td><td>: <span id="det-status" class="badge badge-info">-</span></td></tr>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <h6 class="font-weight-bold text-primary mb-3">Rincian Biaya & Komponen</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Nama Item</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-right">Harga Satuan</th>
                                        <th class="text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="det-rincian">
                                    <!-- Dynamic -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-right">Total Biaya</th>
                                        <th class="text-right text-primary h5 font-weight-bold" id="det-biaya">Rp 0</th>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-right">Status Pembayaran</td>
                                        <td class="text-right font-weight-bold" id="det-pembayaran">-</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <a href="#" id="btnCetakInvoiceModal" target="_blank" class="btn btn-primary">
                    <i class="fas fa-print mr-1"></i> Cetak Invoice
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Today's Schedule Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-info text-white">
        <h6 class="m-0 font-weight-bold"><i class="fas fa-calendar-day mr-1"></i> Jadwal Servis Hari Ini</h6>
        <small class="d-block d-md-inline"><?php echo e(\Carbon\Carbon::now()->translatedFormat("d F Y")); ?></small>
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-md-end align-items-center mb-3" style="gap: 10px;">
            <select id="filterKendaraanJadwal" class="form-control form-control-sm" style="width: auto; min-width: 130px;">
                <option value="">Semua Kendaraan</option>
                <option value="Motor">Motor</option>
                <option value="Mobil">Mobil</option>
            </select>
            <select id="filterStatusJadwal" class="form-control form-control-sm" style="width: auto; min-width: 130px;">
                <option value="">Semua Status</option>
                <option value="Menunggu">Menunggu</option>
                <option value="Sedang Dikerjakan">Sedang Dikerjakan</option>
                <option value="Selesai">Selesai</option>
            </select>
            <button id="btnResetJadwal" class="btn btn-outline-secondary btn-sm d-none">
                <i class="fas fa-undo mr-1"></i> Reset
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm" id="tabelJadwal" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Jam</th>
                        <th>Nama Pelanggan</th>
                        <th>No. Polisi</th>
                        <th>Kendaraan</th>
                        <th>Keluhan / Servis</th>
                        <th>Teknisi</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $jadwalHariIni; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($index + 1); ?></td>
                        <td class="font-weight-bold" style="color: #4e73df;"><?php echo e(\Carbon\Carbon::parse($item->jam_booking)->format('H:i')); ?></td>
                        <td><?php echo e($item->user->nama_lengkap ?? $item->user->name ?? '-'); ?></td>
                        <td><span class="badge badge-dark"><?php echo e($item->nomor_plat); ?></span></td>
                        <td><?php echo e(ucfirst($item->tipe_kendaraan)); ?></td>
                        <td><?php echo e($item->keluhan); ?></td>
                        <td><?php echo e($item->teknisi->name ?? '-'); ?></td>
                        <td>
                            <?php
                                $status = strtolower($item->status);
                                $badge = 'secondary';
                                if($status == 'selesai') $badge = 'success';
                                elseif($status == 'proses' || $status == 'sedang dikerjakan') $badge = 'warning text-white';
                            ?>
                            <span class="badge badge-<?php echo e($badge); ?>"><?php echo e(ucfirst($item->status)); ?></span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            // Tooltip initialization
            $('[data-toggle="tooltip"]').tooltip();

            // Chart.js initialization
            var ctx = document.getElementById('incomeChart').getContext('2d');
            var incomeChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
                    datasets: [{
                        label: 'Pendapatan',
                        data: <?php echo json_encode($pendapatanBulanan, 15, 512) ?>,
                        backgroundColor: '#4e73df',
                        hoverBackgroundColor: '#2e59d9',
                        borderColor: '#4e73df',
                        borderWidth: 1
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        xAxes: [{
                            gridLines: { display: false, drawBorder: false }
                        }],
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                callback: function(value, index, values) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            },
                            gridLines: { color: "rgb(234, 236, 244)", zeroLineColor: "rgb(234, 236, 244)", drawBorder: false, borderDash: [2], zeroLineBorderDash: [2] }
                        }]
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                return 'Pendapatan: Rp ' + tooltipItem.yLabel.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            });

            // DataTables initialization - Transaksi
            var table = $('#tabelTransaksi').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
                pageLength: 10,
                order: [[0, 'asc']]
            });

            function cekResetTransaksi() {
                var status    = $('#filterStatus').val();
                var kendaraan = $('#filterKendaraan').val();
                if (status !== '' || kendaraan !== '') {
                    $('#btnResetTransaksi').removeClass('d-none');
                } else {
                    $('#btnResetTransaksi').addClass('d-none');
                }
            }

            $('#filterStatus').on('change', function() {
                table.column(7).search(this.value).draw();
                cekResetTransaksi();
            });

            $('#filterKendaraan').on('change', function() {
                table.column(3).search(this.value).draw();
                cekResetTransaksi();
            });

            $('#btnResetTransaksi').on('click', function () {
                $('#filterStatus').val('');
                $('#filterKendaraan').val('');
                table.column(7).search('').draw();
                table.column(3).search('').draw();
                $(this).addClass('d-none');
            });

            // DataTables initialization - Jadwal
            var tableJadwal = $('#tabelJadwal').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
                pageLength: 5,
                order: [[1, 'asc']]
            });

            function cekResetJadwal() {
                var kendaraan = $('#filterKendaraanJadwal').val();
                var status    = $('#filterStatusJadwal').val();
                if (kendaraan !== '' || status !== '') {
                    $('#btnResetJadwal').removeClass('d-none');
                } else {
                    $('#btnResetJadwal').addClass('d-none');
                }
            }

            $('#filterKendaraanJadwal').on('change', function () {
                tableJadwal.column(4).search(this.value).draw();
                cekResetJadwal();
            });

            $('#filterStatusJadwal').on('change', function () {
                tableJadwal.column(7).search(this.value).draw();
                cekResetJadwal();
            });

            $('#btnResetJadwal').on('click', function () {
                $('#filterKendaraanJadwal').val('');
                $('#filterStatusJadwal').val('');
                tableJadwal.column(4).search('').draw();
                tableJadwal.column(7).search('').draw();
                $(this).addClass('d-none');
            });

            // Detail Modal Handler
            $(document).on('click', '.btn-detail', function() {
                var item = $(this).data('item');
                
                // Populate Modal
                $('#det-kode').text(item.kode);
                $('#det-nama').text(item.nama_pelanggan);
                $('#det-hp').text(item.no_hp);
                $('#det-email').text(item.email);
                $('#det-kendaraan').text(item.kendaraan);
                $('#det-plat').text(item.no_polisi);
                $('#det-tgl').text(item.tanggal);
                $('#det-jam').text(item.jam);
                $('#det-layanan').text(item.layanan);
                $('#det-teknisi').text(item.teknisi);
                $('#det-status').text(item.status);
                $('#det-biaya').text(item.biaya);
                $('#det-pembayaran').text(item.status_bayar);

                // Update Print Button URL
                var invoiceUrl = "<?php echo e(route('adminservis.transaksi.invoice', ':kode')); ?>";
                $('#btnCetakInvoiceModal').attr('href', invoiceUrl.replace(':kode', item.kode));

                // Build Rincian Table
                var rincianHtml = '';
                if(item.rincian.length > 0) {
                    item.rincian.forEach(function(r) {
                        rincianHtml += '<tr>' +
                            '<td>' + r.nama + '</td>' +
                            '<td class="text-center">' + r.qty + '</td>' +
                            '<td class="text-right">Rp ' + r.harga + '</td>' +
                            '<td class="text-right font-weight-bold">Rp ' + r.subtotal + '</td>' +
                            '</tr>';
                    });
                } else {
                    rincianHtml = '<tr><td colspan="4" class="text-center italic">Tidak ada rincian komponen</td></tr>';
                }
                $('#det-rincian').html(rincianHtml);

                // Show Modal
                $('#modalDetail').modal('show');
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/adminservis/index.blade.php ENDPATH**/ ?>