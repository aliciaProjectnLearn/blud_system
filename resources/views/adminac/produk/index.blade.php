@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Produk / Material AC</h1>
        <div>
            <button class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm mr-2" data-toggle="modal" data-target="#tambahKategoriModal">
                <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Kategori
            </button>
            <button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#tambahModal">
                <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Produk
            </button>
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

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Produk</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('adminac.produk.index') }}" class="mb-4">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <input type="text" name="search" class="form-control" placeholder="Cari Nama Produk..." value="{{ $search }}">
                    </div>
                    <div class="col-md-4 mb-2">
                        <select name="id_kategori_komponen" class="form-control">
                            <option value="">-- Semua Kategori --</option>
                            @foreach($kategoris as $kategori)
                                <option value="{{ $kategori->id }}" {{ $kategori_id == $kategori->id ? 'selected' : '' }}>
                                    {{ $kategori->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tampilkan</button>
                        <a href="{{ route('adminac.produk.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Satuan</th>
                            <th>Stok</th>
                            <th class="text-center" style="width: 15%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($produks as $produk)
                        <tr>
                            <td>{{ $loop->iteration + $produks->firstItem() - 1 }}</td>
                            <td>{{ $produk->nama_produk }}</td>
                            <td>{{ $produk->kategori->nama ?? '-' }}</td>
                            <td>Rp {{ number_format($produk->harga, 0, ',', '.') }}</td>
                            <td>{{ $produk->satuan }}</td>
                            <td>
                                {{ $produk->stok }}
                                @if($produk->stok < 5)
                                    <span class="badge badge-danger ml-1">Stok Menipis</span>
                                @endif
                            </td>
                            <td class="text-center">
                       
                            <button class="btn btn-sm btn-info mb-1" data-toggle="modal" data-target="#editModal{{ $produk->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('adminac.produk.destroy', $produk->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger mb-1">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>



                        <!-- Modal Edit -->
                        <div class="modal fade" id="editModal{{ $produk->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{ $produk->id }}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <form action="{{ route('adminac.produk.update', $produk->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel{{ $produk->id }}">Edit Produk</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Kategori Komponen</label>
                                                <select name="id_kategori_komponen" class="form-control" required>
                                                    <option value="">-- Pilih Kategori --</option>
                                                    @foreach($kategoris as $k)
                                                        <option value="{{ $k->id }}" {{ $produk->id_kategori_komponen == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Nama Produk</label>
                                                <input type="text" name="nama_produk" class="form-control" value="{{ $produk->nama_produk }}" required>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-4">
                                                    <label>Satuan</label>
                                                    <input type="text" name="satuan" class="form-control" value="{{ $produk->satuan }}" placeholder="Meter, Pcs, dll" required>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Harga (Rp)</label>
                                                    <input type="number" name="harga" class="form-control" value="{{ $produk->harga }}" min="0" required>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Stok</label>
                                                    <input type="number" name="stok" class="form-control" value="{{ $produk->stok }}" min="0" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Deskripsi (Opsional)</label>
                                                <textarea name="deskripsi" class="form-control" rows="3">{{ $produk->deskripsi }}</textarea>
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
                            <td colspan="7" class="text-center">Data produk tidak ditemukan.</td>
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
<div class="modal fade" id="tambahModal" tabindex="-1" role="dialog" aria-labelledby="tambahModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('adminac.produk.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahModalLabel">Tambah Produk Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kategori Komponen</label>
                        <select name="id_kategori_komponen" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoris as $k)
                                <option value="{{ $k->id }}">{{ $k->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nama Produk</label>
                        <input type="text" name="nama_produk" class="form-control" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Satuan</label>
                            <input type="text" name="satuan" class="form-control" placeholder="Meter, Pcs, dll" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Harga (Rp)</label>
                            <input type="number" name="harga" class="form-control" min="0" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Stok</label>
                            <input type="number" name="stok" class="form-control" value="0" min="0" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi (Opsional)</label>
                        <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Kategori -->
<div class="modal fade" id="tambahKategoriModal" tabindex="-1" role="dialog" aria-labelledby="tambahKategoriModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('adminac.kategori_komponen.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahKategoriModalLabel">Tambah Kategori Komponen Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Kategori</label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Sensor, Kompresor..." required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
