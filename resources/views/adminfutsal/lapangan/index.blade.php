@extends('layouts.app')
@section('title', 'Manajemen Lapangan Futsal')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-door-open text-primary mr-2"></i>Manajemen Lapangan Futsal</h1>
        <a href="{{ route('admin.futsal.lapangan.create') }}" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-plus fa-sm mr-1"></i> Tambah Lapangan
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <div class="row">
        @forelse($lapangans as $lapangan)
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card shadow h-100">
                {{-- Foto Lapangan --}}
                @if($lapangan->foto)
                    <img src="{{ asset('storage/' . $lapangan->foto) }}"
                         class="card-img-top"
                         style="height: 180px; object-fit: cover;"
                         alt="{{ $lapangan->nama }}">
                @else
                    <div class="d-flex align-items-center justify-content-center bg-light"
                         style="height:180px;">
                        <i class="fas fa-futbol fa-4x text-muted opacity-50"></i>
                    </div>
                @endif

                <div class="card-body">
                    <h5 class="card-title font-weight-bold text-primary">{{ $lapangan->nama }}</h5>
                    <table class="table table-sm table-borderless mb-2" style="font-size:.85rem;">
                        <tr>
                            <td class="text-muted pl-0" style="width:35%"><i class="fas fa-ruler-combined mr-1"></i>Ukuran</td>
                            <td>{{ $lapangan->ukuran ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted pl-0"><i class="fas fa-map-marker-alt mr-1"></i>Lokasi</td>
                            <td>{{ $lapangan->lokasi ?? '-' }}</td>
                        </tr>
                        @if($lapangan->deskripsi)
                        <tr>
                            <td class="text-muted pl-0"><i class="fas fa-info-circle mr-1"></i>Deskripsi</td>
                            <td>{{ Str::limit($lapangan->deskripsi, 60) }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
                <div class="card-footer bg-white border-top-0 d-flex gap-2">
                    <a href="{{ route('admin.futsal.lapangan.edit', $lapangan->id) }}"
                       class="btn btn-warning btn-sm flex-fill">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    <form action="{{ route('admin.futsal.lapangan.destroy', $lapangan->id) }}"
                          method="POST"
                          class="flex-fill"
                          onsubmit="return confirm('Yakin hapus lapangan ini? Data jam operasional juga akan terhapus.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm w-100">
                            <i class="fas fa-trash mr-1"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-door-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada lapangan yang terdaftar.</p>
                    <a href="{{ route('admin.futsal.lapangan.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i> Tambah Lapangan Pertama
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
