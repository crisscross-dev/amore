@extends('layouts.app')

@section('title', 'Approve Admissions - Admin Dashboard - Amore Academy')

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
                            <i class="fas fa-user-check me-2"></i>
                            Approve Admissions
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

                    <!-- Statistics Cards -->
                    <div class="stats-grid admissions-dashboard__stats-grid">
                        <div class="stat-card admissions-dashboard__stat-card">
                            <div class="stat-icon stat-icon-primary admissions-dashboard__stat-icon admissions-dashboard__stat-icon--primary">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <div class="stat-content admissions-dashboard__stat-content">
                                <div class="stat-value admissions-dashboard__stat-value">{{ $stats['total'] }}</div>
                                <div class="stat-label admissions-dashboard__stat-label">Total Applications</div>
                            </div>
                        </div>

                        <div class="stat-card admissions-dashboard__stat-card">
                            <div class="stat-icon stat-icon-warning admissions-dashboard__stat-icon admissions-dashboard__stat-icon--warning">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-content admissions-dashboard__stat-content">
                                <div class="stat-value admissions-dashboard__stat-value">{{ $stats['pending'] }}</div>
                                <div class="stat-label admissions-dashboard__stat-label">Pending Review</div>
                            </div>
                        </div>

                        <div class="stat-card admissions-dashboard__stat-card">
                            <div class="stat-icon stat-icon-success admissions-dashboard__stat-icon admissions-dashboard__stat-icon--success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-content admissions-dashboard__stat-content">
                                <div class="stat-value admissions-dashboard__stat-value">{{ $stats['approved'] }}</div>
                                <div class="stat-label admissions-dashboard__stat-label">Approved</div>
                            </div>
                        </div>

                    </div>

                    <!-- Filters and Search -->
                    <div class="admissions-card admissions-dashboard__card">
                        <div class="card-header admissions-dashboard__card-header">
                            <h5><i class="fas fa-filter me-2"></i>Filter Applications</h5>
                        </div>
                        <div class="card-body admissions-dashboard__card-body">
                            <form method="GET" action="{{ route('admin.admissions.index') }}" id="filterForm">
                                <div class="filter-grid admissions-dashboard__filters">
                                    <!-- Type Filter -->
                                    <div class="filter-item admissions-dashboard__filters-item">
                                        <label for="typeFilter"><i class="fas fa-graduation-cap me-1"></i>Admission Type</label>
                                        <select name="type" id="typeFilter" class="form-select">
                                            <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All Types</option>
                                            <option value="jhs" {{ $type === 'jhs' ? 'selected' : '' }}>Junior High School (JHS)</option>
                                            <option value="shs" {{ $type === 'shs' ? 'selected' : '' }}>Senior High School (SHS)</option>
                                        </select>
                                    </div>

                                    <!-- Status Filter -->
                                    <!-- <div class="filter-item">
                        <label for="statusFilter"><i class="fas fa-flag me-1"></i>Status</label>
                        <select name="status" id="statusFilter" class="form-select">
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Status</option>
                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="waitlisted" {{ $status === 'waitlisted' ? 'selected' : '' }}>Waitlisted</option>
                        </select>
                    </div> -->

                                    <!-- Search -->
                                    <div class="filter-item admissions-dashboard__filters-item">
                                        <label for="searchInput"><i class="fas fa-search me-1"></i>Search</label>
                                        <input type="text" name="search" id="searchInput" class="form-control"
                                            placeholder="Name, LRN, or ID..." value="{{ $search }}">
                                    </div>

                                    <!-- Actions -->
                                    <div class="filter-item filter-actions admissions-dashboard__filters-item admissions-dashboard__filters-actions">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                        <a href="{{ route('admin.admissions.index') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-redo"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Bulk Actions Bar -->
                    <div class="bulk-actions-bar admissions-dashboard__bulk-actions" id="bulkActionsBar" style="display: none;">
                        <div class="bulk-actions-content admissions-dashboard__bulk-actions-content">
                            <div class="bulk-actions-info admissions-dashboard__bulk-actions-info">
                                <i class="fas fa-check-square"></i>
                                <span id="selectedCount">0</span> application(s) selected
                            </div>
                            <div class="bulk-actions-buttons admissions-dashboard__bulk-actions-buttons">
                                <button type="button" class="btn btn-success" id="bulkApproveBtn">
                                    <i class="fas fa-check"></i> Approve Selected
                                </button>
                                <button type="button" class="btn btn-danger" id="bulkRejectBtn">
                                    <i class="fas fa-times"></i> Reject Selected
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="clearSelectionBtn">
                                    <i class="fas fa-times-circle"></i> Clear Selection
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Admissions List -->
                    <div class="admissions-card admissions-dashboard__card">
                        <div class="card-header admissions-dashboard__card-header">
                            <h5><i class="fas fa-list me-2"></i>Applications List</h5>
                            <div class="bulk-select-all admissions-dashboard__bulk-select-all">
                                <a href="{{ route('admin.admissions.approved') }}" class="btn btn-warning">
                                    <i class="fas fa-user-edit me-2"></i>Approved Students
                                </a>
                            </div>
                        </div>
                        <div class="card-body admissions-dashboard__card-body">
                            @if($admissions->count() > 0)
                            <div class="admissions-list admissions-dashboard__list">
                                @foreach($admissions as $admission)
                                <div class="admission-item admissions-dashboard__list-item">
                                    <div class="admission-checkbox admissions-dashboard__list-checkbox">
                                        <input type="checkbox" class="form-check-input admission-select"
                                            data-type="{{ strtolower($admission->admission_type) }}"
                                            data-id="{{ $admission->id }}">
                                    </div>

                                    <div class="admission-info admissions-dashboard__list-content">
                                        <div class="admission-header-row admissions-dashboard__list-header">
                                            <div class="admission-name-section admissions-dashboard__list-header-group">
                                                <h5 class="admission-name" style="color: #198754">{{ $admission->full_name }}</h5>
                                                <div class="admission-meta admissions-dashboard__list-meta">
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

                <!-- Bulk Reject Modal -->
                <div class="modal fade" id="bulkRejectModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="fas fa-times-circle me-2"></i>Reject Selected Applications</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form id="bulkRejectForm" method="POST" action="{{ route('admin.admissions.bulk-action') }}">
                                @csrf
                                <div class="modal-body">
                                    <input type="hidden" name="action" value="reject">
                                    <div id="bulkAdmissionsInput"></div>

                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        You are about to reject <strong id="bulkRejectCount">0</strong> application(s).
                                    </div>

                                    <div class="mb-3">
                                        <label for="bulkRejectionReason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="bulkRejectionReason" name="rejection_reason"
                                            rows="4" required placeholder="Enter reason for rejection..."></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-times"></i> Reject Applications
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Bulk Approve Modal -->
                <div class="modal fade" id="bulkApproveModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="fas fa-check-circle me-2"></i>Approve Selected Applications</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form id="bulkApproveForm" method="POST" action="{{ route('admin.admissions.bulk-action') }}">
                                @csrf
                                <div class="modal-body">
                                    <input type="hidden" name="action" value="approve">
                                    <div id="bulkApproveAdmissionsInput"></div>

                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle"></i>
                                        You are about to approve <strong id="bulkApproveCount">0</strong> application(s).
                                    </div>

                                    <p>Are you sure you want to approve these applications?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check"></i> Approve Applications
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>
</div>

@endsection