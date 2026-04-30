@extends('layouts.app')

@section('title', 'Tambah Teknisi Servis')

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tambah Teknisi Servis</h1>
    <a href="{{ route('admin.servis.teknisi.index') }}" class="btn btn-secondary btn-sm shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50 mr-2"></i> Kembali
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Form Tambah Teknisi</h6>
    </div>
    <div class="card-body">

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong><i class="fas fa-exclamation-circle mr-1"></i> Terdapat kesalahan:</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.servis.teknisi.store') }}" method="POST">
            @csrf

            {{-- Nama Lengkap --}}
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">
                    Nama Lengkap <span class="text-danger">*</span>
                </label>
                <div class="col-sm-9">
                    <input type="text" name="nama_lengkap"
                           class="form-control @error('nama_lengkap') is-invalid @enderror"
                           value="{{ old('nama_lengkap') }}"
                           placeholder="Contoh: Budi Santoso" required>
                    @error('nama_lengkap')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Nama Panggilan --}}
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">
                    Nama <span class="text-danger">*</span>
                </label>
                <div class="col-sm-9">
                    <input type="text" name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}"
                           placeholder="Contoh: Budi" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Username --}}
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">
                    Username <span class="text-danger">*</span>
                </label>
                <div class="col-sm-9">
                    <input type="text" name="username"
                           class="form-control @error('username') is-invalid @enderror"
                           value="{{ old('username') }}"
                           placeholder="Contoh: budi_servis" required>
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Email --}}
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">
                    Email <span class="text-danger">*</span>
                </label>
                <div class="col-sm-9">
                    <input type="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}"
                           placeholder="Contoh: budi@servis.com" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- No. HP --}}
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">
                    No. Handphone <span class="text-danger">*</span>
                </label>
                <div class="col-sm-9">
                    <input type="text" name="no_hp"
                           class="form-control @error('no_hp') is-invalid @enderror"
                           value="{{ old('no_hp') }}"
                           placeholder="Contoh: 081234567890" required>
                    @error('no_hp')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Tipe Teknisi (Role) --}}
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">
                    Tipe Teknisi <span class="text-danger">*</span>
                </label>
                <div class="col-sm-9">
                    <select name="role_id"
                            class="form-control @error('role_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Tipe Teknisi --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->nama }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Pilih sesuai spesialisasi kendaraan yang ditangani.</small>
                    @error('role_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Password --}}
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">
                    Password <span class="text-danger">*</span>
                </label>
                <div class="col-sm-9">
                    <input type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           minlength="8" required>
                    <small class="text-muted">Minimal 8 karakter.</small>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Konfirmasi Password --}}
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">
                    Konfirmasi Password <span class="text-danger">*</span>
                </label>
                <div class="col-sm-9">
                    <input type="password" name="password_confirmation"
                           class="form-control"
                           minlength="8" required>
                </div>
            </div>

            <hr>
            <div class="form-group row mb-0">
                <div class="col-sm-9 offset-sm-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                    <a href="{{ route('admin.servis.teknisi.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </div>
        </form>

    </div>
</div>

@endsection
