@extends('layouts.publik')

@section('title', 'Detail Status Booking AC')

@section('content')
<div class="row justify-content-center py-4">
    <div class="col-md-10 col-lg-9">
        <div class="card shadow-lg border-0 rounded-xl overflow-hidden">
            <div class="card-header bg-gradient-primary text-white py-4 px-4 border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 font-weight-bold"><i class="fas fa-snowflake mr-2"></i>Status Booking AC</h4>
                    <span class="badge badge-light text-primary px-3 py-2 rounded-pill font-weight-bold shadow-sm">
                        TOKEN: {{ $booking->access_token }}
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                {{-- Status Header --}}
                <div class="bg-light p-4 text-center border-bottom">
                    <div class="mb-3">
                        @if($booking->status == 'menunggu')
                            <span class="badge badge-warning px-5 py-3 rounded-pill shadow-sm" style="font-size: 1.1rem;">
                                <i class="fas fa-clock mr-2"></i>Menunggu Konfirmasi
                            </span>
                        @elseif($booking->status == 'proses')
                            <span class="badge badge-info px-5 py-3 rounded-pill shadow-sm" style="font-size: 1.1rem;">
                                <i class="fas fa-tools mr-2"></i>Sedang Dikerjakan
                            </span>
                        @elseif($booking->status == 'selesai')
                            <span class="badge badge-success px-5 py-3 rounded-pill shadow-sm" style="font-size: 1.1rem;">
                                <i class="fas fa-check-circle mr-2"></i>Pekerjaan Selesai
                            </span>
                        @else
                            <span class="badge badge-danger px-5 py-3 rounded-pill shadow-sm" style="font-size: 1.1rem;">
                                {{ ucfirst($booking->status) }}
                            </span>
                        @endif
                    </div>
                    <p class="text-muted mb-0">Terima kasih telah mempercayakan layanan AC Anda kepada kami.</p>
                </div>

                <div class="p-4 p-md-5">
                    <div class="row mb-5">
                        <div class="col-md-6 mb-4 mb-md-0">
                            <h6 class="text-primary font-weight-bold text-uppercase small mb-3 border-bottom pb-2">
                                <i class="fas fa-user mr-2"></i>Informasi Pelanggan
                            </h6>
                            <div class="pl-2">
                                <div class="mb-3">
                                    <label class="small text-muted mb-0 d-block">Nama Pelanggan</label>
                                    <span class="h6 font-weight-bold text-dark">{{ $booking->nama_pelanggan ?? ($booking->user->nama_lengkap ?? '-') }}</span>
                                </div>
                                <div class="mb-0">
                                    <label class="small text-muted mb-0 d-block">Nomor WhatsApp</label>
                                    <span class="h6 font-weight-bold text-dark">{{ $booking->no_hp ?? ($booking->user->no_hp ?? '-') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary font-weight-bold text-uppercase small mb-3 border-bottom pb-2">
                                <i class="fas fa-calendar-check mr-2"></i>Detail Layanan
                            </h6>
                            <div class="pl-2">
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="small text-muted mb-0 d-block">Layanan Utama</label>
                                        <span class="font-weight-bold text-dark">{{ $booking->layanan->nama ?? '-' }}</span>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="small text-muted mb-0 d-block">Tgl Kunjungan</label>
                                        <span class="font-weight-bold text-dark">{{ \Carbon\Carbon::parse($booking->tgl_kunjungan)->translatedFormat('d F Y') }}</span>
                                    </div>
                                    <div class="col-12">
                                        <label class="small text-muted mb-0 d-block">Alamat Lokasi</label>
                                        <span class="font-weight-bold text-dark d-block">{{ $booking->alamat }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <h6 class="text-primary font-weight-bold text-uppercase small mb-3 border-bottom pb-2">
                            <i class="fas fa-receipt mr-2"></i>Rincian Pekerjaan & Biaya
                        </h6>
                        @if($booking->detailServis->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover border-bottom">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="border-0">Item Pekerjaan / Sparepart</th>
                                            <th class="text-center border-0">Qty</th>
                                            <th class="text-right border-0">Harga Satuan</th>
                                            <th class="text-right border-0">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($booking->detailServis as $detail)
                                        <tr>
                                            <td class="align-middle">
                                                <div class="font-weight-bold text-dark">{{ $detail->item }}</div>
                                                @if($detail->catatan)
                                                    <div class="small text-muted italic">
                                                        <i class="fas fa-info-circle text-info mr-1"></i> {{ $detail->catatan }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="text-center align-middle">{{ $detail->quantity }} {{ $detail->satuan }}</td>
                                            <td class="text-right align-middle">Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                                            <td class="text-right align-middle font-weight-bold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-light">
                                        <tr>
                                            <td colspan="3" class="text-right font-weight-bold py-3">Total Tagihan</td>
                                            <td class="text-right font-weight-bold text-primary py-3" style="font-size: 1.2rem;">
                                                Rp {{ number_format($booking->pembayaran->total_harga ?? $booking->detailServis->sum('subtotal'), 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-light text-center py-5 border rounded-lg bg-white shadow-sm">
                                <i class="fas fa-spinner fa-spin fa-2x text-primary mb-3"></i>
                                <p class="mb-0 text-muted font-weight-bold">Teknisi sedang melakukan pengecekan atau pengerjaan di lokasi.</p>
                                <small class="text-muted">Rincian biaya akan muncul setelah pekerjaan selesai.</small>
                            </div>
                        @endif
                    </div>

                    @if($booking->foto_hasil)
                    <div class="mb-5">
                        <h6 class="text-primary font-weight-bold text-uppercase small mb-3 border-bottom pb-2">
                            <i class="fas fa-camera mr-2"></i>Dokumentasi Hasil Pekerjaan
                        </h6>
                        <div class="text-center bg-light p-3 rounded-lg border shadow-sm">
                            @php
                                $photoPath = $booking->foto_hasil;
                                if (!Str::startsWith($photoPath, 'http')) {
                                    if (Str::contains($photoPath, 'dokumentasi_ac/')) {
                                        $photoPath = asset('storage/' . $photoPath);
                                    } else {
                                        $photoPath = asset('uploads/ac/hasil/' . $photoPath);
                                    }
                                }
                            @endphp
                            <img src="{{ $photoPath }}" 
                                 class="img-fluid rounded shadow-sm border bg-white" 
                                 style="max-height: 500px; width: auto; object-fit: contain;" 
                                 alt="Dokumentasi Selesai"
                                 onerror="this.src='https://placehold.co/800x600?text=Foto+Dokumentasi+Sedang+Dimuat'">
                        </div>
                    </div>
                    @endif

                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 border-top pt-4" style="gap: 15px;">
                        <div></div> {{-- Spacer to keep the other buttons aligned --}}
                        <div class="d-flex order-1 order-md-2" style="gap: 10px;">
                            <a href="https://wa.me/628123456789" target="_blank" class="btn btn-success px-4 py-2 rounded-pill font-weight-bold shadow-sm">
                                <i class="fab fa-whatsapp mr-2"></i>Hubungi Customer Service
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
