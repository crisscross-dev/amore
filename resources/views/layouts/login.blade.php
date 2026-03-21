@extends('layouts.app-public')

@section('title', 'Login - Amore Academy')

@vite(['resources/css/auth.css', 'resources/js/auth.js'])

@section('content')
<div class="container d-flex justify-content-center align-items-center login-center-container">
  <div class="col-md-5 ">
    <div class="card shadow-lg p-4 animate__animated animate__fadeIn login-form" id="hero-content">
  <a href="index.html" class="btn btn-link text-secondary mb-3 p-0 login-back-link">
        <span class="icon-arrow-left mr-2"></span>Back
      </a>
      <div class="text-center mb-4">
        <h2 class="heading mb-2">Login to Your Account</h2>
        <p class="fw-bold fs-6" style="color: #495057;">Welcome back to Amore Academy</p>
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
            required 
          />
        </div>
        
        <div class="form-group mb-3 position-relative">
          <x-form.label for="password">Password</x-form.label>
          <div class="input-group">
            <input 
              type="password" 
              class="form-control @error('password') is-invalid @enderror" 
              id="password" 
              name="password" 
              placeholder="Password"
              required
            >
            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
              <i class="bi bi-eye" id="togglePasswordIcon"></i>
            </button>
          </div>
          @error('password')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>
        
        <x-ui.button type="submit" variant="secondary" class="login-button" :fullWidth="true">
          Login
        </x-ui.button>
        
        <div class="mt-3 text-center">
          <a href="{{ route('password.request') }}" class="small text-black">Forgot password?</a>
        </div>
      </form>
      <div class="mt-3 text-center">
        <x-ui.button 
          variant="secondary" 
          class="login-button" 
          :fullWidth="true"
          href="{{ route('register') }}"
        >
          Create Account
        </x-ui.button>
      </div>
    </div>
  </div>
</div>

@endsection
