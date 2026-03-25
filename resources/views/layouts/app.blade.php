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
@php
$currentUser = auth()->user();
$accountType = $currentUser ? strtolower((string) $currentUser->account_type) : null;
$useAppShell = auth()->check()
&& in_array($accountType, ['admin', 'faculty'], true)
&& !Request::routeIs('welcome');
$isLoggedIn = auth()->check();
@endphp

<body id="top" class="{{ $useAppShell ? 'has-app-shell' : '' }}" data-account-type="{{ $accountType ?? '' }}">

  @if($useAppShell)
  <div class="app-shell-layout">
    <header class="app-shell-header">
      <nav class="navbar navbar-light navbar-custom app-global-header app-auth-header">
        <div class="container-fluid app-auth-header-inner">
          <div class="app-header-spacer" aria-hidden="true"></div>

          <span class="navbar-brand text-white fw-bold fs-3 app-brand-centered">Amore Academy</span>

          <div class="text-end app-header-actions">
            <span class="nav-item btn rounded-4 content_homepage">
              <a href="{{ route('profile.edit') }}" class="text-decoration-none text-white">
                <i class="bi bi-person-circle me-2"></i>
                {{ Auth::user()->first_name }}
              </a>
            </span>
          </div>
        </div>
      </nav>
    </header>

    <aside class="app-shell-sidebar d-none d-md-block">
      @if($accountType === 'admin')
      @include('partials.admin-sidebar')
      @elseif($accountType === 'faculty')
      @include('partials.faculty-sidebar')
      @endif
    </aside>

    <main class="app-shell-main" role="main">
      <div class="app-shell-content">
        @yield('content')
      </div>
    </main>
  </div>
  @else
  {{-- Navbar --}}
  <nav class="navbar navbar-expand-lg navbar-light navbar-custom app-global-header {{ $isLoggedIn ? 'app-auth-header' : '' }}">
    <div class="container {{ $isLoggedIn ? 'app-auth-header-inner app-auth-header-inner-simple' : '' }}">
      @if($isLoggedIn)
      <div class="app-header-spacer" aria-hidden="true"></div>
      @endif

      <span class="navbar-brand text-white fw-bold fs-3 {{ $isLoggedIn ? 'app-brand-centered' : '' }}">Amore Academy</span>

      @guest
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      @endguest

      @guest
      <div class="collapse navbar-collapse text-white" id="navbarNav">
        <ul class="navbar-nav mx-auto list-bg">
          <li class="nav-item text-center"><a class="nav-link text-white" href="{{ route('welcome') }}#about">About Us</a></li>
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
      @else
      <div class="text-end app-header-actions">
        <span class="nav-item btn rounded-4 content_homepage">
          <a href="{{ route('profile.edit') }}" class="text-decoration-none text-white">
            <i class="bi bi-person-circle me-2"></i>
            {{ Auth::user()->first_name }}
          </a>
        </span>
      </div>
      @endguest
    </div>
  </nav>

  {{-- Page Content --}}
  <main>
    @yield('content')
  </main>
  @endif

  <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
  @vite(['resources/js/app.js'])
  <script src="{{ asset('js/swal.js') }}"></script>

  @stack('scripts')
</body>

</html>