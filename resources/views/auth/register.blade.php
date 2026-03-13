@extends('layouts.auth')

@section('title', 'Register')

@section('content')
<div class="container">

    <div class="row justify-content-center">

        <div class="col-xl-10 col-lg-12 col-md-9">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">

                    <div class="row">

                        {{-- Kiri - Gambar --}}
                        <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>

                        {{-- Kanan - Form Register --}}
                        <div class="col-lg-7">
                            <div class="p-5">

                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">Buat Akun Baru!</h1>
                                </div>

                                <form class="user" method="POST" action="{{ route('register') }}">
                                    @csrf

                                    {{-- Nama --}}
                                    <div class="form-group">
                                        <input
                                            type="text"
                                            class="form-control form-control-user @error('name') is-invalid @enderror"
                                            name="name"
                                            value="{{ old('name') }}"
                                            placeholder="Nama Lengkap"
                                            required
                                            autofocus>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Email --}}
                                    <div class="form-group">
                                        <input
                                            type="email"
                                            class="form-control form-control-user @error('email') is-invalid @enderror"
                                            name="email"
                                            value="{{ old('email') }}"
                                            placeholder="Alamat Email"
                                            required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Password --}}
                                    <div class="form-group row">
                                        <div class="col-sm-6 mb-3 mb-sm-0">
                                            <input
                                                type="password"
                                                class="form-control form-control-user @error('password') is-invalid @enderror"
                                                name="password"
                                                placeholder="Password"
                                                required>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-sm-6">
                                            <input
                                                type="password"
                                                class="form-control form-control-user"
                                                name="password_confirmation"
                                                placeholder="Konfirmasi Password"
                                                required>
                                        </div>
                                    </div>

                                    {{-- Submit --}}
                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        <i class="fas fa-user-plus fa-fw mr-1"></i> Daftar Sekarang
                                    </button>

                                </form>

                                <hr>

                                <div class="text-center">
                                    <a class="small" href="{{ route('login') }}">
                                        Sudah punya akun? Login!
                                    </a>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection