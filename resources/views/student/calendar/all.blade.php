@extends('layouts.app-student')

@section('title', 'All Events - School Calendar - Amore Academy')

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
                <div class="d-md-none mobile-profile mb-4 d-print-none">
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
                    </div>
                </div>

                <!-- Page Header -->
                <div class="welcome-card mb-4 d-print-none">
                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                        <div>
                            <h4 class="mb-2">
                                <i class="fas fa-calendar-alt me-2"></i>
                                School Calendar - All Events
                            </h4>
                            <p class="mb-0 opacity-90">
                                Complete list of academic events and activities
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('calendar.index') }}" class="btn btn-warning">
                                <i class="fas fa-arrow-left me-2"></i>Back to Calendar
                            </a>
                            <button onclick="window.print()" class="btn btn-light">
                                <i class="fas fa-print me-2"></i>Print
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filter Card -->
                <div class="activity-card mb-4 d-print-none">
                    <div class="card-header">
                        <i class="fas fa-filter me-2"></i>Filter Events
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('calendar.all') }}" class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="year" class="form-label fw-bold">Year</label>
                                <select name="year" id="year" class="form-select">
                                    @foreach($availableYears as $year)
                                        <option value="{{ $year }}" {{ $filterYear == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="month" class="form-label fw-bold">Month</label>
                                <select name="month" id="month" class="form-select">
                                    <option value="">All Months</option>
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $filterMonth == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create(null, $m)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-search me-2"></i>Apply Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Print Header (Only visible when printing) -->
                <div class="print-header d-none d-print-block mb-4">
                    <div class="text-center">
                        <img src="{{ asset('images/amore-logo.png') }}" alt="Amore Academy Logo" style="height: 60px;" class="mb-2">
                        <h2 class="mb-1">Amore Academy</h2>
                        <h4 class="text-success mb-1">School Calendar {{ $filterYear }}</h4>
                        @if($filterMonth)
                            <p class="mb-0">{{ \Carbon\Carbon::create(null, $filterMonth)->format('F') }} {{ $filterYear }}</p>
                        @else
                            <p class="mb-0">Academic Year {{ $filterYear }}</p>
                        @endif
                        <hr class="my-3">
                    </div>
                </div>

                <!-- Events by Month -->
                @if($events->isEmpty())
                    <div class="activity-card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-calendar-times text-success" style="font-size: 4rem; opacity: 0.3;"></i>
                            <h5 class="text-success mt-4 mb-3">No Events Found</h5>
                            <p class="text-muted mb-4">
                                There are no events for the selected period.
                            </p>
                        </div>
                    </div>
                @else
                    @foreach($eventsByMonth as $monthYear => $monthEvents)
                        <div class="activity-card mb-4">
                            <div class="card-header bg-success text-white">
                                <i class="fas fa-calendar me-2"></i>
                                <strong>{{ $monthYear }}</strong>
                                <span class="badge bg-white text-success ms-2">{{ $monthEvents->count() }} event(s)</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 events-table">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 120px;">Date</th>
                                                <th>Event Title</th>
                                                <th style="width: 120px;">Time</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($monthEvents as $event)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="date-box text-center me-2" style="width: 45px;">
                                                                <div class="fw-bold text-success" style="font-size: 1.2rem; line-height: 1;">
                                                                    {{ \Carbon\Carbon::parse($event->start_date)->format('d') }}
                                                                </div>
                                                                <small class="text-muted">
                                                                    {{ \Carbon\Carbon::parse($event->start_date)->format('D') }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-2" style="width: 4px; height: 30px; border-radius: 2px;" data-bg-color="{{ $event->color }}"></div>
                                                            <strong class="text-success">{{ $event->title }}</strong>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($event->is_all_day)
                                                            <span class="badge bg-success">All Day</span>
                                                        @else
                                                            <small class="text-muted">
                                                                {{ \Carbon\Carbon::parse($event->start_date)->format('g:i A') }}
                                                                @if($event->end_date)
                                                                    <br>- {{ \Carbon\Carbon::parse($event->end_date)->format('g:i A') }}
                                                                @endif
                                                            </small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($event->description)
                                                            <small class="text-muted">{{ Str::limit($event->description, 60) }}</small>
                                                        @else
                                                            <small class="text-muted fst-italic">No description</small>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Summary Card -->
                    <div class="activity-card d-print-none">
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <h3 class="text-success mb-0">{{ $events->count() }}</h3>
                                    <small class="text-muted">Total Events</small>
                                </div>
                                <div class="col-md-4">
                                    <h3 class="text-success mb-0">{{ $eventsByMonth->count() }}</h3>
                                    <small class="text-muted">Months with Events</small>
                                </div>
                                <div class="col-md-4">
                                    <h3 class="text-success mb-0">{{ $events->where('is_all_day', true)->count() }}</h3>
                                    <small class="text-muted">All-Day Events</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Print Footer -->
                <div class="print-footer d-none d-print-block mt-4">
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Generated on: {{ now()->format('F d, Y h:i A') }}</small>
                        </div>
                        <div class="col-6 text-end">
                            <small class="text-muted">Amore Academy - School Calendar</small>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<!-- Print Styles -->
<style>
@media print {
    /* Hide non-printable elements */
    .d-print-none,
    .sidebar,
    .mobile-profile,
    .welcome-card,
    nav,
    footer,
    .btn {
        display: none !important;
    }
    
    /* Show print elements */
    .d-print-block {
        display: block !important;
    }
    
    /* Full width content */
    .col-lg-9,
    .col-md-8 {
        width: 100% !important;
        max-width: 100% !important;
        flex: 0 0 100% !important;
        padding: 0 !important;
    }
    
    .dashboard-container {
        background: white !important;
        padding: 0 !important;
    }
    
    .container-fluid {
        padding: 0 !important;
    }
    
    /* Table styling for print */
    .activity-card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
        margin-bottom: 20px !important;
        page-break-inside: avoid;
    }
    
    .card-header {
        background-color: #198754 !important;
        color: white !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    
    .table {
        font-size: 11px !important;
    }
    
    .table th,
    .table td {
        padding: 8px !important;
        border: 1px solid #ddd !important;
    }
    
    .badge {
        border: 1px solid #198754 !important;
    }
    
    /* Color indicators */
    .date-box .fw-bold {
        color: #198754 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    
    /* Page breaks */
    .activity-card {
        page-break-inside: avoid;
    }
    
    /* Print header/footer */
    .print-header,
    .print-footer {
        display: block !important;
    }
}

/* Screen styles for date box */
.date-box {
    min-width: 45px;
    padding: 5px;
    background: #f8f9fa;
    border-radius: 5px;
}

.events-table tbody tr:hover {
    background-color: rgba(25, 135, 84, 0.05);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-bg-color]').forEach(function (element) {
        var color = element.getAttribute('data-bg-color');
        if (color) {
            element.style.backgroundColor = color;
        }
    });
});
</script>

@endsection

