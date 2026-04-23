@extends('layouts.app')

@section('title', 'Detail Transaksi #' . $transaksi->id)

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb & Back -->
    <div class="mb-4">
        <a href="{{ route('adminservis.transaksi.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Transaksi #{{ $transaksi->id }}</h1>
        <div class="badge badge-light border shadow-sm px-3 py-2">
            <span class="text-muted small text-uppercase font-weight-bold mr-1">ID Pembayaran:</span>
            <span class="text-primary font-weight-bold">{{ $transaksi->kode_pembayaran }}</span>
        </div>
    </div>

    <div class="row">
        <!-- Left Side: Transaction & Payment Info -->
        <div class="col-lg-8">
            <!-- Section 1: Data Pelanggan -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-circle mr-2"></i>Informasi Pelanggan</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-xs font-weight-bold text-muted text-uppercase mb-1 d-block">Nama Lengkap</label>
                            <div class="h6 font-weight-bold text-gray-800">{{ $transaksi->bookingServis->pelanggan->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-xs font-weight-bold text-muted text-uppercase mb-1 d-block">Email</label>
                            <div class="text-gray-800">{{ $transaksi->bookingServis->pelanggan->email ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-xs font-weight-bold text-muted text-uppercase mb-1 d-block">No. Telepon / WhatsApp</label>
                            <div class="text-gray-800">{{ $transaksi->bookingServis->pelanggan->no_hp ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-xs font-weight-bold text-muted text-uppercase mb-1 d-block">Alamat</label>
                            <div class="text-gray-800">{{ $transaksi->bookingServis->pelanggan->alamat ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Detail Booking & Kendaraan -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                    <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-motorcycle mr-2"></i>Informasi Booking & Kendaraan</h6>
                    <span class="badge badge-light border">{{ $transaksi->bookingServis->kode_booking }}</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="text-xs font-weight-bold text-muted text-uppercase mb-1 d-block">Tanggal Booking</label>
                            <div class="text-gray-800 font-weight-bold">{{ $transaksi->bookingServis->tanggal_booking ? \Carbon\Carbon::parse($transaksi->bookingServis->tanggal_booking)->translatedFormat('d F Y') : '-' }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-xs font-weight-bold text-muted text-uppercase mb-1 d-block">Jam Booking</label>
                            <div class="text-gray-800">{{ $transaksi->bookingServis->jam_booking ?? '-' }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-xs font-weight-bold text-muted text-uppercase mb-1 d-block">Tipe Layanan</label>
                            <div class="badge badge-primary px-2 py-1">{{ $transaksi->bookingServis->layananServis->nama_layanan ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-xs font-weight-bold text-muted text-uppercase mb-1 d-block">Kendaraan</label>
                            <div class="h6 font-weight-bold text-gray-800 text-uppercase">
                                {{ $transaksi->bookingServis->merek_kendaraan }} {{ $transaksi->bookingServis->tipe_kendaraan }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-xs font-weight-bold text-muted text-uppercase mb-1 d-block">Nomor Plat</label>
                            <div class="badge badge-dark p-2 text-uppercase font-weight-bold" style="letter-spacing: 1px;">{{ $transaksi->bookingServis->nomor_plat }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-xs font-weight-bold text-muted text-uppercase mb-1 d-block">Keluhan</label>
                            <div class="small italic text-muted">"{{ $transaksi->bookingServis->keluhan ?? '-' }}"</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Rincian Servis -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white border-bottom-0">
                    <h6 class="m-0 font-weight-bold text-warning"><i class="fas fa-list-ul mr-2"></i>Rincian Servis & Suku Cadang</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-xs font-weight-bold text-uppercase border-0">Item / Jasa</th>
                                    <th class="px-4 py-2 text-xs font-weight-bold text-uppercase border-0 text-right" width="150">Harga Satuan</th>
                                    <th class="px-4 py-2 text-xs font-weight-bold text-uppercase border-0 text-center" width="100">Jumlah</th>
                                    <th class="px-4 py-2 text-xs font-weight-bold text-uppercase border-0 text-right" width="150">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transaksi->bookingServis->rincianServis as $rincian)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="font-weight-bold text-gray-800">{{ $rincian->nama_item }}</div>
                                            <div class="text-xs text-muted">{{ $rincian->produkServis->nama_produk ?? 'Jasa Servis' }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-right">Rp {{ number_format($rincian->harga_satuan, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-center">{{ $rincian->jumlah }}</td>
                                        <td class="px-4 py-3 text-right font-weight-bold text-gray-800">Rp {{ number_format($rincian->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted small">Tidak ada rincian item untuk transaksi ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="3" class="text-right font-weight-bold px-4 py-3 text-gray-800">Total Biaya Keseluruhan</td>
                                    <td class="text-right font-weight-bold text-primary px-4 py-3 h5 mb-0">
                                        Rp {{ number_format($transaksi->total_biaya, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Payment Info & Proof -->
        <div class="col-lg-4">
            <!-- Payment Info -->
            <div class="card shadow mb-4 border-left-success">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-success"><i class="fas fa-file-invoice-dollar mr-2"></i>Status Pembayaran</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <label class="text-xs font-weight-bold text-muted text-uppercase d-block mb-2">Kondisi Saat Ini</label>
                        @php
                            $statusClass = 'secondary';
                            $statusLabel = $transaksi->status_pembayaran;
                            
                            if (in_array($transaksi->status_pembayaran, ['belum_bayar', 'dp'])) {
                                $statusClass = 'warning';
                                $statusLabel = 'Pending';
                            } elseif ($transaksi->status_pembayaran == 'lunas') {
                                $statusClass = 'success';
                                $statusLabel = 'Dibayar';
                            }
                        @endphp
                        <div class="h4">
                            <span class="badge badge-{{ $statusClass }} px-4 py-2 text-uppercase shadow-sm">
                                {{ $statusLabel }}
                            </span>
                        </div>
                    </div>
                    
                    <hr class="my-4">

                    <div class="mb-3">
                        <label class="text-xs font-weight-bold text-muted text-uppercase mb-1 d-block">Metode Pembayaran</label>
                        <div class="font-weight-bold text-primary text-uppercase">{{ $transaksi->tipe_pembayaran }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="text-xs font-weight-bold text-muted text-uppercase mb-1 d-block">Tanggal Pelunasan</label>
                        <div class="text-gray-800 font-weight-bold">
                            {{ $transaksi->tanggal_bayar ? \Carbon\Carbon::parse($transaksi->tanggal_bayar)->translatedFormat('d F Y, H:i') : '-' }}
                        </div>
                    </div>

                    @if ($transaksi->jumlah_dp > 0)
                        <div class="mb-3">
                            <label class="text-xs font-weight-bold text-muted text-uppercase mb-1 d-block">Uang Muka (DP)</label>
                            <div class="text-warning font-weight-bold">Rp {{ number_format($transaksi->jumlah_dp, 0, ',', '.') }}</div>
                        </div>
                    @endif

                    @if ($transaksi->catatan)
                        <div class="mb-0">
                            <label class="text-xs font-weight-bold text-muted text-uppercase mb-1 d-block">Catatan Internal / Kasir</label>
                            <div class="p-2 bg-light rounded small text-gray-700 italic">
                                "{{ $transaksi->catatan }}"
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment Proof -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-secondary"><i class="fas fa-image mr-2"></i>Bukti Pembayaran</h6>
                </div>
                <div class="card-body text-center">
                    @if ($transaksi->path_bukti_bayar)
                        <img src="{{ asset('storage/' . $transaksi->path_bukti_bayar) }}" class="img-fluid rounded border shadow-sm mb-3" alt="Bukti Pembayaran">
                        <a href="{{ asset('storage/' . $transaksi->path_bukti_bayar) }}" target="_blank" class="btn btn-outline-secondary btn-sm btn-block">
                            <i class="fas fa-external-link-alt fa-sm mr-1"></i> Lihat Ukuran Penuh
                        </a>
                    @else
                        <div class="py-5 text-muted opacity-25">
                            <i class="fas fa-image-slash fa-4x mb-3"></i>
                            <p class="mb-0 small font-weight-bold">Tidak ada bukti pembayaran tersedia</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .text-xs { font-size: 0.75rem; }
    .opacity-25 { opacity: 0.25; }
    .italic { font-style: italic; }
</style>
@endpush
