@extends('layouts.app')

@section('title', 'Manajemen Transaksi')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Transaksi Futsal</h1>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Data Transaksi Pembayaran</h6>
            
            <!-- Filter Form -->
            <form method="GET" action="{{ route('adminfutsal.transaksi.index') }}" class="form-inline">
                <select name="jenis_transaksi" class="form-control form-control-sm mr-2">
                    <option value="">-- Semua Jenis --</option>
                    <option value="booking" {{ request('jenis_transaksi') == 'booking' ? 'selected' : '' }}>Booking</option>
                    <option value="membership" {{ request('jenis_transaksi') == 'membership' ? 'selected' : '' }}>Membership</option>
                    <option value="event" {{ request('jenis_transaksi') == 'event' ? 'selected' : '' }}>Event</option>
                    <option value="guest" {{ request('jenis_transaksi') == 'guest' ? 'selected' : '' }}>Guest</option>
                </select>

                <select name="status" class="form-control form-control-sm mr-2">
                    <option value="">-- Semua Status --</option>
                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="verifikasi" {{ request('status') == 'verifikasi' ? 'selected' : '' }}>Verifikasi</option>
                    <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
                
                <button type="submit" class="btn btn-sm btn-secondary"><i class="fas fa-filter"></i> Filter</button>
                @if(request()->filled('jenis_transaksi') || request()->filled('status'))
                    <a href="{{ route('adminfutsal.transaksi.index') }}" class="btn btn-sm btn-light ml-1">Reset</a>
                @endif
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Kode Pembayaran</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Jenis Transaksi</th>
                            <th>Total Harga</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksis as $index => $trx)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $trx->kode_pembayaran }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($trx->tgl_bayar ?? $trx->created_at)->format('d M Y H:i') }}</td>
                            <td>{{ $trx->booking->user->name ?? 'User Tidak Diketahui' }}</td>
                            <td>
                                @if($trx->jenis_transaksi == 'membership')
                                    <span class="badge badge-info shadow-sm"><i class="fas fa-id-card"></i> Membership</span>
                                @elseif($trx->jenis_transaksi == 'event')
                                    <span class="badge badge-warning shadow-sm"><i class="fas fa-calendar-check"></i> Event</span>
                                @elseif($trx->jenis_transaksi == 'guest')
                                    <span class="badge badge-secondary shadow-sm">Guest</span>
                                @else
                                    <span class="badge badge-primary shadow-sm"><i class="fas fa-calendar-check"></i> Booking</span>
                                @endif
                            </td>
                            <td>Rp {{ number_format($trx->jumlah_bayar, 0, ',', '.') }}</td>
                            <td>
                                @if($trx->status == 'verifikasi')
                                    <span class="badge badge-primary" style="font-size: 0.9em;">Verifikasi</span>
                                @elseif($trx->status == 'menunggu')
                                    <span class="badge badge-warning text-dark" style="font-size: 0.9em;">Menunggu</span>
                                @elseif($trx->status == 'dibatalkan')
                                    <span class="badge badge-danger" style="font-size: 0.9em;">Dibatalkan</span>
                                @else
                                    <span class="badge badge-success" style="font-size: 0.9em;">{{ ucfirst($trx->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('adminfutsal.transaksi.show', $trx->id) }}" class="btn btn-info btn-sm btn-circle" title="Detail Transaksi">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada data transaksi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
