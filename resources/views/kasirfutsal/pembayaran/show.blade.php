@extends('layouts.app')

@section('title', 'Detail Pembayaran Futsal')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Pembayaran Futsal</h1>
        <a href="{{ route('kasirfutsal.pembayaran.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success d-none" id="flash-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger d-none" id="flash-error">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Pembayaran & Booking</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="30%"><strong>Kode Transaksi</strong></td>
                            <td width="5%">:</td>
                            <td>{{ $pembayaran->kode_pembayaran }}</td>
                        </tr>
                        <tr>
                            <td><strong>Lapangan</strong></td>
                            <td>:</td>
                            <td>{{ $booking->lapangan->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Mulai</strong></td>
                            <td>:</td>
                            <td>{{ \Carbon\Carbon::parse($booking->start_datetime)->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Pemesan</strong></td>
                            <td>:</td>
                            <td>{{ $booking->nama_pemesan ?? ($booking->user->name ?? '-') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Jenis Transaksi</strong></td>
                            <td>:</td>
                            <td><span class="badge badge-info">{{ ucfirst($pembayaran->jenis_transaksi) }}</span></td>
                        </tr>
                        <tr>
                            <td><strong>Total Tagihan</strong></td>
                            <td>:</td>
                            <td class="text-danger font-weight-bold h5">Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status Pembayaran</strong></td>
                            <td>:</td>
                            <td>
                                @if ($pembayaran->status == \App\Models\PembayaranFutsal::STATUS_VERIFIKASI)
                                    <span class="badge badge-success">Lunas</span>
                                @elseif ($pembayaran->status == \App\Models\PembayaranFutsal::STATUS_MENUNGGU)
                                    <span class="badge badge-warning">Belum Lunas</span>
                                @else
                                    <span class="badge badge-danger">Batal</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Bayar</strong></td>
                            <td>:</td>
                            <td>{{ $pembayaran->tgl_bayar ? \Carbon\Carbon::parse($pembayaran->tgl_bayar)->format('d M Y H:i') : '-' }}</td>
                        </tr>
                        @if($pembayaran->bukti)
                        <tr>
                            <td><strong>Bukti Bayar</strong></td>
                            <td>:</td>
                            <td>
                                <a href="{{ asset('storage/' . $pembayaran->bukti) }}" target="_blank" class="btn btn-sm btn-info">
                                    <i class="fas fa-file-image"></i> Lihat Bukti
                                </a>
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aksi Kasir</h6>
                </div>
                <div class="card-body text-center">
                    @if ($pembayaran->status == \App\Models\PembayaranFutsal::STATUS_MENUNGGU)
                        <form action="{{ route('kasirfutsal.pembayaran.proses', $pembayaran->id) }}" method="POST" id="form-proses">
                            @csrf
                            <div class="form-group text-left">
                                <label for="tipe_pembayaran_id"><strong>Metode Pembayaran</strong></label>
                                <select name="tipe_pembayaran_id" id="tipe_pembayaran_id" class="form-control" required>
                                    <option value="">-- Pilih Metode --</option>
                                    @foreach($tipePembayaran as $tipe)
                                        <option value="{{ $tipe->id }}">{{ $tipe->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" class="btn btn-success btn-block mt-3" onclick="prosesPembayaran()">
                                <i class="fas fa-cash-register"></i> Proses Pembayaran
                            </button>
                        </form>
                    @elseif ($pembayaran->status == \App\Models\PembayaranFutsal::STATUS_VERIFIKASI)
                        <p class="text-success"><i class="fas fa-check-circle fa-2x mb-2"></i><br>Pembayaran telah lunas.</p>
                        <button class="btn btn-primary btn-block mt-3" onclick="window.print()">
                            <i class="fas fa-print"></i> Cetak Invoice
                        </button>
                    @endif
                </div>
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

    function prosesPembayaran() {
        var tipe = document.getElementById('tipe_pembayaran_id').value;
        if (!tipe) {
            Swal.fire('Peringatan', 'Harap pilih metode pembayaran terlebih dahulu!', 'warning');
            return;
        }

        Swal.fire({
            title: 'Proses Pembayaran?',
            text: "Pastikan uang/transfer sudah diterima sesuai nominal tagihan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Proses!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-proses').submit();
            }
        });
    }
</script>
@endpush
