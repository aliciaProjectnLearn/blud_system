@extends('layouts.app')

@section('title', 'Tambah Layanan AC')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tambah Layanan AC</h1>
    <a href="{{ route('adminac.layanan.index') }}" class="btn btn-secondary btn-sm shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50 mr-2"></i> Kembali
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Form Tambah Layanan AC</h6>
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

        <form action="{{ route('adminac.layanan.store') }}" method="POST">
            @csrf
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Kategori <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                    <select name="kategori_id" class="form-control @error('kategori_id') is-invalid @enderror" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($kategoris as $k)
                            <option value="{{ $k->id }}" {{ old('kategori_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Nama Layanan <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" placeholder="Contoh: Cleaning AC" required>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Deskripsi / Kapasitas AC</label>
                <div class="col-sm-9">
                    <input type="text" name="kapasitas_ac" class="form-control @error('kapasitas_ac') is-invalid @enderror" value="{{ old('kapasitas_ac') }}" placeholder="Contoh: 0.5 - 1 PK">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Harga Jasa (Rp) <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                    <input type="number" name="harga_jasa" class="form-control @error('harga_jasa') is-invalid @enderror" value="{{ old('harga_jasa') }}" placeholder="Contoh: 75000" min="0" required>
                </div>
            </div>

            <hr>
            <div class="form-group row mb-0">
                <div class="col-sm-9 offset-sm-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                    <a href="{{ route('adminac.layanan.index') }}" class="btn btn-secondary">
                        Batal
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
