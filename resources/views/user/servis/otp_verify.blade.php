@extends('layouts.publik')

@section('title', 'Verifikasi OTP — Akses Token Servis')

@push('styles')
<style>
    :root {
        --primary: #4e73df;
        --primary-dark: #224abe;
        --danger: #e74a3b;
        --warning: #f6c23e;
        --success: #1cc88a;
        --muted: #858796;
    }

    .otp-wrapper {
        min-height: 100vh;
        background: linear-gradient(135deg, #f0f4ff 0%, #e8ecf8 100%);
        display: flex;
        align-items: center;
        padding: 40px 16px;
    }

    .otp-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(78, 115, 223, 0.15);
        overflow: hidden;
        max-width: 460px;
        width: 100%;
        margin: 0 auto;
    }

    .otp-header {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: #fff;
        padding: 32px 30px 24px;
        text-align: center;
    }

    .otp-header .shield-icon {
        width: 64px;
        height: 64px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 28px;
    }

    .otp-body {
        padding: 32px 30px;
    }

    .otp-input-group {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin: 20px 0;
    }

    .otp-digit {
        width: 52px;
        height: 60px;
        border: 2px solid #e3e6f0;
        border-radius: 12px;
        font-size: 24px;
        font-weight: 700;
        text-align: center;
        color: var(--primary-dark);
        transition: border-color 0.2s, box-shadow 0.2s;
        outline: none;
        background: #f8f9fc;
    }

    .otp-digit:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.15);
        background: #fff;
    }

    .otp-digit.has-value {
        border-color: var(--primary);
        background: #fff;
    }

    .otp-digit.error {
        border-color: var(--danger);
        background: #fff5f5;
    }

    .otp-digit:disabled {
        background: #f0f0f0;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .hidden-otp-input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .timer-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f0f7ff;
        color: var(--primary);
        border-radius: 20px;
        padding: 6px 14px;
        font-size: 0.85rem;
        font-weight: 600;
        border: 1px solid #c7d7f9;
    }

    .timer-badge.expired {
        background: #fff3cd;
        color: #856404;
        border-color: #ffc107;
    }

    .timer-badge.blocked {
        background: #fdf0ee;
        color: var(--danger);
        border-color: #f5c2bc;
    }

    .resend-btn {
        background: none;
        border: 2px solid var(--primary);
        color: var(--primary);
        border-radius: 10px;
        padding: 8px 20px;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .resend-btn:hover:not(:disabled) {
        background: var(--primary);
        color: #fff;
    }

    .resend-btn:disabled {
        border-color: #ccc;
        color: #999;
        cursor: not-allowed;
    }

    .submit-btn {
        width: 100%;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 14px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: transform 0.15s, box-shadow 0.15s;
        letter-spacing: 0.5px;
    }

    .submit-btn:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(78, 115, 223, 0.35);
    }

    .submit-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .block-overlay {
        background: #fff8f7;
        border: 2px solid #f5c2bc;
        border-radius: 14px;
        padding: 20px;
        text-align: center;
        margin-bottom: 16px;
    }

    .countdown-circle {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--danger);
        line-height: 1;
        margin: 8px 0;
    }

    .info-text {
        color: var(--muted);
        font-size: 0.88rem;
        line-height: 1.6;
    }

    .divider {
        border: none;
        border-top: 1px solid #eaecf4;
        margin: 20px 0;
    }
</style>
@endpush

@section('content')
<div class="otp-wrapper">
    <div style="width:100%;">
        <div class="otp-card">
            {{-- HEADER --}}
            <div class="otp-header">
                <div class="shield-icon"><i class="fas fa-shield-alt"></i></div>
                <h5 class="mb-1 font-weight-bold">Verifikasi Akses</h5>
                <p class="mb-0 small" style="opacity:0.85;">
                    Kode OTP telah dikirim ke WhatsApp<br>
                    <strong>{{ $maskedPhone }}</strong>
                </p>
            </div>

            {{-- BODY --}}
            <div class="otp-body">

                {{-- ALERT ERROR --}}
                @if($errors->has('otp'))
                    <div class="alert alert-danger d-flex align-items-center mb-3" style="border-radius:10px; font-size:0.9rem;">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ $errors->first('otp') }}
                    </div>
                @endif

                {{-- ALERT BLOKIR --}}
                @if($otpStatus['blocked'])
                    <div class="block-overlay">
                        <i class="fas fa-lock fa-2x text-danger mb-2"></i>
                        <p class="font-weight-bold mb-1" style="color:#c0392b;">Terlalu Banyak Percobaan</p>
                        <p class="info-text mb-2">Akses diblokir sementara. Coba lagi dalam:</p>
                        <div class="countdown-circle" id="block-countdown">--</div>
                        <p class="info-text">detik</p>
                    </div>
                @endif

                {{-- OTP EXPIRED NOTICE --}}
                @if(!$otpStatus['blocked'] && $otpStatus['otp_expired'] && $otpStatus['has_otp'])
                    <div class="alert alert-warning mb-3" style="border-radius:10px; font-size:0.9rem;">
                        <i class="fas fa-clock mr-2"></i>
                        OTP sebelumnya telah kedaluwarsa. Klik <strong>Kirim Ulang</strong> untuk mendapatkan kode baru.
                    </div>
                @endif

                {{-- FORM OTP --}}
                <form id="otp-form" action="{{ route('user.servis.token.verify', $token) }}" method="POST">
                    @csrf

                    {{-- Hidden real input --}}
                    <input type="text"
                           name="otp"
                           id="otp-hidden"
                           class="hidden-otp-input"
                           maxlength="6"
                           autocomplete="one-time-code"
                           value="{{ old('otp') }}">

                    {{-- Visual digit boxes --}}
                    <div class="otp-input-group" id="otp-visual">
                        @for($i = 0; $i < 6; $i++)
                            <input type="text"
                                   class="otp-digit {{ $errors->has('otp') ? 'error' : '' }}"
                                   id="otp-d{{ $i }}"
                                   maxlength="1"
                                   inputmode="numeric"
                                   pattern="[0-9]*"
                                   {{ $otpStatus['blocked'] ? 'disabled' : '' }}>
                        @endfor
                    </div>

                    {{-- Timer OTP --}}
                    @if(!$otpStatus['blocked'] && !$otpStatus['otp_expired'])
                        <div class="text-center mb-3">
                            <span class="timer-badge" id="otp-timer-badge">
                                <i class="fas fa-clock"></i>
                                OTP berlaku: <strong id="otp-countdown">{{ $otpStatus['otp_seconds'] ?? 180 }}</strong> detik
                            </span>
                        </div>
                    @endif

                    {{-- Submit Button --}}
                    <button type="submit"
                            id="submit-btn"
                            class="submit-btn mb-3"
                            {{ ($otpStatus['blocked'] || ($otpStatus['otp_expired'] && !$otpStatus['has_otp'])) ? 'disabled' : '' }}>
                        <i class="fas fa-check-circle mr-2"></i> Verifikasi OTP
                    </button>

                    <hr class="divider">

                    {{-- Resend Section --}}
                    <div class="text-center">
                        <p class="info-text mb-2">Tidak menerima kode?</p>
                        <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap" style="gap:12px;">

                            {{-- Resend via AJAX --}}
                            <button type="button"
                                    id="resend-btn"
                                    class="resend-btn"
                                    onclick="resendOtp()"
                                    {{ (!$otpStatus['can_resend'] && !$otpStatus['blocked']) ? 'disabled' : '' }}>
                                <i class="fas fa-redo mr-1"></i>
                                Kirim Ulang
                                <span id="resend-cooldown-text"
                                      @if($otpStatus['resend_cooldown'] > 0) style="display:inline;" @else style="display:none;" @endif>
                                    (<span id="resend-countdown">{{ $otpStatus['resend_cooldown'] }}</span>d)
                                </span>
                            </button>
                        </div>
                    </div>
                </form>

                <hr class="divider">
                <p class="info-text text-center mb-0">
                    <i class="fas fa-info-circle mr-1 text-primary"></i>
                    OTP berlaku <strong>3 menit</strong> dan hanya bisa digunakan <strong>sekali</strong>.
                    Setelah verifikasi berhasil, akses aktif selama <strong>60 menit</strong>.
                </p>
            </div>
        </div>

        <div class="text-center mt-3">
            <a href="{{ route('user.servis.katalog') }}" class="text-muted small">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Katalog Layanan
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ─── Konfigurasi Awal ──────────────────────────────────────────────────
const RESEND_URL  = "{{ route('user.servis.token.resend', $token) }}";
const CSRF_TOKEN  = "{{ csrf_token() }}";
const IS_BLOCKED  = {{ $otpStatus['blocked'] ? 'true' : 'false' }};
const HAS_ERROR   = {{ $errors->has('otp') ? 'true' : 'false' }};

// ─── Countdown OTP (sisa waktu berlaku) ───────────────────────────────
@if(!$otpStatus['blocked'] && !$otpStatus['otp_expired'])
let otpSecondsLeft = {{ $otpStatus['otp_seconds'] ?? 180 }};
const otpTimerEl   = document.getElementById('otp-countdown');
const otpBadge     = document.getElementById('otp-timer-badge');
const submitBtn    = document.getElementById('submit-btn');

const otpTimer = setInterval(function () {
    otpSecondsLeft--;
    if (otpSecondsLeft <= 0) {
        clearInterval(otpTimer);
        otpTimerEl.textContent = '0';
        if (otpBadge) {
            otpBadge.classList.add('expired');
            otpBadge.innerHTML = '<i class="fas fa-times-circle"></i> OTP Kedaluwarsa — Minta kode baru';
        }
        if (submitBtn) submitBtn.disabled = true;
        // Aktifkan tombol resend
        const resendBtn = document.getElementById('resend-btn');
        if (resendBtn) {
            resendBtn.disabled = false;
            document.getElementById('resend-cooldown-text').style.display = 'none';
        }
        return;
    }
    if (otpTimerEl) otpTimerEl.textContent = otpSecondsLeft;
}, 1000);
@endif

// ─── Countdown Blokir ─────────────────────────────────────────────────
@if($otpStatus['blocked'])
let blockSecondsLeft = {{ $otpStatus['block_seconds'] }};
const blockEl = document.getElementById('block-countdown');

const blockTimer = setInterval(function () {
    blockSecondsLeft--;
    if (blockSecondsLeft <= 0) {
        clearInterval(blockTimer);
        // Reload halaman agar user bisa coba lagi
        window.location.reload();
        return;
    }
    if (blockEl) blockEl.textContent = blockSecondsLeft;
}, 1000);

if (blockEl) blockEl.textContent = blockSecondsLeft;
@endif

// ─── Countdown Resend Cooldown ─────────────────────────────────────────
@if(!$otpStatus['blocked'] && $otpStatus['resend_cooldown'] > 0)
let resendSecondsLeft = {{ $otpStatus['resend_cooldown'] }};
const resendCountdownEl = document.getElementById('resend-countdown');
const resendBtn         = document.getElementById('resend-btn');

const resendTimer = setInterval(function () {
    resendSecondsLeft--;
    if (resendSecondsLeft <= 0) {
        clearInterval(resendTimer);
        document.getElementById('resend-cooldown-text').style.display = 'none';
        if (resendBtn) resendBtn.disabled = false;
        return;
    }
    if (resendCountdownEl) resendCountdownEl.textContent = resendSecondsLeft;
}, 1000);
@endif

// ─── OTP Input Handling (6 kotak digit) ───────────────────────────────
const digits    = document.querySelectorAll('.otp-digit');
const hiddenOtp = document.getElementById('otp-hidden');

function syncHidden() {
    let val = '';
    digits.forEach(d => val += (d.value || ''));
    hiddenOtp.value = val;
}

function focusNext(index) {
    if (index < digits.length - 1) digits[index + 1].focus();
}

function focusPrev(index) {
    if (index > 0) digits[index - 1].focus();
}

// Isi kotak dari nilai lama (jika ada error dan withInput)
const existingVal = hiddenOtp.value || '';
digits.forEach((digit, i) => {
    if (existingVal[i]) {
        digit.value = existingVal[i];
        digit.classList.add('has-value');
    }
});

digits.forEach((digit, index) => {
    digit.addEventListener('input', function (e) {
        const val = this.value.replace(/\D/g, '');
        this.value = val ? val[val.length - 1] : '';
        this.classList.toggle('has-value', !!this.value);
        syncHidden();
        if (this.value) focusNext(index);
    });

    digit.addEventListener('keydown', function (e) {
        if (e.key === 'Backspace' && !this.value) {
            focusPrev(index);
        }
        if (e.key === 'ArrowLeft') focusPrev(index);
        if (e.key === 'ArrowRight') focusNext(index);
    });

    digit.addEventListener('paste', function (e) {
        e.preventDefault();
        const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
        pasted.split('').slice(0, 6).forEach((ch, i) => {
            if (digits[i]) {
                digits[i].value = ch;
                digits[i].classList.add('has-value');
            }
        });
        syncHidden();
        const lastFilled = Math.min(pasted.length, 6) - 1;
        if (digits[lastFilled]) digits[lastFilled].focus();
    });
});

// Auto-focus kotak pertama
if (!IS_BLOCKED && digits[0]) digits[0].focus();

// ─── Form Submit ──────────────────────────────────────────────────────
document.getElementById('otp-form').addEventListener('submit', function (e) {
    syncHidden();
    if (hiddenOtp.value.length < 6) {
        e.preventDefault();
        digits.forEach(d => d.classList.add('error'));
        alert('Masukkan 6 digit OTP terlebih dahulu.');
    }
});

// ─── Kirim Ulang OTP (AJAX) ───────────────────────────────────────────
function resendOtp() {
    const btn = document.getElementById('resend-btn');
    if (btn.disabled) return;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Mengirim...';

    fetch(RESEND_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept':       'application/json',
        },
        body: JSON.stringify({}),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Reset OTP inputs
            digits.forEach(d => { d.value = ''; d.classList.remove('has-value', 'error'); });
            hiddenOtp.value = '';
            if (digits[0]) digits[0].focus();

            // Tampilkan notifikasi sukses
            showToast(data.message, 'success');

            // Mulai cooldown resend
            let countdown = data.cooldown_seconds || 60;
            const cdText  = document.getElementById('resend-cooldown-text');
            const cdNum   = document.getElementById('resend-countdown');
            if (cdText) cdText.style.display = 'inline';
            if (cdNum)  cdNum.textContent    = countdown;

            const newResendTimer = setInterval(() => {
                countdown--;
                if (countdown <= 0) {
                    clearInterval(newResendTimer);
                    btn.disabled = false;
                    if (cdText) cdText.style.display = 'none';
                    btn.innerHTML = '<i class="fas fa-redo mr-1"></i> Kirim Ulang';
                    return;
                }
                if (cdNum) cdNum.textContent = countdown;
            }, 1000);

            // Reset OTP timer di badge jika ada
            const otpBadge2 = document.getElementById('otp-timer-badge');
            if (otpBadge2) {
                otpBadge2.classList.remove('expired');
                let newOtpSecs = 180;
                otpBadge2.innerHTML = '<i class="fas fa-clock"></i> OTP berlaku: <strong id="otp-countdown">' + newOtpSecs + '</strong> detik';
                const newOtpEl = document.getElementById('otp-countdown');
                const newOtpTimer2 = setInterval(() => {
                    newOtpSecs--;
                    if (newOtpSecs <= 0) { clearInterval(newOtpTimer2); }
                    if (newOtpEl) newOtpEl.textContent = Math.max(0, newOtpSecs);
                }, 1000);
            }

            // Aktifkan submit button
            const sb = document.getElementById('submit-btn');
            if (sb) sb.disabled = false;

        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-redo mr-1"></i> Kirim Ulang';
            showToast(data.message || 'Gagal mengirim OTP.', 'danger');
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-redo mr-1"></i> Kirim Ulang';
        showToast('Terjadi kesalahan jaringan. Silakan coba lagi.', 'danger');
    });
}

// ─── Toast Notification ──────────────────────────────────────────────
function showToast(message, type = 'info') {
    const colors = {
        success: '#1cc88a',
        danger:  '#e74a3b',
        info:    '#4e73df',
        warning: '#f6c23e',
    };
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed; top: 20px; right: 20px; z-index: 9999;
        background: ${colors[type] || colors.info}; color: #fff;
        padding: 12px 20px; border-radius: 10px; font-weight: 600;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15); font-size: 0.9rem;
        max-width: 320px; transition: opacity 0.3s;
    `;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
}
</script>
@endpush
