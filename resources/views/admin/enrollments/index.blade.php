@extends('layouts.app')

@section('title', 'Enrollment Approvals - Admin Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Admin Dashboard CSS -->
@vite(['resources/css/layouts/dashboard-roles/dashboard-admin.css'])

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
                                <a href="{{ route('admin.enrollments.index') }}" class="btn mobile-nav-btn w-100 active">
                                    <i class="fas fa-user-plus d-block mb-1"></i>
                                    <small>Enrollments</small>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('admin.subjects.index') }}" class="btn mobile-nav-btn w-100">
                                    <i class="fas fa-book d-block mb-1"></i>
                                    <small>Subjects</small>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('admin.admissions.index') }}" class="btn mobile-nav-btn w-100">
                                    <i class="fas fa-user-check d-block mb-1"></i>
                                    <small>Admissions</small>
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

                <div class="header-title d-flex align-items-center justify-content-between mb-2">
                    <h5 class="mb-2 fw-semibold text-success">
                        <i class="fas fa-user-plus me-2"></i>
                        Enrollment Approvals
                    </h5>
                    <!-- <div class="d-none d-lg-block">
                        <a href="{{ route('calendar.create') }}" class="btn btn-success btn-m">
                            <i class="fas fa-plus me-2"></i>Add Activity
                        </a>
                    </div> -->
                </div>

                <!-- Flash Messages -->
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-warning text-white mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="small">Pending</div>
                                        <div class="h3">{{ $stats['pending'] }}</div>
                                    </div>
                                    <i class="bi bi-clock-history fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-success text-white mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="small">Approved</div>
                                        <div class="h3">{{ $stats['approved'] }}</div>
                                    </div>
                                    <i class="bi bi-check-circle fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-danger text-white mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="small">Rejected</div>
                                        <div class="h3">{{ $stats['rejected'] }}</div>
                                    </div>
                                    <i class="bi bi-x-circle fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-primary text-white mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="small">Total</div>
                                        <div class="h3">{{ $stats['total'] }}</div>
                                    </div>
                                    <i class="bi bi-people fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="admissions-card mb-4">
                    <div class="card-body">
                        <form action="{{ route('admin.enrollments.index') }}" method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Grade Level</label>
                                <select name="grade_level" class="form-select">
                                    <option value="">All Grade Levels</option>
                                    @foreach($gradeLevels as $level)
                                    <option value="{{ $level }}" {{ request('grade_level') == $level ? 'selected' : '' }}>{{ $level }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">School Year</label>
                                <select name="school_year_id" class="form-select">
                                    @foreach($schoolYears as $sy)
                                    <option value="{{ $sy->id }}" {{ request('school_year_id') == $sy->id ? 'selected' : '' }}>{{ $sy->year_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Search</label>
                                <input type="text" name="search" class="form-control" placeholder="Student name" value="{{ request('search') }}">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-funnel me-1"></i>Filter
                                </button>
                                <a href="{{ route('admin.enrollments.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Enrollments Table -->
                <div class="admissions-card">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-list-ul me-2"></i>Enrollment Requests
                    </div>
                    <div class="card-body">
                        @if($enrollments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Grade Level</th>
                                        <th>School Year</th>
                                        <th>Enrollment Date</th>
                                        <th>Documents</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrollments as $enrollment)
                                    <tr>
                                        <td>
                                            {{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}
                                            <br><small class="text-muted">{{ $enrollment->student->email }}</small>
                                        </td>
                                        <td>{{ $enrollment->current_grade_level }} → <strong>{{ $enrollment->enrolling_grade_level }}</strong></td>
                                        <td>{{ $enrollment->schoolYear->year_name }}</td>
                                        <td>{{ $enrollment->enrollment_date->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $enrollment->documents->count() }} uploaded</span>
                                        </td>
                                        <td>
                                            @if($enrollment->status === 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                            @elseif($enrollment->status === 'approved')
                                            <span class="badge bg-success">Approved</span>
                                            @elseif($enrollment->status === 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.enrollments.show', $enrollment) }}" class="btn btn-sm btn-primary" title="Review Enrollment">
                                                <i class="fas fa-eye me-1"></i>Review
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $enrollments->links() }}
                        </div>
                        @else
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted">No enrollment requests found matching your criteria.</p>
                            </td>
                        </tr>
                        @endif
                    </div>
                </div>

                <div class="admissions-card mt-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-user-check me-2"></i>Approved Admissions Ready for Enrollment</span>
                        <span class="badge bg-light text-primary">{{ $admissionsReady->total() }}</span>
                    </div>
                    <div class="card-body">
                        @if($admissionsReady->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Applicant</th>
                                        <th>Grade Level</th>
                                        <th>School Level</th>
                                        <th>Approved At</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($admissionsReady as $admission)
                                    <tr>
                                        <td>
                                            <strong>{{ $admission->first_name }} {{ $admission->last_name }}</strong>
                                            <br><small class="text-muted">{{ $admission->email ?: 'No email provided' }}</small>
                                        </td>
                                        <td>{{ $admission->grade_level ?: 'N/A' }}</td>
                                        <td>{{ strtoupper($admission->school_level) }}</td>
                                        <td>{{ optional($admission->approved_at)->format('M d, Y h:i A') ?: 'N/A' }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.enrollments.review-admission', $admission) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-user-plus me-1"></i>Assign Section
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $admissionsReady->links() }}
                        </div>
                        @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No approved admissions are waiting for enrollment processing.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection