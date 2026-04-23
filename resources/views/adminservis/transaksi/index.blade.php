@extends('layouts.app')

@section('title', 'Manajemen Transaksi Servis')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Manajemen Transaksi Servis</h1>
            <p class="mb-0 text-muted small">Fitur Read-Only — Admin hanya dapat memantau dan menganalisis transaksi.</p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow mb-4 border-left-primary">
        <div class="card-body">
            <form action="{{ route('adminservis.transaksi.index') }}" method="GET" id="filterForm">
                <div class="row align-items-end">
                    <div class="col-md-3 mb-3">
                        <label class="form-label small font-weight-bold">Dari Tanggal</label>
                        <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label small font-weight-bold">Sampai Tanggal</label>
                        <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label small font-weight-bold">Status</label>
                        <select name="status" class="form-control form-control-sm">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="dibayar" {{ request('status') == 'dibayar' ? 'selected' : '' }}>Dibayar</option>
                            <option value="gagal" {{ request('status') == 'gagal' ? 'selected' : '' }}>Gagal</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label small font-weight-bold">Cari Pelanggan</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama pelanggan..." class="form-control">
                        </div>
                    </div>
                    <div class="col-md-1 mb-3">
                        <div class="btn-group w-100">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                            <a href="{{ route('adminservis.transaksi.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-undo fa-sm"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card shadow mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 text-xs font-weight-bold text-uppercase" width="50">No</th>
                            <th class="px-4 py-3 text-xs font-weight-bold text-uppercase">Nama Pelanggan</th>
                            <th class="px-4 py-3 text-xs font-weight-bold text-uppercase">Tanggal Transaksi</th>
                            <th class="px-4 py-3 text-xs font-weight-bold text-uppercase text-right">Total Pembayaran</th>
                            <th class="px-4 py-3 text-xs font-weight-bold text-uppercase text-center">Metode</th>
                            <th class="px-4 py-3 text-xs font-weight-bold text-uppercase text-center">Status</th>
                            <th class="px-4 py-3 text-xs font-weight-bold text-uppercase text-center" width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transaksi as $index => $item)
                            <tr>
                                <td class="px-4 py-3 align-middle text-muted small">{{ $transaksi->firstItem() + $index }}</td>
                                <td class="px-4 py-3 align-middle font-weight-bold text-gray-800">
                                    {{ $item->bookingServis->pelanggan->name ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    {{ $item->tanggal_bayar ? \Carbon\Carbon::parse($item->tanggal_bayar)->translatedFormat('d M Y') : '-' }}
                                </td>
                                <td class="px-4 py-3 align-middle text-right font-weight-bold text-primary">
                                    Rp {{ number_format($item->total_biaya, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 align-middle text-center text-uppercase small font-weight-bold text-muted">
                                    {{ $item->tipe_pembayaran }}
                                </td>
                                <td class="px-4 py-3 align-middle text-center">
                                    @php
                                        $statusClass = 'secondary';
                                        $statusLabel = $item->status_pembayaran;
                                        
                                        if (in_array($item->status_pembayaran, ['belum_bayar', 'dp'])) {
                                            $statusClass = 'warning';
                                            $statusLabel = 'Pending';
                                        } elseif ($item->status_pembayaran == 'lunas') {
                                            $statusClass = 'success';
                                            $statusLabel = 'Dibayar';
                                        }
                                    @endphp
                                    <span class="badge badge-{{ $statusClass }} px-2 py-1 text-uppercase" style="font-size: 0.7rem;">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 align-middle text-center">
                                    <a href="{{ route('adminservis.transaksi.show', $item->id) }}" class="btn btn-info btn-sm btn-circle shadow-sm" title="Lihat Detail">
                                        <i class="fas fa-eye fa-sm"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-receipt fa-3x mb-3 opacity-25"></i>
                                        <p class="mb-0">Tidak ada data transaksi yang ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Pagination -->
        @if ($transaksi->hasPages())
            <div class="card-footer bg-white py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="small text-muted">
                        Menampilkan {{ $transaksi->firstItem() }} hingga {{ $transaksi->lastItem() }} dari {{ $transaksi->total() }} entri
                    </div>
                    <div>
                        {{ $transaksi->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .text-xs { font-size: 0.75rem; }
    .opacity-25 { opacity: 0.25; }
    .btn-circle {
        width: 30px;
        height: 30px;
        padding: 6px 0;
        border-radius: 15px;
        text-align: center;
        font-size: 12px;
        line-height: 1.42857;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(78, 115, 223, 0.03);
    }
</style>
@endpush
