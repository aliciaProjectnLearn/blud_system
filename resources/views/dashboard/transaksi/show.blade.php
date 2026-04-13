@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Detail Transaksi</h4>

        <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Transaksi</h6>
                    @if($detail->sistem == 'AC')
                        <span class="badge badge-primary">AC</span>
                    @elseif($detail->sistem == 'Futsal')
                        <span class="badge badge-success">Futsal</span>
                    @else
                        <span class="badge badge-warning">Ruko</span>
                    @endif
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="35%" class="text-muted">ID Transaksi</td>
                            <td><strong>#{{ $detail->id }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Sistem</td>
                            <td>{{ $detail->sistem }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">
                                @if($detail->sistem == 'Futsal') Lapangan
                                @elseif($detail->sistem == 'AC') Layanan
                                @else Unit Ruko
                                @endif
                            </td>
                            <td>{{ $detail->nama_item ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Total</td>
                            <td><strong>Rp {{ number_format($detail->total ?? $detail->total_harga ?? $detail->jumlah_bayar ?? $detail->jumlah_tagihan, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                @if($detail->status == 'menunggu')
                                    <span class="badge badge-secondary">Menunggu</span>
                                @elseif($detail->status == 'verifikasi' || $detail->status == 'lunas')
                                    <span class="badge badge-success">{{ ucfirst($detail->status) }}</span>
                                @else
                                    <span class="badge badge-danger">{{ ucfirst($detail->status) }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Bayar</td>
                            <td>{{ $detail->tgl_bayar ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Dibuat</td>
                            <td>{{ \Carbon\Carbon::parse($detail->created_at)->format('d/m/Y H:i') }}</td>
                        </tr>

                        {{-- Info tambahan Futsal --}}
                        @if($detail->sistem == 'Futsal')
                        <tr><td colspan="2"><hr class="my-2"></td></tr>
                        <tr>
                            <td class="text-muted">Tanggal Booking</td>
                            <td>{{ $detail->tgl_booking ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jam</td>
                            <td>{{ $detail->jam_mulai ?? '-' }} – {{ $detail->jam_selesai ?? '-' }}</td>
                        </tr>
                        @endif

                        {{-- Info tambahan Ruko --}}
                        @if($detail->sistem == 'Ruko')
                        <tr><td colspan="2"><hr class="my-2"></td></tr>
                        <tr>
                            <td class="text-muted">Periode Sewa</td>
                            <td>{{ $detail->tgl_mulai ?? '-' }} – {{ $detail->tgl_selesai ?? '-' }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Pelanggan</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted" width="40%">Nama</td>
                            <td>{{ $detail->nama_pelanggan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Email</td>
                            <td>{{ $detail->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">No. HP</td>
                            <td>{{ $detail->no_hp ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
