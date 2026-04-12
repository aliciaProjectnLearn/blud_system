@extends('layouts.app')

@section('title', 'Rekap Keuangan BLUD')

@push('styles')
<style>
    .card-sistem { border-left: 4px solid; }
    .card-futsal  { border-left-color: #1cc88a; }
    .card-kantin  { border-left-color: #f6c23e; }
    .card-ac      { border-left-color: #4e73df; }
    .badge-pemasukan  { background-color: #1cc88a; color: #fff; }
    .badge-pengeluaran { background-color: #e74a3b; color: #fff; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-1 text-gray-800">Rekap Keuangan BLUD</h1>
    <p class="mb-4 text-muted">Ringkasan keuangan seluruh sistem — Futsal, Kantin, dan AC.</p>

    {{-- Filter Tanggal --}}
    <div class="card shadow mb-4">
        <div class="card-body py-3">
            <form method="GET" class="form-inline">
                <label class="mr-2 font-weight-bold">Periode:</label>
                <input type="date" name="start_date" class="form-control form-control-sm mr-2"
                       value="{{ $startDate }}">
                <span class="mr-2">s/d</span>
                <input type="date" name="end_date" class="form-control form-control-sm mr-2"
                       value="{{ $endDate }}">
                <button class="btn btn-primary btn-sm mr-2">Filter</button>
                <a href="{{ route('dashboard.rekap-keuangan') }}" class="btn btn-secondary btn-sm">Reset</a>
            </form>
        </div>
    </div>

    {{-- Summary Cards --}}
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
                                Rp {{ number_format($totalPemasukan ?? 0, 0, ',', '.') }}
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
                                Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}
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
                            <div class="h5 mb-0 font-weight-bold {{ ($saldoAkhir ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($saldoAkhir ?? 0, 0, ',', '.') }}
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

    {{-- Breakdown --}}
    <div class="row mb-4">

        @foreach ($breakdown as $key => $item)

        @php
            $warna = [
                'futsal' => 'success',
                'kantin' => 'warning',
                'ac' => 'primary'
            ][$key];
        @endphp

        <div class="col-lg-4 mb-4">
            <div class="card shadow card-sistem card-{{ $key }} h-100">
                <div class="card-header py-3 d-flex align-items-center">
                    <span class="badge badge-{{ $warna }} mr-2">
                        {{ ucfirst($key) }}
                    </span>
                    <h6 class="m-0 font-weight-bold text-{{ $warna }}">
                        Sistem {{ ucfirst($key) }}
                    </h6>
                </div>

                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">

                        <tr>
                            <td class="text-muted">Pemasukan</td>
                            <td class="text-right font-weight-bold text-success">
                                Rp {{ number_format($item['pemasukan'], 0, ',', '.') }}
                            </td>
                        </tr>

                        <tr>
                            <td class="text-muted">Pengeluaran</td>
                            <td class="text-right font-weight-bold text-danger">
                                Rp {{ number_format($item['pengeluaran'], 0, ',', '.') }}
                            </td>
                        </tr>

                        <tr class="border-top">
                            <td class="font-weight-bold">Saldo</td>
                            <td class="text-right font-weight-bold {{ $item['saldo'] >= 0 ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($item['saldo'], 0, ',', '.') }}
                            </td>
                        </tr>

                    </table>
                </div>
            </div>
        </div>

        @endforeach

    </div>

    {{-- Grafik --}}
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

    {{-- Table --}}
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

                        @forelse($daftarTransaksi as $t)

                        <tr>

                            <td>
                                {{ ($daftarTransaksi->firstItem() ?? 0) + $loop->index }}
                            </td>

                            <td>
                                <span class="badge badge-{{ $t->sistem == 'Futsal' ? 'success' : ($t->sistem == 'Kantin' ? 'warning' : 'primary') }}">
                                    {{ $t->sistem }}
                                </span>
                            </td>

                            <td>
                                <span class="badge {{ $t->tipe == 'Pemasukan' ? 'badge-pemasukan' : 'badge-pengeluaran' }}">
                                    {{ $t->tipe }}
                                </span>
                            </td>

                            <td>{{ $t->deskripsi }}</td>

                            <td class="{{ $t->tipe == 'Pemasukan' ? 'text-success' : 'text-danger' }} font-weight-bold">
                                {{ $t->tipe == 'Pemasukan' ? '+' : '-' }}
                                Rp {{ number_format($t->nominal, 0, ',', '.') }}
                            </td>

                            <td>
                                {{ $t->tanggal ? \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y') : '-' }}
                            </td>

                        </tr>

                        @empty

                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                Tidak ada data
                            </td>
                        </tr>

                        @endforelse

                    </tbody>

                </table>
            </div>

            <div class="mt-3">
                {{ $daftarTransaksi->links() }}
            </div>

        </div>

    </div>

</div>
@endsection


@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const grafik = @json($grafik)

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

@endpush
