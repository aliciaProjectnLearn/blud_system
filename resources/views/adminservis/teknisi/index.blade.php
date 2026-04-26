@extends('layouts.app')

@section('title', 'Manajemen Teknisi Servis')

@section('content')

{{-- Page Header --}}
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-4">
    <h1 class="h3 mb-2 mb-sm-0 text-gray-800">Manajemen Teknisi Servis</h1>
    <a href="{{ route('admin.servis.teknisi.create') }}" class="btn btn-primary btn-sm shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50 mr-2"></i> Tambah Teknisi
    </a>
</div>

{{-- Alert --}}
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

{{-- Kinerja Teknisi --}}
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-bar mr-1"></i> Kinerja Teknisi Bulan Ini</h6>
        <form method="GET" action="{{ route('admin.servis.teknisi.index') }}" class="form-inline">
            <label for="bulan_filter" class="mr-2 small">Bulan:</label>
            <select name="bulan_filter" id="bulan_filter" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ $bulanFilter == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                    </option>
                @endforeach
            </select>
            @foreach(request()->except('bulan_filter') as $key => $val)
                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
            @endforeach
        </form>
    </div>
    <div class="card-body">
        @if($teknisis->isEmpty())
            <p class="text-center text-muted">Belum ada data teknisi.</p>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>Nama Teknisi</th>
                            <th>Tipe</th>
                            <th class="text-center">Selesai Bulan Ini</th>
                            <th class="text-center">Tugas Aktif</th>
                            <th class="text-center">Ketersediaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($teknisis as $t)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.servis.teknisi.show', $t->id) }}" class="font-weight-bold text-dark">
                                        {{ $t->nama_lengkap ?? $t->name }}
                                    </a><br>
                                    <small class="text-muted">{{ $t->no_hp ?? '-' }}</small>
                                </td>
                                <td>
                                    @foreach($t->roles->whereIn('nama', ['Teknisi Motor', 'Teknisi Mobil']) as $role)
                                        <span class="badge {{ $role->nama === 'Teknisi Mobil' ? 'badge-primary' : 'badge-info' }}">
                                            {{ $role->nama }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="text-center"><strong>{{ $t->total_selesai_bulan_ini }}</strong> Tugas</td>
                                <td class="text-center">{{ $t->total_aktif }} Tugas</td>
                                <td class="text-center">
                                    @if(!$t->status_aktif)
                                        <span class="badge badge-secondary">Nonaktif</span>
                                    @elseif($t->total_aktif > 0)
                                        <span class="badge badge-warning">Sedang Bertugas</span>
                                    @else
                                        <span class="badge badge-success">Tersedia</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- Daftar Teknisi --}}
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary mb-2 mb-md-0">Daftar Teknisi</h6>
        <form action="{{ route('admin.servis.teknisi.index') }}" method="GET"
              class="d-flex flex-column flex-sm-row align-items-sm-center" style="gap: 8px;">
            {{-- Pertahankan bulan_filter --}}
            <input type="hidden" name="bulan_filter" value="{{ $bulanFilter }}">

            <div class="input-group input-group-sm">
                <input type="text" name="search" class="form-control" placeholder="Cari nama, email, no HP..."
                       value="{{ request('search') }}">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search fa-sm"></i></button>
                </div>
            </div>

            <select name="tipe" class="form-control form-control-sm" onchange="this.form.submit()" style="min-width:150px;">
                <option value="">Semua Tipe</option>
                @foreach($roles as $role)
                    <option value="{{ $role->nama }}" {{ request('tipe') === $role->nama ? 'selected' : '' }}>
                        {{ $role->nama }}
                    </option>
                @endforeach
            </select>

            <select name="status" class="form-control form-control-sm" onchange="this.form.submit()" style="min-width:130px;">
                <option value="">Semua Status</option>
                <option value="aktif"    {{ request('status') === 'aktif'    ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>

            @if(request('search') || request('tipe') || request('status'))
                <a href="{{ route('admin.servis.teknisi.index', ['bulan_filter' => $bulanFilter]) }}"
                   class="btn btn-secondary btn-sm">Reset</a>
            @endif
        </form>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>No. HP</th>
                        <th>Tipe</th>
                        <th class="text-center">Status</th>
                        <th width="18%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teknisis as $index => $teknisi)
                        <tr class="{{ !$teknisi->status_aktif ? 'table-secondary' : '' }}">
                            <td>{{ $teknisis->firstItem() + $index }}</td>
                            <td>
                                <a href="{{ route('admin.servis.teknisi.show', $teknisi->id) }}" class="font-weight-bold">
                                    {{ $teknisi->nama_lengkap ?? $teknisi->name }}
                                </a>
                            </td>
                            <td>{{ $teknisi->email }}</td>
                            <td>{{ $teknisi->no_hp ?? '-' }}</td>
                            <td>
                                @foreach($teknisi->roles->whereIn('nama', ['Teknisi Motor', 'Teknisi Mobil']) as $role)
                                    <span class="badge {{ $role->nama === 'Teknisi Mobil' ? 'badge-primary' : 'badge-info' }}">
                                        {{ $role->nama }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="text-center">
                                @if($teknisi->status_aktif)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.servis.teknisi.show', $teknisi->id) }}"
                                   class="btn btn-primary btn-sm" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.servis.teknisi.edit', $teknisi->id) }}"
                                   class="btn btn-info btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                {{-- Toggle Aktif/Nonaktif --}}
                                <button class="btn btn-sm {{ $teknisi->status_aktif ? 'btn-warning' : 'btn-success' }}"
                                        data-toggle="modal"
                                        data-target="#toggleModal{{ $teknisi->id }}"
                                        title="{{ $teknisi->status_aktif ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="fas fa-{{ $teknisi->status_aktif ? 'ban' : 'check' }}"></i>
                                </button>
                                {{-- Hapus --}}
                                <button class="btn btn-danger btn-sm"
                                        data-toggle="modal"
                                        data-target="#deleteModal{{ $teknisi->id }}"
                                        title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        {{-- Modal Toggle Status --}}
                        <div class="modal fade" id="toggleModal{{ $teknisi->id }}" tabindex="-1" role="dialog">
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
                                            <div class="alert alert-warning mb-0">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                Teknisi yang dinonaktifkan tidak akan bisa menerima tugas baru.
                                                Pastikan tidak ada booking aktif sebelum menonaktifkan.
                                            </div>
                                        @else
                                            <p>Aktifkan kembali teknisi <strong>{{ $teknisi->nama_lengkap ?? $teknisi->name }}</strong>?</p>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                        <form action="{{ route('admin.servis.teknisi.toggle-status', $teknisi->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn {{ $teknisi->status_aktif ? 'btn-warning' : 'btn-success' }}">
                                                {{ $teknisi->status_aktif ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan' }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Modal Hapus --}}
                        <div class="modal fade" id="deleteModal{{ $teknisi->id }}" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                                        <button type="button" class="close text-white" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Apakah Anda yakin ingin menghapus teknisi
                                            <strong>{{ $teknisi->nama_lengkap ?? $teknisi->name }}</strong>?</p>
                                        @if($teknisi->total_aktif > 0)
                                            <div class="alert alert-danger mb-0">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                Teknisi ini sedang menangani
                                                <strong>{{ $teknisi->total_aktif }} booking aktif</strong>
                                                dan <strong>tidak dapat dihapus</strong>.
                                            </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                        <form action="{{ route('admin.servis.teknisi.destroy', $teknisi->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger"
                                                    {{ $teknisi->total_aktif > 0 ? 'disabled' : '' }}>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-user-slash fa-2x mb-2 d-block"></i>
                                Tidak ada teknisi ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $teknisis->appends(request()->query())->links() }}
        </div>
    </div>
</div>

@endsection
