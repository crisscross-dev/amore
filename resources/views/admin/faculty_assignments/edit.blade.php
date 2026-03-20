@extends('layouts.app')

@section('title', 'Assign Faculty Position - Admin Dashboard - Amore Academy')

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
                <div class="welcome-card">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-7">
                            <h4 class="mb-2">
                                <i class="fas fa-user-tag me-2"></i>
                                Assign Position
                            </h4>
                            <p class="mb-0 opacity-90">
                                Update the assignment for {{ $user->first_name }} {{ $user->last_name }}.
                            </p>
                        </div>
                        <div class="col-lg-5">
                            <div class="quick-link-card">
                                <span class="text-uppercase small fw-semibold text-white-50">Quick actions</span>
                                <a href="{{ route('admin.faculty-assignments.index') }}">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                    View Faculty List
                                </a>
                                <a href="{{ route('admin.faculty-positions.index') }}">
                                    <i class="fas fa-layer-group"></i>
                                    Manage Positions
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faculty-management-card">
                    <a href="{{ route('admin.faculty-assignments.index') }}" class="back-link mb-3">
                        <i class="fas fa-arrow-left"></i>
                        Back to faculty overview
                    </a>

                    <form action="{{ route('admin.faculty-assignments.update', $user) }}" method="POST" class="row g-4">
                        @csrf
                        @method('PUT')

                        <div class="col-md-6">
                            <label class="form-label">Select Position</label>
                            <select name="faculty_position_id" class="form-select @error('faculty_position_id') is-invalid @enderror">
                                <option value="">No position assigned</option>
                                @foreach($positions as $position)
                                    <option value="{{ $position->id }}" {{ old('faculty_position_id', $user->faculty_position_id) == $position->id ? 'selected' : '' }}>
                                        {{ $position->name }} ({{ ucfirst($position->category) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('faculty_position_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <select name="department" class="form-select @error('department') is-invalid @enderror">
                                <option value="">No department</option>
                                @foreach($departmentOptions as $department)
                                    <option value="{{ $department }}" {{ old('department', $user->department) === $department ? 'selected' : '' }}>
                                        {{ $department }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <div class="assignment-summary">
                                <h6 class="mb-2">Current Assignment</h6>
                                <p class="mb-1">Position: <strong>{{ $user->facultyPosition->name ?? 'Unassigned' }}</strong></p>
                                <p class="mb-1">Department: <strong>{{ $user->department ?? '—' }}</strong></p>
                                <p class="mb-0">Assigned On: <strong>{{ $user->position_assigned_date ? $user->position_assigned_date->format('M d, Y') : '—' }}</strong></p>
                            </div>
                        </div>

                        <div class="col-12 d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.faculty-assignments.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection

