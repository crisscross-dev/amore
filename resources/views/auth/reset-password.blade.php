@extends('layouts.app')

@section('title', 'Reset Password - Amore Academy')

@vite(['resources/css/auth.css', 'resources/js/auth.js'])

@section('content')
<div class="container d-flex justify-content-center align-items-center login-center-container">
  <div class="col-md-5">
    <div class="card shadow-lg p-4 animate__animated animate__fadeIn login-form" id="hero-content">
      <a href="{{ route('login') }}" class="btn btn-link text-secondary mb-3 p-0 login-back-link">
        <span class="icon-arrow-left mr-2"></span>Back to Login
      </a>
      
      <div class="text-center mb-4">
        <h2 class="heading mb-2">Reset Your Password</h2>
        <p class="text-muted">Enter your new password below</p>
      </div>

      <form method="post" action="{{ route('password.update') }}">
        @csrf
        
        <!-- Hidden Token -->
        <input type="hidden" name="token" value="{{ $token }}">
        
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
            placeholder="Enter your email address" 
            :value="old('email', request()->email)"
            required 
          />
        </div>

        <div class="form-group mb-3 position-relative">
          <x-form.label for="password">New Password</x-form.label>
          <div class="input-group">
            <input 
              type="password" 
              class="form-control @error('password') is-invalid @enderror" 
              id="password" 
              name="password" 
              placeholder="Enter new password"
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

        <div class="form-group mb-3 position-relative">
          <x-form.label for="password_confirmation">Confirm Password</x-form.label>
          <div class="input-group">
            <input 
              type="password" 
              class="form-control @error('password_confirmation') is-invalid @enderror" 
              id="password_confirmation" 
              name="password_confirmation" 
              placeholder="Confirm new password"
              required
            >
            <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
              <i class="bi bi-eye" id="togglePasswordConfirmIcon"></i>
            </button>
          </div>
          @error('password_confirmation')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>
        
        <x-ui.button type="submit" variant="secondary" class="login-button" :fullWidth="true">
          Reset Password
        </x-ui.button>
      </form>

      <div class="mt-3 text-center">
        <p class="text-muted small">Remember your password? 
          <a href="{{ route('login') }}" class="text-success">Login here</a>
        </p>
      </div>
    </div>
  </div>
</div>
@endsection
