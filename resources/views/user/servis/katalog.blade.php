@extends('layouts.publik')

@section('title', 'Katalog Layanan Servis')

@push('styles')
    <style>
        /* ── Hero Banner ── */
        .hero-servis {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 60%, #1a3a9c 100%);
            border-radius: 20px;
            padding: 52px 48px;
            color: #fff;
            position: relative;
            overflow: hidden;
            margin-bottom: 28px;
            box-shadow: 0 12px 40px rgba(78, 115, 223, 0.28);
        }

        .hero-servis::before {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 320px; height: 320px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
        }
        .hero-servis::after {
            content: '';
            position: absolute;
            bottom: -60px; left: -40px;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }

        .hero-inner { position: relative; z-index: 2; }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            opacity: 0.75;
            margin-bottom: 14px;
        }

        .hero-servis h1 {
            font-size: 2.4rem;
            font-weight: 900;
            line-height: 1.2;
            margin-bottom: 14px;
        }

        .hero-servis .lead {
            font-size: 1rem;
            opacity: 0.85;
            max-width: 560px;
            line-height: 1.7;
            margin-bottom: 28px;
        }

        .hero-pills { display: flex; flex-wrap: wrap; gap: 10px; }
        .hero-pill {
            background: rgba(255,255,255,0.13);
            border: 1px solid rgba(255,255,255,0.22);
            border-radius: 100px;
            padding: 6px 16px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #fff;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .hero-pill i { font-size: 0.75rem; }

        .hero-icon-right {
            position: absolute;
            right: 48px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 8rem;
            opacity: 0.08;
            z-index: 1;
        }

        /* ── Stats Bar ── */
        .stats-bar {
            background: #fff;
            border-radius: 16px;
            display: flex;
            box-shadow: 0 4px 18px rgba(0,0,0,0.06);
            margin-bottom: 28px;
            overflow: hidden;
            border: 1px solid #e8ecf8;
        }

        .stat-item {
            flex: 1;
            text-align: center;
            padding: 22px 16px;
            border-right: 1px solid #e8ecf8;
            transition: background .2s;
        }
        .stat-item:last-child { border-right: none; }
        .stat-item:hover { background: #f8f9ff; }

        .stat-icon {
            width: 40px; height: 40px;
            border-radius: 10px;
            background: #eef2ff;
            color: #4e73df;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
            margin: 0 auto 10px;
        }
        .stat-icon.green { background: #e6fdf5; color: #1cc88a; }

        .stat-num {
            font-size: 1.65rem;
            font-weight: 900;
            color: #4e73df;
            line-height: 1;
            margin-bottom: 4px;
        }
        .stat-num.green { color: #1cc88a; }

        .stat-lbl {
            font-size: 0.72rem;
            color: #858796;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        /* ── Section Label ── */
        .section-label {
            font-size: 1.15rem;
            font-weight: 800;
            color: #2d3a6b;
            margin-bottom: 4px;
        }
        .section-sub {
            font-size: 0.83rem;
            color: #858796;
            margin-bottom: 20px;
        }

        /* ── Layanan Card ── */
        .layanan-card {
            border: 1px solid #e8ecf8;
            border-radius: 18px;
            background: #fff;
            transition: transform 0.25s, box-shadow 0.25s, border-color 0.25s;
            box-shadow: 0 3px 14px rgba(0,0,0,0.05);
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        .layanan-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #4e73df, #6f8de8);
        }
        .layanan-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 14px 32px rgba(78,115,223,0.14);
            border-color: #c5d0f8;
        }

        .layanan-card .card-body { padding: 22px; }

        .layanan-icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            background: #eef2ff;
            color: #4e73df;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.35rem;
            margin-bottom: 16px;
        }

        .tipe-badge {
            background: #eef2ff;
            color: #4e73df;
            font-size: 0.68rem;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 100px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .duration-txt {
            font-size: 0.78rem;
            color: #858796;
            font-weight: 500;
        }

        .layanan-title {
            font-size: 1rem;
            font-weight: 800;
            color: #2d3a6b;
            margin-bottom: 6px;
        }

        .layanan-desc {
            font-size: 0.83rem;
            color: #858796;
            line-height: 1.6;
        }

        .card-footer-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 16px;
            margin-top: 14px;
            border-top: 1px solid #f0f2fa;
        }

        .price-label {
            font-size: 0.68rem;
            color: #858796;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .price-tag {
            font-size: 1.2rem;
            font-weight: 900;
            color: #1cc88a;
        }

        .btn-pilih {
            background: #4e73df;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 9px 20px;
            font-size: 0.82rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            text-decoration: none;
            transition: background .2s, transform .2s, box-shadow .2s;
        }
        .btn-pilih:hover {
            background: #224abe;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(78,115,223,0.3);
            color: #fff;
            text-decoration: none;
        }

        /* ── Alert ── */
        .custom-alert {
            border-radius: 14px;
            border: none;
            border-left: 4px solid #1cc88a;
            background: #f0fdf8;
            box-shadow: 0 4px 14px rgba(0,0,0,0.05);
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .hero-servis     { padding: 36px 24px; }
            .hero-servis h1  { font-size: 1.8rem; }
            .hero-icon-right { display: none; }
            .stats-bar       { flex-direction: column; }
            .stat-item       { border-right: none; border-bottom: 1px solid #e8ecf8; }
            .stat-item:last-child { border-bottom: none; }
            .trust-strip .d-flex { flex-direction: column; gap: 18px; }
            .trust-divider   { width: 100%; height: 1px; }
        }

        .layanan-card .card-body {
            padding: 16px;
        }

        .layanan-title {
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .layanan-desc {
            font-size: 0.74rem;
            line-height: 1.5;
            margin-bottom: 12px;
        }

        .price-tag {
            font-size: 0.95rem;
        }

        .price-label {
            font-size: 0.62rem;
        }

        .btn-pilih {
            width: 100%;
            justify-content: center;
            padding: 7px 10px;
            font-size: 0.72rem;
            border-radius: 8px;
        }

        .card-footer-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .layanan-icon {
            width: 42px;
            height: 42px;
            font-size: 1rem;
        }

        .tipe-badge {
            font-size: 0.58rem;
            padding: 4px 8px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">

        {{-- HERO --}}
        <div class="hero-servis">
            <i class="fas fa-tools hero-icon-right d-none d-md-block"></i>
            <div class="hero-inner">

                <div class="hero-eyebrow">
                    <i class="fas fa-motorcycle"></i>
                    Bengkel Profesional BLUD
                </div>

                <h1>Katalog Layanan Servis</h1>

                <p class="lead">
                    Pilih layanan perawatan terbaik untuk kendaraan Anda.
                    Staf ahli kami siap memberikan performa maksimal.
                </p>

                <div class="hero-pills">
                    <span class="hero-pill"><i class="fas fa-check-circle"></i> Pengerjaan Cepat</span>
                    <span class="hero-pill"><i class="fas fa-bolt"></i> Suku Cadang Original</span>
                    <span class="hero-pill"><i class="fas fa-shield-alt"></i> Bergaransi</span>
                </div>

            </div>
        </div>

        {{-- STATS --}}
        <div class="stats-bar">

            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-tools"></i></div>
                <div class="stat-num">{{ $layanans->count() }}</div>
                <div class="stat-lbl">Jenis Layanan</div>
            </div>

            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-num">3</div>
                <div class="stat-lbl">Kapasitas / Jam</div>
            </div>

            <div class="stat-item">
                <div class="stat-icon green"><i class="fas fa-shield-alt"></i></div>
                <div class="stat-num green">100%</div>
                <div class="stat-lbl">Suku Cadang Asli</div>
            </div>

        </div>

        {{-- LAYANAN --}}
        <div class="section-label">Pilih Layanan</div>
        <div class="section-sub">Temukan layanan yang sesuai untuk kendaraan Anda</div>

        <div class="row">
            @foreach ($layanans as $l)
                <div class="col-6 col-md-6 col-lg-4 mb-4">
                    <div class="layanan-card">
                        <div class="card-body d-flex flex-column">

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="tipe-badge">{{ $l->tipe_kendaraan }}</span>

                                <i
                                    class="fas {{ strtolower($l->tipe_kendaraan) == 'motor' ? 'fa-motorcycle' : 'fa-car-side' }}"></i>
                            </div>

                            <div class="layanan-title">
                                {{ $l->nama_layanan }}
                            </div>

                            <p class="layanan-desc flex-grow-1">
                                {{ Str::limit($l->deskripsi, 100) }}
                            </p>

                            <div class="card-footer-row">
                                <div>
                                    <div class="price-label">Mulai dari</div>

                                    <div class="price-tag">
                                        Rp {{ number_format($l->harga_estimasi, 0, ',', '.') }}
                                    </div>
                                </div>

                                <a href="{{ route('user.servis.booking', ['layanan_id' => $l->id]) }}"
                                    class="btn-pilih">
                                    Pilih
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ALERT SUCCESS --}}
        @if (session('success'))
            <div class="alert custom-alert alert-dismissible fade show mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle fa-lg mr-3" style="color:#1cc88a;"></i>
                    <div>
                        <strong class="d-block">Berhasil!</strong>
                        {{ session('success') }}
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

    </div>
@endsection