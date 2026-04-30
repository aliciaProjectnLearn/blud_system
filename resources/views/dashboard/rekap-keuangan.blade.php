@extends('layouts.app')

@section('title', 'Rekap Keuangan BLUD')

@push('styles')
    <style>
        .card-sistem { border-left: 4px solid; }
        .card-futsal  { border-left-color: #1cc88a; }
        .card-kantin  { border-left-color: #f6c23e; }
        .card-ac      { border-left-color: #4e73df; }
        .card-servis  { border-left-color: #e74a3b; }

        .tbl-rekap th, .tbl-rekap td { vertical-align: middle; }
        .tbl-rekap tfoot td { font-weight: 700; background-color: #f8f9fc; }
    </style>
@endpush

@section('content')
    <div class="container-fluid">

        <h1 class="h3 mb-1 text-gray-800">Rekap Keuangan BLUD</h1>
        <p class="mb-4 text-muted">Ringkasan pendapatan bersih seluruh sistem — Futsal, Kantin, AC, dan Servis Kendaraan.</p>

        {{-- Filter Tanggal --}}
        <div class="card shadow mb-4">
            <div class="card-body py-3">
                <form method="GET" class="form-inline flex-wrap">
                    <label class="mr-2 font-weight-bold">Periode:</label>
                    <input type="date" name="start_date" class="form-control form-control-sm mr-2"
                        value="{{ $startDate }}">
                    <span class="mr-2">s/d</span>
                    <input type="date" name="end_date" class="form-control form-control-sm mr-2"
                        value="{{ $endDate }}">
                    <button class="btn btn-primary btn-sm mr-2">
                        <i class="fas fa-filter mr-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.dashboard.rekap-keuangan') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-undo mr-1"></i>Reset
                    </a>
                </form>
            </div>
        </div>

        {{-- Summary Cards: Total Keseluruhan --}}
        <div class="row mb-4">
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Pemasukan</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    Rp {{ number_format($totalPemasukan ?? 0, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="col-auto"><i class="fas fa-arrow-up fa-2x text-success"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Pengeluaran</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="col-auto"><i class="fas fa-arrow-down fa-2x text-danger"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Pendapatan Bersih</div>
                                <div class="h5 mb-0 font-weight-bold {{ ($saldoAkhir ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                    Rp {{ number_format($saldoAkhir ?? 0, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="col-auto"><i class="fas fa-wallet fa-2x text-primary"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Pendapatan Bersih Per Sistem --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-table mr-1"></i>Rekap Pendapatan Bersih Per Sistem
                </h6>
            </div>
            <div class="card-body">
                @php
                    $sistemMeta = [
                        'futsal' => ['label' => 'Futsal',           'icon' => 'fa-futbol',     'warna' => 'success'],
                        'kantin' => ['label' => 'Kantin (Ruko)',     'icon' => 'fa-store',      'warna' => 'warning'],
                        'ac'     => ['label' => 'Cuci AC',          'icon' => 'fa-snowflake',  'warna' => 'primary'],
                        'servis' => ['label' => 'Servis Kendaraan', 'icon' => 'fa-motorcycle', 'warna' => 'danger'],
                    ];
                @endphp
                <div class="table-responsive">
                    <table class="table table-bordered table-hover tbl-rekap mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Sistem</th>
                                <th class="text-right">Total Pemasukan</th>
                                <th class="text-right">Total Pengeluaran</th>
                                <th class="text-right">Pendapatan Bersih</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($breakdown as $key => $item)
                                @php $meta = $sistemMeta[$key]; @endphp
                                <tr>
                                    <td>
                                        <i class="fas {{ $meta['icon'] }} text-{{ $meta['warna'] }} mr-1"></i>
                                        {{ $meta['label'] }}
                                    </td>
                                    <td class="text-right text-success font-weight-bold">
                                        Rp {{ number_format($item['pemasukan'] ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="text-right text-danger font-weight-bold">
                                        Rp {{ number_format($item['pengeluaran'] ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="text-right font-weight-bold {{ ($item['saldo'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                        Rp {{ number_format($item['saldo'] ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td><i class="fas fa-sigma mr-1"></i>Total Keseluruhan</td>
                                <td class="text-right text-success">
                                    Rp {{ number_format($totalPemasukan ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="text-right text-danger">
                                    Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="text-right {{ ($saldoAkhir ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                    Rp {{ number_format($saldoAkhir ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Breakdown Card Per Sistem --}}
        <div class="row mb-4">
            @foreach ($breakdown as $key => $item)
                @php $meta = $sistemMeta[$key]; @endphp
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card shadow card-sistem card-{{ $key }} h-100">
                        <div class="card-header py-3 d-flex align-items-center">
                            <i class="fas {{ $meta['icon'] }} text-{{ $meta['warna'] }} mr-2"></i>
                            <h6 class="m-0 font-weight-bold text-{{ $meta['warna'] }}">{{ $meta['label'] }}</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <td class="text-muted">Pemasukan</td>
                                    <td class="text-right font-weight-bold text-success">
                                        Rp {{ number_format($item['pemasukan'] ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Pengeluaran</td>
                                    <td class="text-right font-weight-bold text-danger">
                                        Rp {{ number_format($item['pengeluaran'] ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr class="border-top">
                                    <td class="font-weight-bold">Bersih</td>
                                    <td class="text-right font-weight-bold {{ ($item['saldo'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                        Rp {{ number_format($item['saldo'] ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Grafik Pemasukan vs Pengeluaran per Bulan --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-bar mr-1"></i>Grafik Pemasukan vs Pengeluaran (6 Bulan Terakhir)
                </h6>
            </div>
            <div class="card-body">
                <div style="height:350px">
                    <canvas id="grafikKeuangan"></canvas>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const grafik = @json($grafik);
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
                    },
                    {
                        label: 'Pendapatan Bersih',
                        data: grafik.map(g => g.pemasukan - g.pengeluaran),
                        backgroundColor: '#4e73df',
                        type: 'line',
                        fill: false,
                        borderColor: '#4e73df',
                        tension: 0.3
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
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.dataset.label + ': Rp ' + ctx.raw.toLocaleString('id-ID')
                        }
                    }
                }
            }
        });
    </script>
@endpush
