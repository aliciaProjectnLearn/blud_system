{{-- resources/views/adminfutsal/membership/paket/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- Page Heading --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Paket Membership</h1>
        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambah">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Paket
        </button>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }} <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }} <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    {{-- Statistik --}}
    <div class="row mb-4">
        <div class="col-xl-6 col-md-6">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Paket</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_paket'] }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-box fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-6">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Pengguna</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_pengguna'] }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Paket Membership</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tabelPaket" width="100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Paket</th>
                            <th>Jumlah Kuota</th>
                            <th>Harga</th>
                            <th>Pengguna</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pakets as $i => $paket)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $paket->nama_paket }}</td>
                            <td>{{ $paket->jumlah_kuota }}x bermain</td>
                            <td>Rp {{ number_format($paket->harga, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge badge-info">{{ $paket->memberships_count }} pengguna</span>
                            </td>
                            <td>
                                @if($paket->status === 'aktif')
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-warning btn-sm"
                                    data-toggle="modal"
                                    data-target="#modalEdit"
                                    data-id="{{ $paket->id }}"
                                    data-nama="{{ $paket->nama_paket }}"
                                    data-kuota="{{ $paket->jumlah_kuota }}"
                                    data-harga="{{ $paket->harga }}"
                                    data-status="{{ $paket->status }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('adminfutsal.paket-membership.destroy', $paket) }}"
                                    method="POST" class="d-inline"
                                    onsubmit="return confirm('Hapus paket ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center">Belum ada paket membership.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('adminfutsal.paket-membership.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Paket Membership</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Paket</label>
                        <input type="text" name="nama_paket" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Jumlah Kuota (kali bermain)</label>
                        <input type="number" name="jumlah_kuota" class="form-control" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>Harga (Rp)</label>
                        <input type="number" name="harga" class="form-control" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="aktif">Aktif</option>
                            <option value="tidak aktif">Tidak Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <form id="formEdit" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Paket Membership</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Paket</label>
                        <input type="text" name="nama_paket" id="edit_nama" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Jumlah Kuota (kali bermain)</label>
                        <input type="number" name="jumlah_kuota" id="edit_kuota" class="form-control" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>Harga (Rp)</label>
                        <input type="number" name="harga" id="edit_harga" class="form-control" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="edit_status" class="form-control">
                            <option value="aktif">Aktif</option>
                            <option value="tidak aktif">Tidak Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
$('#modalEdit').on('show.bs.modal', function (e) {
    const btn    = $(e.relatedTarget);
    const id     = btn.data('id');
    $('#formEdit').attr('action', `/adminfutsal/paket-membership/${id}`);
    $('#edit_nama').val(btn.data('nama'));
    $('#edit_kuota').val(btn.data('kuota'));
    $('#edit_harga').val(btn.data('harga'));
    $('#edit_status').val(btn.data('status'));
});
</script>
@endpush
@endsection
