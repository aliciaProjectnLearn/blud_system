@extends('layouts.publik')

@section('title', 'Booking Berhasil')

@section('content')
<div class="row justify-content-center py-5">
    <div class="col-lg-7 text-center">
        {{-- Success Illustration --}}
        <div class="mb-4">
            <div class="success-icon-wrapper mx-auto mb-4">
                <i class="fas fa-check fa-4x text-white"></i>
            </div>
            <h1 class="display-4 font-weight-bold text-gray-900">Booking Berhasil!</h1>
            <p class="lead text-gray-700">Terima kasih, pengajuan sewa Anda telah kami terima.</p>
        </div>

        <div class="card shadow-lg border-0 rounded-lg mb-4 overflow-hidden">
            <div class="card-body p-4 p-lg-5">
                <div class="alert alert-success border-0 bg-success-soft text-success mb-4">
                    <i class="fas fa-whatsapp fa-lg mr-2"></i> Link akses telah dikirimkan ke nomor WhatsApp Anda.
                </div>

                <p class="text-muted mb-4">Gunakan link atau token berikut untuk mengakses dashboard penyewaan Anda di masa mendatang:</p>
                
                <div class="input-group mb-4 bg-light p-2 rounded border">
                    <input type="text" class="form-control border-0 bg-transparent text-center font-weight-bold text-primary" value="{{ $sewa->access_token }}" id="tokenInput" readonly>
                    <div class="input-group-append">
                        <button class="btn btn-primary rounded" type="button" onclick="copyToken()">
                            <i class="fas fa-copy mr-2"></i> Salin Token
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <a href="{{ route('user.kantin.sewa.detail', $sewa->access_token) }}" class="btn btn-primary btn-block btn-lg shadow-sm py-3 font-weight-bold">
                            LIHAT DETAIL SEWA <i class="fas fa-external-link-alt ml-2"></i>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('user.kantin.katalog') }}" class="btn btn-outline-secondary btn-block btn-lg py-3">
                            KEMBALI KE KATALOG
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card-footer bg-light py-4">
                <div class="row align-items-center">
                    <div class="col-md-8 text-md-left mb-3 mb-md-0">
                        <h6 class="font-weight-bold text-gray-800 mb-1">Butuh Bantuan Pembayaran?</h6>
                        <p class="small text-muted mb-0">Jika Anda ingin membayar secara tunai atau memiliki pertanyaan teknis, silakan hubungi bendahara.</p>
                    </div>
                    <div class="col-md-4 text-md-right">
                        <a href="https://wa.me/{{ config('blud.bendahara.no_hp') }}" class="btn btn-success btn-sm rounded-pill px-4">
                            <i class="fab fa-whatsapp mr-1"></i> Hubungi Bendahara
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <p class="small text-muted">Pastikan Anda menyimpan link akses ini. Link ini adalah satu-satunya cara untuk mengakses data penyewaan Anda tanpa login.</p>
    </div>
</div>
@endsection

@section('styles')
<style>
    .success-icon-wrapper {
        width: 120px;
        height: 120px;
        background-color: #1cc88a;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 25px rgba(28, 200, 138, 0.3);
        animation: scaleIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    @keyframes scaleIn {
        from { transform: scale(0); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    .bg-success-soft {
        background-color: rgba(28, 200, 138, 0.1);
    }
    #tokenInput {
        font-family: 'Courier New', Courier, monospace;
        letter-spacing: 1px;
        font-size: 0.9rem;
    }
</style>
@endsection

@section('scripts')
<script>
    function copyToken() {
        var copyText = document.getElementById("tokenInput");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");
        
        Swal.fire({
            icon: 'success',
            title: 'Tersalin!',
            text: 'Token akses berhasil disalin ke clipboard.',
            timer: 2000,
            showConfirmButton: false
        });
    }
</script>
@endsection
