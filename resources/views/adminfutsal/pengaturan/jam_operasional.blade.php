@extends('layouts.app')

@section('title', 'Jam Operasional Lapangan')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-clock text-primary mr-2"></i> Jam Operasional Lapangan</h1>
        <a href="{{ route('adminfutsal.pengaturan.index') }}" class="btn btn-secondary btn-sm shadow-sm"><i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Kembali ke Pengaturan</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        @foreach($lapangans as $lapangan)
        <div class="col-12 mb-4">
            <div class="card shadow mb-4 border-left-primary">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">{{ $lapangan->nama }}</h6>
                </div>
                <div class="card-body p-0">
                    <form action="{{ route('adminfutsal.pengaturan.jam_operasional.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="lapangan_id" value="{{ $lapangan->id }}">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="15%" class="text-center">Hari</th>
                                        <th width="30%" class="text-center">Jam Buka</th>
                                        <th width="30%" class="text-center">Jam Tutup</th>
                                        <th width="25%" class="text-center">Status (Aktif/Libur)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                                        $jamOps = $lapangan->jamOperasional->keyBy('hari');
                                    @endphp
                                    @foreach($hariList as $hari)
                                        @php
                                            $jo = $jamOps->get($hari);
                                            $jamBuka = $jo ? substr($jo->jam_buka, 0, 5) : '07:00';
                                            $jamTutup = $jo ? substr($jo->jam_tutup, 0, 5) : '22:00';
                                            $isAktif = $jo ? $jo->is_aktif : true;
                                        @endphp
                                    <tr>
                                        <td class="align-middle text-center font-weight-bold">{{ $hari }}</td>
                                        <td>
                                            <input type="time" name="jam[{{ $hari }}][jam_buka]" class="form-control text-center" value="{{ $jamBuka }}" required>
                                        </td>
                                        <td>
                                            <input type="time" name="jam[{{ $hari }}][jam_tutup]" class="form-control text-center" value="{{ $jamTutup }}" required>
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="switch_{{ $lapangan->id }}_{{ $hari }}" name="jam[{{ $hari }}][is_aktif]" value="1" {{ $isAktif ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="switch_{{ $lapangan->id }}_{{ $hari }}">{{ $isAktif ? 'Buka' : 'Libur' }}</label>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-white text-right">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Pengaturan {{ $lapangan->nama }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
