@extends('layouts.app')

@section('title', 'Dashboard Sewa Kantin')

@section('content')

    <style>
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.2s infinite;
            border-radius: 4px;
        }

        @keyframes shimmer {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        .skeleton-card {
            height: 80px;
        }

        .skeleton-row {
            height: 20px;
            margin-bottom: 10px;
        }
    </style>

    {{-- Skeleton Loader --}}
    <div id="skeleton-loader" class="container-fluid">
        <div class="row mb-4">
            @for ($i = 0; $i < 4; $i++)
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card shadow h-100 py-3 px-3">
                        <div class="skeleton skeleton-card"></div>
                    </div>
                </div>
            @endfor
        </div>

        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card shadow p-3">
                    <div class="skeleton skeleton-row w-50 mb-3"></div>
                    <div class="skeleton skeleton-row"></div>
                    <div class="skeleton skeleton-row"></div>
                    <div class="skeleton skeleton-row w-75"></div>
                </div>
            </div>

            <div class="col-lg-8 mb-4">
                <div class="card shadow p-3">
                    <div class="skeleton skeleton-row w-25 mb-3"></div>
                    <div class="skeleton" style="height:180px"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid" id="main-content" style="display:none">

        {{-- Page Heading --}}
        <div class="mb-4">
            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between">
                <h1 class="h3 mb-2 mb-sm-0 text-gray-800">Dashboard Sistem Sewa Kantin</h1>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-sm-0">
                    <a href="{{ route('user.kantin.booking.form') }}" class="btn btn-sm btn-primary shadow-sm mr-2">
                        <i class="fas fa-plus fa-sm text-white-50"></i> Sewa Kantin Baru
                    </a>
                    <a href="{{ route('user.dashboard') }}" class="btn btn-sm btn-secondary shadow-sm">
                        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        @php
            $isPenyewa = (bool) $penyewa;
            $totalSewa = $totalSewa ?? 0;
            $totalDikonfirmasi = $totalDikonfirmasi ?? 0;
            $totalSelesai = $totalSelesai ?? 0;
            $tagihanMendatang = $tagihanMendatang ?? null;
            $sewaAktif = $sewaAktif ?? null;
            $labelGrafik = $labelGrafik ?? [];
            $dataGrafik = $dataGrafik ?? [];
            $transaksiTerbaru = $transaksiTerbaru ?? collect();
        @endphp

        {{-- Summary Cards --}}
        <div class="row">

            {{-- Total Sewa --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Penyewaan
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $totalSewa }} Sewa
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-store fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Dikonfirmasi --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Pembayaran Dikonfirmasi
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $totalDikonfirmasi }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Selesai --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Pembayaran Lunas
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $totalSelesai }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tagihan --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">

                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Tagihan Mendatang
                        </div>

                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            @if ($tagihanMendatang)
                                Rp {{ number_format($tagihanMendatang->jumlah_tagihan, 0, ',', '.') }}
                            @else
                                <span class="text-muted" style="font-size:14px">
                                    Tidak ada tagihan
                                </span>
                            @endif
                        </div>

                        @if ($tagihanMendatang)
                            <div class="text-xs text-danger mt-1">
                                Jatuh tempo :
                                {{ \Carbon\Carbon::parse($tagihanMendatang->tgl_jatuh_tempo)->format('d M Y') }}
                            </div>
                        @endif

                    </div>
                </div>
            </div>

        </div>

        {{-- Info Sewa Aktif --}}
        <div class="row">

            <div class="col-lg-4 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Status Sewa Aktif
                        </h6>
                    </div>

                    <div class="card-body">

                        @if ($sewaAktif)
                            @if ($sewaAktif->status === 'pending')
                                <div class="alert alert-warning border-left-warning shadow-sm py-2 px-3 mb-3 text-sm">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Pengajuan unit
                                    <strong>
                                        {{ $sewaAktif->ruko->kode_unit ?? ($sewaAktif->ruko->no_unit ?? '-') }}
                                    </strong>
                                    sedang diproses
                                </div>
                            @endif

                            <table class="table table-borderless table-sm">

                                <tr>
                                    <td><strong>Unit</strong></td>
                                    <td>:
                                        {{ $sewaAktif->ruko->kode_unit ?? ($sewaAktif->ruko->no_unit ?? '-') }}
                                    </td>
                                </tr>

                                <tr>
                                    <td><strong>Mulai</strong></td>
                                    <td>:
                                        {{ \Carbon\Carbon::parse($sewaAktif->tgl_mulai)->format('d M Y') }}
                                    </td>
                                </tr>

                                <tr>
                                    <td><strong>Selesai</strong></td>
                                    <td>:
                                        {{ \Carbon\Carbon::parse($sewaAktif->tgl_selesai)->format('d M Y') }}
                                    </td>
                                </tr>

                                <tr>
                                    <td><strong>Status</strong></td>
                                    <td>
                                        @if ($sewaAktif->status === 'pending')
                                            <span class="badge badge-warning">
                                                Pengajuan Diproses
                                            </span>
                                        @else
                                            <span class="badge badge-success">
                                                {{ ucfirst($sewaAktif->status) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>

                            </table>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-store fa-3x text-gray-200 mb-3"></i>
                                <p class="text-muted small">
                                    Tidak ada sewa aktif saat ini
                                </p>
                            </div>
                        @endif

                    </div>
                </div>
            </div>

            {{-- Grafik --}}
            <div class="col-lg-8 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Tagihan Per Bulan ({{ now()->year }})
                        </h6>
                    </div>

                    <div class="card-body">
                        <canvas id="chartTagihan" class="w-100" style="max-height: 250px;"></canvas>
                    </div>

                </div>
            </div>

        </div>

        {{-- Transaksi --}}
        <div class="row">
            <div class="col-lg-12 mb-4">
                <div class="card shadow">

                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Transaksi Terbaru
                        </h6>
                    </div>

                    <div class="card-body">

                        @if ($transaksiTerbaru->isEmpty())

                            <div class="text-center py-4">
                                <i class="fas fa-receipt fa-3x text-gray-200 mb-3"></i>
                                <p class="text-muted">
                                    Belum ada data transaksi
                                </p>
                            </div>
                        @else
                            <div class="table-responsive">

                                <table class="table table-bordered">

                                    <thead>
                                        <tr>
                                            <th>Unit</th>
                                            <th>Termin</th>
                                            <th>Jumlah</th>
                                            <th class="d-none d-md-table-cell">Jatuh Tempo</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @foreach ($transaksiTerbaru as $t)
                                            <tr>

                                                <td>
                                                    {{ $t->sewaRuko->ruko->kode_unit ?? ($t->sewaRuko->ruko->no_unit ?? '-') }}
                                                </td>

                                                <td>Termin {{ $t->termin }}</td>

                                                <td>
                                                    Rp {{ number_format($t->jumlah_tagihan, 0, ',', '.') }}
                                                </td>

                                                <td class="d-none d-md-table-cell">
                                                    @if ($t->tgl_jatuh_tempo)
                                                        {{ \Carbon\Carbon::parse($t->tgl_jatuh_tempo)->format('d M Y') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>

                                                <td>

                                                    @php
                                                        $badge = match ($t->status) {
                                                            'lunas' => 'success',
                                                            'verifikasi' => 'info',
                                                            'menunggu' => 'warning',
                                                            default => 'secondary',
                                                        };
                                                    @endphp

                                                    <span class="badge badge-{{ $badge }}">
                                                        {{ ucfirst($t->status) }}
                                                    </span>

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

    </div>

@endsection

@push('scripts')
    <script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>

    <script>
        const ctx = document.getElementById('chartTagihan');

        if (ctx) {
            new Chart(ctx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($labelGrafik) !!},
                    datasets: [{
                        label: 'Tagihan',
                        data: {!! json_encode($dataGrafik) !!},
                        backgroundColor: 'rgba(28,200,138,0.5)',
                        borderColor: 'rgba(28,200,138,1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID')
                                }
                            }
                        }]
                    }
                }
            })
        }

        window.addEventListener('load', function() {
            document.getElementById('skeleton-loader').style.display = 'none'
            document.getElementById('main-content').style.display = 'block'
        })
    </script>
@endpush
