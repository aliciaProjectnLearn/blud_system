@extends('layouts.app')

@section('title', 'Detail Pembayaran Futsal')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4 d-print-none">
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
        {{-- Kolom Kiri: Info Pembayaran --}}
        <div class="col-lg-8 print-full-width">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        @if($jenisBooking === 'membership')
                            <i class="fas fa-id-card mr-1"></i> Informasi Pembelian Paket Membership
                        @else
                            <i class="fas fa-receipt mr-1"></i> Informasi Pembayaran & Booking
                        @endif
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="30%"><strong>Kode Transaksi</strong></td>
                            <td width="5%">:</td>
                            <td>{{ $pembayaran->kode_pembayaran }}</td>
                        </tr>

                        @if($jenisBooking === 'membership' && $membership)
                            {{-- INFO MEMBERSHIP --}}
                            <tr>
                                <td><strong>Nama Pemesan</strong></td>
                                <td>:</td>
                                <td>{{ $membership->user->nama_lengkap ?? ($membership->user->name ?? ($pembayaran->booking->user->name ?? '-')) }}</td>
                            </tr>
                            <tr>
                                <td><strong>No. WhatsApp</strong></td>
                                <td>:</td>
                                <td>{{ $membership->user->no_hp ?? ($pembayaran->booking->user->no_hp ?? '-') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Paket Dibeli</strong></td>
                                <td>:</td>
                                <td>
                                    <strong class="text-primary">{{ $membership->paket->nama_paket ?? '-' }}</strong>
                                    <span class="badge badge-info ml-1">{{ $membership->paket->jumlah_kuota ?? 0 }} Jam Kuota</span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status Membership</strong></td>
                                <td>:</td>
                                <td>
                                    @if(($membership->status ?? '') === 'aktif')
                                        <span class="badge badge-success">Aktif</span>
                                    @elseif(($membership->status ?? '') === 'menunggu')
                                        <span class="badge badge-warning">Menunggu Verifikasi</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst($membership->status ?? '-') }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Tipe Transaksi</strong></td>
                                <td>:</td>
                                <td><span class="badge badge-info">Pembelian Paket</span></td>
                            </tr>
                        @else
                            {{-- INFO BOOKING REGULER / EVENT --}}
                            <tr>
                                <td><strong>Lapangan</strong></td>
                                <td>:</td>
                                <td>{{ $booking->lapangan->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Waktu Main</strong></td>
                                <td>:</td>
                                <td>
                                    @if($booking)
                                        @if($jenisBooking === 'event')
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
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Pemesan</strong></td>
                                <td>:</td>
                                <td>{{ $booking->nama_pemesan ?? ($booking->user->name ?? '-') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tipe Booking</strong></td>
                                <td>:</td>
                                <td>
                                    @php
                                        $labelTipeBooking = match($jenisBooking) {
                                            'event'  => 'Booking Event',
                                            'paket'  => 'Paket Membership',
                                            default  => 'Reguler',
                                        };
                                        $badgeTipeBooking = match($jenisBooking) {
                                            'event'  => 'badge-warning',
                                            'paket'  => 'badge-info',
                                            default  => 'badge-primary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeTipeBooking }}">{{ $labelTipeBooking }}</span>
                                </td>
                            </tr>
                        @endif

                        @if($pembayaran->tipePembayaran ?? null)
                        <tr>
                            <td><strong>Metode Pembayaran</strong></td>
                            <td>:</td>
                            <td><strong>{{ $pembayaran->tipePembayaran->nama }}</strong></td>
                        </tr>
                        @endif
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
                                    <span class="badge badge-danger">Ditolak</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Diproses</strong></td>
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

        {{-- Kolom Kanan: Aksi Kasir --}}
        <div class="col-lg-4 d-print-none">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aksi Kasir</h6>
                </div>
                <div class="card-body text-center">
                    @if ($pembayaran->status == \App\Models\PembayaranFutsal::STATUS_MENUNGGU)
                    <form action="{{ route('kasirfutsal.pembayaran.proses', $pembayaran->id) }}"
                          method="POST" id="form-proses">
                        @csrf

                        @if($jenisBooking === 'membership')
                            {{-- Membership: Metode sudah jelas QRIS, tidak perlu pilih --}}
                            <input type="hidden" name="tipe_pembayaran_id" value="{{ $metodeDariBooking ?? 3 }}">
                            <div class="alert alert-info py-2 mb-3 text-left">
                                <i class="fas fa-id-card mr-1"></i>
                                <strong>Pembelian Paket Membership</strong><br>
                                <small>Verifikasi bukti QRIS di bawah, lalu proses untuk mengaktifkan paket.</small>
                            </div>
                        @else
                            <div class="form-group text-left">
                                <label><strong>Metode Pembayaran</strong></label>

                                @if($metodeDariBooking && $namaMetode)
                                    {{-- Metode sudah diketahui dari pilihan user saat booking --}}
                                    <div class="alert alert-info py-2 mb-2">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        User memilih: <strong>{{ $namaMetode }}</strong>
                                    </div>
                                    <input type="hidden" name="tipe_pembayaran_id" value="{{ $metodeDariBooking }}">

                                    {{-- Kasir tetap bisa override jika perlu --}}
                                    <div class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input"
                                               id="override-metode"
                                               onchange="toggleOverride(this)">
                                        <label class="custom-control-label small text-muted" for="override-metode">
                                            Ganti metode pembayaran
                                        </label>
                                    </div>

                                    <select name="tipe_pembayaran_id_override"
                                            id="select-override"
                                            class="form-control"
                                            style="display:none;">
                                        <option value="">-- Pilih Metode Lain --</option>
                                        @foreach($tipePembayaran as $tipe)
                                            @php
                                                $namaLower = strtolower($tipe->nama);
                                                if ($jenisBooking === 'event') {
                                                    $boleh = str_contains($namaLower, 'tunai') || str_contains($namaLower, 'qris');
                                                    if (!$boleh) continue;
                                                }
                                            @endphp
                                            <option value="{{ $tipe->id }}"
                                                    {{ $tipe->id == $metodeDariBooking ? 'selected' : '' }}>
                                                {{ $tipe->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    {{-- Metode belum diketahui, kasir pilih manual --}}
                                    <select name="tipe_pembayaran_id" id="tipe_pembayaran_id"
                                            class="form-control" required>
                                        <option value="">-- Pilih Metode --</option>
                                        @foreach($tipePembayaran as $tipe)
                                            @php
                                                $namaLower = strtolower($tipe->nama);
                                                if ($jenisBooking === 'event') {
                                                    $boleh = str_contains($namaLower, 'tunai') || str_contains($namaLower, 'qris');
                                                    if (!$boleh) continue;
                                                }
                                            @endphp
                                            <option value="{{ $tipe->id }}">{{ $tipe->nama }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                        @endif

                        @php
                            // Deteksi apakah metode aktif adalah QRIS (termasuk untuk membership)
                            $isQris = ($jenisBooking === 'membership') || ($namaMetode && str_contains(strtolower($namaMetode), 'qris'));
                        @endphp

                        @if($isQris)
                        <div id="blok-verifikasi-qris" class="text-left mb-3">
                            <hr class="my-2">
                            <p class="font-weight-bold text-dark mb-2">
                                <i class="fas fa-qrcode mr-1 text-primary"></i>
                                Verifikasi Bukti QRIS
                            </p>

                            @if($pembayaran->bukti)
                                {{-- Bukti sudah diupload user --}}
                                <div class="text-center mb-2">
                                    <a href="{{ asset('storage/' . $pembayaran->bukti) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $pembayaran->bukti) }}"
                                             class="img-fluid rounded border"
                                             style="max-height:180px; cursor:zoom-in;"
                                             alt="Bukti QRIS">
                                    </a>
                                    <div class="small text-muted mt-1">
                                        <i class="fas fa-search-plus mr-1"></i>Klik gambar untuk perbesar
                                    </div>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input"
                                           id="cek-bukti-qris"
                                           onchange="toggleProsesBukti(this)">
                                    <label class="custom-control-label text-dark" for="cek-bukti-qris">
                                        Saya sudah mengecek bukti pembayaran QRIS dan sesuai
                                    </label>
                                </div>
                            @else
                                {{-- Bukti belum diupload --}}
                                <div class="alert alert-warning py-2 mb-2">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    <strong>Bukti QRIS belum diupload.</strong><br>
                                    <small>Minta pelanggan mengirimkan foto bukti transfer QRIS.</small>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input"
                                           id="cek-bukti-qris"
                                           onchange="toggleProsesBukti(this)">
                                    <label class="custom-control-label small text-muted" for="cek-bukti-qris">
                                        Saya konfirmasi pembayaran QRIS sudah diterima secara manual
                                    </label>
                                </div>
                            @endif
                        </div>
                        @endif

                        <button type="button"
                                id="btn-proses-pembayaran"
                                class="btn btn-success btn-block mt-3"
                                onclick="prosesPembayaran()"
                                {{ $isQris ? 'disabled' : '' }}>
                            <i class="fas fa-cash-register"></i>
                            {{ $jenisBooking === 'membership' ? 'Verifikasi & Aktifkan Paket' : 'Proses Pembayaran' }}
                        </button>

                        <hr class="my-3">

                        {{-- Tombol Tolak --}}
                        <button type="button"
                                class="btn btn-outline-danger btn-block"
                                data-toggle="modal"
                                data-target="#modalTolak">
                            <i class="fas fa-times-circle mr-1"></i> Tolak Pembayaran
                        </button>
                    </form>
                    @elseif ($pembayaran->status == \App\Models\PembayaranFutsal::STATUS_VERIFIKASI)
                        <p class="text-success"><i class="fas fa-check-circle fa-2x mb-2"></i><br>
                            @if($jenisBooking === 'membership')
                                Paket membership telah aktif.
                            @else
                                Pembayaran telah lunas.
                            @endif
                        </p>
                        @if($jenisBooking !== 'membership')
                        <button class="btn btn-primary btn-block mt-3" onclick="window.print()">
                            <i class="fas fa-print"></i> Cetak Invoice
                        </button>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tolak Pembayaran --}}
@if ($pembayaran->status == \App\Models\PembayaranFutsal::STATUS_MENUNGGU)
<div class="modal fade" id="modalTolak" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-times-circle mr-2"></i>Tolak Pembayaran</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('kasirfutsal.pembayaran.tolak', $pembayaran->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        <strong>Perhatian!</strong> Tindakan ini akan <strong>membatalkan</strong> pembayaran
                        @if($jenisBooking === 'membership')
                            dan menolak aktivasi paket membership.
                        @else
                            dan membatalkan booking.
                        @endif
                        Pemesan akan mendapatkan notifikasi WhatsApp.
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Alasan Penolakan <span class="text-muted small">(opsional)</span></label>
                        <input type="text" name="alasan_tolak" class="form-control"
                               placeholder="contoh: Bukti pembayaran tidak sesuai nominal">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-arrow-left mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times-circle mr-1"></i> Ya, Tolak Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
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
        var hiddenInput = document.querySelector('#form-proses input[name="tipe_pembayaran_id"]');
        var selectManual = document.getElementById('tipe_pembayaran_id');

        if (selectManual && !hiddenInput) {
            if (!selectManual.value) {
                Swal.fire('Peringatan', 'Harap pilih metode pembayaran terlebih dahulu!', 'warning');
                return;
            }
        }

        var cekBukti = document.getElementById('cek-bukti-qris');
        if (cekBukti && !cekBukti.checked) {
            Swal.fire('Peringatan', 'Harap centang konfirmasi bukti QRIS terlebih dahulu!', 'warning');
            return;
        }

        var titleText = '{{ $jenisBooking === "membership" ? "Aktifkan Paket Membership?" : "Proses Pembayaran?" }}';
        var bodyText  = '{{ $jenisBooking === "membership" ? "Pastikan bukti QRIS sudah sesuai. Paket akan langsung aktif." : "Pastikan pembayaran sudah diterima sesuai nominal tagihan." }}';

        Swal.fire({
            title: titleText,
            text: bodyText,
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

    function toggleProsesBukti(checkbox) {
        var btn = document.getElementById('btn-proses-pembayaran');
        if (btn) btn.disabled = !checkbox.checked;
    }

    function toggleOverride(checkbox) {
        const select = document.getElementById('select-override');
        const hiddenInput = document.querySelector('input[name="tipe_pembayaran_id"]');

        if (checkbox.checked) {
            select.style.display = 'block';
            if (hiddenInput) hiddenInput.removeAttribute('name');
            select.setAttribute('name', 'tipe_pembayaran_id');
        } else {
            select.style.display = 'none';
            if (hiddenInput) hiddenInput.setAttribute('name', 'tipe_pembayaran_id');
            select.setAttribute('name', 'tipe_pembayaran_id_override');
        }
    }
</script>
@endpush

@push('styles')
<style>
    @media print {
        body { background-color: #fff !important; }
        .sidebar, .navbar, footer, .btn, .d-print-none { display: none !important; }
        #content-wrapper { background-color: #fff !important; margin: 0 !important; padding: 0 !important; width: 100% !important; }
        .print-full-width {
            flex: 0 0 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
        }
        .card { border: 1px solid #000 !important; box-shadow: none !important; }
        .card-header { background-color: #f8f9fc !important; border-bottom: 1px solid #000 !important; -webkit-print-color-adjust: exact; }
        table td { color: #000 !important; }
        .badge { border: 1px solid #000 !important; color: #000 !important; }
    }
</style>
@endpush
