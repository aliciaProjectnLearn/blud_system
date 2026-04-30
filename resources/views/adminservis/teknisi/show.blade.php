@extends('layouts.app')

@section('title', 'Detail Teknisi')

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Detail Teknisi</h1>
    <a href="{{ route('admin.servis.teknisi.index') }}" class="btn btn-secondary btn-sm shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50 mr-2"></i> Kembali
    </a>
</div>

{{-- Alert --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif

<div class="row">
    {{-- Info Teknisi --}}
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-user-cog mr-1"></i> Informasi Teknisi
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center"
                         style="width:80px; height:80px; font-size:2rem;">
                        <i class="fas fa-user-cog"></i>
                    </div>
                    <h5 class="mt-3 mb-1 font-weight-bold">{{ $teknisi->nama_lengkap ?? $teknisi->name }}</h5>
                    @foreach($teknisi->roles->whereIn('nama', ['Teknisi Motor', 'Teknisi Mobil']) as $role)
                        <span class="badge {{ $role->nama === 'Teknisi Mobil' ? 'badge-primary' : 'badge-info' }} mb-1">
                            {{ $role->nama }}
                        </span>
                    @endforeach
                    <br>
                    @if($teknisi->status_aktif)
                        <span class="badge badge-success mt-1">Aktif</span>
                    @else
                        <span class="badge badge-secondary mt-1">Nonaktif</span>
                    @endif
                </div>

                <table class="table table-borderless table-sm">
                    <tr>
                        <td class="text-muted" width="40%"><i class="fas fa-user mr-1"></i> Username</td>
                        <td>{{ $teknisi->username ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted"><i class="fas fa-envelope mr-1"></i> Email</td>
                        <td>{{ $teknisi->email }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted"><i class="fas fa-phone mr-1"></i> No. HP</td>
                        <td>{{ $teknisi->no_hp ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted"><i class="fas fa-calendar mr-1"></i> Bergabung</td>
                        <td>{{ $teknisi->created_at->translatedFormat('d F Y') }}</td>
                    </tr>
                </table>

                <hr>
                <div class="d-flex" style="gap: 8px;">
                    <a href="{{ route('admin.servis.teknisi.edit', $teknisi->id) }}"
                       class="btn btn-info btn-sm flex-grow-1">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    <button class="btn btn-sm flex-grow-1 {{ $teknisi->status_aktif ? 'btn-warning' : 'btn-success' }}"
                            data-toggle="modal" data-target="#toggleModal">
                        <i class="fas fa-{{ $teknisi->status_aktif ? 'ban' : 'check' }} mr-1"></i>
                        {{ $teknisi->status_aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="col-lg-8 mb-4">
        <div class="row">
            <div class="col-sm-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Selesai</div>
                                <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $statistik['total_selesai'] }}</div>
                                <div class="text-xs text-muted">Semua waktu</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Tugas Aktif</div>
                                <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $statistik['total_aktif'] }}</div>
                                <div class="text-xs text-muted">Sedang berjalan</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-tools fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Bulan Ini</div>
                                <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $statistik['bulan_ini'] }}</div>
                                <div class="text-xs text-muted">{{ now()->translatedFormat('F Y') }}</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-calendar-check fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Ditugaskan</div>
                                <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $statistik['total_all'] }}</div>
                                <div class="text-xs text-muted">Semua waktu</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-clipboard-list fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Riwayat Pekerjaan --}}
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary mb-2 mb-md-0">
            <i class="fas fa-history mr-1"></i> Riwayat Pekerjaan
        </h6>
        <form action="{{ route('admin.servis.teknisi.show', $teknisi->id) }}" method="GET" class="form-inline">
            <select name="status_riwayat" class="form-control form-control-sm mr-2"
                    onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="menunggu"    {{ request('status_riwayat') === 'menunggu'    ? 'selected' : '' }}>Menunggu</option>
                <option value="dikonfirmasi"{{ request('status_riwayat') === 'dikonfirmasi'? 'selected' : '' }}>Dikonfirmasi</option>
                <option value="diproses"    {{ request('status_riwayat') === 'diproses'    ? 'selected' : '' }}>Diproses</option>
                <option value="selesai"     {{ request('status_riwayat') === 'selesai'     ? 'selected' : '' }}>Selesai</option>
                <option value="dibatalkan"  {{ request('status_riwayat') === 'dibatalkan'  ? 'selected' : '' }}>Dibatalkan</option>
            </select>
            @if(request('status_riwayat'))
                <a href="{{ route('admin.servis.teknisi.show', $teknisi->id) }}"
                   class="btn btn-secondary btn-sm">Reset</a>
            @endif
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Kode Booking</th>
                        <th>Pelanggan</th>
                        <th>Kendaraan</th>
                        <th>Layanan</th>
                        <th>Tanggal</th>
                        <th>Total Biaya</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatPekerjaan as $index => $booking)
                        <tr>
                            <td>{{ $riwayatPekerjaan->firstItem() + $index }}</td>
                            <td><code>{{ $booking->kode_booking }}</code></td>
                            <td>{{ $booking->user->nama_lengkap ?? $booking->user->name ?? '-' }}</td>
                            <td>
                                {{ ucfirst($booking->tipe_kendaraan) }}
                                @if($booking->merek_kendaraan)
                                    <small class="text-muted d-block">{{ $booking->merek_kendaraan }}</small>
                                @endif
                            </td>
                            <td>{{ $booking->layananServis->nama_layanan ?? '-' }}</td>
                            <td>{{ $booking->tanggal_booking->translatedFormat('d F Y') }}</td>
                            <td>
                                Rp {{ number_format(optional($booking->pembayaranServis)->total_biaya ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                @php
                                    $badgeMap = [
                                        'menunggu'     => 'secondary',
                                        'dikonfirmasi' => 'info',
                                        'diproses'     => 'warning',
                                        'selesai'      => 'success',
                                        'dibatalkan'   => 'danger',
                                    ];
                                    $badge = $badgeMap[$booking->status] ?? 'secondary';
                                @endphp
                                <span class="badge badge-{{ $badge }}">{{ ucfirst($booking->status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Belum ada riwayat pekerjaan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $riwayatPekerjaan->appends(request()->query())->links() }}
        </div>
    </div>
</div>

{{-- Modal Toggle Status --}}
<div class="modal fade" id="toggleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header {{ $teknisi->status_aktif ? 'bg-warning' : 'bg-success' }} text-white">
                <h5 class="modal-title">
                    {{ $teknisi->status_aktif ? 'Nonaktifkan Teknisi' : 'Aktifkan Teknisi' }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if($teknisi->status_aktif)
                    <p>Apakah Anda yakin ingin <strong>menonaktifkan</strong> teknisi
                        <strong>{{ $teknisi->nama_lengkap ?? $teknisi->name }}</strong>?</p>
                    @if($statistik['total_aktif'] > 0)
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Teknisi ini masih memiliki <strong>{{ $statistik['total_aktif'] }} booking aktif</strong>.
                            Tidak dapat dinonaktifkan.
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-info-circle mr-1"></i>
                            Teknisi yang dinonaktifkan tidak dapat menerima tugas baru.
                        </div>
                    @endif
                @else
                    <p>Aktifkan kembali teknisi <strong>{{ $teknisi->nama_lengkap ?? $teknisi->name }}</strong>?</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form action="{{ route('admin.servis.teknisi.toggle-status', $teknisi->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="btn {{ $teknisi->status_aktif ? 'btn-warning' : 'btn-success' }}"
                            {{ ($teknisi->status_aktif && $statistik['total_aktif'] > 0) ? 'disabled' : '' }}>
                        {{ $teknisi->status_aktif ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
