@extends('layouts.app')

@section('title', 'Edit Data Penyewa')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-edit mr-2 text-primary"></i>Edit Data Penyewa
        </h1>
        <a href="{{ route('admin.kantin.penyewa.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Perubahan Data</h6>
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

                    <form action="{{ route('admin.kantin.penyewa.update', $penyewa->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" name="nama_lengkap" value="{{ old('nama_lengkap', $penyewa->user->nama_lengkap ?? $penyewa->user->name) }}" required placeholder="Nama lengkap penyewa">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">NIK <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('nik') is-invalid @enderror" name="nik" value="{{ old('nik', $penyewa->user->nik ?? '') }}" required placeholder="Nomor Induk Kependudukan">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Kontak / No. HP <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('no_hp') is-invalid @enderror" name="no_hp" value="{{ old('no_hp', $penyewa->user->no_hp) }}" required placeholder="Contoh: 08123456789">
                            </div>
                        </div>

                        <hr>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Nama Usaha <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('nama_usaha') is-invalid @enderror" name="nama_usaha" value="{{ old('nama_usaha', $penyewa->nama_usaha) }}" required placeholder="Contoh: Warung Kopi AA">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Alamat Usaha / Domisili <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <textarea class="form-control @error('alamat') is-invalid @enderror" name="alamat" rows="3" required placeholder="Alamat lengkap">{{ old('alamat', $penyewa->alamat) }}</textarea>
                            </div>
                        </div>

                        <div class="form-group row mt-4">
                            <div class="col-sm-9 offset-sm-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> Update Data Penyewa
                                </button>
                                <a href="{{ route('admin.kantin.penyewa.index') }}" class="btn btn-light border ml-2 text-dark">
                                    Batal
                                </a>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-info-circle mr-1"></i> Penting</h6>
                </div>
                <div class="card-body text-sm text-gray-700">
                    <p>Perubahan pada record NIK juga diperiksa apakah berbenturan dengan penyewa lainnya untuk menghindari redudansi identitas peminjam.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
