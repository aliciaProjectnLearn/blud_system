<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BLUD Portal - Satu Portal, Berbagai Layanan Publik</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Alpine.js untuk Hamburger Menu -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-white">
    {{-- Header/Navigation Bar --}}
    <header class="sticky top-0 z-50 bg-white shadow-sm border-b border-gray-200 relative" x-data="{ open: false }">
        <nav class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between bg-white relative z-50">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: #213C51;">
                    <svg class="text-white" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2v20M2 12h20"/>
                        <path d="m6 6 12 12M18 6 6 18"/>
                    </svg>
                </div>
                <span class="font-bold text-xl" style="color: #213C51;">
                    BLUD PORTAL
                </span>
            </div>
            
            <!-- Hamburger Toggle Mobile/Tablet -->
            <div class="lg:hidden flex items-center">
                <button @click="open = !open" type="button" class="text-gray-700 hover:text-[#213C51] focus:outline-none p-2 m-0 border-0 bg-transparent">
                    <svg class="w-7 h-7" x-show="!open" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg class="w-7 h-7" x-show="open" style="display: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden lg:flex items-center gap-8">
                <a href="#home" class="text-gray-700 hover:text-[#213C51] transition-colors font-medium">Home</a>
                <a href="#layanan" class="text-gray-700 hover:text-[#213C51] transition-colors font-medium">Layanan</a>
                <a href="#tentang" class="text-gray-700 hover:text-[#213C51] transition-colors font-medium">Tentang Kami</a>
                <a href="#kontak" class="text-gray-700 hover:text-[#213C51] transition-colors font-medium">Kontak</a>
                
                <div class="flex items-center gap-4 border-l border-gray-300 pl-6 ml-2">
                    <a href="{{ route('user.dashboard') }}" class="flex items-center gap-2 text-gray-700 hover:text-[#6594B1] transition-colors font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Dashboard Saya
                    </a>

                    @php
                        $roleName = strtolower(auth()->user()->roles->first()->nama ?? '');
                        $dashboardRoute = url('/');
                        if ($roleName === 'superadmin') $dashboardRoute = route('dashboard');
                        elseif ($roleName === 'adminfutsal') $dashboardRoute = route('adminfutsal.dashboard');
                        elseif ($roleName === 'adminkantin') $dashboardRoute = route('adminkantin.dashboard');
                        elseif ($roleName === 'adminac') $dashboardRoute = route('adminac.dashboard');
                        elseif ($roleName === 'teknisi') $dashboardRoute = route('teknisi.dashboard');
                    @endphp
                    @if($roleName !== 'pelanggan' && $roleName !== '')
                    <a href="{{ $dashboardRoute }}" class="px-5 py-2 rounded-lg text-white font-medium transition-all hover:shadow-lg hover:scale-105" style="background-color: #213C51;">
                        Dashboard
                    </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline m-0">
                        @csrf
                        <button type="submit" class="px-5 py-2 rounded-lg text-white font-medium transition-colors hover:bg-red-700 cursor-pointer" style="background-color: #e53e3e;">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </nav>
        
        <!-- Mobile Dropdown Menu -->
        <div x-show="open" style="display: none;" class="lg:hidden absolute top-full left-0 w-full bg-white border-t border-gray-100 shadow-lg z-40">
            <div class="px-6 py-4 space-y-3">
                <a href="#home" class="block text-gray-700 hover:text-[#213C51] transition-colors font-medium py-2">Home</a>
                <a href="#layanan" class="block text-gray-700 hover:text-[#213C51] transition-colors font-medium py-2">Layanan</a>
                <a href="#tentang" class="block text-gray-700 hover:text-[#213C51] transition-colors font-medium py-2">Tentang Kami</a>
                <a href="#kontak" class="block text-gray-700 hover:text-[#213C51] transition-colors font-medium py-2">Kontak</a>
                
                <hr class="border-gray-200 my-2">
                
                <a href="{{ route('user.dashboard') }}" class="flex items-center gap-2 text-gray-700 hover:text-[#6594B1] transition-colors font-medium py-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Dashboard Saya
                </a>
                
                <div class="pt-2 pb-2">
                    @if($roleName !== 'pelanggan' && $roleName !== '')
                    <a href="{{ $dashboardRoute }}" class="block text-center w-full px-6 py-3 rounded-lg text-white font-medium mb-3 shadow-sm hover:shadow-md" style="background-color: #213C51;">
                        Dashboard
                    </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="block text-center w-full px-6 py-3 rounded-lg text-white font-medium shadow-sm hover:shadow-md" style="background-color: #e53e3e;">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    {{-- Hero Section --}}
    <section id="home" class="relative py-24 overflow-hidden" style="background-color: #EEEEEE;">
        <div class="absolute inset-0 opacity-10">
            <img 
                src="https://images.unsplash.com/photo-1764053430604-d585d1f1dad6?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxkaWdpdGFsJTIwdGVjaG5vbG9neSUyMG5ldHdvcmslMjBpbnRlZ3JhdGlvbnxlbnwxfHx8fDE3NzU0NDIyMzd8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral"
                alt="Digital Integration"
                class="w-full h-full object-cover"
            />
        </div>
        
        <div class="relative max-w-7xl mx-auto px-6 text-center">
            <h1 class="text-5xl font-bold mb-6" style="color: #213C51;">
                Satu Portal, Berbagai Layanan Publik
            </h1>
            <p class="text-xl max-w-3xl mx-auto leading-relaxed" style="color: #6594B1;">
                Akses mudah dan cepat untuk sewa lapangan futsal, penyewaan ruko/kantin, 
                serta layanan servis AC profesional dalam satu tempat.
            </p>
        </div>
    </section>

    {{-- Service Grid Section --}}
    <section id="layanan" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-6" style="color: #213C51;">
                    Layanan Kami
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Pilih layanan yang Anda butuhkan dan nikmati kemudahan akses terintegrasi
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                {{-- Card 1 - Futsal --}}
                <div class="group bg-white rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 overflow-hidden border border-gray-100 flex flex-col h-full">
                    <div class="p-8 flex flex-col flex-grow">
                        <div 
                            class="w-16 h-16 rounded-xl flex items-center justify-center mb-6 transition-transform duration-300 group-hover:scale-110"
                            style="background-color: #6594B1;">
                            <svg class="text-white" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 2v20"/>
                                <path d="M2 12h20"/>
                                <path d="m6 6 12 12"/>
                                <path d="M18 6 6 18"/>
                            </svg>
                        </div>
                        
                        <h3 class="text-2xl font-bold mb-4" style="color: #213C51;">
                            Pusat Olahraga Futsal
                        </h3>
                        
                        <p class="text-gray-600 mb-8 leading-relaxed flex-grow">
                            Booking lapangan online, cek ketersediaan jadwal, dan kelola membership 
                            dengan mudah.
                        </p>
                        
                        <a href="{{ route('user.futsal.index') }}" 
                           class="mt-auto block w-full py-3 rounded-lg text-white font-medium text-center transition-all shadow-sm hover:shadow-lg focus:ring focus:ring-opacity-50"
                           style="background-color: #213C51;">
                            Pesan Lapangan
                        </a>
                    </div>
                </div>

                {{-- Card 2 - Ruko/Canteen --}}
                <div class="group bg-white rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 overflow-hidden border border-gray-100 flex flex-col h-full">
                    <div class="p-8 flex flex-col flex-grow">
                        <div 
                            class="w-16 h-16 rounded-xl flex items-center justify-center mb-6 transition-transform duration-300 group-hover:scale-110"
                            style="background-color: #6594B1;">
                            <svg class="text-white" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 21h18"/>
                                <path d="M4 21V7l8-4v18"/>
                                <path d="M12 3v18"/>
                                <path d="M12 7h8v14"/>
                                <path d="M8 11h.01"/>
                                <path d="M8 15h.01"/>
                                <path d="M16 11h.01"/>
                                <path d="M16 15h.01"/>
                            </svg>
                        </div>
                        
                        <h3 class="text-2xl font-bold mb-4" style="color: #213C51;">
                            Penyewaan Lahan & Ruko
                        </h3>
                        
                        <p class="text-gray-600 mb-8 leading-relaxed flex-grow">
                            Informasi unit tersedia, pengajuan sewa, dan manajemen dokumen kontrak 
                            terpadu.
                        </p>
                        
                        <a href="{{ route('user.ruko.index') }}" 
                           class="mt-auto block w-full py-3 rounded-lg text-white font-medium text-center transition-all shadow-sm hover:shadow-lg focus:ring focus:ring-opacity-50"
                           style="background-color: #213C51;">
                            Cek Unit Ruko
                        </a>
                    </div>
                </div>

                {{-- Card 3 - AC Services --}}
                <div class="group bg-white rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 overflow-hidden border border-gray-100 flex flex-col h-full">
                    <div class="p-8 flex flex-col flex-grow">
                        <div 
                            class="w-16 h-16 rounded-xl flex items-center justify-center mb-6 transition-transform duration-300 group-hover:scale-110"
                            style="background-color: #6594B1;">
                            <svg class="text-white" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.7 7.7a2.5 2.5 0 1 1 1.8 4.3H2"/>
                                <path d="M9.6 4.6A2 2 0 1 1 11 8H2"/>
                                <path d="M12.6 19.4A2 2 0 1 0 14 16H2"/>
                            </svg>
                        </div>
                        
                        <h3 class="text-2xl font-bold mb-4" style="color: #213C51;">
                            Layanan Servis & Produk AC
                        </h3>
                        
                        <p class="text-gray-600 mb-8 leading-relaxed flex-grow">
                            Pesan jasa perbaikan AC profesional dari teknisi bersertifikat dan beli 
                            sparepart berkualitas.
                        </p>
                        
                        <a href="{{ route('user.ac.index') }}" 
                           class="mt-auto block w-full py-3 rounded-lg text-white font-medium text-center transition-all shadow-sm hover:shadow-lg focus:ring focus:ring-opacity-50"
                           style="background-color: #213C51;">
                            Pesan Servis
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Benefits Section --}}
    <section id="tentang" class="py-24" style="background-color: #EEEEEE;">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-6" style="color: #213C51;">
                    Mengapa Memilih Kami?
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Kami berkomitmen memberikan layanan cepat, terintegrasi, dan transparan
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-24">
                <div class="bg-white p-8 rounded-2xl shadow-sm text-center flex flex-col h-full hover:shadow-md transition-shadow">
                    <div 
                        class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm"
                        style="background-color: #6594B1;">
                        <svg class="text-white" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="color: #213C51;">
                        Cepat & Sigap
                    </h3>
                    <p class="text-gray-600 leading-relaxed flex-grow">
                        Proses booking dan pengajuan yang efisien menghemat waktu Anda tanpa perlu antre panjang
                    </p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-sm text-center flex flex-col h-full hover:shadow-md transition-shadow">
                    <div 
                        class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm"
                        style="background-color: #6594B1;">
                        <svg class="text-white" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="color: #213C51;">
                        Transparan
                    </h3>
                    <p class="text-gray-600 leading-relaxed flex-grow">
                        Informasi harga, ketersediaan, dan status proses yang 100% jelas, terbuka, dan dapat dipantau
                    </p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-sm text-center flex flex-col h-full hover:shadow-md transition-shadow">
                    <div 
                        class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm"
                        style="background-color: #6594B1;">
                        <svg class="text-white" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v20M2 12h20"/>
                            <path d="m6 6 12 12M18 6 6 18"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="color: #213C51;">
                        Terintegrasi
                    </h3>
                    <p class="text-gray-600 leading-relaxed flex-grow">
                        Satu pintu akses untuk semua kebutuhan penyewaan dan layanan jasa Anda
                    </p>
                </div>
            </div>
            
            {{-- Statistik Kredibilitas --}}
            <div class="mt-12 max-w-7xl mx-auto border-t border-gray-300 pt-16">
                <div class="text-center mb-16">
                    <h3 class="text-3xl font-bold mb-4" style="color: #213C51;">Statistik Kredibilitas</h3>
                    <p class="text-lg text-gray-600 font-medium">Telah dipercaya oleh ratusan orang dengan ribuan testimoni positif.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Stat 1 -->
                    <div class="bg-white p-10 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-all hover:-translate-y-1 text-center flex flex-col h-full justify-center">
                        <div class="text-5xl font-black mb-4" style="color: #6594B1;">500+</div>
                        <h4 class="font-bold text-lg" style="color: #213C51;">Masyarakat Terdaftar</h4>
                    </div>
                    
                    <!-- Stat 2 -->
                    <div class="bg-white p-10 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-all hover:-translate-y-1 text-center flex flex-col h-full justify-center">
                        <div class="text-5xl font-black mb-4" style="color: #6594B1;">1.500+</div>
                        <h4 class="font-bold text-lg" style="color: #213C51;">Transaksi Booking Berhasil</h4>
                    </div>
                    
                    <!-- Stat 3 -->
                    <div class="bg-white p-10 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-all hover:-translate-y-1 text-center flex flex-col h-full justify-center">
                        <div class="text-5xl font-black mb-4" style="color: #6594B1;">200+</div>
                        <h4 class="font-bold text-lg" style="color: #213C51;">Unit Ruko & Kantin Aktif</h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer id="kontak" class="py-16" style="background-color: #213C51;">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-12">
                {{-- Brand Column --}}
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background-color: #6594B1;">
                            <svg class="text-white" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2v20M2 12h20"/>
                                <path d="m6 6 12 12M18 6 6 18"/>
                            </svg>
                        </div>
                        <span class="font-bold text-2xl text-white">
                            BLUD PORTAL
                        </span>
                    </div>
                    <p class="text-gray-300 leading-relaxed text-lg">
                        Platform terintegrasi untuk layanan publik yang lebih baik dan efisien.
                    </p>
                </div>

                {{-- Quick Links --}}
                <div>
                    <h4 class="font-bold text-white text-xl mb-6">Tautan Cepat</h4>
                    <ul class="space-y-4">
                        <li>
                            <a href="#" class="text-gray-300 hover:text-white transition-colors">
                                Kebijakan Privasi
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-300 hover:text-white transition-colors">
                                Syarat & Ketentuan
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-300 hover:text-white transition-colors">
                                Bantuan
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-300 hover:text-white transition-colors">
                                FAQ
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Social Media --}}
                <div>
                    <h4 class="font-bold text-white text-xl mb-6">Ikuti Kami</h4>
                    <div class="flex gap-4">
                        <a 
                            href="#" 
                            class="w-12 h-12 rounded-lg flex items-center justify-center transition-all hover:scale-110 shadow-md"
                            style="background-color: #6594B1;">
                            <svg class="text-white w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a 
                            href="#" 
                            class="w-12 h-12 rounded-lg flex items-center justify-center transition-all hover:scale-110 shadow-md"
                            style="background-color: #6594B1;">
                            <svg class="text-white w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>
                        <a 
                            href="#" 
                            class="w-12 h-12 rounded-lg flex items-center justify-center transition-all hover:scale-110 shadow-md"
                            style="background-color: #6594B1;">
                            <svg class="text-white w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Copyright --}}
            <div class="border-t border-gray-600 pt-8 text-center mt-12">
                <p class="text-gray-300">
                    © {{ date('Y') }} BLUD Portal. Semua hak dilindungi undang-undang.
                </p>
            </div>
        </div>
    </footer>
</body>
</html>
