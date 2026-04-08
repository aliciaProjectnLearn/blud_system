@extends('layouts.app')
@section('title', 'Riwayat Pembayaran')
@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Riwayat Pembayaran</h1>
        <a href="{{ route('user.kantin.dashboard') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Semua Riwayat Pembayaran</h6>
        </div>
        <div class="card-body">
            @if($riwayat->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-receipt fa-4x text-gray-300 mb-3"></i>
                    <p class="text-muted">Belum ada riwayat pembayaran.</p>
                </div>
            @else
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Unit</th>
                            <th>Termin</th>
                            <th>Jumlah Tagihan</th>
                            <th>Tanggal Bayar</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riwayat as $r)
                        @php
                            $badge = match($r->status) {
                                'lunas'      => 'success',
                                'verifikasi' => 'info',
                                'menunggu'   => 'warning',
                                default      => 'secondary',
                            };
                        @endphp
                        <tr>
                            <td>{{ $r->sewaRuko->ruko->kode_unit ?? '-' }}</td>
                            <td>Termin {{ $r->termin }}</td>
                            <td>Rp {{ number_format($r->jumlah_tagihan, 0, ',', '.') }}</td>
                            <td>{{ $r->tgl_bayar ? \Carbon\Carbon::parse($r->tgl_bayar)->format('d M Y') : '-' }}</td>
                            <td><span class="badge badge-{{ $badge }}">{{ ucfirst($r->status) }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
