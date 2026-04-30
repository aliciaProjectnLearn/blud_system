@extends('layouts.app')

@section('title', 'Dashboard Teknisi ' . ucfirst($tipeKendaraan ?? 'Servis'))

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        Dashboard Teknisi {{ ucfirst($tipeKendaraan ?? 'Servis') }}
    </h1>
    <span class="badge badge-pill badge-{{ $tipeKendaraan === 'motor' ? 'warning' : 'primary' }} px-3 py-2" style="font-size:.85rem;">
        <i class="fas fa-{{ $tipeKendaraan === 'motor' ? 'motorcycle' : 'car' }} mr-1"></i>
        {{ $roleTeknisi ?? 'Teknisi Servis' }}
    </span>
</div>

{{-- Alert Sukses / Error --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif

{{-- Kartu Statistik --}}
<div class="row">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Pekerjaan Selesai (Bulan Ini)
                        </div>
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

{{-- ── Pekerjaan Aktif ─────────────────────────────────────────────── --}}
<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card shadow mb-4 border-left-primary">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-tools mr-1"></i> Pekerjaan Aktif
                </h6>
            </div>
            <div class="card-body p-3 p-md-4">

                {{-- Form Filter --}}
                <form method="GET" action="{{ route('teknisiservis.dashboard') }}" class="form-inline mb-3">
                    <select name="status" class="form-control form-control-sm mr-2 mb-2">
                        <option value="">Semua Status</option>
                        <option value="menunggu"     {{ request('status') == 'menunggu'     ? 'selected' : '' }}>Menunggu</option>
                        <option value="dikonfirmasi" {{ request('status') == 'dikonfirmasi' ? 'selected' : '' }}>Dikonfirmasi</option>
                        <option value="proses"       {{ request('status') == 'proses'       ? 'selected' : '' }}>Dalam Proses</option>
                    </select>
                    <input type="date" name="tanggal" class="form-control form-control-sm mr-2 mb-2"
                           value="{{ request('tanggal') }}">
                    <button type="submit" class="btn btn-primary btn-sm mr-1 mb-2">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    <a href="{{ route('teknisiservis.dashboard') }}" class="btn btn-secondary btn-sm mb-2">
                        <i class="fas fa-undo mr-1"></i> Reset
                    </a>
                </form>

                {{-- Desktop Table --}}
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Kode Booking</th>
                                <th>Pelanggan</th>
                                <th>Layanan</th>
                                <th>Kendaraan</th>
                                <th>Keluhan</th>
                                <th>Tanggal Booking</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pekerjaanAktif as $pekerjaan)
                            <tr>
                                <td>{{ $pekerjaanAktif->firstItem() + $loop->index }}</td>
                                <td><code>{{ $pekerjaan->kode_booking }}</code></td>
                                <td>{{ $pekerjaan->user->nama_lengkap ?? $pekerjaan->user->name ?? '-' }}</td>
                                <td>{{ $pekerjaan->layananServis->nama_layanan ?? '-' }}</td>
                                <td>
                                    {{ $pekerjaan->merek_kendaraan }}
                                    <small class="text-muted d-block">({{ $pekerjaan->nomor_plat }})</small>
                                </td>
                                <td>
                                    <span title="{{ $pekerjaan->keluhan }}">
                                        {{ \Illuminate\Support\Str::limit($pekerjaan->keluhan, 40) }}
                                    </span>
                                </td>
                                <td>
                                    {{ $pekerjaan->tanggal_booking->format('d M Y') }}
                                    <small class="text-muted d-block">{{ $pekerjaan->jam_booking }}</small>
                                </td>
                                <td>
                                    @php
                                        $badgeMap = [
                                            'menunggu'     => 'info',
                                            'dikonfirmasi' => 'primary',
                                            'proses'       => 'warning',
                                            'selesai'      => 'success',
                                            'batal'        => 'danger',
                                        ];
                                        $badgeColor = $badgeMap[$pekerjaan->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge badge-{{ $badgeColor }}">
                                        {{ ucfirst($pekerjaan->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('teknisiservis.dashboard.show', $pekerjaan->id) }}"
                                       class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    Belum ada pekerjaan aktif untuk Anda saat ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Card --}}
                <div class="d-block d-md-none">
                    @forelse($pekerjaanAktif as $pekerjaan)
                    <div class="card shadow-sm mb-3 border-left-primary">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="font-weight-bold text-dark">
                                    {{ $pekerjaan->user->nama_lengkap ?? $pekerjaan->user->name ?? '-' }}
                                </span>
                                @php
                                    $badgeMap = [
                                        'menunggu'     => 'info',
                                        'dikonfirmasi' => 'primary',
                                        'proses'       => 'warning',
                                        'selesai'      => 'success',
                                        'batal'        => 'danger',
                                    ];
                                    $badgeColor = $badgeMap[$pekerjaan->status] ?? 'secondary';
                                @endphp
                                <span class="badge badge-{{ $badgeColor }}">{{ ucfirst($pekerjaan->status) }}</span>
                            </div>
                            <div class="mb-3">
                                <p class="small text-muted mb-1">
                                    <i class="fas fa-hashtag fa-fw mr-1"></i>
                                    <code>{{ $pekerjaan->kode_booking }}</code>
                                </p>
                                <p class="small text-muted mb-1">
                                    <i class="fas fa-tools fa-fw mr-1"></i>
                                    {{ $pekerjaan->layananServis->nama_layanan ?? '-' }}
                                </p>
                                <p class="small text-muted mb-1">
                                    <i class="fas fa-car fa-fw mr-1"></i>
                                    {{ $pekerjaan->merek_kendaraan }} ({{ $pekerjaan->nomor_plat }})
                                </p>
                                <p class="small text-muted mb-1">
                                    <i class="far fa-calendar-alt fa-fw mr-1"></i>
                                    {{ $pekerjaan->tanggal_booking->format('d M Y') }} {{ $pekerjaan->jam_booking }}
                                </p>
                                <p class="small text-muted mb-0">
                                    <i class="fas fa-comment-alt fa-fw mr-1"></i>
                                    {{ \Illuminate\Support\Str::limit($pekerjaan->keluhan, 60) }}
                                </p>
                            </div>
                            <a href="{{ route('teknisiservis.dashboard.show', $pekerjaan->id) }}"
                               class="btn btn-info btn-sm btn-block">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted font-italic py-3 border rounded bg-light">
                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                        Belum ada pekerjaan aktif untuk Anda saat ini.
                    </div>
                    @endforelse
                </div>

                {{-- Pagination Aktif --}}
                <div class="mt-3">
                    {{ $pekerjaanAktif->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- ── Histori Pekerjaan ──────────────────────────────────────────── --}}
    <div class="col-lg-12">
        <div class="card shadow mb-4 border-left-success">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-history mr-1"></i> Histori Pekerjaan Selesai
                </h6>
            </div>
            <div class="card-body p-3 p-md-4">

                {{-- Form Filter Histori (tanggal saja) --}}
                <form method="GET" action="{{ route('teknisiservis.dashboard') }}" class="form-inline mb-3">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <input type="date" name="tanggal" class="form-control form-control-sm mr-2 mb-2"
                           value="{{ request('tanggal') }}"
                           placeholder="Filter tanggal booking">
                    <button type="submit" class="btn btn-success btn-sm mr-1 mb-2">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    <a href="{{ route('teknisiservis.dashboard') }}" class="btn btn-secondary btn-sm mb-2">
                        <i class="fas fa-undo mr-1"></i> Reset
                    </a>
                </form>

                {{-- Desktop Table --}}
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Kode Booking</th>
                                <th>Pelanggan</th>
                                <th>Layanan</th>
                                <th>Kendaraan</th>
                                <th>Tanggal Selesai</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($historiPekerjaan as $histori)
                            <tr>
                                <td>{{ $historiPekerjaan->firstItem() + $loop->index }}</td>
                                <td><code>{{ $histori->kode_booking }}</code></td>
                                <td>{{ $histori->user->nama_lengkap ?? $histori->user->name ?? '-' }}</td>
                                <td>{{ $histori->layananServis->nama_layanan ?? '-' }}</td>
                                <td>
                                    {{ $histori->merek_kendaraan }}
                                    <small class="text-muted d-block">({{ $histori->nomor_plat }})</small>
                                </td>
                                <td>{{ $histori->updated_at->format('d M Y H:i') }}</td>
                                <td>
                                    <span class="badge badge-success">Selesai</span>
                                </td>
                                <td>
                                    <a href="{{ route('teknisiservis.dashboard.show', $histori->id) }}"
                                       class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    Belum ada histori pekerjaan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Card --}}
                <div class="d-block d-md-none">
                    @forelse($historiPekerjaan as $histori)
                    <div class="card shadow-sm mb-3 border-left-success">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="font-weight-bold text-dark">
                                    {{ $histori->user->nama_lengkap ?? $histori->user->name ?? '-' }}
                                </span>
                                <span class="badge badge-success">Selesai</span>
                            </div>
                            <div class="mb-3">
                                <p class="small text-muted mb-1">
                                    <i class="fas fa-hashtag fa-fw mr-1"></i>
                                    <code>{{ $histori->kode_booking }}</code>
                                </p>
                                <p class="small text-muted mb-1">
                                    <i class="fas fa-tools fa-fw mr-1"></i>
                                    {{ $histori->layananServis->nama_layanan ?? '-' }}
                                </p>
                                <p class="small text-muted mb-1">
                                    <i class="fas fa-car fa-fw mr-1"></i>
                                    {{ $histori->merek_kendaraan }} ({{ $histori->nomor_plat }})
                                </p>
                                <p class="small text-muted mb-0">
                                    <i class="far fa-calendar-check fa-fw mr-1"></i>
                                    {{ $histori->updated_at->format('d M Y H:i') }}
                                </p>
                            </div>
                            <a href="{{ route('teknisiservis.dashboard.show', $histori->id) }}"
                               class="btn btn-info btn-sm btn-block">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted font-italic py-3 border rounded bg-light">
                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                        Belum ada histori pekerjaan.
                    </div>
                    @endforelse
                </div>

                {{-- Pagination Histori --}}
                <div class="mt-3">
                    {{ $historiPekerjaan->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
