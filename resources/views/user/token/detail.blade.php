@extends('layouts.publik')

@section('title', 'Detail Booking Futsal')

@section('content')
<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 py-2"><i class="fas fa-ticket-alt mr-2"></i> Detail Booking</h5>
                </div>
                <div class="card-body p-4">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    {{-- POIN 12: Keterangan menunggu konfirmasi admin --}}
                    @if($bookingFutsal->status === 'menunggu')
                        <div class="alert alert-warning d-flex align-items-start">
                            <i class="fas fa-clock fa-lg mr-3 mt-1"></i>
                            <div>
                                <strong>Menunggu Konfirmasi Admin/Kasir</strong><br>
                                Booking Anda masih menunggu konfirmasi admin/kasir sebelum dianggap sah. Anda akan mendapat notifikasi WhatsApp setelah dikonfirmasi.
                            </div>
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <strong>PENTING:</strong> Simpan link halaman ini untuk mengakses informasi booking Anda di kemudian hari.
                    </div>
                    
                    <div class="table-responsive mt-4">
                        <table class="table table-bordered">
                            <tr>
                                <th width="35%" class="bg-light">Kode Booking</th>
                                <td><strong>{{ $bookingFutsal->booking->kode_booking ?? 'BKG-'.$bookingFutsal->booking->id }}</strong></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Status</th>
                                <td>
                                    @if($bookingFutsal->status == 'menunggu')
                                        <span class="badge badge-warning p-2">Menunggu</span>
                                    @elseif($bookingFutsal->status == 'dikonfirmasi')
                                        <span class="badge badge-primary p-2">Dikonfirmasi</span>
                                    @elseif($bookingFutsal->status == 'selesai')
                                        <span class="badge badge-success p-2">Selesai</span>
                                    @elseif($bookingFutsal->status == 'dibatalkan')
                                        <span class="badge badge-danger p-2">Dibatalkan</span>
                                    @else
                                        <span class="badge badge-secondary p-2">{{ ucfirst($bookingFutsal->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Nama Lapangan</th>
                                <td>{{ $bookingFutsal->lapangan->nama }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Waktu Main</th>
                                <td>
                                    @if($bookingFutsal->jenis_pembayaran === 'event')
                                        {{-- Event: tampilkan range tanggal lengkap --}}
                                        {{ \Carbon\Carbon::parse($bookingFutsal->start_datetime)->locale('id')->translatedFormat('l, d F Y') }}
                                        <span class="text-muted">s/d</span>
                                        {{ \Carbon\Carbon::parse($bookingFutsal->end_datetime)->locale('id')->translatedFormat('l, d F Y') }}
                                        <div class="small text-muted mt-1">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ \Carbon\Carbon::parse($bookingFutsal->start_datetime)->format('H:i') }}
                                            –
                                            {{ \Carbon\Carbon::parse($bookingFutsal->end_datetime)->format('H:i') }}
                                        </div>
                                    @else
                                        {{-- Reguler / Paket: tampilan normal satu hari --}}
                                        {{ \Carbon\Carbon::parse($bookingFutsal->start_datetime)->locale('id')->translatedFormat('l, d F Y') }} | {{ \Carbon\Carbon::parse($bookingFutsal->start_datetime)->format('H:i') }} - {{ \Carbon\Carbon::parse($bookingFutsal->end_datetime)->format('H:i') }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Jenis Booking</th>
                                <td>
                                    @php
                                        $labelJenisBooking = match($bookingFutsal->jenis_pembayaran) {
                                            'event'  => 'Booking Event',
                                            'paket'  => 'Paket Membership',
                                            default  => 'Reguler',
                                        };
                                        $badgeJenisBooking = match($bookingFutsal->jenis_pembayaran) {
                                            'event'  => 'badge-warning',
                                            'paket'  => 'badge-info',
                                            default  => 'badge-primary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeJenisBooking }} p-2">{{ $labelJenisBooking }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Nama Pemesan</th>
                                <td>{{ $bookingFutsal->nama_pemesan }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">No HP</th>
                                <td>{{ $bookingFutsal->no_hp }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Metode Pembayaran</th>
                                <td>
                                    @php
                                        $pembayaranFutsal = $bookingFutsal->booking->pembayaranFutsal->first();
                                        $namaMetodeBayar = $pembayaranFutsal?->tipePembayaran?->nama ?? null;
                                    @endphp
                                    @if($namaMetodeBayar)
                                        <strong>{{ $namaMetodeBayar }}</strong>
                                    @else
                                        <span class="text-muted small">
                                            <i class="fas fa-clock mr-1"></i>Menunggu proses kasir
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        @if($bookingFutsal->status == 'menunggu')
                            @php
                                $bisaBatal = \Carbon\Carbon::parse($bookingFutsal->start_datetime)->gt(now()->addHours(2));
                            @endphp
                            @if($bisaBatal)
                                <a href="{{ route('user.token.batalkan', $token) }}" class="btn btn-danger">
                                    <i class="fas fa-times-circle"></i> Batalkan Booking
                                </a>
                            @else
                                <div class="text-right">
                                    <button class="btn btn-secondary" disabled>
                                        <i class="fas fa-times-circle"></i> Batalkan Booking
                                    </button>
                                    <div class="small text-muted mt-1">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Pembatalan tidak tersedia kurang dari 2 jam sebelum waktu main.
                                    </div>
                                </div>
                            @endif
                        @elseif($bookingFutsal->status == 'dibatalkan')
                            <span class="text-muted small"><i class="fas fa-ban mr-1"></i>Booking ini sudah dibatalkan.</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
