@extends('layouts.app')

@section('title', 'Manajemen Unit Kantin')

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-store mr-2 text-primary"></i>Manajemen Unit Kantin
        </h1>
        <a href="{{ route('admin.kantin.unit.create') }}" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-plus fa-sm mr-1"></i> Tambah Unit
        </a>
    </div>


    {{-- Card Statistik Ringkasan --}}
    <div class="row mb-3">
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Unit</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $units->total() }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-store fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Unit Terisi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $units->getCollection()->where('status_unit', 'terisi')->count() }}
                            </div>
                        </div>
                        <div class="col-auto"><i class="fas fa-door-closed fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Unit Kosong</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $units->getCollection()->where('status_unit', 'kosong')->count() }}
                            </div>
                        </div>
                        <div class="col-auto"><i class="fas fa-door-open fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Card Tabel Utama --}}
    <div class="card shadow mb-4">

        {{-- Card Header + Filter --}}
        <div class="card-header py-3">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list mr-1"></i> Daftar Unit Kantin
                    </h6>
                </div>
                {{-- Filter Form --}}
                <div class="col-md-8">
                    <form method="GET" action="{{ route('admin.kantin.unit.index') }}" class="form-inline justify-content-md-end">
                        {{-- Filter Kategori --}}
                        <div class="form-group mr-2 mb-0">
                            <label class="mr-1 text-xs font-weight-bold text-gray-600">Kategori</label>
                            <select name="kategori_id" class="form-control form-control-sm" onchange="this.form.submit()">
                                <option value="">-- Semua --</option>
                                @foreach ($kategoris as $kat)
                                    <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>
                                        {{ $kat->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filter Status --}}
                        <div class="form-group mr-2 mb-0">
                            <label class="mr-1 text-xs font-weight-bold text-gray-600">Status</label>
                            <select name="status_unit" class="form-control form-control-sm" onchange="this.form.submit()">
                                <option value="">-- Semua --</option>
                                <option value="terisi"  {{ request('status_unit') === 'terisi'  ? 'selected' : '' }}>Terisi</option>
                                <option value="kosong"  {{ request('status_unit') === 'kosong'  ? 'selected' : '' }}>Kosong</option>
                            </select>
                        </div>

                        {{-- Reset Filter --}}
                        @if (request()->hasAny(['kategori_id', 'status_unit']))
                            <a href="{{ route('admin.kantin.unit.index') }}" class="btn btn-sm btn-outline-secondary mb-0">
                                <i class="fas fa-times"></i> Reset
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTableUnit" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center" style="width:5%;">No</th>
                            <th style="width:12%;">Kode Unit</th>
                            <th style="width:20%;">Nama / Kategori</th>
                            <th class="text-right" style="width:18%;">Harga Sewa</th>
                            <th class="text-center" style="width:10%;">Dokumen</th>
                            <th class="text-center" style="width:12%;">Status</th>
                            <th class="text-center" style="width:23%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($units as $i => $unit)
                            <tr>
                                {{-- No --}}
                                <td class="text-center">{{ $units->firstItem() + $i }}</td>

                                {{-- Kode Unit --}}
                                <td>
                                    <span class="font-weight-bold text-primary">
                                        {{ $unit->kode_unit ?? '-' }}
                                    </span>
                                </td>

                                {{-- Nama / Kategori --}}
                                <td>
                                    <div class="font-weight-bold">{{ $unit->kategori->nama ?? '-' }}</div>
                                    <small class="text-muted">Unit #{{ $unit->id }}</small>
                                </td>

                                {{-- Harga --}}
                                <td class="text-right">
                                    @if ($unit->harga)
                                        <span class="font-weight-bold">
                                            Rp {{ number_format($unit->harga, 0, ',', '.') }}
                                        </span>
                                        <br><small class="text-muted">/tahun</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                {{-- Jumlah Dokumen --}}
                                <td class="text-center">
                                    @if ($unit->dokumentasiUnit->count() > 0)
                                        <span class="badge badge-info">
                                            {{ $unit->dokumentasiUnit->count() }} file
                                        </span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>

                                {{-- Status Badge --}}
                                <td class="text-center">
                                    @if ($unit->status_unit === 'terisi')
                                        <span class="badge badge-success px-2 py-1">
                                            <i class="fas fa-circle fa-xs mr-1"></i>Terisi
                                        </span>
                                    @else
                                        <span class="badge badge-secondary px-2 py-1">
                                            <i class="fas fa-circle fa-xs mr-1"></i>Kosong
                                        </span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="text-center">
                                    {{-- Tombol Detail --}}
                                    <a href="{{ route('admin.kantin.unit.show', $unit->id) }}"
                                       class="btn btn-info btn-sm" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- Tombol Edit --}}
                                    <a href="{{ route('admin.kantin.unit.edit', $unit->id) }}"
                                       class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>

                                    {{-- Tombol Hapus: HANYA tampil jika status KOSONG --}}
                                    @if ($unit->status_unit === 'kosong')
                                        <button type="button"
                                                class="btn btn-danger btn-sm btn-hapus"
                                                title="Hapus"
                                                data-id="{{ $unit->id }}"
                                                data-kode="{{ $unit->kode_unit }}">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                        {{-- Form hapus tersembunyi --}}
                                        <form id="form-hapus-{{ $unit->id }}"
                                              action="{{ route('admin.kantin.unit.destroy', $unit->id) }}"
                                              method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @else
                                        {{-- Placeholder agar kolom tidak bergeser --}}
                                        <button class="btn btn-danger btn-sm" disabled title="Tidak bisa dihapus saat Terisi">
                                            <i class="fas fa-lock"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    Belum ada data unit.
                                    <a href="{{ route('admin.kantin.unit.create') }}">Tambah sekarang</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($units->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted small">
                        Menampilkan {{ $units->firstItem() }}–{{ $units->lastItem() }}
                        dari {{ $units->total() }} unit
                    </div>
                    {{ $units->links() }}
                </div>
            @endif
        </div>
    </div>

</div>

{{-- Modal Konfirmasi Hapus --}}
<div class="modal fade" id="modalHapus" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Konfirmasi Hapus
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-1">Apakah Anda yakin ingin menghapus unit:</p>
                <p class="font-weight-bold text-danger mb-0" id="kodeUnitTarget">-</p>
                <small class="text-muted">Semua file dokumentasi terkait juga akan ikut dihapus.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Batal
                </button>
                <button type="button" class="btn btn-danger btn-sm" id="btnKonfirmasiHapus">
                    <i class="fas fa-trash mr-1"></i>Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Tangkap semua tombol hapus → tampilkan modal konfirmasi
    let targetFormId = null;

    document.querySelectorAll('.btn-hapus').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id   = this.dataset.id;
            const kode = this.dataset.kode;

            targetFormId = 'form-hapus-' + id;
            document.getElementById('kodeUnitTarget').textContent = kode;
            $('#modalHapus').modal('show');
        });
    });

    // Submit form hapus setelah konfirmasi
    document.getElementById('btnKonfirmasiHapus').addEventListener('click', function () {
        if (targetFormId) {
            document.getElementById(targetFormId).submit();
        }
    });
</script>
@endpush
