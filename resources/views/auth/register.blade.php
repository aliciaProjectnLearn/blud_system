@extends('layouts.auth')

@section('title', 'Register')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #4e73df;
        }

        .register-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .register-card {
            width: 100%;
            max-width: 520px;
            background: #fff;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .2);
        }

        .register-header {
            padding: 2rem 2.5rem 1.75rem;
            border-bottom: 1px solid #f0f2f8;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .register-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-bottom: 1rem;
        }

        .register-header h1 {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1a1f36;
        }

        .register-header p {
            font-size: 13px;
            color: #8892a4;
        }

        .register-body {
            padding: 1.75rem 2.5rem 2.25rem;
        }

        .register-field {
            margin-bottom: 1rem;
        }

        .register-field label {
            font-size: 11.5px;
            font-weight: 600;
            color: #5a6478;
            margin-bottom: .4rem;
            display: block;
            text-transform: uppercase;
        }

        .form-control {
            width: 100%;
            height: 46px;
            border: 1.5px solid #d6e4f7;
            border-radius: 50px;
            padding: 0 18px;
            font-size: 13.5px;
            background: #ddeaf8;
        }

        .form-control:focus {
            border-color: #4e73df;
            background: #d0e2f5;
            box-shadow: 0 0 0 3px rgba(78, 115, 223, .15);
        }

        .btn-register {
            width: 100%;
            height: 46px;
            background: #4e73df;
            border: none;
            border-radius: 50px;
            color: #fff;
            font-weight: 600;
        }

        .btn-register:hover {
            background: #2e59d9;
        }

        .register-links {
            text-align: center;
            margin-top: 1rem;
        }

        .register-links a {
            color: #4e73df;
            font-weight: 600;
            text-decoration: none;
        }
    </style>
@endpush

@section('content')
    <div class="register-page">
        <div class="register-card">

            <div class="register-header">
                <img src="{{ asset('img/logo_smk.png') }}" class="register-logo">
                <h1>Buat Akun Baru</h1>
                <p>Daftar untuk mengakses layanan BLUD</p>
            </div>

            <div class="register-body">

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="register-field">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap') }}"
                            required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 register-field">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" value="{{ old('username') }}"
                                required>
                        </div>

                        <div class="col-md-6 register-field">
                            <label>No HP</label>
                            <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}" required>
                        </div>
                    </div>

                    <div class="register-field">
                        <label>NIK</label>
                        <input type="text" name="nik" class="form-control" value="{{ old('nik') }}" required>
                    </div>

                    <div class="register-field">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 register-field">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="col-md-6 register-field">
                            <label>Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-register">
                        Daftar Sekarang
                    </button>

                    <div class="register-links">
                        <a href="{{ route('login') }}">
                            Sudah punya akun? Login
                        </a>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
