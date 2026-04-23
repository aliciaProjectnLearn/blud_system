@extends('layouts.app')

@section('title', 'Laporan Transaksi Servis Kendaraan')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-bar mr-2 text-primary"></i>Laporan Transaksi Servis Kendaraan
        </h1>
    </div>

    {{-- Filter Card --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter mr-1"></i> Filter Laporan</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('adminservis.laporan.index') }}">
                <div class="form-row align-items-end">

                    {{-- Filter Dari Tanggal --}}
                    <div class="col-md-3 mb-3">
                        <label for="tanggal_dari">Dari Tanggal</label>
                        <input type="date" name="tanggal_dari" id="tanggal_dari"
                            value="{{ request('tanggal_dari') }}" class="form-control">
                    </div>

                    {{-- Filter Sampai Tanggal --}}
                    <div class="col-md-3 mb-3">
                        <label for="tanggal_sampai">Sampai Tanggal</label>
                        <input type="date" name="tanggal_sampai" id="tanggal_sampai"
                            value="{{ request('tanggal_sampai') }}" class="form-control">
                    </div>

                    {{-- Filter Status --}}
                    <div class="col-md-3 mb-3">
                        <label for="status">Status Pembayaran</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="belum_bayar" {{ request('status') == 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                            <option value="dp" {{ request('status') == 'dp' ? 'selected' : '' }}>DP / Cicilan</option>
                            <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                        </select>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="col-md-3 mb-3">
                        <button type="submit" class="btn btn-primary mr-2">
                            <i class="fas fa-search fa-sm"></i> Tampilkan Laporan
                        </button>
                        <a href="{{ route('adminservis.laporan.index') }}" class="btn btn-secondary">
                            <i class="fas fa-undo fa-sm"></i> Reset
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- Statistik Cards --}}
    <div class="row mb-4">

        {{-- Card 1: Total Pendapatan --}}
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Pendapatan (Lunas)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-check-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Total Transaksi --}}
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Transaksi
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $totalTransaksi }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-receipt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Total Booking --}}
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Booking
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $totalBooking }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Pesan Periode Laporan --}}
    @if(request()->filled('tanggal_dari') || request()->filled('tanggal_sampai'))
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle mr-1"></i>
        Menampilkan laporan periode:
        <strong>{{ request('tanggal_dari') ?: '-' }}</strong>
        s/d
        <strong>{{ request('tanggal_sampai') ?: '-' }}</strong>
        @if(request()->filled('status'))
            &mdash; Status: <strong>{{ ucwords(str_replace('_', ' ', request('status'))) }}</strong>
        @endif
    </div>
    @endif

    {{-- Data Table Card --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Data Transaksi Servis</h6>

            {{-- Tombol Export (hanya tampil jika ada data) --}}
            @if($laporan->total() > 0)
            <div>
                <a href="{{ route('adminservis.laporan.export.pdf', request()->all()) }}"
                   target="_blank" class="btn btn-sm btn-danger mr-2 shadow-sm">
                    <i class="fas fa-file-pdf fa-sm text-white-50"></i> Export PDF
                </a>
                <a href="{{ route('adminservis.laporan.export.excel', request()->all()) }}"
                   target="_blank" class="btn btn-sm btn-success shadow-sm">
                    <i class="fas fa-file-excel fa-sm text-white-50"></i> Export Excel
                </a>
            </div>
            @endif
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Kode Booking</th>
                            <th>Pelanggan</th>
                            <th>Layanan</th>
                            <th>Teknisi</th>
                            <th>Tipe Kendaraan</th>
                            <th>Merek | No Plat</th>
                            <th>Tgl Booking</th>
                            <th>Tgl Bayar</th>
                            <th class="text-right">Total Biaya</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($laporan as $item)
                            <tr>
                                <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                <td class="align-middle font-weight-bold">
                                    {{ $item->bookingServis->kode_booking ?? '-' }}
                                </td>
                                <td class="align-middle">
                                    {{ $item->bookingServis->pelanggan->name ?? $item->bookingServis->pelanggan->nama_lengkap ?? '-' }}
                                </td>
                                <td class="align-middle">
                                    {{ $item->bookingServis->layananServis->nama_layanan ?? '-' }}
                                </td>
                                <td class="align-middle">
                                    {{ $item->bookingServis->teknisi->name ?? '<span class="text-muted font-italic">Belum Ditugaskan</span>' }}
                                </td>
                                <td class="align-middle">
                                    {{ $item->bookingServis->tipe_kendaraan ?? '-' }}
                                </td>
                                <td class="align-middle">
                                    {{ $item->bookingServis->merek_kendaraan ?? '-' }}
                                    @if($item->bookingServis->nomor_plat)
                                        <br><small class="text-muted">{{ $item->bookingServis->nomor_plat }}</small>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    @if($item->bookingServis->tanggal_booking)
                                        {{ \Carbon\Carbon::parse($item->bookingServis->tanggal_booking)->format('d-m-Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="align-middle">
                                    @if($item->tanggal_bayar)
                                        {{ \Carbon\Carbon::parse($item->tanggal_bayar)->format('d-m-Y H:i') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="align-middle text-right font-weight-bold">
                                    Rp {{ number_format($item->total_biaya, 0, ',', '.') }}
                                </td>
                                <td class="align-middle text-center">
                                    @php $status = $item->status_pembayaran @endphp
                                    @if($status == 'lunas')
                                        <span class="badge badge-success px-2 py-1">Lunas</span>
                                    @elseif($status == 'dp')
                                        <span class="badge badge-warning px-2 py-1">DP / Cicilan</span>
                                    @elseif($status == 'belum_bayar')
                                        <span class="badge badge-danger px-2 py-1">Belum Bayar</span>
                                    @else
                                        <span class="badge badge-secondary px-2 py-1">{{ $status ?? '-' }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-5 text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 mt-2 d-block"></i>
                                    <h5>Data laporan tidak ditemukan</h5>
                                    <p class="mb-0">Silakan ubah filter untuk mencari data transaksi.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($laporan->hasPages())
        <div class="card-footer">
            {{ $laporan->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
