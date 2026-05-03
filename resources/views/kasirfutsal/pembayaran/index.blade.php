@extends('layouts.app')

@section('title', 'Daftar Pembayaran Futsal')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daftar Pembayaran Futsal</h1>
    </div>

    @if (session('success'))
        <div class="alert alert-success d-none" id="flash-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger d-none" id="flash-error">{{ session('error') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Pembayaran</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('kasirfutsal.pembayaran.index') }}" method="GET" class="form-inline">
                <div class="form-group mb-2 mr-3">
                    <label for="status" class="mr-2">Status:</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">Semua</option>
                        <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="belum lunas" {{ request('status') == 'belum lunas' ? 'selected' : '' }}>Belum Lunas</option>
                        <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Ditolak/Batal</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mb-2">Filter</button>
                <a href="{{ route('kasirfutsal.pembayaran.index') }}" class="btn btn-secondary mb-2 ml-2">Reset</a>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Pembayaran</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>No</th>
                            <th>Kode Transaksi</th>
                            <th>Tgl Diproses</th>
                            <th>Pemesan</th>
                            <th>Tipe & Metode Bayar</th>
                            <th>Total Bayar</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pembayarans as $key => $item)
                            <tr>
                                <td>{{ $pembayarans->firstItem() + $key }}</td>
                                <td>{{ $item->kode_pembayaran }}</td>
                                <td>{{ $item->tgl_bayar ? \Carbon\Carbon::parse($item->tgl_bayar)->format('d M Y H:i') : '-' }}</td>
                                <td>
                                    @if($item->jenis_transaksi === 'membership')
                                        {{ $item->membershipUser->user->nama_lengkap ?? ($item->membershipUser->user->name ?? '-') }}
                                    @else
                                        {{ $item->bookingFutsal->nama_pemesan ?? ($item->bookingFutsal->user->name ?? ($item->booking->user->name ?? '-')) }}
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $labelTrx = match($item->jenis_transaksi) {
                                            'event'      => 'Booking Event',
                                            'booking'    => 'Reguler',
                                            'paket'      => 'Paket',
                                            'membership' => 'Pembelian Paket',
                                            default      => ucfirst($item->jenis_transaksi ?? '-'),
                                        };
                                        $badgeTrx = match($item->jenis_transaksi) {
                                            'event'      => 'badge-warning',
                                            'membership' => 'badge-info',
                                            'paket'      => 'badge-info',
                                            default      => 'badge-primary',
                                        };
                                        $namaMetodeBayar = $item->tipePembayaran->nama ?? null;
                                    @endphp
                                    <span class="badge {{ $badgeTrx }}">{{ $labelTrx }}</span>
                                    @if($namaMetodeBayar)
                                        <div class="small text-muted mt-1">{{ $namaMetodeBayar }}</div>
                                    @endif
                                </td>
                                <td>Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                                <td>
                                    @if ($item->status == \App\Models\PembayaranFutsal::STATUS_VERIFIKASI)
                                        <span class="badge badge-success">Lunas</span>
                                    @elseif ($item->status == \App\Models\PembayaranFutsal::STATUS_MENUNGGU)
                                        <span class="badge badge-warning">Belum Lunas</span>
                                    @else
                                        <span class="badge badge-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('kasirfutsal.pembayaran.show', $item->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $pembayarans->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (document.getElementById('flash-success')) {
            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: document.getElementById('flash-success').innerText, showConfirmButton: false, timer: 3000 });
        }
        if (document.getElementById('flash-error')) {
            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: document.getElementById('flash-error').innerText, showConfirmButton: false, timer: 3000 });
        }
    });
</script>
@endpush
