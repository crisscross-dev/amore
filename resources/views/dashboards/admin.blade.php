@extends('layouts.app')

@section('title', 'Admin Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Admin Dashboard CSS -->
@vite(['resources/css/layouts/dashboard-roles/dashboard-admin.css'])

<style>
    /* Modern Stat Cards */
    .stat-card-modern {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s, box-shadow 0.3s;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .stat-card-modern:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    }

    .stat-card-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .stat-card-icon.stat-success {
        background: linear-gradient(135deg, #198754 0%, #20c997 100%);
        color: white;
    }

    .stat-card-icon.stat-warning {
        background: linear-gradient(135deg, #ffc107 0%, #ffeb3b 100%);
        color: #000;
    }

    .stat-card-icon.stat-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #6ea8fe 100%);
        color: white;
    }

    .stat-card-icon.stat-info {
        background: linear-gradient(135deg, #0dcaf0 0%, #6edff6 100%);
        color: #000;
    }

    .stat-card-details {
        flex: 1;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #198754;
        margin: 0;
        line-height: 1;
    }

    .stat-title {
        font-size: 0.875rem;
        color: #6c757d;
        margin: 0.5rem 0;
    }

    .stat-footer {
        margin-top: 0.5rem;
    }

    /* Alert Card */
    .alert-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .alert-card .card-header {
        background: linear-gradient(135deg, #dc3545 0%, #f08793 100%);
        color: white;
        padding: 1rem 1.5rem;
        font-weight: 600;
        border: none;
    }

    .alert-card .card-body {
        padding: 1.5rem;
    }

    /* Quick Action Cards */
    .quick-action-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s, box-shadow 0.3s;
        text-align: center;
    }

    .quick-action-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    }

    .quick-action-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin: 0 auto 1rem;
        color: white;
    }

    .quick-action-card h5 {
        color: #212529;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .quick-action-card p {
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .stat-card-modern {
            flex-direction: column;
            text-align: center;
        }

        .stat-number {
            font-size: 1.75rem;
        }
    }
</style>

<div class="dashboard-container">
    <div class="container-fluid px-4">
        <div class="row">
            <!-- Main Dashboard Content -->
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

                <!-- Welcome Message -->
                <div class="welcome-card mb-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <!-- Profile Section -->
                        <div class="d-flex align-items-center gap-3">
                            <!-- Profile Picture -->
                            <div class="position-relative">
                                <img src="{{ asset('uploads/profile_picture/' . Auth::user()->profile_picture) }}"
                                    alt="Profile Picture"
                                    class="rounded-circle border border-3 border-white shadow"
                                    width="80"
                                    height="80"
                                    style="object-fit: cover;">
                                <div class="position-absolute bottom-0 end-0 bg-success rounded-circle d-flex align-items-center justify-content-center shadow"
                                    style="width: 24px; height: 24px;">
                                    <i class="fas fa-shield-alt text-white" style="font-size: 12px;"></i>
                                </div>
                            </div>

                            <!-- Profile Info -->
                            <div>
                                <h4 class="mb-1">
                                    Welcome back, {{ Auth::user()->first_name ?? 'User' }} {{ Auth::user()->last_name ?? '' }}!
                                </h4>
                                <p class="mb-1 opacity-90">
                                    <span class="badge bg-white bg-opacity-25 text-success px-3 py-1">
                                        <i class="fas fa-user-shield me-1"></i>Administrator
                                    </span>
                                    <span class="ms-2 opacity-75">
                                        <i class="fas fa-id-badge me-1"></i>{{ Auth::user()->custom_id ?? 'ADMIN-0001' }}
                                    </span>
                                </p>
                                <p class="mb-0 opacity-75 small">
                                    <i class="fas fa-graduation-cap me-1"></i>Dashboard Overview - School Year {{ $currentSchoolYear }}
                                </p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex align-items-center gap-3">
                            <a href="{{ route('profile.edit') }}" class="btn btn-warning btn-lg">
                                <i class="fas fa-user-edit me-2"></i>Edit Profile
                            </a>
                            <div class="d-none d-lg-block">
                                <i class="fas fa-tachometer-alt" style="font-size: 3rem; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Statistics Cards -->
                <div class="row g-3 mb-4">
                    <!-- Total Enrolled Students -->
                    <div class="col-lg-3 col-md-6">
                        <div class="stat-card-modern">
                            <div class="stat-card-icon stat-success">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="stat-card-details">
                                <h3 class="stat-number">{{ number_format($stats['approved']) }}</h3>
                                <p class="stat-title">Total Enrolled Students</p>
                                <div class="stat-footer">
                                    <span class="badge {{ $studentPercentChange >= 0 ? 'bg-success' : 'bg-danger' }}">
                                        <i class="fas fa-{{ $studentPercentChange >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                        {{ abs($studentPercentChange) }}%
                                    </span>
                                    <small class="text-muted ms-2">vs last year</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Admissions -->
                    <div class="col-lg-3 col-md-6">
                        <div class="stat-card-modern">
                            <div class="stat-card-icon stat-warning">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-card-details">
                                <h3 class="stat-number">{{ $stats['pending'] }}</h3>
                                <p class="stat-title">Pending Admissions</p>
                                <div class="stat-footer">
                                    <a href="{{ route('admin.admissions.index') }}" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-eye me-1"></i>Review Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Active Faculty -->
                    <div class="col-lg-3 col-md-6">
                        <div class="stat-card-modern">
                            <div class="stat-card-icon stat-primary">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <div class="stat-card-details">
                                <h3 class="stat-number">{{ $activeFaculty }}</h3>
                                <p class="stat-title">Active Faculty</p>
                                <div class="stat-footer">
                                    @if($pendingFaculty > 0)
                                    <span class="badge bg-warning">{{ $pendingFaculty }} Pending</span>
                                    @else
                                    <span class="text-muted">All approved</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Sections -->
                    <div class="col-lg-3 col-md-6">
                        <div class="stat-card-modern">
                            <div class="stat-card-icon stat-info">
                                <i class="fas fa-door-open"></i>
                            </div>
                            <div class="stat-card-details">
                                <h3 class="stat-number">{{ $totalSections }}</h3>
                                <p class="stat-title">Sections & Classes</p>
                                <div class="stat-footer">
                                    @if($unassignedSections > 0)
                                    <span class="badge bg-danger">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        {{ $unassignedSections }} Unassigned
                                    </span>
                                    @else
                                    <span class="text-success">
                                        <i class="fas fa-check"></i> All assigned
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Quick Actions -->
                <div class="row g-3 mb-4">
                    <div class="col-lg-4">
                        <div class="quick-action-card">
                            <div class="quick-action-icon bg-success">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <h5>Manage Admissions</h5>
                            <p class="text-muted">Review and approve student applications</p>
                            <a href="{{ route('admin.admissions.index') }}" class="btn btn-success btn-sm w-100">
                                <i class="fas fa-arrow-right me-2"></i>Go to Admissions
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="quick-action-card">
                            <div class="quick-action-icon bg-primary">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <h5>Manage Accounts</h5>
                            <p class="text-muted">View and manage student & faculty accounts</p>
                            <a href="{{ route('admin.accounts.manage') }}" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-arrow-right me-2"></i>Go to Accounts
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="quick-action-card">
                            <div class="quick-action-icon bg-info">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <h5>School Calendar</h5>
                            <p class="text-muted">Manage events and announcements</p>
                            <a href="{{ route('calendar.index') }}" class="btn btn-info btn-sm w-100">
                                <i class="fas fa-arrow-right me-2"></i>Go to Calendar
                            </a>
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