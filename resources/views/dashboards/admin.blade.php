@extends('layouts.app')

@section('title', 'Admin Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Admin Dashboard CSS -->
@vite(['resources/css/layouts/dashboard-roles/dashboard-admin.css'])

<style>
    .dashboard-container {
        padding: 1.45rem 0 2rem;
    }

    .admin-shell {
        background: #eef3ef;
        border-radius: 1.25rem;
        padding: 1.3rem;
    }

    .kpi-grid .col-xl-3,
    .kpi-grid .col-md-6 {
        display: flex;
    }

    .kpi-card {
        background: #ffffff;
        border-radius: 1.2rem;
        padding: 1.25rem;
        width: 100%;
        min-height: 182px;
        border: 1px solid rgba(18, 42, 72, 0.06);
        box-shadow: 0 10px 26px rgba(16, 37, 63, 0.08);
        display: flex;
        flex-direction: column;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 30px rgba(16, 37, 63, 0.12);
    }

    .kpi-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 0.85rem;
    }

    .kpi-icon {
        width: 56px;
        height: 56px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
    }

    .kpi-icon.admissions {
        background: #f8ede0;
        color: #e38a2c;
    }

    .kpi-icon.students {
        background: #e3f1e7;
        color: #2d5a34;
    }

    .kpi-icon.faculty {
        background: #e8edf9;
        color: #1f3b68;
    }

    .kpi-icon.grades {
        background: #f8dfe2;
        color: #de3f3f;
    }

    .kpi-chip {
        border-radius: 999px;
        padding: 0.42rem 0.92rem;
        font-size: 0.72rem;
        font-weight: 700;
        line-height: 1;
        min-width: 66px;
        text-align: center;
    }

    .kpi-chip.good {
        background: #dff3e8;
        color: #1f9d5c;
    }

    .kpi-chip.warn {
        background: #fff5eb;
        color: #c67822;
    }

    .kpi-chip.alert {
        background: #f9dfe2;
        color: #d93939;
    }

    .kpi-value {
        margin: 0;
        font-size: clamp(2.3rem, 3.1vw, 3rem);
        line-height: 1;
        font-weight: 800;
        color: var(--color-primary, #18411f);
    }

    .kpi-label {
        margin: 0.7rem 0 0;
        color: #7b8596;
        font-size: 0.84rem;
        font-weight: 500;
    }

    .content-panel {
        background: #ffffff;
        border-radius: 1.2rem;
        padding: 1.25rem;
        border: 1px solid rgba(24, 65, 31, 0.08);
        box-shadow: 0 8px 22px rgba(24, 65, 31, 0.09);
        height: auto;
    }

    .dashboard-panels > [class*="col-"] {
        display: flex;
    }

    .dashboard-panels .content-panel {
        width: 100%;
        height: 100%;
    }

    .panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 1.15rem;
    }

    .panel-title {
        margin: 0;
        font-family: Georgia, "Times New Roman", serif;
        font-size: clamp(1.8rem, 2.65vw, 2.7rem);
        color: var(--color-primary, #18411f);
        font-weight: 700;
    }

    .panel-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.55rem 0.95rem;
        border-radius: 0.75rem;
        text-decoration: none;
        background: #fff5eb;
        color: #e8913a;
        font-weight: 700;
        transition: background 0.2s ease, color 0.2s ease;
        font-size: 0.8rem;
    }

    .panel-link:hover {
        background: #fce9d5;
        color: #c87821;
    }

    .dashboard-section.row {
        --bs-gutter-x: 0;
        --bs-gutter-y: 0;
        margin-left: 0;
        margin-right: 0;
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(300px, 1fr);
        gap: 1rem;
        align-items: stretch;
    }

    .dashboard-section > [class*="col-"] {
        display: flex;
        min-width: 0;
        width: 100%;
        padding-left: 0;
        padding-right: 0;
    }

    .dashboard-main-column,
    .dashboard-side-column {
        min-width: 0;
    }

    .activity-card {
        width: 100%;
        display: flex;
        flex-direction: column;
        min-width: 0;
        border-radius: 1.2rem;
        border: 1px solid rgba(24, 65, 31, 0.08);
        background: #ffffff;
        box-shadow: 0 8px 22px rgba(24, 65, 31, 0.09);
        overflow: hidden;
    }

    .activity-card .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.65rem 1rem;
        min-height: 58px;
        background: linear-gradient(135deg, #1f9a5b, #30c39b);
        color: #ffffff;
        border-bottom: none;
    }

    .activity-card .card-header span {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 1rem;
        font-weight: 700;
        line-height: 1;
    }

    .activity-card .card-header .section-title {
        display: inline-flex;
        align-items: center;
        font-size: 1.08rem;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        font-weight: 700;
        letter-spacing: 0.01em;
        line-height: 1.15;
        text-decoration: none;
        padding: 0;
        border-radius: 0;
        background: transparent;
        color: #ffffff;
    }

    .activity-card .card-header .section-title i {
        font-size: 1.02rem;
        line-height: 1;
        color: #ffffff;
    }

    .dashboard-notifications-card .card-header .notification-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        background: #fee2e2;
        color: #b91c1c;
        font-size: 0.9rem;
        font-weight: 700;
        line-height: 1;
        padding: 0.24rem 0.62rem;
    }

    .dashboard-notifications-card .card-body,
    .announcement-side-card .card-body {
        flex: 1 1 auto;
        min-height: 0;
    }

    .recent-logs-table-wrapper {
        overflow-x: auto;
        max-width: 100%;
    }

    .announcement-side-card {
        display: flex;
        flex-direction: column;
    }

    .announcement-side-card .announcement-scroll-area {
        height: 100%;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .announcement-side-card .card-footer {
        margin-top: auto;
    }

    .enrollment-table {
        margin-bottom: 0;
    }

    .enrollment-table thead th {
        border-bottom: 1px solid #ebeff5;
        color: #8994a6;
        font-weight: 600;
        padding: 0.85rem 0.62rem;
        font-size: 0.78rem;
    }

    .enrollment-table tbody td {
        border-top: 1px solid #ebeff5;
        padding: 0.8rem 0.62rem;
        vertical-align: middle;
    }

    .log-cell {
        display: flex;
        flex-direction: column;
        gap: 0.22rem;
        min-width: 260px;
    }

    .log-title {
        color: #1b2a3a;
        font-size: 0.96rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .log-detail {
        color: #8691a2;
        font-size: 0.78rem;
        line-height: 1.2;
    }

    .log-actor,
    .log-time {
        color: #1b2a3a;
        font-size: 0.84rem;
        font-weight: 600;
    }

    .log-time {
        color: #8691a2;
        font-weight: 500;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        padding: 0.35rem 0.7rem;
        font-size: 0.76rem;
        font-weight: 600;
    }

    .status-pill.active {
        background: #ddf3e5;
        color: #1f9a5b;
    }

    .status-pill.pending {
        background: #fff0d5;
        color: #d48819;
    }

    .events-list {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
    }

    .events-group-title {
        color: #7f8a9c;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        margin-bottom: 0.35rem;
    }

    .event-item {
        display: flex;
        gap: 0.72rem;
        align-items: flex-start;
    }

    .event-date-pill {
        width: 42px;
        min-width: 42px;
        border-radius: 0.8rem;
        text-align: center;
        padding: 0.37rem 0.2rem;
        font-weight: 700;
        background: #f8ede0;
        color: #d68921;
    }

    .event-date-pill.blue {
        background: #e3f1e7;
        color: #2d5a34;
    }

    .event-date-pill.violet {
        background: #dce7ef;
        color: #1f3b68;
    }

    .event-date-pill span {
        display: block;
        line-height: 1;
    }

    .event-date-pill .day {
        font-size: 0.94rem;
    }

    .event-date-pill .month {
        margin-top: 0.12rem;
        font-size: 0.52rem;
        letter-spacing: 0.05em;
    }

    .event-title {
        margin: 0.15rem 0 0;
        color: #1b2a3a;
        font-size: 0.92rem;
        font-weight: 600;
        line-height: 1.1;
    }

    .event-meta {
        margin: 0.18rem 0 0;
        color: #7f8a9c;
        font-size: 0.76rem;
        line-height: 1.1;
    }

    .announcement-item {
        display: flex;
        gap: 0.72rem;
        align-items: flex-start;
        padding: 0.62rem 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .announcement-item:last-child {
        border-bottom: none;
    }

    .announcement-icon {
        width: 42px;
        min-width: 42px;
        height: 42px;
        border-radius: 0.8rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e8edf9;
        color: #1f3b68;
        font-size: 0.88rem;
    }

    .announcement-title {
        margin: 0.1rem 0 0;
        color: #1b2a3a;
        font-size: 0.9rem;
        font-weight: 600;
        line-height: 1.2;
    }

    .announcement-meta {
        margin: 0.15rem 0 0;
        color: #7f8a9c;
        font-size: 0.74rem;
        line-height: 1.2;
    }

    .empty-state {
        color: #8b95a6;
        font-size: 0.8rem;
        padding: 0.5rem 0.2rem;
    }

    .recent-log-pagination .pagination {
        margin-bottom: 0;
        gap: 0.3rem;
    }

    .recent-log-pagination .page-link {
        border: 1px solid #e5ebf2;
        color: #6f7d90;
        border-radius: 0.55rem;
        font-size: 0.76rem;
        min-width: 32px;
        text-align: center;
        padding: 0.35rem 0.5rem;
    }

    .recent-log-pagination .page-item.active .page-link {
        background: #e3f1e7;
        border-color: #d0e3d6;
        color: #1f9a5b;
    }

    .recent-log-pagination .page-item.disabled .page-link {
        color: #b3bdca;
        background: #f7f9fc;
    }

    @media (max-width: 1399px) {
        .panel-title {
            font-size: 1.55rem;
        }

        .log-title,
        .event-title {
            font-size: 0.9rem;
        }

        .log-detail,
        .event-meta {
            font-size: 0.74rem;
        }

        .log-actor,
        .log-time {
            font-size: 0.78rem;
        }
    }

    @media (max-width: 991px) {
        .admin-shell {
            padding: 1.15rem;
        }

        .activity-card .card-header .section-title {
            font-size: 1rem;
        }

        .activity-card .card-header .section-title i {
            font-size: 0.95rem;
        }

        .dashboard-notifications-card .card-header .notification-badge {
            font-size: 0.82rem;
            padding: 0.2rem 0.52rem;
        }

        .dashboard-section.row {
            grid-template-columns: 1fr;
            gap: 0.9rem;
        }

        .kpi-card {
            min-height: 174px;
            padding: 1rem;
        }

        .kpi-value {
            font-size: 2.15rem;
        }

        .panel-title {
            font-size: 1.35rem;
        }

        .log-title,
        .event-title {
            font-size: 0.88rem;
        }

        .log-detail,
        .event-meta {
            font-size: 0.72rem;
        }

        .log-actor,
        .log-time {
            font-size: 0.76rem;
        }
    }

    @media (max-width: 767px) {
        .dashboard-container {
            padding-top: 1.1rem;
        }

        .admin-shell {
            border-radius: 1rem;
            padding: 1rem;
        }

        .dashboard-section.row {
            gap: 0.8rem;
        }

        .kpi-card {
            min-height: 164px;
        }

        .kpi-value {
            font-size: 2rem;
        }

        .panel-head {
            flex-wrap: wrap;
        }

        .content-panel {
            padding: 1.2rem;
        }

        .enrollment-table thead {
            display: none;
        }

        .enrollment-table tbody tr {
            display: block;
            border-bottom: 1px solid #ebeff5;
            padding: 0.9rem 0;
        }

        .enrollment-table tbody td {
            display: flex;
            justify-content: space-between;
            border-top: none;
            padding: 0.28rem 0;
        }

        .enrollment-table tbody td::before {
            content: attr(data-label);
            color: #8d98a8;
            font-size: 0.8rem;
            margin-right: 0.8rem;
        }

        .log-cell {
            min-width: 0;
        }

        .log-title,
        .event-title {
            font-size: 1rem;
        }

        .log-detail,
        .event-meta {
            font-size: 0.85rem;
        }

        .log-actor,
        .log-time {
            font-size: 0.84rem;
        }
    }
</style>

<div class="dashboard-container">
    <div class="container-fluid px-4">
        <div class="row">
            <!-- Main Dashboard Content -->
            <main class="col-12">
                @php
                    $totalAdmissions = (int) ($stats['jhs_approved'] ?? 0) + (int) ($stats['shs_approved'] ?? 0);
                    $gradeApprovalCount = max((int) $totalStudents - (int) $studentsWithoutGrades, 0);
                    $studentTrend = ($studentPercentChange >= 0 ? '+' : '') . $studentPercentChange . '%';

                    $admissionsChipText = $totalPendingAdmissions > 0 ? '+' . $totalPendingAdmissions : 'Done';
                    $admissionsChipClass = $totalPendingAdmissions > 0 ? ($overdueAdmissions > 0 ? 'alert' : 'warn') : 'good';

                    $studentsChipClass = $studentPercentChange >= 0 ? 'good' : 'alert';

                    $facultyChipText = $pendingFaculty > 0 ? '+' . $pendingFaculty : 'Done';
                    $facultyChipClass = $pendingFaculty > 0 ? 'warn' : 'good';

                    $gradeChipText = $studentsWithoutGrades > 0 ? '+' . $studentsWithoutGrades : 'Done';
                    $gradeChipClass = $studentsWithoutGrades > 0 ? 'alert' : 'good';
                @endphp

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
                                <a href="{{ route('profile.edit') }}" class="btn mobile-nav-btn w-100">
                                    <i class="fas fa-user-edit d-block mb-1"></i>
                                    <small>Profile</small>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('calendar.index') }}" class="btn mobile-nav-btn w-100">
                                    <i class="fas fa-calendar-alt d-block mb-1"></i>
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
                                <a href="{{ route('admin.accounts.manage') }}" class="btn mobile-nav-btn w-100">
                                    <i class="fas fa-users-cog d-block mb-1"></i>
                                    <small>Accounts</small>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('admin.reports.index') }}" class="btn mobile-nav-btn w-100">
                                    <i class="fas fa-chart-line d-block mb-1"></i>
                                    <small>Reports</small>
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

                <div class="admin-shell">
                    <div class="row g-4 kpi-grid mb-4">
                        <div class="col-xl-3 col-md-6">
                            <div class="kpi-card">
                                <div class="kpi-head">
                                    <div class="kpi-icon admissions">
                                        <i class="fas fa-inbox"></i>
                                    </div>
                                    <span class="kpi-chip {{ $admissionsChipClass }}">{{ $admissionsChipText }}</span>
                                </div>
                                <h3 class="kpi-value">{{ number_format($totalAdmissions) }}</h3>
                                <p class="kpi-label">Total Admissions</p>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="kpi-card">
                                <div class="kpi-head">
                                    <div class="kpi-icon students">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <span class="kpi-chip {{ $studentsChipClass }}">{{ $studentTrend }}</span>
                                </div>
                                <h3 class="kpi-value">{{ number_format($totalStudents) }}</h3>
                                <p class="kpi-label">Total Students</p>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="kpi-card">
                                <div class="kpi-head">
                                    <div class="kpi-icon faculty">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                    <span class="kpi-chip {{ $facultyChipClass }}">{{ $facultyChipText }}</span>
                                </div>
                                <h3 class="kpi-value">{{ number_format($activeFaculty) }}</h3>
                                <p class="kpi-label">Teachers</p>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="kpi-card">
                                <div class="kpi-head">
                                    <div class="kpi-icon grades">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <span class="kpi-chip {{ $gradeChipClass }}">{{ $gradeChipText }}</span>
                                </div>
                                <h3 class="kpi-value">{{ number_format($gradeApprovalCount) }}</h3>
                                <p class="kpi-label">Grade Approval</p>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-4 dashboard-section" id="adminLiveSection" data-live-url="{{ route('dashboard.admin.live-section') }}">
                        <div class="col-lg-8 dashboard-main-column">
                            <div class="activity-card dashboard-notifications-card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="fas fa-bell me-2"></i>
                                        Recent Logs
                                    </span>
                                </div>

                                <div class="card-body p-0">
                                    @if($recentLogs->isEmpty())
                                        <div class="p-4 text-center text-muted">
                                            <i class="fas fa-clipboard-list fa-3x mb-3 opacity-50"></i>
                                            <p class="mb-0">No recent logs found.</p>
                                        </div>
                                    @else
                                        <div class="table-responsive recent-logs-table-wrapper">
                                            <table class="table enrollment-table">
                                                <thead>
                                                    <tr>
                                                        <th>Activity</th>
                                                        <th>Performed By</th>
                                                        <th>Time</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($recentLogs as $log)
                                                        <tr>
                                                            <td data-label="Activity">
                                                                <div class="log-cell">
                                                                    <span class="log-title">{{ $log['title'] }}</span>
                                                                    <span class="log-detail">{{ $log['description'] }}</span>
                                                                </div>
                                                            </td>
                                                            <td data-label="Performed By"><span class="log-actor">{{ $log['actor'] }}</span></td>
                                                            <td data-label="Time"><span class="log-time">{{ $log['time']?->timezone('Asia/Manila')->format('M d, g:i A') }}</span></td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @if($recentLogs->hasPages())
                                            <div class="recent-log-pagination p-3 border-top bg-white">
                                                {{ $recentLogs->onEachSide(1)->links('pagination::bootstrap-5') }}
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 dashboard-side-column">
                            <div class="activity-card announcement-side-card h-100">
                                <div class="card-header d-flex align-items-center">
                                    <span>
                                        <i class="fas fa-bullhorn me-2"></i>
                                        Announcement
                                    </span>
                                </div>

                                <div class="card-body p-0">
                                    @if($upcomingEvents->isEmpty() && $recentAnnouncements->isEmpty())
                                        <div class="p-4 text-center text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                            <p class="mb-0">No upcoming events or announcements.</p>
                                        </div>
                                    @else
                                        <div class="announcement-scroll-area">
                                            <div class="events-list p-3">
                                                @if($upcomingEvents->isNotEmpty())
                                                    <div class="events-group-title">Events</div>
                                                    @foreach($upcomingEvents as $index => $event)
                                                        @php
                                                            $dateClass = ['event-date-pill', 'event-date-pill blue', 'event-date-pill violet'];
                                                            $pillClass = $dateClass[$index % count($dateClass)];
                                                            $eventTime = $event->is_all_day ? 'All Day' : $event->start_date->format('g A');
                                                            $eventType = $event->event_type ? ucfirst($event->event_type) : 'General';
                                                        @endphp
                                                        <div class="event-item">
                                                            <div class="{{ $pillClass }}">
                                                                <span class="day">{{ $event->start_date->format('d') }}</span>
                                                                <span class="month">{{ strtoupper($event->start_date->format('M')) }}</span>
                                                            </div>
                                                            <div>
                                                                <h6 class="event-title">{{ $event->title }}</h6>
                                                                <p class="event-meta">{{ $eventType }} • {{ $eventTime }}</p>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif

                                                @if($recentAnnouncements->isNotEmpty())
                                                    <div class="events-group-title {{ $upcomingEvents->isNotEmpty() ? 'mt-2' : '' }}"></div>
                                                    @foreach($recentAnnouncements as $announcement)
                                                        <div class="announcement-item">
                                                            <div class="announcement-icon">
                                                                <i class="fas fa-bullhorn"></i>
                                                            </div>
                                                            <div>
                                                                <h6 class="announcement-title">{{ $announcement->title }}</h6>
                                                                <p class="announcement-meta">
                                                                    {{ ucfirst($announcement->priority ?? 'normal') }} • {{ $announcement->created_at?->format('M d') }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="card-footer bg-white border-0 border-top p-3 text-center">
                                    <a href="{{ route('announcements.index') }}" class="btn btn-sm btn-outline-success px-4">
                                        View All
                                    </a>
                                </div>
                            </div>
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

        var liveSectionEl = document.getElementById('adminLiveSection');
        var pollingTimerId = null;
        var isRefreshing = false;

        function refreshLiveSection() {
            if (!liveSectionEl || isRefreshing || document.hidden) {
                return;
            }

            var liveUrl = liveSectionEl.getAttribute('data-live-url');
            if (!liveUrl) {
                return;
            }

            isRefreshing = true;

            var requestUrl = new URL(liveUrl, window.location.origin);
            var currentParams = new URLSearchParams(window.location.search);
            var currentLogsPage = currentParams.get('logs_page');
            if (currentLogsPage) {
                requestUrl.searchParams.set('logs_page', currentLogsPage);
            }

            fetch(requestUrl.toString(), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error('Live update request failed');
                    }
                    return response.json();
                })
                .then(function(payload) {
                    if (!payload || !payload.html) {
                        return;
                    }

                    var tempWrapper = document.createElement('div');
                    tempWrapper.innerHTML = payload.html.trim();
                    var freshSection = tempWrapper.firstElementChild;

                    if (!freshSection) {
                        return;
                    }

                    liveSectionEl.replaceWith(freshSection);
                    liveSectionEl = freshSection;
                })
                .catch(function() {
                    // Silently ignore polling failures to avoid interrupting dashboard usage.
                })
                .finally(function() {
                    isRefreshing = false;
                });
        }

        pollingTimerId = window.setInterval(refreshLiveSection, 10000);

        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                refreshLiveSection();
            }
        });

        window.addEventListener('beforeunload', function() {
            if (pollingTimerId) {
                window.clearInterval(pollingTimerId);
            }
        });
    });
</script>

@endsection