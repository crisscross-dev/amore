@extends('layouts.app')

@section('title', 'Approved Admissions - Admin Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Admin Dashboard CSS -->
@vite(['resources/css/layouts/dashboard-roles/dashboard-admin.css', 'resources/css/pagination.css', 'resources/js/admissions.js'])

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
                                <a href="{{ route('admin.admissions.index') }}" class="btn mobile-nav-btn w-100 active">
                                    <i class="fas fa-user-check d-block mb-1"></i>
                                    <small>Approvals</small>
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

                <div class="admissions-container admissions-dashboard">
                    <div class="header-title d-flex align-items-center justify-content-between mb-2">
                        <h5 class="mb-2 fw-semibold text-success">
                            <i class="fas fa-check-circle me-2"></i>
                            Approved Admissions
                        </h5>
                        <div class="d-none d-lg-block">
                            <a href="{{ route('admissions.selection') }}" class="btn btn-success btn-m">
                                <i class="fas fa-plus me-2"></i>New Admission
                            </a>
                        </div>
                    </div>

                    <!-- Flash Messages -->
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i>
                        <strong>Success!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i>
                        <strong>Error!</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <!-- Admissions List -->
                    <div class="admissions-card admissions-dashboard__card admissions-dashboard__card--list">
                        <div class="card-header admissions-dashboard__card-header">
                            <h5><i class="fas fa-list me-2"></i>Applications List</h5>
                            <div class="view-rejected-btn">
                                <a href="{{ route('admin.enrollments.index') }}" class="btn btn-warning">
                                    <i class="fas fa-user-edit me-2"></i>Go to Enrollments
                                </a>
                            </div>
                        </div>
                        <div class="card-body admissions-dashboard__card-body">
                            <form method="GET" action="{{ route('admin.admissions.approved') }}" id="filterForm" class="admissions-dashboard__toolbar">
                                <div class="admissions-dashboard__toolbar-fields">
                                    <div class="filter-item admissions-dashboard__filters-item admissions-dashboard__toolbar-type">
                                        <select name="type" id="typeFilter" class="form-select">
                                            <option value="all" {{ request('type', 'all') === 'all' ? 'selected' : '' }}>All Types</option>
                                            <option value="jhs" {{ request('type') === 'jhs' ? 'selected' : '' }}>Junior High School (JHS)</option>
                                            <option value="shs" {{ request('type') === 'shs' ? 'selected' : '' }}>Senior High School (SHS)</option>
                                        </select>
                                    </div>

                                    <div class="filter-item admissions-dashboard__filters-item admissions-dashboard__toolbar-search">
                                        <input type="text" name="search" id="searchInput" class="form-control" placeholder="Search by name, LRN, or applicant ID" value="{{ request('search') }}">
                                    </div>
                                </div>
                            </form>

                            @if($admissions->count() > 0)
                            <div class="table-responsive admissions-dashboard__table-wrapper">
                                <table class="table table-hover align-middle admissions-dashboard__table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Applicant</th>
                                            <th>LRN</th>
                                            <th>Admission Type</th>
                                            <th>Applied Date</th>
                                            <th>Approved Date</th>
                                            <th>School</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($admissions as $admission)
                                        <tr class="admissions-dashboard__table-row" data-modal-target="approvedAdmissionShowModal-{{ $admission->id }}">
                                            <td>
                                                <div class="admissions-dashboard__applicant-name">{{ $admission->full_name }}</div>
                                                @if(strtoupper($admission->school_level) === 'SHS' && $admission->strand)
                                                <div class="admissions-dashboard__applicant-subline text-muted">
                                                    {{ $admission->strand }}@if($admission->strand === 'TVL' && $admission->tvl_specialization) - {{ $admission->tvl_specialization }}@endif
                                                </div>
                                                @endif
                                            </td>
                                            <td>{{ $admission->lrn }}</td>
                                            <td>{{ strtoupper($admission->school_level) }}</td>
                                            <td>{{ $admission->created_at->format('M d, Y') }}</td>
                                            <td>{{ $admission->approved_at ? $admission->approved_at->format('M d, Y') : 'N/A' }}</td>
                                            <td>{{ $admission->school_name }}</td>
                                            <td>@include('admin.admissions._status_badge', ['status' => $admission->status])</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @foreach($admissions as $admission)
                            <div class="modal fade" id="approvedAdmissionShowModal-{{ $admission->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="fas fa-user-graduate me-2"></i>{{ $admission->full_name }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body admissions-dashboard__show-modal-body">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                                        <div>
                                                            <div class="h5 mb-1">{{ $admission->full_name }}</div>
                                                            <div class="text-muted small">Applicant ID: {{ $admission->applicant_id ?? 'N/A' }}</div>
                                                        </div>
                                                        <div>@include('admin.admissions._status_badge', ['status' => $admission->status])</div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <h6 class="mb-2"><i class="fas fa-user me-2"></i>Personal Information</h6>
                                                </div>
                                                <div class="col-md-6"><small class="text-muted d-block">LRN</small>
                                                    <div class="fw-semibold">{{ $admission->lrn }}</div>
                                                </div>
                                                <div class="col-md-6"><small class="text-muted d-block">Admission Type</small>
                                                    <div class="fw-semibold">{{ strtoupper($admission->school_level) }}</div>
                                                </div>
                                                <div class="col-md-6"><small class="text-muted d-block">Birthdate</small>
                                                    <div class="fw-semibold">{{ $admission->dob ? $admission->dob->format('F d, Y') : 'N/A' }}</div>
                                                </div>
                                                <div class="col-md-6"><small class="text-muted d-block">Gender</small>
                                                    <div class="fw-semibold">{{ $admission->gender ? ucfirst($admission->gender) : 'N/A' }}</div>
                                                </div>
                                                <div class="col-12"><small class="text-muted d-block">Address</small>
                                                    <div class="fw-semibold">{{ $admission->address ?? 'N/A' }}</div>
                                                </div>

                                                <div class="col-12 mt-2">
                                                    <h6 class="mb-2"><i class="fas fa-book me-2"></i>Academic Information</h6>
                                                </div>
                                                <div class="col-md-6"><small class="text-muted d-block">Previous School</small>
                                                    <div class="fw-semibold">{{ $admission->school_name }}</div>
                                                </div>
                                                <div class="col-md-6"><small class="text-muted d-block">School Type</small>
                                                    <div class="fw-semibold">{{ $admission->school_type ? ucfirst($admission->school_type) : 'N/A' }}</div>
                                                </div>
                                                @if(strtoupper($admission->school_level) === 'SHS' && $admission->strand)
                                                <div class="col-md-6"><small class="text-muted d-block">Strand</small>
                                                    <div class="fw-semibold">{{ $admission->strand }}</div>
                                                </div>
                                                @endif
                                                @if($admission->strand === 'TVL' && $admission->tvl_specialization)
                                                <div class="col-md-6"><small class="text-muted d-block">TVL Specialization</small>
                                                    <div class="fw-semibold">{{ $admission->tvl_specialization }}</div>
                                                </div>
                                                @endif

                                                <div class="col-12 mt-2">
                                                    <h6 class="mb-2"><i class="fas fa-history me-2"></i>Application Timeline</h6>
                                                </div>
                                                <div class="col-md-6"><small class="text-muted d-block">Submitted</small>
                                                    <div class="fw-semibold">{{ $admission->created_at->format('M d, Y h:i A') }}</div>
                                                </div>
                                                <div class="col-md-6"><small class="text-muted d-block">Approved</small>
                                                    <div class="fw-semibold">{{ $admission->approved_at ? $admission->approved_at->format('M d, Y h:i A') : 'N/A' }}</div>
                                                </div>

                                                <div class="col-12 mt-2">
                                                    <h6 class="mb-2"><i class="fas fa-key me-2"></i>Login Credentials</h6>
                                                </div>
                                                <div class="col-md-6"><small class="text-muted d-block">Email</small>
                                                    <div class="fw-semibold">{{ $admission->email ?? 'N/A' }}</div>
                                                </div>
                                                <div class="col-md-6"><small class="text-muted d-block">Password</small>
                                                    <div class="fw-semibold">{{ $admission->temp_password ?? 'Not available' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach

                            <!-- Pagination -->
                            <div class="d-flex justify-content-between align-items-center mt-4 p-3 bg-light rounded border">
                                <div>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Showing <strong class="text-success">{{ $admissions->firstItem() ?? 0 }}</strong> to
                                        <strong class="text-success">{{ $admissions->lastItem() ?? 0 }}</strong> of
                                        <strong class="text-success">{{ $admissions->total() }}</strong> results
                                    </small>
                                </div>
                                <nav aria-label="Page navigation">
                                    {{ $admissions->appends(request()->query())->links('pagination::bootstrap-5') }}
                                </nav>
                            </div>
                            @else
                            <div class="empty-state admissions-dashboard__empty">
                                <i class="fas fa-inbox"></i>
                                <h5>No Applications Found</h5>
                                <p>No admission applications match your current filters.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>
</div>

@endsection