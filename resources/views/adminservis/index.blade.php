@extends('layouts.app')

@section('title', 'Dashboard Servis Kendaraan')

@push('styles')
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
@endpush

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard — Servis Kendaraan</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent p-0 mb-0">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item">Admin Servis</li>
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalTransaksi) }}</div>
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $servisHariIni }}</div>
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTeknisi }}</div>
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
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-table mr-1"></i> Transaksi Terbaru</h6>
        <div class="d-flex gap-2">
            <select id="filterStatus" class="form-control form-control-sm mr-2" style="width: 150px;">
                <option value="">Semua Status</option>
                <option value="Selesai">Selesai</option>
                <option value="Proses">Proses</option>
                <option value="Menunggu">Menunggu</option>
                <option value="Dibatalkan">Dibatalkan</option>
            </select>
            <select id="filterKendaraan" class="form-control form-control-sm" style="width: 150px;">
                <option value="">Semua Kendaraan</option>
                <option value="Motor">Motor</option>
                <option value="Mobil">Mobil</option>
            </select>
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
                    @foreach($transaksiTerbaru as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><code>{{ $item['kode'] }}</code></td>
                        <td>{{ $item['nama_pelanggan'] }}</td>
                        <td>{{ $item['jenis_kendaraan'] }}</td>
                        <td>{{ $item['jenis_servis'] }}</td>
                        <td>{{ $item['teknisi'] }}</td>
                        <td>Rp {{ number_format($item['total_biaya'], 0, ',', '.') }}</td>
                        <td>
                            @php
                                $badge = 'secondary';
                                if($item['status'] == 'Selesai') $badge = 'success';
                                elseif($item['status'] == 'Proses') $badge = 'primary';
                                elseif($item['status'] == 'Menunggu') $badge = 'warning';
                                elseif($item['status'] == 'Dibatalkan') $badge = 'danger';
                            @endphp
                            <span class="badge badge-{{ $badge }}">{{ $item['status'] }}</span>
                        </td>
                        <td>
                            <button class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> Detail</button>
                            <button class="btn btn-secondary btn-sm"><i class="fas fa-print"></i> Cetak</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Today's Schedule Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-info text-white">
        <h6 class="m-0 font-weight-bold"><i class="fas fa-calendar-day mr-1"></i> Jadwal Servis Hari Ini — {{ \Carbon\Carbon::now()->translatedFormat("d F Y") }}</h6>
    </div>
    <div class="card-body">
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
                    @foreach($jadwalHariIni as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="font-weight-bold">{{ $item['jam'] }}</td>
                        <td>{{ $item['nama_pelanggan'] }}</td>
                        <td><span class="badge badge-dark">{{ $item['no_polisi'] }}</span></td>
                        <td>{{ $item['jenis_kendaraan'] }}</td>
                        <td>{{ $item['keluhan'] }}</td>
                        <td>{{ $item['teknisi'] }}</td>
                        <td>
                            @php
                                $badge = 'secondary';
                                if($item['status'] == 'Selesai') $badge = 'success';
                                elseif($item['status'] == 'Sedang Dikerjakan') $badge = 'warning text-white';
                            @endphp
                            <span class="badge badge-{{ $badge }}">{{ $item['status'] }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            // Chart.js initialization
            var ctx = document.getElementById('incomeChart').getContext('2d');
            var incomeChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
                    datasets: [{
                        label: 'Pendapatan',
                        data: @json($pendapatanBulanan),
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

            // DataTables initialization
            var table = $('#tabelTransaksi').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
                pageLength: 10,
                order: [[0, 'asc']]
            });

            $('#filterStatus').on('change', function() {
                table.column(7).search(this.value).draw();
            });

            $('#filterKendaraan').on('change', function() {
                table.column(3).search(this.value).draw();
            });

            $('#tabelJadwal').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
                pageLength: 5,
                order: [[1, 'asc']]
            });
        });
    </script>
@endpush
