@extends('layouts.auth')

@section('title', 'Login')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><rect width='24' height='24' rx='4' fill='%234e73df'/><g stroke='white' stroke-width='2' stroke-linecap='round'><path d='M12 2v20M2 12h20'/><path d='m6 6 12 12M18 6 6 18'/></g></svg>">


    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #4e73df;
        }

        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        /* ── Card ── */
        .login-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .2);
        }

        /* ── Header ── */
        .login-header {
            padding: 2rem 2.5rem 1.75rem;
            border-bottom: 1px solid #f0f2f8;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .login-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-bottom: 1rem;
        }

        .login-header h1 {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1a1f36;
            letter-spacing: -.3px;
            margin-bottom: .25rem;
        }

        .login-header p {
            font-size: 13px;
            color: #8892a4;
            margin: 0;
        }

        /* ── Body ── */
        .login-body {
            padding: 1.75rem 2.5rem 2.25rem;
        }

        /* ── Alert ── */
        .login-alert {
            background: #fdf2f2;
            border: 1px solid #fbd5d5;
            border-radius: 10px;
            padding: .75rem 1rem;
            margin-bottom: 1.25rem;
            font-size: 13px;
            color: #c81e1e;
        }

        /* ── Fields ── */
        .login-field {
            margin-bottom: 1rem;
        }

        .login-field label {
            display: block;
            font-size: 11.5px;
            font-weight: 600;
            color: #5a6478;
            margin-bottom: .4rem;
            letter-spacing: .3px;
            text-transform: uppercase;
        }

        .login-field .form-control {
            width: 100%;
            height: 46px;
            border: 1.5px solid #d6e4f7;
            border-radius: 50px;
            padding: 0 18px;
            font-size: 13.5px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #2d3748;
            outline: none;
            background: #ddeaf8;
            transition: border-color .2s, box-shadow .2s;
            box-shadow: none;
            appearance: none;
        }

        .login-field .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 3px rgba(78, 115, 223, .15);
            background: #d0e2f5;
        }

        .login-field .form-control.is-invalid {
            border-color: #e74a3b;
        }

        .login-field .form-control.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(231, 74, 59, .12);
        }

        .login-field .form-control::placeholder {
            color: #8fafd4;
        }

        .login-field .invalid-feedback {
            font-size: 12px;
            color: #e74a3b;
            margin-top: .35rem;
            padding-left: 4px;
            display: block;
        }

        /* ── Password toggle ── */
        .pw-wrap {
            position: relative;
        }

        .pw-wrap .form-control {
            padding-right: 48px;
        }

        .pw-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #8fafd4;
            padding: 0;
            line-height: 1;
            transition: color .2s;
        }

        .pw-toggle:hover {
            color: #4e73df;
        }

        /* ── Meta row ── */
        .login-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.4rem;
        }

        .login-remember {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 13px;
            color: #6e7a8a;
            cursor: pointer;
            user-select: none;
            font-weight: 500;
            margin: 0;
        }

        .login-remember input[type="checkbox"] {
            accent-color: #4e73df;
            width: 14px;
            height: 14px;
            cursor: pointer;
        }

        .login-forgot {
            font-size: 13px;
            color: #4e73df;
            font-weight: 600;
            text-decoration: none;
        }

        .login-forgot:hover {
            text-decoration: underline;
        }

        /* ── Button ── */
        .btn-login {
            width: 100%;
            height: 46px;
            background: #4e73df;
            border: none;
            border-radius: 50px;
            color: #fff;
            font-size: 14px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background .15s, transform .1s;
        }

        .btn-login:hover {
            background: #2e59d9;
            color: #fff;
        }

        .btn-login:active {
            transform: scale(.98);
        }

        /* ── Divider ── */
        .login-divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 1.1rem 0;
            font-size: 12px;
            color: #c0c8d8;
        }

        .login-divider::before,
        .login-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #edf0f7;
        }

        /* ── Footer links ── */
        .login-links {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
        }

        .login-links a {
            color: #4e73df;
            font-weight: 600;
            text-decoration: none;
        }

        .login-links a:hover {
            text-decoration: underline;
        }

        /* ── Responsive ── */
        @media (max-width: 480px) {
            .login-header {
                padding: 1.75rem 1.5rem 1.5rem;
            }

            .login-body {
                padding: 1.75rem 1.5rem;
            }

            .login-links {
                flex-direction: column;
                gap: 8px;
                align-items: center;
            }
        }
    </style>
@endpush

@section('content')
    <div class="login-page">
        <div class="login-card">

            {{-- Header --}}
            <div class="login-header">
                <img src="{{ asset('img/logo_smk.png') }}" alt="Logo BLUD System" class="login-logo">
                <h1>Selamat Datang di</h1>
                <h1>BLUD SMKN 1 CIREBON</h1>
                <p>Masuk untuk mengakses dashboard Anda</p>
            </div>

            {{-- Body --}}
            <div class="login-body">

                {{-- Error umum --}}
                @if ($errors->any() && !$errors->has('email') && !$errors->has('password'))
                    <div class="login-alert">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" novalidate>
                    @csrf

                    {{-- Email --}}
                    <div class="login-field">
                        <label for="email">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                            name="email" value="{{ old('email') }}" placeholder="nama@email.com" required
                            autocomplete="email" autofocus>
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="login-field">
                        <label for="password">Password</label>
                        <div class="pw-wrap">
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" placeholder="Masukkan password" required
                                autocomplete="current-password">
                            <button type="button" class="pw-toggle" id="pw-toggle-btn" onclick="togglePassword()"
                                aria-label="Tampilkan / sembunyikan password">
                                <svg id="eye-icon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>
                            @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Remember Me + Lupa Password --}}
                    <div class="login-meta">
                        <label class="login-remember">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            Ingat Saya
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="login-forgot">Lupa Password?</a>
                        @endif
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="btn-login">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="white"
                            stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3" />
                        </svg>
                        Masuk
                    </button>

                    {{-- Link Daftar & Reset --}}
                    @if (Route::has('register') || Route::has('password.request'))
                        <div class="login-divider">atau</div>
                        <div class="login-links">
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}">Belum punya akun? Daftar</a>
                            @endif
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}">Reset Password</a>
                            @endif
                        </div>
                    @endif

                </form>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const btn = document.getElementById('pw-toggle-btn');
            const icon = document.getElementById('eye-icon');

            const eyeOpen = `<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/>`;
            const eyeClosed =
                `<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>`;

            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = eyeClosed;
                btn.style.color = '#4e73df';
            } else {
                input.type = 'password';
                icon.innerHTML = eyeOpen;
                btn.style.color = '';
            }
        }
    </script>
@endpush
