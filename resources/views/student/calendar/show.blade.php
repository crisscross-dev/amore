@extends('layouts.app-student')

@section('title', 'Event Details - Student Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Student Dashboard CSS -->
@vite(['resources/css/layouts/dashboard-roles/dashboard-student.css'])

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
                                <a href="{{ route('calendar.index') }}" class="btn mobile-nav-btn w-100 active">
                                    <i class="fas fa-calendar-alt"></i>
                                    <small>Calendar</small>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('announcements.index') }}" class="btn mobile-nav-btn w-100">
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
                                <i class="fas fa-calendar-day me-2"></i>
                                Events on {{ \Carbon\Carbon::create($year, $month, $day)->format('F d, Y') }}
                            </h4>
                            <p class="mb-0 opacity-90">
                                View events scheduled for this day
                            </p>
                        </div>
                        <div class="d-none d-lg-block">
                            <a href="{{ route('calendar.index', ['year' => $year, 'month' => $month]) }}" class="btn btn-success btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>Back to Calendar
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Mobile Back Button -->
                <div class="d-lg-none mb-3">
                    <a href="{{ route('calendar.index', ['year' => $year, 'month' => $month]) }}" class="btn btn-outline-success w-100">
                        <i class="fas fa-arrow-left me-2"></i>Back to Calendar
                    </a>
                </div>

                @if($events->isEmpty())
                    <!-- No Events Card -->
                    <div class="activity-card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-calendar-times text-success" style="font-size: 4rem; opacity: 0.3;"></i>
                            <h5 class="text-success mt-4 mb-3">No Events Scheduled</h5>
                            <p class="text-muted mb-4">
                                There are no events scheduled for this day.
                            </p>
                            <a href="{{ route('calendar.index', ['year' => $year, 'month' => $month]) }}" class="btn btn-outline-success">
                                <i class="fas fa-arrow-left me-2"></i>Back to Calendar
                            </a>
                        </div>
                    </div>
                @else
                    <!-- Events Table -->
                    <div class="activity-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>
                                <i class="fas fa-list me-2"></i>
                                Events List
                            </span>
                            <span class="badge bg-white text-success">{{ $events->count() }} event(s)</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-success text-white">
                                        <tr>
                                            <th class="ps-3">Title</th>
                                            <th>Time</th>
                                            <th>Description</th>
                                            <th>Created By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($events as $event)
                                            <tr>
                                                <td class="ps-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2" style="width: 4px; height: 30px; background-color: {{ $event->color }}; border-radius: 2px;"></div>
                                                        <strong class="text-success">{{ $event->title }}</strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($event->is_all_day)
                                                        <span class="badge bg-success">All Day</span>
                                                    @else
                                                        <span class="text-muted">
                                                            <i class="far fa-clock me-1"></i>
                                                            {{ \Carbon\Carbon::parse($event->start_date)->format('g:i A') }}
                                                            @if($event->end_date)
                                                                - {{ \Carbon\Carbon::parse($event->end_date)->format('g:i A') }}
                                                            @endif
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($event->description)
                                                        <span class="text-muted">{{ Str::limit($event->description, 50) }}</span>
                                                    @else
                                                        <span class="text-muted fst-italic">No description</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $event->creator->first_name ?? 'Admin' }} {{ $event->creator->last_name ?? '' }}
                                                    </small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </main>
        </div>
    </div>
</div>

<style>
.event-detail-card {
    transition: all 0.3s ease;
}

.event-detail-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.icon-box {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
}

.event-info-section {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
}
</style>

@endsection

