@extends('layouts.app')

@section('title', 'Edit Profile - Admin Dashboard - Amore Academy')

@section('content')
<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Admin Edit Profile CSS -->
@vite(['resources/css/admin/dashboard-admin-edit.css'])

<div class="edit-profile-container">
    <div class="container-fluid px-4 px-xl-5">
        <div class="row justify-content-center">
            <div class="col-12" style="max-width: 1600px;">

                <div class="header-title d-flex align-items-center justify-content-between mb-2">
                    <h5 class="mb-2 fw-semibold text-success">
                        <i class="fas fa-user-edit me-2"></i>Edit Profile
                    </h5>
                    <!-- <div class="d-none d-lg-block">
                        <a href="{{ route('admin.faculty-positions.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus me-2"></i>New Position
                        </a>
                    </div> -->
                </div>

                <!-- Success Message -->
                @if(session('success'))
                <div class="mb-3">
                    <x-ui.alert type="success" :dismissible="true">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    </x-ui.alert>
                </div>
                @endif

                <!-- Error Messages -->
                @if($errors->any())
                <div class="mb-3">
                    <x-ui.alert type="danger" :dismissible="true">
                        <strong><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-ui.alert>
                </div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">

                        <!-- Profile Picture Card -->
                        <div class="col-xl-3 col-lg-4">
                            <div class="profile-picture-card">
                                <div class="card-icon">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <h5 class="mb-4">
                                    <i class="fas fa-image me-2"></i>Profile Picture
                                </h5>

                                <div class="profile-pic-wrapper">
                                    <div class="profile-pic-container-edit">
                                        <img src="{{ asset('uploads/profile_picture/' . Auth::user()->profile_picture) }}"
                                            alt="Profile Picture"
                                            class="profile-pic-preview"
                                            id="profilePreview">
                                        <div class="profile-pic-overlay">
                                            <i class="fas fa-camera fa-2x"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <x-form.file-input
                                        name="profile_picture"
                                        accept="image/*"
                                        id="profilePictureInput" />
                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-info-circle me-1"></i>Recommended: Square image, max 2MB
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Personal Information Card -->
                        <div class="col-xl-9 col-lg-8">
                            <div class="profile-form-card">
                                <div class="card-icon">
                                    <i class="fas fa-user"></i>
                                </div>
                                <h5 class="mb-4">
                                    <i class="fas fa-id-card me-2"></i>Personal Information
                                </h5>

                                <div class="row g-3">
                                    <!-- First Name -->
                                    <div class="col-md-6">
                                        <x-form.label for="first_name">
                                            <i class="fas fa-user me-1"></i>First Name
                                        </x-form.label>
                                        <x-form.input
                                            id="first_name"
                                            name="first_name"
                                            :value="old('first_name', Auth::user()->first_name)"
                                            required />
                                    </div>

                                    <!-- Middle Name -->
                                    <div class="col-md-6">
                                        <x-form.label for="middle_name">
                                            <i class="fas fa-user me-1"></i>Middle Name
                                        </x-form.label>
                                        <x-form.input
                                            id="middle_name"
                                            name="middle_name"
                                            :value="old('middle_name', Auth::user()->middle_name)"
                                            required />
                                    </div>

                                    <!-- Last Name -->
                                    <div class="col-md-6">
                                        <x-form.label for="last_name">
                                            <i class="fas fa-user me-1"></i>Last Name
                                        </x-form.label>
                                        <x-form.input
                                            id="last_name"
                                            name="last_name"
                                            :value="old('last_name', Auth::user()->last_name)"
                                            required />
                                    </div>

                                    <!-- Admin ID -->
                                    <div class="col-md-6">
                                        <x-form.label for="custom_id">
                                            <i class="fas fa-id-badge me-1"></i>Admin ID
                                        </x-form.label>
                                        <x-form.input
                                            id="custom_id"
                                            name="custom_id"
                                            :value="old('custom_id', Auth::user()->custom_id)"
                                            disabled />
                                        <small class="text-muted">
                                            <i class="fas fa-lock me-1"></i>Admin ID cannot be changed
                                        </small>
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-6">
                                        <x-form.label for="email">
                                            <i class="fas fa-envelope me-1"></i>Email Address
                                        </x-form.label>
                                        <x-form.input
                                            type="email"
                                            id="email"
                                            name="email"
                                            :value="old('email', Auth::user()->email)"
                                            required />
                                    </div>

                                    <!-- Contact Number -->
                                    <div class="col-md-6">
                                        <x-form.label for="contact_number">
                                            <i class="fas fa-phone me-1"></i>Contact Number
                                        </x-form.label>
                                        <x-form.input
                                            id="contact_number"
                                            name="contact_number"
                                            :value="old('contact_number', Auth::user()->contact_number)"
                                            placeholder="+63 912 345 6789"
                                            required />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Security Settings Card -->
                        <div class="col-12">
                            <div class="profile-form-card">
                                <div class="card-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <h5 class="mb-4">
                                    <i class="fas fa-lock me-2"></i>Security Settings
                                </h5>

                                <div class="alert alert-info border-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Password Change:</strong> Leave the password fields blank if you don't want to change your password.
                                </div>

                                <div class="row g-3">
                                    <!-- New Password -->
                                    <div class="col-md-6">
                                        <x-form.label for="password">
                                            <i class="fas fa-key me-1"></i>New Password
                                        </x-form.label>
                                        <x-form.input
                                            type="password"
                                            id="password"
                                            name="password"
                                            placeholder="Enter new password (optional)" />
                                        <small class="text-muted">
                                            <i class="fas fa-shield-alt me-1"></i>Minimum 8 characters
                                        </small>
                                    </div>

                                    <!-- Confirm Password -->
                                    <div class="col-md-6">
                                        <x-form.label for="password_confirmation">
                                            <i class="fas fa-key me-1"></i>Confirm New Password
                                        </x-form.label>
                                        <x-form.input
                                            type="password"
                                            id="password_confirmation"
                                            name="password_confirmation"
                                            placeholder="Confirm new password" />
                                        <small class="text-muted">
                                            <i class="fas fa-check-circle me-1"></i>Must match new password
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-12">
                            <div class="profile-actions">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                    <small class="text-muted">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        Make sure all information is correct before saving
                                    </small>
                                    <div class="d-flex gap-2">
                                        <x-ui.button variant="secondary" type="button" onclick="window.history.back()">
                                            <i class="fas fa-times me-2"></i>Cancel
                                        </x-ui.button>
                                        <x-ui.button type="submit" variant="success" class="save-button">
                                            <i class="fas fa-save me-2"></i>Save Changes
                                        </x-ui.button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<!-- Image Preview Script -->
<script>
    document.getElementById('profilePictureInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profilePreview').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection