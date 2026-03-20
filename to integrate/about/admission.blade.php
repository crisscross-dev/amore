<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>@yield('title', 'Admission - Amore Academy')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Display+Playfair:wght@400;700&family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  
  @vite(['resources/css/app.css', 'resources/css/admissions.css'])
  
  @stack('styles')
</head>
<body>

  {{-- Top Bar --}}
  <div class="top-bar top-border mt-1">
    <div class="container py-3">
      <div class="row align-items-center">
        <div class="col-12 d-flex align-items-center">
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

  {{-- Navbar --}}
  <nav class="navbar navbar-expand-lg navbar-light navbar-custom">
    <div class="container">
      <a class="navbar-brand text-white fw-bold" href="{{ route('welcome') }}">Amore Academy</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse text-white" id="navbarNav">
        <ul class="navbar-nav mx-auto list-bg">
          <li class="nav-item text-white text-center"><a class="nav-link text-white" href="{{ route('welcome') }}">Home</a></li>
          <li class="nav-item text-center"><a class="nav-link text-white" href="#">About Us</a></li>
          <li class="nav-item text-center"><a class="nav-link text-white" href="#">News</a></li>
          <li class="nav-item text-center"><a class="nav-link text-white" href="#">Faculty</a></li>
          <li class="nav-item text-center"><a class="nav-link text-white" href="#">Gallery</a></li>
          <li class="nav-item text-center"><a class="nav-link text-white" href="#">Contact</a></li>
        </ul>

        <div class="text-center">
            @auth
                <span class="nav-item btn rounded-4 content_homepage">
                    <a href="{{ route('dashboard') }}"> Logged in as {{ Auth::user()->first_name }} </a>
                </span>
            @else
                <a class="nav-item btn btn-primary rounded-4 content_homepage" href="/login">Login</a>
            @endauth
        </div>
      </div>
    </div>
  </nav>

  {{-- Page Content --}}
  <main>
    @yield('content')
  </main>

  @vite(['resources/js/app.js'])
  
  @stack('scripts')
</body>
</html>
