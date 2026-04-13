@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Pembayaran Sewa</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }} <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }} <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary mb-2 mb-md-0">Daftar Pembayaran</h6>
                <form method="GET" action="{{ route('adminkantin.pembayaran.index') }}">
                    <div class="row g-2">
                        <div class="col-12 col-md-auto">
                            <input type="text" name="search" class="form-control form-control-sm"
                                placeholder="Cari nama usaha..." value="{{ request('search') }}">
                        </div>
                        <div class="col-6 col-md-auto">
                            <select name="termin" class="form-control form-control-sm">
                                <option value="">-- Semua Termin --</option>
                                <option value="1" {{ request('termin') == '1' ? 'selected' : '' }}>Termin 1</option>
                                <option value="2" {{ request('termin') == '2' ? 'selected' : '' }}>Termin 2</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-auto">
                            <select name="status" class="form-control form-control-sm">
                                <option value="">-- Semua Status --</option>
                                <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                <option value="verifikasi" {{ request('status') === 'verifikasi' ? 'selected' : '' }}>Verifikasi</option>
                                <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                                <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-auto d-flex gap-1">
                            <button type="submit" class="btn btn-sm btn-secondary mr-1">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            @if(request()->filled('search') || request()->filled('status') || request()->filled('termin'))
                                <a href="{{ route('adminkantin.pembayaran.index') }}" class="btn btn-sm btn-light">Reset</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%">
                    <thead>
                        <tr>
                            <th class="d-none d-sm-table-cell">No</th>
                            <th>Penyewa</th>
                            <th>Kode Unit</th>
                            <th>Termin</th>
                            <th class="d-none d-md-table-cell">Jatuh Tempo</th>
                            <th>Jumlah Tagihan</th>
                            <th class="d-none d-lg-table-cell">Tgl Bayar</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pembayarans as $i => $p)
                        @php $terlambat = $p->isTerlambat(); @endphp
                        <tr class="{{ $terlambat ? 'table-danger' : '' }}">
                            <td class="d-none d-sm-table-cell">{{ $pembayarans->firstItem() + $i }}</td>
                            <td>
                                <strong>{{ $p->sewaRuko->penyewa->nama_usaha ?? '-' }}</strong>
                                @if($terlambat)
                                    <span class="badge badge-danger ml-1">Menunggak</span>
                                @endif
                            </td>
                            <td>{{ $p->sewaRuko->ruko->kode_unit ?? '-' }}</td>
                            <td>Termin {{ $p->termin }}</td>
                            <td class="d-none d-md-table-cell">
                                {{ $p->tgl_jatuh_tempo
                                    ? \Carbon\Carbon::parse($p->tgl_jatuh_tempo)->format('d M Y')
                                    : '-' }}
                            </td>
                            <td>Rp {{ number_format($p->jumlah_tagihan, 0, ',', '.') }}</td>
                            <td class="d-none d-lg-table-cell">
                                {{ $p->tgl_bayar
                                    ? \Carbon\Carbon::parse($p->tgl_bayar)->format('d M Y')
                                    : '-' }}
                            </td>
                            <td>
                                @if($p->status === 'lunas')
                                    <span class="badge badge-success">Lunas</span>
                                @elseif($p->status === 'verifikasi')
                                    <span class="badge badge-info">Verifikasi</span>
                                @elseif($p->status === 'menunggu')
                                    <span class="badge badge-warning">Menunggu</span>
                                @else
                                    <span class="badge badge-danger">Dibatalkan</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('adminkantin.pembayaran.show', $p) }}"
                                    class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($p->status === 'verifikasi')
                                    <a href="{{ route('adminkantin.pembayaran.kwitansi', $p) }}"
                                        class="btn btn-success btn-sm">
                                        <i class="fas fa-download"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="text-center">Belum ada data pembayaran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end">
                {{ $pembayarans->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
