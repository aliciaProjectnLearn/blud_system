@extends('layouts.app')

@section('title', 'Pembayaran Gaji Teknisi AC')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Pembayaran Gaji Teknisi</h1>
        <a href="{{ route('admin.ac.keuangan.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Keuangan
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Teknisi</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.ac.keuangan.payroll') }}" method="GET" class="form-inline">
                <div class="form-group mb-2 mr-sm-2">
                    <label class="mr-2">Pilih Teknisi</label>
                    <select name="teknisi_id" class="form-control">
                        <option value="">Semua Teknisi</option>
                        @foreach($teknisis as $t)
                            <option value="{{ $t->id }}" {{ request('teknisi_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mb-2"><i class="fas fa-search mr-1"></i> Cari Pekerjaan</button>
            </form>
        </div>
    </div>

    <form action="{{ route('admin.ac.keuangan.payroll.store') }}" method="POST">
        @csrf
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Pekerjaan Selesai (Belum Digaji)</h6>
                <button type="submit" class="btn btn-success btn-sm font-weight-bold px-4" onclick="return confirm('Konfirmasi pembayaran gaji untuk item terpilih?')">
                    <i class="fas fa-check-double mr-1"></i> Proses Gaji Terpilih
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th width="50px" class="text-center">Select</th>
                                <th>Teknisi</th>
                                <th>Pelanggan / Booking</th>
                                <th>Layanan</th>
                                <th>Tanggal Selesai</th>
                                <th class="text-right">Nominal Gaji (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pekerjaanUnpaid as $item)
                                <tr>
                                    <td class="text-center align-middle">
                                        <input type="checkbox" name="booking_id[]" value="{{ $item->id }}" class="item-checkbox" style="width: 20px; height: 20px;">
                                    </td>
                                    <td class="align-middle">
                                        <div class="font-weight-bold">{{ $item->teknisi->name }}</div>
                                        <div class="small text-muted">{{ $item->teknisi->no_hp }}</div>
                                    </td>
                                    <td class="align-middle">
                                        <div>{{ $item->nama_pelanggan ?? ($item->user->nama_lengkap ?? '-') }}</div>
                                        <div class="badge badge-light border small text-muted">ID: #{{ $item->id }}</div>
                                    </td>
                                    <td class="align-middle">{{ $item->layanan->nama ?? '-' }}</td>
                                    <td class="align-middle">{{ \Carbon\Carbon::parse($item->updated_at)->format('d/m/Y') }}</td>
                                    <td class="align-middle text-right">
                                        <input type="number" name="nominal[]" class="form-control text-right" value="50000" min="0">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted italic">Tidak ada pekerjaan yang menunggu penggajian.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
@endsection
