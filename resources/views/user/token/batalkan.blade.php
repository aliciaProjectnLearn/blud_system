@extends('layouts.publik')

@section('content')
<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0 py-2"><i class="fas fa-exclamation-triangle mr-2"></i> Batalkan Booking</h5>
                </div>
                <div class="card-body p-4 text-center">
                    
                    <h4 class="mb-3">Apakah Anda yakin ingin membatalkan booking ini?</h4>
                    <p class="text-muted mb-4">
                        Lapangan: <strong>{{ $bookingFutsal->lapangan->nama }}</strong><br>
                        Waktu: <strong>{{ \Carbon\Carbon::parse($bookingFutsal->start_datetime)->locale('id')->translatedFormat('d F Y | H:i') }}</strong>
                    </p>

                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle mr-1"></i> Pembatalan hanya bisa dilakukan minimal 2 jam sebelum waktu bermain. Tindakan ini tidak dapat dikembalikan.
                    </div>

                    <form action="{{ route('user.token.batalkan.proses', $token) }}" method="POST" class="mt-4">
                        @csrf
                        <div class="d-flex justify-content-center gap-3" style="gap: 15px;">
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
