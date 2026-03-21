@extends('layouts.app')

@section('title', 'Approved Admissions - Admin Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Admin Dashboard CSS -->
@vite(['resources/css/layouts/dashboard-roles/dashboard-admin.css', 'resources/css/pagination.css', 'resources/js/admissions.js'])

<style>
    .admission-name {
        color: #198754;
    }
</style>

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
                    <!-- Page header -->
                    <div class="welcome-card mb-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-2">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Approved Admissions
                                </h4>
                                <p class="mb-0 opacity-90">
                                    View all approved admission applications
                                </p>
                            </div>
                            <div class="d-none d-lg-block">
                                <a href="{{ route('admissions.selection') }}" class="btn btn-success btn-lg">
                                    <i class="fas fa-plus me-2"></i>New Admission
                                </a>
                            </div>
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

                    <!-- Filters and Search -->
                    <div class="admissions-card admissions-dashboard__card">
                        <div class="card-header admissions-dashboard__card-header">
                            <h5><i class="fas fa-filter me-2"></i>Filter Approved Applications</h5>
                        </div>
                        <div class="card-body admissions-dashboard__card-body">
                            <form method="GET" action="{{ route('admin.admissions.approved') }}" id="filterForm">
                                <div class="filter-grid admissions-dashboard__filters">
                                    <!-- Type Filter -->
                                    <div class="filter-item admissions-dashboard__filters-item">
                                        <label for="typeFilter"><i class="fas fa-graduation-cap me-1"></i>Admission Type</label>
                                        <select name="type" id="typeFilter" class="form-select">
                                            <option value="all" {{ request('type', 'all') === 'all' ? 'selected' : '' }}>All Types</option>
                                            <option value="jhs" {{ request('type') === 'jhs' ? 'selected' : '' }}>Junior High School (JHS)</option>
                                            <option value="shs" {{ request('type') === 'shs' ? 'selected' : '' }}>Senior High School (SHS)</option>
                                        </select>
                                    </div>

                                    <!-- Search -->
                                    <div class="filter-item admissions-dashboard__filters-item">
                                        <label for="searchInput"><i class="fas fa-search me-1"></i>Search</label>
                                        <input type="text" name="search" id="searchInput" class="form-control"
                                            placeholder="Name, LRN, or ID..." value="{{ request('search') }}">
                                    </div>

                                    <!-- Actions -->
                                    <div class="filter-item filter-actions admissions-dashboard__filters-item admissions-dashboard__filters-actions">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                        <a href="{{ route('admin.admissions.approved') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-redo"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Admissions List -->
                    <div class="admissions-card admissions-dashboard__card">
                        <div class="card-header admissions-dashboard__card-header">
                            <h5><i class="fas fa-list me-2"></i>Applications List</h5>
                            <div class="view-rejected-btn">
                                <a href="{{ route('admin.enrollments.index') }}" class="btn btn-warning">
                                    <i class="fas fa-user-edit me-2"></i>Go to Enrollments
                                </a>
                            </div>
                        </div>
                        <div class="card-body admissions-dashboard__card-body">
                            @if($admissions->count() > 0)
                            <div class="admissions-list admissions-dashboard__list">
                                @foreach($admissions as $admission)
                                <div class="admission-item admissions-dashboard__list-item">
                                    <div class="admission-info admissions-dashboard__list-content">
                                        <div class="admission-header-row admissions-dashboard__list-header">
                                            <div class="admission-name-section admissions-dashboard__list-header-group">
                                                <h6 class="admission-name">{{ $admission->full_name }}</h6>
                                                <div class="admission-meta admissions-dashboard__list-meta">
                                                    <span class="meta-item admissions-dashboard__list-meta-item">
                                                        <i class="fas fa-id-card"></i> {{ $admission->applicant_id ?? 'N/A' }}
                                                    </span>
                                                    <span class="meta-item admissions-dashboard__list-meta-item">
                                                        <i class="fas fa-hashtag"></i> LRN: {{ $admission->lrn }}
                                                    </span>
                                                    <span class="meta-item admissions-dashboard__list-meta-item">
                                                        <i class="fas fa-graduation-cap"></i> {{ strtoupper($admission->school_level) }}
                                                    </span>
                                                    @if(strtoupper($admission->school_level) === 'SHS' && $admission->strand)
                                                    <span class="meta-item admissions-dashboard__list-meta-item">
                                                        <i class="fas fa-book"></i> {{ $admission->strand }}
                                                        @if($admission->strand === 'TVL' && $admission->tvl_specialization)
                                                        - {{ $admission->tvl_specialization }}
                                                        @endif
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="admission-status-section admissions-dashboard__list-status">
                                                @include('admin.admissions._status_badge', ['status' => $admission->status])
                                            </div>
                                        </div>

                                        <div class="admission-details-row admissions-dashboard__list-details">
                                            <div class="detail-item admissions-dashboard__list-detail-item">
                                                <i class="fas fa-calendar"></i>
                                                Applied: {{ $admission->created_at->format('M d, Y') }}
                                            </div>
                                            <div class="detail-item admissions-dashboard__list-detail-item">
                                                <i class="fas fa-school"></i>
                                                From: {{ $admission->school_name }}
                                            </div>
                                            @if($admission->status === 'approved' && $admission->approved_at)
                                            <div class="detail-item admissions-dashboard__list-detail-item text-success">
                                                <i class="fas fa-check"></i>
                                                Approved: {{ $admission->approved_at->format('M d, Y') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="admission-actions admissions-dashboard__list-actions">
                                        <a href="{{ route('admin.admissions.show', ['type' => $admission->school_level, 'id' => $admission->id]) }}"
                                            class="btn btn-success btn-sm">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>

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