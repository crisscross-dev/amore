@extends('layouts.app')

@section('title', 'Section Details - Admin Dashboard - Amore Academy')

@section('content')

@vite(['resources/css/layouts/dashboard-roles/dashboard-admin.css', 'resources/js/admin-sections.js'])

<div class="dashboard-container" data-section-id="{{ $section->id }}">
    <div class="container-fluid px-4">
        <div class="row">
            <main class="col-12">
                <div class="welcome-card mb-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h4 class="mb-2">
                                <i class="fas fa-layer-group me-2"></i>{{ $section->name }}
                                <span class="badge bg-info ms-2">Grade {{ $section->grade_level }}</span>
                                @if($section->is_active)
                                    <span class="badge bg-success ms-1">Active</span>
                                @else
                                    <span class="badge bg-secondary ms-1">Inactive</span>
                                @endif
                            </h4>
                            <p class="mb-0 opacity-90">
                                @if($section->academic_year)
                                    <i class="fas fa-calendar me-1"></i>{{ $section->academic_year }}
                                @endif
                                @if($section->capacity)
                                    <i class="fas fa-users ms-3 me-1"></i>{{ $section->students->count() }} / {{ $section->capacity }} students
                                @else
                                    <i class="fas fa-users ms-3 me-1"></i>{{ $section->students->count() }} students
                                @endif
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('admin.sections.edit', $section) }}" class="btn btn-warning me-2">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <a href="{{ route('admin.sections.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back
                            </a>
                        </div>
                    </div>
                    @if($section->description)
                        <div class="mt-3">
                            <p class="mb-0">{{ $section->description }}</p>
                        </div>
                    @endif
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->has('schedule'))
                    <div class="alert alert-danger alert-dismissible fade show section-schedule-error">
                        <i class="fas fa-exclamation-triangle me-2"></i>{{ $errors->first('schedule') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <div class="d-none" id="scheduleConflictMessage" data-message="{{ $errors->first('schedule') }}"></div>
                @endif

                @if($errors->has('room'))
                    <div class="alert alert-warning alert-dismissible fade show">
                        <i class="fas fa-door-closed me-2"></i>{{ $errors->first('room') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="admissions-card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-user-check me-2"></i>Section Adviser</h5>
                        @if($section->adviser)
                            <span class="badge bg-success">Assigned</span>
                        @else
                            <span class="badge bg-warning text-dark">Unassigned</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.sections.assign-adviser', $section) }}" method="POST" class="row g-3 align-items-end">
                            @csrf
                            <div class="col-md-8">
                                <label class="form-label">Adviser</label>
                                <select name="adviser_id" class="form-select">
                                    <option value="">No adviser</option>
                                    @foreach($facultyMembers as $faculty)
                                        <option value="{{ $faculty->id }}" {{ optional($section->adviser)->id === $faculty->id ? 'selected' : '' }} title="{{ $faculty->first_name }} {{ $faculty->last_name }}">
                                            {{ $faculty->first_name }} {{ $faculty->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-save me-2"></i>Save Adviser
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="admissions-card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-book me-2"></i>Subject Teacher Assignments</h5>
                        <span class="badge bg-info">{{ $subjects->count() }} subjects</span>
                    </div>
                    <div class="card-body">
                        @if($errors->has('teacher_ids'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first('teacher_ids') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        @if($subjects->isEmpty())
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-book-open fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">No subjects available for this grade level yet.</p>
                            </div>
                        @else
                            @php
                                $dayOptions = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                $timeOptions = [];
                                for ($hour = 7; $hour <= 17; $hour++) {
                                    foreach ([0, 30] as $minute) {
                                        if ($hour === 17 && $minute === 30) {
                                            continue;
                                        }
                                        $timeOptions[] = sprintf('%02d:%02d', $hour, $minute);
                                    }
                                }
                                $roomOptions = ['201', '202', '203', '204', '205'];
                            @endphp
                            <form action="{{ route('admin.sections.assign-subject-teachers', $section) }}" method="POST">
                                @csrf
                                <div class="d-flex justify-content-end mb-3">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-1"></i>Save All
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th style="width: 20%;">Subject</th>
                                                <th style="width: 25%;">Assigned Teacher</th>
                                                <th style="width: 12%;">Day</th>
                                                <th style="width: 10%;">Start</th>
                                                <th style="width: 10%;">End</th>
                                                <th style="width: 10%;">Room</th>
                                                <th class="text-center" style="width: 13%;">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($subjects as $subject)
                                                @php
                                                    $assignment = $subjectAssignments->get($subject->id);
                                                    $normalizedStart = optional($assignment)->start_time;
                                                    if ($normalizedStart && strlen($normalizedStart) > 5) {
                                                        $normalizedStart = substr($normalizedStart, 0, 5);
                                                    }
                                                    $normalizedEnd = optional($assignment)->end_time;
                                                    if ($normalizedEnd && strlen($normalizedEnd) > 5) {
                                                        $normalizedEnd = substr($normalizedEnd, 0, 5);
                                                    }
                                                    $startSelected = old('start_times.' . $subject->id, $normalizedStart);
                                                    $endSelected = old('end_times.' . $subject->id, $normalizedEnd);
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div class="fw-semibold">{{ $subject->name }}</div>
                                                        <small class="text-muted">{{ $subject->description ?: 'No description provided' }}</small>
                                                    </td>
                                                    <td>
                                                        <select name="teacher_ids[{{ $subject->id }}]" class="form-select" style="min-width: 200px;">
                                                            <option value="">No teacher</option>
                                                            @foreach($facultyMembers as $faculty)
                                                                <option value="{{ $faculty->id }}" {{ optional($assignment)->teacher_id === $faculty->id ? 'selected' : '' }} title="{{ $faculty->first_name }} {{ $faculty->last_name }}">
                                                                    {{ $faculty->first_name }} {{ $faculty->last_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="days[{{ $subject->id }}]" class="form-select">
                                                            <option value="">Select day</option>
                                                            @foreach($dayOptions as $day)
                                                                <option value="{{ $day }}" {{ old('days.' . $subject->id, optional($assignment)->day_of_week) === $day ? 'selected' : '' }}>
                                                                    {{ $day }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="start_times[{{ $subject->id }}]" class="form-select">
                                                            <option value="">Start time</option>
                                                            @foreach($timeOptions as $time)
                                                                <option value="{{ $time }}" {{ $startSelected === $time ? 'selected' : '' }}>
                                                                    {{ $time }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="end_times[{{ $subject->id }}]" class="form-select">
                                                            <option value="">End time</option>
                                                            @foreach($timeOptions as $time)
                                                                <option value="{{ $time }}" {{ $endSelected === $time ? 'selected' : '' }}>
                                                                    {{ $time }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $currentRoom = old('rooms.' . $subject->id, optional($assignment)->room);
                                                        @endphp
                                                        <select name="rooms[{{ $subject->id }}]" class="form-select">
                                                            <option value="">Select room</option>
                                                            @foreach($roomOptions as $room)
                                                                <option value="{{ $room }}" {{ $currentRoom == $room ? 'selected' : '' }}>
                                                                    {{ $room }}
                                                                </option>
                                                            @endforeach
                                                            @if($currentRoom && !in_array($currentRoom, $roomOptions))
                                                                <option value="{{ $currentRoom }}" selected>{{ $currentRoom }}</option>
                                                            @endif
                                                        </select>
                                                    </td>
                                                    <td class="text-center">
                                                        @if(optional($assignment)->teacher)
                                                            <span class="badge bg-success">Assigned</span>
                                                        @else
                                                            <span class="badge bg-warning text-dark">Unassigned</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Students in this section -->
                <div class="admissions-card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Students in Section</h5>
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                            <i class="fas fa-user-plus me-1"></i>Add Students
                        </button>
                    </div>
                    <div class="card-body">
                        @if($section->students->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>Student ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>LRN</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($section->students as $student)
                                            <tr>
                                                <td><strong>{{ $student->custom_id }}</strong></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($student->profile_picture)
                                                            <img src="{{ asset('uploads/profile_picture/' . $student->profile_picture) }}" 
                                                                 class="rounded-circle me-2" width="32" height="32" style="object-fit: cover;">
                                                        @else
                                                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-2" 
                                                                 style="width: 32px; height: 32px; font-size: 14px;">
                                                                {{ strtoupper(substr($student->first_name, 0, 1)) }}
                                                            </div>
                                                        @endif
                                                        <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                                                    </div>
                                                </td>
                                                <td>{{ $student->email }}</td>
                                                <td>{{ $student->lrn ?? '—' }}</td>
                                                <td class="text-center">
                                                    <form action="{{ route('admin.students.assign-section', $student) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="section_id" value="">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                onclick="return confirm('Remove this student from the section?')"
                                                                title="Remove from section">
                                                            <i class="fas fa-times"></i> Remove
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-users fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">No students assigned to this section yet.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Add students to section -->
                @if($availableStudents->count() > 0)
                    <div class="admissions-card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Add Students to Section</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-3">
                                <i class="fas fa-info-circle me-1"></i>
                                Showing students in Grade {{ $section->grade_level }} without a section assignment.
                            </p>
                            
                            <form action="{{ route('admin.students.bulk-assign-section') }}" method="POST" id="bulkAssignForm">
                                @csrf
                                <input type="hidden" name="section_id" value="{{ $section->id }}">
                                
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                        <label class="form-check-label" for="selectAll">
                                            <strong>Select All</strong>
                                        </label>
                                    </div>
                                    <button type="submit" class="btn btn-success" id="bulkAddBtn" disabled>
                                        <i class="fas fa-user-plus me-1"></i>Add Selected (<span id="selectedCount">0</span>)
                                    </button>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th width="90">Select</th>
                                                <th>Student ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>LRN</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($availableStudents as $student)
                                                <tr>
                                                    <td>
                                                        <div class="form-check d-flex align-items-center gap-2">
                                                            <input class="form-check-input student-checkbox"
                                                                   type="checkbox"
                                                                   name="student_ids[]"
                                                                   value="{{ $student->id }}"
                                                                   id="student_{{ $student->id }}">
                                                            <label class="form-check-label" for="student_{{ $student->id }}">
                                                                Select
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td><strong>{{ $student->custom_id }}</strong></td>
                                                    <td>
                                                        <label for="student_{{ $student->id }}" class="d-flex align-items-center mb-0" style="cursor: pointer;">
                                                            @if($student->profile_picture)
                                                                <img src="{{ asset('uploads/profile_picture/' . $student->profile_picture) }}" 
                                                                     class="rounded-circle me-2" width="32" height="32" style="object-fit: cover;">
                                                            @else
                                                                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-2" 
                                                                     style="width: 32px; height: 32px; font-size: 14px;">
                                                                    {{ strtoupper(substr($student->first_name, 0, 1)) }}
                                                                </div>
                                                            @endif
                                                            {{ $student->first_name }} {{ $student->last_name }}
                                                        </label>
                                                    </td>
                                                    <td>{{ $student->email }}</td>
                                                    <td>{{ $student->lrn ?? '—' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        All students in Grade {{ $section->grade_level }} have been assigned to sections.
                    </div>
                @endif
            </main>
        </div>
    </div>
</div>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStudentModalLabel" style="color: #198754;">
                    <i class="fas fa-user-plus me-2"></i>Add Students to {{ $section->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="studentSearch" class="form-label">Filter Students (Grade {{ $section->grade_level }})</label>
                    <div class="position-relative">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-filter"></i></span>
                            <input type="text" 
                                   class="form-control" 
                                   id="studentSearch" 
                                   placeholder="Type to filter by name or ID..."
                                   autocomplete="off">
                            <button class="btn btn-outline-secondary" type="button" id="clearFilterBtn">
                                <i class="fas fa-times me-1"></i>Clear
                            </button>
                        </div>
                        <div id="searchSuggestions" class="position-absolute w-100 bg-white border rounded shadow-sm mt-1" 
                             style="display: block; max-height: 400px; overflow-y: auto; z-index: 1050;">
                            <!-- All students will be displayed here by default -->
                            <div class="text-center py-3">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <small class="text-muted">
                        @if($availableStudents->count() > 0)
                            {{ $availableStudents->count() }} student{{ $availableStudents->count() > 1 ? 's' : '' }} available
                        @else
                            No students available - all assigned to sections
                        @endif
                    </small>
                </div>

                <div id="searchResults" class="mt-3">
                    @if($availableStudents->count() == 0)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            All students in Grade {{ $section->grade_level }} have been assigned to sections.
                        </div>
                    @else
                        <div id="noResults" class="text-center text-muted py-4" style="display: none;">
                            <i class="fas fa-search fa-2x mb-2 opacity-25"></i>
                            <p class="mb-0">No students found</p>
                        </div>
                        
                        <div id="studentList">
                        @foreach($availableStudents as $student)
                            <div class="card mb-2 student-item" 
                                 data-student-id="{{ $student->id }}"
                                 data-student-name="{{ strtolower($student->first_name . ' ' . $student->last_name) }}"
                                 data-student-custom-id="{{ strtolower($student->custom_id) }}"
                                 data-student-display-name="{{ $student->first_name }} {{ $student->last_name }}"
                                 data-student-email="{{ $student->email }}"
                                 data-student-lrn="{{ $student->lrn ?? '' }}"
                                 data-student-picture="{{ $student->profile_picture ?? '' }}"
                                 style="display: none;">
                                <div class="card-body py-2 d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        @if($student->profile_picture)
                                            <img src="{{ asset('uploads/profile_picture/' . $student->profile_picture) }}" 
                                                 class="rounded-circle me-3" width="40" height="40" style="object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-3" 
                                                 style="width: 40px; height: 40px; font-size: 16px;">
                                                {{ strtoupper(substr($student->first_name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $student->custom_id }} | {{ $student->email }}</small>
                                        </div>
                                    </div>
                                    <button type="button" 
                                            class="btn btn-sm btn-success add-student-btn"
                                            data-student-id="{{ $student->id }}"
                                            data-student-name="{{ $student->first_name }} {{ $student->last_name }}">
                                        <i class="fas fa-plus me-1"></i>Add
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                <div id="selectedStudents" class="mt-4" style="display: none;">
                    <h6 class="border-bottom pb-2">
                        <i class="fas fa-check-circle me-2 text-success"></i>
                        Selected Students (<span id="selectedStudentCount">0</span>)
                    </h6>
                    <div id="selectedStudentsList" class="mb-3"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmAddStudents" disabled>
                    <i class="fas fa-check me-1"></i>Add Selected Students
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

