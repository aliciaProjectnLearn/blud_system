@extends('layouts.app')

@section('title', 'Detail Booking Futsal')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Booking Futsal</h1>
        <a href="{{ route('kasirfutsal.booking.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success d-none" id="flash-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger d-none" id="flash-error">{{ session('error') }}</div>
    @endif

    @php
        // Akses pembayaran lewat jalur: BookingFutsal -> Booking -> PembayaranFutsal (hasMany, ambil first)
        $pembayaran = $booking->booking->pembayaranFutsal->first() ?? null;
        $namaMetode = $pembayaran?->tipePembayaran?->nama ?? null;
    @endphp

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Booking</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="30%"><strong>Lapangan</strong></td>
                            <td width="5%">:</td>
                            <td>{{ $booking->lapangan->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Waktu Main</strong></td>
                            <td>:</td>
                            <td>
                                @if($booking->jenis_pembayaran === 'event')
                                    <span class="font-weight-bold">
                                        {{ \Carbon\Carbon::parse($booking->start_datetime)->locale('id')->translatedFormat('d F Y') }}
                                    </span>
                                    <span class="text-muted mx-1">s/d</span>
                                    <span class="font-weight-bold">
                                        {{ \Carbon\Carbon::parse($booking->end_datetime)->locale('id')->translatedFormat('d F Y') }}
                                    </span>
                                @else
                                    {{ \Carbon\Carbon::parse($booking->start_datetime)->locale('id')->translatedFormat('l, d F Y') }}
                                    <span class="text-muted ml-1">
                                        {{ \Carbon\Carbon::parse($booking->start_datetime)->format('H:i') }}
                                        –
                                        {{ \Carbon\Carbon::parse($booking->end_datetime)->format('H:i') }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Pemesan</strong></td>
                            <td>:</td>
                            <td>{{ $booking->nama_pemesan ?? ($booking->user->name ?? '-') }}</td>
                        </tr>
                        <tr>
                            <td><strong>No. HP</strong></td>
                            <td>:</td>
                            <td>{{ $booking->no_hp ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tipe Booking</strong></td>
                            <td>:</td>
                            <td>
                                @php
                                    $labelTipe = match($booking->jenis_pembayaran) {
                                        'event'  => 'Booking Event',
                                        'paket'  => 'Paket Membership',
                                        default  => 'Reguler',
                                    };
                                    $badgeTipe = match($booking->jenis_pembayaran) {
                                        'event'  => 'badge-warning',
                                        'paket'  => 'badge-info',
                                        default  => 'badge-primary',
                                    };
                                @endphp
                                <span class="badge {{ $badgeTipe }}">{{ $labelTipe }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Metode Pembayaran</strong></td>
                            <td>:</td>
                            <td>
                                @if($namaMetode)
                                    @php
                                        // Normalisasi nama metode: ganti "Paket/Membership" → "Paket Membership"
                                        $namaMetodeBersih = str_replace('/', ' ', $namaMetode);
                                        // Override khusus untuk jenis paket
                                        if ($booking->jenis_pembayaran === 'paket') {
                                            $namaMetodeBersih = 'Paket Membership';
                                        }
                                    @endphp
                                    <strong>{{ $namaMetodeBersih }}</strong>
                                @else
                                    <span class="text-muted small"><i class="fas fa-clock mr-1"></i>Belum diproses kasir</span>
                                @endif
                            </td>
                        </tr>
                        @if($pembayaran)
                        <tr>
                            <td><strong>Total Tagihan</strong></td>
                            <td>:</td>
                            <td class="text-danger font-weight-bold">
                                Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Status Pembayaran</strong></td>
                            <td>:</td>
                            <td>
                                @if($pembayaran->status == \App\Models\PembayaranFutsal::STATUS_VERIFIKASI)
                                    <span class="badge badge-success">Lunas</span>
                                @elseif($pembayaran->status == \App\Models\PembayaranFutsal::STATUS_MENUNGGU)
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
                        @endif
                        <tr>
                            <td><strong>Status Booking</strong></td>
                            <td>:</td>
                            <td>
                                @if ($booking->status == 'menunggu')
                                    <span class="badge badge-warning">Menunggu</span>
                                @elseif ($booking->status == 'dikonfirmasi')
                                    <span class="badge badge-primary">Dikonfirmasi</span>
                                @elseif ($booking->status == 'selesai')
                                    <span class="badge badge-success">Selesai</span>
                                @else
                                    <span class="badge badge-danger">Dibatalkan</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Catatan</strong></td>
                            <td>:</td>
                            <td>{{ $booking->catatan ?? '-' }}</td>
                        </tr>
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
                    @if ($booking->status == 'menunggu')
                        <p>Status masih menunggu. Pastikan DP atau syarat lain sudah terpenuhi sebelum konfirmasi.</p>
                        <form action="{{ route('kasirfutsal.booking.konfirmasi', $booking->id) }}" method="POST" id="form-konfirmasi">
                            @csrf
                            <button type="button" class="btn btn-primary btn-block" onclick="konfirmasiBooking()">
                                <i class="fas fa-check"></i> Konfirmasi Booking
                            </button>
                        </form>
                    @elseif ($booking->status == 'dikonfirmasi')
                        <p class="text-success"><i class="fas fa-check-circle"></i> Booking sudah dikonfirmasi.</p>
                        @if ($pembayaran)
                            <a href="{{ route('kasirfutsal.pembayaran.show', $pembayaran->id) }}" class="btn btn-success btn-block mt-3">
                                <i class="fas fa-cash-register"></i> Lihat Pembayaran
                            </a>
                        @endif
                    @elseif ($booking->status == 'selesai')
                        <p class="text-success"><i class="fas fa-check-circle fa-2x mb-2"></i><br>Booking selesai &amp; lunas.</p>
                        @if ($pembayaran)
                            <a href="{{ route('kasirfutsal.pembayaran.show', $pembayaran->id) }}" class="btn btn-primary btn-block mt-3">
                                <i class="fas fa-print"></i> Lihat / Cetak Invoice
                            </a>
                        @endif
                    @else
                        <p>Booking ini berstatus <strong>{{ ucfirst($booking->status) }}</strong>.</p>
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

    function konfirmasiBooking() {
        Swal.fire({
            title: 'Konfirmasi Booking?',
            text: "Apakah Anda yakin ingin mengkonfirmasi booking ini?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Konfirmasi!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-konfirmasi').submit();
            }
        });
    }
</script>
@endpush
