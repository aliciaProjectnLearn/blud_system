@extends('layouts.app')

@section('title', 'Dashboard Admin AC')

@section('content')

    {{-- Page Heading --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Admin AC</h1>
    </div>

    {{-- Stats Cards --}}
    <div class="row">

        {{-- Total Transaksi --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Transaksi AC</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp
                                {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
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

        {{-- Pembayaran Menunggu --}}
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

    {{-- Summary Card: Layanan, Produk, Teknisi --}}
    <div class="row">

        {{-- Total Layanan --}}
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Layanan AC</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalLayanan }} Layanan</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-snowflake fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Produk/Alat --}}
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Produk / Alat</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalProduk }} Produk</div>
                            <div class="text-xs text-muted mt-1">Total stok: {{ $totalStok }} unit</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Teknisi --}}
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Teknisi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTeknisi }} Teknisi</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-cog fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    {{-- End Summary Card --}}

    {{-- Grafik & Status --}}
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

        {{-- Status Booking --}}
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Booking AC</h6>
                </div>
                <div class="card-body">
                    @php $totalBooking = $bookingMenunggu + $bookingProses + $bookingSelesai; @endphp

                    <h4 class="small font-weight-bold">
                        Selesai <span class="float-right">{{ $bookingSelesai }}</span>
                    </h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-success" role="progressbar"
                            style="width: {{ $totalBooking > 0 ? ($bookingSelesai / $totalBooking) * 100 : 0 }}%"></div>
                    </div>

                    <h4 class="small font-weight-bold">
                        Proses <span class="float-right">{{ $bookingProses }}</span>
                    </h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-info" role="progressbar"
                            style="width: {{ $totalBooking > 0 ? ($bookingProses / $totalBooking) * 100 : 0 }}%"></div>
                    </div>

                    <h4 class="small font-weight-bold">
                        Menunggu <span class="float-right">{{ $bookingMenunggu }}</span>
                    </h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-warning" role="progressbar"
                            style="width: {{ $totalBooking > 0 ? ($bookingMenunggu / $totalBooking) * 100 : 0 }}%"></div>
                    </div>

                    <h4 class="small font-weight-bold">
                        Pembayaran Berhasil <span class="float-right">{{ $statusVerifikasi }}</span>
                    </h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-primary" role="progressbar"
                            style="width: {{ $totalTransaksi > 0 ? ($statusVerifikasi / $totalTransaksi) * 100 : 0 }}%"></div>
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
                    @if ($transaksiTerbaru->isEmpty())
                        <p class="text-center text-muted">Belum ada data transaksi.</p>
                    @else
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
                                    @foreach ($transaksiTerbaru as $t)
                                        <tr>
                                            <td>{{ $t->nama_user }}</td>
                                            <td>{{ $t->nama_layanan }}</td>
                                            <td>Rp {{ number_format($t->total_harga, 0, ',', '.') }}</td>
                                            <td>
                                                @if ($t->status === 'dibayar')
                                                    <span class="badge badge-success">{{ $t->status }}</span>
                                                @elseif($t->status === 'pending')
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

    {{-- Tabel Jadwal Kunjungan Hari Ini --}}
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Jadwal Kunjungan Hari Ini — {{ \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}
                    </h6>
                </div>
                <div class="card-body">
                    @if ($jadwalHariIni->isEmpty())
                        <p class="text-center text-muted">Tidak ada jadwal kunjungan hari ini.</p>
                    @else
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
                                    @foreach ($jadwalHariIni as $j)
                                        <tr>
                                            <td>{{ $j->nama_user }}</td>
                                            <td>{{ $j->nama_layanan }}</td>
                                            <td>{{ \Carbon\Carbon::parse($j->tgl_kunjungan)->format('d/m/Y') }}</td>
                                            <td>{{ $j->alamat }}</td>
                                            <td>{{ $j->merek_ac ?? '-' }}</td>
                                            <td>
                                                @if ($j->status === 'selesai')
                                                    <span class="badge badge-success">{{ $j->status }}</span>
                                                @elseif($j->status === 'proses')
                                                    <span class="badge badge-info">{{ $j->status }}</span>
                                                @elseif($j->status === 'menunggu')
                                                    <span class="badge badge-warning">{{ $j->status }}</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ $j->status }}</span>
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
    <script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
    <script>
        const ctx = document.getElementById('chartPendapatan').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($labelBulan) !!},
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: {!! json_encode($dataPendapatan) !!},
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
@endpush
