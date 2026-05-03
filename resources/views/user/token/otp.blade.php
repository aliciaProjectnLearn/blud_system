@extends('layouts.publik')
@php $hideNavbarBack = true; @endphp

@section('title', 'Verifikasi Booking')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-lg rounded-xl overflow-hidden">
                <div class="card-header bg-gradient-primary text-white text-center py-4 border-0">
                    <div class="bg-white d-inline-flex p-3 rounded-circle mb-3 shadow-sm">
                        <i class="fas fa-key fa-2x text-primary"></i>
                    </div>
                    <h4 class="font-weight-bold mb-0">Verifikasi Akses</h4>
                    <p class="small opacity-75 mb-0">Masukkan kode untuk melihat detail booking</p>
                </div>
                <div class="card-body p-4 p-md-5">
                    @if(session('info'))
                        <div class="alert alert-info border-0 shadow-sm mb-4 small text-center">
                            {{ session('info') }}
                        </div>
                    @endif

                    <form action="{{ route('token.otp.verify', $token) }}" method="POST">
                        @csrf
                        <div class="form-group mb-4">
                            <label class="small font-weight-bold text-dark mb-2 d-block text-center">Kode Verifikasi (OTP)</label>
                            <input type="text" name="otp" 
                                   class="form-control form-control-lg text-center letter-spacing-lg @error('otp') is-invalid @enderror" 
                                   placeholder="••••••" maxlength="6" autofocus required>
                            @error('otp')
                                <div class="invalid-feedback text-center mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-block btn-lg rounded-pill shadow-sm">
                            Buka Detail Booking
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-2 border-top">
                        <p class="small text-muted mb-2">Tidak menerima kode? Kode OTP telah dikirim via WhatsApp ke nomor yang terdaftar pada booking ini.</p>
                        <form action="{{ route('token.otp.resend', $token) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-link btn-sm text-primary font-weight-bold text-decoration-none">
                                Kirim Ulang Kode OTP
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); }
    .rounded-xl { border-radius: 1rem; }
    .letter-spacing-lg { letter-spacing: 0.5rem; font-weight: 700; font-size: 1.5rem; }
</style>
@endsection
