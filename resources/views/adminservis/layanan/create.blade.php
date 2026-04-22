@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Layanan Servis</h1>
        <a href="{{ route('adminservis.layanan.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('adminservis.layanan.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="nama_layanan">Nama Layanan <span class="text-danger">*</span></label>
                    <input type="text" name="nama_layanan" id="nama_layanan"
                        class="form-control @error('nama_layanan') is-invalid @enderror"
                        value="{{ old('nama_layanan') }}" placeholder="Contoh: Ganti Oli Motor">
                    @error('nama_layanan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="tipe_kendaraan">Tipe Kendaraan <span class="text-danger">*</span></label>
                    <select name="tipe_kendaraan" id="tipe_kendaraan"
                        class="form-control @error('tipe_kendaraan') is-invalid @enderror">
                        <option value="">-- Pilih Tipe --</option>
                        <option value="motor" {{ old('tipe_kendaraan') == 'motor' ? 'selected' : '' }}>Motor</option>
                        <option value="mobil" {{ old('tipe_kendaraan') == 'mobil' ? 'selected' : '' }}>Mobil</option>
                        <option value="keduanya" {{ old('tipe_kendaraan') == 'keduanya' ? 'selected' : '' }}>Semua</option>
                    </select>
                    @error('tipe_kendaraan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="harga_estimasi">Harga Estimasi (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="harga_estimasi" id="harga_estimasi"
                        class="form-control @error('harga_estimasi') is-invalid @enderror"
                        value="{{ old('harga_estimasi') }}" min="0" placeholder="Contoh: 50000">
                    @error('harga_estimasi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" rows="3"
                        class="form-control @error('deskripsi') is-invalid @enderror"
                        placeholder="Deskripsi layanan (opsional)">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" 
                               id="is_active" name="is_active" value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">Layanan Aktif</label>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <a href="{{ route('adminservis.layanan.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
