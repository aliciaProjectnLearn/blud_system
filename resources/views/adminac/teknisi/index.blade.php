@extends('layouts.app')

@section('title', 'Manajemen Teknisi')

@section('content')
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-4">
    <h1 class="h3 mb-3 mb-sm-0 text-gray-800">Manajemen Teknisi AC</h1>
    <a href="{{ route('adminac.teknisi.create') }}" class="btn btn-primary btn-sm shadow-sm w-100 w-sm-auto">
        <i class="fas fa-plus fa-sm text-white-50 mr-2"></i> Tambah Teknisi
    </a>
</div>
    {{-- Kinerja Teknisi --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Kinerja Teknisi</h6>
            
            {{-- Filter Form --}}
            <form method="GET" action="{{ route('adminac.teknisi.index') }}" class="form-inline">
                <label for="bulan_filter" class="mr-2 small">Bulan:</label>
                <select name="bulan_filter" id="bulan_filter" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ request('bulan_filter', \Carbon\Carbon::today()->month) == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
                @if(request('search') || request('status'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
            </form>
        </div>
        <div class="card-body">
            @if ($dataTeknisi->isEmpty())
                <p class="text-center text-muted">Belum ada data teknisi terdaftar.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nama Teknisi</th>
                                <th>Telepon / NIK</th>
                                <th>Pekerjaan Selesai (Sesuai Filter Bulan)</th>
                                <th>Tugas Aktif (Proses)</th>
                                <th>Status Ketersediaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataTeknisi as $teknisi)
                                <tr>
                                    <td>{{ $teknisi->nama_lengkap ?? $teknisi->name }}</td>
                                    <td>{{ $teknisi->no_hp ?? '-' }}<br><small class="text-muted">{{ $teknisi->nik ?? '' }}</small></td>
                                    <td class="text-center"><strong>{{ $teknisi->total_selesai_bulan_ini }}</strong> Tugas</td>
                                    <td class="text-center">{{ $teknisi->total_aktif }} Tugas</td>
                                    <td>
                                        @if($teknisi->total_aktif > 0)
                                            <span class="badge badge-warning">Sedang Bertugas</span>
                                        @else
                                            <span class="badge badge-success">Tersedia (Kosong)</span>
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

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary mb-2 mb-md-0">Daftar Teknisi</h6>
        <form action="{{ route('adminac.teknisi.index') }}" method="GET" class="form-inline w-100 w-md-auto d-flex flex-column flex-sm-row flex-wrap gap-2">
            <div class="input-group input-group-sm flex-grow-1 mb-2 mb-sm-0">
                <input type="text" name="search" class="form-control" placeholder="Cari nama, email, no HP..." value="{{ request('search') }}">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search fa-sm"></i>
                    </button>
                </div>
            </div>
            <div class="d-flex w-100 w-sm-auto mb-2 mb-sm-0 gap-2">
                <select name="status" class="form-control form-control-sm w-100 w-sm-auto" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="tersedia" {{ request('status') === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="sibuk" {{ request('status') === 'sibuk' ? 'selected' : '' }}>Sibuk</option>
                </select>
                @if(request('search') || request('status'))
                    <a href="{{ route('adminac.teknisi.index') }}" class="btn btn-secondary btn-sm flex-shrink-0">Reset</a>
                @endif
            </div>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>No. Handphone</th>
                        <th>Status</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teknisis as $index => $teknisi)
                        <tr>
                            <td>{{ $teknisis->firstItem() + $index }}</td>
                            <td>{{ $teknisi->nama_lengkap }}</td>
                            <td>{{ $teknisi->email }}</td>
                            <td>{{ $teknisi->no_hp ?? '-' }}</td>
                            <td>
                                @if($teknisi->status_dinamis === 'tersedia')
                                    <span class="badge badge-success">Tersedia</span>
                                @else
                                    <span class="badge badge-warning text-white">Sibuk</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('adminac.teknisi.edit', $teknisi->id) }}" class="btn btn-info btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal{{ $teknisi->id }}" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Delete Modal -->
                        <div class="modal fade" id="deleteModal{{ $teknisi->id }}" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                                        <button type="button" class="close text-white" data-dismiss="modal">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('adminac.teknisi.destroy', $teknisi->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-body">
                                            <p>Apakah Anda yakin ingin menghapus teknisi <strong>{{ $teknisi->nama_lengkap }}</strong>?</p>
                                            @if($teknisi->status_dinamis === 'sibuk')
                                                <div class="alert alert-warning mb-0">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                                    Teknisi ini sedang menangani booking dan <strong>tidak dapat dihapus</strong>.
                                                </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger" {{ $teknisi->status_dinamis === 'sibuk' ? 'disabled' : '' }}>Hapus</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Data teknisi belum tersedia.</td>
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