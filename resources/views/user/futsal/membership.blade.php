@extends('layouts.app')
@section('title', 'Beli Membership Futsal')

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-id-card text-primary mr-2"></i> Membership Futsal
        </h1>
        <a href="{{ route('user.futsal.landing') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali ke Dashboard Utama
        </a>
    </div>

    {{-- Jika sudah punya membership aktif --}}
    @if($membershipAktif)
        <div class="card shadow mb-4 border-left-success">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-success d-flex align-items-center justify-content-center mr-3"
                        style="width:52px;height:52px;">
                        <i class="fas fa-check-circle fa-lg text-white"></i>
                    </div>
                    <div>
                        <h5 class="font-weight-bold text-success mb-0">Membership Aktif</h5>
                        <small class="text-muted">Anda sudah memiliki paket membership yang sedang berjalan</small>
                    </div>
                </div>
                <table class="table table-borderless mb-0">
                    <tr>
                        <td width="35%" class="text-muted py-1">Paket</td>
                        <td class="py-1">: <strong>{{ $membershipAktif->paket->nama_paket }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted py-1">Total Kuota</td>
                        <td class="py-1">: {{ $membershipAktif->total_kuota }} Jam</td>
                    </tr>
                    <tr>
                        <td class="text-muted py-1">Sisa Kuota</td>
                        <td class="py-1">: <strong class="text-primary">{{ $membershipAktif->sisa_kuota }} Jam</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted py-1">Status</td>
                        <td class="py-1">: <span class="badge badge-success px-3 py-1">Aktif</span></td>
                    </tr>
                </table>
                <hr>
                <a href="{{ route('user.futsal.booking.form') }}" class="btn btn-primary shadow-sm">
                    <i class="fas fa-futbol mr-1"></i> Booking Lapangan Sekarang
                </a>
            </div>
        </div>

    {{-- Jika belum punya membership --}}
    @else
        <p class="text-muted mb-4">
            <i class="fas fa-info-circle mr-1"></i>
            Pilih paket membership yang sesuai dengan kebutuhan Anda. Kuota dapat digunakan untuk booking lapangan futsal.
        </p>

        @if($paketMemberships->isEmpty())
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada paket membership yang tersedia saat ini.</p>
                    <a href="{{ route('user.futsal.dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>
        @else
            <form action="{{ route('user.futsal.membership.store') }}" method="POST" id="formMembership">
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
                                        <span class="text-muted d-block">Jam Kuota Main</span>
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

                <div class="mt-2 d-flex align-items-center gap-2">
                    <button type="submit" class="btn btn-primary btn-lg shadow-sm px-5">
                        <i class="fas fa-shopping-cart mr-2"></i> Beli Membership
                    </button>
                    <a href="{{ route('user.futsal.landing') }}" class="btn btn-outline-secondary btn-lg ml-2">
                        Batal
                    </a>
                </div>
            </form>
        @endif
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
</script>
@endpush
