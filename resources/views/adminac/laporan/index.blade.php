@extends('layouts.app')

@section('title', 'Laporan Transaksi AC')

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
        <h1 class="h3 mb-0 text-gray-800">Laporan Transaksi Servis AC</h1>
    </div>

    {{-- Filter Card --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Laporan</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.ac.laporan.index') }}">
                <div class="form-row align-items-end">
                    
                    {{-- Filter Dari Tanggal --}}
                    <div class="col-md-4 mb-3">
                        <label for="tanggal_dari">Dari Tanggal</label>
                        <input type="date" name="tanggal_dari" id="tanggal_dari" value="{{ request('tanggal_dari') }}" class="form-control">
                    </div>

                    {{-- Filter Sampai Tanggal --}}
                    <div class="col-md-4 mb-3">
                        <label for="tanggal_sampai">Sampai Tanggal</label>
                        <input type="date" name="tanggal_sampai" id="tanggal_sampai" value="{{ request('tanggal_sampai') }}" class="form-control">
                    </div>

                    {{-- Action Buttons --}}
                    <div class="col-md-4 mb-3">
                        <button type="submit" class="btn btn-primary mr-2">
                            <i class="fas fa-search fa-sm"></i> Cari
                        </button>
                        <a href="{{ route('admin.ac.laporan.index') }}" class="btn btn-secondary">
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
        <div class="col-xl-6 col-md-6 mb-4 mb-md-0">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Pendapatan (Status Lunas)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-check-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Export Buttons --}}
        <div class="col-xl-6 col-md-6 d-flex justify-content-end align-items-center">
            <a href="{{ route('admin.ac.laporan.export.pdf', request()->all()) }}" target="_blank" class="btn btn-danger mr-2 shadow-sm">
                <i class="fas fa-file-pdf fa-sm text-white-50"></i> Export PDF
            </a>
            <a href="{{ route('admin.ac.laporan.export.excel', request()->all()) }}" target="_blank" class="btn btn-success shadow-sm">
                <i class="fas fa-file-excel fa-sm text-white-50"></i> Export Excel
            </a>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Transaksi</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th>No Invoice</th>
                            <th>Pelanggan</th>
                            <th>Layanan</th>
                            <th>Teknisi</th>
                            <th>Tgl Kunjungan</th>
                            <th>Tgl Pembayaran</th>
                            <th class="text-right">Total Harga</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($laporan as $index => $item)
                            <tr>
                                <td class="text-center align-middle">{{ $index + 1 }}</td>
                                <td class="align-middle">{{ $item->invoice_no ?? '-' }}</td>
                                <td class="align-middle">{{ $item->bookingAc->user->name ?? '-' }}</td>
                                <td class="align-middle">{{ $item->bookingAc->layanan->nama_layanan ?? ($item->bookingAc->layanan->nama ?? '-') }}</td>
                                <td class="align-middle">{{ $item->bookingAc->teknisi->name ?? '-' }}</td>
                                <td class="align-middle">{{ $item->bookingAc->tgl_kunjungan ?? '-' }}</td>
                                <td class="align-middle">{{ \Carbon\Carbon::parse($item->tgl_bayar)->format('d-m-Y H:i') }}</td>
                                <td class="align-middle text-right font-weight-bold">
                                    Rp {{ number_format($item->total_harga, 0, ',', '.') }}
                                </td>
                                <td class="align-middle text-center">
                                    @if(strtolower($item->status) == 'lunas')
                                        <span class="badge badge-success px-2 py-1">{{ $item->status }}</span>
                                    @elseif(strtolower($item->status) == 'menunggu' || strtolower($item->status) == 'pending' || strtolower($item->status) == 'belum lunas')
                                        <span class="badge badge-warning px-2 py-1">{{ $item->status }}</span>
                                    @elseif(strtolower($item->status) == 'dibatalkan' || strtolower($item->status) == 'batal')
                                        <span class="badge badge-danger px-2 py-1">{{ $item->status }}</span>
                                    @else
                                        <span class="badge badge-secondary px-2 py-1">{{ $item->status ?? 'Unknown' }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
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
        <div class="card-footer">
            {{ $laporan->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
