<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="">
    <meta name="author" content="">
    {{-- Logo BLUD --}}
    <link rel="icon"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><rect width='24' height='24' rx='4' fill='%234e73df'/><g stroke='white' stroke-width='2' stroke-linecap='round'><path d='M12 2v20M2 12h20'/><path d='m6 6 12 12M18 6 6 18'/></g></svg>">

    <title>{{ config('app.name', 'MyApp') }} - @yield('title', 'Dashboard')</title>

    {{-- Font Awesome --}}
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- SB Admin 2 CSS --}}
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif !important;
            line-height: 1.6;
        }

        h1,h2,h3,h4,h5,h6 {
            font-weight: 600;
        }
    </style>

    <style>
    @media (max-width: 767.98px) {

        /* Sembunyikan sidebar default SB Admin 2 di mobile */
        #accordionSidebar {
            display: none !important;
        }

        /* Dropdown menu mobile */
        #mobile-nav {
            display: none;
            position: fixed;
            top: 68px; /* 56px topbar + 12px gap */
            left: 12px;
            right: 12px;
            width: calc(100% - 24px);
            max-height: calc(100vh - 80px);
            overflow-y: auto;
            background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
            z-index: 1050;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            padding-bottom: 20px;
            border-radius: 15px;
            border: 1px solid rgba(255,255,255,0.1);
        }

        #mobile-nav.open {
            display: block;
            animation: slideDown 0.25s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Backdrop */
        #mobile-nav-backdrop {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(2px);
            z-index: 1049;
        }

        #mobile-nav-backdrop.open {
            display: block;
        }

        /* Tombol close */
        #mobile-nav-close {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 10px 16px 4px;
        }

        #mobile-nav-close button {
            background: rgba(255,255,255,0.15);
            border: none;
            border-radius: 50%;
            width: 32px; height: 32px;
            color: white;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #mobile-nav-close button:hover {
            background: rgba(255,255,255,0.3);
        }

        /* Section heading (Sidebar & Mobile) */
        #mobile-nav .mobile-nav-heading,
        #mobile-nav .sidebar-heading {
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            color: rgba(255,255,255,0.4);
            letter-spacing: 0.1rem;
            padding: 16px 24px 8px;
        }

        /* Nav items (Sidebar & Mobile) */
        #mobile-nav .mobile-nav-item,
        #mobile-nav .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 24px;
            color: rgba(255,255,255,0.8) !important;
            text-decoration: none;
            font-size: 0.9rem;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            gap: 10px;
            transition: background 0.15s, color 0.15s;
        }

        #mobile-nav .nav-link i,
        #mobile-nav .mobile-nav-item i {
            width: 20px;
            text-align: center;
            font-size: 0.85rem;
            opacity: 0.8;
        }

        #mobile-nav .nav-link:hover,
        #mobile-nav .mobile-nav-item:hover,
        #mobile-nav .nav-item.active .nav-link,
        #mobile-nav .mobile-nav-item.active {
            background: rgba(255,255,255,0.1);
            color: white !important;
            text-decoration: none;
        }

        /* Sub item (collapse) */
        #mobile-nav .collapse {
            background: rgba(0,0,0,0.15);
            border-left: 3px solid rgba(255,255,255,0.3);
            margin-left: 24px;
            margin-right: 24px;
            border-radius: 0 0 8px 8px;
        }

        #mobile-nav .collapse-inner {
            background: transparent !important;
            padding: 0;
        }

        #mobile-nav .collapse-item {
            display: block;
            padding: 10px 20px;
            color: rgba(255,255,255,0.7) !important;
            font-size: 0.85rem;
            text-decoration: none;
            border-bottom: 1px solid rgba(255,255,255,0.03);
            transition: background 0.15s, color 0.15s;
        }

        #mobile-nav .collapse-item:hover {
            background: rgba(255,255,255,0.1);
            color: white !important;
            text-decoration: none;
        }

        /* Divider */
        #mobile-nav .sidebar-divider,
        #mobile-nav .mobile-nav-divider {
            border-top: 1px solid rgba(255,255,255,0.1);
            margin: 8px 0;
            display: none; /* Hide dividers in mobile for cleaner look */
        }

        /* Content wrapper tidak terpengaruh */
        #content-wrapper {
            width: 100% !important;
            margin-left: 0 !important;
        }
    }

    @media (min-width: 768px) {
        #mobile-nav,
        #mobile-nav-backdrop {
            display: none !important;
        }
    }
    </style>

    {{-- AlpineJS --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Custom CSS tambahan per halaman --}}
    @stack('styles')
</head>

<body id="page-top">

    {{-- Page Wrapper --}}
    <div id="wrapper">

        {{-- ===== MOBILE NAV (hanya mobile) ===== --}}
        <div id="mobile-nav-backdrop"></div>
        <div id="mobile-nav">
            <div id="mobile-nav-close">
                <button id="btn-mobile-nav-close"><i class="fas fa-times"></i></button>
            </div>
            {{-- Diisi oleh Sidebar Partial --}}
            <div id="mobile-nav-content">
                @include('partials.sidebar', ['isMobile' => true])
            </div>
        </div>
        {{-- ===== END MOBILE NAV ===== --}}

        {{-- ===== SIDEBAR ===== --}}
        @include('partials.sidebar')
        {{-- ===== END SIDEBAR ===== --}}

        {{-- Content Wrapper --}}
        <div id="content-wrapper" class="d-flex flex-column">

            {{-- Main Content --}}
            <div id="content">

                {{-- ===== NAVBAR / TOPBAR ===== --}}
                @include('partials.navbar')
                {{-- ===== END NAVBAR ===== --}}

                {{-- Begin Page Content --}}
                <div class="container-fluid">

                    {{-- Flash Messages --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('warning') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    {{-- Page Content --}}
                    @yield('content')

                </div>
                {{-- End Page Content --}}

            </div>
            {{-- End Main Content --}}

            {{-- ===== FOOTER ===== --}}
            @include('partials.footer')
            {{-- ===== END FOOTER ===== --}}

        </div>
        {{-- End Content Wrapper --}}

    </div>
    {{-- End Page Wrapper --}}

    {{-- Scroll to Top Button --}}
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    {{-- Logout Modal --}}
    @include('partials.logout-modal')

    {{-- Bootstrap core JavaScript --}}
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    {{-- Core plugin JavaScript --}}
    <script src="{{ asset('assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    {{-- Custom scripts for all pages --}}
    <script src="{{ asset('assets/js/sb-admin-2.min.js') }}"></script>

    {{-- Mobile Navigation --}}
    <script>
         $(document).ready(function () {

        // Buka menu
        $('#sidebarToggleTop').on('click', function () {
            $('#mobile-nav').toggleClass('open');
            $('#mobile-nav-backdrop').toggleClass('open');
        });

        // Tutup via tombol X
        $('#btn-mobile-nav-close').on('click', function () {
            $('#mobile-nav').removeClass('open');
            $('#mobile-nav-backdrop').removeClass('open');
        });

        // Tutup via backdrop
        $('#mobile-nav-backdrop').on('click', function () {
            $('#mobile-nav').removeClass('open');
            $('#mobile-nav-backdrop').removeClass('open');
        });

        // Handle submenu toggle
        $('#mobile-nav').on('click', '.mobile-nav-toggle', function () {
            var target = $(this).data('target');
            $(target).slideToggle(200);
            $(this).toggleClass('collapsed');
        });

    });
    </script>

    {{-- Custom JS tambahan per halaman --}}
    @stack('scripts')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                timer: 2000,
                showConfirmButton: false
            })
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
                confirmButtonColor: '#e74a3b'
            })
        </script>
    @endif

</body>

</html>
