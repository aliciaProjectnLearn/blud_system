@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    {{-- Page Heading --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
        </a>
    </div>

    {{-- Content Row - Stats Cards --}}
    <div class="row">

        {{-- Card Earnings (Monthly) --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Pelanggan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPelanggan }}</div>
                        </div>
                         <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Earnings (Annual) --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Transaksi
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTransaksi }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Tasks --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Pendapatan
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">Rp {{ number_format($totalPendapatan,0,',','.') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Pending Requests --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Booking Menunggu
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBookingPending }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    {{-- End Stats Cards Row --}}

    {{-- Content Row - Table & Tasks --}}
    <div class="row">

        {{-- DataTables --}}
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Progres Pendapatan</h6>
                </div>
                <div class="card-body">
                    <h4 class="small font-weight-bold">
                        Sistem Cuci AC
                        <span class="float-right">
                            Rp {{ number_format($pendapatanAC,0,',','.') }}
                        </span>
                    </h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-primary"
                            role="progressbar"
                            style="width: {{ $persenAC }}%"
                            aria-valuenow="{{ $persenAC }}"
                            aria-valuemin="0"
                            aria-valuemax="100">
                        </div>
                    </div>

                    <h4 class="small font-weight-bold">
                        Booking Lapangan Futsal
                        <span class="float-right">
                            Rp {{ number_format($pendapatanFutsal,0,',','.') }}
                        </span>
                    </h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-success"
                            role="progressbar"
                            style="width: {{ $persenFutsal }}%"
                            aria-valuenow="{{ $persenFutsal }}"
                            aria-valuemin="0"
                            aria-valuemax="100">
                        </div>
                    </div>

                    <h4 class="small font-weight-bold">
                        Sewa Ruko Kantin
                        <span class="float-right">
                            Rp {{ number_format($pendapatanRuko,0,',','.') }}
                        </span>
                    </h4>
                    <div class="progress">
                        <div class="progress-bar bg-warning"
                            role="progressbar"
                            style="width: {{ $persenRuko }}%"
                            aria-valuenow="{{ $persenRuko }}"
                            aria-valuemin="0"
                            aria-valuemax="100">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Illustration --}}
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Notifikasi Terbaru</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($aktivitas as $a)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-bell text-primary mr-2"></i>
                                    <strong>{{ $a->nama }}</strong>
                                    {{ $a->aktivitas }}
                                </div>
                                <span class="small text-gray-500">
                                {{ \Carbon\Carbon::parse($a->created_at)->diffForHumans() }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

        <div class="row">
            <div class="col-lg-12 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Transaksi Terbaru
                        </h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Layanan</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaksiTerbaru as $t)
                                    <tr>
                                        <td>{{ $t->layanan }}</td>
                                        <td>{{ $t->nama_user }}</td>
                                        <td>Rp {{ number_format($t->jumlah_bayar,0,',','.') }}</td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ $t->status }}
                                            </span>
                                        </td>
                                        <td>{{ $t->tgl_bayar }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
@endsection

@push('scripts')
    {{-- Chart.js --}}
    <script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
    {{-- Demo charts (opsional, bisa diganti dengan data real) --}}
    <script src="{{ asset('js/demo/chart-area-demo.js') }}"></script>
    <script src="{{ asset('js/demo/chart-pie-demo.js') }}"></script>
@endpush
