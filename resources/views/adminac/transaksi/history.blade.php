@extends('layouts.app')

@section('title', 'Histori Transaksi AC')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-history mr-2 text-primary"></i>Histori Pembayaran
    </h1>
    <a href="{{ route('admin.ac.transaksi.index') }}" class="btn btn-secondary btn-sm shadow-sm">
        <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali ke Daftar Transaksi
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-white border-bottom-0">
        <h6 class="m-0 font-weight-bold text-primary">
            Daftar Pembayaran Selesai (Dibayar)
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" width="100%" cellspacing="0">
                <thead class="bg-light text-dark">
                    <tr>
                        <th class="pl-4" width="5%">No</th>
                        <th>No. Invoice</th>
                        <th>Pelanggan</th>
                        <th class="text-right">Total Tagihan</th>
                        <th class="text-center">Tgl. Bayar</th>
                        <th class="pr-4 text-center" width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $index => $transaksi)
                        <tr>
                            <td class="pl-4 align-middle font-weight-bold">{{ $transaksis->firstItem() + $index }}</td>
                            <td class="align-middle">
                                <span class="badge badge-light p-2 border font-weight-bold text-success">
                                    {{ $transaksi->invoice_no }}
                                </span>
                            </td>
                            <td class="align-middle text-dark font-weight-bold">{{ $transaksi->bookingAc->user->name ?? 'N/A' }}</td>
                            <td class="align-middle text-right font-weight-bold text-dark">
                                Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}
                            </td>
                            <td class="align-middle text-center">
                                <div class="text-success small font-weight-bold">
                                    <i class="fas fa-check-circle mr-1"></i> {{ $transaksi->tgl_bayar ? \Carbon\Carbon::parse($transaksi->tgl_bayar)->translatedFormat('d M Y') : '-' }}
                                </div>
                            </td>
                            <td class="pr-4 align-middle text-center">
                                <a href="{{ route('admin.ac.transaksi.show', $transaksi->id) }}" class="btn btn-outline-info btn-sm shadow-sm">
                                    <i class="fas fa-file-invoice mr-1 text-info"></i> Invoice
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 py-sm-5 text-muted">
                                <i class="fas fa-info-circle fa-2x mb-3 opacity-25"></i>
                                <p class="mb-0">Belum ada histori pembayaran yang tercatat.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($transaksis->hasPages())
    <div class="card-footer bg-white">
        <div class="d-flex justify-content-center">
            {{ $transaksis->links() }}
        </div>
    </div>
    @endif
</div>
@endsection
