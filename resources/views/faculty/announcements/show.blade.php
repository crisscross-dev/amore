@extends('layouts.app')

@section('title', 'View Announcement - Faculty Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Faculty Dashboard CSS -->
@vite(['resources/css/layouts/dashboard-roles/dashboard-faculty.css', 'resources/js/announcements.js'])

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

                        <h5 class="text-white mb-1">{{ Auth::user()->first_name ?? 'Faculty' }} {{ Auth::user()->last_name ?? 'Name' }}</h5>
                        <p class="text-white-50 small mb-3">
                            Faculty | {{ Auth::user()->custom_id ?? 'EMP-0001' }}
                        </p>

                        <!-- Mobile Navigation -->
                        <div class="row g-2">
                            <div class="col-4">
                                <a href="{{ route('dashboard.faculty') }}" class="btn mobile-nav-btn w-100">
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
                                    <i class="fas fa-book d-block mb-1"></i>
                                    <small>Subjects</small>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="#" class="btn mobile-nav-btn w-100">
                                    <i class="fas fa-graduation-cap d-block mb-1"></i>
                                    <small>Grades</small>
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
                                <i class="fas fa-bullhorn me-2"></i>
                                Announcement Details
                            </h4>
                            <p class="mb-0 opacity-90">
                                View full announcement information
                            </p>
                        </div>
                        <div class="d-none d-lg-block">
                            <a href="{{ route('announcements.index') }}" class="btn btn-success btn-lg">
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
                    <!-- Announcement Content -->
                    <div class="col-lg-8 mb-4">
                        <div class="activity-card" style="border-left: 4px solid {{ $announcement->priority_color }};">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    @if($announcement->is_pinned)
                                        <i class="fas fa-thumbtack text-warning me-2"></i>
                                    @endif
                                    <i class="fas {{ $announcement->priority_icon }} me-2" style="color: {{ $announcement->priority_color }};"></i>
                                    <strong>{{ ucfirst($announcement->priority) }} Priority</strong>
                                </div>
                                <span class="badge bg-info">{{ $announcement->audience_label }}</span>
                            </div>
                            
                            <div class="card-body">
                                <h3 class="text-success fw-bold mb-4">{{ $announcement->title }}</h3>
                                
                                <div class="announcement-content mb-4">
                                    {!! $announcement->content !!}
                                </div>

                                @if($announcement->attachments && count($announcement->attachments) > 0)
                                    <hr class="my-4">
                                    <h6 class="text-success mb-3"><i class="fas fa-paperclip me-2"></i>Attachments</h6>
                                    <div class="list-group">
                                        @foreach($announcement->attachments as $attachment)
                                            @php
                                                $fileType = strtolower($attachment['type']);
                                                $iconClass = match($fileType) {
                                                    'pdf' => 'fa-file-pdf text-danger',
                                                    'doc', 'docx' => 'fa-file-word text-primary',
                                                    'jpg', 'jpeg', 'png' => 'fa-file-image text-info',
                                                    default => 'fa-file-alt text-secondary'
                                                };
                                            @endphp
                                            <a href="{{ asset('storage/' . $attachment['path']) }}" 
                                               class="list-group-item list-group-item-action attachment-item" 
                                               download="{{ $attachment['name'] }}"
                                               target="_blank">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas {{ $iconClass }} fa-2x me-3"></i>
                                                    <div class="flex-grow-1">
                                                        <strong>{{ $attachment['name'] }}</strong>
                                                        <p class="small text-muted mb-0">
                                                            <span class="badge bg-secondary">{{ strtoupper($fileType) }}</span>
                                                            {{ number_format($attachment['size'] / 1024, 2) }} KB
                                                        </p>
                                                    </div>
                                                    <i class="fas fa-download text-muted"></i>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Info -->
                    <div class="col-lg-4">
                        <!-- Announcement Info -->
                        <div class="activity-card mb-4">
                            <div class="card-header bg-success text-white">
                                <i class="fas fa-info-circle me-2"></i>Announcement Info
                            </div>
                            <div class="card-body">
                                <div class="event-info-item mb-3">
                                    <small class="text-muted d-block">Created By</small>
                                    <strong>{{ $announcement->createdBy->first_name ?? 'Admin' }} {{ $announcement->createdBy->last_name ?? '' }}</strong>
                                </div>
                                
                                <div class="event-info-item mb-3">
                                    <small class="text-muted d-block">Created On</small>
                                    <strong>{{ $announcement->created_at->format('M d, Y g:i A') }}</strong>
                                </div>
                                
                                @if($announcement->updated_by)
                                    <div class="event-info-item mb-3">
                                        <small class="text-muted d-block">Last Updated By</small>
                                        <strong>{{ $announcement->updatedBy->first_name ?? 'Admin' }} {{ $announcement->updatedBy->last_name ?? '' }}</strong>
                                    </div>
                                    
                                    <div class="event-info-item mb-3">
                                        <small class="text-muted d-block">Last Updated</small>
                                        <strong>{{ $announcement->updated_at->format('M d, Y g:i A') }}</strong>
                                    </div>
                                @endif
                                
                                @if($announcement->expires_at)
                                    <div class="event-info-item">
                                        <small class="text-muted d-block">Expires On</small>
                                        <strong class="{{ $announcement->is_expired ? 'text-danger' : 'text-success' }}">
                                            {{ $announcement->expires_at->format('M d, Y g:i A') }}
                                            @if($announcement->is_expired)
                                                <span class="badge bg-danger ms-1">Expired</span>
                                            @endif
                                        </strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Status Card -->
                        <div class="activity-card">
                            <div class="card-header">
                                <i class="fas fa-chart-bar me-2"></i>Status
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-flag fa-2x me-3" style="color: {{ $announcement->priority_color }};"></i>
                                    <div>
                                        <small class="text-muted d-block">Priority Level</small>
                                        <strong>{{ ucfirst($announcement->priority) }}</strong>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-users fa-2x text-success me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">Target Audience</small>
                                        <strong>{{ $announcement->audience_label }}</strong>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-{{ $announcement->is_pinned ? 'thumbtack' : 'circle' }} fa-2x text-warning me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">Pin Status</small>
                                        <strong>{{ $announcement->is_pinned ? 'Pinned' : 'Not Pinned' }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<style>
.announcement-content {
    font-size: 1.05rem;
    line-height: 1.8;
    color: #333;
}

.attachment-item {
    transition: all 0.3s ease;
}

.attachment-item:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
}
</style>

@endsection

