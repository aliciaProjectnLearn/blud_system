@extends('layouts.publik')

@section('title', 'Token Tidak Ditemukan')

@section('content')
    <div class="container-fluid py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px;">
                    <div class="card-body p-5 text-center">
                        <i class="fas fa-search fa-4x text-danger mb-4"></i>
                        <h4 class="font-weight-bold text-gray-800">Ups, Data Tidak Ditemukan</h4>
                        <p class="text-muted mt-3 mb-4">
                            Maaf, link akses atau token yang Anda tuju tidak valid atau mungkin sudah kedaluwarsa. Pastikan Anda meng-copy link dengan benar dari pesan WhatsApp Anda.
                        </p>
                        <a href="{{ route('user.servis.katalog') }}" class="btn btn-primary px-4 py-2 font-weight-bold" style="border-radius: 10px;">
                            <i class="fas fa-home mr-2"></i> Kembali ke Layanan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
