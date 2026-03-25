@extends('layouts.app')

@section('title', 'School Year Management - Admin Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Admin Dashboard CSS -->
@vite(['resources/css/layouts/dashboard-roles/dashboard-admin.css'])

<style>
    .sy-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    .sy-subtitle {
        color: #64748b;
        margin-bottom: 0;
        font-size: 0.92rem;
    }

    .sy-card {
        border: 1px solid #d1fae5;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.07);
    }

    .sy-card .card-header {
        background: linear-gradient(135deg, #166534 0%, #15803d 100%);
        border: 0;
        padding: 0.85rem 1rem;
    }

    .sy-card .table thead th {
        color: #0f172a;
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        border-bottom-width: 1px;
    }

    .sy-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 999px;
        padding: 0.2rem 0.6rem;
    }

    .sy-pill-active {
        background: #dcfce7;
        color: #166534;
    }

    .sy-pill-inactive {
        background: #e2e8f0;
        color: #334155;
    }

    .sy-empty {
        border: 1px dashed #bbf7d0;
        border-radius: 12px;
        background: #f8fafc;
        padding: 2rem 1.25rem;
        text-align: center;
    }

    .sy-modal-label {
        font-weight: 600;
        color: #334155;
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
                                <a href="{{ route('admin.school-years.index') }}" class="btn mobile-nav-btn w-100 active">
                                    <i class="fas fa-calendar-check d-block mb-1"></i>
                                    <small>School Years</small>
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
                    <div>
                        <h5 class="mb-1 fw-semibold text-success">
                            <i class="fas fa-calendar-check me-2"></i>
                            School Year Management
                        </h5>
                        <p class="sy-subtitle">Manage academic timelines and enrollment windows in one place.</p>
                    </div>
                    <button type="button" class="btn btn-success btn-m" data-bs-toggle="modal" data-bs-target="#addSchoolYearModal">
                        <i class="fas fa-plus me-2"></i>Add School Year
                    </button>
                </div>

                <div class="d-lg-none mb-3">
                    <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#addSchoolYearModal">
                        <i class="fas fa-plus me-2"></i>Add School Year
                    </button>
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

                <!-- School Years Card -->
                <div class="admissions-card sy-card mb-4">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-calendar-check me-2"></i>School Years
                    </div>
                    <div class="card-body">
                        @if($schoolYears->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>School Year</th>
                                        <th>Academic Period</th>
                                        <th>Enrollment Period</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($schoolYears as $schoolYear)
                                    <tr>
                                        <td>
                                            <strong>{{ $schoolYear->year_name }}</strong>
                                        </td>
                                        <td>
                                            {{ $schoolYear->start_date->format('M d, Y') }} -
                                            {{ $schoolYear->end_date->format('M d, Y') }}
                                        </td>
                                        <td>
                                            {{ $schoolYear->enrollment_start->format('M d, Y') }} -
                                            {{ $schoolYear->enrollment_end->format('M d, Y') }}
                                            @if($schoolYear->isEnrollmentPeriod())
                                            <span class="badge bg-info text-dark ms-2">Open</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($schoolYear->is_active)
                                            <span class="sy-pill sy-pill-active"><i class="fas fa-circle fa-2xs"></i>Active</span>
                                            @else
                                            <span class="sy-pill sy-pill-inactive"><i class="fas fa-circle fa-2xs"></i>Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!$schoolYear->is_active)
                                            <form action="{{ route('admin.school-years.activate', $schoolYear) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success me-1" title="Activate">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            </form>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-warning me-1" title="Edit" data-bs-toggle="modal" data-bs-target="#editSchoolYearModal{{ $schoolYear->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.school-years.destroy', $schoolYear) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger btn-delete" data-swal="delete" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @foreach($schoolYears as $schoolYear)
                        <div class="modal fade" id="editSchoolYearModal{{ $schoolYear->id }}" tabindex="-1" aria-labelledby="editSchoolYearModalLabel{{ $schoolYear->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg">
                                    <div class="modal-header bg-warning">
                                        <h5 class="modal-title" id="editSchoolYearModalLabel{{ $schoolYear->id }}">
                                            <i class="fas fa-edit me-2"></i>Edit School Year
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('admin.school-years.update', $schoolYear) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="modal_context" value="edit-{{ $schoolYear->id }}">
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="edit_year_name_{{ $schoolYear->id }}" class="form-label sy-modal-label">School Year Name <span class="text-danger">*</span></label>
                                                <input
                                                    type="text"
                                                    class="form-control @error('year_name') is-invalid @enderror"
                                                    id="edit_year_name_{{ $schoolYear->id }}"
                                                    name="year_name"
                                                    value="{{ old('modal_context') === 'edit-' . $schoolYear->id ? old('year_name') : $schoolYear->year_name }}"
                                                    placeholder="e.g., 2026-2027"
                                                    required>
                                                @if(old('modal_context') === 'edit-' . $schoolYear->id)
                                                @error('year_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                @endif
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="edit_start_date_{{ $schoolYear->id }}" class="form-label sy-modal-label">Academic Year Start <span class="text-danger">*</span></label>
                                                    <input
                                                        type="date"
                                                        class="form-control @error('start_date') is-invalid @enderror"
                                                        id="edit_start_date_{{ $schoolYear->id }}"
                                                        name="start_date"
                                                        value="{{ old('modal_context') === 'edit-' . $schoolYear->id ? old('start_date') : optional($schoolYear->start_date)->format('Y-m-d') }}"
                                                        required>
                                                    @if(old('modal_context') === 'edit-' . $schoolYear->id)
                                                    @error('start_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    @endif
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="edit_end_date_{{ $schoolYear->id }}" class="form-label sy-modal-label">Academic Year End <span class="text-danger">*</span></label>
                                                    <input
                                                        type="date"
                                                        class="form-control @error('end_date') is-invalid @enderror"
                                                        id="edit_end_date_{{ $schoolYear->id }}"
                                                        name="end_date"
                                                        value="{{ old('modal_context') === 'edit-' . $schoolYear->id ? old('end_date') : optional($schoolYear->end_date)->format('Y-m-d') }}"
                                                        required>
                                                    @if(old('modal_context') === 'edit-' . $schoolYear->id)
                                                    @error('end_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="edit_enrollment_start_{{ $schoolYear->id }}" class="form-label sy-modal-label">Enrollment Start <span class="text-danger">*</span></label>
                                                    <input
                                                        type="date"
                                                        class="form-control @error('enrollment_start') is-invalid @enderror"
                                                        id="edit_enrollment_start_{{ $schoolYear->id }}"
                                                        name="enrollment_start"
                                                        value="{{ old('modal_context') === 'edit-' . $schoolYear->id ? old('enrollment_start') : optional($schoolYear->enrollment_start)->format('Y-m-d') }}"
                                                        required>
                                                    @if(old('modal_context') === 'edit-' . $schoolYear->id)
                                                    @error('enrollment_start')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    @endif
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="edit_enrollment_end_{{ $schoolYear->id }}" class="form-label sy-modal-label">Enrollment End <span class="text-danger">*</span></label>
                                                    <input
                                                        type="date"
                                                        class="form-control @error('enrollment_end') is-invalid @enderror"
                                                        id="edit_enrollment_end_{{ $schoolYear->id }}"
                                                        name="enrollment_end"
                                                        value="{{ old('modal_context') === 'edit-' . $schoolYear->id ? old('enrollment_end') : optional($schoolYear->enrollment_end)->format('Y-m-d') }}"
                                                        required>
                                                    @if(old('modal_context') === 'edit-' . $schoolYear->id)
                                                    @error('enrollment_end')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-warning">
                                                <i class="fas fa-save me-1"></i>Update School Year
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <div class="sy-empty">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3 d-block"></i>
                            <p class="text-muted mb-3">No school years found. Create your first school year to get started.</p>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addSchoolYearModal">
                                <i class="fas fa-plus me-2"></i>Create School Year
                            </button>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="modal fade" id="addSchoolYearModal" tabindex="-1" aria-labelledby="addSchoolYearModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title" id="addSchoolYearModalLabel">
                                    <i class="fas fa-plus-circle me-2"></i>Add School Year
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('admin.school-years.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="modal_context" value="create">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="year_name" class="form-label sy-modal-label">School Year Name <span class="text-danger">*</span></label>
                                        <input
                                            type="text"
                                            class="form-control @error('year_name') is-invalid @enderror"
                                            id="year_name"
                                            name="year_name"
                                            value="{{ old('year_name') }}"
                                            placeholder="e.g., 2026-2027"
                                            required>
                                        @error('year_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Use format YYYY-YYYY (example: 2026-2027)</small>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="start_date" class="form-label sy-modal-label">Academic Year Start <span class="text-danger">*</span></label>
                                            <input
                                                type="date"
                                                class="form-control @error('start_date') is-invalid @enderror"
                                                id="start_date"
                                                name="start_date"
                                                value="{{ old('start_date') }}"
                                                required>
                                            @error('start_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="end_date" class="form-label sy-modal-label">Academic Year End <span class="text-danger">*</span></label>
                                            <input
                                                type="date"
                                                class="form-control @error('end_date') is-invalid @enderror"
                                                id="end_date"
                                                name="end_date"
                                                value="{{ old('end_date') }}"
                                                required>
                                            @error('end_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="enrollment_start" class="form-label sy-modal-label">Enrollment Start <span class="text-danger">*</span></label>
                                            <input
                                                type="date"
                                                class="form-control @error('enrollment_start') is-invalid @enderror"
                                                id="enrollment_start"
                                                name="enrollment_start"
                                                value="{{ old('enrollment_start') }}"
                                                required>
                                            @error('enrollment_start')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="enrollment_end" class="form-label sy-modal-label">Enrollment End <span class="text-danger">*</span></label>
                                            <input
                                                type="date"
                                                class="form-control @error('enrollment_end') is-invalid @enderror"
                                                id="enrollment_end"
                                                name="enrollment_end"
                                                value="{{ old('enrollment_end') }}"
                                                required>
                                            @error('enrollment_end')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check-circle me-1"></i>Create School Year
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

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalContext = "{{ old('modal_context', 'create') }}";
        var modalElement = null;

        if (typeof modalContext === 'string' && modalContext.indexOf('edit-') === 0) {
            modalElement = document.getElementById('editSchoolYearModal' + modalContext.replace('edit-', ''));
        }

        if (!modalElement) {
            modalElement = document.getElementById('addSchoolYearModal');
        }

        if (!modalElement || typeof bootstrap === 'undefined') {
            return;
        }
        var schoolYearModal = new bootstrap.Modal(modalElement);
        schoolYearModal.show();
    });
</script>
@endif
@endsection