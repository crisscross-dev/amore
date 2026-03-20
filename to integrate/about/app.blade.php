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

  {{-- Navbar --}}
  <nav class="navbar navbar-expand-lg navbar-light navbar-custom">
    <div class="container">
      <a class="navbar-brand text-white fw-bold" href="{{ route('welcome') }}#top">Amore Academy</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse text-white" id="navbarNav">
        <ul class="navbar-nav mx-auto  list-bg">
          <li class="nav-item text-center"><a class="nav-link text-white" href="{{ route('welcome') }}#about">About Us</a></li>
          <li class="nav-item text-center"><a class="nav-link text-white" href="{{ route('welcome') }}#news">News</a></li>
          <li class="nav-item text-center" ><a class="nav-link text-white" href="{{ route('welcome') }}#faculty">Faculty</a></li>
          <li class="nav-item text-center"><a class="nav-link text-white" href="{{ route('welcome') }}#gallery">Gallery</a></li>
          <li class="nav-item text-center"><a class="nav-link text-white" href="{{ route('welcome') }}#contacts">Contact</a></li>
        </ul>

        <div class="text-center">
            @auth
                <span class="nav-item btn rounded-4 content_homepage">
                    <a href="{{ route('dashboard') }}" class="text-decoration-none">
                        <i class="bi bi-person-circle me-2"></i>
                        {{ Auth::user()->first_name }}
                    </a>
                </span>
            @else
                <span class="nav-item btn rounded-4 content_homepage position-relative overflow-hidden">
                    <a href="{{ route('login') }}" class="text-decoration-none d-flex align-items-center justify-content-center">
                        <i class="bi bi-box-arrow-in-right me-2"></i>
                        <span>Login</span>
                    </a>
                    <span class="btn-shimmer"></span>
                </span>
            @endauth
        </div>


        <!-- <div class="text-center">
          @if (Request::is('login'))
            <a class="nav-item btn btn-primary rounded-4 content_homepage" href="/login">Login as Admin</a>
          @else 
            <a class="nav-item btn btn-primary rounded-4 content_homepage" href="/login">Login</a>
          @endif
        </div> -->
      </div>
    </div>
  </nav>

  {{-- Page Content --}}
  <main>
    @yield('content')
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @vite(['resources/js/app.js'])
  
  @stack('scripts')
</body>
</html>
