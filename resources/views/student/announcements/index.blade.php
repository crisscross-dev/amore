@extends('layouts.app-student')

@section('title', 'Announcements - Student Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Student Dashboard CSS -->
@vite(['resources/css/layouts/dashboard-roles/dashboard-student.css', 'resources/js/announcements.js'])

<div class="dashboard-container announcement-live-page"
    data-live-url="{{ route('announcements.live-signature') }}"
    data-live-signature="{{ $announcementLiveSignature ?? '' }}">
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
                        
                        <h5 class="text-white mb-1">{{ Auth::user()->first_name ?? 'Student' }} {{ Auth::user()->last_name ?? 'Name' }}</h5>
                        <p class="text-white-50 small mb-3">
                            Student | {{ Auth::user()->custom_id ?? 'STU-0001' }}
                        </p>
                        
                        <!-- Mobile Navigation -->
                        <div class="row g-2">
                            <div class="col-4">
                                <a href="{{ route('dashboard.student') }}" class="btn mobile-nav-btn w-100">
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
                                Announcement
                            </h4>
                            <p class="mb-0 opacity-90">
                                View announcements and updates for Amore Academy
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Success/Error Messages -->


                <!-- Search and Filter -->
                <div class="activity-card mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('announcements.index') }}" class="row g-3">
                            <div class="col-md-5">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="search" 
                                       name="search" 
                                       value="{{ request('search') }}"
                                       placeholder="Search by title or content...">
                            </div>
                            <div class="col-md-3">
                                <label for="priority" class="form-label">Priority</label>
                                <select class="form-select" id="priority" name="priority">
                                    <option value="">All Priorities</option>
                                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label d-none d-md-block">&nbsp;</label>
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label d-none d-md-block">&nbsp;</label>
                                <a href="{{ route('announcements.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Announcements List -->
                @if($announcements->count() > 0)
                    <div class="row">
                        @foreach($announcements as $announcement)
                            <div class="col-12 mb-4">
                                  <div class="activity-card announcement-card {{ $announcement->is_pinned ? 'pinned-announcement' : '' }}"
                                      data-border-left-color="{{ $announcement->priority_color }}">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            @if($announcement->is_pinned)
                                                <i class="fas fa-thumbtack text-warning me-2" title="Pinned"></i>
                                            @endif
                                            <i class="fas {{ $announcement->priority_icon }} me-2" data-text-color="{{ $announcement->priority_color }}"></i>
                                            <strong>{{ ucfirst($announcement->priority) }} Priority</strong>
                                        </div>
                                        <div class="d-flex gap-2 align-items-center">
                                            <span class="badge bg-info">{{ $announcement->audience_label }}</span>
                                            @if($announcement->is_expired)
                                                <span class="badge bg-secondary">Expired</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="card-body">
                                        <h5 class="text-success fw-bold mb-3">{{ $announcement->title }}</h5>
                                        
                                        <p class="mb-3 announcement-description">
                                            {{ Str::limit(strip_tags($announcement->content), 200) }}
                                        </p>
                                        
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                            <div class="d-flex align-items-center text-muted small">
                                                <i class="far fa-calendar me-2"></i>
                                                {{ $announcement->created_at->format('M d, Y') }} at {{ $announcement->created_at->format('g:i A') }}
                                                <span class="mx-2">|</span>
                                                <i class="far fa-user me-2"></i>
                                                {{ $announcement->createdBy->first_name ?? 'Admin' }} {{ $announcement->createdBy->last_name ?? '' }}
                                            </div>
                                            
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('announcements.show', $announcement) }}" class="btn btn-sm btn-success">
                                                    <i class="fas fa-eye me-1"></i>View Details
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $announcements->links() }}
                    </div>
                @else
                    <!-- No Announcements -->
                    <div class="activity-card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-bullhorn text-success" style="font-size: 4rem; opacity: 0.3;"></i>
                            <h5 class="text-success mt-4 mb-3">No Announcements Found</h5>
                            <p class="text-muted mb-4">
                                {{ request()->hasAny(['search', 'priority']) 
                                    ? 'Try adjusting your filters.' 
                                    : 'There are no announcements at this time.' }}
                            </p>
                            
                            @if(request()->hasAny(['search', 'priority']))
                                <a href="{{ route('announcements.index') }}" class="btn btn-outline-success">
                                    <i class="fas fa-times me-2"></i>Clear Filters
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </main>
        </div>
    </div>
</div>

<style>
.pinned-announcement {
    background-color: #fffbeb;
}

.announcement-card {
    transition: all 0.3s ease;
}

.announcement-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.announcement-description {
    color: #374151;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-border-left-color]').forEach(function (element) {
        var color = element.getAttribute('data-border-left-color');
        if (color) {
            element.style.borderLeft = '4px solid ' + color;
        }
    });

    document.querySelectorAll('[data-text-color]').forEach(function (element) {
        var color = element.getAttribute('data-text-color');
        if (color) {
            element.style.color = color;
        }
    });
});
</script>

@endsection

