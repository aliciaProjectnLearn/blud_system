@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="container">

    {{-- Outer Row --}}
    <div class="row justify-content-center">

        <div class="col-xl-10 col-lg-12 col-md-9">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">

                    {{-- Nested Row within Card Body --}}
                    <div class="row">

                        {{-- Left side image (hidden on mobile) --}}
                        <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>

                        {{-- Right side - Login Form --}}
                        <div class="col-lg-6">
                            <div class="p-5">

                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">Selamat Datang!</h1>
                                </div>

                                {{-- Form Login --}}
                                <form class="user" method="POST" action="{{ route('login') }}">
                                    @csrf

                                    {{-- Email --}}
                                    <div class="form-group">
                                        <input
                                            type="email"
                                            class="form-control form-control-user @error('email') is-invalid @enderror"
                                            id="email"
                                            name="email"
                                            value="{{ old('email') }}"
                                            placeholder="Masukkan Email..."
                                            required
                                            autocomplete="email"
                                            autofocus>
                                        @error('email')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    {{-- Password --}}
                                    <div class="form-group">
                                        <input
                                            type="password"
                                            class="form-control form-control-user @error('password') is-invalid @enderror"
                                            id="password"
                                            name="password"
                                            placeholder="Password"
                                            required
                                            autocomplete="current-password">
                                        @error('password')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    {{-- Remember Me --}}
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox small">
                                            <input
                                                type="checkbox"
                                                class="custom-control-input"
                                                id="remember"
                                                name="remember"
                                                {{ old('remember') ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="remember">
                                                Ingat Saya
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Submit Button --}}
                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        <i class="fas fa-sign-in-alt fa-fw mr-1"></i> Login
                                    </button>

                                    <hr>

                                    {{-- Forgot Password --}}
                                    @if(Route::has('password.request'))
                                        <div class="text-center">
                                            <a class="small" href="{{ route('password.request') }}">
                                                Lupa Password?
                                            </a>
                                        </div>
                                    @endif

                                    {{-- Register --}}
                                    @if(Route::has('register'))
                                        <div class="text-center">
                                            <a class="small" href="{{ route('register') }}">
                                                Belum punya akun? Daftar!
                                            </a>
                                        </div>
                                    @endif

                                </form>
                                {{-- End Form Login --}}

                            </div>
                        </div>
                        {{-- End Right Side --}}

                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
