@extends('layouts.publik')

@section('title', 'Link Sudah Tidak Aktif')

@section('content')
<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <i class="fas fa-lock fa-4x text-secondary"></i>
                    </div>
                    <h4 class="font-weight-bold text-gray-800 mb-2">Link Sudah Tidak Aktif</h4>
                    <p class="text-muted mb-4">
                        Akses ini sudah tidak aktif karena status booking sudah <strong>{{ ucfirst($status) }}</strong>.
                    </p>
                    <div class="alert alert-light border text-left">
                        <small class="text-muted">
                            <i class="fas fa-info-circle mr-1 text-info"></i>
                            Data tetap tersimpan di sistem kami.
                            Jika ada pertanyaan, silakan hubungi Admin BLUD melalui WhatsApp atau datang langsung ke lokasi.
                        </small>
                    </div>
                    <a href="{{ url('/') }}" class="btn btn-primary mt-2">
                        <i class="fas fa-home mr-1"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
