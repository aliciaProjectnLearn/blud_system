@extends('layouts.app')

@section('title', 'Manajemen Keuangan Futsal')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Keuangan Futsal</h1>
    </div>

    <!-- Alert Error (Validation) -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Bagian Atas: 3 Card Ringkasan -->
    <div class="row">
        <!-- Card Total Pemasukan -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Pemasukan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Total Pengeluaran -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Pengeluaran</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Saldo Akhir -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Saldo Akhir</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Pengeluaran Per Kategori -->
    @if(isset($summaryKategori) && $summaryKategori->count() > 0)
    <h1 class="h5 mb-3 text-gray-800">Ringkasan Pengeluaran</h1>
    <div class="row mb-4">
        @foreach($summaryKategori as $summary)
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                {{ ucwords(str_replace('_', ' ', $summary->kategori)) }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($summary->total, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Pembagian Pendapatan -->
    <h1 class="h5 mb-3 text-gray-800"><i class="fas fa-chart-pie mr-1"></i> Estimasi Pembagian Pendapatan</h1>
    <div class="row mb-4">
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Porsi Sekolah (60%)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($pembagian['sekolah'], 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-school fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Porsi Pengelola/Unit (40%)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($pembagian['unit'], 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bagian Tengah: Filter dan Tombol Tambah Pengeluaran -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Filter Transaksi</h6>
                    <button type="button" class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#modalTambahPengeluaran">
                        <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Tambah Pengeluaran
                    </button>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.futsal.keuangan.index') }}" method="GET">
                        <div class="form-row align-items-end">
                            <div class="col-md-3 mb-3">
                                <label for="tgl_mulai">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="tgl_mulai" name="tgl_mulai" value="{{ request('tgl_mulai') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="tgl_akhir">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="tgl_akhir" name="tgl_akhir" value="{{ request('tgl_akhir') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="tipe_transaksi">Tipe Transaksi</label>
                                <select class="form-control" id="tipe_transaksi" name="tipe_transaksi">
                                    <option value="">-- Semua Transaksi --</option>
                                    <option value="Pemasukan" {{ request('tipe_transaksi') == 'Pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                                    <option value="Pengeluaran" {{ request('tipe_transaksi') == 'Pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                                <a href="{{ route('admin.futsal.keuangan.index') }}" class="btn btn-secondary"><i class="fas fa-sync"></i> Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bagian Bawah: DataTables Transaksi Gabungan -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Transaksi Keuangan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Kode Transaksi</th>
                            <th>Tipe</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th>Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaksiGabungan as $index => $transaksi)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($transaksi['tanggal_transaksi'])->format('d M Y') }}</td>
                                <td>{{ $transaksi['kode_transaksi'] }}</td>
                                <td>
                                    <!-- Badge berdasarkan Tipe Transaksi -->
                                    @if($transaksi['tipe_transaksi'] == 'Pemasukan')
                                        <span class="badge badge-success">Pemasukan</span>
                                    @else
                                        <span class="badge badge-danger">Pengeluaran</span>
                                    @endif
                                </td>
                                <td>{{ ucwords(str_replace('_', ' ', $transaksi['kategori'])) }}</td>
                                <td>{{ $transaksi['deskripsi'] }}</td>
                                <td>
                                    <!-- Warna text nominal sesuai tipe -->
                                    @if($transaksi['tipe_transaksi'] == 'Pemasukan')
                                        <span class="text-success">+ Rp {{ number_format($transaksi['nominal'], 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-danger">- Rp {{ number_format($transaksi['nominal'], 0, ',', '.') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Modal Tambah Pengeluaran -->
<div class="modal fade" id="modalTambahPengeluaran" tabindex="-1" role="dialog" aria-labelledby="modalTambahPengeluaranLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('admin.futsal.keuangan.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahPengeluaranLabel">Tambah Pengeluaran Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="tgl_pengeluaran">Tanggal Pengeluaran <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tgl_pengeluaran') is-invalid @enderror" id="tgl_pengeluaran" name="tgl_pengeluaran" value="{{ old('tgl_pengeluaran', date('Y-m-d')) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="nominal">Nominal (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('nominal') is-invalid @enderror" id="nominal" name="nominal" placeholder="Contoh: 50000" value="{{ old('nominal') }}" required min="1">
                    </div>
                    <div class="form-group">
                        <label for="kategori">Kategori Pengeluaran <span class="text-danger">*</span></label>
                        <select class="form-control @error('kategori') is-invalid @enderror" id="kategori" name="kategori" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="pemeliharaan" {{ old('kategori') == 'pemeliharaan' ? 'selected' : '' }}>Pemeliharaan</option>
                            <option value="gaji_penjaga" {{ old('kategori') == 'gaji_penjaga' ? 'selected' : '' }}>Gaji Penjaga</option>
                            <option value="lainnya" {{ old('kategori') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi Pengeluaran <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3" placeholder="Contoh: Beli bola futsal baru" required>{{ old('deskripsi') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Pengeluaran</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Inisialisasi DataTables
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            }
        });
    });
</script>
@endpush
