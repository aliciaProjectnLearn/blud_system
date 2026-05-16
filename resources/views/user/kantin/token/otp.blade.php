@extends('layouts.publik')

@section('title', 'Verifikasi OTP')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body p-4 text-center">

                    {{-- Icon --}}
                    <div class="mb-3">
                        <div class="rounded-circle bg-primary d-inline-flex 
                                    align-items-center justify-content-center"
                             style="width:70px;height:70px; box-shadow: 0 4px 10px rgba(78, 115, 223, 0.2);">
                            <i class="fas fa-shield-alt fa-2x text-white"></i>
                        </div>
                    </div>

                    <h5 class="font-weight-bold mb-1">Verifikasi OTP</h5>
                    <p class="text-muted small mb-3">
                        Kode OTP telah dikirim ke WhatsApp<br>
                        <strong>{{ $hpSensor }}</strong>
                    </p>

                    {{-- Alert --}}
                    @if(session('error'))
                    <div class="alert alert-danger small border-0 shadow-sm">
                        <i class="fas fa-times-circle mr-1"></i>
                        {{ session('error') }}
                    </div>
                    @endif
                    @if(session('info'))
                    <div class="alert alert-info small border-0 shadow-sm">
                        <i class="fas fa-info-circle mr-1"></i>
                        {{ session('info') }}
                    </div>
                    @endif

                    {{-- Form OTP --}}
                    <form action="{{ route('user.kantin.otp.verifikasi', $token) }}" 
                          method="POST" id="otpForm">
                        @csrf
                        <div class="mb-3">
                            <input type="text" name="otp" 
                                   class="form-control form-control-lg text-center 
                                          font-weight-bold letter-spacing-wide"
                                   maxlength="6" 
                                   placeholder="_ _ _ _ _ _"
                                   pattern="[0-9]{6}"
                                   inputmode="numeric"
                                   autocomplete="one-time-code"
                                   autofocus
                                   style="font-size:28px; letter-spacing:10px; border-radius: 10px; border: 2px solid #e3e6f0;">
                        </div>

                        <button type="submit" class="btn btn-primary btn-block btn-lg mb-3 py-3 font-weight-bold" style="border-radius: 10px;">
                            <i class="fas fa-check-circle mr-2"></i> Verifikasi
                        </button>
                    </form>

                    {{-- Timer & Kirim Ulang --}}
                    <div class="py-2">
                        <p class="text-muted small mb-1">
                            Kode berlaku selama <span id="timer" class="font-weight-bold text-danger">5:00</span>
                        </p>
                        <a href="{{ route('user.kantin.otp.kirim-ulang', $token) }}"
                           id="btn-kirim-ulang" class="btn btn-link btn-sm text-primary font-weight-bold d-none">
                            <i class="fas fa-redo mr-1"></i> Kirim Ulang OTP
                        </a>
                    </div>

                    <hr class="my-4">
                    <div class="text-left bg-light p-3 rounded small text-muted">
                        <i class="fas fa-info-circle mr-1 text-primary"></i>
                        Tidak menerima OTP? Pastikan nomor WhatsApp 
                        <strong>{{ $hpSensor }}</strong> aktif dan terhubung ke internet.
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="{{ route('user.kantin.katalog') }}" class="text-muted small">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Katalog
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Countdown timer 5 menit
    let waktu = 5 * 60;
    const timerEl = document.getElementById('timer');
    const btnKirimUlang = document.getElementById('btn-kirim-ulang');

    const interval = setInterval(() => {
        waktu--;
        const menit = Math.floor(waktu / 60);
        const detik = Math.floor(waktu % 60);
        timerEl.textContent = menit + ':' + String(detik).padStart(2, '0');

        if (waktu <= 0) {
            clearInterval(interval);
            timerEl.textContent = 'Kadaluarsa';
            timerEl.classList.remove('text-danger');
            timerEl.classList.add('text-secondary');
            btnKirimUlang.classList.remove('d-none');
        }
    }, 1000);

    // Auto format input OTP
    $('input[name="otp"]').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
        if(this.value.length === 6) {
            // Auto submit optionally? Let's not for better UX unless requested
        }
    });
});
</script>
@endpush
