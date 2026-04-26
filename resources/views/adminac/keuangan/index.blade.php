@extends('layouts.app')

@section('title', 'Manajemen Keuangan AC')

@section('content')

    {{-- Page Heading --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Keuangan AC</h1>
        <button class="d-none d-sm-inline-block btn btn-sm btn-danger shadow-sm" data-toggle="modal" data-target="#modalPengeluaran">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Pengeluaran
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="row">
        {{-- Total Pemasukan --}}
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Pemasukan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Pengeluaran --}}
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Pengeluaran</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Saldo Akhir --}}
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Saldo Akhir</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Form --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Data</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.ac.keuangan.index') }}" method="GET" class="form-inline">
                <div class="form-group mb-2 mr-sm-2">
                    <label for="start_date" class="mr-2">Mulai</label>
                    <input type="date" class="form-control" name="start_date" id="start_date" value="{{ request('start_date') }}">
                </div>
                <div class="form-group mb-2 mr-sm-2">
                    <label for="end_date" class="mr-2">Sampai</label>
                    <input type="date" class="form-control" name="end_date" id="end_date" value="{{ request('end_date') }}">
                </div>
                <div class="form-group mb-2 mr-sm-2">
                    <label for="tipe" class="mr-2">Tipe</label>
                    <select class="form-control" name="tipe" id="tipe">
                        <option value="">Semua</option>
                        <option value="pemasukan" {{ request('tipe') == 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                        <option value="pengeluaran" {{ request('tipe') == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mb-2 mr-2"><i class="fas fa-filter"></i> Filter</button>
                <a href="{{ route('admin.ac.keuangan.index') }}" class="btn btn-secondary mb-2"><i class="fas fa-sync"></i> Reset</a>
            </form>
        </div>
    </div>

    {{-- Tabel Riwayat Transaksi --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Transaksi (Pemasukan & Pengeluaran)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Deskripsi / Keterangan</th>
                            <th>Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transaksi as $item)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
                                <td>
                                    @if ($item->tipe === 'pemasukan')
                                        <span class="badge badge-success px-2 py-1">Pemasukan</span>
                                    @else
                                        <span class="badge badge-danger px-2 py-1">Pengeluaran</span>
                                    @endif
                                </td>
                                <td>{{ $item->deskripsi }}</td>
                                <td class="font-weight-bold {{ $item->tipe === 'pemasukan' ? 'text-success' : 'text-danger' }}">
                                    @if($item->tipe === 'pemasukan') + @else - @endif 
                                    Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada data transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Tambah Pengeluaran --}}
    <div class="modal fade" id="modalPengeluaran" tabindex="-1" role="dialog" aria-labelledby="modalPengeluaranTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('admin.ac.keuangan.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalPengeluaranTitle">Tambah Pengeluaran Baru</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="tanggal">Tanggal Pengeluaran</label>
                            <input type="date" class="form-control" name="tanggal" id="tanggal" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi / Keterangan</label>
                            <input type="text" class="form-control" name="deskripsi" id="deskripsi" required placeholder="Contoh: Beli bensin teknisi...">
                        </div>
                        <div class="form-group">
                            <label for="nominal">Nominal (Rp)</label>
                            <input type="number" class="form-control" name="nominal" id="nominal" required min="0" placeholder="Contoh: 50000">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <button class="btn btn-danger" type="submit">Simpan Pengeluaran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
