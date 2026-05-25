@extends('layouts.app')

@section('title', 'Tambah Unit Kantin')

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus-circle mr-2 text-primary"></i>Tambah Unit Kantin
        </h1>
        <a href="{{ route('admin.kantin.unit.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-store mr-1"></i> Form Data Unit
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

                    <form action="{{ route('admin.kantin.unit.store') }}"
                          method="POST"
                          enctype="multipart/form-data">
                        @csrf

                        {{-- ── Kode Unit (Read-Only, Auto-Generate) ── --}}
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">
                                Kode Unit <span class="text-danger">*</span>
                            </label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-qrcode"></i></span>
                                    </div>
                                    <input type="text"
                                           name="kode_unit"
                                           id="kode_unit"
                                           class="form-control font-weight-bold text-primary @error('kode_unit') is-invalid @enderror"
                                           value="{{ old('kode_unit', $kodeUnit) }}"
                                           readonly>
                                </div>
                                <small class="text-muted">Kode dibuat otomatis oleh sistem.</small>
                                @error('kode_unit')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
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
                                            {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>
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
                                           value="{{ old('harga', 0) }}"
                                           placeholder="Masukkan nominal harga setahun">
                                </div>
                                <small class="text-muted">Gunakan angka saja, tanpa titik atau koma.</small>
                                @error('harga')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- ── Ukuran Ruko ── --}}
                        <div class="form-group row">
                            <label for="ukuran_ruko" class="col-sm-3 col-form-label font-weight-bold">
                                Ukuran Ruko
                            </label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-expand-arrows-alt"></i></span>
                                    </div>
                                    <input type="text"
                                           name="ukuran_ruko"
                                           id="ukuran_ruko"
                                           class="form-control @error('ukuran_ruko') is-invalid @enderror"
                                           value="{{ old('ukuran_ruko') }}"
                                           placeholder="cth: 3x4 meter, 12 m2">
                                </div>
                                @error('ukuran_ruko')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- ── Deskripsi ── --}}
                        <div class="form-group row">
                            <label for="deskripsi" class="col-sm-3 col-form-label font-weight-bold">
                                Deskripsi Unit
                            </label>
                            <div class="col-sm-9">
                                <textarea name="deskripsi"
                                          id="deskripsi"
                                          class="form-control @error('deskripsi') is-invalid @enderror"
                                          rows="3"
                                          placeholder="Tambahkan keterangan detail mengenai unit ruko ini...">{{ old('deskripsi') }}</textarea>
                                @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- ── Aturan Pembayaran Unit ── --}}
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">
                                Aturan Pembayaran <span class="text-danger">*</span>
                            </label>
                            <div class="col-sm-9">
                                <div class="card bg-light border-0">
                                    <div class="card-body p-3">
                                        <div class="custom-control custom-radio mb-2">
                                            <input type="radio" id="pay_1_termin" name="metode_pembayaran_unit"
                                                   value="1_termin" class="custom-control-input"
                                                   {{ old('metode_pembayaran_unit') === '1_termin' ? 'checked' : '' }}>
                                            <label class="custom-control-label font-weight-bold text-dark" for="pay_1_termin">
                                                1 Termin Saja
                                            </label>
                                            <p class="small text-muted mb-0">Penyewa wajib melunasi seluruh biaya sewa di awal.</p>
                                        </div>
                                        <div class="custom-control custom-radio mb-2">
                                            <input type="radio" id="pay_2_termin" name="metode_pembayaran_unit"
                                                   value="2_termin" class="custom-control-input"
                                                   {{ old('metode_pembayaran_unit') === '2_termin' ? 'checked' : '' }}>
                                            <label class="custom-control-label font-weight-bold text-dark" for="pay_2_termin">
                                                2 Termin Saja
                                            </label>
                                            <p class="small text-muted mb-0">Penyewa wajib membayar dalam 2 tahap (50% awal, 50% bulan ke-6).</p>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="pay_fleksibel" name="metode_pembayaran_unit"
                                                   value="fleksibel" class="custom-control-input"
                                                   {{ old('metode_pembayaran_unit', 'fleksibel') === 'fleksibel' ? 'checked' : '' }}>
                                            <label class="custom-control-label font-weight-bold text-dark" for="pay_fleksibel">
                                                Fleksibel (1 & 2 Termin)
                                            </label>
                                            <p class="small text-muted mb-0">Penyewa bebas memilih antara 1 atau 2 termin saat booking.</p>
                                        </div>
                                    </div>
                                </div>
                                @error('metode_pembayaran_unit')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
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
                                               {{ old('status_unit', 'kosong') === 'kosong' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="status_kosong">
                                            <span class="badge badge-secondary">Kosong</span>
                                        </label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="status_terisi" name="status_unit"
                                               value="terisi" class="custom-control-input"
                                               {{ old('status_unit') === 'terisi' ? 'checked' : '' }}>
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

                        {{-- ── Dokumentasi (Multiple File Upload) ── --}}
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">
                                Upload Dokumen
                            </label>
                            <div class="col-sm-9">
                                <div class="custom-file">
                                    <input type="file"
                                           name="dokumen[]"
                                           id="dokumen"
                                           class="custom-file-input @error('dokumen') is-invalid @enderror @error('dokumen.*') is-invalid @enderror"
                                           multiple
                                           accept=".jpg,.jpeg,.png,.pdf">
                                    <label class="custom-file-label" for="dokumen">
                                        Pilih file (bisa lebih dari satu)
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

                                {{-- Preview list nama file --}}
                                <ul id="previewFileList" class="list-unstyled mt-2 mb-0"></ul>
                            </div>
                        </div>

                        <small class="text-danger"> *) Wajib diisi</small>
                        {{-- ── Tombol Submit ── --}}
                        <div class="form-group row mb-0">
                            <div class="col-sm-9 offset-sm-3">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save mr-1"></i> Simpan Unit
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
    $(document).ready(function() {
        // AJAX untuk generate Kode Unit otomatis saat Kategori berubah
        $('#kategori_id').on('change', function() {
            const kategoriId = $(this).val();
            const kodeInput = $('input[name="kode_unit"]');
            
            if (kategoriId) {
                // Tampilkan loading state sederhana
                kodeInput.val('Memuat...');
                
                $.ajax({
                    url: "{{ route('admin.kantin.unit.getNewKode') }}",
                    method: "GET",
                    data: { kategori_id: kategoriId },
                    success: function(response) {
                        kodeInput.val(response.kode);
                    },
                    error: function() {
                        kodeInput.val('');
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Gagal mengambil kode unit baru.',
                        });
                    }
                });
            } else {
                kodeInput.val('');
            }
        });
    });

    // Update label custom-file-input & preview nama file
    document.getElementById('dokumen').addEventListener('change', function () {
        const files   = this.files;
        const label   = this.nextElementSibling;
        const preview = document.getElementById('previewFileList');

        preview.innerHTML = '';

        if (files.length === 0) {
            label.textContent = 'Pilih file (bisa lebih dari satu)';
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
