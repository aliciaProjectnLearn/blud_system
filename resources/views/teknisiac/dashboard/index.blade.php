@extends('layouts.app')

@section('title', 'Dashboard Teknisi AC')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard Teknisi AC</h1>
</div>

<div class="row">
    <!-- Tugas Saat Ini -->
    <div class="col-lg-12 mb-4">
        <div class="card shadow mb-4 border-left-primary">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Tugas Saat Ini (Belum Selesai)</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
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
                                <td>{{ $pekerjaan->user->nama_lengkap ?? $pekerjaan->user->name }}</td>
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
            </div>
        </div>
    </div>

    <!-- Histori Pekerjaan -->
    <div class="col-lg-12">
        <div class="card shadow mb-4 border-left-success">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">Histori Pekerjaan Selesai</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
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
                                <td>{{ $histori->user->nama_lengkap ?? $histori->user->name }}</td>
                                <td>{{ $histori->layanan->nama ?? '-' }}</td>
                                <td>{{ $histori->updated_at->format('d M Y H:i') }}</td>
                                <td>
                                    <span class="badge badge-success">Selesai</span>
                                </td>
                                <td>
                                    <a href="{{ route('teknisi.pekerjaan.show', $histori->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> Lihat Detail
                                    </a>
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
            </div>
        </div>
    </div>
</div>
@endsection
