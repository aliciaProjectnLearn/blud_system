@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Manajemen Penyewaan</h1>

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Pencarian</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.kantin.penyewaan.index') }}" method="GET" class="row">
                <div class="col-6 col-md-3 mb-3">
                    <label>Status Sewa</label>
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <label>Unit Ruko</label>
                    <select name="ruko_id" class="form-control">
                        <option value="">Semua Unit</option>
                        @foreach($rukos as $ruko)
                            <option value="{{ $ruko->id }}" {{ request('ruko_id') == $ruko->id ? 'selected' : '' }}>
                                {{ $ruko->kode_unit }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <label>Nama Penyewa</label>
                    <select name="penyewa_id" class="form-control">
                        <option value="">Semua Penyewa</option>
                        @foreach($penyewas as $p)
                            <option value="{{ $p->id }}" {{ request('penyewa_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->user->nama_lengkap ?? $p->nama_usaha }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">Cari</button>
                    <a href="{{ route('admin.kantin.penyewaan.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Penyewaan Ruko / Kantin</h6>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered" width="100%">
                    <thead class="thead-light">
                        <tr>
                            <th class="d-none d-sm-table-cell">No</th>
                            <th>Nama Penyewa</th>
                            <th>Kode Unit</th>
                            <th class="d-none d-md-table-cell">Jenis Unit</th>
                            <th>Tgl Mulai</th>
                            <th class="d-none d-md-table-cell">Tgl Selesai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $index => $item)
                        <tr>
                            <td class="d-none d-sm-table-cell">{{ $index + 1 }}</td>
                            <td>{{ $item->penyewa->user->nama_lengkap ?? $item->penyewa->nama_usaha }}</td>
                            <td>{{ $item->ruko->kode_unit }}</td>
                            <td class="d-none d-md-table-cell">{{ $item->ruko->kategori->nama ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tgl_mulai)->format('d/m/Y') }}</td>
                            <td class="d-none d-md-table-cell">{{ \Carbon\Carbon::parse($item->tgl_selesai)->format('d/m/Y') }}</td>
                            <td>
                                @if($item->status == 'aktif')
                                    <span class="badge badge-success">Aktif</span>
                                @elseif($item->status == 'selesai')
                                    <span class="badge badge-secondary">Selesai</span>
                                @else
                                    <span class="badge badge-danger">Dibatalkan</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.kantin.penyewaan.show', $item->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> <span class="d-none d-md-inline">Detail</span>
                                </a>
                                
                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal{{ $item->id }}">
                                    <i class="fas fa-edit"></i> <span class="d-none d-md-inline">Edit</span>
                                </button>

                                @if($item->status != 'aktif')
                                <form action="{{ route('admin.kantin.penyewaan.destroy', $item->id) }}" method="POST" style="display:inline;" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirmDelete(event)">
                                        <i class="fas fa-trash"></i> <span class="d-none d-md-inline">Hapus</span>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <form action="{{ route('admin.kantin.penyewaan.update', $item->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Penyewaan: {{ $item->ruko->kode_unit }}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Tanggal Mulai</label>
                                                <input type="date" name="tgl_mulai" class="form-control" value="{{ $item->tgl_mulai }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Tanggal Selesai</label>
                                                <input type="date" name="tgl_selesai" class="form-control" value="{{ $item->tgl_selesai }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Total Biaya Tahunan</label>
                                                <input type="number" name="total_biaya_tahunan" class="form-control" value="{{ $item->total_biaya_tahunan }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="status" class="form-control" required>
                                                    <option value="aktif" {{ $item->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                                    <option value="selesai" {{ $item->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                                    <option value="dibatalkan" {{ $item->status == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data penyewaan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(event) {
        event.preventDefault();
        const form = event.target.closest('form');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data penyewaan akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
</script>
@endsection
