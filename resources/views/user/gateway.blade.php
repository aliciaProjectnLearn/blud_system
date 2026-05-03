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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * { font-family: 'Poppins', sans-serif; }
        body { background-color: #f8f9fc; -webkit-font-smoothing: antialiased; }
        
        .modal-fade-in { animation: fadeIn 0.3s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }

        @keyframes spin { to { transform: rotate(360deg); } }

        #btnCekBooking .btn-label { display: inline; }
        @media (max-width: 767px) {
            #btnCekBooking .btn-label { display: none; }
            #btnCekBooking { padding: 10px 14px !important; border-radius: 50px !important; }
        }

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

            {{-- Jalur Publik: Tambahkan tombol Cek Booking di Navbar --}}
            <button type="button" id="btnCekBooking"
                style="display:flex; align-items:center; gap:8px; padding:10px 20px; background:#4e73df; color:white; border:none; border-radius:12px; font-weight:700; font-size:14px; cursor:pointer; transition:opacity 0.2s;"
                onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l2.27-2.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                </svg>
                <span class="btn-label">Cek Booking</span>
            </button>
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
                    <p class="text-gray-600 mb-6 leading-relaxed text-sm flex-grow">Informasi unit tersedia, pengajuan sewa, and manajemen dokumen kontrak terpadu.</p>
                    <span class="mt-auto w-full py-2 rounded-lg text-white font-medium text-sm text-center block" style="background-color:#4e73df;">
                        Lihat Layanan
                    </span>
                </a>

                <a href="{{ route('user.ac.layanan') }}" class="card-hover block bg-white p-6 rounded-2xl shadow-sm border-t-4 border-[#4e73df] text-center flex flex-col h-full no-underline">
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

                <a href="{{ route('user.servis.katalog') }}" class="card-hover block bg-white p-6 rounded-2xl shadow-sm border-t-4 border-[#4e73df] text-center flex flex-col h-full no-underline">
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
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Kami berkomitmen memberikan layanan cepat, terintegrasi, and transparan.</p>
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
                    <p class="text-gray-300 leading-relaxed text-sm">Platform terintegrasi untuk layanan publik SMKN 1 Cirebon yang lebih baik and efisien.</p>
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

    <!-- Modal Cek Booking -->
    <div id="modalCekBooking" style="display:none; position:fixed; inset:0; z-index:60;">

        {{-- Backdrop --}}
        <div id="closeModalOverlay" style="position:absolute; inset:0; background:rgba(17,24,39,0.65);"></div>

        {{-- Centering wrapper --}}
        <div style="position:relative; display:flex; align-items:center; justify-content:center; min-height:100vh; padding:1rem;">

            {{-- Card --}}
            <div style="position:relative; background:#ffffff; border-radius:24px; box-shadow:0 25px 50px rgba(0,0,0,0.25); width:100%; max-width:440px; padding:40px 36px;">

                {{-- Close button --}}
                <button id="closeModalBtn" style="position:absolute; top:16px; right:16px; width:32px; height:32px; border-radius:50%; background:#f3f4f6; border:none; cursor:pointer; font-size:18px; color:#6b7280; display:flex; align-items:center; justify-content:center; line-height:1;">×</button>

                {{-- Step 1: Input Phone --}}
                <div id="stepPhone">
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="text-blue-600" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l2.27-2.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-extrabold text-gray-900">Cek Status Booking</h3>
                        <p class="text-gray-500 mt-2 text-sm leading-relaxed">Masukkan nomor WhatsApp untuk mengecek booking Anda</p>
                    </div>
                    <div class="space-y-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Nomor WhatsApp</label>
                            <input type="text" id="inputPhone" class="w-full px-5 py-4 bg-gray-50 rounded-2xl border-2 border-transparent focus:border-[#4e73df] focus:bg-white outline-none transition-all text-gray-900 font-medium text-lg" placeholder="Contoh: 08123456789">
                            <p id="phoneError" class="hidden mt-2 text-xs font-semibold text-red-500 italic"></p>
                        </div>
                        <button type="button" id="btnRequestOtp"
                            style="width:100%; padding:14px; background:#4e73df; color:white; border:none; border-radius:16px; font-weight:700; font-size:15px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; margin-top:12px; transition:opacity 0.2s;"
                            onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                            <span>Kirim Kode OTP</span>
                            <div id="loaderPhone" style="display:none; width:18px; height:18px; border:2px solid white; border-top-color:transparent; border-radius:50%; animation:spin 0.7s linear infinite;"></div>
                        </button>
                    </div>
                </div>

                {{-- Step 2: Input OTP --}}
                <div id="stepOtp" class="hidden">
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="text-blue-600" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-extrabold text-gray-900">Verifikasi OTP</h3>
                        <p class="text-gray-500 mt-2 text-sm">Kode OTP telah dikirim ke <span id="displayPhone" class="font-bold text-[#4e73df]"></span></p>
                    </div>
                    <div class="space-y-6">
                        <div class="flex justify-center gap-2" id="otpInputs">
                            @for($i=0; $i<6; $i++)
                            <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-2xl font-bold bg-gray-50 border-2 border-transparent rounded-xl focus:border-[#4e73df] focus:bg-white outline-none transition-all">
                            @endfor
                        </div>
                        <div class="flex flex-col items-center gap-2">
                            <p id="otpTimer" class="text-xs font-bold px-3 py-1.5 rounded-full"></p>
                            <p id="otpAttempts" class="text-[10px] uppercase tracking-tighter text-gray-400 font-bold"></p>
                        </div>
                        <button type="button" id="btnVerifyOtp"
                            style="width:100%; padding:14px; background:#4e73df; color:white; border:none; border-radius:16px; font-weight:700; font-size:15px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; margin-top:12px; transition:opacity 0.2s;"
                            onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                            <span>Verifikasi & Cek Link</span>
                            <div id="loaderOtp" style="display:none; width:18px; height:18px; border:2px solid white; border-top-color:transparent; border-radius:50%; animation:spin 0.7s linear infinite;"></div>
                        </button>
                        <div class="text-center">
                            <button type="button" id="btnResendOtp" style="background:none; border:none; color:#4e73df; font-weight:700; font-size:13px; cursor:pointer; padding:4px 8px;">
                                Kirim Ulang OTP
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Step 3: Result --}}
                <div id="stepResult" class="hidden text-center">
                    <div id="resultIcon" class="w-20 h-20 rounded-3xl flex items-center justify-center mx-auto mb-6"></div>
                    <h3 id="resultTitle" class="text-2xl font-extrabold text-gray-900 mb-2"></h3>
                    <p id="resultMessage" class="text-gray-500 text-sm leading-relaxed mb-8 px-2"></p>
                    <button type="button" class="closeModalBtn"
                        style="width:100%; padding:14px; background:#f3f4f6; color:#374151; border:none; border-radius:16px; font-weight:700; font-size:15px; cursor:pointer; margin-top:8px; transition:opacity 0.2s;"
                        onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('modalCekBooking');
        const btnOpen = document.getElementById('btnCekBooking');
        const overlay = document.getElementById('closeModalOverlay');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const closeBtns = document.querySelectorAll('.closeModalBtn');
        
        const stepPhone = document.getElementById('stepPhone');
        const stepOtp = document.getElementById('stepOtp');
        const stepResult = document.getElementById('stepResult');
        
        const inputPhone = document.getElementById('inputPhone');
        const phoneError = document.getElementById('phoneError');
        const btnRequestOtp = document.getElementById('btnRequestOtp');
        const loaderPhone = document.getElementById('loaderPhone');
        
        const otpInputs = document.querySelectorAll('.otp-input');
        const displayPhone = document.getElementById('displayPhone');
        const otpTimer = document.getElementById('otpTimer');
        const otpAttempts = document.getElementById('otpAttempts');
        const btnVerifyOtp = document.getElementById('btnVerifyOtp');
        const btnResendOtp = document.getElementById('btnResendOtp');
        const loaderOtp = document.getElementById('loaderOtp');
        
        const resultIcon = document.getElementById('resultIcon');
        const resultTitle = document.getElementById('resultTitle');
        const resultMessage = document.getElementById('resultMessage');

        let timerInterval = null;
        let cooldownInterval = null;

        function clearCekBookingState() {
            sessionStorage.removeItem('cekBooking_phone');
            sessionStorage.removeItem('cekBooking_otpSentAt');
            sessionStorage.removeItem('cekBooking_cooldownSentAt');
        }

        // Open Modal
        btnOpen.addEventListener('click', () => {
            modal.style.display = 'block';
            
            const savedPhone = sessionStorage.getItem('cekBooking_phone');
            const sentAt = parseInt(sessionStorage.getItem('cekBooking_otpSentAt') || '0');
            const OTP_TTL = 3 * 60 * 1000; // 3 menit dalam ms
            const COOLDOWN_TTL = 60 * 1000; // 60 detik dalam ms
            const now = Date.now();
            const elapsed = now - sentAt;

            if (savedPhone && sentAt && elapsed < OTP_TTL) {
                // OTP masih berlaku → langsung tampilkan Step 2
                const remainingOtpSeconds = Math.floor((OTP_TTL - elapsed) / 1000);
                const cooldownElapsed = now - parseInt(sessionStorage.getItem('cekBooking_cooldownSentAt') || '0');
                const remainingCooldown = Math.max(0, Math.floor((COOLDOWN_TTL - cooldownElapsed) / 1000));

                inputPhone.value = savedPhone; // restore phone value untuk keperluan verify
                displayPhone.textContent = maskPhone(savedPhone);
                stepPhone.classList.add('hidden');
                stepOtp.classList.remove('hidden');
                stepResult.classList.add('hidden');

                startOtpTimer(remainingOtpSeconds); // lanjutkan dari sisa waktu
                if (remainingCooldown > 0) {
                    startResendCooldown(remainingCooldown); // lanjutkan cooldown
                }
                otpInputs[0].focus();
            } else {
                // OTP sudah expired atau belum pernah kirim → tampilkan Step 1
                clearCekBookingState();
                resetModal();
                setTimeout(() => inputPhone.focus(), 100);
            }
        });

        // Close Modal
        const closeModal = () => {
            modal.style.display = 'none';
            clearInterval(timerInterval);
            clearInterval(cooldownInterval);
        };
        overlay.addEventListener('click', closeModal);
        closeModalBtn.addEventListener('click', closeModal);
        document.getElementById('closeModalBtn').addEventListener('click', closeModal);
        closeBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                if (!stepResult.classList.contains('hidden')) {
                    clearCekBookingState();
                }
                closeModal();
            });
        });

        function resetModal() {
            stepPhone.classList.remove('hidden');
            stepOtp.classList.add('hidden');
            stepResult.classList.add('hidden');
            inputPhone.value = '';
            phoneError.classList.add('hidden');
            otpInputs.forEach(i => i.value = '');
            otpAttempts.textContent = '';
            clearInterval(timerInterval);
            clearInterval(cooldownInterval);
        }

        // Mask Phone
        function maskPhone(phone) {
            let p = phone.replace(/[^0-9]/g, '');
            if (p.startsWith('0')) p = '62' + p.substring(1);
            return '+' + p.substring(0, 2) + ' ' + p.substring(2, 5) + '-****-' + p.substring(p.length - 4);
        }

        // Request OTP Logic
        async function performRequestOtp(isResend = false) {
            const phone = inputPhone.value.trim();
            if (!phone) {
                showError('Nomor WhatsApp wajib diisi');
                return;
            }

            if (isResend) {
                btnResendOtp.disabled = true;
                btnResendOtp.style.color = '#9ca3af';
                btnResendOtp.style.cursor = 'default';
                btnResendOtp.textContent = 'Mengirim...';
            } else {
                btnRequestOtp.disabled = true;
                loaderPhone.style.display = 'block';
            }
            
            phoneError.classList.add('hidden');

            try {
                const response = await fetch("{{ route('user.cek-booking.otp') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ phone })
                });

                const data = await response.json();
                if (data.success) {
                    const normalizedPhone = phone.startsWith('0') ? '62' + phone.substring(1) : phone;
                    sessionStorage.setItem('cekBooking_phone', normalizedPhone);
                    sessionStorage.setItem('cekBooking_otpSentAt', Date.now().toString());
                    sessionStorage.setItem('cekBooking_cooldownSentAt', Date.now().toString());

                    displayPhone.textContent = maskPhone(phone);
                    stepPhone.classList.add('hidden');
                    stepOtp.classList.remove('hidden');
                    startOtpTimer(180);
                    startResendCooldown(60);
                    if (!isResend) setTimeout(() => otpInputs[0].focus(), 100);
                } else {
                    if (isResend) {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: data.message });
                        startResendCooldown(10);
                    } else {
                        showError(data.message);
                    }
                }
            } catch (error) {
                if (isResend) Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal mengirim OTP.' });
                else showError('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                btnRequestOtp.disabled = false;
                loaderPhone.style.display = 'none';
            }
        }

        btnRequestOtp.addEventListener('click', () => performRequestOtp(false));
        btnResendOtp.addEventListener('click', () => {
            if (!btnResendOtp.disabled) performRequestOtp(true);
        });

        // Enter key
        inputPhone.addEventListener('keypress', (e) => { if (e.key === 'Enter') btnRequestOtp.click(); });

        function showError(msg) {
            phoneError.textContent = msg;
            phoneError.classList.remove('hidden');
        }

        // OTP Input Logic
        otpInputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (e.target.value.length === 1 && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
                const allFilled = Array.from(otpInputs).every(i => i.value.length === 1);
                if (allFilled) btnVerifyOtp.click();
            });
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && e.target.value === '' && index > 0) otpInputs[index - 1].focus();
                if (e.key === 'Enter') btnVerifyOtp.click();
            });
        });

        // Verify OTP
        btnVerifyOtp.addEventListener('click', async () => {
            const otp = Array.from(otpInputs).map(i => i.value).join('');
            if (otp.length !== 6) return;

            btnVerifyOtp.disabled = true;
            loaderOtp.style.display = 'block';

            try {
                const response = await fetch("{{ route('user.cek-booking.verify') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ phone: inputPhone.value, otp })
                });

                const data = await response.json();
                if (data.success) {
                    showResult(data.found, data.message);
                } else {
                    if (data.remaining_attempts !== undefined) {
                        otpAttempts.textContent = `Kesempatan tersisa: ${data.remaining_attempts} dari 3`;
                    }
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message, confirmButtonColor: '#4e73df' });
                    if (data.message.includes('diblokir')) {
                        clearCekBookingState();
                        closeModal();
                    }
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal memverifikasi OTP.' });
            } finally {
                btnVerifyOtp.disabled = false;
                loaderOtp.style.display = 'none';
            }
        });

        function startOtpTimer(duration) {
            let timer = duration;
            clearInterval(timerInterval);
            otpTimer.style.color = '#2563eb';
            otpTimer.style.background = '#eff6ff';
            timerInterval = setInterval(() => {
                const minutes = Math.floor(timer / 60);
                const seconds = timer % 60;
                otpTimer.textContent = `Kedaluwarsa dalam ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
                if (timer <= 30) {
                    otpTimer.style.color = '#ef4444';
                    otpTimer.style.background = '#fef2f2';
                }
                if (--timer < 0) {
                    clearInterval(timerInterval);
                    otpTimer.textContent = 'Kode OTP kedaluwarsa';
                    otpTimer.style.color = '#ef4444';
                    otpTimer.style.background = '#fef2f2';
                    clearCekBookingState();
                }
            }, 1000);
        }

        function startResendCooldown(duration) {
            let timer = duration;
            btnResendOtp.disabled = true;
            btnResendOtp.style.color = '#9ca3af';
            btnResendOtp.style.cursor = 'default';
            clearInterval(cooldownInterval);
            cooldownInterval = setInterval(() => {
                btnResendOtp.textContent = `Kirim ulang dalam ${timer}s`;
                if (--timer < 0) {
                    clearInterval(cooldownInterval);
                    btnResendOtp.disabled = false;
                    btnResendOtp.textContent = 'Kirim Ulang OTP';
                    btnResendOtp.style.color = '#4e73df';
                    btnResendOtp.style.cursor = 'pointer';
                }
            }, 1000);
        }

        function showResult(found, message) {
            clearCekBookingState();
            stepOtp.classList.add('hidden');
            stepResult.classList.remove('hidden');
            if (found) {
                resultIcon.className = 'w-20 h-20 bg-green-50 rounded-3xl flex items-center justify-center mx-auto mb-6 text-green-600';
                resultIcon.innerHTML = '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>';
                resultTitle.textContent = 'Link Berhasil Dikirim!';
                resultTitle.className = 'text-2xl font-extrabold text-green-600 mb-2';
            } else {
                resultIcon.className = 'w-20 h-20 bg-orange-50 rounded-3xl flex items-center justify-center mx-auto mb-6 text-orange-500';
                resultIcon.innerHTML = '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>';
                resultTitle.textContent = 'Data Tidak Ditemukan';
                resultTitle.className = 'text-2xl font-extrabold text-orange-500 mb-2';
            }
            resultMessage.textContent = message;
        }
    });
    </script>
</body>
</html>
