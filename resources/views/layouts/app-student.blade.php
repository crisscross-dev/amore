<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Amore Academy')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('SchoolLogo.png') }}">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sweetalert2.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Display+Playfair:wght@400;700&family=Inter:wght@400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/swal-custom.css'])
    <link href="{{ asset('css/bootstrap-icons.min.css') }}" rel="stylesheet">
    <style>
        html,
        body {
            margin: 0;
            min-height: 100vh;
            overflow: hidden;
        }
    </style>
    @stack('styles')
</head>

<body id="top" class="has-app-shell" data-account-type="student">
    <div class="app-shell-layout">
        <header class="app-shell-header">
            <nav class="navbar navbar-light navbar-custom app-global-header app-auth-header">
                <div class="container-fluid app-auth-header-inner">
                    <div class="app-header-spacer" aria-hidden="true"></div>

                    <span class="navbar-brand text-white fw-bold fs-3 app-brand-centered">Amore Academy</span>

                    <div class="text-end app-header-actions">
                        <span class="nav-item btn rounded-4 content_homepage">
                            <a href="{{ route('dashboard.student') }}" class="text-decoration-none">
                                <i class="bi bi-person-circle me-2"></i>
                                {{ Auth::user()->first_name }}
                            </a>
                        </span>
                    </div>
                </div>
            </nav>
        </header>

        <aside class="app-shell-sidebar d-none d-md-block">
            @include('partials.student-sidebar')
        </aside>

        <main class="app-shell-main" role="main">
            <div class="app-shell-content">
                @yield('content')
            </div>
        </main>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    @vite(['resources/js/app.js'])
    <script src="{{ asset('js/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('js/swal.js') }}"></script>

    @stack('scripts')
</body>

</html>