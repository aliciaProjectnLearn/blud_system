@extends('layouts.app')

@section('title', 'Dashboard User')

@push('styles')
<style>
    /* Reverted to Standard SB Admin 2 Palette */
    body {
        background-color: #f8f9fc !important;
    }

    #wrapper #content-wrapper {
        background-color: #f8f9fc !important;
    }

    .bg-gradient-primary {
        background-color: #4e73df !important;
        background-image: linear-gradient(180deg, #4e73df 10%, #224abe 100%) !important;
    }

    .sidebar-dark .sidebar-brand,
    .sidebar-dark .nav-item .nav-link,
    .sidebar-dark .sidebar-heading {
        color: #FFFFFF !important;
    }

    .sidebar-dark .nav-item .nav-link i {
        color: rgba(255, 255, 255, 0.8) !important;
    }

    .sidebar-dark .nav-item.active .nav-link,
    .sidebar-dark .nav-item.active .nav-link i {
        color: #FFFFFF !important;
        background-color: rgba(255, 255, 255, 0.1);
    }

    .navbar {
        background-color: #ffffff !important;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
    }

    .navbar .nav-link, .navbar .navbar-brand, .navbar .fa-bars {
        color: #4e73df !important;
    }

    .card {
        border: none;
        background-color: #FFFFFF !important;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(0, 0, 0, 0.05);
        border-radius: 12px;
    }

    .card-header {
        background-color: #f8f9fc !important;
        color: #4e73df !important;
        border-bottom: 1px solid #e3e6f0 !important;
        border-top-left-radius: 12px !important;
        border-top-right-radius: 12px !important;
        font-weight: bold;
    }

    .welcome-banner {
        background: linear-gradient(90deg, #4e73df 0%, #224abe 100%);
        color: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 25px;
        box-shadow: 0 4px 15px rgba(78, 115, 223, 0.2);
    }

    .quick-access-card {
        text-align: center;
        padding: 20px;
        cursor: pointer;
        transition: transform 0.3s, box-shadow 0.3s;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .quick-access-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .quick-access-icon {
        font-size: 2.5rem;
        margin-bottom: 15px;
        color: #4e73df;
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #f1f1f1; }
    ::-webkit-scrollbar-thumb { background: #4e73df; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #224abe; }

    /* Print styling for detail modal */
    @media print {
        body * {
            visibility: hidden;
        }
        #detailModal, #detailModal * {
            visibility: visible;
        }
        #detailModal {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .modal-footer {
            display: none !important;
        }
        .modal-backdrop {
            display: none !important;
        }
    }

    /* Mobile: tabel history lebih readable */
    @media (max-width: 575.98px) {
        .card-header a {
            font-size: 0.8rem;
            padding: 4px 8px;
        }

        /* Sembunyikan kolom kurang penting di mobile */
        .table thead th:nth-child(2),
        .table tbody td:nth-child(2) {
            display: none;
        }
        
        .table thead th:nth-child(3),
        .table tbody td:nth-child(3) {
            display: none;
        }

        /* Font lebih kecil di tabel */
        .table th, .table td {
            font-size: 0.8rem;
            padding: 8px 6px;
            vertical-align: middle;
        }
    }
</style>
@endpush

@section('content')
<!-- Greeting Section -->
<div class="welcome-banner animate__animated animate__fadeIn">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h3 class="font-weight-bold">Selamat Datang, {{ $user->name }}!</h3>
            <p class="lead mb-0" style="font-size: 16px;">Kelola booking futsal, sewa ruko, dan servis AC Anda dalam satu tempat yang nyaman.</p>
        </div>
        <div class="col-md-4 text-right d-none d-md-block">
            <i class="fas fa-user-circle fa-5x opacity-50"></i>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <!-- Card 1: Futsal -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary h-100 py-2 shadow-sm">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Booking Futsal Aktif</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            @if($futsalAktif)
                                #{{ $futsalAktif->booking->id }}
                                <span class="badge badge-warning text-xs font-weight-normal">{{ $futsalAktif->booking->status }}</span>
                            @else
                                <span class="text-muted">Tidak ada booking</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-futbol fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Ruko -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success h-100 py-2 shadow-sm">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Status Sewa Kantin/Ruko</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            @if($sewaRuko)
                                Masa Aktif s.d
                            @else
                                <span class="text-muted">Belum ada sewa</span>
                            @endif
                        </div>
                        @if($sewaRuko)
                        <div class="mt-1 text-sm font-weight-bold">
                            {{ \Carbon\Carbon::parse($sewaRuko->tgl_selesai)->format('d M Y') }}
                        </div>
                        @endif
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-store fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: AC -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info h-100 py-2 shadow-sm">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Jadwal Servis AC</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            @if($acMendatang)
                                {{ \Carbon\Carbon::parse($acMendatang->tgl_kunjungan)->format('d M Y') }}
                            @else
                                <span class="text-muted">Tidak ada jadwal</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tools fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Total -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning h-100 py-2 shadow-sm">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Riwayat Transaksi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTransaksi }} Transaksi</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Access Buttons -->
<div class="row mb-5">
    <div class="col-12 mb-3">
        <h2 class="font-weight-bold text-primary text-center">Layanan Cepat</h2>
    </div>
    <div class="col-md-4 mb-3">
        <a href="{{ route('user.futsal.index') }}" class="text-decoration-none">
            <div class="card quick-access-card shadow-sm border-0">
                <i class="fas fa-futbol quick-access-icon"></i>
                <h6 class="font-weight-bold text-primary mb-1">Booking Futsal</h6>
                <small class="text-muted">Cek jadwal & booking futsal</small>
            </div>
        </a>
    </div>
    <div class="col-md-4 mb-3">
        <a href="{{ route('user.kantin.katalog') }}" class="text-decoration-none">
            <div class="card quick-access-card shadow-sm border-0">
                <i class="fas fa-store quick-access-icon"></i>
                <h6 class="font-weight-bold text-primary mb-1">Daftar Sewa Ruko</h6>
                <small class="text-muted">Pantau & lunasi tagihan sewa</small>
            </div>
        </a>
    </div>
    <div class="col-md-4 mb-3">
        <a href="{{ route('user.ac.layanan') }}" class="text-decoration-none">
            <div class="card quick-access-card shadow-sm border-0">
                <i class="fas fa-tools quick-access-icon"></i>
                <h6 class="font-weight-bold text-primary mb-1">Order Servis AC</h6>
                <small class="text-muted">Maintenance AC kantor/rumah</small>
            </div>
        </a>
    </div>
</div>

<!-- Recent Activities & Tracker -->
<div class="row">
    <!-- Recent Activities Table -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Histori Aktivitas Terakhir</h6>
                <a href="#" class="text-primary small">Lihat Semua</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" width="100%" cellspacing="0">
                        <thead class="bg-light">
                            <tr>
                                <th>Layanan</th>
                                <th>Tanggal</th>
                                <th>Nominal</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentActivities as $act)
                            <tr>
                                <td class="font-weight-bold">{{ $act['layanan'] }}</td>
                                <td>{{ \Carbon\Carbon::parse($act['tanggal'])->format('d/m/Y') }}</td>
                                <td>Rp {{ number_format($act['nominal'], 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge badge-{{ $act['badge'] }}">
                                        {{ $act['status'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-info" onclick='showDetail("{{ $act["layanan"] }}", @json($act["raw"]))'>
                                        <i class="fas fa-eye"></i> Detail
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">Belum ada transaksi</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Status Tracker -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Progress Layanan Saat Ini</h6>
            </div>
            <div class="card-body">
                @if($futsalAktif)
                <h4 class="small font-weight-bold">Futsal: #{{ $futsalAktif->booking->id }} <span class="float-right">{{ ucwords($futsalAktif->booking->status) }}</span></h4>
                <div class="progress mb-4">
                    @php
                        $perc = 0;
                        if($futsalAktif->booking->status == 'menunggu') $perc = 50;
                        elseif($futsalAktif->booking->status == 'dikonfirmasi') $perc = 75;
                        elseif($futsalAktif->booking->status == 'selesai') $perc = 100;
                    @endphp
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $perc }}%" aria-valuenow="{{ $perc }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                @endif

                @if($acMendatang)
                <h4 class="small font-weight-bold">Servis AC: {{ \Carbon\Carbon::parse($acMendatang->tgl_kunjungan)->format('d M') }} <span class="float-right">{{ ucwords($acMendatang->status) }}</span></h4>
                <div class="progress mb-4">
                    @php
                        $percAc = 0;
                        if($acMendatang->status == 'pending') $percAc = 25;
                        elseif($acMendatang->status == 'proses') $percAc = 75;
                        elseif($acMendatang->status == 'selesai') $percAc = 100;
                    @endphp
                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ $percAc }}%" aria-valuenow="{{ $percAc }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                @endif

                @if($sewaRuko && count($sewaRuko->pembayaran) > 0)
                @php $pay = $sewaRuko->pembayaran->first(); @endphp
                <h4 class="small font-weight-bold">Sewa Unit: {{ $sewaRuko->ruko->no_unit ?? 'Ruko' }} <span class="float-right text-danger">Jatuh Tempo</span></h4>
                <div class="progress">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <small class="text-muted mt-2 d-block">Batas bayar: {{ \Carbon\Carbon::parse($pay->tgl_jatuh_tempo)->format('d/m/Y') }}</small>
                @endif

                @if(!$futsalAktif && !$acMendatang && (!$sewaRuko || count($sewaRuko->pembayaran) == 0))
                <div class="text-center py-4">
                    <i class="fas fa-tasks fa-3x text-gray-200 mb-3"></i>
                    <p class="text-muted small">Semua layanan telah selesai</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="detailModalLabel">Detail Transaksi</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalContent">
                <!-- Content will be injected via JS -->
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary border-0" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary border-0" onclick="window.print()">Cetak Bukti</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showDetail(layanan, data) {
        let content = '';
        const modalTitle = document.getElementById('detailModalLabel');
        const modalBody = document.getElementById('modalContent');

        modalTitle.innerText = `Detail ${layanan}`;

        if (layanan === 'Booking Futsal') {
            content = `
                <div class="row">
                    <div class="col-md-6 border-right">
                        <h6><b>Informasi Booking</b></h6>
                        <table class="table table-sm table-borderless">
                            <tr><td>ID Booking</td><td>: <b>#${data.booking.id}</b></td></tr>
                            <tr><td>Waktu Mulai</td><td>: ${data.booking.booking_futsal?.start_datetime ?? ''}</td></tr>
                            <tr><td>Status</td><td>: <span class="badge badge-info">${data.booking.status}</span></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6><b>Informasi Pembayaran</b></h6>
                        <table class="table table-sm table-borderless">
                            <tr><td>Kode Bayar</td><td>: ${data.kode_pembayaran}</td></tr>
                            <tr><td>Nominal</td><td>: Rp ${new Intl.NumberFormat('id-ID').format(data.jumlah_bayar)}</td></tr>
                            <tr><td>Status</td><td>: <span class="badge badge-success">${data.status}</span></td></tr>
                        </table>
                    </div>
                </div>
            `;
        } else if (layanan === 'Sewa Ruko/Kantin') {
            content = `
                <div class="row">
                    <div class="col-md-6 border-right">
                        <h6><b>Informasi Sewa</b></h6>
                        <table class="table table-sm table-borderless">
                            <tr><td>Booking ID</td><td>: <b>${data.booking.booking_id}</b></td></tr>
                            <tr><td>Unit</td><td>: ${data.booking.ruko_id}</td></tr>
                            <tr><td>Periode</td><td>: ${data.booking.tgl_mulai} s.d ${data.booking.tgl_selesai}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6><b>Informasi Tagihan</b></h6>
                        <table class="table table-sm table-borderless">
                            <tr><td>Termin</td><td>: ${data.termin}</td></tr>
                            <tr><td>Total Tagihan</td><td>: Rp ${new Intl.NumberFormat('id-ID').format(data.jumlah_tagihan)}</td></tr>
                            <tr><td>Status</td><td>: <span class="badge badge-success">${data.status}</span></td></tr>
                        </table>
                    </div>
                </div>
            `;
        } else if (layanan === 'Servis AC') {
            content = `
                <div class="row">
                    <div class="col-md-6 border-right">
                        <h6><b>Informasi Servis</b></h6>
                        <table class="table table-sm table-borderless">
                            <tr><td>Tanggal Kunjungan</td><td>: ${data.booking.tgl_kunjungan}</td></tr>
                            <tr><td>Merek AC</td><td>: ${data.booking.merek_ac}</td></tr>
                            <tr><td>Keluhan</td><td>: ${data.booking.detail_keluhan}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6><b>Informasi Invoice</b></h6>
                        <table class="table table-sm table-borderless">
                            <tr><td>Invoice No</td><td>: <b>${data.invoice_no}</b></td></tr>
                            <tr><td>Total Harga</td><td>: Rp ${new Intl.NumberFormat('id-ID').format(data.total_harga)}</td></tr>
                            <tr><td>Status</td><td>: <span class="badge badge-success">${data.status}</span></td></tr>
                        </table>
                    </div>
                </div>
            `;
        }

        modalBody.innerHTML = content;
        $('#detailModal').modal('show');
    }
</script>
@endpush
