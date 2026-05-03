@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Merek Kendaraan</h1>
        <button type="button" class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#modalTambahMerek">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Merek
        </button>
    </div>

    {{-- Session Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    {{-- Error validation alert --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <form method="GET" action="{{ route('admin.servis.merek.index') }}" class="d-flex gap-2 align-items-center flex-wrap">
                <input type="text" name="search" class="form-control form-control-sm w-auto"
                    placeholder="Cari nama merek..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="{{ route('admin.servis.merek.index') }}" class="btn btn-secondary btn-sm">Reset</a>
            </form>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Merek</th>
                            <th>Tipe</th>
                            <th>Jumlah Model</th>
                            <th>Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($merek as $index => $item)
                            <tr>
                                <td>{{ $merek->firstItem() + $index }}</td>
                                <td>{{ $item->nama }}</td>
                                <td><span class="badge badge-info">{{ ucfirst($item->tipe) }}</span></td>
                                <td>{{ $item->model_kendaraan_count }} model</td>
                                <td>
                                    @if($item->is_active)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-secondary">Non-Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" 
                                        data-target="#modalEditMerek-{{ $item->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.servis.merek.destroy', $item->id) }}" method="POST" class="d-inline form-hapus">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm btn-hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Modal Edit Merek --}}
                            <div class="modal fade" id="modalEditMerek-{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.servis.merek.update', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title font-weight-bold">Edit Merek Kendaraan</h5>
                                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label class="small font-weight-bold">Nama Merek <span class="text-danger">*</span></label>
                                                    <input type="text" name="nama" class="form-control" value="{{ old('nama', $item->nama) }}" required>
                                                </div>
                                                <div class="form-group">
                                                    <label class="small font-weight-bold">Tipe Kendaraan <span class="text-danger">*</span></label>
                                                    <select name="tipe" class="form-control" required>
                                                        <option value="motor" {{ $item->tipe === 'motor' ? 'selected' : '' }}>Motor</option>
                                                        <option value="mobil" {{ $item->tipe === 'mobil' ? 'selected' : '' }}>Mobil</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" id="edit-switch-active-{{ $item->id }}" name="is_active" value="1" {{ $item->is_active ? 'checked' : '' }}>
                                                        <label class="custom-control-label small font-weight-bold" for="edit-switch-active-{{ $item->id }}">Status Aktif</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Batal</button>
                                                <button class="btn btn-primary btn-sm" type="submit">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Data merek kendaraan tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $merek->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah Merek --}}
<div class="modal fade" id="modalTambahMerek" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.servis.merek.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">Tambah Merek Kendaraan</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="small font-weight-bold">Nama Merek <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" placeholder="Honda, Yamaha, Toyota, dll." value="{{ old('nama') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold">Tipe Kendaraan <span class="text-danger">*</span></label>
                        <select name="tipe" class="form-control" required>
                            <option value="motor" {{ old('tipe') === 'motor' ? 'selected' : '' }}>Motor</option>
                            <option value="mobil" {{ old('tipe') === 'mobil' ? 'selected' : '' }}>Mobil</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="switch-active" name="is_active" value="1" checked>
                            <label class="custom-control-label small font-weight-bold" for="switch-active">Status Aktif</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary btn-sm" type="submit">Tambah Merek</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('.btn-hapus').forEach(function(btn) {
        btn.addEventListener('click', function() {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: 'Merek yang masih memiliki model kendaraan yang terhubung tidak dapat dihapus.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e3342f',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.closest('form').submit();
                }
            });
        });
    });
</script>
@endpush
@endsection
