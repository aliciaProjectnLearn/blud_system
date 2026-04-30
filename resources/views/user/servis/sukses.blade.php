@extends('layouts.publik')

@section('title', 'Booking Berhasil')

@push('styles')
    <style>
        .success-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            text-align: center;
        }

        .success-header {
            background: linear-gradient(90deg, #1cc88a, #13855c);
            color: white;
            padding: 40px 20px;
        }

        .success-icon {
            font-size: 4rem;
            margin-bottom: 15px;
        }

        .token-box {
            background: #f8f9fc;
            border: 2px dashed #4e73df;
            border-radius: 12px;
            padding: 20px;
            margin: 30px 0;
            word-break: break-all;
        }

        .btn-wa {
            background-color: #25D366;
            color: white;
            font-weight: bold;
        }

        .btn-wa:hover {
            background-color: #1ebe57;
            color: white;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card success-card mb-4">
                    <div class="success-header">
                        <i class="fas fa-check-circle success-icon"></i>
                        <h3 class="font-weight-bold mb-0">Booking Berhasil!</h3>
                        <p class="mb-0 mt-2 text-white-50">Terima kasih, janji servis Anda telah kami terima.</p>
                    </div>
                    <div class="card-body p-5">
                        
                        <h5 class="font-weight-bold text-gray-800">Kode Booking: <span class="text-primary">{{ $booking->kode_booking }}</span></h5>
                        
                        <div class="token-box">
                            <p class="mb-2 font-weight-bold text-gray-700">Ini adalah Link Akses Anda:</p>
                            <a href="{{ $linkAkses }}" class="text-primary d-block mb-3" target="_blank">{{ $linkAkses }}</a>
                            <small class="text-danger font-weight-bold">
                                <i class="fas fa-exclamation-triangle"></i> PENTING: Harap simpan atau bookmark link di atas! Anda membutuhkannya untuk memantau status servis kendaraan Anda karena sistem tidak menggunakan login.
                            </small>
                        </div>

                        <p class="text-muted mb-4">
                            Agar lebih aman, silakan kirim link tersebut ke WhatsApp Anda sendiri atau Admin bengkel dengan menekan tombol di bawah ini.
                        </p>

                        <div class="d-flex justify-content-center gap-3" style="gap: 15px;">
                            <a href="{{ $urlWa }}" target="_blank" class="btn btn-wa px-4 py-2" style="border-radius: 10px;">
                                <i class="fab fa-whatsapp mr-2"></i> Kirim Link via WhatsApp
                            </a>
                            <a href="{{ $linkAkses }}" class="btn btn-primary px-4 py-2" style="border-radius: 10px;">
                                <i class="fas fa-arrow-right mr-2"></i> Cek Status Sekarang
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
