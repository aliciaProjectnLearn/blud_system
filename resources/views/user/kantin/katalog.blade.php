@extends('layouts.publik')

@section('title', 'Katalog Sewa Kantin & Ruko')

@push('styles')
<style>
    /* ── Hero Banner ── */
    .hero-kantin {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 60%, #1a3a9c 100%);
        border-radius: 16px;
        padding: 48px 40px;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 32px;
        box-shadow: 0 8px 32px rgba(78,115,223,0.30);
    }
    .hero-kantin::before {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 280px; height: 280px;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
    }
    .hero-kantin::after {
        content: '';
        position: absolute;
        bottom: -80px; left: 40%;
        width: 200px; height: 200px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
    }
    .hero-kantin .hero-icon {
        font-size: 5rem;
        opacity: 0.18;
        position: absolute;
        right: 48px; top: 50%;
        transform: translateY(-50%);
    }
    .hero-kantin h1 {
        font-size: 2.2rem;
        font-weight: 800;
        margin-bottom: 10px;
        letter-spacing: -0.5px;
    }
    .hero-kantin p.lead {
        font-size: 1.1rem;
        opacity: 0.9;
        max-width: 520px;
    }

    @media (max-width: 767.98px) {
        .hero-kantin {
            padding: 32px 24px;
            text-align: center;
        }
        .hero-kantin h1 {
            font-size: 1.6rem;
        }
        .hero-kantin p.lead {
            font-size: 0.95rem;
            margin: 0 auto;
        }
        .hero-kantin .mt-4 {
            display: flex;
            justify-content: center;
        }
    }

    /* ── Step Indicator ── */
    .step-indicator {
        display: flex;
        align-items: flex-start;
        justify-content: center;
        gap: 0;
        margin-bottom: 32px;
        flex-wrap: nowrap;
    }
    .step-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
        position: relative;
        z-index: 1;
    }
    .step-item:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 20px;
        left: 50%;
        width: 100%;
        height: 2px;
        background: #e3e6f0;
        z-index: -1;
    }
    .step-circle {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: #4e73df;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.25rem;
        box-shadow: 0 3px 10px rgba(78,115,223,0.3);
        margin-bottom: 8px;
        border: 4px solid #fff;
        line-height: 1;
    }
    .step-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #4e73df;
        text-align: center;
        margin-bottom: 2px;
    }
    .step-desc {
        font-size: 0.7rem;
        color: #858796;
        text-align: center;
        max-width: 100px;
    }

    @media (max-width: 767.98px) {
        .step-indicator {
            flex-direction: column;
            gap: 20px;
            align-items: flex-start;
            padding-left: 20px;
        }
        .step-item {
            flex-direction: row;
            align-items: flex-start;
            text-align: left;
            gap: 20px;
            width: 100%;
        }
        .step-item:not(:last-child)::after {
            left: 26px;
            top: 52px;
            width: 2px;
            height: calc(100% + 20px);
        }
        .step-circle {
            min-width: 52px;
        }
        .step-content {
            display: flex;
            flex-direction: column;
        }
        .step-desc {
            text-align: left;
            max-width: none;
        }
        .step-label {
            text-align: left;
            font-size: 0.85rem;
            margin-bottom: 2px;
        }

        /* Landscape Mobile Modal */
        #modalZoomDenah .modal-dialog {
            max-width: 95vw;
            margin: 10px auto;
        }
        #zoom-content svg {
            transform: rotate(90deg);
            transform-origin: center center;
            width: 80vh;
            height: auto;
            display: block;
            margin: auto;
        }
        #modalZoomDenah .modal-body {
            min-height: 90vw;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #zoom-content {
            padding: 40px;
            width: 100%;
        }
    }

    /* ── Denah SVG Container ── */
    .denah-container {
        background: #f8f9fc;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
        border: 1px solid #e3e6f0;
        position: relative;
        min-height: 180px;
        display: flex;
        align-items: center;
    }
    .btn-zoom-denah {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 5;
        background: white;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        color: #4e73df;
        border: 1px solid #e3e6f0;
        transition: all 0.2s;
    }
    .btn-zoom-denah:hover {
        background: #4e73df;
        color: white;
        text-decoration: none;
    }
    .denah-container svg {
        display: block;
        margin: 0 auto;
        max-width: 100%;
    }
    .unit-building {
        transition: opacity 0.2s;
    }
    .unit-building:hover polygon,
    .unit-building:hover rect.badan {
        opacity: 0.78;
    }

    /* Modal Styling */
    #modalUnitDetail .modal-header { border-bottom: none; }
    #modalUnitDetail .modal-footer { border-top: none; }
    .detail-label { font-size: 0.75rem; color: #858796; text-transform: uppercase; font-weight: 700; }
    .detail-value { font-size: 1.1rem; color: #2e59d9; font-weight: 800; display: block; }

    /* ── Kategori Header ── */
    .kategori-header {
        background: linear-gradient(90deg, #4e73df11, transparent);
        border-left: 4px solid #4e73df;
        border-radius: 0 8px 8px 0;
        padding: 10px 16px;
        margin-bottom: 16px;
    }
    .kategori-header h5 {
        margin: 0;
        font-weight: 800;
        color: #2e59d9;
        font-size: 1rem;
    }

    /* ── Unit Card ── */
    .unit-card {
        border: none;
        border-radius: 14px;
        overflow: hidden;
        transition: transform 0.25s, box-shadow 0.25s;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        height: 100%;
        background: #fff;
    }
    .unit-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 28px rgba(28,200,138,0.18);
    }
    .unit-card .card-img-container {
        position: relative;
        height: 160px;
        overflow: hidden;
        background: #f8f9fc;
    }
    .unit-card .card-img-top {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .unit-card .img-placeholder {
        height: 160px;
        background: linear-gradient(135deg, #1cc88a11, #1cc88a05);
        display: flex; align-items: center; justify-content: center;
    }
    .unit-card .img-placeholder i {
        font-size: 3rem;
        color: #1cc88a33;
    }
    .unit-card .badge-status {
        position: absolute;
        top: 12px;
        right: 12px;
        padding: 4px 10px;
        border-radius: 30px;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        z-index: 2;
    }
    .unit-card .card-body {
        padding: 16px;
    }
    .unit-card .category-label {
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #1cc88a;
        margin-bottom: 4px;
        display: block;
        letter-spacing: 0.5px;
    }
    .unit-card .card-title {
        font-weight: 800;
        font-size: 1.05rem;
        color: #2e59d9;
        margin-bottom: 8px;
    }
    .unit-card .price-tag {
        font-size: 1.1rem;
        font-weight: 800;
        color: #4e73df;
        margin-bottom: 12px;
    }
    .unit-card .price-tag small {
        font-size: 0.78rem;
        color: #858796;
        font-weight: 400;
    }
    .unit-card .btn-booking {
        font-weight: 700;
        font-size: 0.85rem;
        border-radius: 10px;
        padding: 8px;
        transition: all 0.2s;
    }

    /* ── Payment Info Bar ── */
    .payment-info-bar {
        background: #fff;
        border-left: 4px solid #4e73df;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .payment-info-bar h5 {
        font-weight: 800;
        color: #2e59d9;
        font-size: 1rem;
        margin-bottom: 10px;
    }
    .payment-info-bar p {
        font-size: 0.88rem;
        margin-bottom: 0;
        color: #5a5c69;
    }
    .payment-step {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 10px;
    }
    .payment-step i { color: #1cc88a; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- Hero Banner --}}
    <div class="hero-kantin animate__animated animate__fadeIn">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1>Sewa Kantin & Ruko</h1>
                <p class="lead">Pilih unit terbaik untuk usaha Anda. Fasilitas lengkap, lokasi strategis, dan pembayaran fleksibel 2 termin.</p>
                
            </div>
            <div class="col-md-4 text-right d-none d-md-block">
                <i class="fas fa-store hero-icon"></i>
            </div>
        </div>
    </div>

    {{-- Step Indicator --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list-ol mr-2"></i>Langkah-Langkah Penyewaan</h6>
        </div>
        <div class="card-body py-4">
            <div class="step-indicator">
                <div class="step-item">
                    <div class="step-circle"><i class="fas fa-store"></i></div>
                    <div class="step-content">
                        <div class="step-label">Pilih Unit</div>
                        <div class="step-desc">Pilih unit dari denah atau daftar</div>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-circle"><i class="fas fa-file-alt"></i></div>
                    <div class="step-content">
                        <div class="step-label">Isi Form</div>
                        <div class="step-desc">Lengkapi data & tanggal mulai</div>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-circle"><i class="fas fa-credit-card"></i></div>
                    <div class="step-content">
                        <div class="step-label">Pilih Jumlah Termin</div>
                        <div class="step-desc">Terdapat pilihan 1 atau 2 Termin</div>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-circle"><i class="fas fa-check"></i></div>
                    <div class="step-content">
                        <div class="step-label">Sewa Aktif</div>
                        <div class="step-desc">Aktif setelah admin verifikasi</div>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-circle"><i class="fas fa-bell"></i></div>
                    <div class="step-content">
                        <div class="step-label">Pengingat</div>
                        <div class="step-desc">Notif WA H-30 jatuh tempo termin 2 (jika memilih 2 termin)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Info Bar --}}
    <div class="payment-info-bar animate__animated animate__fadeInUp">
        <h5><i class="fas fa-info-circle mr-2"></i>Pilihan Termin Pembayaran</h5>
        <div class="row">
            <div class="col-md-6">
                <p>Anda dapat memilih skema pembayaran yang paling sesuai:</p>
                <div class="payment-step">
                    <i class="fas fa-check-circle"></i>
                    <span><b>1 Termin (Lunas):</b> Pembayaran 100% langsung di awal saat booking.</span>
                </div>
                <div class="payment-step">
                    <i class="fas fa-check-circle"></i>
                    <span><b>2 Termin (Cicilan):</b> 50% di awal sewa, 50% sisa pada bulan ke-6.</span>
                </div>
            </div>
            <div class="col-md-6 border-left d-none d-md-block">
                <p class="small">Sewa akan berstatus <b>Aktif</b> segera setelah pembayaran (Termin 1 atau Lunas) diverifikasi oleh Admin. Untuk pilihan 2 Termin, notifikasi tagihan Termin 2 akan dikirimkan otomatis melalui WhatsApp pada bulan ke-5.</p>
            </div>
        </div>
    </div>

    {{-- Kategori Sections --}}
    @foreach([
        ['id' => 1, 'nama' => 'Kantin Besar', 'icon' => 'fa-utensils', 'svg_id' => 'svg-kantin-besar'],
        ['id' => 2, 'nama' => 'Kantin Container', 'icon' => 'fa-box', 'svg_id' => 'svg-kantin-container'],
        ['id' => 3, 'nama' => 'Ruko Depan', 'icon' => 'fa-building', 'svg_id' => 'svg-ruko-depan']
    ] as $cat)
    <div class="card shadow mb-5">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas {{ $cat['icon'] }} mr-2"></i>{{ $cat['nama'] }}
            </h6>
        </div>
        <div class="card-body">
            {{-- Denah Container --}}
            <p class="text-muted small mb-2"><i class="fas fa-map-marker-alt mr-1"></i> Denah lokasi unit — klik unit <span class="text-success font-weight-bold">hijau</span> untuk menyewa</p>
            
            <button type="button" class="btn btn-outline-primary btn-block mb-3 d-md-none btn-zoom-trigger" 
                data-title="{{ $cat['nama'] }}" data-target="#{{ $cat['svg_id'] }}">
                <i class="fas fa-map mr-1"></i> Lihat Denah {{ $cat['nama'] }}
            </button>

            <div class="denah-container d-none d-md-flex">
                <a href="javascript:void(0)" class="btn-zoom-denah btn-zoom-trigger" data-title="{{ $cat['nama'] }}" data-target="#{{ $cat['svg_id'] }}">
                    <i class="fas fa-search-plus"></i>
                </a>
                <svg id="{{ $cat['svg_id'] }}" width="100%"></svg>
            </div>

            {{-- Card Grid --}}
            <div class="row mt-4">
                @foreach($rukoList->where('kategori_id', $cat['id']) as $ruko)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="unit-card shadow-sm">
                        <div class="card-img-container">
                            @php 
                                $dok = $ruko->dokumentasiUnit->first();
                                $path = $dok ? ($dok->file ?? $dok->path_file ?? '') : '';
                            @endphp
                            @if($path)
                                <img src="{{ asset('storage/' . str_replace('\\','/',$path)) }}" class="card-img-top" alt="{{ $ruko->kode_unit }}">
                            @else
                                <div class="img-placeholder"><i class="fas {{ $cat['icon'] }}"></i></div>
                            @endif
                            <span class="badge badge-status {{ $ruko->status_unit == 'kosong' ? 'badge-success' : 'badge-danger' }}">
                                {{ $ruko->status_unit == 'kosong' ? 'Tersedia' : 'Terisi' }}
                            </span>
                        </div>
                        <div class="card-body">
                            <span class="category-label">{{ $cat['nama'] }}</span>
                            <h5 class="card-title">{{ $ruko->kode_unit }}</h5>
                            <div class="price-tag mb-2">Rp {{ number_format($ruko->harga, 0, ',', '.') }}<small>/tahun</small></div>
                            <div class="small text-muted mb-3"><i class="fas fa-expand-arrows-alt mr-1"></i> {{ $ruko->ukuran_ruko ?? '-' }}</div>
                            @if($ruko->status_unit == 'kosong')
                                <button type="button" class="btn btn-primary btn-block btn-booking btn-sm" onclick="showUnitDetail({{ $ruko->id }})">
                                    <i class="fas fa-calendar-check mr-1"></i> Pilih Unit
                                </button>
                            @else
                                <button class="btn btn-secondary btn-block btn-booking btn-sm" disabled>
                                    <i class="fas fa-times-circle mr-1"></i> Tidak Tersedia
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach

</div>

{{-- Modal Detail Unit (Fixed) --}}
<div class="modal fade" id="modalUnitDetail" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-white pb-0">
                <h5 class="modal-title font-weight-bold text-primary" id="detail_kode_unit">UNIT XXX</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <span class="detail-label">Status Unit</span>
                        <div id="detail_status_badge" class="mt-1"></div>
                    </div>
                    <div class="col-6">
                        <span class="detail-label">Kategori</span>
                        <span id="detail_kategori" class="detail-value text-dark">-</span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <span class="detail-label">Ukuran Unit</span>
                        <span id="detail_ukuran" class="detail-value text-dark" style="font-size: 0.95rem;">-</span>
                    </div>
                </div>

                <div class="mb-4">
                    <span class="detail-label">Deskripsi Unit</span>
                    <div id="detail_deskripsi" class="small text-muted mt-1" style="line-height: 1.5;">
                        Tidak ada deskripsi detail.
                    </div>
                </div>

                <div class="p-3 bg-light rounded border-left-primary">
                    <span class="detail-label">Biaya Sewa</span>
                    <span id="detail_harga" class="detail-value text-primary font-weight-bold" style="font-size: 1.5rem;">Rp 0</span>
                    <small class="text-muted">Per Tahun (Bisa cicil 2 Termin)</small>
                </div>
            </div>
            <div class="modal-footer pt-0">
                <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Tutup</button>
                <div id="booking_button_container"></div>
            </div>
        </div>
    </div>
</div>

<script>
    function showUnitDetail(id) {
        // Loading state
        $('#detail_kode_unit').text('Memuat...');
        $('#modalUnitDetail').modal('show');
        
        $.get(`/user/kantin/unit/${id}/detail`, function(unit) {
            $('#detail_kode_unit').text('UNIT ' + unit.kode_unit);
            $('#detail_kategori').text(unit.kategori.nama);
            $('#detail_ukuran').text(unit.ukuran_ruko || '-');
            $('#detail_deskripsi').text(unit.deskripsi || 'Tidak ada deskripsi detail.');
            $('#detail_harga').text('Rp ' + new Intl.NumberFormat('id-ID').format(unit.harga));
            
            let badgeClass = 'badge-success', badgeText = 'Tersedia';
            if (unit.status_unit !== 'kosong') { badgeClass = 'badge-danger'; badgeText = 'Terisi'; }
            
            $('#detail_status_badge').html(`<span class="badge ${badgeClass} px-3 py-2" style="font-size:0.85rem">${badgeText}</span>`);

            const btnContainer = $('#booking_button_container');
            btnContainer.empty();
            if (unit.status_unit === 'kosong') {
                btnContainer.append(`<a href="/user/kantin/booking/${unit.id}" class="btn btn-primary px-4 shadow">Booking Sekarang</a>`);
            } else {
                btnContainer.append(`<button class="btn btn-secondary" disabled>Unit Tidak Tersedia</button>`);
            }
        }).fail(function() {
            $('#detail_kode_unit').text('Error');
            alert('Gagal mengambil data unit.');
        });
    }
</script>

{{-- Modal Zoom Denah --}}
<div class="modal fade" id="modalZoomDenah" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="zoomModalLabel">Detail Denah</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" style="overflow: auto; background: #f8f9fc; min-height: 400px; display: flex; align-items: center; justify-content: center;">
                <div id="zoom-content" style="width: 100%; padding: 40px;">
                    {{-- SVG akan dipindah ke sini saat diklik --}}
                </div>
            </div>
            <div class="modal-footer bg-light">
                <p class="small text-muted mr-auto mb-0"><i class="fas fa-info-circle mr-1"></i> Gunakan mouse/jari untuk scroll denah jika tidak muat.</p>
                <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if(session('booking_sukses'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: 'Pengajuan Berhasil! 🎉',
            text: '{!! session('booking_pesan') !!}',
            confirmButtonText: 'Oke, Mengerti',
            confirmButtonColor: '#3d5af1',
            allowOutsideClick: false,
        });
    });
</script>
@endif
<script>
$(function() {
    const rukoDataArr = {!! json_encode($rukoDataJs) !!};
    const rukoData = rukoDataArr;
    const bookingUrlBase = "{{ url('user/kantin/booking') }}";

    const COLOR_KOSONG  = '#1cc88a';
    const COLOR_TERISI  = '#e74a3b';
    const COLOR_ROOF_K  = '#17a673';
    const COLOR_ROOF_T  = '#c0392b';

    function svgEl(tag, attrs) {
        const el = document.createElementNS('http://www.w3.org/2000/svg', tag);
        for (let k in attrs) el.setAttribute(k, attrs[k]);
        return el;
    }

    function buatBangunan(svg, x, y, w, h, unit) {
        const isKosong = unit.status_unit === 'kosong';
        const fillBody = isKosong ? COLOR_KOSONG : COLOR_TERISI;
        const fillRoof = isKosong ? COLOR_ROOF_K : COLOR_ROOF_T;
        const roofH    = Math.round(h * 0.45);
        const cx       = x + w / 2;
        const bodyY    = y + roofH;
        const bodyH    = h - roofH;

        const g = svgEl('g', {
            'class'       : 'unit-building',
            'data-id'     : unit.id,
            'data-kode'   : unit.kode_unit,
            'data-status' : unit.status_unit,
            'style'       : `cursor:${isKosong ? 'pointer' : 'not-allowed'}`
        });

        // Teks Kode Unit
        const label = svgEl('text', {
            x: cx, y: y - 5,
            'text-anchor' : 'middle',
            'font-size'   : '9',
            'font-weight' : 'bold',
            fill          : '#4e73df'
        });
        label.textContent = unit.kode_unit;
        g.appendChild(label);

        // Atap
        g.appendChild(svgEl('polygon', {
            points : `${x},${bodyY} ${cx},${y} ${x+w},${bodyY}`,
            fill   : fillRoof, stroke: 'white', 'stroke-width': '1'
        }));

        // Badan
        g.appendChild(svgEl('rect', {
            x, y: bodyY, width: w, height: bodyH,
            fill: fillBody, stroke: 'white', 'stroke-width': '1', rx: '2', class: 'badan'
        }));

        // Jendela
        const winW = Math.round(w * 0.32);
        const winH = Math.round(bodyH * 0.28);
        g.appendChild(svgEl('rect', {
            x: cx - winW/2, y: bodyY + Math.round(bodyH * 0.12),
            width: winW, height: winH, fill: 'rgba(255,255,255,0.4)', rx: '2'
        }));

        // Pintu
        const doorW = Math.round(w * 0.28);
        const doorH = Math.round(bodyH * 0.38);
        g.appendChild(svgEl('rect', {
            x: cx - doorW/2, y: bodyY + bodyH - doorH,
            width: doorW, height: doorH, fill: 'rgba(0,0,0,0.18)', rx: '1'
        }));

        const title = svgEl('title', {});
        title.textContent = `${unit.kode_unit} — ${isKosong ? 'Tersedia' : 'Sudah Terisi'}`;
        g.appendChild(title);

        if (isKosong) {
            g.addEventListener('click', () => {
                window.location.href = `${bookingUrlBase}/${unit.id}`;
            });
        }
        svg.appendChild(g);
    }

    // 1. Kantin Besar
    (function() {
        const svg = document.getElementById('svg-kantin-besar');
        if (!svg) return;
        
        const units = rukoData.filter(r => r.kategori_id == 1).sort((a,b) => b.kode_unit.localeCompare(a.kode_unit));
        const W = 68, H = 55, GAP = 10, startX = 10, startY = 28;
        
        // Perluas viewBox jika ada banyak unit + masjid
        const totalW = startX + (units.length + 1) * (W + GAP) + 20;
        svg.setAttribute('viewBox', `0 0 ${Math.max(820, totalW)} 100`);
        svg.setAttribute('height', '100');

        // Render Unit Kantin
        units.forEach((u, i) => buatBangunan(svg, startX + i*(W+GAP), startY, W, H, u));

        // ── Tambah Masjid di Ujung Kanan ──
        const mX = startX + units.length * (W + GAP) + 10;
        const mY = startY - 5;
        const mW = 80, mH = 60;
        const mCX = mX + mW/2;

        const masjid = svgEl('g', { class: 'landmark', 'style': 'cursor:default' });
        
        // Label
        const ml = svgEl('text', { x: mCX, y: mY - 5, 'text-anchor': 'middle', 'font-size': '10', 'font-weight': 'bold', fill: '#1cc88a' });
        ml.textContent = 'MASJID';
        masjid.appendChild(ml);

        // Badan Masjid
        masjid.appendChild(svgEl('rect', { x: mX, y: mY + 15, width: mW, height: mH - 10, fill: '#f8f9fc', stroke: '#1cc88a', 'stroke-width': '2', rx: '2' }));
        
        // Kubah Utama
        masjid.appendChild(svgEl('path', { 
            d: `M ${mX+10} ${mY+15} Q ${mCX} ${mY-15} ${mX+mW-10} ${mY+15} Z`, 
            fill: '#1cc88a', stroke: 'white', 'stroke-width': '1' 
        }));

        // Pintu Masjid (Lengkung)
        const dW = 20, dH = 25;
        masjid.appendChild(svgEl('path', {
            d: `M ${mCX-dW/2} ${mY+mH+5} L ${mCX-dW/2} ${mY+mH-dH+10} Q ${mCX} ${mY+mH-dH-5} ${mCX+dW/2} ${mY+mH-dH+10} L ${mCX+dW/2} ${mY+mH+5} Z`,
            fill: '#858796', opacity: '0.3'
        }));

        svg.appendChild(masjid);
    })();

    // 2. Kantin Container (REVISI FINAL: Skala Seimbang)
    (function() {
        const svg = document.getElementById('svg-kantin-container');
        if (!svg) return;
        
        svg.setAttribute('viewBox', '0 0 750 550');
        svg.setAttribute('height', '380');

        // ── LANDMARK: Gerbang Belakang (Gapura Estetik) ──
        const gX = 40, gY = 50, gW = 60, gH = 320;
        const gate = svgEl('g', { class: 'landmark' });
        gate.appendChild(svgEl('path', { d: `M ${gX-10} ${gY+40} Q ${gX+30} ${gY-10} ${gX+70} ${gY+40}`, fill: '#4e73df', stroke: '#2e59d9', 'stroke-width': '2' }));
        gate.appendChild(svgEl('rect', { x: gX, y: gY+40, width: 20, height: gH-40, fill: '#858796', rx: 2 }));
        gate.appendChild(svgEl('rect', { x: gX+40, y: gY+40, width: 20, height: gH-40, fill: '#858796', rx: 2 }));
        const gt = svgEl('text', { 
            x: gX + 30, y: gY + gH/2 + 20, 'text-anchor': 'middle', 'font-size': '12', fill: '#4e73df', 'font-weight': 'bold',
            transform: `rotate(-90, ${gX + 30}, ${gY + gH/2 + 20})`
        });
        gt.textContent = 'Gerbang Belakang';
        gate.appendChild(gt);
        svg.appendChild(gate);

        // ── LANDMARK: Parkiran Siswa (Skala Seimbang) ──
        const pk = svgEl('rect', { x: 130, y: 360, width: 130, height: 165, rx: 8, fill: 'none', stroke: '#adb5bd', 'stroke-width': '2', 'stroke-dasharray': '5,5' });
        svg.appendChild(pk);
        const pkText = svgEl('text', { x: 195, y: 442, 'text-anchor': 'middle', 'font-size': '13', fill: '#858796', 'font-weight': 'bold' });
        pkText.textContent = 'Parkiran';
        svg.appendChild(pkText);

        // ── LANDMARK: Jejeran Ruang Kelas (Skala Seimbang) ──
        const rkX = 300, rkY = 380, rkW = 380, rkH = 140;
        const rkGroup = svgEl('g', { class: 'landmark' });
        rkGroup.appendChild(svgEl('rect', { x: rkX, y: rkY, width: rkW, height: rkH, fill: '#f8f9fc', stroke: '#4e73df', 'stroke-width': '1.5', rx: 4 }));
        rkGroup.appendChild(svgEl('rect', { x: rkX + 150, y: rkY + 30, width: 80, height: 45, fill: '#1cc88a', stroke: '#858796', 'stroke-width': '1.5' }));
        const rkText = svgEl('text', { x: rkX + rkW/2, y: rkY + 105, 'text-anchor': 'middle', 'font-size': '16', fill: '#4e73df', 'font-weight': 'bold' });
        rkText.textContent = 'Jejeran Ruang Kelas';
        rkGroup.appendChild(rkText);
        svg.appendChild(rkGroup);

        // ── LANDMARK: Koperasi Siswa (Skala Seimbang) ──
        const kopX = 400, kopY = 240, kopW = 85, kopH = 95;
        const bodyH = 50, roofH = 40;
        const cx = kopX + kopW/2;
        const kop = svgEl('g', { class: 'landmark' });
        kop.appendChild(svgEl('polygon', { points: `${kopX},${kopY+roofH} ${cx},${kopY} ${kopX+kopW},${kopY+roofH}`, fill: '#858796', stroke: 'white' }));
        kop.appendChild(svgEl('rect', { x: kopX, y: kopY+roofH, width: kopW, height: bodyH, fill: '#dee2e6', stroke: '#858796', rx: 2 }));
        const kt = svgEl('text', { x: cx, y: kopY + 75, 'text-anchor': 'middle', 'font-size': '10', fill: '#495057', 'font-weight': 'bold' });
        kt.textContent = 'KOPERASI';
        kop.appendChild(kt);
        svg.appendChild(kop);

        // ── UNIT KANTIN (Skala Seimbang - Atap Segitiga) ──
        const units = rukoData.filter(r => r.kategori_id == 2).sort((a,b) => a.kode_unit.localeCompare(b.kode_unit));
        const uW = 75, uH = 65; // Ukuran seimbang (antara kecil dan besar)
        const pos = [
            {x: 170, y: 50},
            {x: 580, y: 60},
            {x: 295, y: 250},
            {x: 515, y: 250}
        ];

        units.forEach((u, i) => {
            if (!pos[i]) return;
            buatBangunan(svg, pos[i].x, pos[i].y, uW, uH, u);
        });
    })();

    // 3. Ruko Depan
    (function() {
        const svg = document.getElementById('svg-ruko-depan');
        if (!svg) return;
        svg.setAttribute('viewBox', '0 0 660 160');
        svg.setAttribute('height', '160');

        // Koperasi
        svg.appendChild(svgEl('rect', { x: 10, y: 20, width: 110, height: 55, rx: 4, fill: '#858796' }));
        const t1 = svgEl('text', { x: 65, y: 44, 'text-anchor': 'middle', 'font-size': '9', fill: 'white', 'font-weight': 'bold' });
        t1.textContent = 'Koperasi Sekolah';
        svg.appendChild(t1);
        const t2 = svgEl('text', { x: 65, y: 64, 'text-anchor': 'middle', 'font-size': '8', fill: '#eee' });
        t2.textContent = '(Tdk Disewakan)';
        svg.appendChild(t2);

        // Jalan Raya
        svg.appendChild(svgEl('rect', { x: 0, y: 95, width: 660, height: 20, fill: '#adb5bd' }));
        const t3 = svgEl('text', { x: 330, y: 109, 'text-anchor': 'middle', 'font-size': '9', fill: 'white', 'font-weight': 'bold' });
        t3.textContent = 'Jalan Raya';
        svg.appendChild(t3);

        // SMAN 7
        svg.appendChild(svgEl('rect', { x: 0, y: 120, width: 660, height: 35, fill: '#4e73df', opacity: '0.25' }));
        const t4 = svgEl('text', { x: 330, y: 142, 'text-anchor': 'middle', 'font-size': '10', fill: '#2e59d9', 'font-weight': 'bold' });
        t4.textContent = 'SMAN 7 Cirebon';
        svg.appendChild(t4);

        const units = rukoData.filter(r => r.kategori_id == 3).sort((a,b) => a.kode_unit.localeCompare(b.kode_unit));
        const W = 90, H = 55, GAP = 12, startX = 130, startY = 20;
        units.forEach((u, i) => buatBangunan(svg, startX + i*(W+GAP), startY, W, H, u));
    })();

    // Modal Zoom Logic
    $('.btn-zoom-trigger').on('click', function() {
        const svgId = $(this).data('target');
        const title = $(this).data('title');
        const originalSvg = document.querySelector(svgId);
        
        if (originalSvg) {
            const clone = originalSvg.cloneNode(true);
            clone.removeAttribute('width');
            clone.removeAttribute('height');
            clone.style.width = '100%';
            clone.style.height = 'auto';
            clone.style.display = 'block';
            
            // Re-attach listeners to clone for booking
            clone.querySelectorAll('.unit-building').forEach(g => {
                if (g.getAttribute('data-status') === 'kosong') {
                    g.addEventListener('click', () => {
                        window.location.href = `${bookingUrlBase}/${g.getAttribute('data-id')}`;
                    });
                }
            });
            
            $('#zoom-content').empty().append(clone);
            $('#zoomModalLabel').text('Detail Denah: ' + title);
            $('#modalZoomDenah').modal('show');
        }
    });
});
</script>
@endpush
