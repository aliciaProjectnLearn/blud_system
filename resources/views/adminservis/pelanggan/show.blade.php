@extends('layouts.app')

@section('title', 'Detail Pelanggan Servis')

@section('content')
<div class="mb-4 d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center">
        <a href="{{ route('admin.servis.pelanggan.index') }}" class="btn btn-outline-secondary btn-circle mr-3 shadow-sm">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0 text-gray-800">Detail Pelanggan</h1>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent p-0 mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.servis.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.servis.pelanggan.index') }}">Pelanggan</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detail</li>
        </ol>
    </nav>
</div>

<div class="row">
    <!-- Profil Pelanggan -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">Profil Pelanggan</h6>
            </div>
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <img class="img-profile rounded-circle shadow" src="https://ui-avatars.com/api/?name={{ urlencode($pelanggan->name) }}&background=4e73df&color=ffffff&size=128" style="width: 128px;">
                </div>
                <h4 class="font-weight-bold text-dark">{{ $pelanggan->nama_lengkap ?? $pelanggan->name }}</h4>
                <p class="text-muted mb-4"><i class="fas fa-envelope mr-1"></i> {{ $pelanggan->email }}</p>
                
                <div class="row no-gutters border rounded bg-light p-3">
                    <div class="col-6 border-right">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Booking</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pelanggan->booking_servis_count }}x</div>
                    </div>
                    <div class="col-6">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Biaya</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($pelanggan->total_pengeluaran ?? 0, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white border-top-0">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td width="30%" class="text-muted">Username</td>
                        <td class="font-weight-bold">: {{ $pelanggan->username }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">No. HP</td>
                        <td class="font-weight-bold">: {{ $pelanggan->no_hp ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">NIK</td>
                        <td class="font-weight-bold">: {{ $pelanggan->nik ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Alamat</td>
                        <td class="font-weight-bold">: {{ $pelanggan->alamat ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Riwayat Booking -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Riwayat Booking Servis</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="bg-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Kode</th>
                                <th>Kendaraan</th>
                                <th>Layanan</th>
                                <th class="text-right">Biaya</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $booking)
                            <tr>
                                <td>
                                    {{ $booking->tanggal_booking->translatedFormat('d M Y') }}
                                    <div class="text-xs text-muted">{{ \Carbon\Carbon::parse($booking->jam_booking)->format('H:i') }}</div>
                                </td>
                                <td><code>{{ $booking->kode_booking }}</code></td>
                                <td>
                                    <span class="badge badge-dark">{{ $booking->nomor_plat }}</span>
                                    <div class="text-xs">{{ ucfirst($booking->tipe_kendaraan) }} {{ $booking->merek_kendaraan }}</div>
                                </td>
                                <td>{{ $booking->layananServis->nama_layanan ?? '-' }}</td>
                                <td class="text-right">
                                    Rp {{ number_format(optional($booking->pembayaranServis)->total_biaya ?? 0, 0, ',', '.') }}
                                </td>
                                <td>
                                    @php
                                        $status = strtolower($booking->status);
                                        $badge = 'secondary';
                                        if($status == 'selesai') $badge = 'success';
                                        elseif($status == 'proses') $badge = 'primary';
                                        elseif($status == 'menunggu') $badge = 'warning';
                                        elseif($status == 'dibatalkan') $badge = 'danger';
                                    @endphp
                                    <span class="badge badge-{{ $badge }}">{{ ucfirst($booking->status) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted italic">Tidak ada riwayat booking.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $history->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
