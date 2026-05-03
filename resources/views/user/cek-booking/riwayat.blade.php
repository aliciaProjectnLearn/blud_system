@extends('layouts.publik')

@section('content')
<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            {{-- Header Card --}}
            <div class="card border-0 shadow-sm mb-4 overflow-hidden" style="border-radius: 16px;">
                <div class="card-body p-4 text-white" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center mr-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-history fa-lg"></i>
                        </div>
                        <div>
                            <h4 class="font-weight-bold mb-0">Riwayat Booking Anda</h4>
                            <p class="mb-0 opacity-75">Nomor Terdaftar: +{{ $no_hp }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($bookings->isEmpty())
                <div class="card border-0 shadow-sm text-center p-5" style="border-radius: 16px;">
                    <div class="mb-4">
                        <i class="fas fa-calendar-times fa-4x text-gray-300"></i>
                    </div>
                    <h5 class="text-gray-800 font-weight-bold">Tidak Ada Booking Aktif</h5>
                    <p class="text-muted">Nomor Anda tidak terdaftar pada layanan aktif kami saat ini.</p>
                    <div class="mt-3">
                        <a href="{{ route('user.gateway') }}" class="btn btn-primary px-4 py-2 rounded-pill font-weight-bold shadow-sm">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Gateway
                        </a>
                    </div>
                </div>
            @else
                <div class="row">
                    @foreach($bookings as $booking)
                        <div class="col-md-6 mb-4">
                            <div class="card border-0 shadow-sm h-100 overflow-hidden card-hover" style="border-radius: 16px;">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <span class="badge badge-pill badge-primary mb-2 px-3 py-2" style="font-size: 0.75rem;">
                                                <i class="fas fa-tag mr-1"></i> {{ $booking->kode_booking ?? 'BKG-'.$booking->id }}
                                            </span>
                                            <h5 class="font-weight-bold text-gray-800 mb-1">
                                                {{ $booking->unit_name ?? 'Layanan BLUD' }}
                                            </h5>
                                        </div>
                                        @php
                                            $status = strtolower($booking->status ?? $booking->status_sewa ?? 'unknown');
                                            $badgeClass = 'badge-secondary';
                                            if(in_array($status, ['aktif', 'dikonfirmasi', 'selesai'])) $badgeClass = 'badge-success';
                                            if(in_array($status, ['pending', 'menunggu', 'bayar'])) $badgeClass = 'badge-warning';
                                            if(in_array($status, ['proses'])) $badgeClass = 'badge-info';
                                            if(in_array($status, ['dibatalkan', 'ditolak'])) $badgeClass = 'badge-danger';
                                        @endphp
                                        <span class="badge badge-{{ $badgeClass }} px-3 py-2" style="border-radius: 8px;">
                                            {{ strtoupper($status) }}
                                        </span>
                                    </div>

                                    <div class="mb-4">
                                        <div class="d-flex align-items-center mb-2 text-muted">
                                            <i class="far fa-calendar-alt mr-2" style="width: 20px;"></i>
                                            <span class="small">{{ \Carbon\Carbon::parse($booking->start_datetime)->format('d M Y') }}</span>
                                        </div>
                                        <div class="d-flex align-items-center text-muted">
                                            <i class="far fa-clock mr-2" style="width: 20px;"></i>
                                            <span class="small">{{ \Carbon\Carbon::parse($booking->start_datetime)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_datetime)->format('H:i') }}</span>
                                        </div>
                                    </div>

                                    <a href="{{ url('sewa-token/' . $booking->access_token) }}" 
                                       class="btn btn-outline-primary btn-block rounded-pill font-weight-bold transition-all">
                                        <i class="fas fa-eye mr-2"></i> Lihat Detail Booking
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-4">
                    <p class="text-muted small">
                        <i class="fas fa-info-circle mr-1"></i>
                        Data di atas adalah booking aktif. Sesi Anda akan berakhir dalam 15 menit.
                    </p>
                    <a href="{{ route('user.gateway') }}" class="btn btn-link text-muted">
                        <i class="fas fa-sign-out-alt mr-1"></i> Keluar dari Sesi
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .card-hover { transition: all 0.3s ease; }
    .card-hover:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important; }
    .btn-outline-primary:hover { background-color: #4e73df; border-color: #4e73df; }
    .opacity-75 { opacity: 0.75; }
</style>
@endsection
