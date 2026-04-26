@extends('layouts.app')

@section('title', 'Monitoring Transaksi')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-2 text-gray-800">Monitoring Transaksi</h1>
    <p class="mb-4">Super Admin dapat melihat semua transaksi dari sistem AC, Futsal, dan Ruko.</p>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">

            <h6 class="m-0 font-weight-bold text-primary">
                Daftar Transaksi
                @if(request('search') || request('sistem') || request('status'))
                    <span class="badge badge-info ml-2">Filter aktif</span>
                @endif
            </h6>

            <form method="GET" class="form-inline flex-wrap">

                <input type="text"
                       name="search"
                       class="form-control form-control-sm mr-2 mb-2"
                       placeholder="Cari ID..."
                       value="{{ request('search') }}">

                <select name="sistem" class="form-control form-control-sm mr-2 mb-2">
                    <option value="">Semua Sistem</option>
                    <option value="AC" {{ request('sistem') == 'AC' ? 'selected' : '' }}>AC</option>
                    <option value="Futsal" {{ request('sistem') == 'Futsal' ? 'selected' : '' }}>Futsal</option>
                    <option value="Ruko" {{ request('sistem') == 'Ruko' ? 'selected' : '' }}>Ruko</option>
                </select>

                <select name="status" class="form-control form-control-sm mr-2 mb-2">
                    <option value="">Semua Status</option>
                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="verifikasi" {{ request('status') == 'verifikasi' ? 'selected' : '' }}>Verifikasi</option>
                    <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>

                <button class="btn btn-primary btn-sm mr-2 mb-2">
                    <i class="fas fa-filter mr-1"></i>Filter
                </button>

                <a href="{{ route('admin.transaksi.index') }}"
                   class="btn btn-outline-secondary btn-sm mb-2">
                    <i class="fas fa-undo mr-1"></i>Reset
                </a>

            </form>

        </div>

        <div class="card-body">

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
                                <td>{{ ($transaksi->firstItem() ?? 0) + $loop->index }}</td>

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

                                <td>
                                    Rp {{ number_format($t->total, 0, ',', '.') }}
                                </td>

                                <td>
                                    @if ($t->status == 'menunggu' || $t->status == 'pending')
                                        <span class="badge badge-secondary">Menunggu</span>
                                    @elseif($t->status == 'verifikasi' || $t->status == 'dibayar')
                                        <span class="badge badge-success">Lunas</span>
                                    @else
                                        <span class="badge badge-danger">Dibatalkan</span>
                                    @endif
                                </td>

                                <td class="d-none d-md-table-cell">
                                    {{ $t->tgl_bayar ?? '-' }}
                                </td>

                                <td>
                                    <a href="{{ route('transaksi.show', $t->id) }}?sistem={{ $t->sistem }}"
                                       class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    Tidak ada data transaksi.
                                </td>
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
