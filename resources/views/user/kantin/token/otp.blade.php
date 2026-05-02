@extends('layouts.publik')
@php $hideNavbarBack = true; @endphp

@section('title', 'Verifikasi Keamanan')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-lg rounded-xl overflow-hidden">
                <div class="card-header bg-primary text-white text-center py-4 border-0">
                    <div class="bg-white d-inline-flex p-3 rounded-circle mb-3 shadow-sm">
                        <i class="fas fa-shield-alt fa-2x text-primary"></i>
                    </div>
                    <h4 class="font-weight-bold mb-0">Verifikasi OTP</h4>
                    <p class="small opacity-75 mb-0">Keamanan akses penyewaan Anda</p>
                </div>
                <div class="card-body p-4 p-md-5">
                    @if(session('info'))
                        <div class="alert alert-info border-0 shadow-sm mb-4 small">
                            <i class="fas fa-info-circle mr-2"></i> {{ session('info') }}
                        </div>
                    @endif

                    <div class="text-center mb-4">
                        <p class="text-muted small mb-1">Kode OTP telah dikirim ke nomor WhatsApp:</p>
                        <h6 class="font-weight-bold">{{ substr($sewa->no_hp_snapshot, 0, 4) }}****{{ substr($sewa->no_hp_snapshot, -4) }}</h6>
                    </div>

                    <form action="{{ route('user.kantin.sewa.otp.verify', $sewa->access_token) }}" method="POST">
                        @csrf
                        <div class="form-group mb-4">
                            <label class="small font-weight-bold text-dark mb-2">Masukkan 6 Digit Kode OTP</label>
                            <input type="text" name="otp" 
                                   class="form-control form-control-lg text-center letter-spacing-lg @error('otp') is-invalid @enderror" 
                                   placeholder="000000" maxlength="6" autofocus required>
                            @error('otp')
                                <div class="invalid-feedback text-center mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-block btn-lg rounded-pill shadow-sm mb-3">
                            Verifikasi Sekarang <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <p class="small text-muted mb-0">Tidak menerima kode?</p>
                        <form action="{{ route('user.kantin.sewa.otp.resend', $sewa->access_token) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-link btn-sm text-primary font-weight-bold text-decoration-none">
                                Kirim Ulang Kode OTP
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 text-center py-3">
                    <p class="small text-muted mb-0">
                        <i class="fas fa-lock mr-1"></i> Koneksi aman & terenkripsi
                    </p>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('user.gateway') }}" class="text-muted small text-decoration-none hover-primary">
                    <i class="fas fa-home mr-1"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .rounded-xl { border-radius: 1.25rem !important; }
    .letter-spacing-lg { letter-spacing: 0.5rem; font-weight: 700; font-size: 1.5rem; }
    .btn-primary { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); border: none; }
    .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15); }
    .opacity-75 { opacity: 0.75; }
    .hover-primary:hover { color: #4e73df !important; }
</style>
@endsection
