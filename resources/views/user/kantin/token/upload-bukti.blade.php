@extends('layouts.publik')
@php $hideNavbarBack = true; @endphp

@section('title', 'Upload Bukti Pembayaran')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="mb-4">
            <a href="{{ route('user.kantin.sewa.detail', $sewa->access_token) }}" class="btn btn-outline-primary btn-sm mb-3 shadow-sm">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Detail Sewa
            </a>
            <h1 class="h3 font-weight-bold text-gray-800 mt-2">Pilih Metode Pembayaran</h1>
            <p class="text-muted">Silakan pilih metode pembayaran dan unggah bukti pembayaran Anda di bawah ini.</p>
        </div>

        <form action="{{ route('user.kantin.sewa.upload', $sewa->access_token) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="pembayaran_id" value="{{ $pembayaran->id }}">
            <input type="hidden" name="token" value="{{ $sewa->access_token }}">
            <input type="hidden" name="metode_pembayaran" id="hidden-metode">

            {{-- Tampilkan info termin yang sedang dibayar (read-only) --}}
            <div class="card border-left-primary mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">PEMBAYARAN</small>
                            <h6 class="font-weight-bold">
                                Termin {{ $pembayaran->termin_ke }}
                                @if($sewa->tipe_pembayaran === '1_termin')
                                    — Lunas
                                @else
                                    ({{ $pembayaran->termin_ke == 1 ? '50% di Awal' : '50% Bulan ke-6' }})
                                @endif
                            </h6>
                        </div>
                        <div class="col-6 text-right">
                            <small class="text-muted">JUMLAH TAGIHAN</small>
                            <h5 class="font-weight-bold text-primary">
                                Rp {{ number_format($pembayaran->jumlah_tagihan, 0, ',', '.') }}
                            </h5>
                        </div>
                    </div>
                    <hr class="my-2">
                    <small class="text-muted">
                        <i class="fas fa-calendar mr-1"></i>
                        Jatuh tempo: {{ \Carbon\Carbon::parse($pembayaran->tgl_jatuh_tempo)->format('d M Y') }}
                    </small>
                </div>
            </div>

            {{-- Pilihan Metode Pembayaran --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">💳 Pilih Metode Pembayaran</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Transfer Bank -->
                        <div class="col-md-4 mb-2">
                            <label class="metode-card w-100 mb-0" for="metode_transfer">
                                <input type="radio" name="metode" 
                                       id="metode_transfer" value="transfer"
                                       class="metode-radio d-none" required>
                                <div class="border rounded p-2 text-center metode-option">
                                    <i class="fas fa-university fa-lg text-primary mb-1 mt-1"></i>
                                    <div class="font-weight-bold" style="font-size: 0.9rem;">Transfer Bank</div>
                                    <small class="text-muted" style="font-size: 0.75rem;">ATM/m-Banking</small>
                                </div>
                            </label>
                        </div>
                        <!-- QRIS -->
                        <div class="col-md-4 mb-2">
                            <label class="metode-card w-100 mb-0" for="metode_qris">
                                <input type="radio" name="metode"
                                       id="metode_qris" value="qris"
                                       class="metode-radio d-none" required>
                                <div class="border rounded p-2 text-center metode-option">
                                    <i class="fas fa-qrcode fa-lg text-success mb-1 mt-1"></i>
                                    <div class="font-weight-bold" style="font-size: 0.9rem;">QRIS</div>
                                    <small class="text-muted" style="font-size: 0.75rem;">Scan QR Code</small>
                                </div>
                            </label>
                        </div>
                        <!-- Tunai -->
                        <div class="col-md-4 mb-2">
                            <label class="metode-card w-100 mb-0" for="metode_tunai">
                                <input type="radio" name="metode"
                                       id="metode_tunai" value="tunai"
                                       class="metode-radio d-none" required>
                                <div class="border rounded p-2 text-center metode-option">
                                    <i class="fas fa-money-bill-wave fa-lg text-warning mb-1 mt-1"></i>
                                    <div class="font-weight-bold" style="font-size: 0.9rem;">Tunai</div>
                                    <small class="text-muted" style="font-size: 0.75rem;">Bayar via admin</small>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Panel Transfer Bank --}}
            <div id="panel-transfer" class="metode-panel d-none">
                <div class="card border-left-primary mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-university mr-2"></i> Informasi Rekening
                    </div>
                    <div class="card-body px-3 py-2">
                        <div class="row align-items-center mb-2 border-bottom pb-2">
                            <div class="col-12 col-md-4 text-muted small font-weight-bold">Bank</div>
                            <div class="col-10 col-md-6"><strong>{{ config('blud.rekening.bank') }}</strong></div>
                            <div class="col-2 col-md-2 text-right">
                                <button type="button" onclick="salin('{{ config('blud.rekening.bank') }}')" class="btn btn-sm btn-outline-secondary border-0 bg-light">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row align-items-center mb-2 border-bottom pb-2">
                            <div class="col-12 col-md-4 text-muted small font-weight-bold">No. Rekening</div>
                            <div class="col-10 col-md-6"><strong id="no-rek">{{ config('blud.rekening.nomor') }}</strong></div>
                            <div class="col-2 col-md-2 text-right">
                                <button type="button" onclick="salin('{{ str_replace('-', '', config('blud.rekening.nomor')) }}')" 
                                        class="btn btn-sm btn-outline-secondary border-0 bg-light">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row align-items-center mb-2 border-bottom pb-2 mt-2 mt-md-0">
                            <div class="col-12 col-md-4 text-muted small font-weight-bold">Atas Nama</div>
                            <div class="col-12 col-md-8"><strong>{{ config('blud.rekening.atas_nama') }}</strong></div>
                        </div>
                        <div class="row align-items-center pt-1 mt-2 mt-md-0">
                            <div class="col-12 col-md-4 text-muted small font-weight-bold">Jumlah Transfer</div>
                            <div class="col-10 col-md-6">
                                <strong class="text-primary h5 mb-0" id="jumlah-transfer">
                                    Rp {{ number_format($pembayaran->jumlah_tagihan, 0, ',', '.') }}
                                </strong>
                            </div>
                            <div class="col-2 col-md-2 text-right">
                                <button type="button" onclick="salin('{{ $pembayaran->jumlah_tagihan }}')" 
                                        class="btn btn-sm btn-outline-secondary border-0 bg-light">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Upload bukti --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <label class="font-weight-bold mb-2">
                            <i class="fas fa-upload mr-1"></i> Upload Bukti Transfer
                        </label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="bukti_transfer"
                                   name="bukti" accept=".jpg,.jpeg,.png,.pdf">
                            <label class="custom-file-label" for="bukti_transfer">
                                Pilih file (JPG, PNG, PDF maks 2MB)
                            </label>
                        </div>
                        <div id="preview-transfer" class="mt-3 d-none">
                            <img id="img-preview-transfer" src="" class="img-fluid rounded" 
                                 style="max-height:200px">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Panel QRIS --}}
            <div id="panel-qris" class="metode-panel d-none">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-qrcode mr-2"></i> Scan QRIS untuk Membayar
                    </div>
                    <div class="card-body text-center">
                        @if($sewa->ruko->kategori && $sewa->ruko->kategori->qris_path)
                            <img src="{{ asset('storage/' . $sewa->ruko->kategori->qris_path) }}" alt="QR Code Pembayaran" 
                                 class="img-fluid mb-3" style="max-width:250px">
                        @elseif(file_exists(public_path('assets/img/qris_blud.png')))
                            <img src="{{ asset('assets/img/qris_blud.png') }}" alt="QR Code Pembayaran" 
                                 class="img-fluid mb-3" style="max-width:250px">
                        @else
                            {{-- Generate QR menggunakan Google Charts API sebagai opsi terakhir --}}
                            @php
                                $qrData = 'BLUD-PAYMENT-' . $sewa->access_token . '-TERMIN' . $pembayaran->termin_ke . '-' . $pembayaran->jumlah_tagihan;
                                $qrUrl = 'https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=' . urlencode($qrData) . '&choe=UTF-8';
                            @endphp
                            <img src="{{ $qrUrl }}" alt="QR Code Pembayaran" 
                                 class="img-fluid mb-3" style="max-width:250px">
                        @endif
                        <p class="text-muted small">
                            Scan QR di atas menggunakan aplikasi pembayaran Anda<br>
                            (GoPay, OVO, Dana, ShopeePay, m-Banking, dll)
                        </p>
                        <div class="alert alert-info">
                            <strong>Nominal:</strong> 
                            Rp {{ number_format($pembayaran->jumlah_tagihan, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
                {{-- Upload bukti QRIS --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <label class="font-weight-bold mb-2">
                            <i class="fas fa-upload mr-1"></i> Upload Bukti Pembayaran QRIS
                        </label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="bukti_qris"
                                   name="bukti" accept=".jpg,.jpeg,.png,.pdf">
                            <label class="custom-file-label" for="bukti_qris">
                                Pilih screenshot bukti pembayaran
                            </label>
                        </div>
                        <div id="preview-qris" class="mt-3 d-none">
                            <img id="img-preview-qris" src="" class="img-fluid rounded" 
                                 style="max-height:200px">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Panel Tunai --}}
            <div id="panel-tunai" class="metode-panel d-none">
                <div class="card border-left-warning mb-4">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="fas fa-money-bill-wave fa-3x text-warning mb-2"></i>
                            <h5 class="font-weight-bold">Pembayaran Tunai</h5>
                        </div>
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle mr-2"></i>
                            Untuk pembayaran tunai, silakan hubungi Admin BLUD yang
                            mengirimkan link ini kepada Anda.
                        </div>
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-circle fa-3x text-secondary mr-3"></i>
                                    <div>
                                        <div class="font-weight-bold">{{ config('blud.bendahara.nama') }}</div>
                                        <div class="text-muted small">Bendahara BLUD</div>
                                        <a href="https://wa.me/{{ config('blud.bendahara.no_hp') }}?text={{ urlencode('Halo Admin BLUD, saya ingin melakukan pembayaran tunai untuk sewa unit. Token: ' . $sewa->access_token) }}"
                                           class="btn btn-success btn-sm mt-2" target="_blank">
                                            <i class="fab fa-whatsapp mr-1"></i> Hubungi via WhatsApp
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted small text-center mt-3">
                            Setelah pembayaran diterima, admin akan mengkonfirmasi 
                            status pembayaran Anda secara manual.
                        </p>
                    </div>
                </div>
            </div>

            <div id="btn-submit-wrapper">
                {{-- Muncul hanya untuk transfer & qris --}}
                <button type="submit" id="btn-submit" 
                        class="btn btn-primary btn-block btn-lg d-none">
                    <i class="fas fa-cloud-upload-alt mr-2"></i> UNGGAH BUKTI SEKARANG
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// CSS untuk card metode aktif
const style = document.createElement('style');
style.textContent = `
    .metode-option { cursor: pointer; transition: all 0.2s; }
    .metode-option:hover { border-color: #3d5af1 !important; background: #f0f4ff; }
    .metode-radio:checked + .metode-option { 
        border-color: #3d5af1 !important; 
        background: #f0f4ff; 
        box-shadow: 0 0 0 2px #3d5af1;
    }
`;
document.head.appendChild(style);

// Show/hide panel
document.querySelectorAll('.metode-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('hidden-metode').value = this.value;

        // Sembunyikan semua panel
        document.querySelectorAll('.metode-panel').forEach(p => p.classList.add('d-none'));
        document.getElementById('btn-submit').classList.add('d-none');
        
        // Disable file inputs for safety
        document.getElementById('bukti_transfer').disabled = true;
        document.getElementById('bukti_qris').disabled = true;
        
        // Tampilkan panel yang dipilih
        const panel = document.getElementById('panel-' + this.value);
        if (panel) panel.classList.remove('d-none');
        
        // Tombol submit hanya untuk transfer & qris
        if (this.value === 'transfer') {
            document.getElementById('btn-submit').classList.remove('d-none');
            document.getElementById('bukti_transfer').disabled = false;
        } else if (this.value === 'qris') {
            document.getElementById('btn-submit').classList.remove('d-none');
            document.getElementById('bukti_qris').disabled = false;
        }
    });
});

// Custom file label update
document.querySelectorAll('.custom-file-input').forEach(input => {
    input.addEventListener('change', function() {
        const fileName = this.files[0]?.name || 'Pilih file';
        this.nextElementSibling.textContent = fileName;
        
        // Preview gambar
        if (this.files[0] && this.files[0].type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = e => {
                const previewId = this.id === 'bukti_transfer' ? 'img-preview-transfer' : 'img-preview-qris';
                const wrapperId = this.id === 'bukti_transfer' ? 'preview-transfer' : 'preview-qris';
                
                const preview = document.getElementById(previewId);
                const wrapper = document.getElementById(wrapperId);
                
                if (preview && wrapper) {
                    preview.src = e.target.result;
                    wrapper.classList.remove('d-none');
                }
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
});

// Salin ke clipboard
function salin(text) {
    navigator.clipboard.writeText(text).then(() => {
        Swal.fire({ 
            toast: true, position: 'top-end', icon: 'success',
            title: 'Disalin!', showConfirmButton: false, timer: 1500 
        });
    });
}
</script>
@endpush
