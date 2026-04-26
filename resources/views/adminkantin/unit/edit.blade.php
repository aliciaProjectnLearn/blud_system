@extends('layouts.app')

@section('title', 'Edit Unit ' . $unit->kode_unit)

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-pencil-alt mr-2 text-warning"></i>Edit Unit
            <span class="text-primary">{{ $unit->kode_unit }}</span>
        </h1>
        <a href="{{ route('admin.kantin.unit.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-warning">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-edit mr-1"></i> Form Edit Unit
                    </h6>
                </div>
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            <strong>Terdapat kesalahan input:</strong>
                            <ul class="mb-0 mt-1 pl-3">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.kantin.unit.update', $unit->id) }}"
                          method="POST"
                          enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- ── Kode Unit (Read-Only) ── --}}
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Kode Unit</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-qrcode"></i></span>
                                    </div>
                                    <input type="text"
                                           class="form-control font-weight-bold text-primary"
                                           value="{{ $unit->kode_unit }}"
                                           readonly>
                                </div>
                                <small class="text-muted">Kode unit tidak dapat diubah.</small>
                            </div>
                        </div>

                        {{-- ── Kategori ── --}}
                        <div class="form-group row">
                            <label for="kategori_id" class="col-sm-3 col-form-label font-weight-bold">
                                Kategori <span class="text-danger">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select name="kategori_id"
                                        id="kategori_id"
                                        class="form-control @error('kategori_id') is-invalid @enderror">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach ($kategoris as $kat)
                                        <option value="{{ $kat->id }}"
                                            {{ old('kategori_id', $unit->kategori_id) == $kat->id ? 'selected' : '' }}>
                                            {{ $kat->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kategori_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- ── Harga Sewa ── --}}
                        <div class="form-group row">
                            <label for="harga" class="col-sm-3 col-form-label font-weight-bold">
                                Harga Sewa pertahun <span class="text-danger">*</span>
                            </label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="number"
                                           name="harga"
                                           id="harga"
                                           class="form-control @error('harga') is-invalid @enderror"
                                           value="{{ old('harga', $unit->harga) }}"
                                           placeholder="Masukkan nominal harga setahun">
                                </div>
                                <small class="text-muted">Gunakan angka saja, tanpa titik atau koma.</small>
                                @error('harga')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- ── Status Unit ── --}}
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">
                                Status Unit <span class="text-danger">*</span>
                            </label>
                            <div class="col-sm-9">
                                <div class="d-flex">
                                    <div class="custom-control custom-radio mr-4">
                                        <input type="radio" id="status_kosong" name="status_unit"
                                               value="kosong" class="custom-control-input"
                                               {{ old('status_unit', $unit->status_unit) === 'kosong' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="status_kosong">
                                            <span class="badge badge-secondary">Kosong</span>
                                        </label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="status_terisi" name="status_unit"
                                               value="terisi" class="custom-control-input"
                                               {{ old('status_unit', $unit->status_unit) === 'terisi' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="status_terisi">
                                            <span class="badge badge-success">Terisi</span>
                                        </label>
                                    </div>
                                </div>
                                @error('status_unit')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr>

                        {{-- ── Dokumentasi Existing ── --}}
                        @if ($unit->dokumentasiUnit->count() > 0)
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label font-weight-bold">
                                    Dokumen Saat Ini
                                </label>
                                <div class="col-sm-9">
                                    <div class="border rounded p-3 bg-light">
                                        <p class="text-xs text-muted mb-2 font-weight-bold text-uppercase">
                                            Centang untuk menghapus file:
                                        </p>
                                        @foreach ($unit->dokumentasiUnit as $dok)
                                            <div class="custom-control custom-checkbox mb-2">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       id="hapus_dok_{{ $dok->id }}"
                                                       name="hapus_dokumen[]"
                                                       value="{{ $dok->id }}">
                                                <label class="custom-control-label" for="hapus_dok_{{ $dok->id }}">
                                                    {{-- Preview thumbnail jika gambar --}}
                                                    @php
                                                        $ext = strtolower(pathinfo($dok->file, PATHINFO_EXTENSION));
                                                        $isImage = in_array($ext, ['jpg','jpeg','png']);
                                                    @endphp
                                                    @if ($isImage)
                                                        <img src="{{ asset('storage/' . $dok->file) }}"
                                                             alt="preview"
                                                             class="img-thumbnail mr-2"
                                                             style="width:48px; height:48px; object-fit:cover;">
                                                    @else
                                                        <i class="fas fa-file-pdf text-danger fa-lg mr-2"></i>
                                                    @endif
                                                    <span class="text-sm">
                                                        {{ basename($dok->file) }}
                                                    </span>
                                                    <span class="badge badge-light ml-1">{{ $dok->tipe }}</span>
                                                    <a href="{{ asset('storage/' . $dok->file) }}"
                                                       target="_blank"
                                                       class="btn btn-xs btn-outline-info ml-2"
                                                       title="Lihat file">
                                                        <i class="fas fa-external-link-alt fa-xs"></i>
                                                    </a>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <small class="text-danger">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        File yang dicentang akan dihapus permanen setelah disimpan.
                                    </small>
                                </div>
                            </div>
                        @endif

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Tambah Dokumen</label>
                            <div class="col-sm-9">
                                <div class="custom-file">
                                    <input type="file"
                                           name="dokumen[]"
                                           id="dokumen"
                                           class="custom-file-input @error('dokumen') is-invalid @enderror @error('dokumen.*') is-invalid @enderror"
                                           multiple
                                           accept=".jpg,.jpeg,.png,.pdf">
                                    <label class="custom-file-label" for="dokumen">
                                        Pilih file baru (opsional)
                                    </label>
                                </div>
                                <small class="text-muted">
                                    Format: JPG, PNG, PDF. Maks. 5 MB/file, maks. 10 file.
                                </small>
                                @error('dokumen')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                @error('dokumen.*')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <ul id="previewFileList" class="list-unstyled mt-2 mb-0"></ul>
                            </div>
                        </div>

                        {{-- ── Tombol Submit ── --}}
                        <div class="form-group row mb-0">
                            <div class="col-sm-9 offset-sm-3">
                                <button type="submit" class="btn btn-warning px-4">
                                    <i class="fas fa-save mr-1"></i> Simpan Perubahan
                                </button>
                                <a href="{{ route('admin.kantin.unit.index') }}" class="btn btn-light ml-2">
                                    <i class="fas fa-times mr-1"></i> Batal
                                </a>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.getElementById('dokumen').addEventListener('change', function () {
        const files   = this.files;
        const label   = this.nextElementSibling;
        const preview = document.getElementById('previewFileList');

        preview.innerHTML = '';

        if (files.length === 0) {
            label.textContent = 'Pilih file baru (opsional)';
            return;
        }

        label.textContent = files.length + ' file dipilih';

        Array.from(files).forEach(function (file) {
            const li = document.createElement('li');
            li.className = 'small text-muted';
            li.innerHTML = '<i class="fas fa-paperclip mr-1"></i>' + file.name
                         + ' <span class="text-secondary">(' + (file.size / 1024).toFixed(1) + ' KB)</span>';
            preview.appendChild(li);
        });
    });
</script>
@endpush
