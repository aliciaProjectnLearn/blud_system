@extends('layouts.app')

@section('title', 'Tambah Teknisi')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tambah Teknisi</h1>
    <a href="{{ route('adminac.teknisi.index') }}" class="btn btn-secondary btn-sm shadow-sm">
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
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('adminac.teknisi.store') }}" method="POST">
            @csrf

            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                    <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror" value="{{ old('nama_lengkap') }}" placeholder="Contoh: Budi Santoso" required>
                    @error('nama_lengkap')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Nama <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Budi" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Username <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" placeholder="Contoh: budi_teknisi" required>
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3 col-form-label">No. Handphone <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                    <input type="text" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror" value="{{ old('no_hp') }}" placeholder="Contoh: 081234567890" required>
                    @error('no_hp')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Email <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Contoh: budi@teknisi.com" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Password <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" minlength="6" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <hr>
            <div class="form-group row mb-0">
                <div class="col-sm-9 offset-sm-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                    <a href="{{ route('adminac.teknisi.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection