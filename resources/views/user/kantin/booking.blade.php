@extends('layouts.app')

@section('title', 'Form Pengajuan Sewa')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Form Pengajuan Sewa</h1>
        <a href="{{ route('user.kantin.katalog') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Katalog
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <!-- Info Unit -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Unit</h6>
                </div>
                <div class="card-body text-center">
                    @php
                        // Ambil relasi foto pertama
                        $photo = $ruko->dokumentasiUnit->first();
                        // Ambil path-nya, atau null jika tidak ada relasi
                        $path = $photo->file ?? null;
                    @endphp

                    @if($path)
                        {{-- Prioritas Mutlak: Tampilkan foto upload Admin --}}
                        {{-- Gunakan str_replace untuk Windows Compatibility --}}
                        <img src="{{ asset('storage/' . str_replace('\\', '/', $path)) }}" 
                             class="img-fluid rounded mb-3" 
                             alt="Foto Unit" 
                             style="width: 100%; object-fit: cover; height: 200px;">
                    @else
                        {{-- JIKA DAN HANYA JIKA Admin belum upload di DB --}}
                        {{-- Tampilkan gambar placeholder lokal yang netral (bukan dari internet) --}}
                        <img src="{{ asset('assets/img/no-image.png') }}" 
                             class="img-fluid rounded mb-3 shadow-sm" 
                             alt="Foto Tidak Tersedia" 
                             style="width: 100%; object-fit: cover; height: 200px;">
                    @endif
                    <h4 class="font-weight-bold">{{ $ruko->kode_unit ?? ($ruko->no_unit ?? 'Unit') }}</h4>
                    <p class="text-muted">{{ $ruko->kategori->nama ?? '-' }}</p>
                    <h3 class="text-success font-weight-bold">Rp {{ number_format($ruko->harga, 0, ',', '.') }}<small class="text-muted text-sm">/tahun</small></h3>
                    <hr>
                    <div class="text-left small mb-0">
                        <p class="mb-1"><strong>Durasi Sewa Minimum:</strong> 1 Tahun</p>
                        <p class="mb-1 text-info"><i class="fas fa-info-circle"></i> Setelah diajukan, permohonan maksimal diproses 2x24 jam oleh Admin.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Pengajuan -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Profil & Dokumen</h6>
                </div>
                <div class="card-body">
                    <form id="formBookingUnit" action="{{ route('user.kantin.store_booking', $ruko->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <h6 class="font-weight-bold mb-3">Informasi Pemohon</h6>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Nama Pemohon</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control bg-light" value="{{ $user->name }}" readonly>
                            </div>
                        </div>

                        <!-- Jika belum jadi penyewa, minta NIK dan Nama Usaha -->
                        @if(!$penyewa)
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">NIK <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" required placeholder="Masukkan NIK 16 digit" value="{{ old('nik', $user->nik) }}">
                                    @error('nik') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Nama Usaha/Toko <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="nama_usaha" class="form-control @error('nama_usaha') is-invalid @enderror" required placeholder="Contoh: Kedai Kopi Maju" value="{{ old('nama_usaha') }}">
                                    @error('nama_usaha') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" required placeholder="Masukkan Alamat Usaha/Tinggal">{{ old('alamat', $user->alamat) }}</textarea>
                                    @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        @else
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">NIK</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control bg-light" value="{{ $user->nik }}" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Nama Usaha</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control bg-light" value="{{ $penyewa->nama_usaha }}" readonly>
                                </div>
                            </div>
                        @endif

                        <hr>
                        <h6 class="font-weight-bold mb-3">Rencana Sewa & Pembayaran</h6>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="date" name="tgl_mulai" class="form-control @error('tgl_mulai') is-invalid @enderror" required value="{{ old('tgl_mulai', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}">
                                <small class="text-muted">Sewa berlaku selama 1 tahun sejak tanggal mulai.</small>
                                @error('tgl_mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="alert alert-info border-0 shadow-sm">
                            <div class="font-weight-bold mb-2 small"><i class="fas fa-calculator mr-1"></i> Estimasi Cicilan 2 Termin:</div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small">Termin 1 (Saat ini)</span>
                                <span class="font-weight-bold">Rp {{ number_format($ruko->harga / 2, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="small">Termin 2 (Bulan ke-6)</span>
                                <span class="font-weight-bold text-gray-700">Rp {{ number_format($ruko->harga / 2, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <hr>
                        <h6 class="font-weight-bold mb-3">Persyaratan Dokumen</h6>
                        
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Upload KTP <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <div class="custom-file">
                                    <input type="file" name="dokumen_ktp" class="custom-file-input" id="dokumen_ktp" required accept=".pdf,.jpg,.jpeg,.png">
                                    <label class="custom-file-label" for="dokumen_ktp">Pilih file (PDF/JPG/PNG max 2MB)...</label>
                                </div>
                                @error('dokumen_ktp') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                <small class="text-muted">KTP digunakan untuk verifikasi Identitas dan MOU Penyewaan.</small>
                            </div>
                        </div>

                        <div class="form-group text-right mt-4">
                            <button type="button" id="btnAjukanSewa" class="btn btn-primary px-4 py-2">
                                <i class="fas fa-paper-plane mr-1"></i> Ajukan Permohonan Sewa
                            </button>
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
    // To show file name on custom-file-input
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });

    // SweetAlert2 Confirmation
    document.getElementById('btnAjukanSewa')?.addEventListener('click', function(e) {
        e.preventDefault();

        // Validasi dasar HTML5 before SWAL
        const form = document.getElementById('formBookingUnit');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Pengajuan',
            text: 'Apakah Anda yakin ingin mengajukan permohonan sewa untuk unit ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#858796',
            confirmButtonText: '<i class="fas fa-check mr-1"></i> Ya, Ajukan',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>
@endpush
