@extends('layouts.app')

@section('title', 'Monitoring Transaksi')

@section('content')
    <div class="container-fluid">

        <h1 class="h3 mb-2 text-gray-800">Monitoring Transaksi</h1>
        <p class="mb-4">Super Admin dapat melihat semua transaksi dari sistem AC, Futsal, dan Ruko.</p>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Transaksi</h6>
            </div>
            <div class="card-body">

                <form method="GET" class="mb-3">
                    <div class="row">
                        <div class="col-12 col-sm-6 col-lg-3 mb-2">
                            <input type="text" name="search" class="form-control form-control-sm"
                                placeholder="Cari ID transaksi..." value="{{ request('search') }}">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3 mb-2">
                            <select name="sistem" class="form-control form-control-sm">
                                <option value="">Semua Sistem</option>
                                <option value="AC" {{ request('sistem') == 'AC' ? 'selected' : '' }}>AC</option>
                                <option value="Futsal" {{ request('sistem') == 'Futsal' ? 'selected' : '' }}>Futsal</option>
                                <option value="Ruko" {{ request('sistem') == 'Ruko' ? 'selected' : '' }}>Ruko</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3 mb-2">
                            <select name="status" class="form-control form-control-sm">
                                <option value="">Semua Status</option>
                                <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu
                                </option>
                                <option value="verifikasi" {{ request('status') == 'verifikasi' ? 'selected' : '' }}>
                                    Verifikasi</option>
                                <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>
                                    Dibatalkan</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3 mb-2">
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary btn-sm btn-block">Filter</button>
                                <a href="{{ route('transaksi.index') }}"
                                    class="btn btn-secondary btn-sm btn-block">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Sistem</th>
                                <th>ID</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th class="d-none d-md-table-cell">Tgl Bayar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaksi as $t)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @if ($t->sistem == 'AC')
                                            <span class="badge badge-primary">AC</span>
                                        @elseif($t->sistem == 'Futsal')
                                            <span class="badge badge-success">Futsal</span>
                                        @else
                                            <span class="badge badge-warning">Ruko</span>
                                        @endif
                                    </td>
                                    <td>#{{ $t->id }}</td>
                                    <td>Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                                    <td>
                                        @if ($t->status == 'menunggu' || $t->status == 'pending')
                                            <span class="badge badge-secondary">Menunggu</span>
                                        @elseif($t->status == 'verifikasi' || $t->status == 'dibayar')
                                            <span class="badge badge-success">Lunas</span>
                                        @else
                                            <span class="badge badge-danger">Dibatalkan</span>
                                        @endif
                                    </td>
                                    <td class="d-none d-md-table-cell">{{ $t->tgl_bayar ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('transaksi.show', $t->id) }}?sistem={{ $t->sistem }}"
                                            class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Tidak ada data transaksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $transaksi->appends(request()->query())->links() }}
                </div>

            </div>
        </div>
    </div>
@endsection
