@extends('layouts.app')

@section('title', 'Faculty Position Assignments - Admin Dashboard - Amore Academy')

@section('content')
<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@vite([
'resources/css/layouts/dashboard-roles/dashboard-admin.css',
'resources/css/admin/faculty-management.css',
])

<div class="dashboard-container">
    <div class="container-fluid px-4">
        <div class="row">

            <main class="col-12">

                <div class="header-title d-flex align-items-center justify-content-between mb-2">
                    <h5 class="mb-2 fw-semibold text-success">
                        Faculty Assignment
                    </h5>
                    <!-- <div class="d-none d-lg-block">
                        <a href="{{ route('admin.school-years.create') }}" class="btn btn-primary btn-m">
                            <i class="fas fa-plus me-2"></i>Add School Year
                        </a>
                    </div> -->
                </div>
                <div class="faculty-management-actions mb-0">
                    <form method="GET" action="{{ route('admin.faculty-assignments.index') }}" class="faculty-inline-filters">

                        <select name="position_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All positions</option>
                            @foreach($positions as $position)
                            <option value="{{ $position->id }}" {{ request('position_id') == $position->id ? 'selected' : '' }}>
                                {{ $position->name }}
                            </option>
                            @endforeach
                        </select>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search name or email">
                    </form>

                </div>

                @if(session('success'))
                <x-ui.alert type="success" :dismissible="true">{{ session('success') }}</x-ui.alert>
                @endif

                <div class="faculty-management-card faculty-management-table">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="faculty-col-name">Name</th>
                                    <th>Email</th>
                                    <th>Position</th>
                                    <th class="faculty-col-department">Department</th>
                                    <!-- Removed Assigned and Assigned By columns -->
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($facultyMembers as $faculty)
                                <tr class="faculty-row-clickable"
                                    role="button"
                                    tabindex="0"
                                    data-user-id="{{ $faculty->id }}"
                                    data-user-name="{{ $faculty->first_name }} {{ $faculty->last_name }}"
                                    data-position-id="{{ $faculty->faculty_position_id ?? '' }}"
                                    data-department="{{ $faculty->department ?? '' }}"
                                    data-current-position="{{ $faculty->facultyPosition->name ?? 'Unassigned' }}"
                                    data-current-assigned="{{ $faculty->position_assigned_date ? $faculty->position_assigned_date->format('M d, Y') : '—' }}"
                                    data-current-assigned-by="{{ optional($faculty->positionAssignee)->first_name ? optional($faculty->positionAssignee)->first_name . ' ' . optional($faculty->positionAssignee)->last_name : '—' }}">
                                    <td class="faculty-col-name">
                                        <div class="fw-semibold text-success">{{ $faculty->first_name }} {{ $faculty->last_name }}</div>
                                        <small class="text-muted">{{ $faculty->custom_id }}</small>
                                    </td>
                                    <td>{{ $faculty->email }}</td>
                                    <td>
                                        @if($faculty->facultyPosition)
                                        <span class="badge bg-success">{{ $faculty->facultyPosition->name }}</span>
                                        @else
                                        <span class="badge bg-secondary">Unassigned</span>
                                        @endif
                                    </td>
                                    <td class="faculty-col-department">{{ $faculty->department ?? '—' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="faculty-management-empty">
                                            <i class="fas fa-search"></i>
                                            <h5 class="fw-semibold mb-2">No faculty members found</h5>
                                            <p class="mb-0">Adjust your filters or ensure faculty accounts are registered.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 pagination-wrapper faculty-assignments-pagination">
                        {{ $facultyMembers->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

@php
$updateRouteTemplate = route('admin.faculty-assignments.update', ['user' => '__USER__']);
@endphp

<div class="modal fade" id="facultyAssignmentModal" tabindex="-1" aria-labelledby="facultyAssignmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="facultyAssignmentModalLabel">Update Faculty Assignment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="facultyAssignmentForm" method="POST" data-route-template="{{ $updateRouteTemplate }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <p class="mb-3">
                        Updating: <strong id="modalFacultyName">Faculty Member</strong>
                    </p>

                    <div class="mb-3">
                        <label for="modalFacultyPosition" class="form-label">Select Position</label>
                        <select id="modalFacultyPosition" name="faculty_position_id" class="form-select">
                            <option value="">No position assigned</option>
                            @foreach($positions as $position)
                            <option value="{{ $position->id }}">{{ $position->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="modalFacultyDepartment" class="form-label">Department</label>
                        <select id="modalFacultyDepartment" name="department" class="form-select">
                            <option value="">No department</option>
                            @foreach($departmentOptions as $department)
                            <option value="{{ $department }}">{{ $department }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="assignment-modal-current small text-muted">
                        <div>Current position: <strong id="modalCurrentPosition">Unassigned</strong></div>
                        <div>Assigned on: <strong id="modalCurrentAssigned">—</strong></div>
                        <div>Assigned by: <strong id="modalCurrentAssignedBy">—</strong></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('facultyAssignmentModal');
        const modalInstance = bootstrap.Modal.getOrCreateInstance(modal);
        const form = document.getElementById('facultyAssignmentForm');
        const nameEl = document.getElementById('modalFacultyName');
        const positionEl = document.getElementById('modalFacultyPosition');
        const departmentEl = document.getElementById('modalFacultyDepartment');
        const currentPositionEl = document.getElementById('modalCurrentPosition');
        const currentAssignedEl = document.getElementById('modalCurrentAssigned');
        const currentAssignedByEl = document.getElementById('modalCurrentAssignedBy');
        const updateRouteTemplate = form.getAttribute('data-route-template') || '';

        const populateModalFromRow = function(row) {
            const userId = row.getAttribute('data-user-id') || '';
            const userName = row.getAttribute('data-user-name') || 'Faculty Member';
            const positionId = row.getAttribute('data-position-id') || '';
            const department = row.getAttribute('data-department') || '';
            const currentPosition = row.getAttribute('data-current-position') || 'Unassigned';
            const currentAssigned = row.getAttribute('data-current-assigned') || '—';
            const currentAssignedBy = row.getAttribute('data-current-assigned-by') || '—';

            form.action = updateRouteTemplate.replace('__USER__', userId);
            nameEl.textContent = userName;
            positionEl.value = positionId;
            departmentEl.value = department;
            currentPositionEl.textContent = currentPosition;
            currentAssignedEl.textContent = currentAssigned;
            currentAssignedByEl.textContent = currentAssignedBy;
        };

        document.querySelectorAll('.faculty-row-clickable').forEach(function(row) {
            row.addEventListener('dblclick', function() {
                populateModalFromRow(row);
                modalInstance.show();
            });

            row.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    populateModalFromRow(row);
                    modalInstance.show();
                }
            });
        });
    });
</script>
@endpush
@endsection