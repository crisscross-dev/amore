@extends('layouts.app')

@section('title', 'Announcements - Admin Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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
                                <i class="fas fa-bullhorn me-2"></i>
                                Announcements
                            </h4>
                            <p class="mb-0 opacity-90">
                                Manage and post announcements for Amore Academy
                            </p>
                        </div>
                        <div class="d-none d-lg-block">
                            <a href="{{ route('announcements.create') }}" class="btn btn-light btn-lg">
                                <i class="fas fa-plus me-2"></i>New Announcement
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Mobile Create Button -->
                <div class="d-lg-none mb-3">
                    <a href="{{ route('announcements.create') }}" class="btn btn-success w-100">
                        <i class="fas fa-plus me-2"></i>New Announcement
                    </a>
                </div>

                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Search and Filter -->
                <div class="activity-card mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('announcements.index') }}" class="row g-3">
                            <div class="col-md-4">
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
                            <div class="col-md-3">
                                <label for="audience" class="form-label">Audience</label>
                                <select class="form-select" id="audience" name="audience">
                                    <option value="">All Audiences</option>
                                    <option value="all" {{ request('audience') == 'all' ? 'selected' : '' }}>All Users</option>
                                    <option value="faculty" {{ request('audience') == 'faculty' ? 'selected' : '' }}>Faculty Only</option>
                                    <option value="students" {{ request('audience') == 'students' ? 'selected' : '' }}>Students Only</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label d-none d-md-block">&nbsp;</label>
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
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
                                     style="border-left: 4px solid {{ $announcement->priority_color }};">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            @if($announcement->is_pinned)
                                                <i class="fas fa-thumbtack text-warning me-2" title="Pinned"></i>
                                            @endif
                                            <i class="fas {{ $announcement->priority_icon }} me-2" style="color: {{ $announcement->priority_color }};"></i>
                                            <strong>{{ ucfirst($announcement->priority) }} Priority</strong>
                                        </div>
                                        <div class="d-flex gap-2 align-items-center">
                                            <span class="badge bg-info">{{ $announcement->audience_label }}</span>
                                            @if($announcement->target_audience)
                                                @php
                                                    $visibilityBadge = match($announcement->target_audience) {
                                                        'public' => ['icon' => 'fa-globe', 'color' => 'primary', 'label' => 'Public'],
                                                        'all' => ['icon' => 'fa-users', 'color' => 'success', 'label' => 'All Users'],
                                                        'students' => ['icon' => 'fa-graduation-cap', 'color' => 'info', 'label' => 'Students'],
                                                        'faculty' => ['icon' => 'fa-chalkboard-teacher', 'color' => 'warning', 'label' => 'Faculty'],
                                                        default => ['icon' => 'fa-users', 'color' => 'secondary', 'label' => 'Unknown']
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $visibilityBadge['color'] }}">
                                                    <i class="fas {{ $visibilityBadge['icon'] }} me-1"></i>
                                                    {{ $visibilityBadge['label'] }}
                                                </span>
                                            @endif
                                            @if($announcement->is_expired)
                                                <span class="badge bg-secondary">Expired</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="card-body">
                                        <h5 class="text-success fw-bold mb-3">{{ $announcement->title }}</h5>
                                        
                                        <p class="mb-3">
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
                                                <a href="{{ route('announcements.show', $announcement) }}" class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-eye me-1"></i>View
                                                </a>
                                                <a href="{{ route('announcements.edit', $announcement) }}" class="btn btn-sm btn-success">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </a>
                                                <form method="POST" action="{{ route('announcements.pin', $announcement) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-thumbtack me-1"></i>{{ $announcement->is_pinned ? 'Unpin' : 'Pin' }}
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteModal{{ $announcement->id }}">
                                                    <i class="fas fa-trash me-1"></i>Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal{{ $announcement->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg">
                                        <div class="modal-header" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); border: none;">
                                            <h5 class="modal-title text-white fw-bold">
                                                <i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="text-center mb-3">
                                                <i class="fas fa-trash-alt text-danger" style="font-size: 3rem; opacity: 0.5;"></i>
                                            </div>
                                            <p class="text-center mb-3">Are you sure you want to delete this announcement?</p>
                                            <div class="alert alert-warning d-flex align-items-start mb-0">
                                                <i class="fas fa-exclamation-circle me-2 mt-1"></i>
                                                <div>
                                                    <strong>Warning:</strong> The announcement "<strong>{{ $announcement->title }}</strong>" will be permanently deleted.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 bg-light">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                                <i class="fas fa-times me-1"></i>Cancel
                                            </button>
                                            <form method="POST" action="{{ route('announcements.destroy', $announcement) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">
                                                    <i class="fas fa-trash me-1"></i>Delete
                                                </button>
                                            </form>
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
                                {{ request()->hasAny(['search', 'priority', 'audience']) 
                                    ? 'Try adjusting your filters.' 
                                    : 'Get started by creating your first announcement.' }}
                            </p>
                            
                            @if(!request()->hasAny(['search', 'priority', 'audience']))
                                <a href="{{ route('announcements.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus me-2"></i>Create Announcement
                                </a>
                            @else
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

@endsection

