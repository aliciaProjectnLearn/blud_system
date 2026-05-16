@extends('layouts.publik')

@section('title', 'Detail Booking AC')

@push('styles')
<style>
    /* Modern Aesthetic Customizations */
    body {
        background-color: #f4f7f6;
    }
    
    .status-hero {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        padding: 40px 30px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(78, 115, 223, 0.2);
        margin-bottom: -40px;
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        overflow: hidden;
    }

    .status-hero::after {
        content: '\f2dc'; /* FontAwesome snowflake */
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        right: -20px;
        top: -20px;
        font-size: 10rem;
        opacity: 0.05;
        transform: rotate(15deg);
    }

    .modern-card {
        background: #ffffff;
        border-radius: 16px;
        border: none;
        box-shadow: 0 15px 35px rgba(0,0,0,0.05);
        padding: 30px;
        margin-top: 20px;
        transition: transform 0.3s ease;
    }

    .info-group {
        display: flex;
        align-items: flex-start;
        margin-bottom: 20px;
        padding: 15px;
        background: #f8fafc;
        border-radius: 12px;
        border-left: 4px solid #4e73df;
        transition: all 0.3s ease;
    }
    
    .info-group:hover {
        transform: translateX(5px);
        background: #f1f5f9;
    }

    .info-icon {
        background: #e2e8f0;
        color: #4e73df;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        margin-right: 15px;
        flex-shrink: 0;
    }

    .info-content label {
        display: block;
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #64748b;
        font-weight: 700;
        margin-bottom: 3px;
    }

    .info-content p {
        margin: 0;
        font-weight: 600;
        color: #1e293b;
        font-size: 1rem;
    }

    .status-badge {
        font-size: 1rem;
        padding: 8px 20px;
        border-radius: 30px;
        font-weight: 800;
        letter-spacing: 1px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .table-modern {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }
    
    .table-modern thead th {
        background-color: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        color: #475569;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.8rem;
        padding: 15px;
    }

    .table-modern tbody td {
        padding: 15px;
        vertical-align: middle;
        color: #334155;
        font-weight: 500;
    }

    .history-item {
        border-radius: 12px;
        margin-bottom: 15px;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
    }

    .history-item:hover {
        border-color: #4e73df;
        box-shadow: 0 5px 15px rgba(78, 115, 223, 0.1);
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<div class="container py-5 mt-3">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <!-- Hero Status -->
            <div class="status-hero animate__animated animate__fadeInDown">
                <div>
                    <h6 class="text-white-50 text-uppercase tracking-wide mb-1" style="letter-spacing: 1.5px; font-weight: 600;">Status Layanan Anda</h6>
                    <h2 class="font-weight-bold mb-0">Booking #{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</h2>
                    <p class="mb-0 mt-2 text-white-50"><i class="far fa-calendar-alt mr-2"></i> {{ \Carbon\Carbon::parse($booking->tgl_kunjungan)->translatedFormat('l, d F Y') }}</p>
                </div>
                <div class="text-right">
                    @php
                        $badgeClass = 'bg-warning text-dark';
                        $iconClass = 'fa-clock';
                        if($booking->status == 'proses') { $badgeClass = 'bg-info text-white'; $iconClass = 'fa-tools'; }
                        if($booking->status == 'selesai') { $badgeClass = 'bg-success text-white'; $iconClass = 'fa-check-circle'; }
                    @endphp
                    <span class="badge {{ $badgeClass }} status-badge text-uppercase"><i class="fas {{ $iconClass }} mr-2"></i>{{ $booking->status }}</span>
                </div>
            </div>

            <!-- Main Content Card -->
            <div class="modern-card animate__animated animate__fadeInUp" style="position: relative; z-index: 2;">
                
                <h5 class="font-weight-bold text-gray-800 border-bottom pb-3 mb-4"><i class="fas fa-file-invoice mr-2 text-primary"></i> Rincian Pesanan</h5>
                
                <div class="row">
                    <!-- Informasi Pelanggan -->
                    <div class="col-md-6">
                        <div class="info-group">
                            <div class="info-icon"><i class="fas fa-user"></i></div>
                            <div class="info-content">
                                <label>Nama Pelanggan</label>
                                <p>{{ $booking->nama_pelanggan }}</p>
                            </div>
                        </div>
                        
                        <div class="info-group">
                            <div class="info-icon"><i class="fab fa-whatsapp"></i></div>
                            <div class="info-content">
                                <label>Nomor WhatsApp</label>
                                <p>{{ $booking->no_hp }}</p>
                            </div>
                        </div>

                        <div class="info-group">
                            <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <div class="info-content">
                                <label>Alamat Lengkap</label>
                                <p class="text-break">{{ $booking->alamat }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Layanan -->
                    <div class="col-md-6">
                        <div class="info-group" style="border-left-color: #1cc88a;">
                            <div class="info-icon" style="color: #1cc88a; background: #e3fdf4;"><i class="fas fa-snowflake"></i></div>
                            <div class="info-content">
                                <label>Layanan Dipilih</label>
                                <p>{{ $booking->layanan->nama ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="info-group" style="border-left-color: #1cc88a;">
                            <div class="info-icon" style="color: #1cc88a; background: #e3fdf4;"><i class="fas fa-tag"></i></div>
                            <div class="info-content">
                                <label>Merek AC</label>
                                <p>{{ $booking->merek_ac ?? 'Tidak disebutkan' }}</p>
                            </div>
                        </div>

                        <div class="info-group" style="border-left-color: #1cc88a;">
                            <div class="info-icon" style="color: #1cc88a; background: #e3fdf4;"><i class="fas fa-exclamation-circle"></i></div>
                            <div class="info-content">
                                <label>Detail Keluhan</label>
                                <p class="text-break">{{ $booking->detail_keluhan ?? 'Tidak ada keluhan khusus' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 mb-4 border-bottom pb-2">
                    <h5 class="font-weight-bold text-gray-800"><i class="fas fa-tools mr-2 text-primary"></i> Rincian Servis & Sparepart</h5>
                    <p class="text-muted small">Daftar tindakan dan suku cadang yang digunakan oleh teknisi kami.</p>
                </div>

                @if($booking->detailServis->count() > 0)
                    <div class="table-responsive table-modern">
                        <table class="table table-hover mb-0 border-0">
                            <thead>
                                <tr>
                                    <th width="50%">Item / Tindakan</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-right">Harga Satuan</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($booking->detailServis as $ds)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 mr-3 text-secondary"><i class="fas fa-cog"></i></div>
                                            <div>
                                                <h6 class="mb-0 font-weight-bold text-dark">{{ $ds->item }}</h6>
                                                @if($ds->catatan) <small class="text-muted">{{ $ds->catatan }}</small> @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center font-weight-bold">{{ $ds->quantity }}</td>
                                    <td class="text-right">Rp {{ number_format($ds->harga, 0, ',', '.') }}</td>
                                    <td class="text-right font-weight-bold text-primary">Rp {{ number_format($ds->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            @if($booking->pembayaran)
                            <tfoot class="bg-light">
                                <tr>
                                    <th colspan="3" class="text-right py-3 text-uppercase font-weight-bold text-gray-600">Total Biaya Keseluruhan:</th>
                                    <th class="text-right py-3 text-success" style="font-size: 1.2rem; font-weight: 900;">Rp {{ number_format($booking->pembayaran->total_harga, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                @else
                    <div class="text-center py-5 bg-light rounded-lg border-dashed">
                        <div class="mb-3 text-gray-400"><i class="fas fa-clipboard-list fa-3x"></i></div>
                        <h6 class="text-gray-600 font-weight-bold">Belum Ada Rincian</h6>
                        <p class="text-muted small mb-0">Teknisi kami sedang atau belum mengerjakan pesanan Anda. Rincian akan muncul di sini setelah pekerjaan diselesaikan.</p>
                    </div>
                @endif
                
                @if($booking->foto_hasil)
                <div class="mt-5 mb-4 border-bottom pb-2">
                    <h5 class="font-weight-bold text-gray-800"><i class="fas fa-camera mr-2 text-primary"></i> Foto Dokumentasi</h5>
                </div>
                <div class="text-center">
                    <img src="{{ asset('storage/' . $booking->foto_hasil) }}" class="img-fluid rounded-lg shadow-sm" alt="Dokumentasi Pekerjaan" style="max-height: 400px; object-fit: cover; border: 4px solid #fff;">
                </div>
                @endif
            </div>

            <!-- Riwayat Section -->
            @if($riwayat->count() > 0)
            <div class="mt-5 mb-4 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                <h5 class="font-weight-bold text-gray-800"><i class="fas fa-history mr-2 text-secondary"></i> Riwayat Layanan AC Anda</h5>
                <p class="text-muted small">Pekerjaan yang pernah kami lakukan untuk nomor {{ $booking->no_hp }}</p>
                
                <div class="row mt-3">
                    @foreach($riwayat as $item)
                    <div class="col-md-6">
                        <div class="history-item bg-white p-3 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-3 mr-3 text-primary d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="fas fa-tools"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 font-weight-bold text-dark">{{ $item->layanan->nama ?? 'Layanan AC' }}</h6>
                                    <small class="text-muted"><i class="far fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::parse($item->tgl_kunjungan)->translatedFormat('d M Y') }}</small>
                                </div>
                            </div>
                            <div>
                                <span class="badge {{ $item->status == 'selesai' ? 'badge-success' : ($item->status == 'proses' ? 'badge-info' : 'badge-warning') }} p-2 shadow-sm text-uppercase" style="border-radius: 8px;">
                                    {{ $item->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            
            <div class="text-center mt-4 mb-5 animate__animated animate__fadeIn" style="animation-delay: 0.4s;">
                <p class="text-muted small">Punya pertanyaan? Hubungi Customer Service kami via <a href="#" class="font-weight-bold text-success"><i class="fab fa-whatsapp"></i> WhatsApp</a></p>
            </div>

        </div>
    </div>
</div>

<!-- Add Animate.css for micro-animations -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
@endsection
