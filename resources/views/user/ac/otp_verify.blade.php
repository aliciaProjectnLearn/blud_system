@extends('layouts.publik')

@section('title', 'Verifikasi OTP - BLUD System')

@section('content')
<div class="row justify-content-center py-5">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg border-0 rounded-xl overflow-hidden">
            <div class="card-header bg-gradient-primary text-white py-4 px-4 text-center border-0">
                <h4 class="mb-0 font-weight-bold"><i class="fas fa-lock mr-2"></i>Verifikasi Keamanan</h4>
                <p class="mb-0 small opacity-8">Masukkan kode OTP untuk mengakses detail booking</p>
            </div>
            <div class="card-body p-4 p-md-5">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="text-center mb-4">
                    <div class="bg-light p-3 rounded-lg border d-inline-block mb-3">
                        <i class="fas fa-comment-alt-dots fa-3x text-primary"></i>
                    </div>
                    <h6>Kode OTP telah dikirim ke WhatsApp</h6>
                    <p class="text-muted small">
                        Kami telah mengirimkan 6 digit kode keamanan ke nomor:<br>
                        <strong class="text-dark">{{ substr($booking->no_hp, 0, 4) }}-xxxx-{{ substr($booking->no_hp, -4) }}</strong>
                    </p>
                </div>

                <form action="{{ route('user.ac.token.otp.verify', $token) }}" method="POST">
                    @csrf
                    <div class="form-group mb-4">
                        <label class="font-weight-bold small text-uppercase text-muted">Masukkan 6 Digit OTP</label>
                        <input type="text" name="otp" class="form-control form-control-lg text-center font-weight-bold letter-spacing-lg" 
                               maxlength="6" placeholder="000000" autofocus required autocomplete="one-time-code"
                               style="font-size: 2rem; border-radius: 12px; height: 70px; letter-spacing: 0.5rem;">
                        @error('otp')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg py-3 rounded-pill font-weight-bold shadow-sm mb-4">
                        Verifikasi OTP <i class="fas fa-chevron-right ml-2"></i>
                    </button>
                </form>

                <div class="text-center border-top pt-4">
                    <p class="text-muted small mb-3">Tidak menerima kode? atau kode kadaluarsa?</p>
                    
                    <form action="{{ route('user.ac.token.otp.resend', $token) }}" method="POST" id="resendForm">
                        @csrf
                        @php
                            $canResend = true;
                            $cooldownSeconds = 0;
                            if($otp) {
                                $isExpired = $otp->expires_at->isPast();
                                $isBlockFinished = !($otp->blocked_until && $otp->blocked_until->isFuture());
                                $isCooldownFinished = !($otp->sent_at && $otp->sent_at->addSeconds(60)->isFuture());

                                if (($isExpired || $isBlockFinished) && $isCooldownFinished) {
                                    $canResend = true;
                                } else {
                                    $canResend = false;
                                }

                                if (!$isCooldownFinished) {
                                    $cooldownSeconds = $otp->sent_at->addSeconds(60)->diffInSeconds(now());
                                }
                            }
                        @endphp

                        <button type="submit" id="resendBtn" class="btn btn-link font-weight-bold" 
                                {{ (!$canResend) ? 'disabled' : '' }}>
                            Kirim Ulang OTP <span id="cooldownText">{{ $cooldownSeconds > 0 ? "($cooldownSeconds)" : "" }}</span>
                        </button>
                    </form>

                    <div class="mt-3">
                        <div class="small text-muted mb-1">OTP Berlaku Selama:</div>
                        <div class="h5 font-weight-bold text-primary" id="timer">03:00</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="{{ route('user.ac.index') }}" class="text-muted small font-weight-bold">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>

<style>
    .letter-spacing-lg {
        letter-spacing: 0.5rem;
    }
    .rounded-xl {
        border-radius: 1.5rem !important;
    }
    .opacity-8 {
        opacity: 0.8;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const expiresAt = new Date("{{ $otp ? $otp->expires_at->toIso8601String() : now()->addMinutes(3)->toIso8601String() }}").getTime();
        const timerElement = document.getElementById('timer');
        const resendBtn = document.getElementById('resendBtn');
        const cooldownText = document.getElementById('cooldownText');
        
        let cooldown = parseInt("{{ $cooldownSeconds }}");
        let blockedUntilStr = "{{ $otp && $otp->blocked_until ? $otp->blocked_until->toIso8601String() : '' }}";

        function updateResendState() {
            const now = new Date().getTime();
            const isExpired = expiresAt <= now;
            let isBlocked = false;
            if (blockedUntilStr) {
                isBlocked = new Date(blockedUntilStr).getTime() > now;
            }
            const isCooldownFinished = cooldown <= 0;

            // Rule: "Tombol Kirim Ulang OTP hanya aktif jika: OTP sudah expired ATAU masa blokir sudah selesai."
            // AND "Cooldown kirim ulang: 60 detik sejak pengiriman terakhir (sent_at)."
            if ((isExpired || !isBlocked) && isCooldownFinished) {
                resendBtn.disabled = false;
            } else {
                resendBtn.disabled = true;
            }
        }

        // Timer OTP Countdown
        const timerInterval = setInterval(function() {
            const now = new Date().getTime();
            const distance = expiresAt - now;

            if (distance < 0) {
                clearInterval(timerInterval);
                timerElement.innerHTML = "EXPIRED";
                timerElement.classList.remove('text-primary');
                timerElement.classList.add('text-danger');
                updateResendState();
                return;
            }

            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            timerElement.innerHTML = (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds;
        }, 1000);

        // Cooldown Timer for Resend Button
        if (cooldown > 0) {
            const cooldownInterval = setInterval(function() {
                cooldown--;
                if (cooldown <= 0) {
                    clearInterval(cooldownInterval);
                    cooldownText.innerHTML = "";
                    updateResendState();
                } else {
                    cooldownText.innerHTML = "(" + cooldown + ")";
                }
            }, 1000);
        }
        
        // Initial state check
        updateResendState();
    });
</script>
@endsection
