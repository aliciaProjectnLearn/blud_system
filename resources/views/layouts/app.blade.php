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

    {{-- Custom CSS tambahan per halaman --}}
    @stack('styles')
</head>

<body id="page-top">

    {{-- Page Wrapper --}}
    <div id="wrapper">

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

</body>

</html>
