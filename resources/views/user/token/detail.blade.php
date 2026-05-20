@extends('layouts.publik')

@section('title', 'Detail Booking Futsal')

@push('styles')
<style>
    /* ── Detail Card Layout ── */
    .detail-card {
        border-radius: 14px;
        overflow: hidden;
    }
    .detail-card .card-header {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%) !important;
        border: none;
        padding: 1.2rem 1.5rem;
    }
    .detail-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .detail-row {
        padding: 14px 0;
        border-bottom: 1px solid #e3e6f0;
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
    }
    .detail-row:last-child {
        border-bottom: none;
    }
    .detail-label {
        font-size: 0.82rem;
        color: #858796;
        font-weight: 500;
        width: 160px;
        flex-shrink: 0;
        padding-top: 2px;
    }
    .detail-value {
        flex: 1;
        min-width: 0;
        font-size: 0.95rem;
        font-weight: 600;
        color: #3a3b45;
        word-break: break-word;
    }
    .badge-booking {
        font-size: 0.82rem;
        padding: 6px 14px;
        border-radius: 20px;
        font-weight: 600;
        letter-spacing: 0.3px;
        white-space: nowrap;
        display: inline-block;
    }
    .alert-booking {
        border-radius: 10px;
        font-size: 0.9rem;
        line-height: 1.5;
        border: none;
    }
    .btn-batal-booking {
        font-weight: 600;
        border-radius: 10px;
        padding: 10px 24px;
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    .btn-batal-booking:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(231,74,59,0.3);
    }
    .waktu-main-event .separator {
        display: inline;
        margin: 0 4px;
    }

    /* ── Mobile-first responsive ── */
    @media (max-width: 575.98px) {
        .detail-row {
            flex-direction: column;
            padding: 10px 0;
        }
        .detail-label {
            width: 100%;
            font-size: 0.76rem;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .detail-value {
            font-size: 0.92rem;
        }
        .alert-booking {
            font-size: 0.85rem;
        }
        .btn-batal-booking {
            width: 100%;
            padding: 12px;
        }
        .detail-card .card-body {
            padding: 1rem !important;
        }
        .detail-card .card-header {
            padding: 1rem 1.2rem;
        }
        .detail-card .card-header h5 {
            font-size: 1rem;
        }
        .waktu-main-event .separator {
            display: block;
            margin: 2px 0;
        }
        .w-sm-auto { width: auto !important; }
        @media (max-width: 575.98px) {
            .w-100 { width: 100% !important; }
        }
    }
</style>
@endpush

@section('content')
<div class="container py-4 py-md-5 mt-3 mt-md-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow-sm border-0 detail-card">
                <div class="card-header text-white">
                    <h5 class="mb-0 py-1 font-weight-bold">
                        <i class="fas fa-ticket-alt mr-2"></i> Detail Booking
                    </h5>
                </div>
                <div class="card-body p-3 p-md-4">

                    {{-- Flash Messages --}}
                    @if(session('error'))
                        <div class="alert alert-danger alert-booking alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-exclamation-circle mr-2 mt-1"></i>
                                <div>{{ session('error') }}</div>
                            </div>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success alert-booking alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-check-circle mr-2 mt-1"></i>
                                <div>{{ session('success') }}</div>
                            </div>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    {{-- Menunggu Konfirmasi Admin --}}
                    @if($bookingFutsal->status === 'menunggu')
                        <div class="alert alert-warning alert-booking d-flex align-items-start mb-3">
                            <i class="fas fa-clock mr-2 mt-1"></i>
                            <div>
                                <strong>Menunggu Konfirmasi Admin/Kasir</strong><br>
                                <span>Booking Anda masih menunggu konfirmasi admin/kasir sebelum dianggap sah. Anda akan mendapat notifikasi WhatsApp setelah dikonfirmasi.</span>
                            </div>
                        </div>
                    @endif

                    <div class="alert alert-info alert-booking mb-3">
                        <i class="fas fa-bookmark mr-1"></i>
                        <strong>PENTING:</strong> Simpan link halaman ini untuk mengakses informasi booking Anda di kemudian hari.
                    </div>

                    {{-- Detail Booking (Card-style, mobile-first) --}}
                    <ul class="detail-list mt-3">
                        {{-- Kode Booking --}}
                        <li class="detail-row">
                            <div class="detail-label">
                                <i class="fas fa-hashtag mr-1"></i> Kode Booking
                            </div>
                            <div class="detail-value">
                                {{ $bookingFutsal->booking->kode_booking ?? 'BKG-'.$bookingFutsal->booking->id }}
                            </div>
                        </li>

                        {{-- Status --}}
                        <li class="detail-row">
                            <div class="detail-label">
                                <i class="fas fa-flag mr-1"></i> Status
                            </div>
                            <div class="detail-value">
                                @if($bookingFutsal->status == 'menunggu')
                                    <span class="badge badge-warning badge-booking text-dark">Menunggu</span>
                                @elseif($bookingFutsal->status == 'dikonfirmasi')
                                    <span class="badge badge-primary badge-booking">Dikonfirmasi</span>
                                @elseif($bookingFutsal->status == 'selesai')
                                    <span class="badge badge-success badge-booking">Selesai</span>
                                @elseif($bookingFutsal->status == 'dibatalkan')
                                    <span class="badge badge-danger badge-booking">Dibatalkan</span>
                                @else
                                    <span class="badge badge-secondary badge-booking">{{ ucfirst($bookingFutsal->status) }}</span>
                                @endif
                            </div>
                        </li>

                        {{-- Nama Lapangan --}}
                        <li class="detail-row">
                            <div class="detail-label">
                                <i class="fas fa-futbol mr-1"></i> Lapangan
                            </div>
                            <div class="detail-value">{{ $bookingFutsal->lapangan->nama }}</div>
                        </li>

                        {{-- Waktu Main --}}
                        <li class="detail-row">
                            <div class="detail-label">
                                <i class="fas fa-calendar-alt mr-1"></i> Waktu Main
                            </div>
                            <div class="detail-value">
                                @if($bookingFutsal->jenis_pembayaran === 'event')
                                    <div class="waktu-main-event">
                                        <span>{{ \Carbon\Carbon::parse($bookingFutsal->start_datetime)->locale('id')->translatedFormat('l, d F Y') }}</span>
                                        <span class="separator text-muted">s/d</span>
                                        <span>{{ \Carbon\Carbon::parse($bookingFutsal->end_datetime)->locale('id')->translatedFormat('l, d F Y') }}</span>
                                        <div class="small text-muted mt-1">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ \Carbon\Carbon::parse($bookingFutsal->start_datetime)->format('H:i') }}
                                            –
                                            {{ \Carbon\Carbon::parse($bookingFutsal->end_datetime)->format('H:i') }}
                                        </div>
                                    </div>
                                @else
                                    <div>
                                        {{ \Carbon\Carbon::parse($bookingFutsal->start_datetime)->locale('id')->translatedFormat('l, d F Y') }}
                                    </div>
                                    <div class="small text-muted mt-1">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ \Carbon\Carbon::parse($bookingFutsal->start_datetime)->format('H:i') }} – {{ \Carbon\Carbon::parse($bookingFutsal->end_datetime)->format('H:i') }}
                                    </div>
                                @endif
                            </div>
                        </li>

                        {{-- Jenis Booking --}}
                        <li class="detail-row">
                            <div class="detail-label">
                                <i class="fas fa-tag mr-1"></i> Jenis Booking
                            </div>
                            <div class="detail-value">
                                @php
                                    $labelJenisBooking = match($bookingFutsal->jenis_pembayaran) {
                                        'event'  => 'Booking Event',
                                        'paket'  => 'Paket Membership',
                                        default  => 'Reguler',
                                    };
                                    $badgeJenisBooking = match($bookingFutsal->jenis_pembayaran) {
                                        'event'  => 'badge-warning text-dark',
                                        'paket'  => 'badge-info',
                                        default  => 'badge-primary',
                                    };
                                @endphp
                                <span class="badge {{ $badgeJenisBooking }} badge-booking">{{ $labelJenisBooking }}</span>
                            </div>
                        </li>

                        {{-- Nama Pemesan --}}
                        <li class="detail-row">
                            <div class="detail-label">
                                <i class="fas fa-user mr-1"></i> Nama Pemesan
                            </div>
                            <div class="detail-value">{{ $bookingFutsal->nama_pemesan }}</div>
                        </li>

                        {{-- No HP --}}
                        <li class="detail-row">
                            <div class="detail-label">
                                <i class="fas fa-phone mr-1"></i> No HP
                            </div>
                            <div class="detail-value">{{ $bookingFutsal->no_hp }}</div>
                        </li>

                        {{-- Metode Pembayaran --}}
                        <li class="detail-row">
                            <div class="detail-label">
                                <i class="fas fa-credit-card mr-1"></i> Pembayaran
                            </div>
                            <div class="detail-value">
                                @php
                                    $pembayaranFutsal = $bookingFutsal->booking->pembayaranFutsal->first();
                                    $namaMetodeBayar = $pembayaranFutsal?->tipePembayaran?->nama ?? null;
                                @endphp
                                @if($namaMetodeBayar)
                                    {{ $namaMetodeBayar }}
                                @else
                                    <span class="text-muted" style="font-weight:400;">
                                        <i class="fas fa-clock mr-1"></i>Menunggu proses kasir
                                    </span>
                                @endif
                            </div>
                        </li>
                    </ul>

                    {{-- Action Buttons --}}
                    <div class="mt-4 pt-3 border-top">
                        @if($bookingFutsal->status == 'menunggu')
                            @php
                                $bisaBatal = \Carbon\Carbon::parse($bookingFutsal->start_datetime)->gt(now()->addHours(2));
                            @endphp
                            @if($bisaBatal)
                                <div class="d-flex flex-column flex-sm-row justify-content-end">
                                    <a href="{{ route('user.token.batalkan', $token) }}"
                                       class="btn btn-danger btn-batal-booking">
                                        <i class="fas fa-times-circle mr-1"></i> Batalkan Booking
                                    </a>
                                </div>
                            @else
                                <div class="text-right text-sm-right">
                                    <button class="btn btn-secondary btn-batal-booking w-100 w-sm-auto" disabled>
                                        <i class="fas fa-times-circle mr-1"></i> Batalkan Booking
                                    </button>
                                    <div class="small text-muted mt-2">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Pembatalan tidak tersedia kurang dari 2 jam sebelum waktu main.
                                    </div>
                                </div>
                            @endif
                        @elseif($bookingFutsal->status == 'dibatalkan')
                            <div class="text-right">
                                <span class="text-muted small">
                                    <i class="fas fa-ban mr-1"></i>Booking ini sudah dibatalkan.
                                </span>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
