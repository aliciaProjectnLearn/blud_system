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

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('user.token.riwayat', $token) }}" class="btn btn-info text-white"><i class="fas fa-history"></i> Lihat Riwayat</a>
                        @if($bookingFutsal->status == 'menunggu' && \Carbon\Carbon::parse($bookingFutsal->start_datetime)->gt(now()->addHours(2)))
                            <a href="{{ route('user.token.batalkan', $token) }}" class="btn btn-danger"><i class="fas fa-times-circle"></i> Batalkan Booking</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
