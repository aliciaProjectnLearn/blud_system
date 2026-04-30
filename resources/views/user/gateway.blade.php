<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BLUD Portal - Satu Portal, Berbagai Layanan Publik</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        * { font-family: 'Poppins', sans-serif; }
        body { background-color: #f8f9fc; -webkit-font-smoothing: antialiased; }
        .card-hover { transition: transform .25s, box-shadow .25s; cursor: pointer; }
        .card-hover:hover { transform: translateY(-6px); box-shadow: 0 12px 32px rgba(78,115,223,.15); }
        .star { color: #f6c23e; }
        @media (max-width: 768px) {
            #home h1 { font-size: 1.8rem; }
            #home p { font-size: 0.95rem; }
            .p-8 { padding: 1rem !important; }
            .text-5xl { font-size: 2rem; }
            .text-4xl { font-size: 1.5rem; }
        }
        @media (max-width: 480px) {
            #home h1 { font-size: 1.4rem; }
            .text-5xl { font-size: 1.5rem; }
        }
        .grid-layanan {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 1.5rem;
        }
        @media (min-width: 768px) { .grid-layanan { grid-template-columns: repeat(2, 1fr); } }
        @media (min-width: 1024px) { .grid-layanan { grid-template-columns: repeat(4, 1fr); } }
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
                <span class="font-bold text-xl" style="color:#4e73df;">BLUD PORTAL</span>
            </div>

            {{-- Jalur Publik: Tidak ada tombol login/dashboard di sini --}}
        </nav>
    </header>

    {{-- Hero --}}
    <section id="home" class="relative py-24 overflow-hidden" style="background-color:#EEEEEE;">
        <div class="absolute inset-0 opacity-10">
            <img src="{{ asset('img/depan_smk (1).JPG') }}" alt="SMK" class="w-full h-full object-cover"/>
        </div>
        <div class="relative max-w-7xl mx-auto px-6 text-center">
            <h1 class="text-5xl font-bold mb-6" style="color:#4e73df;">Satu Portal, Berbagai Layanan Publik</h1>
            <p class="text-xl max-w-3xl mx-auto leading-relaxed" style="color:#4e73df;">
                Akses mudah dan cepat untuk sewa lapangan futsal, penyewaan ruko/kantin, layanan servis AC, hingga servis kendaraan profesional dalam satu tempat.
            </p>
        </div>
    </section>

    {{-- Layanan --}}
    <section id="layanan" class="py-24 bg-[#f8f9fc]">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-6" style="color:#4e73df;">Layanan Kami</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Temukan layanan yang sesuai dengan kebutuhan Anda</p>
            </div>
            <div class="grid-layanan">

                <a href="{{ route('user.futsal.landing') }}" class="card-hover block bg-white p-6 rounded-2xl shadow-sm border-t-4 border-[#4e73df] text-center flex flex-col h-full no-underline">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm" style="background-color:#4e73df;">
                        <svg class="text-white" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/><path d="M12 2v20M2 12h20m-6-6-12 12M18 6 6 18"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="color:#4e73df;">Pusat Olahraga Futsal</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed text-sm flex-grow">Booking lapangan online, cek ketersediaan jadwal, dan kelola membership dengan mudah.</p>
                    <span class="mt-auto w-full py-2 rounded-lg text-white font-medium text-sm text-center block" style="background-color:#4e73df;">
                        Lihat Layanan
                    </span>
                </a>

                <a href="{{ route('user.kantin.katalog') }}" class="card-hover block bg-white p-6 rounded-2xl shadow-sm border-t-4 border-[#4e73df] text-center flex flex-col h-full no-underline">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm" style="background-color:#4e73df;">
                        <svg class="text-white" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 21h18M4 21V7l8-4v18M12 3v18M12 7h8v14M8 11h.01M8 15h.01M16 11h.01M16 15h.01"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="color:#4e73df;">Penyewaan Kantin & Ruko</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed text-sm flex-grow">Informasi unit tersedia, pengajuan sewa, dan manajemen dokumen kontrak terpadu.</p>
                    <span class="mt-auto w-full py-2 rounded-lg text-white font-medium text-sm text-center block" style="background-color:#4e73df;">
                        Lihat Layanan
                    </span>
                </a>

                <a href="{{ route('ac.layanan') }}" class="card-hover block bg-white p-6 rounded-2xl shadow-sm border-t-4 border-[#4e73df] text-center flex flex-col h-full no-underline">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm" style="background-color:#4e73df;">
                        <svg class="text-white" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17.7 7.7a2.5 2.5 0 1 1 1.8 4.3H2M9.6 4.6A2 2 0 1 1 11 8H2M12.6 19.4A2 2 0 1 0 14 16H2"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="color:#4e73df;">Layanan Servis AC</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed text-sm flex-grow">Pesan jasa perbaikan AC profesional dari teknisi bersertifikat.</p>
                    <span class="mt-auto w-full py-2 rounded-lg text-white font-medium text-sm text-center block" style="background-color:#4e73df;">
                        Lihat Layanan
                    </span>
                </a>

                <a href="{{ route('servis.katalog') }}" class="card-hover block bg-white p-6 rounded-2xl shadow-sm border-t-4 border-[#4e73df] text-center flex flex-col h-full no-underline">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm" style="background-color:#4e73df;">
                        <svg class="text-white" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="color:#4e73df;">Servis Motor & Mobil</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed text-sm flex-grow">Layanan servis kendaraan berkala dengan peralatan modern dan mekanik ahli.</p>
                    <span class="mt-auto w-full py-2 rounded-lg text-white font-medium text-sm text-center block" style="background-color:#4e73df;">
                        Lihat Layanan
                    </span>
                </a>

            </div>
        </div>
    </section>

    {{-- Keunggulan --}}
    <section id="tentang" class="py-24" style="background-color:#EEEEEE;">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-6" style="color:#4e73df;">Mengapa Memilih Kami?</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Kami berkomitmen memberikan layanan cepat, terintegrasi, dan transparan.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-24">
                @foreach([
                    ['path' => 'M13 2 3 14h9l-1 8 10-12h-9l1-8z', 'title' => 'Cepat & Sigap', 'desc' => 'Proses booking dan pengajuan yang efisien menghemat waktu Anda tanpa perlu antre panjang.'],
                    ['path' => 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10', 'title' => 'Transparan', 'desc' => 'Informasi harga, ketersediaan, dan status 100% jelas, terbuka, dan dapat dipantau.'],
                    ['path' => 'M12 2v20M2 12h20m-6-6-12 12M18 6 6 18', 'title' => 'Terintegrasi', 'desc' => 'Satu pintu akses untuk semua kebutuhan penyewaan dan layanan jasa Anda.'],
                ] as $item)
                <div class="bg-white p-8 rounded-2xl shadow-sm border-l-4 border-[#4e73df] text-center flex flex-col h-full hover:shadow-md transition-shadow">
                    <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm" style="background-color:#4e73df;">
                        <svg class="text-white" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="{{ $item['path'] }}"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="color:#4e73df;">{{ $item['title'] }}</h3>
                    <p class="text-gray-600 leading-relaxed flex-grow text-sm">{{ $item['desc'] }}</p>
                </div>
                @endforeach
            </div>

            <div class="border-t border-gray-300 pt-16">
                <div class="text-center mb-12">
                    <h3 class="text-3xl font-bold mb-4" style="color:#4e73df;">Statistik Kredibilitas</h3>
                    <p class="text-gray-600">Telah dipercaya oleh ratusan orang dengan ribuan testimoni positif.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach([['500+','Masyarakat Terdaftar'],['1.500+','Transaksi Booking Berhasil'],['200+','Unit Ruko & Kantin Aktif']] as $s)
                    <div class="bg-white p-10 rounded-2xl shadow-sm border-l-4 border-[#4e73df] hover:shadow-md hover:-translate-y-1 transition-all text-center">
                        <div class="text-5xl font-black mb-4" style="color:#4e73df;">{{ $s[0] }}</div>
                        <p class="font-bold" style="color:#4e73df;">{{ $s[1] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Testimoni — hanya tampil jika ada data --}}
    @if($testimoni->isNotEmpty())
    <section class="py-24 bg-[#f8f9fc]">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold mb-4" style="color:#4e73df;">Kata Mereka</h2>
                <p class="text-gray-600 max-w-xl mx-auto">Ribuan pengguna sudah merasakan kemudahan layanan BLUD Portal.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($testimoni as $t)
                <div class="bg-white rounded-2xl p-6 shadow-sm border-l-4 border-[#4e73df] hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0" style="background-color:#4e73df;">
                            {{ strtoupper(substr($t->nama, 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-bold text-gray-800 text-sm">{{ $t->nama }}</div>
                            <div class="text-xs text-gray-400">{{ $t->peran }}</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        @for($i = 0; $i < $t->bintang; $i++)<span class="star text-sm">★</span>@endfor
                        @for($i = $t->bintang; $i < 5; $i++)<span class="text-gray-300 text-sm">★</span>@endfor
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed">"{{ $t->isi }}"</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
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
                        <span class="font-bold text-2xl text-white">BLUD PORTAL</span>
                    </div>
                    <p class="text-gray-300 leading-relaxed text-sm">Platform terintegrasi untuk layanan publik SMKN 1 Cirebon yang lebih baik dan efisien.</p>
                </div>
                <div>
                    <h4 class="font-bold text-white text-xl mb-6">Tautan Cepat</h4>
                    <ul class="space-y-3 text-sm">
                        @foreach(['Kebijakan Privasi','Syarat & Ketentuan','Bantuan','FAQ'] as $link)
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">{{ $link }}</a></li>
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
                <p class="text-gray-300 text-sm">© {{ date('Y') }} BLUD SMKN 1 Cirebon. Semua hak dilindungi undang-undang.</p>
            </div>
        </div>
    </footer>

    {{-- Footer --}}
    <footer class="bg-white border-t border-gray-200 py-12">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded flex items-center justify-center" style="background-color:#4e73df;">
                        <svg class="text-white" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M12 2v20M2 12h20"/><path d="m6 6 12 12M18 6 6 18"/>
                        </svg>
                    </div>
                    <span class="font-bold text-gray-800">BLUD PORTAL</span>
                </div>
                
                <div class="flex gap-8 text-sm text-gray-500">
                    <a href="#" class="hover:text-[#4e73df]">Tentang Kami</a>
                    <a href="#" class="hover:text-[#4e73df]">Syarat & Ketentuan</a>
                </div>
                
                <div class="text-sm text-gray-400">
                    &copy; {{ date('Y') }} BLUD SMKN 1 CIREBON. All rights reserved.
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
