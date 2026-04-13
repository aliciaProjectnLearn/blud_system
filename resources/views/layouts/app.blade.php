<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>{{ config('app.name', 'MyApp') }} - @yield('title', 'Dashboard')</title>

    {{-- Font Awesome --}}
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    {{-- SB Admin 2 CSS --}}
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <style>
        /* ── Mobile Sidebar Overlay ── */
        @media (max-width: 767.98px) {

            /* Overlay gelap di belakang sidebar */
            #sidebar-overlay {
                display: none;
                position: fixed;    
                pointer-events: none;
                top: 0; left: 0;
                width: 100%; height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1040;
                transition: opacity 0.3s ease;
            }

            #sidebar-overlay.show {
                display: block;
                opacity: 1;
                pointer-events: auto;
            }

            /* Sidebar full-screen overlay di mobile */
            #accordionSidebar {
                position: fixed !important;
                top: 0; left: 0;
                width: 280px !important;
                max-width: 280px !important;
                height: 100vh !important;
                z-index: 1050;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                overflow-y: auto;
                box-shadow: 4px 0 15px rgba(0, 0, 0, 0.3);
            }

            #accordionSidebar.mobile-open {
                transform: translateX(0);
            }

            /* Tombol close di dalam sidebar */
            .sidebar-close-btn {
                display: flex !important;
                position: absolute;
                top: 12px; right: 12px;
                width: 32px; height: 32px;
                background: rgba(255,255,255,0.2);
                border: none;
                border-radius: 50%;
                color: white;
                font-size: 16px;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                z-index: 10;
                transition: background 0.2s;
            }

            .sidebar-close-btn:hover {
                background: rgba(255,255,255,0.35);
            }

            /* Content wrapper tidak terdorong */
            #content-wrapper {
                width: 100% !important;
                margin-left: 0 !important;
                position: relative;
                z-index: 1;
            }

            /* Sidebar brand padding kanan untuk tombol close */
            .sidebar-brand {
                padding-right: 48px !important;
            }
        }

        /* Sembunyikan tombol close di desktop */
        @media (min-width: 768px) {
            .sidebar-close-btn {
                display: none !important;
            }
            #sidebar-overlay {
                display: none !important;
            }
        }
    </style>

    {{-- Custom CSS tambahan per halaman --}}
    @stack('styles')
</head>

<body id="page-top">

    {{-- Page Wrapper --}}
    <div id="wrapper">

        {{-- Mobile Sidebar Overlay --}}
        <div id="sidebar-overlay"></div>

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
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('warning'))
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

    {{-- Custom JS tambahan per halaman --}}
    @stack('scripts')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(session('success'))
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

    <script>
    $(function() {

        // ── Hanya aktif di mobile (< 768px) ──────────────────────────
        function isMobile() {
            return window.innerWidth < 768;
        }

        // ── Buka sidebar di mobile ────────────────────────────────────
        function openMobileSidebar() {
            if (!isMobile()) return;
            $('#accordionSidebar').addClass('mobile-open');
            $('#sidebar-overlay').addClass('show');
            $('body').css('overflow', 'hidden');
        }

        // ── Tutup sidebar di mobile ───────────────────────────────────
        function closeMobileSidebar() {
            $('#accordionSidebar').removeClass('mobile-open');
            $('#sidebar-overlay').removeClass('show');
            $('body').css('overflow', '');
        }

        // ── Intercept tombol hamburger SB Admin 2 ────────────────────
        // SB Admin 2 menggunakan #sidebarToggle untuk toggle sidebar
        $(document).on('click', '#sidebarToggle, #sidebarToggleTop', function(e) {
            if (isMobile()) {
                e.preventDefault();
                e.stopPropagation();
                if ($('#accordionSidebar').hasClass('mobile-open')) {
                    closeMobileSidebar();
                } else {
                    openMobileSidebar();
                }
            }
        });

        // ── Klik overlay untuk tutup ──────────────────────────────────
        $(document).on('click', '#sidebar-overlay', function() {
            closeMobileSidebar();
        });

        // ── Tombol X di dalam sidebar ─────────────────────────────────
        $(document).on('click', '.sidebar-close-btn', function() {
            closeMobileSidebar();
        });

        // ── Tutup sidebar saat resize ke desktop ──────────────────────
        $(window).on('resize', function() {
            if (!isMobile()) {
                closeMobileSidebar();
            }
        });

        // ── Tutup sidebar saat klik link menu di mobile ───────────────
        // (agar tidak perlu klik X setelah navigasi)
        $('#accordionSidebar').on('click', 'a:not([data-toggle])', function() {
            if (isMobile()) {
                closeMobileSidebar();
            }
        });
    });
    </script>

</body>

</html>
