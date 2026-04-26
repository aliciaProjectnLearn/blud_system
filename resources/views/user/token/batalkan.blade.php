@extends('layouts.publik')

@section('title', 'Konfirmasi Pembatalan')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-danger text-white">
                    <h6 class="m-0 font-weight-bold">Konfirmasi Pembatalan Booking</h6>
                </div>
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-exclamation-triangle fa-4x text-warning"></i>
                    </div>
                    <h4 class="text-gray-800 font-weight-bold">Apakah Anda yakin?</h4>
                    <p class="text-muted">Anda akan membatalkan booking ini. Tindakan ini tidak dapat dibatalkan.</p>
                    
                    <div class="alert alert-light border my-4 text-left">
                        <p class="mb-1 small text-muted">Booking ID: <strong>#{{ $booking->id }}</strong></p>
                        <p class="mb-1 small text-muted">Layanan: 
                            <strong>
                                @if($booking instanceof \App\Models\BookingFutsal) Futsal
                                @elseif($booking instanceof \App\Models\BookingAc) Servis AC
                                @elseif($booking instanceof \App\Models\BookingServis) Servis Kendaraan
                                @elseif($booking instanceof \App\Models\SewaRuko) Sewa Kantin
                                @endif
                            </strong>
                        </p>
                    </div>

                    <form action="{{ route('user.token.batalkan.proses', $token) }}" method="POST">
                        @csrf
                        <div class="form-group mb-4">
                            <label class="small font-weight-bold text-dark d-block text-left">Alasan Pembatalan (Opsional)</label>
                            <textarea name="alasan" class="form-control" rows="3" placeholder="Beritahu kami mengapa Anda membatalkan..."></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-center" style="gap:10px;">
                            <a href="{{ route('user.token.show', $token) }}" class="btn btn-secondary px-4">Kembali</a>
                            <button type="submit" class="btn btn-danger px-4">Ya, Batalkan Booking</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
