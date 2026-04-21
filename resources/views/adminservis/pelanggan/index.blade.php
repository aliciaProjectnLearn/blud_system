@extends('layouts.app')

@section('title', 'Manajemen Pelanggan Servis')

@section('content')
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-4">
    <h1 class="h3 mb-2 mb-sm-0 text-gray-800">Manajemen Pelanggan</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent p-0 mb-0">
            <li class="breadcrumb-item"><a href="{{ route('adminservis.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pelanggan</li>
        </ol>
    </nav>
</div>

<!-- Statistik Ringkas -->
<div class="row">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pelanggan Terdaftar</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_pelanggan']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Pelanggan Aktif (30 Hari)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['pelanggan_aktif']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Transaksi Servis</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_transaksi']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter & Search -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Pelanggan Servis</h6>
        <form action="{{ route('adminservis.pelanggan.index') }}" method="GET" class="d-flex flex-wrap align-items-center mt-3 mt-md-0" style="gap: 10px;">
            <div class="input-group input-group-sm" style="width: 250px;">
                <input type="text" name="search" class="form-control" placeholder="Cari nama, email, hp..." value="{{ request('search') }}">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search fa-sm"></i>
                    </button>
                </div>
            </div>
            
            <select name="sort" class="form-control form-control-sm" style="width: auto;" onchange="this.form.submit()">
                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Booking Terbaru</option>
                <option value="total_booking" {{ request('sort') == 'total_booking' ? 'selected' : '' }}>Booking Terbanyak</option>
                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Booking Terlama</option>
            </select>

            @if(request()->anyFilled(['search', 'sort', 'min_booking']))
                <a href="{{ route('adminservis.pelanggan.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-undo mr-1"></i> Reset
                </a>
            @endif
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Pelanggan</th>
                        <th>Kontak</th>
                        <th class="text-center">Total Booking</th>
                        <th class="text-right">Total Transaksi</th>
                        <th>Terakhir Booking</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pelanggans as $pelanggan)
                    @php
                        $lastBooking = \Carbon\Carbon::parse($pelanggan->terakhir_booking);
                        $isAktif = $lastBooking->diffInDays(now()) <= 30;
                        $isJarang = $lastBooking->diffInDays(now()) > 90;
                    @endphp
                    <tr>
                        <td>
                            <div class="font-weight-bold text-dark">{{ $pelanggan->nama_lengkap ?? $pelanggan->name }}</div>
                            <small class="text-muted">{{ $pelanggan->email }}</small>
                        </td>
                        <td>{{ $pelanggan->no_hp ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge badge-light border px-3 py-2">{{ $pelanggan->booking_servis_count }}x</span>
                        </td>
                        <td class="text-right font-weight-bold text-success">
                            Rp {{ number_format($pelanggan->total_pengeluaran ?? 0, 0, ',', '.') }}
                        </td>
                        <td>
                            @if($pelanggan->terakhir_booking)
                                {{ $lastBooking->translatedFormat('d M Y') }}
                                <div class="text-xs text-muted">{{ $lastBooking->diffForHumans() }}</div>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($isAktif)
                                <span class="badge badge-success px-3">Aktif</span>
                            @elseif($isJarang)
                                <span class="badge badge-danger px-3">Jarang</span>
                            @else
                                <span class="badge badge-warning text-white px-3">Normal</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('adminservis.pelanggan.show', $pelanggan->id) }}" class="btn btn-info btn-sm btn-circle" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <img src="{{ asset('img/undraw_no_data.svg') }}" alt="No Data" style="width: 150px; opacity: 0.5;">
                            <p class="mt-3 text-muted">Belum ada data pelanggan yang melakukan booking servis.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $pelanggans->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection
