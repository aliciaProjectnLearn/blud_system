@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Transaksi</h1>
        <a href="{{ route('adminfutsal.transaksi.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Informasi Detail -->
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Pembayaran</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="30%"><strong>Kode Pembayaran</strong></td>
                            <td width="5%">:</td>
                            <td><strong class="text-primary">{{ $transaksi->kode_pembayaran }}</strong></td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Transaksi</strong></td>
                            <td>:</td>
                            <td>{{ \Carbon\Carbon::parse($transaksi->created_at)->format('d F Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Jenis Transaksi</strong></td>
                            <td>:</td>
                            <td>
                                @if($transaksi->jenis_transaksi == 'membership')
                                    <span class="badge badge-info shadow-sm"><i class="fas fa-id-card"></i> Membership</span>
                                @elseif($transaksi->jenis_transaksi == 'guest')
                                    <span class="badge badge-secondary shadow-sm">Guest</span>
                                @else
                                    <span class="badge badge-primary shadow-sm"><i class="fas fa-calendar-check"></i> Booking</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Nama Pelanggan</strong></td>
                            <td>:</td>
                            <td>{{ $transaksi->booking->user->name ?? 'Guest/Unknown' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Nama Lapangan</strong></td>
                            <td>:</td>
                            <td>{{ $transaksi->booking->bookingFutsal->lapangan->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Total Pembayaran</strong></td>
                            <td>:</td>
                            <td class="text-success" style="font-size: 1.1em;"><strong>Rp {{ number_format($transaksi->jumlah_bayar, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td><strong>Status</strong></td>
                            <td>:</td>
                            <td>
                                @if($transaksi->status == 'verifikasi')
                                    <span class="badge badge-primary">Verifikasi (Berhasil)</span>
                                @elseif($transaksi->status == 'menunggu')
                                    <span class="badge badge-warning text-dark">Menunggu</span>
                                @elseif($transaksi->status == 'dibatalkan')
                                    <span class="badge badge-danger">Dibatalkan</span>
                                @else
                                    <span class="badge badge-success">{{ ucfirst($transaksi->status) }}</span>
                                @endif
                            </td>
                        </tr>
                    </table>

                    @if($transaksi->status == 'menunggu')
                        <hr>
                        <form action="{{ route('adminfutsal.transaksi.konfirmasi', $transaksi->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memverifikasi pembayaran ini?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success btn-block py-2">
                                <i class="fas fa-check-circle"></i> Konfirmasi Pembayaran Berhasil
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Bukti Pembayaran -->
        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bukti Pembayaran</h6>
                </div>
                <div class="card-body text-center">
                    @if($transaksi->bukti)
                        <img src="{{ asset('storage/' . $transaksi->bukti) }}" alt="Bukti Pembayaran" class="img-fluid rounded border p-1 mb-3" style="max-height: 400px; object-fit: contain; width: 100%;">
                        <div>
                            <a href="{{ asset('storage/' . $transaksi->bukti) }}" target="_blank" class="btn btn-sm btn-outline-primary">Lihat Gambar Penuh</a>
                        </div>
                    @else
                        <div class="py-5 text-muted">
                            <i class="fas fa-images fa-4x mb-3 text-gray-300"></i>
                            <p>Belum ada bukti pembayaran yang diunggah.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
