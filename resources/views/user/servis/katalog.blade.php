@extends('layouts.publik')

@section('title', 'Katalog Layanan Servis')

@push('styles')
    <style>
        /* ── Hero Banner (Tema Biru) ── */
        .hero-servis {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 60%, #1a3a9c 100%);
            border-radius: 16px;
            padding: 48px 40px;
            color: #fff;
            position: relative;
            overflow: hidden;
            margin-bottom: 32px;
            box-shadow: 0 8px 32px rgba(78, 115, 223, 0.3);
        }

        .hero-servis::before {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.06);
        }

        .hero-servis .hero-icon {
            font-size: 5rem;
            opacity: 0.18;
            position: absolute;
            right: 48px;
            top: 50%;
            transform: translateY(-50%);
        }

        /* ── Stats Bar ── */
        .stats-bar {
            background: #fff;
            border-radius: 12px;
            padding: 18px 24px;
            display: flex;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
            margin-bottom: 28px;
        }

        .stats-bar .stat-item {
            flex: 1;
            text-align: center;
            border-right: 1px solid #e3e6f0;
        }

        .stats-bar .stat-item:last-child {
            border-right: none;
        }

        .stats-bar .stat-num {
            font-size: 1.6rem;
            font-weight: 900;
            color: #4e73df;
        }

        .stats-bar .stat-lbl {
            font-size: 0.78rem;
            color: #858796;
            text-transform: uppercase;
        }

        /* ── Layanan Card ── */
        .layanan-card {
            border: none;
            border-radius: 14px;
            transition: transform 0.25s, box-shadow 0.25s;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
            height: 100%;
        }

        .layanan-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 28px rgba(78, 115, 223, 0.15);
        }

        .layanan-card .tipe-badge {
            background: #eef0ff;
            color: #4e73df;
            font-size: 0.7rem;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 20px;
            text-transform: uppercase;
        }

        .price-tag {
            font-size: 1.2rem;
            font-weight: 800;
            color: #1cc88a;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="hero-servis">
            <i class="fas fa-tools hero-icon d-none d-md-block"></i>
            <div style="position:relative; z-index:1">
                <p class="text-uppercase mb-2" style="font-size:0.75rem; letter-spacing:2px; opacity:0.75;">
                    <i class="fas fa-motorcycle mr-1"></i> Bengkel Profesional Blud
                </p>
                <h1>Katalog Layanan Servis</h1>
                <p class="lead mb-4">Pilih layanan perawatan terbaik untuk kendaraan Anda. Teknisi ahli kami siap memberikan
                    performa maksimal.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="#" class="btn btn-outline-light font-weight-bold px-4"> {{-- TODO: ganti ke token-based route --}}
                        <i class="fas fa-history mr-2"></i> Riwayat Servis
                    </a>
                </div>
            </div>
        </div>

        <div class="stats-bar">
            <div class="stat-item">
                <div class="stat-num">{{ $layanans->count() }}</div>
                <div class="stat-lbl">Jenis Layanan</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">3</div>
                <div class="stat-lbl">Kapasitas/Jam</div>
            </div>
            <div class="stat-item">
                <div class="stat-num" style="color:#1cc88a;">Asli</div>
                <div class="stat-lbl">Suku Cadang</div>
            </div>
        </div>

        <div class="row">
            @foreach ($layanans as $l)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card layanan-card">
                        <div class="card-body d-flex flex-column">
                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <span class="tipe-badge">{{ $l->tipe_kendaraan }}</span>
                                <div class="text-muted small"><i class="fas fa-clock mr-1"></i> ~{{ $l->durasi_estimasi }}
                                    mnt</div>
                            </div>
                            <h5 class="font-weight-bold text-gray-800 mb-2">{{ $l->nama_layanan }}</h5>
                            <p class="text-muted small flex-grow-1">{{ Str::limit($l->deskripsi, 100) }}</p>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <div>
                                    <div class="text-xs text-muted">Mulai dari</div>
                                    <div class="price-tag">Rp {{ number_format($l->harga_estimasi, 0, ',', '.') }}</div>
                                </div>
                                <a href="{{ route('user.servis.booking', ['layanan_id' => $l->id]) }}"
                                    class="btn btn-primary btn-sm px-4 font-weight-bold shadow-sm"
                                    style="border-radius:8px;">
                                    Pilih <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        {{-- Notifikasi Sukses --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert"
                style="border-radius: 12px;">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle mr-3 fa-2x"></i>
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
