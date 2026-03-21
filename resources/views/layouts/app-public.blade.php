<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Amore Academy')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('SchoolLogo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Display+Playfair:wght@400;700&family=Inter:wght@400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    @stack('styles')
</head>

<body id="top">
    <nav class="navbar navbar-expand-lg navbar-light navbar-custom app-global-header">
        <div class="container">
            <a class="navbar-brand text-white fw-bold fs-3" href="{{ route('welcome') }}#top">Amore Academy</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavPublic">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse text-white" id="navbarNavPublic">
                <ul class="navbar-nav mx-auto list-bg">
                    <li class="nav-item text-center"><a class="nav-link text-white" href="{{ route('welcome') }}#about">Us</a></li>
                    <li class="nav-item text-center"><a class="nav-link text-white" href="{{ route('welcome') }}#news">News</a></li>
                    <li class="nav-item text-center"><a class="nav-link text-white" href="{{ route('welcome') }}#faculty">Faculty</a></li>
                    <li class="nav-item text-center"><a class="nav-link text-white" href="{{ route('welcome') }}#gallery">Gallery</a></li>
                    <li class="nav-item text-center"><a class="nav-link text-white" href="{{ route('welcome') }}#contacts">Contact</a></li>
                </ul>

                <div class="text-center">
                    <a href="{{ route('login') }}" class="nav-item btn rounded-4 content_homepage position-relative overflow-hidden text-decoration-none d-flex align-items-center justify-content-center">
                        <i class="bi bi-box-arrow-in-right me-2"></i>
                        <span>Login</span>
                        <span class="btn-shimmer"></span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <div class="top-bar top-border mt-2">
        <div class="container py-3">
            <div class="row align-items-center">
                <div class="col-12 d-flex align-items-center justify-content-center">
                    <a href="#" class="small mr-3">
                        <span class="me-1"><i class="bi bi-question-circle"></i></span>
                        <span class="small1">Have a questions?</span>
                    </a>
                    <a href="#" class="small mr-3">
                        <span class="me-1"><i class="bi bi-telephone"></i></span>
                        <span class="small1">10 20 123 456</span>
                    </a>
                    <a href="#" class="small mr-3">
                        <span class="me-1"><i class="bi bi-envelope"></i></span>
                        <span class="small1">amoreacademy@gmail.com</span>
                    </a>
                    <a href="#" class="small mr-3">
                        <span class="me-1"><i class="bi bi-pin-map"></i></span>
                        <span class="small1">Trece Martires City Cavite</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @vite(['resources/js/app.js'])

    @stack('scripts')
</body>

</html>