@extends('layouts.app')

@section('title', 'Detail Pekerjaan - ' . $pekerjaan->kode_booking)

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        Detail Pekerjaan
        <small class="text-muted" style="font-size:.65em;">
            <code>{{ $pekerjaan->kode_booking }}</code>
        </small>
    </h1>
    <a href="{{ route('teknisiservis.dashboard') }}" class="btn btn-secondary btn-sm shadow-sm">
        <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali ke Dashboard
    </a>
</div>

<div class="row">
    {{-- ── Informasi Booking ───────────────────────────────────── --}}
    <div class="col-lg-7 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle mr-1"></i> Informasi Booking
                </h6>
                @php
                    $badgeMap = [
                        'menunggu'     => 'info',
                        'dikonfirmasi' => 'primary',
                        'proses'       => 'warning',
                        'selesai'      => 'success',
                        'batal'        => 'danger',
                    ];
                    $badgeColor = $badgeMap[$pekerjaan->status] ?? 'secondary';
                @endphp
                <span class="badge badge-{{ $badgeColor }} px-3 py-2" style="font-size:.85rem;">
                    {{ ucfirst($pekerjaan->status) }}
                </span>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tbody>
                        <tr>
                            <th width="40%" class="text-muted">Kode Booking</th>
                            <td><code class="font-weight-bold">{{ $pekerjaan->kode_booking }}</code></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Nama Pelanggan</th>
                            <td>{{ $pekerjaan->user->nama_lengkap ?? $pekerjaan->user->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Layanan</th>
                            <td>{{ $pekerjaan->layananServis->nama_layanan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Tipe Kendaraan</th>
                            <td>
                                <i class="fas fa-{{ $pekerjaan->tipe_kendaraan === 'motor' ? 'motorcycle' : 'car' }} mr-1"></i>
                                {{ ucfirst($pekerjaan->tipe_kendaraan) }}
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Merek Kendaraan</th>
                            <td>{{ $pekerjaan->merek_kendaraan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Nomor Plat</th>
                            <td>
                                <span class="badge badge-secondary" style="font-size:.9rem;letter-spacing:1px;">
                                    {{ $pekerjaan->nomor_plat }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Tahun Kendaraan</th>
                            <td>{{ $pekerjaan->tahun_kendaraan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Tanggal Booking</th>
                            <td>{{ $pekerjaan->tanggal_booking->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Jam Booking</th>
                            <td>{{ $pekerjaan->jam_booking }}</td>
                        </tr>
                    </tbody>
                </table>

                {{-- Keluhan --}}
                <hr>
                <h6 class="font-weight-bold text-gray-700 mb-2">
                    <i class="fas fa-comment-medical mr-1"></i> Keluhan Pelanggan
                </h6>
                <div class="p-3 bg-light rounded border">
                    {{ $pekerjaan->keluhan ?? '-' }}
                </div>

                {{-- Catatan Admin --}}
                @if($pekerjaan->catatan_admin)
                <hr>
                <h6 class="font-weight-bold text-gray-700 mb-2">
                    <i class="fas fa-sticky-note mr-1"></i> Catatan Admin
                </h6>
                <div class="p-3 bg-warning-light rounded border border-warning">
                    <i class="fas fa-exclamation-circle text-warning mr-1"></i>
                    {{ $pekerjaan->catatan_admin }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Rincian Servis ──────────────────────────────────────── --}}
    <div class="col-lg-5 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">
                    <i class="fas fa-list-alt mr-1"></i> Rincian Servis
                </h6>
            </div>
            <div class="card-body">
                @if($pekerjaan->rincianServis && $pekerjaan->rincianServis->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Item / Produk</th>
                                    <th class="text-center">Qty</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pekerjaan->rincianServis as $rincian)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $rincian->item ?? $rincian->nama_produk ?? '-' }}</td>
                                    <td class="text-center">{{ $rincian->quantity ?? $rincian->qty ?? '-' }}</td>
                                    <td>{{ $rincian->catatan ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-clipboard fa-3x mb-3 d-block text-gray-300"></i>
                        <p class="mb-0">Belum ada rincian servis.</p>
                        <small>Rincian akan ditambahkan oleh kasir setelah pekerjaan selesai.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ── Tombol Kembali (bawah) ──────────────────────────────────── --}}
<div class="row">
    <div class="col-12">
        <a href="{{ route('teknisiservis.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Dashboard
        </a>
        {{-- TIDAK ada tombol update/aksi apapun — read-only --}}
    </div>
</div>
@endsection
