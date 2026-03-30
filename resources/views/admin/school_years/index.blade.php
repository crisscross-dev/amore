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

    #schoolYearDatePickerModal .modal-dialog {
        max-width: 900px;
    }

    #schoolYearDatePickerModal .sy-date-picker-columns {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.9rem;
    }

    #schoolYearDatePickerModal .sy-date-picker-column {
        border: 2px solid #198754;
        border-radius: 0.75rem;
        overflow: hidden;
        background: #fff;
        min-height: 360px;
        display: flex;
        flex-direction: column;
    }

    #schoolYearDatePickerModal .sy-date-picker-column.is-locked {
        border-color: #b9bec5;
        background: #f1f3f5;
    }

    #schoolYearDatePickerModal .sy-date-picker-column-title {
        font-weight: 700;
        color: #198754;
        text-align: center;
        padding: 0.7rem;
        border-bottom: 1px solid #198754;
        font-size: 1.05rem;
        background: #fff;
    }

    #schoolYearDatePickerModal .sy-date-picker-column.is-locked .sy-date-picker-column-title {
        color: #6c757d;
        border-bottom-color: #b9bec5;
        background: #eceff2;
    }

    #schoolYearDatePickerModal .sy-date-picker-column-body {
        padding: 0.5rem;
        overflow-y: auto;
        max-height: 320px;
        background: #f8f9fa;
        flex: 1 1 auto;
    }

    #schoolYearDatePickerModal .sy-date-picker-option {
        width: 100%;
        border: 1px solid #d8dee3;
        border-radius: 0.42rem;
        background: #fff;
        color: #212529;
        text-align: center;
        padding: 0.5rem 0.65rem;
        margin-bottom: 0.25rem;
        font-weight: 500;
    }

    #schoolYearDatePickerModal .sy-date-picker-option:last-child {
        margin-bottom: 0;
    }

    #schoolYearDatePickerModal .sy-date-picker-option:hover {
        background: #edf8f2;
    }

    #schoolYearDatePickerModal .sy-date-picker-option.is-selected {
        background: #198754;
        border-color: #198754;
        color: #fff;
        font-weight: 600;
    }

    #schoolYearDatePickerModal .sy-date-picker-option.is-disabled,
    #schoolYearDatePickerModal .sy-date-picker-option:disabled {
        opacity: 0.55;
        pointer-events: none;
        color: #6c757d;
        background-color: #e9ecef;
        border-color: #cfd4da;
    }

    .sy-date-picker-trigger .form-control[readonly] {
        background: #fff;
        cursor: pointer;
        pointer-events: auto;
    }

    .sy-date-picker-trigger .btn.js-open-sy-date-picker {
        cursor: pointer;
    }

    .sy-date-picker-trigger .form-control.sy-date-disabled {
        background: #f1f5f9;
        color: #94a3b8;
        cursor: not-allowed;
    }

    @media (max-width: 991px) {
        #schoolYearDatePickerModal .sy-date-picker-columns {
            grid-template-columns: 1fr;
        }
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
                                    <small>School Year</small>
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


                <!-- School Years Card -->
                <div class="admissions-card sy-card mb-4">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-calendar-check me-2"></i>School Year
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
                                                    data-year-name-input
                                                    inputmode="numeric"
                                                    maxlength="9"
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
                                            data-year-name-input
                                            inputmode="numeric"
                                            maxlength="9"
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
                                            <div class="input-group sy-date-picker-trigger">
                                                <input
                                                    type="date"
                                                    class="form-control @error('start_date') is-invalid @enderror"
                                                    id="start_date"
                                                    name="start_date"
                                                    value="{{ old('start_date') }}"
                                                    required
                                                    readonly
                                                    autocomplete="off">
                                                <button type="button" class="btn btn-outline-success js-open-native-date" data-date-input-id="start_date">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </button>
                                            </div>
                                            @error('start_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="end_date" class="form-label sy-modal-label">Academic Year End <span class="text-danger">*</span></label>
                                            <div class="input-group sy-date-picker-trigger">
                                                <input
                                                    type="date"
                                                    class="form-control @error('end_date') is-invalid @enderror"
                                                    id="end_date"
                                                    name="end_date"
                                                    value="{{ old('end_date') }}"
                                                    required
                                                    readonly
                                                    autocomplete="off">
                                                <button type="button" class="btn btn-outline-success js-open-native-date" data-date-input-id="end_date">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </button>
                                            </div>
                                            @error('end_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="enrollment_start" class="form-label sy-modal-label">Enrollment Start <span class="text-danger">*</span></label>
                                            <div class="input-group sy-date-picker-trigger">
                                                <input
                                                    type="date"
                                                    class="form-control @error('enrollment_start') is-invalid @enderror"
                                                    id="enrollment_start"
                                                    name="enrollment_start"
                                                    value="{{ old('enrollment_start') }}"
                                                    required
                                                    readonly
                                                    autocomplete="off">
                                                <button type="button" class="btn btn-outline-success js-open-native-date" data-date-input-id="enrollment_start">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </button>
                                            </div>
                                            @error('enrollment_start')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="enrollment_end" class="form-label sy-modal-label">Enrollment End <span class="text-danger">*</span></label>
                                            <div class="input-group sy-date-picker-trigger">
                                                <input
                                                    type="date"
                                                    class="form-control @error('enrollment_end') is-invalid @enderror"
                                                    id="enrollment_end"
                                                    name="enrollment_end"
                                                    value="{{ old('enrollment_end') }}"
                                                    required
                                                    readonly
                                                    autocomplete="off">
                                                <button type="button" class="btn btn-outline-success js-open-native-date" data-date-input-id="enrollment_end">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </button>
                                            </div>
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

                <div class="modal fade" id="schoolYearDatePickerModal" tabindex="-1" aria-labelledby="schoolYearDatePickerLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 shadow-lg">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title" id="schoolYearDatePickerLabel">
                                    <i class="fas fa-calendar-alt me-2"></i>Select Date
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="sy-date-picker-columns">
                                    <div class="sy-date-picker-column" id="syDatePickerYearColumn">
                                        <div class="sy-date-picker-column-title">Year</div>
                                        <div class="sy-date-picker-column-body" id="syDatePickerYearList"></div>
                                    </div>
                                    <div class="sy-date-picker-column" id="syDatePickerMonthColumn">
                                        <div class="sy-date-picker-column-title">Month</div>
                                        <div class="sy-date-picker-column-body" id="syDatePickerMonthList"></div>
                                    </div>
                                    <div class="sy-date-picker-column" id="syDatePickerDayColumn">
                                        <div class="sy-date-picker-column-title">Day</div>
                                        <div class="sy-date-picker-column-body" id="syDatePickerDayList"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary" id="syDatePickerClearBtn">Clear</button>
                                <button type="button" class="btn btn-success" id="syDatePickerApplyBtn">Apply</button>
                            </div>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function normalizeYearNameInput(value) {
            var digits = String(value || '').replace(/\D+/g, '').slice(0, 8);
            if (digits.length <= 4) {
                return digits;
            }

            return digits.slice(0, 4) + '-' + digits.slice(4);
        }

        function isControlKey(event) {
            return event.ctrlKey || event.metaKey || [
                'Backspace',
                'Delete',
                'Tab',
                'ArrowLeft',
                'ArrowRight',
                'Home',
                'End'
            ].indexOf(event.key) !== -1;
        }

        document.querySelectorAll('[data-year-name-input]').forEach(function(input) {
            input.value = normalizeYearNameInput(input.value);

            input.addEventListener('keydown', function(event) {
                if (isControlKey(event)) {
                    return;
                }

                if (['e', 'E', '+', '-', '.'].indexOf(event.key) !== -1) {
                    event.preventDefault();
                    return;
                }

                if (!/^\d$/.test(event.key)) {
                    event.preventDefault();
                }
            });

            input.addEventListener('input', function() {
                input.value = normalizeYearNameInput(input.value);
            });

            input.addEventListener('paste', function() {
                setTimeout(function() {
                    input.value = normalizeYearNameInput(input.value);
                }, 0);
            });
        });

        function bindNativeDateTrigger(buttonSelector) {
            document.querySelectorAll(buttonSelector).forEach(function(button) {
                button.addEventListener('click', function() {
                    var inputId = button.getAttribute('data-date-input-id');
                    if (!inputId) {
                        return;
                    }

                    var dateInput = document.getElementById(inputId);
                    if (!dateInput || dateInput.disabled) {
                        return;
                    }

                    dateInput.focus();
                    if (dateInput._flatpickr && typeof dateInput._flatpickr.open === 'function') {
                        dateInput._flatpickr.open();
                        return;
                    }

                    if (typeof dateInput.showPicker === 'function') {
                        dateInput.showPicker();
                    } else {
                        dateInput.click();
                    }
                });
            });
        }

        function syncDateDependency(startInput, endInput, disabledPlaceholder) {
            if (!startInput || !endInput) {
                return;
            }

            var hasStart = !!startInput.value;
            endInput.min = hasStart ? startInput.value : '';
            endInput.disabled = !hasStart;
            endInput.classList.toggle('sy-date-disabled', !hasStart);

            if (!endInput.dataset.defaultPlaceholder) {
                endInput.dataset.defaultPlaceholder = endInput.getAttribute('placeholder') || '';
            }

            endInput.setAttribute('placeholder', hasStart ?
                endInput.dataset.defaultPlaceholder :
                disabledPlaceholder);

            if (!hasStart) {
                endInput.value = '';
                return;
            }

            if (endInput.value && endInput.value < startInput.value) {
                endInput.value = '';
            }
        }

        var startDateInput = document.getElementById('start_date');
        var endDateInput = document.getElementById('end_date');
        var enrollmentStartInput = document.getElementById('enrollment_start');
        var enrollmentEndInput = document.getElementById('enrollment_end');

        function updateDateDependencies() {
            syncDateDependency(startDateInput, endDateInput, 'Set Academic Year Start first');
            syncDateDependency(enrollmentStartInput, enrollmentEndInput, 'Set Enrollment Start first');
        }

        [startDateInput, enrollmentStartInput].forEach(function(input) {
            if (!input) {
                return;
            }

            input.addEventListener('change', updateDateDependencies);
            input.addEventListener('input', updateDateDependencies);
        });

        bindNativeDateTrigger('.js-open-native-date');
        updateDateDependencies();
    });
</script>
@endsection