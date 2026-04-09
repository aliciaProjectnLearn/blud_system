@extends('layouts.app')

@section('title', 'Katalog Unit Kantin')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Katalog Unit Tersedia</h1>
        <a href="{{ route('user.kantin.dashboard') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        @forelse($rukoList as $ruko)
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100">
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
                             class="card-img-top" 
                             alt="Foto Unit" 
                             style="height: 200px; object-fit: cover;">
                    @else
                        {{-- JIKA DAN HANYA JIKA Admin belum upload di DB --}}
                        {{-- Tampilkan gambar placeholder lokal yang netral (bukan dari internet) --}}
                        <img src="{{ asset('assets/img/no-image.png') }}" 
                             class="card-img-top" 
                             alt="Foto Tidak Tersedia" 
                             style="height: 200px; object-fit: cover;">
                    @endif
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title font-weight-bold text-primary">{{ $ruko->kode_unit ?? ($ruko->no_unit ?? 'Unit') }}</h5>
                        <p class="card-text text-muted mb-1"><i class="fas fa-tags"></i> Kategori: {{ $ruko->kategori->nama ?? '-' }}</p>
                        <p class="font-weight-bold text-success mb-3">Rp {{ number_format($ruko->harga, 0, ',', '.') }} / Tahun</p>
                        <a href="{{ route('user.kantin.booking', $ruko->id) }}" class="btn btn-primary btn-block mt-auto">Sewa Sekarang</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-box-open fa-4x text-gray-300 mb-3"></i>
                <h5 class="text-muted">Belum ada unit ruko/kantin yang tersedia saat ini.</h5>
            </div>
        @endforelse
    </div>
</div>
@endsection
