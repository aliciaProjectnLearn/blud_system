@extends('layouts.app')

@section('title', 'Manajemen Layanan AC')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manajemen Layanan AC</h1>
    <div>
        <button class="btn btn-outline-primary btn-sm shadow-sm mr-2" data-toggle="modal" data-target="#addKategoriModal">
            <i class="fas fa-layer-group fa-sm mr-2"></i> Tambah Kategori
        </button>
        <a href="{{ route('admin.ac.layanan.create') }}" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 mr-2"></i> Tambah Layanan
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <form action="{{ route('admin.ac.layanan.index') }}" method="GET" class="form-inline">
            <div class="form-group mr-2">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari layanan..." value="{{ request('search') }}">
            </div>
            <div class="form-group mr-2">
                <select name="kategori_id" class="form-control form-control-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kategori)
                        <option value="{{ $kategori->id }}" {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}>
                            {{ $kategori->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-search fa-sm mr-1"></i> Filter
            </button>
            @if(request()->has('search') || request()->has('kategori_id'))
                <a href="{{ route('admin.ac.layanan.index') }}" class="btn btn-secondary btn-sm ml-2">Reset</a>
            @endif
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Kategori</th>
                        <th>Nama Layanan</th>
                        <th>Deskripsi<br><small>(Kapasitas AC)</small></th>
                        <th>Harga Jasa</th>
                        <th>Status</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($layanans as $index => $layanan)
                        <tr>
                            <td>{{ $layanans->firstItem() + $index }}</td>
                            <td>{{ $layanan->kategori->nama }}</td>
                            <td>{{ $layanan->nama }}</td>
                            <td>{{ $layanan->kapasitas_ac ?? '-' }}</td>
                            <td>Rp {{ number_format($layanan->harga_jasa, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge badge-success">Aktif</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.ac.layanan.edit', $layanan->id) }}" class="btn btn-info btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal{{ $layanan->id }}" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Delete Modal -->
                        <div class="modal fade" id="deleteModal{{ $layanan->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{ $layanan->id }}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title" id="deleteModalLabel{{ $layanan->id }}">Konfirmasi Hapus</h5>
                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('admin.ac.layanan.destroy', $layanan->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-body text-left">
                                            <p>Apakah Anda yakin ingin menghapus layanan <strong>{{ $layanan->nama }}</strong>?</p>
                                            <p class="text-muted small">Tindakan ini tidak dapat dibatalkan.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger">Hapus Sekarang</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Data layanan tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $layanans->links() }}
        </div>
    </div>
</div>

<!-- Add Kategori Modal -->
<div class="modal fade" id="addKategoriModal" tabindex="-1" role="dialog" aria-labelledby="addKategoriModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addKategoriModalLabel">Tambah Kategori AC Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.ac.kategori.store') }}" method="POST">
                @csrf
                <div class="modal-body text-left">
                    <div class="form-group">
                        <label>Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Sparepart, Material" required>
                        <small class="text-muted">Kategori ini akan otomatis dikategorikan sebagai bagian dari layanan AC.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
