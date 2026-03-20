@extends('layouts.app')

@section('title', 'Create Account - Amore Academy')

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

<div class="container register-container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
    <div class="card shadow-lg p-4 p-md-4 rounded-3 border-0 w-100" style="max-width: 680px; margin: 0 auto; background-color: rgba(255, 255, 255, 0.9);"> <!-- Compact layout with reduced width -->
            <div class="text-center mb-4">
                <!-- School Logo -->
                <img src="images/logo.png" alt="School Logo" style="width: 90px;" class="mb-3 d-block mx-auto">
                
                <!-- Title -->
                <h2 class="fw-bold text-success heading-times">CREATE YOUR ACCOUNT</h2>
                <p class="text-success">Supporting Excellence in Teaching</p>
            </div>
            <!-- Success Message -->
            @if(session('success'))
                <x-ui.alert type="success" :dismissible="true">
                    {{ session('success') }}
                </x-ui.alert>
            @endif
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

            <form method="POST" action="{{ route('register.store') }}" enctype="multipart/form-data">
                @csrf

                <!-- Personal Information -->
                <h5 class="fw-bold text-success mb-3">Personal Information</h5>
                <div class="row mb-3">
                    <div class="col-md-4 mb-2">
                        <x-form.input name="first_name" placeholder="Enter First Name" class="border-success" required />
                    </div>
                    <div class="col-md-4 mb-2">
                        <x-form.input name="middle_name" placeholder="Enter Middle Name" class="border-success" required />
                    </div>
                    <div class="col-md-4 mb-2">
                        <x-form.input name="last_name" placeholder="Enter Last Name" class="border-success" required />
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-2">
                        <x-form.input type="email" name="email" placeholder="Enter Email Address" class="border-success" required />
                    </div>
                    <div class="col-md-6 mb-2">
                        <x-form.input name="contact_number" placeholder="Contact Number" class="border-success" required />
                    </div>
                </div>

                <!-- Account Information -->
                <h5 class="fw-bold text-success mb-3">Account Type (Faculty)</h5>

                <input type="hidden" name="account_type" value="faculty">
                <div class="row mb-3">
                    <div class="col-md-6 mb-2">
                        <div class="input-group">
                            <input 
                              type="password" 
                              class="form-control border-success @error('password') is-invalid @enderror" 
                              id="password" 
                              name="password" 
                              placeholder="Enter Password"
                              required
                            >
                            <button class="btn btn-outline-success" type="button" id="togglePassword">
                                <i class="bi bi-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                        <div class="progress mt-2" style="height: 8px;">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 0%" id="passwordStrength"></div>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="input-group">
                            <input 
                              type="password" 
                              class="form-control border-success" 
                              id="password_confirmation" 
                              name="password_confirmation" 
                                                            data-sync-with-password="true"
                              placeholder="Confirm Password"
                              required
                            >
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-grid mb-3">
                    <x-ui.button type="submit" variant="success" class="fw-bold" :fullWidth="true">
                        Create Account
                    </x-ui.button>
                </div>

                <!-- Login Link -->
                <div class="mt-2 text-center">
                    <p class="text-dark">Already have an account?
                        <a href="{{ route('login') }}" class="fw-bold text-success">Login</a>
                    </p>
                </div>
            </form>
        </div>
        </div>
    </div>
</div>

@endsection
