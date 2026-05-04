@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Model Kendaraan</h1>
        <button type="button" class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#modalTambahModel">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Model
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
            <form method="GET" action="{{ route('admin.servis.model.index') }}" class="d-flex gap-2 align-items-center flex-wrap">
                <input type="text" name="search" class="form-control form-control-sm w-auto"
                    placeholder="Cari nama model..." value="{{ request('search') }}">
                <select name="merek_id" class="form-control form-control-sm w-auto">
                    <option value="">-- Semua Merek --</option>
                    @foreach($mereks as $m)
                        <option value="{{ $m->id }}" {{ request('merek_id') == $m->id ? 'selected' : '' }}>{{ $m->nama }} ({{ ucfirst($m->tipe) }})</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="{{ route('admin.servis.model.index') }}" class="btn btn-secondary btn-sm">Reset</a>
            </form>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Merek</th>
                            <th>Nama Model</th>
                            <th>Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($model as $index => $item)
                            <tr>
                                <td>{{ $model->firstItem() + $index }}</td>
                                <td>{{ $item->merek->nama ?? '-' }}</td>
                                <td>{{ $item->nama_model }}</td>
                                <td>
                                    @if($item->is_active)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-secondary">Non-Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" 
                                        data-target="#modalEditModel-{{ $item->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.servis.model.destroy', $item->id) }}" method="POST" class="d-inline form-hapus">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm btn-hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Modal Edit Model --}}
                            <div class="modal fade" id="modalEditModel-{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.servis.model.update', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title font-weight-bold">Edit Model Kendaraan</h5>
                                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label class="small font-weight-bold">Nama Merek <span class="text-danger">*</span></label>
                                                    <select name="merek_kendaraan_id" class="form-control" required>
                                                        @foreach($mereks as $m)
                                                            <option value="{{ $m->id }}" {{ $item->merek_kendaraan_id == $m->id ? 'selected' : '' }}>
                                                                {{ $m->nama }} ({{ ucfirst($m->tipe) }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label class="small font-weight-bold">Nama Model <span class="text-danger">*</span></label>
                                                    <input type="text" name="nama_model" class="form-control" value="{{ old('nama_model', $item->nama_model) }}" required>
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
                                <td colspan="5" class="text-center text-muted">Data model kendaraan tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $model->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah Model --}}
<div class="modal fade" id="modalTambahModel" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.servis.model.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">Tambah Model Kendaraan</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="small font-weight-bold">Merek Kendaraan <span class="text-danger">*</span></label>
                        <select name="merek_kendaraan_id" class="form-control" required>
                            <option value="">-- Pilih Merek --</option>
                            @foreach($mereks as $m)
                                <option value="{{ $m->id }}" {{ old('merek_kendaraan_id') == $m->id ? 'selected' : '' }}>
                                    {{ $m->nama }} ({{ ucfirst($m->tipe) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold">Nama Model <span class="text-danger">*</span></label>
                        <input type="text" name="nama_model" class="form-control" placeholder="Vario 150, Avanza, dll." value="{{ old('nama_model') }}" required>
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
                    <button class="btn btn-primary btn-sm" type="submit">Tambah Model</button>
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
                text: 'Data model kendaraan ini akan dihapus secara permanen.',
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
