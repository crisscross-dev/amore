@extends('layouts.app')

@section('title', 'Manage Sections - Admin Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Admin Dashboard CSS -->
@vite(['resources/css/layouts/dashboard-roles/dashboard-admin.css', 'resources/js/admin-sections.js'])

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
                                <a href="{{ route('admin.sections.index') }}" class="btn mobile-nav-btn w-100 active">
                                    <i class="fas fa-layer-group d-block mb-1"></i>
                                    <small>Sections</small>
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
                        <i class="fas fa-layer-group me-2"></i>
                        Manage Sections
                    </h5>
                    <div class="d-none d-lg-block">
                        <button type="button" class="btn btn-primary btn-m" data-bs-toggle="modal" data-bs-target="#createSectionModal">
                            <i class="fas fa-plus me-2"></i>New Section
                        </button>
                    </div>
                </div>

                <!-- Mobile New Section Button -->
                <div class="d-lg-none mb-3 d-grid gap-2">
                    <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#createSectionModal">
                        <i class="fas fa-plus me-2"></i>New Section
                    </button>
                </div>

                <!-- Flash Messages -->
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>Please fix the highlighted errors and try again.
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <!-- Section Adviser Management -->
                <div class="admissions-card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-user-check me-2"></i>Section Adviser Management</h5>
                        <span class="badge bg-success">{{ $sections->total() }} sections</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Section Name</th>
                                        <th>Grade Level</th>
                                        <th>Assigned Adviser</th>
                                        <th>Academic Year</th>
                                        <th>Capacity</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sections as $section)
                                    <tr class="js-section-row" data-view-url="{{ route('admin.sections.show', $section) }}" title="Double-click to view details">
                                        <td>
                                            <strong>{{ $section->name }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">Grade {{ $section->grade_level }}</span>
                                        </td>
                                        <td>
                                            @if($section->adviser)
                                            <span class="fw-semibold text-success">{{ $section->adviser->first_name }} {{ $section->adviser->last_name }}</span>
                                            @else
                                            <span class="text-muted">Unassigned</span>
                                            @endif
                                        </td>
                                        <td>{{ $section->academic_year ?? '—' }}</td>
                                        <td>{{ $section->capacity ?? '—' }}</td>
                                        <td>
                                            @if($section->is_active)
                                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Active</span>
                                            @else
                                            <span class="badge bg-secondary"><i class="fas fa-times-circle me-1"></i>Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-warning me-1"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editSectionModal{{ $section->id }}"
                                                title="Edit Section">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.sections.destroy', $section) }}"
                                                method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger"
                                                    type="submit"
                                                    title="Delete Section">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                            <p class="text-muted">No sections found. Create your first section to get started.</p>
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSectionModal">
                                                <i class="fas fa-plus me-2"></i>Create New Section
                                            </button>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($sections->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $sections->links() }}
                        </div>
                        @endif
                    </div>
                </div>

                <div class="modal fade" id="createSectionModal" tabindex="-1" aria-labelledby="createSectionModalLabel" aria-hidden="true" data-open-on-load="{{ $errors->any() ? '1' : '0' }}">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <div>
                                    <h5 class="modal-title" id="createSectionModalLabel">
                                        <i class="fas fa-layer-group me-2"></i>
                                        Create New Section
                                    </h5>
                                    <p class="mb-0 text-muted small">Add a section with a typed name, selected grade, and matching subjects.</p>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @include('admin.sections._form', [
                                'section' => new \App\Models\Section(),
                                'schoolYears' => $schoolYears,
                                'subjects' => $subjects,
                                'action' => route('admin.sections.store'),
                                'method' => 'POST',
                                'submitLabel' => 'Create Section',
                                'modalContext' => 'create',
                                'isModal' => true,
                                'routeBase' => 'admin.sections.',
                                ])
                            </div>
                        </div>
                    </div>
                </div>

                @foreach($sections as $section)
                @php
                $editModalContext = 'edit-' . $section->id;
                $openEditModal = old('modal_context') === $editModalContext;
                $selectedSubjectIds = $section->subjectTeachers->pluck('subject_id')->map(fn($id) => (int) $id)->all();
                @endphp
                <div
                    class="modal fade"
                    id="editSectionModal{{ $section->id }}"
                    tabindex="-1"
                    aria-labelledby="editSectionModalLabel{{ $section->id }}"
                    aria-hidden="true"
                    data-open-on-load="{{ $openEditModal ? '1' : '0' }}">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <div>
                                    <h5 class="modal-title" id="editSectionModalLabel{{ $section->id }}">
                                        <i class="fas fa-edit me-2"></i>
                                        Edit Section
                                    </h5>
                                    <p class="mb-0 text-white-50 small">Update section details and assigned subjects.</p>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @include('admin.sections._form', [
                                'section' => $section,
                                'schoolYears' => $schoolYears,
                                'subjects' => $subjects,
                                'selectedSubjectIds' => $selectedSubjectIds,
                                'action' => route('admin.sections.update', $section),
                                'method' => 'PUT',
                                'submitLabel' => 'Save Changes',
                                'modalContext' => $editModalContext,
                                'isModal' => true,
                                'routeBase' => 'admin.sections.',
                                ])
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

            </main>
        </div>
    </div>
</div>

<!-- Assign / Change Adviser Modal -->
<div class="modal fade" id="assignAdviserModal" tabindex="-1" aria-labelledby="assignAdviserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="assignAdviserForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="assignAdviserModalLabel">Assign / Change Adviser</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2 text-muted">Section: <strong id="assignAdviserSectionName">—</strong></p>
                    <label for="assignAdviserSelect" class="form-label">Select Adviser</label>
                    <select name="adviser_id" id="assignAdviserSelect" class="form-select">
                        <option value="">No adviser</option>
                        @foreach($facultyMembers as $faculty)
                        <option value="{{ $faculty->id }}">{{ $faculty->first_name }} {{ $faculty->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save Adviser</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(session('success'))
<div id="section-flash-data" class="d-none" data-success="{{ session('success') }}"></div>
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const flashNode = document.getElementById('section-flash-data');
        const successMessage = flashNode ? flashNode.dataset.success : '';
        if (successMessage && window.AppSwal && typeof window.AppSwal.showSuccess === 'function') {
            window.AppSwal.showSuccess(successMessage);
        }

        document.querySelectorAll('.js-section-row').forEach(function(row) {
            row.addEventListener('dblclick', function(event) {
                if (event.target.closest('a, button, form, input, select, textarea, label')) {
                    return;
                }

                const viewUrl = row.getAttribute('data-view-url');
                if (viewUrl) {
                    window.location.href = viewUrl;
                }
            });
        });
    });
</script>
@endpush

@push('styles')
<style>
    .js-section-row {
        cursor: pointer;
    }

    .js-section-row:hover {
        background-color: rgba(255, 255, 255, 0.08);
    }
</style>
@endpush

@endsection