@extends('layouts.app')

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
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #4e73df;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1rem;
        box-shadow: 0 3px 10px rgba(78,115,223,0.3);
        margin-bottom: 8px;
        border: 4px solid #fff;
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
            align-items: center;
            text-align: left;
            gap: 15px;
            width: 100%;
        }
        .step-item:not(:last-child)::after {
            left: 21px;
            top: 42px;
            width: 2px;
            height: calc(100% + 20px);
        }
        .step-desc {
            text-align: left;
            max-width: none;
        }
        .step-label {
            text-align: left;
            font-size: 0.85rem;
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
                <div class="mt-4">
                    <a href="{{ route('user.kantin.dashboard') }}" class="btn btn-light font-weight-bold px-4 py-2 mr-2">
                        <i class="fas fa-chart-line mr-1"></i> Dashboard Saya
                    </a>
                </div>
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
                    <div class="step-circle"><i class="fas fa-store fa-sm"></i></div>
                    <div class="step-label">Pilih Unit</div>
                    <div class="step-desc">Pilih unit dari denah atau daftar</div>
                </div>
                <div class="step-item">
                    <div class="step-circle"><i class="fas fa-file-alt fa-sm"></i></div>
                    <div class="step-label">Isi Form</div>
                    <div class="step-desc">Lengkapi data & tanggal mulai</div>
                </div>
                <div class="step-item">
                    <div class="step-circle"><i class="fas fa-credit-card fa-sm"></i></div>
                    <div class="step-label">Bayar Termin 1</div>
                    <div class="step-desc">Pembayaran 50% untuk aktivasi</div>
                </div>
                <div class="step-item">
                    <div class="step-circle"><i class="fas fa-check fa-sm"></i></div>
                    <div class="step-label">Sewa Aktif</div>
                    <div class="step-desc">Aktif setelah admin verifikasi</div>
                </div>
                <div class="step-item">
                    <div class="step-circle"><i class="fas fa-bell fa-sm"></i></div>
                    <div class="step-label">Pengingat</div>
                    <div class="step-desc">Notif WA H-7 jatuh tempo termin 2</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Info Bar --}}
    <div class="payment-info-bar animate__animated animate__fadeInUp">
        <h5><i class="fas fa-info-circle mr-2"></i>Informasi Pembayaran 2 Termin</h5>
        <div class="row">
            <div class="col-md-6">
                <p>Kini Anda bisa menyewa unit dengan sistem cicilan 2x bayar:</p>
                <div class="payment-step">
                    <i class="fas fa-check-circle"></i>
                    <span><b>Termin 1:</b> 50% saat awal sewa (untuk mengaktifkan unit).</span>
                </div>
                <div class="payment-step">
                    <i class="fas fa-check-circle"></i>
                    <span><b>Termin 2:</b> 50% sisa pembayaran pada bulan ke-6.</span>
                </div>
            </div>
            <div class="col-md-6 border-left d-none d-md-block">
                <p class="small">Sewa akan berstatus <b>Aktif</b> segera setelah pembayaran Termin 1 diverifikasi oleh Admin. Notifikasi tagihan Termin 2 akan dikirimkan otomatis melalui WhatsApp.</p>
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
            <div class="denah-container">
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
                            <div class="price-tag">Rp {{ number_format($ruko->harga, 0, ',', '.') }}<small>/tahun</small></div>
                            @if($ruko->status_unit == 'kosong')
                                <a href="{{ route('user.kantin.booking', $ruko->id) }}" class="btn btn-primary btn-block btn-booking">
                                    <i class="fas fa-calendar-check mr-1"></i> Pilih Unit
                                </a>
                            @else
                                <button class="btn btn-secondary btn-block btn-booking" disabled>
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
        svg.setAttribute('viewBox', '0 0 820 100');
        svg.setAttribute('height', '100');
        const units = rukoData.filter(r => r.kategori_id == 1).sort((a,b) => b.kode_unit.localeCompare(a.kode_unit));
        const W = 68, H = 55, GAP = 10, startX = 10, startY = 28;
        units.forEach((u, i) => buatBangunan(svg, startX + i*(W+GAP), startY, W, H, u));
    })();

    // 2. Kantin Container
    (function() {
        const svg = document.getElementById('svg-kantin-container');
        if (!svg) return;
        svg.setAttribute('viewBox', '0 0 420 300');
        svg.setAttribute('height', '300');
        
        // Landmark Gerbang Belakang
        const gb = svgEl('rect', { x: 40, y: 240, width: 200, height: 40, rx: 4, fill: '#858796' });
        svg.appendChild(gb);
        const txt = svgEl('text', { x: 140, y: 266, 'text-anchor': 'middle', 'font-size': '11', fill: 'white', 'font-weight': 'bold' });
        txt.textContent = 'Gerbang Belakang';
        svg.appendChild(txt);

        const units = rukoData.filter(r => r.kategori_id == 2).sort((a,b) => a.kode_unit.localeCompare(b.kode_unit));
        const pos = [[170, 15], [35, 105], [170, 105], [170, 175], [35, 185]];
        const W = 58, H = 48;
        units.forEach((u, i) => { if (pos[i]) buatBangunan(svg, pos[i][0], pos[i][1], W, H, u); });
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