@extends('layouts.app')

@section('title', 'Create Announcement - Admin Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- TinyMCE CDN -->
<script src="/vendor/tinymce/tinymce.min.js" referrerpolicy="origin"></script>

<!-- Admin Dashboard CSS -->
@vite(['resources/css/layouts/dashboard-roles/dashboard-admin.css', 'resources/js/announcements.js'])

<div class="dashboard-container">
    <div class="container-fluid px-4">
        <div class="row">
            <!-- Left Profile Sidebar -->

            <!-- Main Content -->
            <main class="col-12">
                <!-- Mobile Profile (Hidden on Desktop) -->
                <div class="d-md-none mobile-profile mb-4">
                    <div class="text-center">
                        <img src="{{ asset('uploads/profile_picture/' . Auth::user()->profile_picture) }}" 
                             alt="Profile Picture" 
                             class="rounded-circle mb-3 border border-3 border-white"
                             width="80"
                             height="80">
                        
                        <h5 class="text-white mb-1">{{ Auth::user()->first_name ?? 'Admin' }} {{ Auth::user()->last_name ?? 'Name' }}</h5>
                        <p class="text-white-50 small mb-3">
                            Administrator | {{ Auth::user()->custom_id ?? 'ADMIN-0001' }}
                        </p>
                        
                        <!-- Mobile Navigation -->
                        <div class="row g-2">
                            <div class="col-4">
                                <a href="{{ route('dashboard.admin') }}" class="btn mobile-nav-btn w-100">
                                    <i class="fas fa-tachometer-alt d-block mb-1"></i>
                                    <small>Dashboard</small>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('calendar.index') }}" class="btn mobile-nav-btn w-100">
                                    <i class="fas fa-calendar-alt"></i>
                                    <small>Calendar</small>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('announcements.index') }}" class="btn mobile-nav-btn w-100 active">
                                    <i class="fas fa-bullhorn d-block mb-1"></i>
                                    <small>Announce</small>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="#" class="btn mobile-nav-btn w-100">
                                    <i class="fas fa-users-cog d-block mb-1"></i>
                                    <small>Accounts</small>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="#" class="btn mobile-nav-btn w-100">
                                    <i class="fas fa-chart-line d-block mb-1"></i>
                                    <small>Reports</small>
                                </a>
                            </div>
                        </div>
                        
                        <hr class="bg-white opacity-25 my-3">
                        
                        <button 
                            class="btn logout-btn w-100"
                            onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();"
                        >
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </button>
                        
                        <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>

                <!-- Page Header -->
                <div class="welcome-card mb-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                        <div>
                            <h4 class="mb-2">
                                <i class="fas fa-plus-circle me-2"></i>
                                Create New Announcement
                            </h4>
                            <p class="mb-0 opacity-90">
                                Post important announcements to Amore Academy community
                            </p>
                        </div>
                        <div class="d-none d-lg-block">
                            <a href="{{ route('announcements.index') }}" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>Back to List
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Mobile Back Button -->
                <div class="d-lg-none mb-3">
                    <a href="{{ route('announcements.index') }}" class="btn btn-outline-success w-100">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>

                <div class="row">
                    <!-- Form Section -->
                    <div class="col-lg-8 mb-4">
                        <div class="activity-card">
                            <div class="card-header">
                                <i class="fas fa-edit me-2"></i>Announcement Details
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('announcements.store') }}" enctype="multipart/form-data">
                                    @csrf

                                    <!-- Title -->
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control @error('title') is-invalid @enderror" 
                                               id="title" 
                                               name="title" 
                                               value="{{ old('title') }}" 
                                               required 
                                               maxlength="255"
                                               placeholder="Enter announcement title">
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Content -->
                                    <div class="mb-3">
                                        <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('content') is-invalid @enderror" 
                                                  id="content" 
                                                  name="content" 
                                                  rows="8" 
                                                  required
                                                  placeholder="Enter announcement content...">{{ old('content') }}</textarea>
                                        @error('content')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">You can use formatting in the content.</small>
                                    </div>

                                    <!-- Target Audience -->
                                    <div class="mb-3">
                                        <label for="target_audience" class="form-label">Visibility <span class="text-danger">*</span></label>
                                        <select class="form-select @error('target_audience') is-invalid @enderror" 
                                                id="target_audience" 
                                                name="target_audience" 
                                                required>
                                            <option value="">Select visibility</option>
                                            <option value="public" {{ old('target_audience') == 'public' ? 'selected' : '' }}>Public (Visible to everyone)</option>
                                            <option value="all" {{ old('target_audience') == 'all' ? 'selected' : '' }}>All Registered Users</option>
                                            <option value="students" {{ old('target_audience') == 'students' ? 'selected' : '' }}>Students Only</option>
                                            <option value="faculty" {{ old('target_audience') == 'faculty' ? 'selected' : '' }}>Faculty Only</option>
                                        </select>
                                        @error('target_audience')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Expiration Date -->
                                    <div class="mb-3">
                                        <label for="expires_at" class="form-label">Expiration Date (Optional)</label>
                                        <input type="datetime-local" 
                                               class="form-control @error('expires_at') is-invalid @enderror" 
                                               id="expires_at" 
                                               name="expires_at" 
                                               value="{{ old('expires_at') }}"
                                               min="{{ now()->format('Y-m-d\TH:i') }}">
                                        @error('expires_at')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Leave empty for permanent announcement</small>
                                    </div>

                                    <!-- Pin Announcement -->
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" 
                                               class="form-check-input" 
                                               id="is_pinned" 
                                               name="is_pinned" 
                                               value="1"
                                               {{ old('is_pinned') ? 'checked' : '' }}>
                                        <label class="form-check-label text-dark" for="is_pinned">
                                            <i class="fas fa-thumbtack me-1"></i> Pin this announcement (appears at top)
                                        </label>
                                    </div>

                                    <!-- File Attachments -->
                                    <div class="mb-3">
                                        <label for="attachments" class="form-label">Attachments (Optional)</label>
                                        <input type="file" 
                                               class="form-control @error('attachments.*') is-invalid @enderror" 
                                               id="attachments" 
                                               name="attachments[]" 
                                               multiple
                                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                        @error('attachments.*')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Accepted: PDF, DOC, DOCX, JPG, PNG (Max: 10MB per file)
                                        </small>
                                    </div>

                                    <hr class="my-4">

                                    <!-- Submit Buttons -->
                                    <div class="d-flex justify-content-between flex-wrap gap-2">
                                        <a href="{{ route('announcements.index') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-1"></i>Cancel
                                        </a>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-paper-plane me-1"></i>Post Announcement
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Info -->
                    <div class="col-lg-4">
                        <!-- Guidelines Card -->
                        <div class="activity-card mb-4">
                            <div class="card-header bg-success text-white">
                                <i class="fas fa-info-circle me-2"></i>Posting Guidelines
                            </div>
                            <div class="card-body">
                                <ul class="guidelines-list mb-0">
                                    <li><i class="fas fa-check text-success me-2"></i>Write clear and concise titles</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Target the right audience</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Set expiration for time-sensitive announcements</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Pin important announcements</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<style>
.guidelines-list li {
    color: #333;
}
</style>

@endsection

