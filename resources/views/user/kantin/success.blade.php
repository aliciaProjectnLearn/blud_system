@extends('layouts.app')

@section('title', 'Booking Berhasil')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="card shadow-lg border-0 rounded-lg animate__animated animate__zoomIn">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                    </div>
                    <h2 class="font-weight-bold text-dark mb-3">Booking Berhasil!</h2>
                    <p class="text-muted mb-4">Unit <strong>{{ $sewa->ruko->nama_ruko }}</strong> telah berhasil dipesan atas nama <strong>{{ $sewa->nama_penyewa }}</strong>.</p>
                    
                    <div class="alert alert-primary py-4 px-3 mb-4">
                        <p class="small text-uppercase font-weight-bold mb-2">Simpan Link Akses Anda</p>
                        <h4 class="font-weight-bold text-primary mb-3">Kode Akses: {{ $sewa->access_token }}</h4>
                        <p class="small mb-0">Link akses detail penyewaan telah dikirimkan ke nomor WhatsApp <strong>{{ $sewa->no_hp_snapshot }}</strong>.</p>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('user.kantin.sewa.token', ['token' => $sewa->access_token]) }}" class="btn btn-primary btn-lg px-5 shadow">
                            <i class="fas fa-external-link-alt mr-2"></i> Buka Halaman Penyewaan Saya
                        </a>
                    </div>
                    
                    <hr class="my-4">
                    
                    <p class="small text-muted mb-0">
                        <i class="fas fa-info-circle mr-1"></i> Mohon simpan kode akses di atas jika Anda tidak menerima pesan WhatsApp. 
                        Akses ini diperlukan untuk mengunggah bukti pembayaran dan melihat status sewa.
                    </p>
                </div>
            </div>
            
            <div class="mt-4">
                <a href="{{ route('user.kantin.booking.index') }}" class="text-primary font-weight-bold">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Katalog
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
