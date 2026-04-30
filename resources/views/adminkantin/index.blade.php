@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Admin Kantin</h1>
    </div>

    {{-- Card Ringkasan --}}
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Unit Tersedia</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTersedia }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Unit Disewa</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalDisewa }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-store fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Penyewaan Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAktif }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-user-check fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Menunggu Verifikasi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $menungguVerifikasi }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-clock fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Pendapatan Bulan Ini ({{ now()->translatedFormat('F Y') }})</div>
                            <div class="h2 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-money-bill-wave fa-3x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Jatuh Tempo Termin Bulan Ini --}}
    <div class="card shadow mb-4 border-left-danger">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-danger"><i class="fas fa-calendar-times mr-1"></i> Jatuh Tempo Termin Bulan Ini</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" width="100%">
                    <thead class="bg-light">
                        <tr>
                            <th>Unit</th>
                            <th>Penyewa</th>
                            <th>Termin</th>
                            <th>Jatuh Tempo</th>
                            <th>Tagihan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jatuhTempoBulanIni as $j)
                        <tr>
                            <td class="font-weight-bold">{{ $j->sewaRuko->ruko->kode_unit ?? '-' }}</td>
                            <td>{{ $j->sewaRuko->nama_penyewa ?? '-' }}</td>
                            <td>Termin {{ $j->termin_ke }}</td>
                            <td class="text-danger font-weight-bold">{{ \Carbon\Carbon::parse($j->tgl_jatuh_tempo)->format('d M Y') }}</td>
                            <td class="font-weight-bold text-primary">Rp {{ number_format($j->jumlah_tagihan, 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('admin.kantin.pembayaran.show', $j->id) }}" class="btn btn-sm btn-primary shadow-sm">Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada tagihan jatuh tempo bulan ini.</td>
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
            <h6 class="m-0 font-weight-bold text-primary">Penyewaan Terbaru</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Penyewa</th>
                            <th>Unit</th>
                            <th>Tgl Mulai</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksiTerbaru as $i => $t)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td class="font-weight-bold">{{ $t->nama_penyewa }}</td>
                            <td>{{ $t->ruko->kode_unit ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($t->tanggal_mulai_sewa)->format('d M Y') }}</td>
                            <td>
                                @if($t->status_sewa === 'aktif')
                                    <span class="badge badge-success px-3">Aktif</span>
                                @elseif($t->status_sewa === 'pending')
                                    <span class="badge badge-warning px-3">Pending</span>
                                @elseif($t->status_sewa === 'selesai')
                                    <span class="badge badge-secondary px-3">Selesai</span>
                                @else
                                    <span class="badge badge-danger px-3">Dibatalkan</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada data penyewaan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pengingat Pembayaran Termin 2 (H-30) --}}
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
                                <td>{{ $p->sewaRuko->nama_penyewa ?? '-' }}</td>
                                <td>
                                    <span class="text-danger font-weight-bold">
                                        {{ \Carbon\Carbon::parse($p->tgl_jatuh_tempo)->format('d M Y') }}
                                    </span>
                                </td>
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
                                    <form action="{{ route('admin.kantin.kirim-wa', $p->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success shadow-sm" onclick="return confirm('Kirim pengingat WhatsApp ke penyewa ini?')">
                                            <i class="fab fa-whatsapp mr-1"></i> Kirim Pengingat
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
