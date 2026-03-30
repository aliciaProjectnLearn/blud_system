@extends('layouts.app')

@section('title', 'Tambah Penyewa Baru')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-plus mr-2 text-primary"></i>Tambah Penyewa Baru
        </h1>
        <a href="{{ route('adminkantin.penyewa.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Tambah Data Penyewa</h6>
                </div>
                <div class="card-body">
                    
                    @if ($errors->any())
                        <div class="alert alert-danger pb-0">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('adminkantin.penyewa.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required placeholder="Nama lengkap penyewa">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">NIK <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('nik') is-invalid @enderror" name="nik" value="{{ old('nik') }}" required placeholder="Nomor Induk Kependudukan (16 digit)">
                                <small class="text-muted">Nomor KTP untuk identitas utama sewa.</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Kontak / No. HP <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('no_hp') is-invalid @enderror" name="no_hp" value="{{ old('no_hp') }}" required placeholder="Contoh: 08123456789">
                            </div>
                        </div>

                        <hr>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Nama Usaha <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('nama_usaha') is-invalid @enderror" name="nama_usaha" value="{{ old('nama_usaha') }}" required placeholder="Contoh: Warung Kopi AA">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Alamat Usaha / Domisili <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <textarea class="form-control @error('alamat') is-invalid @enderror" name="alamat" rows="3" required placeholder="Alamat lengkap">{{ old('alamat') }}</textarea>
                            </div>
                        </div>

                        <div class="form-group row mt-4">
                            <div class="col-sm-9 offset-sm-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> Simpan Data Penyewa
                                </button>
                                <button type="reset" class="btn btn-warning text-dark ml-2">
                                    <i class="fas fa-undo mr-1"></i> Reset
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-info-circle mr-1"></i> Informasi</h6>
                </div>
                <div class="card-body text-sm text-gray-700">
                    <p>Pembuatan data penyewa baru melalui form ini akan secara otomatis mendaftarkan profil tersebut ke dalam database pengguna (user) sistem, namun ditujukan khusus untuk keperluan pendataan sewa.</p>
                    <p>Pastikan NIK yang diinput valid dan belum pernah terdaftar sebelumnya karena bersifat unik (unique key).</p>
                    <p><span class="text-danger">*</span> Wajib diisi.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
