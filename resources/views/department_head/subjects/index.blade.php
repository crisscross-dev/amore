@extends('layouts.app')

@section('title', 'Subject Management - Department Head Dashboard - Amore Academy')

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
                                <i class="fas fa-book-open me-2"></i>
                                Department Subject Catalog
                            </h4>
                            <p class="mb-0 opacity-90">
                                Curate and update your department's subjects to keep offerings aligned with program goals.
                            </p>
                        </div>
                        <div class="col-lg-5">
                            <div class="quick-link-card">
                                <span class="text-uppercase small fw-semibold text-white-50">Quick actions</span>
                                <a href="{{ route($routeBase . 'create') }}">
                                    <i class="fas fa-plus-circle"></i>
                                    Create New Subject
                                </a>
                                <a href="{{ route('admin.grade-approvals.index') }}">
                                    <i class="fas fa-graduation-cap"></i>
                                    Review Grade Submissions
                                </a>
                            </div>
                        </div>
                    </div>
                </div>


                @if($errors->any())
                <x-ui.alert type="danger" :dismissible="true">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-ui.alert>
                @endif

                @if(!empty($importSummary))
                <div class="faculty-management-card mb-4">
                    <h5 class="mb-3">
                        <i class="fas fa-file-import me-2 text-success"></i>
                        Last Import Summary
                    </h5>
                    <div class="row g-3">
                        <div class="col-sm-3">
                            <div class="fw-semibold">Processed</div>
                            <div>{{ $importSummary['processed'] }}</div>
                        </div>
                        <div class="col-sm-3">
                            <div class="fw-semibold text-success">Created</div>
                            <div>{{ $importSummary['created'] }}</div>
                        </div>
                        <div class="col-sm-3">
                            <div class="fw-semibold text-primary">Updated</div>
                            <div>{{ $importSummary['updated'] }}</div>
                        </div>
                        <div class="col-sm-3">
                            <div class="fw-semibold text-danger">Errors</div>
                            <div>{{ count($importSummary['errors']) }}</div>
                        </div>
                    </div>
                    @if(!empty($importSummary['errors']))
                    <div class="alert alert-warning mt-3 mb-0">
                        <strong>Import warnings:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($importSummary['errors'] as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
                @endif

                <div class="faculty-management-card mb-4">
                    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-lg-between gap-3 mb-4">
                        <div>
                            <h5 class="mb-1">
                                <i class="fas fa-filter me-2 text-success"></i>
                                Refine Subject List
                            </h5>
                            <p class="mb-0 text-muted">Search and filter to focus on the subjects you need to manage.</p>
                        </div>
                        <!-- <form action="{{ route($routeBase . 'import') }}" method="POST" enctype="multipart/form-data" class="d-flex flex-wrap gap-2">
                            @csrf
                            <label class="btn btn-outline-success mb-0">
                                <i class="fas fa-upload me-2"></i>Import CSV
                                <input type="file" name="import_file" accept=".csv,.txt" class="d-none" onchange="this.form.submit()">
                            </label>
                            <a href="{{ route($routeBase . 'index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-rotate-left me-2"></i>Reset Filters
                            </a>
                        </form> -->
                    </div>

                    <form method="GET" action="{{ route($routeBase . 'index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="form-control" placeholder="Name or description">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Subject Type</label>
                            <select name="subject_type" class="form-select">
                                <option value="">All types</option>
                                @foreach($subjectTypes as $value => $label)
                                <option value="{{ $value }}" {{ ($filters['subject_type'] ?? '') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Grade Level</label>
                            <select name="grade_level" class="form-select">
                                <option value="">All grade levels</option>
                                @foreach($filterGradeLevels as $value => $label)
                                <option value="{{ $value }}" {{ ($filters['grade_level'] ?? '') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-filter me-2"></i>Apply filters
                            </button>
                        </div>
                    </form>
                </div>

                <div class="faculty-management-card faculty-management-table">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="fas fa-table me-2 text-success"></i>
                            Subjects Overview
                        </h5>
                        <span class="badge bg-success bg-opacity-75">{{ $subjects->total() }} subject{{ $subjects->total() === 1 ? '' : 's' }}</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Grade</th>
                                    <!-- <th>Hours/Week</th> -->
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subjects as $subject)
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-success">{{ $subject->name }}</div>
                                        <small class="text-muted">{{ $subject->description ?: 'No description provided' }}</small>
                                    </td>
                                    <td class="text-capitalize">
                                        {{ $subject->subject_type ? ($subjectTypes[$subject->subject_type] ?? ucfirst($subject->subject_type)) : '—' }}
                                    </td>
                                    <td>
                                        @php
                                        $mappedLevels = $subject->gradeLevels->pluck('grade_level')->unique()->sort()->values();
                                        @endphp
                                        @if($mappedLevels->isEmpty())
                                        {{ $gradeLevels[$subject->grade_level] ?? strtoupper($subject->grade_level) }}
                                        @elseif($mappedLevels->count() === 6)
                                        All Levels
                                        @else
                                        Grade {{ $mappedLevels->implode(', ') }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route($routeBase . 'edit', $subject) }}" class="btn btn-outline-success">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <form action="{{ route($routeBase . 'destroy', $subject) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="faculty-management-empty">
                                            <i class="fas fa-book"></i>
                                            <h5 class="fw-semibold mb-2">No subjects available yet</h5>
                                            <p class="mb-0">Create your first subject to populate the catalog.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $subjects->links() }}
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection
