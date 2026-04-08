@extends('layouts.app')
@section('title', 'Tagihan Saya')
@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tagihan Saya</h1>
        <a href="{{ route('user.kantin.dashboard') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Tagihan Belum Lunas</h6>
        </div>
        <div class="card-body">
            @if($tagihan->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <p class="text-muted">Tidak ada tagihan yang belum lunas.</p>
                </div>
            @else
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Unit</th>
                            <th>Termin</th>
                            <th>Jumlah Tagihan</th>
                            <th>Jatuh Tempo</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tagihan as $t)
                        <tr>
                            <td>{{ $t->sewaRuko->ruko->kode_unit ?? '-' }}</td>
                            <td>Termin {{ $t->termin }}</td>
                            <td>Rp {{ number_format($t->jumlah_tagihan, 0, ',', '.') }}</td>
                            <td class="{{ \Carbon\Carbon::parse($t->tgl_jatuh_tempo)->isPast() ? 'text-danger font-weight-bold' : '' }}">
                                {{ $t->tgl_jatuh_tempo ? \Carbon\Carbon::parse($t->tgl_jatuh_tempo)->format('d M Y') : '-' }}
                            </td>
                            <td><span class="badge badge-warning">{{ ucfirst($t->status) }}</span></td>
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
