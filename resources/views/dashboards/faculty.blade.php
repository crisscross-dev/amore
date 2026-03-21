@extends('layouts.app')

@section('title', 'Faculty Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Faculty Dashboard CSS -->
@vite(['resources/css/layouts/dashboard-roles/dashboard-faculty.css'])

@php
$currentUser = Auth::user()->loadMissing(['facultyPosition']);
$positionName = optional($currentUser->facultyPosition)->name;
$department = $currentUser->department ?? 'No department assigned';
@endphp

<div class="dashboard-container">
    <div class="container-fluid px-4">
        <div class="row">
            <!-- Left Profile Sidebar -->

            <!-- Main Dashboard Content -->
            <main class="col-12">
                <!-- Mobile Profile (Hidden on Desktop) -->
                <div class="d-md-none mobile-profile mb-4">
                    <div class="text-center">
                        <img src="{{ asset('uploads/profile_picture/' . $currentUser->profile_picture) }}"
                            alt="Profile Picture"
                            class="rounded-circle mb-3 border border-3 border-white"
                            width="80"
                            height="80">

                        <h5 class="text-white mb-1">{{ $currentUser->first_name ?? 'Faculty' }} {{ $currentUser->last_name ?? 'Name' }}</h5>
                        <p class="text-white-50 small mb-1">
                            {{ $currentUser->custom_id ?? 'EMP-0001' }}
                        </p>
                        <p class="text-white-50 small mb-3">
                            <i class="fas fa-user-tie me-1"></i>{{ $positionName ?? 'No position assigned' }}
                            <span class="ms-2">
                                <i class="fas fa-building me-1"></i>{{ $department }}
                            </span>
                        </p>

                        <!-- Mobile Navigation -->
                        <div class="row g-2">
                            <div class="col-4">
                                <a href="" class="btn mobile-nav-btn w-100">
                                    <i class="fas fa-chalkboard d-block mb-1"></i>
                                    <small>Classes</small>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="" class="btn mobile-nav-btn w-100">
                                    <i class="fas fa-users d-block mb-1"></i>
                                    <small>Students</small>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('profile.edit') }}" class="btn mobile-nav-btn w-100">
                                    <i class="fas fa-user-edit d-block mb-1"></i>
                                    <small>Profile</small>
                                </a>
                            </div>
                        </div>

                        <hr class="bg-white opacity-25 my-3">

                        <button
                            class="btn logout-btn w-100"
                            onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </button>

                        <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>

                <!-- Welcome Message -->
                <div class="welcome-card">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <h4 class="mb-1">
                                <i class="fas fa-hand-wave me-2"></i>
                                Welcome back, {{ $currentUser->first_name ?? 'User' }} {{ $currentUser->last_name ?? '' }}!
                            </h4>
                            <p class="mb-0 opacity-75 small">
                                <i class="fas fa-user-tie me-1"></i>{{ $positionName ?? 'No position assigned' }}
                                <span class="ms-3"><i class="fas fa-envelope me-1"></i>{{ $currentUser->email }}</span>
                                <span class="ms-3"><i class="fas fa-phone me-1"></i>{{ $currentUser->contact_number ?? 'N/A' }}</span>
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <a href="{{ route('profile.edit') }}" class="btn btn-warning btn-lg">
                                <i class="fas fa-user-edit me-2"></i>Edit Profile
                            </a>
                            <div class="d-none d-xl-block">
                                <i class="fas fa-chalkboard-teacher" style="font-size: 3rem; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->


                <!-- Announcements Preview -->
                <div class="activity-card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fas fa-bullhorn me-2"></i>
                            Latest Announcements
                        </span>
                        <a href="{{ route('announcements.index') }}" class="btn btn-sm btn-outline-light">
                            View All <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        @if($announcements->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($announcements->take(3) as $announcement)
                            <a href="{{ route('announcements.show', $announcement->id) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                                <i class="fas fa-bullhorn text-primary"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1 fw-bold text-dark">{{ $announcement->title }}</h6>
                                                <small class="text-muted">
                                                    <i class="far fa-calendar-alt me-1"></i>
                                                    {{ $announcement->created_at->format('M d, Y') }}
                                                    <span class="mx-2">•</span>
                                                    <i class="far fa-user me-1"></i>
                                                    {{ $announcement->createdBy->first_name ?? 'Admin' }} {{ $announcement->createdBy->last_name ?? '' }}
                                                </small>
                                            </div>
                                        </div>
                                        <p class="mb-2 text-muted">
                                            {{ \Str::limit(strip_tags($announcement->content), 120) }}
                                        </p>
                                        @if($announcement->is_pinned)
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-thumbtack me-1"></i>Pinned
                                        </span>
                                        @endif
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-user-tie me-1"></i>{{ $announcement->target_audience }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                        @else
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                            <p class="mb-0">No announcements yet. Check back later!</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Upcoming Events -->
                <div class="activity-card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fas fa-calendar-alt me-2"></i>
                            Upcoming Events
                        </span>
                        <a href="{{ route('calendar.index') }}" class="btn btn-sm btn-outline-light">
                            View Calendar <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        @if($upcomingEvents->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($upcomingEvents as $event)
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="rounded-circle p-2 me-3" style="background-color: {{ $event->color }}20; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-calendar-day" style="color: {{ $event->color }}; font-size: 1.2rem;"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1 fw-bold">{{ $event->title }}</h6>
                                                <small class="text-muted">
                                                    <i class="far fa-clock me-1"></i>
                                                    {{ $event->start_date->format('M d, Y') }}
                                                    @if($event->end_date && !$event->start_date->isSameDay($event->end_date))
                                                    - {{ $event->end_date->format('M d, Y') }}
                                                    @endif
                                                    @if(!$event->is_all_day)
                                                    at {{ $event->start_date->format('g:i A') }}
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                        @if($event->description)
                                        <p class="mb-2 text-muted">
                                            {{ \Str::limit(strip_tags($event->description), 100) }}
                                        </p>
                                        @endif
                                        <span class="badge" style="background-color: {{ $event->color }}20; color: {{ $event->color }};">
                                            <i class="fas fa-tag me-1"></i>{{ ucfirst($event->event_type ?? 'Event') }}
                                        </span>
                                        @if($event->is_all_day)
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-sun me-1"></i>All Day
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-calendar-times fa-3x mb-3 opacity-50"></i>
                            <p class="mb-0">No upcoming events scheduled.</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="activity-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fas fa-history me-2"></i>
                            Recent Activity
                        </span>
                        <span class="badge bg-white text-success">3 activities</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table activity-table mb-0">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-tasks me-2"></i>Class Activity</th>
                                        <th><i class="fas fa-calendar me-2"></i>Submission Date</th>
                                        <th class="text-center"><i class="fas fa-flag me-2"></i>Review Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentActivities as $activity)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-{{ $activity['iconColor'] }} bg-opacity-10 rounded-circle p-2 me-3">
                                                    <i class="fas fa-{{ $activity['icon'] }} text-{{ $activity['iconColor'] }}"></i>
                                                </div>
                                                <div>
                                                    <strong>{{ $activity['title'] }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $activity['description'] }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-muted">
                                            <i class="far fa-calendar-alt me-2"></i>
                                            {{ $activity['date']->format('M d, Y') }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $activity['statusColor'] }}">
                                                <i class="fas fa-{{ $activity['icon'] }} me-1"></i>{{ $activity['status'] }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p>No recent activities found</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
@if(session('popupToast'))
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="popupToast" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                {{ session('popupToast') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var toastEl = document.getElementById('popupToast');
        if (toastEl) {
            var toast = new bootstrap.Toast(toastEl, {
                delay: 3000
            });
            toast.show();
        }
    });
</script>

@endsection
