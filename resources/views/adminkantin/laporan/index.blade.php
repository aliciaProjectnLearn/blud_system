@extends('layouts.app')

@section('title', 'Laporan Transaksi Kantin/Ruko')

@push('styles')
<style>
    .border-left-purple {
        border-left: .25rem solid #6f42c1 !important;
    }
    .text-purple {
        color: #6f42c1 !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Laporan Transaksi Kantin/Ruko</h1>
    </div>

    {{-- Filter Card --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Laporan</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.kantin.laporan.index') }}">
                <div class="form-row align-items-end">
                    
                    {{-- Filter Dari Tanggal --}}
                    <div class="col-md-4 mb-3">
                        <label for="tanggal_dari">Dari Tanggal (Tgl Bayar)</label>
                        <input type="date" name="tanggal_dari" id="tanggal_dari" value="{{ request('tanggal_dari') }}" class="form-control">
                    </div>

                    {{-- Filter Sampai Tanggal --}}
                    <div class="col-md-4 mb-3">
                        <label for="tanggal_sampai">Sampai Tanggal (Tgl Bayar)</label>
                        <input type="date" name="tanggal_sampai" id="tanggal_sampai" value="{{ request('tanggal_sampai') }}" class="form-control">
                    </div>

                    {{-- Action Buttons --}}
                    <div class="col-md-4 mb-3">
                        <button type="submit" class="btn btn-primary mr-2">
                            <i class="fas fa-search fa-sm"></i> Cari
                        </button>
                        <a href="{{ route('admin.kantin.laporan.index') }}" class="btn btn-secondary">
                            <i class="fas fa-undo fa-sm"></i> Reset
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- Informasi & Export --}}
    <div class="row mb-4">
        {{-- Total Pendapatan --}}
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Pemasukan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Transaksi --}}
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Transaksi
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totalTransaksi, 0, ',', '.') }} Transaksi
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Export Buttons --}}
        <div class="col-xl-4 col-md-12 d-flex flex-column justify-content-center align-items-end mb-4">
            <div class="w-100">
                <a href="{{ route('admin.kantin.laporan.export.pdf', request()->all()) }}" target="_blank" class="btn btn-danger btn-block mb-2 shadow-sm">
                    <i class="fas fa-file-pdf fa-sm text-white-50"></i> Export PDF
                </a>
                <a href="{{ route('admin.kantin.laporan.export.excel', request()->all()) }}" target="_blank" class="btn btn-success btn-block shadow-sm">
                    <i class="fas fa-file-excel fa-sm text-white-50"></i> Export Excel
                </a>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Transaksi (Pembayaran)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama Penyewa</th>
                            <th>Kode Ruko</th>
                            <th>Tgl Sewa</th>
                            <th>Tgl Bayar</th>
                            <th class="text-right">Jumlah Bayar</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($laporan as $index => $item)
                            <tr>
                                <td class="text-center align-middle">{{ ($laporan->currentPage() - 1) * $laporan->perPage() + $index + 1 }}</td>
                                <td class="align-middle">{{ $item->sewaRuko?->penyewa?->nama_usaha ?? '-' }}</td>
                                <td class="align-middle">{{ $item->sewaRuko?->ruko?->kode_unit ?? '-' }}</td>
                                <td class="align-middle">
                                    {{ $item->sewaRuko ? \Carbon\Carbon::parse($item->sewaRuko->tgl_mulai)->format('d-m-Y') . ' s/d ' . \Carbon\Carbon::parse($item->sewaRuko->tgl_selesai)->format('d-m-Y') : '-' }}
                                </td>
                                <td class="align-middle">{{ $item->tgl_bayar ? \Carbon\Carbon::parse($item->tgl_bayar)->format('d-m-Y H:i') : '-' }}</td>
                                <td class="align-middle text-right font-weight-bold">
                                    Rp {{ number_format($item->jumlah_tagihan, 0, ',', '.') }}
                                </td>
                                <td class="align-middle text-center">
                                    @if(strtolower($item->status) == 'lunas')
                                        <span class="badge badge-success px-2 py-1">Lunas</span>
                                    @elseif(strtolower($item->status) == 'verifikasi')
                                        <span class="badge badge-info px-2 py-1">Menunggu Verifikasi</span>
                                    @elseif(strtolower($item->status) == 'menunggu')
                                        <span class="badge badge-warning px-2 py-1">Menunggu Pembayaran</span>
                                    @elseif(strtolower($item->status) == 'dibatalkan')
                                        <span class="badge badge-danger px-2 py-1">Dibatalkan</span>
                                    @else
                                        {{-- Fallback jika status null atau tidak dikenal --}}
                                        <span class="badge badge-secondary px-2 py-1">{{ ucfirst($item->status ?? 'Unknown') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 mt-2"></i>
                                    <h5>Data laporan tidak ditemukan</h5>
                                    <p>Silakan ubah filter untuk mencari data transaksi.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($laporan->hasPages())
            <div class="mt-4">
                {{ $laporan->links() }}
            </div>
            @endif
        </div>
    </div>

</div>
@endsection
