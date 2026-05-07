@extends('layouts.publik')

@section('title', 'Form Pengajuan Sewa')

@push('styles')
<style>
    .booking-container { padding-top: 1.5rem; padding-bottom: 4rem; background: #f8f9fc; min-height: 100vh; }
    .card { border: none; border-radius: 12px; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1); margin-bottom: 1.5rem; }
    .card-header { background: #fff; border-bottom: 1px solid #e3e6f0; padding: 1rem 1.25rem; border-radius: 12px 12px 0 0 !important; }
    .card-header h6 { color: #4e73df; font-weight: 700; margin: 0; }
    
    .section-title { color: #4e73df; font-weight: 700; display: flex; align-items: center; gap: 10px; margin-bottom: 1.25rem; font-size: 1.1rem; }
    
    /* Sticky Sidebar Fix */
    @media (min-width: 992px) {
        .sticky-sidebar { position: sticky; top: 20px; z-index: 10; }
    }
    
    /* Fix Dropdown Kepotong */
    select.form-control { 
        white-space: normal; 
        height: auto !important; 
        min-height: 45px; 
        width: 100% !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 16px 12px;
        appearance: none;
        -webkit-appearance: none;
    }
    .form-group { margin-bottom: 1.25rem; }
    label { font-weight: 600; color: #4e73df; font-size: 0.85rem; margin-bottom: 0.5rem; }
    .form-control { border-radius: 8px; border: 1px solid #d1d3e2; padding: 0.6rem 1rem; }
    
    /* Estimasi Styles */
    .calc-box { background: #f8f9fc; border: 1px dashed #4e73df; border-radius: 10px; padding: 15px; }
    .price-tag { font-size: 1.5rem; font-weight: 800; color: #1cc88a; }
    
    .checklist-info { list-style: none; padding-left: 0; margin-bottom: 0; }
    .checklist-info li { font-size: 0.85rem; color: #5a5c69; margin-bottom: 8px; display: flex; align-items: flex-start; gap: 8px; }
    .checklist-info li i { color: #1cc88a; margin-top: 3px; }

    .termin-option { cursor: pointer; transition: all 0.2s; border: 2px solid #eaecf4; border-radius: 10px; padding: 12px; position: relative; }
    .termin-option:hover { border-color: #4e73df; }
    input[name="tipe_pembayaran"]:checked + .termin-option { border-color: #4e73df; background: #f0f3ff; }
    
    /* Info Termin Animation */
    .info-termin { display: none; animation: fadeInTermin 0.3s ease-in; }
    .info-termin.active { display: block; }
    @keyframes fadeInTermin {
        from { opacity: 0; transform: translateY(-10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    
    @media (max-width: 991.98px) {
        .order-mobile-1 { order: 2; }
        .order-mobile-2 { order: 1; }
    }
</style>
@endpush

@section('content')
<div class="booking-container">
    <div class="container">
        @if(session('error') && 
            !str_contains(session('error'), 'NIK') && 
            !str_contains(session('error'), 'HP') &&
            !str_contains(session('error'), 'Nomor'))
        <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong>Booking Ditolak:</strong> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        {{-- Alert untuk Error Real-time (AJAX) --}}
        <div id="ajaxErrorAlert" class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="display:none;">
            <div class="d-flex">
                <i class="fas fa-exclamation-triangle mr-2 mt-1"></i>
                <div>
                    <strong id="ajaxErrorTitle">Peringatan Booking:</strong>
                    <ul id="ajaxErrorList" class="mb-0 pl-3 mt-1 small" style="list-style-type: disc;"></ul>
                </div>
            </div>
            <button type="button" class="close" onclick="$('#ajaxErrorAlert').hide();" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="row">
            {{-- KOLOM KIRI: FORM DATA (col-lg-7) --}}
            <div class="col-lg-7 order-mobile-1">
                <form action="{{ route('user.kantin.booking.store') }}" method="POST" id="bookingForm" enctype="multipart/form-data">
                    @csrf
                    
                    {{-- 1. Data Pribadi --}}
                    <div class="card">
                        <div class="card-body">
                            <h5 class="section-title"><i class="fas fa-user-circle"></i> 1. Data Pribadi</h5>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Nama Lengkap*</label>
                                    <input type="text" name="nama" class="form-control" required placeholder="Sesuai KTP" value="{{ $penyewa->nama_lengkap ?? old('nama') }}">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>NIK (16 Digit)*</label>
                                    <input type="text" name="nik" id="nik" class="form-control" required placeholder="16 Digit NIK" maxlength="16" value="{{ $penyewa->nik ?? old('nik') }}">
                                    <div id="nik-error" class="text-danger small mt-1" style="display:none;">Harus tepat 16 digit angka.</div>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label>Upload Foto KTP*</label>
                                    <div class="custom-file">
                                        <input type="file" name="foto_ktp" class="custom-file-input" id="foto_ktp" accept="image/*" required>
                                        <label class="custom-file-label" for="foto_ktp">Pilih file foto KTP...</label>
                                    </div>
                                    <small class="text-muted">Format: JPG, PNG, JPEG. Max: 2MB</small>
                                    <div id="ktp-preview-container" class="mt-2" style="display:none;">
                                        <img id="ktp-preview" src="#" alt="Preview KTP" style="max-height: 150px; border-radius: 8px; border: 1px solid #ddd;">
                                    </div>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Nomor WhatsApp*</label>
                                    <input type="text" name="no_hp" id="no_hp" 
                                           class="form-control" 
                                           placeholder="08xxxxxxxxx"
                                           value="{{ $penyewa->no_hp ?? old('no_hp') }}">

                                    {{-- Feedback realtime --}}
                                    <div id="hp-feedback" class="mt-1 small" style="min-height:18px"></div>
                                    <div id="hp-error" class="text-danger small mt-1" style="display:none;">Gunakan 10-13 digit angka.</div>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Email (Opsional)</label>
                                    <input type="email" name="email" class="form-control" placeholder="user@email.com" value="{{ old('email') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Data Usaha --}}
                    <div class="card">
                        <div class="card-body">
                            <h5 class="section-title"><i class="fas fa-store"></i> 2. Data Usaha</h5>
                            <div class="row">
                                <div class="col-md-7 form-group">
                                    <label>Nama Usaha/Toko*</label>
                                    <input type="text" name="nama_usaha" class="form-control" required placeholder="Contoh: Kedai Kopi Makmur" value="{{ old('nama_usaha') }}">
                                </div>
                                <div class="col-md-5 form-group">
                                    <label>Jenis Usaha*</label>
                                    <select name="jenis_usaha" class="form-control w-100" required>
                                        <option value="">-- Pilih Jenis --</option>
                                        <option value="Makanan & Minuman">Makanan & Minuman</option>
                                        <option value="Pakaian">Pakaian</option>
                                        <option value="ATK">ATK</option>
                                        <option value="Elektronik">Elektronik</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <div class="col-12 form-group">
                                    <label>Alamat Lengkap Usaha/Tinggal*</label>
                                    <textarea name="alamat" class="form-control" rows="2" required placeholder="Jl. Raya No. XX, Desa, Kec...">{{ old('alamat') }}</textarea>
                                </div>
                                <div class="col-12 form-group mb-0">
                                    <label>Deskripsi Usaha (Opsional)</label>
                                    <textarea name="deskripsi_usaha" class="form-control" rows="2" placeholder="Jelaskan produk/jasa Anda...">{{ old('deskripsi_usaha') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Detail Sewa --}}
                    <div class="card">
                        <div class="card-body">
                            <h5 class="section-title"><i class="fas fa-calendar-alt"></i> 3. Detail Sewa</h5>
                            <div class="form-group">
                                <label>Pilih Unit Ruko/Kantin*</label>
                                <select name="ruko_id" id="ruko_id" class="form-control w-100" required>
                                    <option value="">-- Pilih Unit Tersedia --</option>
                                    @foreach($units as $u)
                                        <option value="{{ $u->id }}" {{ ($ruko && $ruko->id == $u->id) ? 'selected' : '' }} data-harga="{{ $u->harga }}">
                                            {{ $u->kode_unit }} — Rp {{ number_format($u->harga, 0, ',', '.') }}/tahun
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Tanggal Mulai Sewa*</label>
                                    <input type="date" name="tanggal_mulai_sewa" id="tgl_mulai" class="form-control" required min="{{ date('Y-m-d', strtotime('+1 day')) }}" value="{{ old('tanggal_mulai_sewa') }}">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Tanggal Selesai Sewa*</label>
                                    <input type="date" name="tanggal_selesai_sewa" id="tgl_selesai" class="form-control" required value="{{ old('tanggal_selesai_sewa') }}">
                                </div>
                                <div class="col-12 form-group mb-0">
                                    <label>Catatan Tambahan (Opsional)</label>
                                    <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan untuk admin...">{{ old('catatan') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Skema Pembayaran --}}
                    <div class="card">
                        <div class="card-body">
                            <h5 class="section-title"><i class="fas fa-credit-card"></i> 4. Skema & Metode Pembayaran</h5>
                            
                            <label class="mb-2">Pilih Skema Termin*</label>
                            <div class="row mb-4">
                                <div class="col-md-6 mb-2">
                                    <input type="radio" name="tipe_pembayaran" value="1_termin" id="termin1" class="d-none" checked>
                                    <label for="termin1" class="termin-option w-100 mb-0">
                                        <div class="font-weight-bold text-dark">1 Termin</div>
                                        <div class="small text-muted">Lunas di awal (100%)</div>
                                    </label>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <input type="radio" name="tipe_pembayaran" value="2_termin" id="termin2" class="d-none">
                                    <label for="termin2" class="termin-option w-100 mb-0">
                                        <div class="font-weight-bold text-dark">2 Termin</div>
                                        <div class="small text-muted">Cicilan 2x (50% per termin)</div>
                                    </label>
                                </div>

                                {{-- Info Box Termin 1 --}}
                                <div class="col-12 mt-2 info-termin" id="info-termin1">
                                    <div class="bg-light p-3 rounded border-left-success">
                                        <div class="font-weight-bold text-success small mb-2"><i class="fas fa-check-circle mr-1"></i> Pembayaran Lunas di Awal</div>
                                        <div class="small text-muted mb-1">Total yang harus dibayar:</div>
                                        <div class="h5 font-weight-bold text-dark mb-2" id="nominal-lunas">Rp 0</div>
                                        <hr class="my-2">
                                        <div class="small text-muted italic"><i class="fas fa-info-circle mr-1"></i> Pembayaran dilakukan sekaligus penuh sebelum masa sewa dimulai.</div>
                                    </div>
                                </div>

                                {{-- Info Box Termin 2 --}}
                                <div class="col-12 mt-2 info-termin" id="info-termin2">
                                    <div class="bg-light p-3 rounded border-left-primary">
                                        <div class="font-weight-bold text-primary small mb-2"><i class="fas fa-calendar-check mr-1"></i> Pembayaran Dibagi 2 Tahap</div>
                                        <div class="row">
                                            <div class="col-sm-6 mb-2 mb-sm-0">
                                                <div class="small text-muted mb-1">Termin 1 (50%) — Awal:</div>
                                                <div class="font-weight-bold text-dark" id="nominal-termin1">Rp 0</div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="small text-muted mb-1">Termin 2 (50%) — Bulan 6:</div>
                                                <div class="font-weight-bold text-dark" id="nominal-termin2">Rp 0</div>
                                            </div>
                                        </div>
                                        <hr class="my-2">
                                        <div class="small text-muted italic"><i class="fas fa-info-circle mr-1"></i> Termin 2 jatuh tempo 6 bulan setelah tanggal mulai sewa.</div>
                                    </div>
                                </div>
                            </div>

                            <div id="alert-tunai" class="alert alert-info border-0 shadow-sm mb-0" style="display:none; font-size: 0.85rem;">
                                <i class="fas fa-info-circle mr-1 text-primary"></i>
                                Pembayaran tunai dilakukan langsung ke Bendahara BLUD SMK. Admin akan menghubungi Anda setelah disetujui.
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary btn-lg btn-block shadow py-3 font-weight-bold" style="border-radius: 10px;">
                            <i class="fas fa-paper-plane mr-2"></i> AJUKAN PENYEWAAN SEKARANG
                        </button>
                    </div>
                </form>
            </div>

            {{-- KOLOM KANAN: INFO UNIT (col-lg-5 sticky) --}}
            <div class="col-lg-5 order-mobile-2">
                <div class="sticky-sidebar">
                    {{-- Card 1: Informasi Unit --}}
                    <div class="card overflow-hidden">
                        <div id="unit-photo-container">
                            @if($ruko)
                                <img src="{{ $ruko->dokumentasiUnit->first() ? asset('storage/' . str_replace('\\','/',$ruko->dokumentasiUnit->first()->file)) : asset('assets/img/no-image.png') }}" 
                                     class="card-img-top" alt="Foto Unit" style="height: 200px; object-fit: cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="fas fa-store fa-3x text-gray-200"></i>
                                </div>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="font-weight-bold text-dark mb-0" id="info-kode">{{ $ruko->kode_unit ?? '-' }}</h5>
                                <span class="badge badge-primary px-2 py-1" id="info-cat">{{ $ruko->kategori->nama ?? 'Unit' }}</span>
                            </div>
                            <div class="price-tag mb-3" id="info-harga-display">
                                Rp {{ number_format($ruko->harga ?? 0, 0, ',', '.') }}<small class="text-muted text-xs">/tahun</small>
                            </div>
                            
                            @if($ruko)
                                <div class="unit-specs border-top pt-3">
                                    <div class="mb-2">
                                        <label class="detail-label mb-1">Ukuran Unit</label>
                                        <div class="small text-dark font-weight-bold"><i class="fas fa-expand-arrows-alt mr-1 text-primary"></i> {{ $ruko->ukuran_ruko ?? '-' }}</div>
                                    </div>
                                    <div>
                                        <label class="detail-label mb-1">Deskripsi Unit</label>
                                        <div class="small text-muted" style="line-height: 1.4;">
                                            {{ $ruko->deskripsi ?? 'Tidak ada deskripsi detail.' }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Card 2: Estimasi Pembayaran --}}
                    <div class="card">
                        <div class="card-header bg-white">
                            <h6><i class="fas fa-receipt mr-2"></i>Estimasi Pembayaran</h6>
                        </div>
                        <div class="card-body p-3">
                            <div id="placeholder-calc" class="text-center py-4 {{ $ruko ? 'd-none' : '' }}">
                                <p class="text-muted small mb-0">Silakan pilih unit dan tanggal sewa untuk melihat estimasi.</p>
                            </div>
                            
                            <div id="actual-calc" class="{{ $ruko ? '' : 'd-none' }}">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="small text-muted">Durasi Sewa</span>
                                    <span class="small font-weight-bold" id="res-durasi">0 Bulan</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 pb-2 border-bottom">
                                    <span class="small text-muted font-weight-bold">Total Biaya Sewa</span>
                                    <span class="small font-weight-bold text-dark" id="res-total">Rp 0</span>
                                </div>

                                {{-- Info Termin --}}
                                <div id="res-breakdown">
                                    {{-- Jika 1 termin --}}
                                    <div id="res-termin-1-full" class="bg-success text-white p-2 rounded small mb-0">
                                        <div class="d-flex justify-content-between font-weight-bold">
                                            <span>Lunas (100%)</span>
                                            <span id="res-full-price">Rp 0</span>
                                        </div>
                                    </div>
                                    
                                    {{-- Jika 2 termin --}}
                                    <div id="res-termin-2-split" style="display:none;">
                                        <div class="d-flex justify-content-between mb-1 small">
                                            <span>Termin 1 (50%)</span>
                                            <span class="font-weight-bold text-primary" id="res-t1">Rp 0</span>
                                        </div>
                                        <div class="d-flex justify-content-between small">
                                            <span>Termin 2 (50%)</span>
                                            <span class="font-weight-bold text-primary" id="res-t2">Rp 0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 3: Informasi Penting --}}
                    <div class="card">
                        <div class="card-body p-3">
                            <h6 class="font-weight-bold text-dark small mb-3">Informasi Penting</h6>
                            <ul class="checklist-info">
                                <li><i class="fas fa-check-circle"></i> <span>Pengajuan akan diproses dalam 1x24 jam kerja.</span></li>
                                <li><i class="fas fa-check-circle"></i> <span>Link akses dikirim via WhatsApp setelah booking.</span></li>
                                <li><i class="fas fa-check-circle"></i> <span>Pembatalan hanya bisa dilakukan saat status masih pending.</span></li>
                                <li><i class="fas fa-check-circle"></i> <span>Dokumen MOU akan tersedia setelah disetujui admin.</span></li>
                            </ul>
                        </div>
                    </div>

                    {{-- Card 4: Butuh Bantuan --}}
                    <div class="card bg-primary text-white">
                        <div class="card-body p-3">
                            <h6 class="font-weight-bold mb-1 small">Butuh Bantuan?</h6>
                            <p class="small mb-3 opacity-75">Hubungi kami jika ada pertanyaan seputar penyewaan.</p>
                            <a href="https://wa.me/6281234567890" target="_blank" class="btn btn-light btn-sm btn-block font-weight-bold text-primary mb-2">
                                <i class="fab fa-whatsapp mr-1"></i> WhatsApp Admin
                            </a>
                            <div class="text-center x-small mt-2" style="font-size: 0.75rem;">
                                <i class="fas fa-clock mr-1"></i> Senin–Jumat, 08.00–16.00
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let currentHarga = {{ $ruko->harga ?? 0 }};

    function updateEstimasi() {
        const id = $('#ruko_id').val();
        const tglMulai = $('#tgl_mulai').val();
        const tglSelesai = $('#tgl_selesai').val();
        const tipe = $('input[name="tipe_pembayaran"]:checked').val();

        if(!id) {
            $('#placeholder-calc').removeClass('d-none');
            $('#actual-calc').addClass('d-none');
            $('.info-termin').removeClass('active');
            return;
        }

        $('#placeholder-calc').addClass('d-none');
        $('#actual-calc').removeClass('d-none');

        // Toggle Info Termin Box
        $('.info-termin').removeClass('active');
        if (tipe === '1_termin') $('#info-termin1').addClass('active');
        else $('#info-termin2').addClass('active');

        // Default durasi 12 bulan jika tanggal belum lengkap
        let months = 12;

        if(tglMulai && tglSelesai) {
            const start = new Date(tglMulai);
            const end = new Date(tglSelesai);
            
            months = (end.getFullYear() - start.getFullYear()) * 12;
            months -= start.getMonth();
            months += end.getMonth();
            months = months <= 0 ? 0 : months;
        }
            
        $('#res-durasi').text(months + ' Bulan');

        // Hitung total (pro-rata 12 bulan)
        const total = (currentHarga / 12) * months;
        const formattedTotal = 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(total));
        $('#res-total').text(formattedTotal);

        // Update Info Lunas (1 Termin)
        $('#nominal-lunas').text(formattedTotal);

        // Perhitungan Termin
        const half = Math.round(total / 2);
        const formattedHalf = 'Rp ' + new Intl.NumberFormat('id-ID').format(half);

        if(tipe === '2_termin') {
            $('#res-termin-1-full').hide();
            $('#res-termin-2-split').show();
            $('#res-t1').text(formattedHalf);
            $('#res-t2').text(formattedHalf);
        } else {
            $('#res-termin-1-full').show();
            $('#res-termin-2-split').hide();
            $('#res-full-price').text(formattedTotal);
        }

        // Selalu update nominal di boks info termin
        $('#nominal-termin1').text(formattedHalf);
        $('#nominal-termin2').text(formattedHalf);
    }

    // Event: Dropdown Unit Berubah (Reload page to get unit details if not already set)
    $('#ruko_id').on('change', function() {
        const id = $(this).val();
        if(id) {
            // Gunakan url() Blade untuk base-nya, lalu sambungkan ID di JS
            const baseUrl = "{{ url('user/kantin/booking') }}";
            window.location.href = baseUrl + '/' + id;
        } else {
            // Jika dikosongkan, kembali ke katalog
            window.location.href = "{{ route('user.kantin.katalog') }}";
        }
    });

    // Event: Tanggal Mulai berubah -> Auto-fill Tanggal Selesai (+1 Tahun)
    $('#tgl_mulai').on('change', function() {
        const mulaiVal = $(this).val();
        if (mulaiVal) {
            const mulai = new Date(mulaiVal);
            const selesai = new Date(mulai);
            selesai.setFullYear(selesai.getFullYear() + 1);
            
            // Format ke yyyy-mm-dd
            const formatted = selesai.toISOString().split('T')[0];
            $('#tgl_selesai').val(formatted);
            $('#tgl_selesai').attr('min', mulaiVal);
            
            updateEstimasi();
        }
    });

    // Event: Tanggal Selesai & Tipe Pembayaran berubah
    $('#tgl_selesai, input[name="tipe_pembayaran"]').on('change', function() {
        updateEstimasi();
    });

    // Event: Metode Pembayaran
    $('input[name="metode_pembayaran"]').on('change', function() {
        if($(this).val() === 'tunai') {
            $('#alert-tunai').slideDown();
        } else {
            $('#alert-tunai').slideUp();
        }
    });

    // Validasi Real-time (NIK & HP)
    const nikInput = document.getElementById('nik');
    const ajaxAlert = $('#ajaxErrorAlert');
    const ajaxErrorList = $('#ajaxErrorList');
    
    let nikTimer = null;
    let ajaxErrors = {
        nik: '',
        hp: ''
    };

    function renderAjaxErrors() {
        ajaxErrorList.empty();
        let hasError = false;
        
        if (ajaxErrors.nik) {
            ajaxErrorList.append('<li>' + ajaxErrors.nik + '</li>');
            hasError = true;
        }
        if (ajaxErrors.hp) {
            ajaxErrorList.append('<li>' + ajaxErrors.hp + '</li>');
            hasError = true;
        }

        if (hasError) {
            if (ajaxAlert.is(':hidden')) {
                ajaxAlert.fadeIn();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        } else {
            ajaxAlert.fadeOut();
        }
        
        // Update submit button state
        const submitBtn = document.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = hasError;
    }

    function checkNikAvailability() {
        const val = nikInput.value.trim();
        if (val.length !== 16) {
            return;
        }

        fetch("{{ route('user.kantin.check-nik') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ nik: val })
        })
        .then(r => r.json())
        .then(data => {
            if (data.available) {
                ajaxErrors.nik = '';
            } else {
                ajaxErrors.nik = data.message;
            }
            renderAjaxErrors();
        })
        .catch(() => {});
    }

    $('#nik').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        
        if(this.value.length === 16) $('#nik-error').hide();
        else $('#nik-error').show();

        clearTimeout(nikTimer);
        ajaxErrors.nik = ''; // Reset while typing
        renderAjaxErrors();
        nikTimer = setTimeout(checkNikAvailability, 600);
    });

    $('#nik').on('blur', function() {
        checkNikAvailability();
    });

    // Event: Tanggal Selesai minimal tgl mulai + 1 month (asumsi)
    $('#tgl_mulai').on('change', function() {
        $('#tgl_selesai').attr('min', this.value);
    });

    // Event: Preview Foto KTP
    $('#foto_ktp').on('change', function() {
        const file = this.files[0];
        if (file) {
            // Update label text
            $(this).next('.custom-file-label').html(file.name);
            
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#ktp-preview').attr('src', e.target.result);
                $('#ktp-preview-container').show();
            }
            reader.readAsDataURL(file);
        } else {
            $(this).next('.custom-file-label').html('Pilih file foto KTP...');
            $('#ktp-preview-container').hide();
        }
    });

    // TASK: Validasi Realtime Nomor HP
    (function() {
        const inputHp   = document.getElementById('no_hp');
        const feedback  = document.getElementById('hp-feedback');
        
        let hpTimer = null;
        let hpValid = true; 

        if (!inputHp) return;

        inputHp.addEventListener('input', function() {
            // Hanya angka
            this.value = this.value.replace(/[^0-9]/g, '');

            clearTimeout(hpTimer);
            const val = this.value.trim();

            // Reset state
            feedback.innerHTML = '';
            inputHp.classList.remove('is-valid', 'is-invalid');
            
            // Reset error state for HP while typing
            ajaxErrors.hp = '';
            renderAjaxErrors();

            if (val.length < 10) {
                hpValid = true; 
                return;
            }

            // Debounce 600ms
            feedback.innerHTML = '<span class="text-muted"><i class="fas fa-spinner fa-spin mr-1"></i>Memeriksa...</span>';
            
            hpTimer = setTimeout(() => cekHp(val), 600);
        });

        inputHp.addEventListener('blur', function() {
            const val = this.value.trim();
            if (val.length >= 10) {
                clearTimeout(hpTimer);
                cekHp(val);
            }
        });

        function cekHp(noHp) {
            fetch(`{{ route('user.kantin.cek.hp') }}?no_hp=${noHp}`)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'tersedia') {
                        feedback.innerHTML = 
                            '<span class="text-success">' +
                            '<i class="fas fa-check-circle mr-1"></i>' +
                            data.pesan + '</span>';
                        inputHp.classList.remove('is-invalid');
                        inputHp.classList.add('is-valid');
                        hpValid = true;
                        ajaxErrors.hp = '';

                    } else if (data.status === 'aktif') {
                        feedback.innerHTML = ''; // Kosongkan feedback inline
                        inputHp.classList.remove('is-valid');
                        inputHp.classList.add('is-invalid');
                        hpValid = false;
                        ajaxErrors.hp = data.pesan;

                    } else {
                        feedback.innerHTML = '';
                        hpValid = true;
                        ajaxErrors.hp = '';
                    }
                    renderAjaxErrors();
                })
                .catch(() => {
                    feedback.innerHTML = '';
                    hpValid = true;
                    ajaxErrors.hp = '';
                    renderAjaxErrors();
                });
        }

        // Block submit jika HP tidak valid
        const form = inputHp.closest('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!hpValid) {
                    e.preventDefault();
                    inputHp.focus();
                    inputHp.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    Swal.fire({
                        icon: 'warning',
                        title: 'Nomor HP Tidak Dapat Digunakan',
                        text: 'Nomor HP ini masih memiliki sewa aktif. Silakan gunakan nomor lain.',
                        confirmButtonColor: '#3d5af1',
                    });
                }
            });
        }
    })();
    // Initial load
    updateEstimasi();
});
</script>
@endpush
