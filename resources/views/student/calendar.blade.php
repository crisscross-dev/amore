@extends('layouts.app-student')

@section('title', 'Calendar - Student Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Student Dashboard CSS -->
@vite(['resources/css/layouts/dashboard-roles/dashboard-student.css', 'resources/js/admin-calendar.js'])

<div id="student-calendar-page" class="dashboard-container" data-calendar-month="{{ $currentMonth }}" data-calendar-year="{{ $currentYear }}">
    <div class="container-fluid px-4">
        <div class="row">
            <!-- Left Profile Sidebar -->
            <!-- Main Calendar Content -->
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
                                <a href="#" class="btn mobile-nav-btn w-100">
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

                <!-- Page Header -->
                <div class="welcome-card mb-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-2">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Academic Calendar
                            </h4>
                            <p class="mb-0 opacity-90">
                                View important dates, events, and schedules for Amore Academy
                            </p>
                        </div>
                        <div class="d-none d-lg-block">
                            <a href="{{ route('dashboard.student') }}" class="btn btn-warning btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Calendar Card -->
                <div class="activity-card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fas fa-calendar me-2"></i>
                            Calendar View - {{ \Carbon\Carbon::create($currentYear, $currentMonth)->format('F Y') }}
                        </span>
                        <span class="badge bg-white text-success">{{ $currentYear }}</span>
                    </div>
                    <div class="card-body">
                        <!-- Calendar Navigation -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <button id="calendar-prev-btn" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-chevron-left me-1"></i>Previous
                            </button>
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="mb-0 text-success fw-bold">{{ \Carbon\Carbon::create($currentYear, $currentMonth)->format('F Y') }}</h5>
                                <button id="calendar-today-btn" class="btn btn-sm btn-success">
                                    <i class="fas fa-calendar-day me-1"></i>Today
                                </button>
                                <button id="calendar-remove-highlight-btn" class="btn btn-sm btn-warning d-none">
                                    <i class="fas fa-times-circle me-1"></i>Remove Highlight
                                </button>
                            </div>
                            <button id="calendar-next-btn" class="btn btn-outline-success btn-sm">
                                Next<i class="fas fa-chevron-right ms-1"></i>
                            </button>
                        </div>

                        <!-- Loading Spinner -->
                        <div id="calendar-loader" class="text-center py-5 d-none">
                            <div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading calendar...</p>
                        </div>

                        <!-- Calendar Grid -->
                        <div class="table-responsive">
                            <table class="table table-bordered text-center calendar-table">
                                <thead>
                                    <tr class="bg-success text-white">
                                        <th>Sun</th>
                                        <th>Mon</th>
                                        <th>Tue</th>
                                        <th>Wed</th>
                                        <th>Thu</th>
                                        <th>Fri</th>
                                        <th>Sat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $calendarDate = \Carbon\Carbon::create($currentYear, $currentMonth, 1);
                                        $firstDayOfMonth = $calendarDate->copy()->startOfMonth();
                                        $lastDayOfMonth = $calendarDate->copy()->endOfMonth();
                                        $startDayOfWeek = $firstDayOfMonth->dayOfWeek;
                                        $daysInMonth = $lastDayOfMonth->day;
                                        $today = now()->day;
                                        $isCurrentMonth = ($currentMonth == now()->month && $currentYear == now()->year);

                                        // Get event days from loaded events
                                        $eventDays = $events->pluck('start_date')->map(function($date) {
                                            return \Carbon\Carbon::parse($date)->day;
                                        })->unique()->toArray();

                                        // Group events by day
                                        $eventsByDay = $events->groupBy(function($event) {
                                            return \Carbon\Carbon::parse($event->start_date)->day;
                                        });
                                    @endphp

                                    @for ($week = 0; $week < 6; $week++)
                                        <tr>
                                            @for ($day = 0; $day < 7; $day++)
                                                @php
                                                    $dayNumber = ($week * 7 + $day) - $startDayOfWeek + 1;
                                                    $hasEvents = in_array($dayNumber, $eventDays);
                                                    $isToday = $isCurrentMonth && $dayNumber == $today;
                                                @endphp

                                                @if ($dayNumber > 0 && $dayNumber <= $daysInMonth)
                                                    <td class="calendar-day {{ $isToday ? 'today' : '' }} {{ $hasEvents ? 'has-event' : '' }}" data-day="{{ $dayNumber }}">
                                                        <div class="day-number">{{ $dayNumber }}</div>
                                                        @if ($hasEvents && isset($eventsByDay[$dayNumber]))
                                                            <div class="event-indicator">
                                                                @foreach($eventsByDay[$dayNumber]->take(1) as $event)
                                                                    <i class="fas fa-circle" style="font-size: 6px; color: {{ $event->color }}"></i>
                                                                @endforeach
                                                                @if($eventsByDay[$dayNumber]->count() > 1)
                                                                    <small class="badge bg-success rounded-pill" style="font-size: 0.6rem;">+{{ $eventsByDay[$dayNumber]->count() - 1 }}</small>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </td>
                                                @else
                                                    <td class="calendar-day empty"></td>
                                                @endif
                                            @endfor
                                        </tr>

                                        @if ($week * 7 + 7 - $startDayOfWeek > $daysInMonth)
                                            @break
                                        @endif
                                    @endfor
                                </tbody>
                            </table>
                        </div>

                        <!-- Calendar Legend -->
                        <div class="d-flex justify-content-center gap-4 mt-3 flex-wrap">
                            <div class="d-flex align-items-center">
                                <div class="legend-box bg-success me-2"></div>
                                <small class="text-muted">Today</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-circle text-success me-2" style="font-size: 8px;"></i>
                                <small class="text-muted">Has Event</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Events & Announcements -->
                <div class="activity-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fas fa-bell me-2"></i>
                            Upcoming Events & Announcements
                        </span>
                        <span class="badge bg-white text-success">{{ $upcomingEvents->count() + $announcements->count() }} items</span>
                    </div>
                    <div class="card-body">
                        @if($upcomingEvents->count() > 0 || $announcements->count() > 0)
                            <!-- Event & Announcement List -->
                            <div class="list-group list-group-flush">
                                <!-- Announcements Section -->
                                @if($announcements->count() > 0)
                                    <div class="list-group-item border-0 px-0 bg-light">
                                        <small class="text-success fw-bold">
                                            <i class="fas fa-bullhorn me-1"></i>RECENT ANNOUNCEMENTS
                                        </small>
                                    </div>
                                    @foreach($announcements as $announcement)
                                        <a href="{{ route('announcements.show', $announcement) }}"
                                           class="list-group-item list-group-item-action border-0 border-bottom px-0 announcement-item"
                                           style="border-left: 4px solid {{ $announcement->priority_color }} !important;">
                                            <div class="d-flex align-items-start">
                                                <div class="rounded p-3 me-3" style="background-color: {{ $announcement->priority_color }}15;">
                                                    <i class="fas {{ $announcement->priority_icon }}" style="font-size: 1.5rem; color: {{ $announcement->priority_color }}"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h6 class="mb-0 fw-bold">{{ $announcement->title }}</h6>
                                                        <div class="d-flex gap-1">
                                                            @if($announcement->is_pinned)
                                                                <span class="badge bg-warning"><i class="fas fa-thumbtack"></i></span>
                                                            @endif
                                                            <span class="badge" style="background-color: {{ $announcement->priority_color }}">
                                                                {{ ucfirst($announcement->priority) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <p class="mb-2 text-muted small">
                                                        <i class="fas fa-user me-1"></i>{{ $announcement->audience_label }}
                                                        <i class="far fa-clock ms-3 me-1"></i>{{ $announcement->created_at->diffForHumans() }}
                                                    </p>
                                                    <p class="mb-0 text-muted small">
                                                        {{ Str::limit(strip_tags($announcement->content), 100) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                @endif

                                <!-- Events Section -->
                                @if($upcomingEvents->count() > 0)
                                    <div class="list-group-item border-0 px-0 bg-light {{ $announcements->count() > 0 ? 'mt-3' : '' }}">
                                        <small class="text-success fw-bold">
                                            <i class="fas fa-calendar-check me-1"></i>UPCOMING EVENTS
                                        </small>
                                    </div>
                                    @foreach($upcomingEvents as $event)
                                        <div class="list-group-item border-0 border-bottom px-0 upcoming-event-item"
                                             data-event-day="{{ $event->start_date->day }}"
                                             data-event-month="{{ $event->start_date->month }}"
                                             data-event-year="{{ $event->start_date->year }}"
                                             role="button"
                                             tabindex="0"
                                             aria-label="Click to view {{ $event->start_date->format('F Y') }} calendar">
                                            <div class="d-flex align-items-start">
                                                <div class="rounded p-3 me-3" style="background-color: {{ $event->color }}15;">
                                                    <i class="fas {{ $event->type_icon }}" style="font-size: 1.5rem; color: {{ $event->color }}"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h6 class="mb-0 fw-bold">{{ $event->title }}</h6>
                                                        <span class="badge" style="background-color: {{ $event->color }}">
                                                            {{ $event->start_date->diffForHumans() }}
                                                        </span>
                                                    </div>
                                                    <p class="mb-2 text-muted small">
                                                        <i class="far fa-calendar me-1"></i>{{ $event->start_date->format('F d, Y') }}
                                                        @if(!$event->is_all_day)
                                                            <i class="far fa-clock ms-3 me-1"></i>{{ $event->start_date->format('g:i A') }}
                                                            @if($event->end_date)
                                                                - {{ $event->end_date->format('g:i A') }}
                                                            @endif
                                                        @else
                                                            <i class="far fa-clock ms-3 me-1"></i>All Day
                                                        @endif
                                                    </p>
                                                    @if($event->description)
                                                        <p class="mb-0 text-muted small">
                                                            {{ Str::limit($event->description, 100) }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-calendar-times text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                                <p class="text-muted mt-3">No upcoming events or announcements</p>
                            </div>
                        @endif

                        <!-- View All Buttons -->
                        <div class="d-flex gap-2 mt-4">
                            <a href="{{ route('announcements.index') }}" class="btn btn-outline-success flex-fill">
                                <i class="fas fa-bullhorn me-2"></i>All Announcements
                            </a>
                            <a href="{{ route('calendar.all') }}" class="btn btn-outline-success flex-fill">
                                <i class="fas fa-calendar-alt me-2"></i>All Events
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

@endsection

