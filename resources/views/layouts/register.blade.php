@extends('layouts.app-public')

@section('title', 'Create Account - Amore Academy')

@vite(['resources/css/auth.css', 'resources/js/auth.js', 'resources/js/register.js'])

@section('content')

<div class="container register-container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
    <div class="card shadow-lg p-4 p-md-5 rounded-3 border-0 w-100" style="min-height: 100vh; max-width: 900px; margin: 0 auto;"> <!-- Maximized space, reduced min-height, increased padding -->
            <div class="text-center mb-4">
                <!-- School Logo -->
                <img src="images/background.png" alt="School Logo" style="width: 120px;" class="mb-3 d-block mx-auto">
                
                <!-- Title -->
                <h2 class="fw-bold text-success heading-times">CREATE YOUR ACCOUNT</h2>
                <p class="text-success">Join Amore Academy Today!</p>
            </div>            <!-- Success Message -->

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
                <input type="hidden" name="registration_nonce" value="{{ $registrationNonce ?? '' }}">

                <!-- Personal Information -->
                <h5 class="fw-bold text-success mb-3">Personal Information</h5>
                <div class="row mb-3">
                    <div class="col-md-3 mb-2">
                        <x-form.input name="first_name" placeholder="Enter First Name" class="border-success js-name-field" required />
                    </div>
                    <div class="col-md-3 mb-2">
                        <x-form.input name="middle_name" placeholder="Enter Middle Name" class="border-success js-name-field" required />
                    </div>
                    <div class="col-md-3 mb-2">
                        <x-form.input name="last_name" placeholder="Enter Last Name" class="border-success js-name-field" required />
                    </div>
                    <div class="col-md-3 mb-2">
                        <x-form.input name="suffix" placeholder="Suffix (e.g., Jr., III)" class="border-success" />
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-2">
                        <x-form.input type="email" name="email" placeholder="Enter Email Address" class="border-success" required />
                    </div>
                    <div class="col-md-6 mb-2">
                        <x-form.input
                            name="contact_number"
                            placeholder="Contact Number"
                            class="border-success js-contact-number"
                            inputmode="numeric"
                            maxlength="11"
                            pattern="\d{11}"
                            required />
                    </div>
                </div>

                <!-- Account Information -->
                <h5 class="fw-bold text-success mb-3">Account Information</h5>

                <input type="hidden" name="account_type" value="faculty">

                <div class="row mb-3">
                    <div class="col-md-6 mb-2">
                        <label class="form-label text-success fw-bold">Account Type</label>
                        <input
                            type="text"
                            class="form-control border-success"
                            value="Faculty"
                            readonly
                        >
                    </div>
                </div>


                <div class="row mb-2">
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
                              placeholder="Confirm Password"
                              required
                            >
                        </div>
                    </div>
                </div>

                <!-- Password Strength -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted">Password Strength:</small>
                            <small id="strengthText" class="fw-bold text-danger">Weak</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 0%" id="passwordStrength"></div>
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
