@extends('layouts.publik')

@section('title', 'Verifikasi OTP Booking Servis')

@push('styles')
<style>
    .otp-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 8px 32px rgba(78, 115, 223, 0.15);
        overflow: hidden;
    }
    .otp-card-header {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        padding: 2rem 1.5rem;
        text-align: center;
        color: white;
    }
    .otp-icon-circle {
        width: 72px;
        height: 72px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 2rem;
        backdrop-filter: blur(4px);
    }
    .otp-inputs {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin: 1.5rem 0;
    }
    .otp-box {
        width: 48px;
        height: 58px;
        font-size: 1.5rem;
        font-weight: 700;
        text-align: center;
        border: 2px solid #d1d3e2;
        border-radius: 10px;
        transition: all 0.2s;
        color: #4e73df;
        background: #f8f9fc;
        caret-color: transparent;
    }
    .otp-box:focus {
        border-color: #4e73df;
        background: #fff;
        outline: none;
        box-shadow: 0 0 0 3px rgba(78,115,223,0.15);
        caret-color: #4e73df;
    }
    .otp-box.filled {
        border-color: #4e73df;
        background: #eef0fb;
    }
    .countdown-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.85rem;
        font-weight: 500;
        color: #6c757d;
        background: #f0f2fa;
        border-radius: 20px;
        padding: 4px 14px;
    }
    .countdown-badge.blocked {
        color: #e74a3b;
        background: #fde8e6;
    }
    .btn-verify {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        border: none;
        border-radius: 10px;
        font-weight: 600;
        letter-spacing: 0.5px;
        padding: 12px 32px;
        transition: all 0.3s;
        color: white;
    }
    .btn-verify:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(78,115,223,0.35);
        color: white;
    }
    .btn-verify:disabled {
        opacity: 0.65;
        cursor: not-allowed;
    }
    .btn-resend {
        border-radius: 10px;
        font-weight: 500;
        transition: all 0.3s;
        border: 2px solid #4e73df;
        color: #4e73df;
        background: transparent;
        padding: 10px 24px;
    }
    .btn-resend:hover:not(:disabled) {
        background: #4e73df;
        color: white;
    }
    .btn-resend:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        border-color: #b7c2ea;
        color: #b7c2ea;
    }
    .phone-mask {
        font-family: 'Courier New', monospace;
        background: rgba(78,115,223,0.08);
        border-radius: 6px;
        padding: 2px 8px;
        font-weight: 600;
        color: #4e73df;
    }
    .blocked-banner {
        background: linear-gradient(135deg, #fde8e6, #fce0dd);
        border: 1px solid #f5c6c3;
        border-radius: 12px;
        padding: 1.25rem;
        text-align: center;
    }
    @media (max-width: 450px) {
        .otp-box { width: 42px; height: 52px; font-size: 1.3rem; }
        .otp-inputs { gap: 8px; }
    }
    @media (max-width: 360px) {
        .otp-box { width: 36px; height: 46px; font-size: 1.1rem; }
        .otp-inputs { gap: 4px; }
    }
</style>
@endpush

@section('content')
<div class="container py-5 mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">

            @php
                $noHp      = $booking->no_hp;
                $tampil    = '****' . substr(preg_replace('/[^0-9]/', '', $noHp), -4);

                $blockedUntil = \Illuminate\Support\Facades\Cache::get('booking_servis_otp_blocked_' . $token);
                $isBlocked    = $blockedUntil && now()->lt(\Carbon\Carbon::parse($blockedUntil));
                $blockDetikSisa = $isBlocked
                    ? max(0, (int) now()->diffInSeconds(\Carbon\Carbon::parse($blockedUntil)))
                    : 0;

                $cooldownSisa = 0;
                $otpSentAt = \Illuminate\Support\Facades\Cache::get('booking_servis_otp_sent_at_' . $token);
                if ($otpSentAt) {
                    $detikSejak = now()->diffInSeconds(\Carbon\Carbon::parse($otpSentAt));
                    $cooldownSisa = max(0, 60 - (int) $detikSejak);
                }

                $otpExpiredAt = \Illuminate\Support\Facades\Cache::get('booking_servis_otp_expired_' . $token);
                $otpSisaDetik = 0;
                if ($otpExpiredAt && now()->lt(\Carbon\Carbon::parse($otpExpiredAt))) {
                    $otpSisaDetik = max(0, (int) now()->diffInSeconds(\Carbon\Carbon::parse($otpExpiredAt)));
                }
            @endphp

            <div class="otp-card card mb-4">
                <div class="otp-card-header">
                    <div class="otp-icon-circle">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4 class="mb-1 font-weight-bold">Verifikasi OTP</h4>
                    <p class="mb-0 opacity-75" style="opacity: 0.85; font-size: 0.92rem;">
                        Kode OTP dikirim ke WhatsApp<br>
                        <span class="phone-mask" style="background:rgba(255,255,255,0.2); color:white;">{{ $tampil }}</span>
                    </p>
                </div>

                <div class="card-body p-4">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show rounded-lg" role="alert">
                            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    @endif
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show rounded-lg" role="alert">
                            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    @endif

                    @if($isBlocked)
                        <div class="blocked-banner mb-3">
                            <i class="fas fa-lock fa-2x text-danger mb-2"></i>
                            <h6 class="text-danger font-weight-bold mb-1">Akses Sementara Diblokir</h6>
                            <p class="text-muted mb-2" style="font-size: 0.88rem;">
                                Terlalu banyak percobaan salah. Silakan coba lagi dalam:
                            </p>
                            <div class="countdown-badge blocked mx-auto" style="width: fit-content;">
                                <i class="fas fa-hourglass-half"></i>
                                <span id="block-countdown"></span>
                            </div>
                        </div>
                    @else
                        <form id="otp-form" method="POST" action="{{ route('user.servis.token.otp.verify', $token) }}" novalidate>
                            @csrf
                            <p class="text-muted text-center mb-1" style="font-size: 0.9rem;">
                                Masukkan 6 digit kode OTP yang dikirim ke WhatsApp Anda.
                            </p>

                            @if($otpSisaDetik > 0)
                            <p class="text-center mb-0">
                                <span class="countdown-badge" id="otp-valid-badge">
                                    <i class="fas fa-clock"></i>
                                    Berlaku: <strong id="otp-timer"></strong>
                                </span>
                            </p>
                            @else
                            <p class="text-center text-danger mb-0" style="font-size: 0.85rem;">
                                <i class="fas fa-exclamation-triangle"></i> OTP sudah kedaluwarsa. Klik "Kirim Ulang".
                            </p>
                            @endif

                            <div class="otp-inputs" id="otp-input-group" role="group" aria-label="Masukkan kode OTP 6 digit">
                                @for ($i = 0; $i < 6; $i++)
                                    <input type="text"
                                           class="otp-box"
                                           id="otp-box-{{ $i }}"
                                           maxlength="1"
                                           inputmode="numeric"
                                           pattern="[0-9]"
                                           autocomplete="one-time-code"
                                           aria-label="Digit {{ $i + 1 }}"
                                           {{ $i === 0 ? 'autofocus' : '' }}>
                                @endfor
                            </div>

                            <input type="hidden" name="otp_input" id="otp-hidden-input">

                            <div class="text-center">
                                <button type="submit" class="btn btn-verify btn-block" id="btn-verify" disabled>
                                    <i class="fas fa-check-circle mr-2"></i> Verifikasi OTP
                                </button>
                            </div>
                        </form>
                    @endif

                    <hr class="my-3">
                    <div class="text-center">
                        <p class="text-muted mb-2" style="font-size: 0.85rem;">Tidak menerima kode?</p>
                        <form method="POST" action="{{ route('user.servis.token.otp.resend', $token) }}" id="resend-form">
                            @csrf
                            @if($isBlocked)
                                <button type="submit" class="btn btn-resend" disabled id="btn-resend">
                                    <i class="fas fa-redo mr-1"></i> Kirim Ulang OTP
                                </button>
                                <p class="text-muted mt-2" style="font-size: 0.8rem;">
                                    <i class="fas fa-info-circle"></i> Resend tersedia setelah blokir berakhir.
                                </p>
                            @elseif($cooldownSisa > 0)
                                <button type="submit" class="btn btn-resend" disabled id="btn-resend">
                                    <i class="fas fa-redo mr-1"></i> Kirim Ulang OTP
                                </button>
                                <p class="text-muted mt-2" style="font-size: 0.8rem;">
                                    Tunggu <strong id="resend-countdown">{{ $cooldownSisa }}</strong>s sebelum kirim ulang
                                </p>
                            @else
                                <button type="submit" class="btn btn-resend" id="btn-resend">
                                    <i class="fas fa-redo mr-1"></i> Kirim Ulang OTP
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm" style="border-radius: 12px; background: #f8f9fc;">
                <div class="card-body py-3 px-4">
                    <p class="mb-1 text-muted" style="font-size: 0.8rem;"><i class="fas fa-info-circle text-primary mr-2"></i><strong>Informasi OTP:</strong></p>
                    <ul class="mb-0 text-muted pl-3" style="font-size: 0.8rem;">
                        <li>Kode berlaku selama <strong>3 menit</strong></li>
                        <li>Maksimal <strong>3x percobaan</strong> per sesi</li>
                        <li>Jika diblokir, tunggu <strong>3 menit</strong></li>
                        <li>Kirim ulang tersedia setelah <strong>60 detik</strong></li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    const boxes        = document.querySelectorAll('.otp-box');
    const hiddenInput  = document.getElementById('otp-hidden-input');
    const btnVerify    = document.getElementById('btn-verify');
    const form         = document.getElementById('otp-form');

    function updateHidden() {
        if (!hiddenInput) return;
        const val = Array.from(boxes).map(b => b.value).join('');
        hiddenInput.value = val;
        if (btnVerify) btnVerify.disabled = val.length < 6;
        boxes.forEach(b => b.classList.toggle('filled', b.value !== ''));
    }

    if (boxes.length) {
        boxes.forEach((box, idx) => {
            box.addEventListener('input', function (e) {
                const val = this.value.replace(/\D/g, '');
                if (val.length > 1) {
                    val.split('').forEach((ch, i) => {
                        if (boxes[idx + i]) boxes[idx + i].value = ch;
                    });
                    const next = boxes[idx + val.length];
                    if (next) next.focus(); else boxes[boxes.length - 1].focus();
                } else {
                    this.value = val;
                    if (val && boxes[idx + 1]) boxes[idx + 1].focus();
                }
                updateHidden();
            });

            box.addEventListener('keydown', function (e) {
                if (e.key === 'Backspace') {
                    if (!this.value && boxes[idx - 1]) {
                        boxes[idx - 1].value = '';
                        boxes[idx - 1].focus();
                    }
                    updateHidden();
                }
                if (e.key === 'ArrowLeft' && boxes[idx - 1]) boxes[idx - 1].focus();
                if (e.key === 'ArrowRight' && boxes[idx + 1]) boxes[idx + 1].focus();
            });

            box.addEventListener('paste', function (e) {
                e.preventDefault();
                const text = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
                text.split('').forEach((ch, i) => { if (boxes[i]) boxes[i].value = ch; });
                const next = boxes[Math.min(text.length, 5)];
                if (next) next.focus();
                updateHidden();
            });
        });
    }

    const otpTimerEl = document.getElementById('otp-timer');
    const otpBadge   = document.getElementById('otp-valid-badge');
    let otpSisa      = {{ $otpSisaDetik }};

    function formatTime(s) {
        const m = Math.floor(s / 60);
        const sec = s % 60;
        return m + ':' + String(sec).padStart(2, '0');
    }

    if (otpTimerEl && otpSisa > 0) {
        otpTimerEl.textContent = formatTime(otpSisa);
        const otpInterval = setInterval(function () {
            otpSisa--;
            if (otpSisa <= 0) {
                clearInterval(otpInterval);
                otpTimerEl.textContent = '0:00';
                if (otpBadge) {
                    otpBadge.classList.remove('countdown-badge');
                    otpBadge.innerHTML = '<i class="fas fa-exclamation-triangle text-danger mr-1"></i><span class="text-danger">OTP kedaluwarsa. Klik "Kirim Ulang".</span>';
                }
                if (btnVerify) btnVerify.disabled = true;
            } else {
                otpTimerEl.textContent = formatTime(otpSisa);
            }
        }, 1000);
    }

    const resendEl  = document.getElementById('resend-countdown');
    const btnResend = document.getElementById('btn-resend');
    let resendSisa  = {{ $cooldownSisa }};

    if (resendEl && resendSisa > 0) {
        resendEl.textContent = resendSisa;
        const resendInterval = setInterval(function () {
            resendSisa--;
            resendEl.textContent = resendSisa;
            if (resendSisa <= 0) {
                clearInterval(resendInterval);
                if (btnResend) btnResend.disabled = false;
                if (resendEl && resendEl.parentElement) {
                    resendEl.parentElement.textContent = '';
                }
            }
        }, 1000);
    }

    const blockEl = document.getElementById('block-countdown');
    @if($isBlocked)
    let blockSisa = {{ $blockDetikSisa }};
    if (blockEl) {
        blockEl.textContent = formatTime(blockSisa);
        const blockInterval = setInterval(function () {
            blockSisa--;
            if (blockSisa <= 0) {
                clearInterval(blockInterval);
                blockEl.textContent = '0:00';
                setTimeout(function () { window.location.reload(); }, 1500);
            } else {
                blockEl.textContent = formatTime(blockSisa);
            }
        }, 1000);
    }
    @endif

    if (form) {
        form.addEventListener('submit', function () {
            if (btnVerify) {
                btnVerify.disabled = true;
                btnVerify.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memverifikasi...';
            }
        });
    }
})();
</script>
@endpush
