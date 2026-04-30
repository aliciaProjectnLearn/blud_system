@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Produk Servis</h1>
        <button class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#modalTambah">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Produk
        </button>
    </div>

    {{-- Alert --}}
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
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <form method="GET" action="{{ route('admin.servis.produk.index') }}" 
                  class="d-flex gap-2 align-items-center flex-wrap">
                <input type="text" name="search" class="form-control form-control-sm w-auto"
                    placeholder="Cari nama produk..." value="{{ request('search') }}">
                <select name="tipe_kendaraan" class="form-control form-control-sm w-auto">
                    <option value="">-- Semua Tipe --</option>
                    <option value="motor" {{ request('tipe_kendaraan') == 'motor' ? 'selected' : '' }}>Motor</option>
                    <option value="mobil" {{ request('tipe_kendaraan') == 'mobil' ? 'selected' : '' }}>Mobil</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="{{ route('admin.servis.produk.index') }}" class="btn btn-secondary btn-sm">Reset</a>
            </form>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Produk</th>
                            <th>Tipe Kendaraan</th>
                            <th>Kode Part / Merk</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($produks as $index => $item)
                            <tr>
                                <td>{{ $produks->firstItem() + $index }}</td>
                                <td>{{ $item->nama_produk }}</td>
                                <td>
                                    @if($item->tipe_kendaraan === 'motor')
                                        <span class="badge badge-warning">Motor</span>
                                    @elseif($item->tipe_kendaraan === 'mobil')
                                        <span class="badge badge-primary">Mobil</span>
                                    @endif
                                </td>
                                <td>{{ $item->kode_part ?? '-' }} / {{ $item->merk ?? '-' }}</td>
                                <td>Rp {{ number_format($item->harga, 2, ',', '.') }}</td>
                                <td>{{ $item->stok }} {{ $item->satuan }}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalEdit{{ $item->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.servis.produk.destroy', $item->id) }}"
                                          method="POST" class="d-inline form-hapus">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm btn-hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Modal Edit -->
                            <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <form action="{{ route('admin.servis.produk.update', $item->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Produk</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Nama Produk <span class="text-danger">*</span></label>
                                                    <input type="text" name="nama_produk" class="form-control" value="{{ $item->nama_produk }}" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Tipe Kendaraan <span class="text-danger">*</span></label>
                                                    <select name="tipe_kendaraan" class="form-control" required>
                                                        <option value="motor" {{ $item->tipe_kendaraan == 'motor' ? 'selected' : '' }}>Motor</option>
                                                        <option value="mobil" {{ $item->tipe_kendaraan == 'mobil' ? 'selected' : '' }}>Mobil</option>
                                                    </select>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 form-group">
                                                        <label>Harga <span class="text-danger">*</span></label>
                                                        <input type="number" step="0.01" name="harga" class="form-control" value="{{ $item->harga }}" required>
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                        <label>Stok <span class="text-danger">*</span></label>
                                                        <input type="number" name="stok" class="form-control" value="{{ $item->stok }}" required>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 form-group">
                                                        <label>Kode Part</label>
                                                        <input type="text" name="kode_part" class="form-control" value="{{ $item->kode_part }}">
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                        <label>Merk</label>
                                                        <input type="text" name="merk" class="form-control" value="{{ $item->merk }}">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 form-group">
                                                        <label>Satuan</label>
                                                        <input type="text" name="satuan" class="form-control" value="{{ $item->satuan }}" placeholder="pcs">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Deskripsi</label>
                                                    <textarea name="deskripsi" class="form-control" rows="3">{{ $item->deskripsi }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Data produk tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $produks->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('admin.servis.produk.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Produk Servis</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="nama_produk" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Tipe Kendaraan <span class="text-danger">*</span></label>
                        <select name="tipe_kendaraan" class="form-control" required>
                            <option value="motor">Motor</option>
                            <option value="mobil">Mobil</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Harga <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="harga" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Stok <span class="text-danger">*</span></label>
                            <input type="number" name="stok" class="form-control" value="0" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Kode Part</label>
                            <input type="text" name="kode_part" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Merk</label>
                            <input type="text" name="merk" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Satuan</label>
                            <input type="text" name="satuan" class="form-control" value="pcs">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"></textarea>
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

@push('scripts')
<script>
    document.querySelectorAll('.btn-hapus').forEach(function(btn) {
        btn.addEventListener('click', function() {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: 'Produk yang sudah digunakan dalam transaksi tidak dapat dihapus.',
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
