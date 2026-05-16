@extends('layouts.publik')

@section('title', 'Dashboard Servis Motor & Mobil')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Servis Motor & Mobil</h1>
        <a href="{{ route('user.servis.booking') }}#servis-section" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Buat Booking Baru
        </a>
    </div>

    <!-- Alert for active only -->
    <div class="alert alert-info border-left-info shadow-sm py-2 px-3 mb-4">
        <i class="fas fa-info-circle mr-2"></i>
        Untuk melihat detail atau histori servis, gunakan 
        <strong>link akses</strong> yang dikirim ke WhatsApp Anda saat booking.
    </div>

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('user.servis.index') }}" method="GET" class="row">
                <div class="col-md-4 mb-3">
                    <label class="small font-weight-bold">Status</label>
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>Diproses</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>
                <div class="col-md-5 mb-3">
                    <label class="small font-weight-bold">Cari (Plat/Tanggal)</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Contoh: B 1234 ABC atau 2024-04..." class="form-control">
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-filter fa-sm"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Booking Servis</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="px-4 py-3">Tanggal & Jam</th>
                            <th class="px-4 py-3">Kendaraan</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-right">Total Biaya</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                            <tr>
                                <td class="px-4 py-3 align-middle">
                                    <div class="font-weight-bold">{{ $booking->tanggal_booking->translatedFormat('d F Y') }}</div>
                                    <div class="small text-muted">{{ $booking->jam_booking }} WIB</div>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <div class="text-uppercase small font-weight-bold">{{ $booking->merek_kendaraan }}</div>
                                    <div class="badge badge-dark">{{ $booking->nomor_plat }}</div>
                                </td>
                                <td class="px-4 py-3 align-middle text-center">
                                    @php
                                        $badgeClass = 'secondary';
                                        if($booking->status == 'menunggu') $badgeClass = 'warning';
                                        elseif($booking->status == 'diproses') $badgeClass = 'primary';
                                        elseif($booking->status == 'selesai') $badgeClass = 'success';
                                        elseif($booking->status == 'batal') $badgeClass = 'danger';
                                    @endphp
                                    <span class="badge badge-{{ $badgeClass }} px-3 py-2 text-uppercase" style="min-width: 100px;">
                                        {{ $booking->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 align-middle text-right">
                                    @php
                                        $total = $booking->rincianServis->sum('subtotal');
                                    @endphp
                                    <div class="font-weight-bold text-primary">
                                        {{ $total > 0 ? 'Rp ' . number_format($total, 0, ',', '.') : '-' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-middle text-center">
                                    @if($booking->access_token)
                                        <a href="{{ route('user.servis.token.show', $booking->access_token) }}" class="btn btn-info btn-sm shadow-sm">
                                            <i class="fas fa-eye fa-sm"></i> Detail
                                        </a>
                                    @else
                                        <span class="btn btn-secondary btn-sm disabled">
                                            <i class="fas fa-eye fa-sm"></i> Detail
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="fas fa-calendar-check fa-3x text-gray-300"></i>
                                    </div>
                                    <h5 class="text-gray-600">Tidak ada booking aktif</h5>
                                    <p class="text-muted">Semua booking Anda telah selesai atau dibatalkan.</p>
                                    <div class="mt-3">
                                        <a href="{{ route('user.servis.katalog') }}" class="btn btn-primary">
                                            Buat Booking Baru
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($bookings->hasPages())
            <div class="card-footer bg-white">
                {{ $bookings->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
</div>
@endsection