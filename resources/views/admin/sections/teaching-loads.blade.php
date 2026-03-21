@extends('layouts.app')

@section('title', 'Teaching Load Management - Admin Dashboard - Amore Academy')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@vite(['resources/css/layouts/dashboard-roles/dashboard-admin.css', 'resources/js/admin-sections.js'])

<div class="dashboard-container">
    <div class="container-fluid px-4">
        <div class="row">
            <main class="col-12">
                <div class="welcome-card mb-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <h4 class="mb-2">
                                <i class="fas fa-chalkboard-teacher me-2"></i>
                                Teaching Load Management
                            </h4>
                            <p class="mb-0 opacity-90">Manage faculty to section-subject teaching assignments.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.sections.index') }}" class="btn btn-outline-light">
                                <i class="fas fa-layer-group me-2"></i>Section Adviser Management
                            </a>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#teachingLoadModal">
                                <i class="fas fa-plus me-2"></i>Assign Teaching Load
                            </button>
                        </div>
                    </div>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
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

                <div class="admissions-card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.teaching-loads.index') }}" class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Search (Faculty or Section)</label>
                                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Type faculty or section name">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Filter by Grade Level</label>
                                <select name="grade_level" class="form-select">
                                    <option value="">All Grade Levels</option>
                                    @foreach($gradeLevels as $gradeLevel)
                                    <option value="{{ $gradeLevel }}" {{ request('grade_level') == $gradeLevel ? 'selected' : '' }}>Grade {{ $gradeLevel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Filter by Subject</label>
                                <select name="subject_id" class="form-select">
                                    <option value="">All Subjects</option>
                                    @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ (string) request('subject_id') === (string) $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 d-flex gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-search me-1"></i>Search / Filter
                                </button>
                                <a href="{{ route('admin.teaching-loads.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-rotate-left me-1"></i>Reset
                                </a>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Faculty Name</th>
                                        <th>Section</th>
                                        <th>Subject</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($teachingLoads as $load)
                                    <tr>
                                        <td>{{ optional($load->teacher)->first_name }} {{ optional($load->teacher)->last_name }}</td>
                                        <td>{{ optional($load->section)->name }}</td>
                                        <td>{{ optional($load->subject)->name }}</td>
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-sm btn-warning me-1 js-open-edit-load-modal"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editTeachingLoadModal"
                                                data-update-url="{{ route('admin.teaching-loads.update', $load) }}"
                                                data-teacher-id="{{ $load->teacher_id }}"
                                                data-section-id="{{ $load->section_id }}"
                                                data-subject-id="{{ $load->subject_id }}"
                                                title="Edit Teaching Load">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <form action="{{ route('admin.teaching-loads.destroy', $load) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this teaching load?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete Teaching Load">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i>
                                            No teaching loads found.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($teachingLoads->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $teachingLoads->links() }}
                        </div>
                        @endif
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<div class="modal fade" id="teachingLoadModal" tabindex="-1" aria-labelledby="teachingLoadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.teaching-loads.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="teachingLoadModalLabel">Assign Teaching Load</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Faculty</label>
                        <select name="teacher_id" class="form-select" required>
                            <option value="">Choose faculty</option>
                            @foreach($facultyMembers as $faculty)
                            <option value="{{ $faculty->id }}">{{ $faculty->first_name }} {{ $faculty->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select Section</label>
                        <select name="section_id" class="form-select" required>
                            <option value="">Choose section</option>
                            @foreach($sectionOptions as $section)
                            <option value="{{ $section->id }}">{{ $section->name }} (Grade {{ $section->grade_level }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Select Subject</label>
                        <select name="subject_id" class="form-select" required>
                            <option value="">Choose subject</option>
                            @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save Teaching Load</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editTeachingLoadModal" tabindex="-1" aria-labelledby="editTeachingLoadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="editTeachingLoadForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editTeachingLoadModalLabel">Edit Teaching Load</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Faculty</label>
                        <select name="teacher_id" id="editTeacherId" class="form-select" required>
                            <option value="">Choose faculty</option>
                            @foreach($facultyMembers as $faculty)
                            <option value="{{ $faculty->id }}">{{ $faculty->first_name }} {{ $faculty->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select Section</label>
                        <select name="section_id" id="editSectionId" class="form-select" required>
                            <option value="">Choose section</option>
                            @foreach($sectionOptions as $section)
                            <option value="{{ $section->id }}">{{ $section->name }} (Grade {{ $section->grade_level }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Select Subject</label>
                        <select name="subject_id" id="editSubjectId" class="form-select" required>
                            <option value="">Choose subject</option>
                            @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update Teaching Load</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection