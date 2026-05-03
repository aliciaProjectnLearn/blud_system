@extends('layouts.publik')

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
                                <td>{{ \Carbon\Carbon::parse($bookingFutsal->start_datetime)->locale('id')->translatedFormat('l, d F Y') }} | {{ \Carbon\Carbon::parse($bookingFutsal->start_datetime)->format('H:i') }} - {{ \Carbon\Carbon::parse($bookingFutsal->end_datetime)->format('H:i') }}</td>
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
                                <th class="bg-light">Jenis Pembayaran</th>
                                <td>
                                    @php 
                                        $pembayaran = $bookingFutsal->booking->pembayaranFutsal->first();
                                        $tipe = $pembayaran->tipePembayaran->nama ?? ($bookingFutsal->jenis_pembayaran === 'membership' ? 'Paket' : 'Reguler');
                                    @endphp
                                    {{ $tipe }}
                                </td>
                            </tr>
                        </table>
                    </div>

                    <hr class="my-4">

                    <div class="text-center">
                        @php
                            $currentStatus = strtolower($booking->status ?? ($booking->booking->status ?? ''));
                        @endphp
                        
                        @if($currentStatus === 'menunggu')
                            <a href="#" onclick="konfirmasiBatal(event)" class="btn btn-outline-danger btn-sm mx-1">
                                <i class="fas fa-times mr-1"></i> Batalkan Booking
                            </a>
                        @elseif(in_array($currentStatus, ['diproses', 'siap_bayar', 'selesai', 'proses', 'aktif']))
                            <button class="btn btn-outline-secondary btn-sm mx-1" disabled>
                                <i class="fas fa-lock mr-1"></i> Tidak dapat dibatalkan
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
