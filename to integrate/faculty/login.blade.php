@extends('layouts.app')

@section('title', 'Login - Amore Academy')
@push('scripts')
  @vite(['resources/js/auth.js'])
@endpush
@section('content')
<style>
  input[type="password"]::-ms-reveal,
  input[type="password"]::-ms-clear {
    display: none;
  }
</style>
<div class="container d-flex justify-content-center align-items-center login-center-container">
  <div class="col-md-5">
    <div class="card shadow-lg p-4 animate__animated animate__fadeIn login-form" id="hero-content" style="background-color: rgba(255, 255, 255, 0.9);">
      <div class="d-flex justify-content-center mb-3">
        <a href="{{ route('register') }}" class="d-inline-flex align-items-center text-success text-decoration-underline login-back-link" style="gap: 0.35rem;">
          <span class="icon-arrow-left"></span><span>Back</span>
        </a>
      </div>
      <div class="text-center mb-4">
        <h2 class="heading mb-2">Login to Your Account</h2>
        <p class="text-muted">Welcome back to Amore Academy</p>
      </div>
      <form method="post" action="{{ route('loginAuth') }}">
        @csrf
        
        <!-- Error Messages -->
        @if($errors->any())
            <x-ui.alert type="danger" :dismissible="true">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-ui.alert>
        @endif

        <!-- Success Message -->
        @if(session('success'))
            <x-ui.alert type="success" :dismissible="true">
                {{ session('success') }}
            </x-ui.alert>
        @endif

        <div class="form-group mb-3">
          <x-form.label for="email">Email address</x-form.label>
          <x-form.input 
            type="email" 
            id="email" 
            name="email" 
            placeholder="Enter email" 
            class="bg-white" 
            required 
          />
        </div>
        
        <div class="form-group mb-3 position-relative">
          <x-form.label for="password">Password</x-form.label>
          <div class="input-group">
            <input 
              type="password" 
              class="form-control bg-white @error('password') is-invalid @enderror" 
              id="password" 
              name="password" 
              placeholder="Password"
              required
            >
            <button class="btn btn-outline-success" type="button" id="togglePassword">
              <i class="bi bi-eye" id="togglePasswordIcon"></i>
            </button>
          </div>
          @error('password')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>
        
        <x-ui.button type="submit" variant="success" class="login-button" :fullWidth="true">
          Login
        </x-ui.button>
        
        <div class="mt-4 text-center">
          <a href="{{ route('password.request') }}" class="small text-black">Forgot password?</a>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
