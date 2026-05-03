@extends('layouts.app')
@section('title', 'Tambah Lapangan Futsal')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-plus text-primary mr-2"></i>Tambah Lapangan Baru</h1>
        <a href="{{ route('admin.futsal.lapangan.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Lapangan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.futsal.lapangan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Nama Lapangan <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                   value="{{ old('nama') }}" placeholder="contoh: Lapangan Utama" required>
                            @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Ukuran</label>
                            <input type="text" name="ukuran" class="form-control @error('ukuran') is-invalid @enderror"
                                   value="{{ old('ukuran') }}" placeholder="contoh: 25x15 meter">
                            @error('ukuran')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Lokasi / Gedung</label>
                            <input type="text" name="lokasi" class="form-control @error('lokasi') is-invalid @enderror"
                                   value="{{ old('lokasi') }}" placeholder="contoh: Gedung Olahraga Lt. 1">
                            @error('lokasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Foto Lapangan</label>
                            <div class="custom-file">
                                <input type="file" name="foto" class="custom-file-input @error('foto') is-invalid @enderror"
                                       id="fotoLapangan" accept="image/*">
                                <label class="custom-file-label" for="fotoLapangan">Pilih gambar...</label>
                            </div>
                            @error('foto')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            <p class="small text-muted mt-1">Format: JPG, PNG. Maks: 2MB</p>
                            <div id="preview-foto" class="mt-2 d-none">
                                <img id="foto-preview" src="#" alt="Preview" class="img-fluid rounded border" style="max-height:150px;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror"
                                      rows="3" placeholder="Fasilitas, keterangan, dsb...">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Spesifikasi Teknis</label>
                            <textarea name="spesifikasi" class="form-control @error('spesifikasi') is-invalid @enderror"
                                      rows="2" placeholder="Jenis lantai, pencahayaan, dsb...">{{ old('spesifikasi') }}</textarea>
                            @error('spesifikasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <hr>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Simpan Lapangan
                </button>
                <a href="{{ route('admin.futsal.lapangan.index') }}" class="btn btn-secondary ml-2">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('fotoLapangan').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        e.target.nextElementSibling.innerText = e.target.files[0].name;
        const reader = new FileReader();
        reader.onload = function(ev) {
            document.getElementById('foto-preview').src = ev.target.result;
            document.getElementById('preview-foto').classList.remove('d-none');
        };
        reader.readAsDataURL(e.target.files[0]);
    }
});
</script>
@endpush
