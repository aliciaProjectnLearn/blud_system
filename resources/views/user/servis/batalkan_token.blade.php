@extends('layouts.publik')

@section('title', 'Batalkan Booking Servis')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">

            <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header py-3" style="background: linear-gradient(90deg, #e74a3b, #c0392b);">
                    <h5 class="mb-0 text-white font-weight-bold">
                        <i class="fas fa-times-circle mr-2"></i>Batalkan Booking Servis
                    </h5>
                </div>

                <div class="card-body p-4">
                    <div class="alert alert-warning d-flex align-items-start" style="border-radius: 12px;">
                        <i class="fas fa-exclamation-triangle fa-lg mr-3 mt-1"></i>
                        <div>
                            <strong>Perhatian!</strong><br>
                            Tindakan ini tidak dapat diurungkan. Booking yang dibatalkan tidak bisa dipulihkan.
                        </div>
                    </div>

                    {{-- Info Booking --}}
                    <div class="border rounded p-3 mb-4" style="border-radius: 12px !important; background: #f8f9fc;">
                        <div class="row">
                            <div class="col-6 mb-2">
                                <div class="text-xs text-uppercase text-muted font-weight-bold">Kode Booking</div>
                                <div class="font-weight-bold text-primary">{{ $booking->kode_booking }}</div>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="text-xs text-uppercase text-muted font-weight-bold">Status</div>
                                <span class="badge badge-warning p-1 px-2">{{ strtoupper($booking->status) }}</span>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="text-xs text-uppercase text-muted font-weight-bold">Kendaraan</div>
                                <div class="font-weight-bold">{{ $booking->merek_kendaraan }}</div>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="text-xs text-uppercase text-muted font-weight-bold">Plat Nomor</div>
                                <div class="font-weight-bold">{{ $booking->nomor_plat }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-xs text-uppercase text-muted font-weight-bold">Tanggal</div>
                                <div class="font-weight-bold">{{ \Carbon\Carbon::parse($booking->tanggal_booking)->translatedFormat('d F Y') }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-xs text-uppercase text-muted font-weight-bold">Jam</div>
                                <div class="font-weight-bold">{{ \Carbon\Carbon::parse($booking->jam_booking)->format('H:i') }} WIB</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 justify-content-between" style="gap: 12px;">
                        <a href="{{ route('user.servis.token.detail', $token) }}"
                           class="btn btn-secondary flex-fill font-weight-bold"
                           style="border-radius: 10px;">
                            <i class="fas fa-arrow-left mr-2"></i>Batal
                        </a>

                        <form action="{{ route('user.servis.token.batalkan.proses', $token) }}" method="POST" class="flex-fill">
                            @csrf
                            <button type="submit"
                                    class="btn btn-danger w-100 font-weight-bold"
                                    style="border-radius: 10px;"
                                    onclick="return confirm('Apakah Anda yakin ingin membatalkan booking ini?')">
                                <i class="fas fa-times-circle mr-2"></i>Ya, Batalkan
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
