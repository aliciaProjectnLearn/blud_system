@extends('layouts.app')

@section('title', 'Jam Operasional')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pengaturan Jam Operasional</h1>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Pengaturan Jam Operasional</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('adminfutsal.pengaturan.update', $pengaturan->id) }}" method="POST">
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

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save fa-sm fa-fw mr-2"></i>Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
