@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
</div>

{{-- Stats Cards --}}
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pelanggan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPelanggan }}</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Transaksi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTransaksi }}</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-clipboard-list fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Pendapatan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Booking Menunggu</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBookingPending }}</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-clock fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Progres & Notifikasi --}}
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Progres Pendapatan</h6>
            </div>
            <div class="card-body">
                <h4 class="small font-weight-bold">
                    Sistem Cuci AC
                    <span class="float-right">Rp {{ number_format($pendapatanAC, 0, ',', '.') }}</span>
                </h4>
                <div class="progress mb-4">
                    <div class="progress-bar bg-primary" role="progressbar"
                         style="width: {{ $persenAC }}%" aria-valuenow="{{ $persenAC }}"
                         aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <h4 class="small font-weight-bold">
                    Booking Lapangan Futsal
                    <span class="float-right">Rp {{ number_format($pendapatanFutsal, 0, ',', '.') }}</span>
                </h4>
                <div class="progress mb-4">
                    <div class="progress-bar bg-success" role="progressbar"
                         style="width: {{ $persenFutsal }}%" aria-valuenow="{{ $persenFutsal }}"
                         aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <h4 class="small font-weight-bold">
                    Sewa Ruko Kantin
                    <span class="float-right">Rp {{ number_format($pendapatanRuko, 0, ',', '.') }}</span>
                </h4>
                <div class="progress">
                    <div class="progress-bar bg-warning" role="progressbar"
                         style="width: {{ $persenRuko }}%" aria-valuenow="{{ $persenRuko }}"
                         aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Notifikasi Terbaru</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($aktivitas as $a)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <i class="fas fa-bell text-primary mr-2"></i>
                                <strong>{{ $a->nama }}</strong>
                                <span class="text-muted">{{ $a->aktivitas }}</span>
                            </div>
                            <small class="text-gray-500 ml-2 text-nowrap">
                                {{ \Carbon\Carbon::parse($a->created_at)->diffForHumans() }}
                            </small>
                        </div>
                    </li>
                    @empty
                    <li class="list-group-item text-center text-muted">Tidak ada aktivitas terbaru.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- Transaksi Terbaru --}}
<div class="row">
    <div class="col-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Transaksi Terbaru</h6>
                <a href="{{ route('transaksi.index') }}" class="btn btn-sm btn-outline-primary">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Layanan</th>
                                <th>Nama Pelanggan</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                                <th class="d-none d-md-table-cell">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaksiTerbaru as $t)
                            <tr>
                                <td>{{ $t->layanan }}</td>
                                <td>{{ $t->nama_user }}</td>
                                <td>Rp {{ number_format($t->jumlah_bayar, 0, ',', '.') }}</td>
                                <td><span class="badge badge-info">{{ $t->status }}</span></td>
                                <td class="d-none d-md-table-cell">{{ $t->tgl_bayar ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada transaksi.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
