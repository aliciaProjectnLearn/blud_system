@extends('layouts.publik')
@section('title', 'Beli Paket Booking Futsal')

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-id-card text-primary mr-2"></i> Paket Booking Futsal
        </h1>
        <a href="{{ route('user.gateway') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali ke Dashboard Utama
        </a>
    </div>

    <p>
        Pilih paket yang sesuai dengan kebutuhan Anda. Kuota dapat digunakan untuk booking lapangan futsal. Pembayaran paket menggunakan QRIS.
    </p>

        @if($paketMemberships->isEmpty())
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada paket membership yang tersedia saat ini.</p>
                    <a href="{{ route('user.gateway') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>
        @else
            <form action="{{ route('user.futsal.paket.store') }}" method="POST" id="formPaket" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    @foreach($paketMemberships as $paket)
                    <div class="col-md-4 mb-4">
                        <label for="paket_{{ $paket->id }}" class="d-block h-100 cursor-pointer">
                            <div class="card shadow h-100 paket-card" id="card_{{ $paket->id }}"
                                style="border: 2px solid transparent; cursor: pointer; transition: all .2s;">
                                <div class="card-body text-center py-4">
                                    <div class="mb-3">
                                        <i class="fas fa-id-card fa-3x text-primary"></i>
                                    </div>
                                    <h5 class="font-weight-bold">{{ $paket->nama_paket }}</h5>
                                    <div class="my-3">
                                        <span class="display-4 font-weight-bold text-primary">{{ $paket->jumlah_kuota }}</span>
                                        <span class="text-muted d-block">Jam Kuota</span>
                                    </div>
                                    <hr>
                                    <h4 class="text-success font-weight-bold mb-3">
                                        Rp {{ number_format($paket->harga, 0, ',', '.') }}
                                    </h4>
                                    <div class="custom-control custom-radio">
                                        <input type="radio"
                                            id="paket_{{ $paket->id }}"
                                            name="paket_membership_id"
                                            value="{{ $paket->id }}"
                                            class="custom-control-input paket-radio"
                                            required>
                                        <label class="custom-control-label font-weight-bold" for="paket_{{ $paket->id }}">
                                            Pilih Paket Ini
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-edit mr-1"></i> Data Pemesan & Pembayaran</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-gray-700">Nama Lengkap</label>
                                    <input type="text" name="nama_pemesan" class="form-control" required placeholder="Nama lengkap Anda">
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold text-gray-700">Nomor WhatsApp</label>
                                    <input type="text" name="no_hp" class="form-control" required placeholder="08xxxxxxxxxx (Minimal 10 digit)">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-gray-100 p-3 rounded border text-center">
                                    <label class="font-weight-bold text-gray-700 mb-2">
                                        <i class="fas fa-qrcode text-primary mr-1"></i> Pembayaran QRIS
                                    </label>
                                    <br>
                                    <img src="{{ asset('assets/img/qris_blud.png') }}" alt="QRIS" class="img-fluid border shadow-sm mb-3" style="max-height:200px;">
                                    
                                    <div class="form-group text-left px-3">
                                        <label class="small font-weight-bold text-danger"><i class="fas fa-upload mr-1"></i> Upload Bukti Pembayaran (Wajib)</label>
                                        <div class="custom-file">
                                            <input type="file" name="bukti_pembayaran" class="custom-file-input" id="bukti_pembayaran" required accept="image/*">
                                            <label class="custom-file-label small" for="bukti_pembayaran">Pilih gambar bukti transfer...</label>
                                        </div>
                                        <p class="x-small text-muted mt-1 mb-0" style="font-size: 10px;">Format: JPG, PNG, JPEG. Maks: 2MB</p>
                                    </div>
                                    <hr class="my-2">
                                    <p class="small text-muted mb-0">Silahkan scan QR di atas untuk membayar sesuai harga paket. Transaksi Anda akan diproses oleh admin setelah bukti pembayaran diverifikasi.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-2 d-flex align-items-center gap-2">
                    <button type="submit" class="btn btn-primary btn-lg shadow-sm px-5">
                        <i class="fas fa-shopping-cart mr-2"></i> Konfirmasi & Beli
                    </button>
                    <a href="{{ route('user.gateway') }}" class="btn btn-outline-secondary btn-lg ml-2">
                        Batal
                    </a>
                </div>
            </form>
        @endif

</div>
@endsection

@push('scripts')
<script>
    // Highlight card saat radio dipilih
    document.querySelectorAll('.paket-radio').forEach(function(radio) {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.paket-card').forEach(function(card) {
                card.style.borderColor = 'transparent';
                card.style.boxShadow = '';
            });
            var selectedCard = document.getElementById('card_' + this.value);
            if (selectedCard) {
                selectedCard.style.borderColor = '#4e73df';
                selectedCard.style.boxShadow = '0 0 0 3px rgba(78,115,223,.2)';
            }
        });
    });
    // Tampilkan nama file yang dipilih
    document.getElementById('bukti_pembayaran').addEventListener('change', function(e) {
        var fileName = e.target.files[0].name;
        var nextSibling = e.target.nextElementSibling;
        nextSibling.innerText = fileName;
    });
</script>
@endpush
