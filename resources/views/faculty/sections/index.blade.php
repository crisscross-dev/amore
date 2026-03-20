@extends('layouts.app')

@section('title', 'View Sections - Faculty Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@vite(['resources/css/layouts/dashboard-roles/dashboard-faculty.css'])

<div class="dashboard-container">
    <div class="container-fluid px-4">
        <div class="row">
            @include('partials.faculty-sidebar')

            <main class="col-lg-9 col-md-8">
                <!-- Page Header -->
                <div class="welcome-card mb-4">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-8">
                            <h4 class="mb-2">
                                <i class="fas fa-layer-group me-2"></i>
                                View Sections
                            </h4>
                            <p class="mb-0 opacity-90">Browse all available sections by grade level.</p>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card mb-4 p-3">
                    <form method="GET" action="{{ route('faculty.sections.index') }}" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label text-success">Search</label>
                            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Section name...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-success">Grade Level</label>
                            <select name="grade_level" class="form-select">
                                <option value="">All Levels</option>
                                @foreach($gradeLevels as $level)
                                    <option value="{{ $level }}" {{ request('grade_level') === $level ? 'selected' : '' }}>{{ $level }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-success">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end gap-2">
                            <button class="btn btn-green w-100">
                                <i class="fas fa-filter me-1"></i>Filter
                            </button>
                        </div>
                        @if(request()->hasAny(['search', 'grade_level', 'status']))
                            <div class="col-12">
                                <a href="{{ route('faculty.sections.index') }}" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-times me-1"></i>Clear Filters
                                </a>
                            </div>
                        @endif
                    </form>
                </div>

                <!-- Sections Table -->
                <div class="faculty-management-card faculty-management-table">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 text-success">
                            <i class="fas fa-table me-2 text-success"></i>
                            Sections Overview
                        </h5>
                        <span class="badge bg-success bg-opacity-75">{{ $sections->total() }} section{{ $sections->total() === 1 ? '' : 's' }}</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Section Name</th>
                                    <th>Grade Level</th>
                                    <th>Adviser</th>
                                    <th>Students</th>
                                    <th>Academic Year</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sections as $section)
                                    <tr>
                                        <td>
                                            <a href="{{ route('faculty.sections.show', $section) }}" class="text-success fw-semibold text-decoration-none">
                                                {{ $section->name }}
                                            </a>
                                            @if($section->description)
                                                <br><small class="text-muted">{{ $section->description }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $section->grade_level }}</span>
                                        </td>
                                        <td>
                                            @if($section->adviser)
                                                <div class="d-flex align-items-center gap-2">
                                                    @if($section->adviser->profile_picture)
                                                        <img src="{{ asset('uploads/profile_picture/' . $section->adviser->profile_picture) }}"
                                                             class="rounded-circle" width="28" height="28" style="object-fit: cover;">
                                                    @else
                                                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center"
                                                             style="width: 28px; height: 28px; font-size: 12px; flex-shrink: 0;">
                                                            {{ strtoupper(substr($section->adviser->first_name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <span>{{ $section->adviser->first_name }} {{ $section->adviser->last_name }}</span>
                                                </div>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ $section->students->count() }}
                                                @if($section->capacity)
                                                    / {{ $section->capacity }}
                                                @endif
                                            </span>
                                        </td>
                                        <td>{{ $section->academic_year ?? '—' }}</td>
                                        <td>
                                            @if($section->is_active)
                                                <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Active</span>
                                            @else
                                                <span class="badge bg-secondary"><i class="fas fa-times-circle me-1"></i>Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <div class="faculty-management-empty text-success">
                                                <i class="fas fa-layer-group text-success"></i>
                                                <h5 class="fw-semibold mb-2 text-success">No sections found</h5>
                                                <p class="mb-0 text-success">Try adjusting your filters to find more results.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $sections->links() }}
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

@endsection
