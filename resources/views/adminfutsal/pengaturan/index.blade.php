@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pengaturan Sistem Futsal</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Pengaturan Jam Operasional & Harga</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.futsal.pengaturan.update', $pengaturan->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="jam_buka">Jam Buka</label>
                            <input type="time" class="form-control @error('jam_buka') is-invalid @enderror" id="jam_buka" name="jam_buka" value="{{ old('jam_buka', \Carbon\Carbon::parse($pengaturan->jam_buka)->format('H:i')) }}" required>
                            @error('jam_buka')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="jam_tutup">Jam Tutup</label>
                            <input type="time" class="form-control @error('jam_tutup') is-invalid @enderror" id="jam_tutup" name="jam_tutup" value="{{ old('jam_tutup', \Carbon\Carbon::parse($pengaturan->jam_tutup)->format('H:i')) }}" required>
                            @error('jam_tutup')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>
                        <h6 class="font-weight-bold text-gray-800">Pengaturan Harga</h6>

                        <div class="form-group">
                            <label for="harga_reguler_futsal">Harga Per Jam (Reguler)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="number" class="form-control @error('harga_reguler_futsal') is-invalid @enderror" id="harga_reguler_futsal" name="harga_reguler_futsal" value="{{ old('harga_reguler_futsal', $pengaturan->harga_reguler_futsal) }}" required>
                            </div>
                            @error('harga_reguler_futsal')
                                <div class="text-danger mt-1 small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="harga_event_futsal">Harga Per Hari (Event)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="number" class="form-control @error('harga_event_futsal') is-invalid @enderror" id="harga_event_futsal" name="harga_event_futsal" value="{{ old('harga_event_futsal', $pengaturan->harga_event_futsal) }}" required>
                            </div>
                            @error('harga_event_futsal')
                                <div class="text-danger mt-1 small">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>
                        <h6 class="font-weight-bold text-gray-800">
                            <i class="fas fa-ban mr-1 text-warning"></i> Pengaturan Jam Blokir Booking
                        </h6>
                        <p class="text-muted small">
                            Atur jam di mana booking tidak bisa dilakukan. 
                            Bisa digunakan untuk jam sekolah, kegiatan internal, atau keperluan lainnya.
                        </p>

                        {{-- Toggle aktif/nonaktif --}}
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="jam_blokir_aktif" 
                                       name="jam_blokir_aktif" 
                                       value="1"
                                       {{ $pengaturan->jam_blokir_aktif ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold" for="jam_blokir_aktif">
                                    Aktifkan Jam Blokir Booking
                                </label>
                            </div>
                            <small class="text-muted">Jika diaktifkan, booking pada jam yang ditentukan akan ditolak.</small>
                        </div>

                        {{-- Konten blokir (tampil hanya jika toggle aktif) --}}
                        <div id="blokir-settings" style="{{ $pengaturan->jam_blokir_aktif ? '' : 'display:none;' }}">

                            {{-- Pilih Hari --}}
                            <div class="form-group">
                                <label class="font-weight-bold">Hari Blokir</label>
                                <div class="d-flex flex-wrap" style="gap: 10px;">
                                    @php
                                        $hariList = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
                                        $hariBlokir = explode(',', $pengaturan->hari_blokir ?? 'Senin,Selasa,Rabu,Kamis,Jumat');
                                    @endphp
                                    @foreach($hariList as $hari)
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="hari_{{ $hari }}" 
                                               name="hari_blokir[]" 
                                               value="{{ $hari }}"
                                               {{ in_array($hari, $hariBlokir) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="hari_{{ $hari }}">{{ $hari }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Jam Mulai & Selesai --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="jam_blokir_mulai" class="font-weight-bold">Jam Mulai Blokir</label>
                                        <input type="time" 
                                               class="form-control" 
                                               id="jam_blokir_mulai" 
                                               name="jam_blokir_mulai"
                                               value="{{ old('jam_blokir_mulai', \Carbon\Carbon::parse($pengaturan->jam_blokir_mulai)->format('H:i')) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="jam_blokir_selesai" class="font-weight-bold">Jam Selesai Blokir</label>
                                        <input type="time" 
                                               class="form-control" 
                                               id="jam_blokir_selesai" 
                                               name="jam_blokir_selesai"
                                               value="{{ old('jam_blokir_selesai', \Carbon\Carbon::parse($pengaturan->jam_blokir_selesai)->format('H:i')) }}">
                                    </div>
                                </div>
                            </div>

                            {{-- Keterangan --}}
                            <div class="form-group">
                                <label for="keterangan_blokir" class="font-weight-bold">
                                    Keterangan <small class="text-muted">(opsional)</small>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="keterangan_blokir" 
                                       name="keterangan_blokir"
                                       placeholder="Contoh: Jam kegiatan sekolah, Jam sholat Jumat, dll"
                                       value="{{ old('keterangan_blokir', $pengaturan->keterangan_blokir) }}"
                                       maxlength="100">
                            </div>

                            {{-- Preview info --}}
                            <div class="alert alert-warning py-2" id="blokir-preview">
                                <i class="fas fa-info-circle mr-1"></i>
                                <span id="blokir-preview-text">Booking akan diblokir setiap hari yang dipilih.</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save fa-sm fa-fw mr-2"></i>Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
document.getElementById('jam_blokir_aktif').addEventListener('change', function() {
    document.getElementById('blokir-settings').style.display = 
        this.checked ? 'block' : 'none';
});
</script>
@endsection

