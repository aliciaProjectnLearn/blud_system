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

    <div class="row">
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
                                @elseif($transaksi->jenis_transaksi == 'event')
                                    <span class="badge badge-warning shadow-sm text-dark"><i class="fas fa-calendar-alt"></i> Event</span>
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
                        
                        {{-- TAMBAHAN BARU: Informasi Jadwal --}}
                        <tr>
                            <td><strong>Jadwal Main</strong></td>
                            <td>:</td>
                            <td>
                                @if($transaksi->booking && $transaksi->booking->bookingFutsal)
                                    @php $bf = $transaksi->booking->bookingFutsal; @endphp
                                    
                                    @if($bf->type === 'event')
                                        <span class="text-warning font-weight-bold">
                                            {{ \Carbon\Carbon::parse($bf->start_datetime)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($bf->end_datetime)->format('d M Y') }}
                                        </span>
                                        <br><small class="text-muted">(Multi-hari / Full Day)</small>
                                    @else
                                        {{ \Carbon\Carbon::parse($bf->start_datetime)->format('d M Y') }}<br>
                                        <small class="text-muted">
                                            <i class="fas fa-clock"></i> Jam: {{ \Carbon\Carbon::parse($bf->start_datetime)->format('H:i') }} - {{ \Carbon\Carbon::parse($bf->end_datetime)->format('H:i') }} WIB
                                        </small>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td><strong>Total Pembayaran</strong></td>
                            <td>:</td>
                            <td class="text-success" style="font-size: 1.1em;">
                                <strong>Rp {{ number_format($transaksi->jumlah_bayar, 0, ',', '.') }}</strong>
                            </td>
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

                    {{-- Form Konfirmasi (hanya muncul jika status menunggu) --}}
                    @if($transaksi->status == 'menunggu')
                        <hr>
                        <form action="{{ route('adminfutsal.transaksi.konfirmasi', $transaksi->id) }}" method="POST" 
                            onsubmit="return confirm('Apakah Anda yakin ingin memverifikasi pembayaran ini?');">
                            @csrf
                            @method('PATCH')
                            
                            @if($transaksi->jenis_transaksi !== 'membership')
                                <div class="form-group">
                                    <label for="jumlah_bayar"><strong>Nominal Pembayaran (Rp)</strong></label>
                                    <input type="number" name="jumlah_bayar" id="jumlah_bayar"
                                        class="form-control" placeholder="Masukkan Nominal Pembayaran" min="1" 
                                        value="{{ $transaksi->jumlah_bayar > 0 ? $transaksi->jumlah_bayar : '' }}"
                                        {{ $transaksi->jenis_transaksi == 'event' ? 'readonly' : 'required' }}>
                                    
                                    @if($transaksi->jenis_transaksi == 'event')
                                        <small class="text-warning font-weight-bold mt-1 d-block">
                                            <i class="fas fa-lock"></i> Harga event sudah dikunci sistem.
                                        </small>
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> 
                                    Harga membership akan otomatis diambil dari paket yang dipilih pelanggan.
                                </div>
                            @endif

                            <button type="submit" class="btn btn-success btn-block py-2">
                                <i class="fas fa-check-circle"></i> Konfirmasi Pembayaran Berhasil
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bukti Pembayaran</h6>
                </div>
                <div class="card-body text-center">
                    @if($transaksi->bukti)
                        <img src="{{ asset('storage/' . $transaksi->bukti) }}" alt="Bukti Pembayaran"
                            class="img-fluid rounded border p-1 mb-3"
                            style="max-height: 400px; object-fit: contain; width: 100%;">
                        <div>
                            <a href="{{ asset('storage/' . $transaksi->bukti) }}" target="_blank"
                                class="btn btn-sm btn-outline-primary">Lihat Gambar Penuh</a>
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