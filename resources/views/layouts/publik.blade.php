<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>BLUD Portal - @yield('title', 'Layanan')</title>

    {{-- Font Awesome --}}
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    {{-- SB Admin 2 CSS --}}
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Poppins', sans-serif !important; 
            background-color: #f8f9fc; 
        }
        .navbar-pelanggan { 
            background: #fff; 
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); 
            border-bottom: 2px solid #4e73df;
        }
        .btn-kembali { 
            border-radius: 8px; 
            font-weight: 500; 
            transition: all 0.3s;
        }
        .btn-kembali:hover {
            transform: translateX(-3px);
        }
        .logo-box { 
            background: #4e73df; 
            width: 35px; 
            height: 35px; 
            border-radius: 8px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin-right: 12px; 
            box-shadow: 0 4px 10px rgba(78, 115, 223, 0.3);
        }
        @media (max-width: 576px) {
            .logo-box { width: 30px; height: 30px; margin-right: 8px; }
            .navbar-brand { font-size: 0.9rem; }
            .btn-kembali { padding-left: 12px !important; padding-right: 12px !important; }
        }
        .navbar-brand {
            letter-spacing: 1px;
        }
        #content-wrapper {
            background-color: transparent !important;
        }
    </style>
    @stack('styles')
</head>

<body id="page-top">

    <div id="wrapper">
        {{-- Main Content --}}
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                
                {{-- Simple Navbar khusus pelanggan --}}
                <nav class="navbar navbar-expand navbar-light navbar-pelanggan mb-4 sticky-top">
                    <div class="container py-2">
                        <a class="navbar-brand d-flex align-items-center" href="{{ route('user.gateway') }}">
                            <div class="logo-box">
                                <svg class="text-white" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2v20M2 12h20"/><path d="m6 6 12 12M18 6 6 18"/>
                                </svg>
                            </div>
                            <span class="font-weight-bold text-primary">BLUD <span class="d-none d-sm-inline">PORTAL</span></span>
                        </a>
                        

                        <a href="{{ route('user.gateway') }}" class="btn btn-sm btn-light btn-kembali text-primary shadow-sm px-3">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali
                        </a>
                    </div>
                </nav>

                {{-- Page Content --}}
                <div class="container pb-5">
                    {{-- Flash Messages --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show shadow-sm border-left-success" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle fa-lg mr-3"></i>
                                <div>{{ session('success') }}</div>
                            </div>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-left-danger" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-circle fa-lg mr-3"></i>
                                <div>{{ session('error') }}</div>
                            </div>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>

            @include('partials.footer')
        </div>
    </div>

    {{-- Scripts --}}
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('assets/js/sb-admin-2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    @stack('scripts')
</body>
</html>
