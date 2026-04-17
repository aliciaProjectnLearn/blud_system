@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Admin Kantin</h1>
    </div>

    {{-- Card Ringkasan --}}
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Pendapatan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto"><i class="fas fa-money-bill-wave fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Penyewa Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPenyewa }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Unit Terisi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $totalUnitTerisi }} / {{ $totalUnit }}
                            </div>
                        </div>
                        <div class="col-auto"><i class="fas fa-store fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Status Pembayaran Per Termin --}}
    <div class="row mb-4">
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Termin 1</h6>
                </div>
                <div class="card-body">
                    @php $totalTermin1 = $statusTermin['termin1_menunggu'] + $statusTermin['termin1_verifikasi']; @endphp
                    <h4 class="small font-weight-bold">
                        Terverifikasi <span class="float-right">{{ $statusTermin['termin1_verifikasi'] }}</span>
                    </h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-success" role="progressbar"
                            style="width: {{ $totalTermin1 > 0 ? ($statusTermin['termin1_verifikasi']/$totalTermin1)*100 : 0 }}%"></div>
                    </div>
                    <h4 class="small font-weight-bold">
                        Menunggu <span class="float-right">{{ $statusTermin['termin1_menunggu'] }}</span>
                    </h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-warning" role="progressbar"
                            style="width: {{ $totalTermin1 > 0 ? ($statusTermin['termin1_menunggu']/$totalTermin1)*100 : 0 }}%"></div>
                    </div>
                    <h4 class="small font-weight-bold">
                        Dibatalkan <span class="float-right">{{ $statusTermin['termin1_dibatalkan'] }}</span>
                    </h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-danger" role="progressbar"
                            style="width: {{ $totalTermin1 > 0 ? ($statusTermin['termin1_dibatalkan']/$totalTermin1)*100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Termin 2</h6>
                </div>
                <div class="card-body">
                    @php $totalTermin2 = $statusTermin['termin2_menunggu'] + $statusTermin['termin2_verifikasi']; @endphp
                    <h4 class="small font-weight-bold">
                        Terverifikasi <span class="float-right">{{ $statusTermin['termin2_verifikasi'] }}</span>
                    </h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-success" role="progressbar"
                            style="width: {{ $totalTermin2 > 0 ? ($statusTermin['termin2_verifikasi']/$totalTermin2)*100 : 0 }}%"></div>
                    </div>
                    <h4 class="small font-weight-bold">
                        Menunggu <span class="float-right">{{ $statusTermin['termin2_menunggu'] }}</span>
                    </h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-warning" role="progressbar"
                            style="width: {{ $totalTermin2 > 0 ? ($statusTermin['termin2_menunggu']/$totalTermin2)*100 : 0 }}%"></div>
                    </div>
                    <h4 class="small font-weight-bold">
                        Dibatalkan <span class="float-right">{{ $statusTermin['termin2_dibatalkan'] }}</span>
                    </h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-danger" role="progressbar"
                            style="width: {{ $totalTermin1 > 0 ? ($statusTermin['termin2_dibatalkan']/$totalTermin1)*100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pengingat Pembayaran Termin 2 --}}
    <div class="card shadow mb-4 border-left-warning">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-warning">
                <i class="fas fa-exclamation-triangle mr-1"></i> Pengingat Pembayaran Termin 2 (H-30)
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>Unit</th>
                            <th>Penyewa</th>
                            <th>Jatuh Tempo</th>
                            <th class="text-center">Sisa Hari</th>
                            <th>Tagihan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengingatTermin2 as $p)
                            <tr>
                                <td class="font-weight-bold">{{ $p->sewaRuko->ruko->kode_unit ?? '-' }}</td>
                                <td>{{ $p->sewaRuko->penyewa->user->nama_lengkap ?? $p->sewaRuko->penyewa->user->name ?? '-' }}</td>
                                <td>
                                    <span class="text-danger font-weight-bold">
                                        {{ \Carbon\Carbon::parse($p->tgl_jatuh_tempo)->format('d M Y') }}
                                    </span>
                                    <br>
                                <td class="font-weight-bold text-center">
                                    @php
                                        $sisaHari = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($p->tgl_jatuh_tempo), false);
                                    @endphp
                                    @if($sisaHari <= 0)
                                        <span class="badge badge-danger">Jatuh Tempo</span>
                                    @elseif($sisaHari <= 7)
                                        <span class="badge badge-warning">{{ $sisaHari }} Hari lagi</span>
                                    @else
                                        <span class="badge badge-info">{{ $sisaHari }} Hari</span>
                                    @endif
                                </td>
                                <td class="text-primary font-weight-bold">Rp {{ number_format($p->jumlah_tagihan, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <form action="{{ route('adminkantin.kirim-wa', $p->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success shadow-sm" onclick="return confirm('Kirim pengingat WhatsApp ke penyewa ini?')">
                                            <i class="fab fa-whatsapp mr-1"></i> Kirim Pengingat
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada tagihan termin 2 yang mendekati jatuh tempo.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Transaksi Terbaru --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Transaksi Terbaru</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Penyewa</th>
                            <th>Unit / Kategori</th>
                            <th>Termin</th>
                            <th>Jumlah Tagihan</th>
                            <th class="d-none d-md-table-cell">Tgl Bayar</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksiTerbaru as $i => $t)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                {{ $t->sewaRuko->penyewa->user->nama_lengkap ?? $t->sewaRuko->penyewa->user->name ?? '-' }}
                            </td>
                            <td>
                                <div class="font-weight-bold text-primary">{{ $t->sewaRuko->ruko->kode_unit ?? '-' }}</div>
                                <small class="text-muted d-none d-md-block">{{ $t->sewaRuko->ruko->kategori->nama ?? '-' }}</small>
                            </td>
                            <td>Termin {{ $t->termin }}</td>
                            <td>Rp {{ number_format($t->jumlah_tagihan, 0, ',', '.') }}</td>
                            <td class="d-none d-md-table-cell">{{ $t->tgl_bayar ? \Carbon\Carbon::parse($t->tgl_bayar)->format('d M Y') : '-' }}</td>
                            <td>
                                @if($t->status === 'lunas')
                                    <span class="badge badge-success">Lunas</span>
                                @elseif($t->status === 'verifikasi')
                                    <span class="badge badge-info">Verifikasi</span>
                                @elseif($t->status === 'menunggu')
                                    <span class="badge badge-warning">Menunggu</span>
                                @else
                                    <span class="badge badge-danger">Dibatalkan</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada transaksi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
