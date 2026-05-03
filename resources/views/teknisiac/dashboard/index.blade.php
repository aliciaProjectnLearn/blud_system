@extends('layouts.app')

@section('title', 'Dashboard Teknisi AC')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard Teknisi AC</h1>
</div>



<div class="row">
    <!-- Jumlah Pekerjaan Selesai Bulan Ini -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Pekerjaan Selesai (Bulan Ini)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSelesaiBulanIni ?? 0 }} Tugas</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Tugas Saat Ini -->
    <div class="col-lg-12 mb-4">
        <div class="card shadow mb-4 border-left-primary">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Tugas Saat Ini (Belum Selesai)</h6>
            </div>
            <div class="card-body p-3 p-md-4">
                <!-- Desktop Table View -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Pelanggan</th>
                                <th>Layanan</th>
                                <th>Tanggal Kunjungan</th>
                                <th>Alamat</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pekerjaanAktif as $pekerjaan)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $pekerjaan->nama_pelanggan ?? ($pekerjaan->user->nama_lengkap ?? ($pekerjaan->user->name ?? '-')) }}</td>
                                <td>{{ $pekerjaan->layanan->nama ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($pekerjaan->tgl_kunjungan)->format('d M Y H:i') }}</td>
                                <td>{{ $pekerjaan->alamat }}</td>
                                <td>
                                    @if($pekerjaan->status == 'menunggu')
                                        <span class="badge badge-info">Menunggu</span>
                                    @elseif($pekerjaan->status == 'proses')
                                        <span class="badge badge-warning">Dalam Proses</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst($pekerjaan->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('teknisi.pekerjaan.show', $pekerjaan->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> Detail & Kerjakan
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Belum ada tugas baru untuk Anda saat ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="d-block d-md-none">
                    @forelse($pekerjaanAktif as $pekerjaan)
                    <div class="card shadow-sm mb-3 border-left-primary">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="font-weight-bold text-dark">{{ $pekerjaan->nama_pelanggan ?? ($pekerjaan->user->nama_lengkap ?? ($pekerjaan->user->name ?? '-')) }}</span>
                                @if($pekerjaan->status == 'menunggu')
                                    <span class="badge badge-info">Menunggu</span>
                                @elseif($pekerjaan->status == 'proses')
                                    <span class="badge badge-warning">Dalam Proses</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($pekerjaan->status) }}</span>
                                @endif
                            </div>
                            <div class="mb-3">
                                <p class="small text-muted mb-1"><i class="fas fa-tools fa-fw mr-1"></i> {{ $pekerjaan->layanan->nama ?? '-' }}</p>
                                <p class="small text-muted mb-1"><i class="far fa-calendar-alt fa-fw mr-1"></i> {{ \Carbon\Carbon::parse($pekerjaan->tgl_kunjungan)->format('d M Y H:i') }}</p>
                                <p class="small text-muted mb-0"><i class="fas fa-map-marker-alt fa-fw mr-1"></i> {{ $pekerjaan->alamat }}</p>
                            </div>
                            <a href="{{ route('teknisi.pekerjaan.show', $pekerjaan->id) }}" class="btn btn-primary btn-sm btn-block">
                                <i class="fas fa-eye"></i> Detail & Kerjakan
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted font-italic py-3 border rounded bg-light">Belum ada tugas baru untuk Anda saat ini.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Histori Pekerjaan -->
    <div class="col-lg-12">
        <div class="card shadow mb-4 border-left-success">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">Histori Pekerjaan Selesai</h6>
            </div>
            <div class="card-body p-3 p-md-4">
                <!-- Desktop Table View -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Pelanggan</th>
                                <th>Layanan</th>
                                <th>Tanggal Selesai</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($historiPekerjaan as $histori)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $histori->nama_pelanggan ?? ($histori->user->nama_lengkap ?? ($histori->user->name ?? '-')) }}</td>
                                <td>{{ $histori->layanan->nama ?? '-' }}</td>
                                <td>{{ $histori->updated_at->format('d M Y H:i') }}</td>
                                <td>
                                    <span class="badge badge-success">Selesai</span>
                                </td>
                                <td>
                                    <a href="{{ route('teknisi.pekerjaan.show', $histori->id) }}" class="btn btn-info btn-sm mb-1">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                    @if(empty($histori->pembayaran) || $histori->pembayaran->status !== 'dibayar')
                                        <a href="{{ route('teknisi.pembayaran.form', $histori->id) }}" class="btn btn-warning btn-sm mb-1">
                                            <i class="fas fa-money-bill-wave"></i> Tagih Pembayaran
                                        </a>
                                    @else
                                        <span class="badge badge-primary"><i class="fas fa-check"></i> Sudah Dibayar</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada histori pekerjaan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="d-block d-md-none">
                    @forelse($historiPekerjaan as $histori)
                    <div class="card shadow-sm mb-3 border-left-success">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="font-weight-bold text-dark">{{ $histori->nama_pelanggan ?? ($histori->user->nama_lengkap ?? ($histori->user->name ?? '-')) }}</span>
                                <span class="badge badge-success">Selesai</span>
                            </div>
                            <div class="mb-3">
                                <p class="small text-muted mb-1"><i class="fas fa-tools fa-fw mr-1"></i> {{ $histori->layanan->nama ?? '-' }}</p>
                                <p class="small text-muted mb-0"><i class="far fa-calendar-check fa-fw mr-1"></i> {{ $histori->updated_at->format('d M Y H:i') }}</p>
                            </div>
                            <div class="d-flex flex-column">
                                <a href="{{ route('teknisi.pekerjaan.show', $histori->id) }}" class="btn btn-info btn-sm btn-block mb-2">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                                @if(empty($histori->pembayaran) || $histori->pembayaran->status !== 'dibayar')
                                    <a href="{{ route('teknisi.pembayaran.form', $histori->id) }}" class="btn btn-warning btn-sm btn-block">
                                        <i class="fas fa-money-bill-wave"></i> Tagih Pembayaran
                                    </a>
                                @else
                                    <div class="bg-primary text-white text-center rounded py-2 small font-weight-bold">
                                        <i class="fas fa-check"></i> Sudah Dibayar
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted font-italic py-3 border rounded bg-light">Belum ada histori pekerjaan.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
