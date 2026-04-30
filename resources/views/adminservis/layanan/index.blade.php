@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Layanan Servis</h1>
        <a href="{{ route('admin.servis.layanan.create') }}" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Layanan
        </a>
    </div>

    {{-- Alert --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <form method="GET" action="{{ route('admin.servis.layanan.index') }}" 
                  class="d-flex gap-2 align-items-center flex-wrap">
                <input type="text" name="search" class="form-control form-control-sm w-auto"
                    placeholder="Cari nama layanan..." value="{{ request('search') }}">
                <select name="tipe_kendaraan" class="form-control form-control-sm w-auto">
                    <option value="">-- Semua Tipe --</option>
                    <option value="motor" {{ request('tipe_kendaraan') == 'motor' ? 'selected' : '' }}>Motor</option>
                    <option value="mobil" {{ request('tipe_kendaraan') == 'mobil' ? 'selected' : '' }}>Mobil</option>
                    <option value="keduanya" {{ request('tipe_kendaraan') == 'keduanya' ? 'selected' : '' }}>Semua</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="{{ route('admin.servis.layanan.index') }}" class="btn btn-secondary btn-sm">Reset</a>
            </form>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Layanan</th>
                            <th>Tipe Kendaraan</th>
                            <th>Deskripsi</th>
                            <th>Harga Estimasi</th>
                            <th>Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($layanan as $index => $item)
                            <tr>
                                <td>{{ $layanan->firstItem() + $index }}</td>
                                <td>{{ $item->nama_layanan }}</td>
                                <td>
                                    @if($item->tipe_kendaraan === 'motor')
                                        <span class="badge badge-warning">Motor</span>
                                    @elseif($item->tipe_kendaraan === 'mobil')
                                        <span class="badge badge-primary">Mobil</span>
                                    @else
                                        <span class="badge badge-success">Semua</span>
                                    @endif
                                </td>
                                <td>{{ $item->deskripsi ?? '-' }}</td>
                                <td>Rp {{ number_format($item->harga_estimasi, 0, ',', '.') }}</td>
                                <td>
                                    @if($item->is_active)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-secondary">Non-Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.servis.layanan.edit', $item->id) }}"
                                        class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.servis.layanan.destroy', $item->id) }}"
                                          method="POST" class="d-inline form-hapus">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm btn-hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Data layanan tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $layanan->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('.btn-hapus').forEach(function(btn) {
        btn.addEventListener('click', function() {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: 'Layanan yang sudah digunakan dalam transaksi tidak dapat dihapus.',
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
