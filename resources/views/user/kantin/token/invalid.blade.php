@extends('layouts.publik')

@section('title', 'Link Tidak Valid')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="mb-4">
                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center" style="width:100px; height:100px;">
                    <i class="fas fa-link fa-3x text-danger"></i>
                </div>
            </div>
            <h4 class="font-weight-bold">Link Tidak Valid</h4>
            <p class="text-muted">{{ $pesan }}</p>
            <a href="{{ route('user.kantin.katalog') }}" 
               class="btn btn-primary mt-2 px-4 py-2">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Katalog
            </a>
        </div>
    </div>
</div>
@endsection
