@extends('layouts.app')

@section('title', 'Dashboard Kasir Futsal')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Kasir Futsal</h1>
        <span class="text-muted">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Booking Aktif (Hari Ini)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBookingAktif }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Event Aktif (Pending)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalEventAktif }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Transaksi Lunas Hari Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahTransaksiSelesai }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Pemasukan Hari Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalPemasukanHariIni, 0, ',', '.') }}</div>
                            <div class="text-xs text-muted mt-1">Diperbarui setelah kasir proses pembayaran</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body d-flex justify-content-center gap-3">
                    <a href="{{ route('kasirfutsal.booking.index') }}" class="btn btn-primary btn-icon-split btn-lg mx-2">
                        <span class="icon text-white-50"><i class="fas fa-calendar-check"></i></span>
                        <span class="text">Menu Booking</span>
                    </a>
                    <a href="{{ route('kasirfutsal.pembayaran.index') }}" class="btn btn-success btn-icon-split btn-lg mx-2">
                        <span class="icon text-white-50"><i class="fas fa-cash-register"></i></span>
                        <span class="text">Menu Pembayaran</span>
                    </a>
                    <a href="{{ route('kasirfutsal.laporan.index') }}" class="btn btn-info btn-icon-split btn-lg mx-2">
                        <span class="icon text-white-50"><i class="fas fa-file-alt"></i></span>
                        <span class="text">Laporan Harian</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
