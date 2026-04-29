@extends('layouts.publik')

@section('title', 'Detail Booking #' . $booking->id)

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            {{-- Alert Success/Error --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            {{-- Token Link Copy --}}
            <div class="card shadow-sm border-left-primary mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="font-weight-bold text-primary mb-1">Simpan Link Akses Ini</h6>
                            <p class="text-muted small mb-0">Gunakan link ini untuk melihat status booking Anda di lain waktu tanpa perlu login.</p>
                        </div>
                        <button class="btn btn-primary btn-sm px-3" onclick="copyTokenLink()">
                            <i class="fas fa-copy mr-1"></i> Copy Link
                        </button>
                    </div>
                    <div class="input-group mt-3">
                        <input type="text" id="tokenLink" class="form-control form-control-sm bg-light" readonly value="{{ url()->current() }}">
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
                    <h6 class="m-0 font-weight-bold text-gray-800">
                        <i class="fas fa-receipt mr-1 text-primary"></i> Detail Booking
                    </h6>
                    <span class="badge badge-{{ $booking->status == 'dikonfirmasi' || $booking->status == 'aktif' ? 'success' : (in_array($booking->status, ['dibatalkan', 'batal']) ? 'danger' : 'warning') }} px-3 py-2">
                        {{ strtoupper($booking->status ?? ($booking->booking->status ?? 'MENUNGGU')) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-xs font-weight-bold text-primary text-uppercase mb-3">Informasi Pelanggan</h6>
                            <p class="mb-1"><strong>Nama:</strong> {{ $user->nama_lengkap ?? $user->name ?? '-' }}</p>
                            <p class="mb-4"><strong>No. HP:</strong> {{ $user->no_hp ?? '-' }}</p>

                            <h6 class="text-xs font-weight-bold text-primary text-uppercase mb-3">Layanan</h6>
                            <p class="mb-1 text-gray-800 h5 font-weight-bold">
                                @if($type == 'futsal')
                                    <i class="fas fa-futbol mr-2 text-success"></i> Futsal - {{ $booking->lapangan->nama ?? '-' }}
                                @elseif($type == 'ac')
                                    <i class="fas fa-snowflake mr-2 text-info"></i> Servis AC - {{ $booking->layanan->nama ?? '-' }}
                                @elseif($type == 'servis')
                                    <i class="fas fa-tools mr-2 text-warning"></i> Servis Kendaraan - {{ $booking->layananServis->nama_layanan ?? '-' }}
                                @elseif($type == 'kantin')
                                    <i class="fas fa-store mr-2 text-danger"></i> Sewa Kantin - {{ $booking->ruko->kode_unit ?? '-' }}
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 text-md-right mt-4 mt-md-0">
                            <h6 class="text-xs font-weight-bold text-primary text-uppercase mb-3">Waktu & Tanggal</h6>
                            @if($type == 'futsal')
                                <p class="mb-1"><strong>Tanggal:</strong> {{ $booking->start_datetime->format('d M Y') }}</p>
                                <p class="mb-0"><strong>Jam:</strong> {{ $booking->start_datetime->format('H:i') }} - {{ $booking->end_datetime->format('H:i') }}</p>
                            @elseif($type == 'ac')
                                <p class="mb-1"><strong>Tanggal Kunjungan:</strong> {{ \Carbon\Carbon::parse($booking->tgl_kunjungan)->format('d M Y') }}</p>
                            @elseif($type == 'servis')
                                <p class="mb-1"><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($booking->tanggal_booking)->format('d M Y') }}</p>
                                <p class="mb-0"><strong>Jam:</strong> {{ $booking->jam_booking }}</p>
                            @elseif($type == 'kantin')
                                <p class="mb-1"><strong>Periode:</strong> {{ \Carbon\Carbon::parse($booking->tgl_mulai)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($booking->tgl_selesai)->format('d M Y') }}</p>
                            @endif
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="text-center">
                        <a href="{{ route('user.token.riwayat', $token) }}" class="btn btn-outline-primary btn-sm mx-1">
                            <i class="fas fa-history mr-1"></i> Lihat Semua Riwayat Saya
                        </a>
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
            
            <div class="text-center mt-4">
                <a href="{{ route('home') }}" class="text-muted small">
                    <i class="fas fa-home mr-1"></i> Kembali ke Halaman Utama
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function copyTokenLink() {
    var copyText = document.getElementById("tokenLink");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    
    Swal.fire({
        icon: 'success',
        title: 'Link Berhasil Disalin',
        showConfirmButton: false,
        timer: 1500
    });
}

function konfirmasiBatal(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Batalkan Booking?',
        text: 'Apakah Anda yakin ingin membatalkan booking ini? Tindakan ini tidak dapat diurungkan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74a3b',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Batalkan',
        cancelButtonText: 'Tidak'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "{{ route('user.token.batalkan', $token) }}";
        }
    });
}
</script>
@endsection
