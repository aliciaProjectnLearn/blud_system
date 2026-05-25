<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>OneBLUD - @yield('title', 'Layanan')</title>

    {{-- Font Awesome --}}
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    {{-- SB Admin 2 CSS --}}
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Poppins', sans-serif !important; 
            background-color: #f8f9fc; 
        }
        .navbar-pelanggan { 
            background: #fff; 
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); 
            border-bottom: 2px solid #4e73df;
        }
        .btn-kembali { 
            border-radius: 8px; 
            font-weight: 500; 
            transition: all 0.3s;
        }
        .btn-kembali:hover {
            transform: translateX(-3px);
        }
        .logo-box { 
            background: #4e73df; 
            width: 35px; 
            height: 35px; 
            border-radius: 8px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin-right: 12px; 
            box-shadow: 0 4px 10px rgba(78, 115, 223, 0.3);
        }
        @media (max-width: 576px) {
            .logo-box { width: 30px; height: 30px; margin-right: 8px; }
            .navbar-brand { font-size: 0.9rem; }
            .btn-kembali { padding-left: 12px !important; padding-right: 12px !important; }
        }
        .navbar-brand {
            letter-spacing: 1px;
        }
        #content-wrapper {
            background-color: transparent !important;
        }
    </style>
    @stack('styles')
</head>

<body id="page-top">

    <div id="wrapper">
        {{-- Main Content --}}
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                
                {{-- Simple Navbar khusus pelanggan --}}
                <nav class="navbar navbar-expand navbar-light navbar-pelanggan mb-4 sticky-top">
                    <div class="container py-2">
                        <a class="navbar-brand d-flex align-items-center" href="{{ route('user.gateway') }}">
                            <div class="logo-box">
                                <svg class="text-white" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2v20M2 12h20"/><path d="m6 6 12 12M18 6 6 18"/>
                                </svg>
                            </div>
                            <span class="font-weight-bold text-primary">OneBLUD</span>
                        </a>
                        

                        <div class="d-flex align-items-center" style="gap: 8px;">
                            @if(Route::is('user.gateway') || Route::is('home'))
                                <button type="button" class="btn btn-sm btn-primary shadow-sm px-3" 
                                        data-toggle="modal" data-target="#modalCekBooking">
                                    <i class="fas fa-search mr-1"></i> Cek Booking
                                </button>
                            @else
                                @if(!isset($hideNavbarBack) || !$hideNavbarBack)
                                    <a href="{{ route('user.gateway') }}" class="btn btn-sm btn-light btn-kembali text-primary shadow-sm px-3">
                                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                </nav>

                {{-- Page Content --}}
                <div class="container pb-5">
                    {{-- Flash Messages --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show shadow-sm border-left-success" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle fa-lg mr-3"></i>
                                <div>{{ session('success') }}</div>
                            </div>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-left-danger" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-circle fa-lg mr-3"></i>
                                <div>{{ session('error') }}</div>
                            </div>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>

            @include('partials.footer')
        </div>
    </div>

    {{-- Scripts --}}
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('assets/js/sb-admin-2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    @stack('scripts')

    @if(Route::is('user.gateway') || Route::is('home'))
    <!-- Modal Cek Booking -->
    <div class="modal fade" id="modalCekBooking" tabindex="-1" 
         role="dialog" aria-labelledby="modalCekBookingLabel" 
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg" 
                 style="border-radius: 16px; overflow: hidden;">
                
                {{-- Header --}}
                <div class="modal-header border-0 text-white"
                     style="background: linear-gradient(135deg, #3d5af1 0%, #224abe 100%); 
                            padding: 1.5rem 2rem;">
                    <div>
                        <h5 class="modal-title font-weight-bold mb-1" id="modalCekBookingLabel">
                            <i class="fas fa-search mr-2"></i> Cek Status Booking
                        </h5>
                        <p class="mb-0 small" style="opacity: 0.85;">
                            Masukkan nomor WhatsApp untuk melihat booking Anda
                        </p>
                    </div>
                    <button type="button" class="close text-white ml-auto" 
                            data-dismiss="modal" aria-label="Close"
                            style="opacity: 0.8; font-size: 1.5rem;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                {{-- Body --}}
                <div class="modal-body p-4">

                    {{-- Step 1: Input Nomor HP --}}
                    <div id="step-hp">
                        <div class="text-center mb-4">
                            <div class="rounded-circle bg-primary d-inline-flex 
                                        align-items-center justify-content-center mb-3"
                                 style="width:60px; height:60px; 
                                        background: linear-gradient(135deg, #3d5af1, #224abe) !important;">
                                <i class="fab fa-whatsapp fa-2x text-white"></i>
                            </div>
                            <p class="text-muted small mb-0">
                                Masukkan nomor WhatsApp yang terdaftar<br>
                                untuk menerima kode OTP
                            </p>
                        </div>

                        <div id="alert-hp" class="d-none"></div>

                        <div class="form-group">
                            <label class="small font-weight-bold text-dark">
                                Nomor WhatsApp
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0 font-weight-bold">
                                        +62
                                    </span>
                                </div>
                                <input type="text" id="input-hp-cek"
                                       class="form-control border-left-0"
                                       placeholder="812xxxx"
                                       inputmode="numeric"
                                       maxlength="13"
                                       style="border-radius: 0 8px 8px 0;">
                            </div>
                            <small class="text-muted">Contoh: 81234567890</small>
                        </div>

                        <button type="button" class="btn btn-primary btn-block font-weight-bold"
                                id="btn-kirim-otp-cek"
                                style="border-radius: 10px; padding: 12px;">
                            <i class="fas fa-paper-plane mr-2"></i> Dapatkan Kode OTP
                        </button>
                    </div>

                    {{-- Step 2: Input OTP (hidden awalnya) --}}
                    <div id="step-otp" class="d-none">
                        <div class="text-center mb-4">
                            <div class="rounded-circle d-inline-flex align-items-center 
                                        justify-content-center mb-3"
                                 style="width:60px; height:60px; background: #eef0fb;">
                                <i class="fas fa-shield-alt fa-2x" style="color: #3d5af1;"></i>
                            </div>
                            <p class="font-weight-bold mb-1">Verifikasi OTP</p>
                            <p class="text-muted small mb-0">
                                Kode OTP dikirim ke WhatsApp<br>
                                <strong id="hp-tampil-cek"></strong>
                            </p>
                        </div>

                        <div id="alert-otp" class="d-none"></div>

                        {{-- 6-box OTP --}}
                        <div class="d-flex justify-content-center gap-2 mb-3" 
                             id="otp-boxes-cek"
                             style="gap: 8px;">
                            @for($i = 0; $i < 6; $i++)
                            <input type="text"
                                   class="otp-box-cek form-control text-center font-weight-bold"
                                   maxlength="1"
                                   inputmode="numeric"
                                   style="width: 45px; height: 52px; font-size: 1.4rem;
                                          border-radius: 10px; border: 2px solid #d1d3e2;
                                          color: #3d5af1; background: #f8f9fc;">
                            @endfor
                        </div>

                        <input type="hidden" id="otp-hidden-cek">

                        <div class="text-center mb-3">
                            <span class="badge badge-light border" id="otp-timer-cek"
                                  style="font-size: 0.8rem; padding: 6px 12px;">
                                <i class="fas fa-clock mr-1"></i>
                                Berlaku: <span id="otp-countdown-cek">3:00</span>
                            </span>
                        </div>

                        <button type="button" class="btn btn-primary btn-block font-weight-bold mb-2"
                                id="btn-verif-otp-cek" disabled
                                style="border-radius: 10px; padding: 12px;">
                            <i class="fas fa-check-circle mr-2"></i> Verifikasi & Lihat Booking
                        </button>

                        <div class="text-center">
                            <button type="button" class="btn btn-link btn-sm text-muted"
                                    id="btn-ganti-hp-cek">
                                <i class="fas fa-arrow-left mr-1"></i> Ganti nomor HP
                            </button>
                            <span class="text-muted">|</span>
                            <button type="button" class="btn btn-link btn-sm"
                                    id="btn-resend-otp-cek" disabled>
                                Kirim Ulang (<span id="resend-countdown-cek">60</span>s)
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
    (function() {
        // Elemen
        const stepHp      = document.getElementById('step-hp');
        const stepOtp     = document.getElementById('step-otp');
        const inputHp     = document.getElementById('input-hp-cek');
        const btnKirimOtp = document.getElementById('btn-kirim-otp-cek');
        const btnVerifOtp = document.getElementById('btn-verif-otp-cek');
        const btnGantiHp  = document.getElementById('btn-ganti-hp-cek');
        const btnResend   = document.getElementById('btn-resend-otp-cek');
        const hpTampil    = document.getElementById('hp-tampil-cek');
        const alertHp     = document.getElementById('alert-hp');
        const alertOtp    = document.getElementById('alert-otp');
        const otpBoxes    = document.querySelectorAll('.otp-box-cek');
        const otpHidden   = document.getElementById('otp-hidden-cek');
        const otpCountdown = document.getElementById('otp-countdown-cek');
        const resendCountdown = document.getElementById('resend-countdown-cek');

        let otpTimer, resendTimer, otpSisa = 180, resendSisa = 60;
        let noHpValue = '';

        // Reset modal saat ditutup
        document.getElementById('modalCekBooking')?.addEventListener('hidden.bs.modal', resetModal);
        document.getElementById('modalCekBooking')?.addEventListener('hide.bs.modal', resetModal);

        function resetModal() {
            stepHp.classList.remove('d-none');
            stepOtp.classList.add('d-none');
            inputHp.value = '';
            otpBoxes.forEach(b => { b.value = ''; b.style.borderColor = '#d1d3e2'; });
            if (otpHidden) otpHidden.value = '';
            clearInterval(otpTimer);
            clearInterval(resendTimer);
            showAlert(alertHp, '', '');
            showAlert(alertOtp, '', '');
        }

        function showAlert(el, type, msg) {
            if (!el) return;
            if (!msg) { el.classList.add('d-none'); el.innerHTML = ''; return; }
            el.className = `alert alert-${type} small`;
            el.innerHTML = `<i class="fas fa-${type === 'danger' ? 'times' : 'check'}-circle mr-1"></i>${msg}`;
            el.classList.remove('d-none');
        }

        function formatTime(s) {
            return Math.floor(s/60) + ':' + String(s%60).padStart(2,'0');
        }

        // Auto-sanitize phone number input
        inputHp?.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g,'').replace(/^0+/, '').replace(/^62/, '');
        });

        // OTP box interaction
        otpBoxes.forEach((box, idx) => {
            box.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g,'');
                if (this.value && otpBoxes[idx+1]) otpBoxes[idx+1].focus();
                updateOtpHidden();
            });
            box.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !this.value && otpBoxes[idx-1]) {
                    otpBoxes[idx-1].value = '';
                    otpBoxes[idx-1].focus();
                    updateOtpHidden();
                }
            });
            box.addEventListener('paste', function(e) {
                e.preventDefault();
                const text = (e.clipboardData||window.clipboardData).getData('text').replace(/\D/g,'');
                text.split('').forEach((ch,i) => { if(otpBoxes[i]) otpBoxes[i].value = ch; });
                updateOtpHidden();
            });
        });

        function updateOtpHidden() {
            const val = Array.from(otpBoxes).map(b => b.value).join('');
            if (otpHidden) otpHidden.value = val;
            if (btnVerifOtp) btnVerifOtp.disabled = val.length < 6;
        }

        // Kirim OTP
        btnKirimOtp?.addEventListener('click', async function() {
            const hp = inputHp?.value.replace(/\D/g,'');
            if (!hp || hp.length < 9) {
                showAlert(alertHp, 'danger', 'Masukkan nomor WhatsApp yang valid.');
                return;
            }

            btnKirimOtp.disabled = true;
            btnKirimOtp.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...';

            try {
                const res = await fetch('{{ route("user.cek.booking.kirim-otp") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify({ no_hp: '62' + hp })
                });
                const data = await res.json();

                if (data.success) {
                    noHpValue = '62' + hp;
                    hpTampil.textContent = '****' + hp.slice(-4);
                    stepHp.classList.add('d-none');
                    stepOtp.classList.remove('d-none');
                    startOtpTimer();
                    startResendTimer();
                } else {
                    showAlert(alertHp, 'danger', data.message || 'Nomor tidak ditemukan dalam sistem.');
                }
            } catch(e) {
                showAlert(alertHp, 'danger', 'Terjadi kesalahan. Coba lagi.');
            }

            btnKirimOtp.disabled = false;
            btnKirimOtp.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Dapatkan Kode OTP';
        });

        // Verifikasi OTP
        btnVerifOtp?.addEventListener('click', async function() {
            const otp = otpHidden?.value;
            if (!otp || otp.length < 6) return;

            btnVerifOtp.disabled = true;
            btnVerifOtp.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memverifikasi...';

            try {
                const res = await fetch('{{ route("user.cek.booking.verifikasi") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify({ no_hp: noHpValue, otp: otp })
                });
                const data = await res.json();

                if (data.success && data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    showAlert(alertOtp, 'danger', data.message || 'OTP salah atau kadaluarsa.');
                    btnVerifOtp.disabled = false;
                    btnVerifOtp.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Verifikasi & Lihat Booking';
                }
            } catch(e) {
                showAlert(alertOtp, 'danger', 'Terjadi kesalahan. Coba lagi.');
                btnVerifOtp.disabled = false;
                btnVerifOtp.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Verifikasi & Lihat Booking';
            }
        });

        // Ganti HP
        btnGantiHp?.addEventListener('click', function() {
            stepOtp.classList.add('d-none');
            stepHp.classList.remove('d-none');
            clearInterval(otpTimer);
            clearInterval(resendTimer);
            otpBoxes.forEach(b => b.value = '');
            showAlert(alertOtp, '', '');
        });

        // Resend OTP
        btnResend?.addEventListener('click', async function() {
            if (this.disabled) return;
            this.disabled = true;
            clearInterval(resendTimer);

            try {
                await fetch('{{ route("user.cek.booking.kirim-otp") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify({ no_hp: noHpValue })
                });
                showAlert(alertOtp, 'success', 'OTP baru telah dikirim.');
                otpSisa = 180;
                startOtpTimer();
                startResendTimer();
            } catch(e) {
                this.disabled = false;
            }
        });

        function startOtpTimer() {
            otpSisa = 180;
            clearInterval(otpTimer);
            if (otpCountdown) otpCountdown.textContent = formatTime(otpSisa);
            otpTimer = setInterval(() => {
                otpSisa--;
                if (otpCountdown) otpCountdown.textContent = formatTime(otpSisa);
                if (otpSisa <= 0) {
                    clearInterval(otpTimer);
                    if (btnVerifOtp) btnVerifOtp.disabled = true;
                }
            }, 1000);
        }

        function startResendTimer() {
            resendSisa = 60;
            if (btnResend) btnResend.disabled = true;
            if (resendCountdown) resendCountdown.textContent = resendSisa;
            clearInterval(resendTimer);
            resendTimer = setInterval(() => {
                resendSisa--;
                if (resendCountdown) resendCountdown.textContent = resendSisa;
                if (resendSisa <= 0) {
                    clearInterval(resendTimer);
                    if (btnResend) {
                        btnResend.disabled = false;
                        btnResend.innerHTML = 'Kirim Ulang';
                    }
                }
            }, 1000);
        }
    })();
    </script>
    @endif
</body>
</html>
