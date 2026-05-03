@extends('layouts.app')

@section('title', 'Laporan Harian Kasir Futsal')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Laporan Transaksi Kasir Futsal</h1>
        <a href="{{ route('kasirfutsal.laporan.pdf', request()->all()) }}" class="btn btn-danger btn-sm" target="_blank">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Laporan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('kasirfutsal.laporan.index') }}" method="GET" class="form-inline">
                <div class="form-group mb-2 mr-3">
                    <label for="tanggal" class="mr-2">Tanggal:</label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ request('tanggal', date('Y-m-d')) }}">
                </div>
                <button type="submit" class="btn btn-primary mb-2">Filter</button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Pemasukan (Lunas)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Transaksi</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>No</th>
                            <th>Kode Transaksi</th>
                            <th>Tgl Dibuat</th>
                            <th>Pemesan</th>
                            <th>Jenis</th>
                            <th>Metode Pembayaran</th>
                            <th>Total Bayar</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($laporan as $key => $item)
                            <tr>
                                <td>{{ $laporan->firstItem() + $key }}</td>
                                <td>{{ $item->kode_pembayaran }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y H:i') }}</td>
                                <td>
                                    @if($item->jenis_transaksi === 'membership')
                                        {{ $item->membershipUser->user->nama_lengkap ?? ($item->membershipUser->user->name ?? '-') }}
                                    @else
                                        {{ $item->bookingFutsal->nama_pemesan ?? ($item->bookingFutsal->user->name ?? '-') }}
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $jenisTransaksi = $item->jenis_transaksi ?? ($item->bookingFutsal->jenis_pembayaran ?? 'reguler');
                                        $labelJenis = match($jenisTransaksi) {
                                            'event'      => 'Booking Event',
                                            'paket'      => 'Paket',
                                            'membership' => 'Pembelian Paket',
                                            default      => 'Reguler',
                                        };
                                        $badgeJenis = match($jenisTransaksi) {
                                            'event'      => 'badge-warning',
                                            'paket'      => 'badge-info',
                                            'membership' => 'badge-info',
                                            default      => 'badge-primary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeJenis }}">{{ $labelJenis }}</span>
                                </td>
                                <td>{{ $item->tipePembayaran->nama ?? '-' }}</td>
                                <td>Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                                <td>
                                    @if ($item->status == \App\Models\PembayaranFutsal::STATUS_VERIFIKASI)
                                        <span class="badge badge-success">Lunas</span>
                                    @elseif ($item->status == \App\Models\PembayaranFutsal::STATUS_MENUNGGU)
                                        <span class="badge badge-warning">
                                            <i class="fas fa-clock mr-1"></i>Belum Diproses
                                        </span>
                                    @else
                                        <span class="badge badge-danger">Batal</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada transaksi pada tanggal ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $laporan->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
