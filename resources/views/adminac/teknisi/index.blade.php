@extends('layouts.app')

@section('title', 'Manajemen Teknisi')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manajemen Teknisi AC</h1>
    <a href="{{ route('adminac.teknisi.create') }}" class="btn btn-primary btn-sm shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50 mr-2"></i> Tambah Teknisi
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Teknisi</h6>
        <form action="{{ route('adminac.teknisi.index') }}" method="GET" class="form-inline">
            <div class="input-group input-group-sm mr-2">
                <input type="text" name="search" class="form-control" placeholder="Cari nama, email, no HP..." value="{{ request('search') }}">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search fa-sm"></i>
                    </button>
                </div>
            </div>
            <select name="status" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="tersedia" {{ request('status') === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                <option value="sibuk" {{ request('status') === 'sibuk' ? 'selected' : '' }}>Sibuk</option>
            </select>
            @if(request('search') || request('status'))
                <a href="{{ route('adminac.teknisi.index') }}" class="btn btn-secondary btn-sm">Reset</a>
            @endif
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