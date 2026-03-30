@extends('layouts.app-public')

@section('title', 'Forgot Password - Amore Academy')

@vite(['resources/css/auth.css', 'resources/js/auth.js'])

@section('content')
<div class="container d-flex justify-content-center align-items-center login-center-container">
  <div class="col-md-5">
    <div class="card shadow-lg p-4 animate__animated animate__fadeIn login-form" id="hero-content" style="background-color: rgba(255, 255, 255, 0.9);">
      <a href="{{ route('login') }}" class="btn btn-link text-success mb-3 p-0 login-back-link">
        <span class="icon-arrow-left mr-2"></span>Back to Login
      </a>
      
      <div class="text-center mb-4">
        <h2 class="heading mb-2">Forgot Password?</h2>
        <p class="text-muted">No worries! Enter your email and we'll send you reset instructions.</p>
      </div>

      <form method="post" action="{{ route('password.email') }}">
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

        <div class="form-group mb-3">
          <x-form.label for="email">Email address</x-form.label>
          <x-form.input 
            type="email" 
            id="email" 
            name="email" 
            placeholder="Enter your email address" 
            :value="old('email')"
            required 
          />
        </div>
        
        <x-ui.button type="submit" variant="success" class="login-button" :fullWidth="true">
          Send Reset Link
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
