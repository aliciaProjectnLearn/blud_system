@extends('layouts.app')

@section('title', 'Dashboard Admin Futsal')

@section('content')

{{-- Page Heading --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard Admin Futsal</h1>
</div>

{{-- Stats Cards --}}
<div class="row">

    {{-- Total Transaksi --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Transaksi Futsal</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTransaksi }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Pendapatan --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Pendapatan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Transaksi Hari Ini --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Transaksi Hari Ini</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $transaksiHariIni }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Status Menunggu --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pembayaran Menunggu</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statusMenunggu }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
{{-- End Stats Cards --}}

{{-- Grafik & Status Pembayaran --}}
<div class="row">

    {{-- Grafik Pendapatan Per Bulan --}}
    <div class="col-lg-8 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Pendapatan Per Bulan ({{ now()->year }})</h6>
            </div>
            <div class="card-body">
                <canvas id="chartPendapatan" height="100"></canvas>
            </div>
        </div>
    </div>

    {{-- Status Pembayaran --}}
    <div class="col-lg-4 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Status Pembayaran</h6>
            </div>
            <div class="card-body">
                <h4 class="small font-weight-bold">
                    Verifikasi <span class="float-right">{{ $statusVerifikasi }}</span>
                </h4>
                <div class="progress mb-4">
                    <div class="progress-bar bg-success" role="progressbar"
                        style="width: {{ $totalTransaksi > 0 ? ($statusVerifikasi/$totalTransaksi)*100 : 0 }}%"></div>
                </div>

                <h4 class="small font-weight-bold">
                    Menunggu <span class="float-right">{{ $statusMenunggu }}</span>
                </h4>
                <div class="progress mb-4">
                    <div class="progress-bar bg-warning" role="progressbar"
                        style="width: {{ $totalTransaksi > 0 ? ($statusMenunggu/$totalTransaksi)*100 : 0 }}%"></div>
                </div>

                <h4 class="small font-weight-bold">
                    Dibatalkan <span class="float-right">{{ $statusDibatalkan }}</span>
                </h4>
                <div class="progress mb-4">
                    <div class="progress-bar bg-danger" role="progressbar"
                        style="width: {{ $totalTransaksi > 0 ? ($statusDibatalkan/$totalTransaksi)*100 : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Tabel Transaksi Terbaru --}}
<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Transaksi Terbaru</h6>
            </div>
            <div class="card-body">
                @if($transaksiTerbaru->isEmpty())
                    <p class="text-center text-muted">Belum ada data transaksi.</p>
                @else
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nama Pelanggan</th>
                                <th>Jumlah Bayar</th>
                                <th>Status</th>
                                <th>Tanggal Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transaksiTerbaru as $t)
                            <tr>
                                <td>{{ $t->nama_user }}</td>
                                <td>Rp {{ number_format($t->jumlah_bayar, 0, ',', '.') }}</td>
                                <td>
                                    @if($t->status === 'verifikasi')
                                        <span class="badge badge-success">{{ $t->status }}</span>
                                    @elseif($t->status === 'menunggu')
                                        <span class="badge badge-warning">{{ $t->status }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ $t->status }}</span>
                                    @endif
                                </td>
                                <td>{{ $t->tgl_bayar ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Tabel Jadwal Lapangan Hari Ini --}}
<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    Ringkasan Jadwal Lapangan Hari Ini — {{ \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}
                </h6>
            </div>
            <div class="card-body">
                @if($jadwalHariIni->isEmpty())
                    <p class="text-center text-muted">Belum ada slot jadwal tergenerate untuk hari ini.</p>
                @else
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="30%">Jam</th>
                                <th width="40%">Nama Lapangan</th>
                                <th width="30%" class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwalHariIni as $j)
                            <tr>
                                <td class="align-middle font-weight-bold">{{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}</td>
                                <td class="align-middle">{{ $j->lapangan->nama ?? 'Unknown' }}</td>
                                <td class="align-middle text-center">
                                    @if($j->status === 'tersedia')
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Tersedia</span>
                                    @else
                                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-times-circle mr-1"></i> Terisi</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('chartPendapatan').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($labelBulan) !!},
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: {!! json_encode($dataPendapatan) !!},
                backgroundColor: 'rgba(78, 115, 223, 0.5)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
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
@endpush
