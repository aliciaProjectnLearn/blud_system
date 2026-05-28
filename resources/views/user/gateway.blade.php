<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><rect width='24' height='24' rx='4' fill='%234e73df'/><g stroke='white' stroke-width='2' stroke-linecap='round'><path d='M12 2v20M2 12h20'/><path d='m6 6 12 12M18 6 6 18'/></g></svg>">

    <title>OneBLUD - Satu Portal, Berbagai Layanan Publik</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Swiper CSS & JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <style>
        * { font-family: 'Poppins', sans-serif; }
        [x-cloak] { display: none !important; }
        body { background-color: #f8f9fc; -webkit-font-smoothing: antialiased; }
        .card-hover { transition: transform .25s, box-shadow .25s; cursor: pointer; }
        .card-hover:hover { transform: translateY(-6px); box-shadow: 0 12px 32px rgba(78,115,223,.15); }
        .swiper-pagination-bullet-active { background-color: #4e73df !important; }
        .testimonialSwiper .swiper-wrapper { align-items: stretch; }
        .testimonialSwiper .swiper-slide { height: auto; opacity: 0; visibility: hidden; transition: all 0.5s ease; padding: 1rem 0; pointer-events: none; }
        .testimonialSwiper .swiper-slide-active { opacity: 1; visibility: visible; pointer-events: auto; }
        .testimonialSwiper .swiper-slide-next, .testimonialSwiper .swiper-slide-prev { opacity: 0.4; visibility: visible; pointer-events: auto; }
        
        @media (max-width: 767px) {
            .swiper-button-prev, .swiper-button-next { display: none !important; }
            .testimonialSwiper { padding-bottom: 2.5rem !important; padding-top: 1rem !important; }
            .testimonialSwiper .swiper-pagination { bottom: 0 !important; }
            .swiper-pagination-bullet { width: 6px; height: 6px; transition: all 0.3s; }
            .swiper-pagination-bullet-active { width: 16px; border-radius: 4px; }
        }
        .swiper-button-next::after, .swiper-button-prev::after { display: none !important; content: '' !important; }
        .star { color: #f6c23e; }
        @media (max-width: 768px) {
            #home h1 { font-size: 1.8rem; }
            #home p { font-size: 0.95rem; }
            .p-8 { padding: 1rem !important; }
            .p-10 { padding: 1.25rem !important; }
            .text-5xl { font-size: 2rem; }
            .text-4xl { font-size: 1.5rem; }
        }
        @media (max-width: 480px) {
            #home h1 { font-size: 1.4rem; }
            .text-5xl { font-size: 1.5rem; }
        }
        .grid-layanan {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: stretch;
            gap: 1.5rem;
        }
        .grid-layanan > a {
            flex: 0 1 340px;
            display: flex;
            flex-direction: column;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        @media (min-width: 769px) {
            .grid-layanan > a {
                min-height: 330px; /* Untuk alignment tombol di desktop */
            }
        }
        @media (max-width: 1024px) {
            .grid-layanan > a {
                flex: 0 1 300px;
            }
        }
        @media (max-width: 768px) {
            .grid-layanan {
                gap: 0.75rem;
            }
            .grid-layanan > a {
                flex: 0 1 calc(50% - 0.5rem);
                min-width: 140px;
                padding: 1.25rem 0.75rem !important;
            }
            .grid-layanan h3 { font-size: 0.9rem !important; margin-bottom: 0.4rem !important; }
            .grid-layanan p { display: none; } 
            .grid-layanan span { 
                display: block !important; 
                padding: 0.4rem !important; 
                font-size: 0.7rem !important; 
                border-radius: 6px !important;
            } 
            .grid-layanan .w-16 { width: 40px !important; height: 40px !important; margin-bottom: 0.75rem !important; }
            .grid-layanan svg { width: 20px !important; height: 20px !important; }
            .grid-layanan > a { min-height: 180px !important; } /* Tinggi seragam di mobile */
        }
        @media (max-width: 480px) {
            .grid-layanan {
                gap: 0.5rem;
            }
            .grid-layanan > a {
                flex: 0 1 calc(50% - 0.3rem);
                padding: 1rem 0.5rem !important;
            }
            .grid-layanan h3 { font-size: 0.8rem !important; }
        }

        /* Premium Modal Styling */
        .modal-content-premium {
            border-radius: 24px;
            border: none;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        }
        .modal-header-premium {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            padding: 2rem;
            border: none;
            position: relative;
        }
        .modal-header-premium .modal-title {
            color: white !important;
            font-weight: 700;
            font-size: 1.5rem;
        }
        .modal-header-premium .close {
            color: white;
            opacity: 0.8;
            text-shadow: none;
            transition: all 0.2s;
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            outline: none;
        }
        .modal-header-premium .close:hover {
            opacity: 1;
            transform: rotate(90deg);
        }
        .premium-input {
            border-radius: 12px !important;
            border: 2px solid #e3e6f0 !important;
            padding: 0.75rem 1rem;
            transition: all 0.2s;
            font-size: 1rem;
        }
        .premium-input:focus {
            border-color: #4e73df !important;
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.1) !important;
        }
        .otp-container {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 2rem 0;
        }
        .otp-field {
            width: 45px;
            height: 55px;
            border-radius: 12px;
            border: 2px solid #e3e6f0;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 700;
            color: #4e73df;
            background-color: #f8f9fc;
            transition: all 0.2s;
        }
        .otp-field:focus {
            border-color: #4e73df;
            background-color: white;
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.1);
            outline: none;
            transform: translateY(-2px);
        }
        .btn-premium {
            border-radius: 12px !important;
            padding: 0.8rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .btn-premium-primary {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%) !important;
            color: white !important;
            border: none !important;
            box-shadow: 0 4px 15px rgba(78, 115, 223, 0.3) !important;
        }
        .btn-premium-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(78, 115, 223, 0.4) !important;
        }
        .btn-premium-primary:active {
            transform: translateY(0);
        }
        .btn-premium-primary:disabled {
            background: #d1d3e2 !important;
            box-shadow: none !important;
            transform: none;
        }
        .status-icon-box {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
        }
        .success-glow {
            background: rgba(28, 200, 138, 0.1);
            color: #1cc88a;
            box-shadow: 0 0 20px rgba(28, 200, 138, 0.2);
        }
        .warning-glow {
            background: rgba(246, 194, 62, 0.1);
            color: #f6c23e;
            box-shadow: 0 0 20px rgba(246, 194, 62, 0.2);
        }
        .alert-danger-soft {
            background-color: #fff5f5;
            color: #e53e3e;
            border: 1px solid #fed7d7;
            border-radius: 12px;
            padding: 0.75rem 1rem;
        }

        /* New Hero Premium Style - BLUE THEME */
        .hero-premium {
            background: linear-gradient(135deg, #1A73E8 0%, #0D47A1 100%);
            position: relative;
            padding: 80px 0 160px;
            color: white;
            overflow: hidden;
        }
        .hero-premium .container-custom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .hero-text {
            flex: 1;
            max-width: 600px;
            z-index: 2;
            text-align: left;
        }
        .hero-eyebrow {
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 2px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            opacity: 0.95;
        }
        .hero-title {
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1.5;
            margin-bottom: 20px;
            text-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .hero-description {
            font-size: 1.15rem;
            margin-bottom: 40px;
            opacity: 0.9;
            line-height: 1.6;
        }
        .btn-hero {
            background: white;
            color: #1A73E8;
            padding: 16px 45px;
            border-radius: 50px;
            font-weight: 700;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            font-size: 1.1rem;
        }
        .btn-hero:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 15px 35px rgba(0,0,0,0.25);
            color: #0D47A1;
            text-decoration: none;
            background-color: #f8f9fc;
        }
        .btn-outline-bottom {
            color: white;
            padding: 10px 0;
            font-weight: 600;
            text-decoration: none;
            border-bottom: 2px solid rgba(255, 255, 255, 0.6);
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
        }
        .btn-outline-bottom:hover {
            color: white;
            border-bottom-color: white;
            text-decoration: none;
            padding-left: 5px;
        }
        .hero-image {
            flex: 1;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            z-index: 2;
            perspective: 1000px;
        }
        .master-hub {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 40px;
            padding: 40px;
            width: 500px;
            box-shadow: 0 40px 100px rgba(0,0,0,0.3);
            transform: rotateY(-15deg) rotateX(10deg);
            animation: floatMaster 8s ease-in-out infinite;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .hub-item {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 15px;
            width: calc(33.33% - 14px);
            min-width: 100px;
            text-align: center;
            color: white;
            transition: all 0.3s;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .hub-item:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateZ(20px);
        }
        .hub-item i {
            font-size: 2rem;
            margin-bottom: 12px;
            display: block;
        }
        .hub-item span {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        @keyframes floatMaster {
            0%, 100% { transform: rotateY(-15deg) rotateX(10deg) translateY(0); }
            50% { transform: rotateY(-10deg) rotateX(5deg) translateY(-30px); }
        }

        @media (max-width: 991px) {
            .hero-image {
                display: none;
            }
            .hero-premium .container-custom {
                flex-direction: column;
                text-align: right;
                padding: 60px 20px;
            }
            .hero-text {
                text-align: right;
                margin-bottom: 0;
                max-width: 100%;
            }
            .hero-title {
                font-size: 2.2rem;
            }
        }

        /* Responsive Grids */
        .grid-keunggulan {
            display: grid;
            gap: 1.5rem;
        }

        @media (max-width: 768px) {
            .grid-keunggulan {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.75rem;
            }
            /* Specific overrides for Keunggulan cards */
            .grid-keunggulan .bg-white {
                padding: 1.25rem 0.75rem !important;
            }
            .grid-keunggulan h3 {
                font-size: 0.9rem !important;
                margin-bottom: 0.5rem !important;
            }
            .grid-keunggulan .w-20 {
                width: 3rem !important;
                height: 3rem !important;
                margin-bottom: 0.75rem !important;
            }
            .grid-keunggulan svg {
                width: 20px !important;
                height: 20px !important;
            }
            .grid-keunggulan p {
                display: none; 
            }

            /* Center last item if odd (for keunggulan) */
            .grid-keunggulan > div:last-child:nth-child(odd) {
                grid-column: 1 / span 2;
                justify-self: center;
                width: 100%;
                max-width: 100%;
            }
            .grid-keunggulan .text-left { text-align: center !important; }
        }
        @media (min-width: 769px) {
            .grid-keunggulan { grid-template-columns: repeat(3, 1fr); }
        }

        @media (max-width: 991px) {
            .hero-image {
                height: 250px;
                width: 100%;
            }
            .floating-element i { font-size: 2.2rem; }
            .el-5, .el-6, .el-7 { display: none; } /* Hide extra icons on mobile to avoid clutter */
            .hero-premium .container-custom {
                flex-direction: column;
                text-align: center;
                padding: 40px 20px 80px;
            }
            .hero-text {
                text-align: left;
                margin-bottom: 20px;
                max-width: 100%;
            }
            .hero-title {
                font-size: 2rem;
            }
        }
        .wave-bottom {
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            line-height: 0;
            z-index: 5;
        }
        .wave-bottom svg {
            display: block;
            width: 100%;
            height: 140px;
            /* Force overlap and scaling to eliminate subpixel gap */
            transform: translateY(2px) scale(1.02);
            transform-origin: bottom center;
        }
        .wave-bottom .shape-fill {
            fill: #f8f9fc;
        }
        #layanan {
            position: relative;
            z-index: 6;
            margin-top: -1px; /* Pull up to cover any potential gap */
        }
    </style>
</head>
<body class="min-h-screen">

    {{-- Navbar --}}
    <header class="sticky top-0 z-50 bg-white shadow-sm border-b border-gray-200" x-data="{ open: false }">
        <nav class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color:#4e73df;">
                    <svg class="text-white" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2v20M2 12h20"/><path d="m6 6 12 12M18 6 6 18"/>
                    </svg>
                </div>
                <span class="font-bold text-xl" style="color:#4e73df;">OneBLUD</span>
            </div>

            {{-- Jalur Publik: Tidak ada tombol login/dashboard di sini --}}
            <button type="button" 
                @click="$dispatch('open-cek-booking')"
                class="inline-flex items-center gap-2 text-sm font-semibold text-white 
                       px-4 py-2 rounded-lg transition-all hover:opacity-90 active:scale-95"
                style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); 
                       box-shadow: 0 4px 12px rgba(78,115,223,0.35);">
                <i class="fas fa-search text-xs"></i>
                Cek Booking
            </button>
 
        </nav>
    </header>

    {{-- Hero Premium --}}
    <section id="home" class="hero-premium">
        <div class="container-custom">
            <div class="hero-text">
                <div class="hero-eyebrow">{{ $cms['hero_eyebrow']->value ?? 'SATU TEMPAT UNTUK SEMUA KEBUTUHAN' }}</div>
                <h1 class="hero-title">{{ $cms['hero_title']->value ?? 'Booking Layanan Kapan Saja, Dari Mana Saja.' }}</h1>
                <p class="hero-description">
                    {{ $cms['hero_subtitle']->value ?? 'Nikmati Kemudahan Dalam Melakukan Booking Online di Berbagai Layanan.' }}
                </p>
                <div class="flex flex-wrap gap-4 items-center">
                    <a href="#layanan" class="btn-hero">
                        {{ $cms['hero_cta_text']->value ?? 'Jelajahi Layanan' }} <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </a>
                </div>
            </div>
            <div class="hero-image">
                <div class="master-hub">
                    @foreach($layanans->take(5) as $layanan)
                    <div class="hub-item">
                        <svg class="text-white" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin: 0 auto 12px; display: block;">
                            @if(\Illuminate\Support\Str::startsWith(trim($layanan->icon_svg), '<'))
                                {!! $layanan->icon_svg !!}
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $layanan->icon_svg }}" />
                            @endif
                        </svg>
                        <span>{{ Str::limit($layanan->nama_layanan, 8) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="wave-bottom">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113,-1.11,1200,0V120H0Z" class="shape-fill"></path>
            </svg>
        </div>
    </section>

    {{-- Layanan --}}
    <section id="layanan" class="py-24 bg-[#f8f9fc]">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-6" style="color:#4e73df;">{{ $cms['layanan_heading']->value ?? 'Layanan Kami' }}</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">{{ $cms['layanan_subheading']->value ?? 'Temukan layanan yang sesuai dengan kebutuhan Anda' }}</p>
            </div>
            <div class="grid-layanan">

                @foreach($layanans as $layanan)
                <a href="{{ ($layanan->route_name && Route::has($layanan->route_name)) ? route($layanan->route_name) : ($layanan->url ?? '#') }}" class="card-hover block bg-white p-6 rounded-2xl shadow-sm border-t-4 border-[#4e73df] text-center flex flex-col h-full no-underline">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm" style="background-color:#4e73df;">
                        @if($layanan->icon_class)
                            <i class="{{ $layanan->icon_class }} text-white" style="font-size: 1.75rem;"></i>
                        @else
                            <svg class="text-white" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                @if(\Illuminate\Support\Str::startsWith(trim($layanan->icon_svg), '<'))
                                    {!! $layanan->icon_svg !!}
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $layanan->icon_svg }}" />
                                @endif
                            </svg>
                        @endif
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="color:#4e73df;">{{ $layanan->nama_layanan }}</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed text-sm flex-grow">{{ $layanan->deskripsi }}</p>
                    <span class="mt-auto w-full py-2 rounded-lg text-white font-medium text-sm text-center block" style="background-color:#4e73df;">
                        Lihat Layanan
                    </span>
                </a>
                @endforeach

            </div>
        </div>
    </section>

    {{-- Keunggulan --}}
    <section id="tentang" class="py-12">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-6" style="color:#4e73df;">{{ $cms['keunggulan_heading']->value ?? 'Mengapa Memilih Kami?' }}</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">{{ $cms['keunggulan_subheading']->value ?? 'Kami berkomitmen memberikan layanan cepat, terintegrasi, dan transparan.' }}</p>
            </div>
            <div class="grid-keunggulan mb-24">
                @foreach($keunggulan as $item)
                <div class="bg-white p-8 rounded-2xl shadow-sm border-l-4 border-[#4e73df] text-center flex flex-col h-full hover:shadow-md transition-shadow">
                    <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm" style="background-color:#4e73df;">
                        <svg class="text-white" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            @if(\Illuminate\Support\Str::startsWith(trim($item->icon_svg), '<'))
                                {!! $item->icon_svg !!}
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item->icon_svg }}" />
                            @endif
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="color:#4e73df;">{{ $item->judul }}</h3>
                    <p class="text-gray-600 leading-relaxed flex-grow text-sm">{{ $item->deskripsi }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Testimoni — Swiper Carousel --}}
    @if($testimoni->isNotEmpty())
    <section class="py-12 bg-[#f8f9fc]">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold mb-4" style="color:#4e73df;">Kata Mereka</h2>
                <p class="text-gray-600 max-w-xl mx-auto">Lihat testimoni dari para pengguna layanan kami.</p>
            </div>
            
            <div class="swiper testimonialSwiper relative px-0 md:px-14 pb-10 pt-4">
                <div class="swiper-wrapper">
                    @foreach($testimoni as $t)
                    <div class="swiper-slide">
                        <div class="bg-white rounded-2xl p-6 md:p-8 shadow-[0_4px_20px_rgba(0,0,0,0.05)] h-full flex flex-col border border-gray-50 relative group">
                            @if($t->status === 'pending')
                                <span class="absolute top-4 right-4 bg-yellow-50 text-yellow-600 border border-yellow-200 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">Pending</span>
                            @endif
                            <div class="flex items-center gap-3 md:gap-4 mb-4">
                                <div class="w-10 h-10 md:w-12 md:h-12 rounded-full flex items-center justify-center text-white font-bold text-base flex-shrink-0 shadow-sm" style="background-color:#4e73df;">
                                    {{ strtoupper(substr($t->user->name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-gray-800 text-sm md:text-base leading-tight">{{ $t->user->name ?? 'Pengguna' }}</div>
                                    <div class="text-[10px] md:text-xs text-gray-400 mt-1 font-semibold uppercase tracking-wider">Pelanggan</div>
                                </div>
                            </div>
                            <div class="mb-3 md:mb-4">
                                @for($i = 0; $i < $t->rating; $i++)<span class="text-yellow-400 text-sm md:text-base">★</span>@endfor
                                @for($i = $t->rating; $i < 5; $i++)<span class="text-gray-200 text-sm md:text-base">★</span>@endfor
                            </div>
                            <p class="text-gray-600 text-[13px] md:text-sm leading-relaxed flex-grow line-clamp-3 mb-4 md:mb-6">"{{ $t->content }}"</p>
                            <div class="mt-auto pt-3 md:pt-4 border-t border-gray-100 text-[10px] md:text-xs text-gray-400 font-medium uppercase tracking-wide">
                                {{ \Carbon\Carbon::parse($t->created_at)->translatedFormat('d M Y') }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Swiper Pagination -->
                <div class="swiper-pagination !bottom-0"></div>
                
                <!-- Swiper Navigation -->
                <div class="swiper-button-prev group !w-12 !h-12 bg-white rounded-full shadow-[0_4px_15px_rgba(0,0,0,0.1)] border border-gray-100 !left-0 md:!left-2 flex items-center justify-center hover:bg-[#4e73df] transition-all">
                    <svg class="w-5 h-5 text-[#4e73df] group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                </div>
                <div class="swiper-button-next group !w-12 !h-12 bg-white rounded-full shadow-[0_4px_15px_rgba(0,0,0,0.1)] border border-gray-100 !right-0 md:!right-2 flex items-center justify-center hover:bg-[#4e73df] transition-all">
                    <svg class="w-5 h-5 text-[#4e73df] group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if(typeof Swiper !== 'undefined') {
                new Swiper('.testimonialSwiper', {
                    effect: 'coverflow',
                    grabCursor: true,
                    centeredSlides: true,
                    loop: true,
                    coverflowEffect: {
                        rotate: 0,
                        stretch: 0,
                        depth: 150,
                        modifier: 1.5,
                        slideShadows: false,
                    },
                    autoplay: {
                        delay: 4000,
                        disableOnInteraction: false,
                    },
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                    },
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                    breakpoints: {
                        320: {
                            direction: 'horizontal',
                            slidesPerView: 1.2,
                        },
                        768: {
                            direction: 'horizontal',
                            slidesPerView: 2,
                        },
                        1024: {
                            direction: 'horizontal',
                            slidesPerView: 3,
                        }
                    }
                });
            }
        });
    </script>
    @endif

    {{-- Footer --}}
    <footer id="kontak" class="py-16" style="background-color:#4e73df;">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-12">
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-white/20">
                            <svg class="text-white" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2v20M2 12h20"/><path d="m6 6 12 12M18 6 6 18"/>
                            </svg>
                        </div>
                        <span class="font-bold text-2xl text-white">{{ $cms['footer_brand']->value ?? 'BLUD PORTAL' }}</span>
                    </div>
                    <p class="text-gray-300 leading-relaxed text-sm">{{ $cms['footer_tagline']->value ?? 'Platform terintegrasi untuk layanan publik SMKN 1 Cirebon yang lebih baik dan efisien.' }}</p>
                </div>
                <div>
                    <h4 class="font-bold text-white text-xl mb-6">Tautan Cepat</h4>
                    <ul class="space-y-3 text-sm">
                        @foreach(['footer_link_1','footer_link_2','footer_link_3','footer_link_4'] as $key)
                            @if(!empty($cms[$key]->value))
                            <li><a href="#" class="text-gray-300 hover:text-white transition-colors">
                                {{ $cms[$key]->value }}
                            </a></li>
                            @endif
                        @endforeach
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-white text-xl mb-6">Ikuti Kami</h4>
                    <div class="flex gap-3">
                        @foreach([
                            'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z',
                            'M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z',
                            'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z',
                        ] as $svg)
                        <a href="#" class="w-10 h-10 rounded-lg flex items-center justify-center bg-white/20 hover:bg-white/30 transition-colors">
                            <svg class="text-white w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="{{ $svg }}"/></svg>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="border-t border-blue-400 pt-8 text-center">
                <p class="text-gray-300 text-sm">{{ $cms['footer_copyright']->value ?? '© ' . date('Y') . ' BLUD SMKN 1 Cirebon. Semua hak dilindungi undang-undang.' }}</p>
            </div>
        </div>
    </footer>

    <!-- Modal Cek Booking -->
    <!-- Modal Cek Booking — Alpine.js -->
<div 
    x-data="cekBookingModal()"
    @open-cek-booking.window="buka()"
    x-show="tampil"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="background: rgba(0,0,0,0.5);"
    @click.self="tutup()">

    <div 
        x-show="tampil"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="bg-white w-full rounded-2xl shadow-2xl overflow-hidden"
        style="max-width: 420px;">

        {{-- Header --}}
        <div class="text-white p-6 relative"
             style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
            <h5 class="font-bold text-xl mb-1">
                <i class="fas fa-search mr-2"></i> Cek Status Booking
            </h5>
            <p class="text-sm mb-0" style="opacity: 0.85;">
                Masukkan nomor WhatsApp untuk melihat booking Anda
            </p>
            <button @click="tutup()" 
                    class="text-white hover:rotate-90 transition-all border-0 bg-transparent"
                    style="position: absolute; top: 1.25rem; right: 1.25rem; opacity: 0.7; font-size: 1.5rem; line-height: 1; outline: none; cursor: pointer;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-6">

            {{-- Step 1: Input HP --}}
            <div x-show="step === 'hp'">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3"
                         style="background: linear-gradient(135deg, #4e73df, #224abe);">
                        <i class="fab fa-whatsapp text-white text-2xl"></i>
                    </div>
                    <p class="text-gray-500 text-sm">
                        Masukkan nomor WhatsApp yang terdaftar<br>untuk menerima kode OTP
                    </p>
                </div>

                <div x-show="alertHp" x-text="alertHp"
                     class="mb-3 p-3 rounded-lg text-sm"
                     :class="alertHpType === 'danger' ? 'bg-red-50 text-red-600 border border-red-200' : 'bg-green-50 text-green-600 border border-green-200'">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor WhatsApp</label>
                    <div class="flex">
                        <span class="flex items-center px-3 bg-gray-100 border border-r-0 border-gray-300 rounded-l-xl text-sm font-bold text-gray-600">+62</span>
                        <input type="text" x-model="noHp"
                               @input="noHp = noHp.replace(/\D/g, '').replace(/^0+/, '').replace(/^62/, '')"
                               @keyup.enter="kirimOtp()"
                               class="flex-1 border border-gray-300 rounded-r-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                               placeholder="812xxxx"
                               inputmode="numeric"
                               maxlength="13">
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Contoh: 81234567890</p>
                </div>

                <button @click="kirimOtp()" 
                        :disabled="loading"
                        class="w-full py-3 px-4 rounded-xl text-white font-semibold transition-all"
                        style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                    <span x-show="!loading"><i class="fas fa-paper-plane mr-2"></i> Dapatkan Kode OTP</span>
                    <span x-show="loading"><i class="fas fa-spinner fa-spin mr-2"></i> Mengirim...</span>
                </button>
            </div>

            {{-- Step 2: Input OTP --}}
            <div x-show="step === 'otp'">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3"
                         style="background: #eef0fb;">
                        <i class="fas fa-shield-alt text-2xl" style="color: #4e73df;"></i>
                    </div>
                    <p class="font-semibold text-gray-800 mb-1">Verifikasi OTP</p>
                    <p class="text-gray-500 text-sm">
                        Jika nomor Anda terdaftar, kode OTP telah dikirim ke WhatsApp.<br>
                        <span class="text-xs text-gray-400">
                            Tidak menerima kode? Pastikan nomor terdaftar di sistem.
                        </span>
                    </p>
                </div>

                <div x-show="alertOtp" x-text="alertOtp"
                     class="mb-3 p-3 rounded-lg text-sm"
                     :class="alertOtpType === 'danger' ? 'bg-red-50 text-red-600 border border-red-200' : 'bg-green-50 text-green-600 border border-green-200'">
                </div>

                {{-- 6 OTP boxes --}}
                <div class="flex justify-center mb-4" style="gap: 8px;">
                    <template x-for="(digit, idx) in otpDigits" :key="idx">
                        <input type="text"
                               class="text-center font-bold border-2 rounded-xl transition-all focus:outline-none"
                               :class="digit ? 'border-blue-500 bg-blue-50' : 'border-gray-200 bg-gray-50'"
                               style="width: 40px; height: 46px; font-size: 1.2rem; color: #4e73df;"
                               maxlength="1"
                               inputmode="numeric"
                               :id="'otp-alpine-' + idx"
                               @input="handleOtpInput($event, idx)"
                               @keydown="handleOtpKeydown($event, idx)"
                               @paste.prevent="handleOtpPaste($event)">
                    </template>
                </div>

                <div class="text-center mb-4">
                    <span class="text-sm px-3 py-1 rounded-full border text-gray-500"
                          :class="otpSisa <= 30 ? 'border-red-300 text-red-500' : 'border-gray-200'">
                        <i class="fas fa-clock mr-1"></i>
                        Berlaku: <span x-text="formatTime(otpSisa)"></span>
                    </span>
                </div>

                <button @click="verifOtp()"
                        :disabled="otpDigits.join('').length < 6 || loading"
                        class="w-full py-3 px-4 rounded-xl text-white font-semibold transition-all mb-3 disabled:opacity-50"
                        style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                    <span x-show="!loading"><i class="fas fa-check-circle mr-2"></i> Verifikasi & Lihat Booking</span>
                    <span x-show="loading"><i class="fas fa-spinner fa-spin mr-2"></i> Memverifikasi...</span>
                </button>

                <div class="flex justify-center items-center gap-2 text-sm">
                    <button @click="reset()" 
                            class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-arrow-left mr-1"></i> Ganti nomor
                    </button>
                    <span class="text-gray-300">|</span>
                    <button @click="resendOtp()" 
                            :disabled="resendSisa > 0"
                            class="text-blue-500 disabled:text-gray-400 disabled:cursor-not-allowed">
                        <span x-show="resendSisa > 0">Kirim Ulang (<span x-text="resendSisa"></span>s)</span>
                        <span x-show="resendSisa === 0">Kirim Ulang</span>
                    </button>
                </div>
            </div>

        </div> 
    </div>
</div>

    <script>
function cekBookingModal() {
    return {
        tampil: false,
        step: 'hp',
        noHp: '',
        otpDigits: ['','','','','',''],
        loading: false,
        alertHp: '',
        alertHpType: 'danger',
        alertOtp: '',
        alertOtpType: 'danger',
        otpSisa: 180,
        resendSisa: 60,
        otpTimer: null,
        resendTimer: null,

        buka() {
            this.tampil = true;
            document.body.style.overflow = 'hidden';

            // Jika step otp tapi timer sudah habis → reset ke hp
            if (this.step === 'otp' && this.otpSisa <= 0) {
                this.resetKeHp();
            }
        },

        tutup() {
            this.tampil = false;
            document.body.style.overflow = '';
        },

        reset() {
            this.resetKeHp();
            this.noHp = '';
            this.alertHp = '';
        },

        resetKeHp() {
            this.step = 'hp';
            this.otpDigits = ['','','','','',''];
            this.alertOtp = '';
            clearInterval(this.otpTimer);
            clearInterval(this.resendTimer);
            this.otpSisa = 180;
            this.resendSisa = 60;
        },

        formatTime(s) {
            return Math.floor(s/60) + ':' + String(s%60).padStart(2,'0');
        },

        handleOtpInput(e, idx) {
            const val = e.target.value.replace(/\D/g,'');
            e.target.value = val;
            this.otpDigits[idx] = val;
            if (val && idx < 5) {
                document.getElementById('otp-alpine-' + (idx+1))?.focus();
            }
        },

        handleOtpKeydown(e, idx) {
            if (e.key === 'Backspace' && !this.otpDigits[idx] && idx > 0) {
                this.otpDigits[idx-1] = '';
                document.getElementById('otp-alpine-' + (idx-1))?.focus();
            }
        },

        handleOtpPaste(e) {
            const text = (e.clipboardData||window.clipboardData).getData('text').replace(/\D/g,'');
            text.split('').forEach((ch, i) => {
                if (i < 6) this.otpDigits[i] = ch;
            });
            // Update input values
            this.$nextTick(() => {
                this.otpDigits.forEach((d, i) => {
                    const el = document.getElementById('otp-alpine-' + i);
                    if (el) el.value = d;
                });
            });
        },

        async kirimOtp() {
            const hp = this.noHp.replace(/\D/g,'');
            if (!hp || hp.length < 9) {
                this.alertHp = 'Masukkan nomor WhatsApp yang valid.';
                this.alertHpType = 'danger';
                return;
            }

            this.loading = true;
            this.alertHp = '';

            try {
                // Normalize HP: remove leading 0 if present, then ensure 62 prefix
                let normalizedHp = hp;
                if (normalizedHp.startsWith('0')) {
                    normalizedHp = '62' + normalizedHp.substring(1);
                } else if (!normalizedHp.startsWith('62')) {
                    normalizedHp = '62' + normalizedHp;
                }

                const res = await fetch('{{ route("user.cek.booking.kirim-otp") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify({ no_hp: normalizedHp })
                });
                const data = await res.json();

                if (data.success) {
                    this.alertHp = '';
                    this.step = 'otp';
                    this.startOtpTimer();
                    this.startResendTimer();
                } else {
                    // Bagian ini sekarang jarang tersentuh karena controller selalu return sukses
                    this.alertHp = data.message || 'Terjadi kesalahan. Silakan coba lagi.';
                    this.alertHpType = 'danger';
                }
            } catch(e) {
                this.alertHp = 'Terjadi kesalahan. Coba lagi.';
                this.alertHpType = 'danger';
            }

            this.loading = false;
        },

        async verifOtp() {
            const otp = this.otpDigits.join('');
            if (otp.length < 6) return;

            this.loading = true;
            this.alertOtp = '';

            try {
                let hp = this.noHp.replace(/\D/g,'');
                let normalizedHp = hp;
                if (normalizedHp.startsWith('0')) {
                    normalizedHp = '62' + normalizedHp.substring(1);
                } else if (!normalizedHp.startsWith('62')) {
                    normalizedHp = '62' + normalizedHp;
                }

                const res = await fetch('{{ route("user.cek.booking.verifikasi") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify({ no_hp: normalizedHp, otp: otp })
                });
                const data = await res.json();

                if (data.success && data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    this.alertOtp = data.message || 'OTP salah atau kadaluarsa.';
                    this.alertOtpType = 'danger';
                    this.loading = false;
                }
            } catch(e) {
                this.alertOtp = 'Terjadi kesalahan. Coba lagi.';
                this.alertOtpType = 'danger';
                this.loading = false;
            }
        },

        async resendOtp() {
            if (this.resendSisa > 0) return;
            clearInterval(this.resendTimer);

            let hp = this.noHp.replace(/\D/g,'');
            let normalizedHp = hp;
            if (normalizedHp.startsWith('0')) {
                normalizedHp = '62' + normalizedHp.substring(1);
            } else if (!normalizedHp.startsWith('62')) {
                normalizedHp = '62' + normalizedHp;
            }

            try {
                await fetch('{{ route("user.cek.booking.kirim-otp") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify({ no_hp: normalizedHp })
                });
                this.alertOtp = 'OTP baru telah dikirim.';
                this.alertOtpType = 'success';
                this.otpSisa = 180;
                this.startOtpTimer();
                this.startResendTimer();
            } catch(e) {}
        },

        startOtpTimer() {
            this.otpSisa = 180;
            clearInterval(this.otpTimer);
            this.otpTimer = setInterval(() => {
                this.otpSisa--;
                if (this.otpSisa <= 0) {
                    clearInterval(this.otpTimer);
                    // Timer habis → tampilkan pesan di step OTP
                    this.alertOtp = 'Kode OTP sudah kadaluarsa. Klik "Kirim Ulang" atau ganti nomor.';
                    this.alertOtpType = 'danger';
                }
            }, 1000);
        },

        startResendTimer() {
            this.resendSisa = 60;
            clearInterval(this.resendTimer);
            this.resendTimer = setInterval(() => {
                this.resendSisa--;
                if (this.resendSisa <= 0) clearInterval(this.resendTimer);
            }, 1000);
        }
    }
}
</script>

    @if(session('booking_success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Booking Berhasil!',
                text: 'Link akses servis kamu sudah dikirim ke WhatsApp {{ session('no_hp') }}. Simpan link tersebut untuk memantau status servis kamu.',
                confirmButtonText: 'Oke, Mengerti',
                confirmButtonColor: '#4e73df'
            });
        });
    </script>
    @endif
</body>
</html>
