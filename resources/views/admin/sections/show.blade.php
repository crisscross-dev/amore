@extends('layouts.app')

@section('title', 'Section Details - Admin Dashboard - Amore Academy')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@vite(['resources/css/layouts/dashboard-roles/dashboard-admin.css', 'resources/js/admin-sections.js'])

@push('styles')
<style>
    .js-assignment-readonly {
        height: calc(2.25rem + 2px);
        padding: 0.375rem 0.75rem;
        line-height: 1.5;
        cursor: default;
    }

    .add-students-modal-dialog .modal-content {
        height: 82vh;
        max-height: 82vh;
    }

    .add-students-modal-dialog .modal-body {
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .add-students-modal-dialog .student-suggestion-panel {
        flex: 1 1 auto;
        min-height: 220px;
        max-height: 420px;
        overflow-y: auto;
    }

    .add-students-modal-dialog .compact-control {
        min-height: 36px;
        padding-top: 0.35rem;
        padding-bottom: 0.35rem;
    }
</style>
@endpush

<div class="dashboard-container" data-section-id="{{ $section->id }}">
    <div class="container-fluid px-4">
        <div class="row">
            <main class="col-12">

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
                            if ($hour===17 && $minute===30) {
                            continue;
                            }
                            $timeOptions[]=sprintf('%02d:%02d', $hour, $minute);
                            }
                            }
                            $roomOptions=['201', '202' , '203' , '204' , '205' ];

                            $gradeNumber=null;
                            if (preg_match('/(7|8|9|10|11|12)/', (string) $section->grade_level, $gradeMatch)) {
                            $gradeNumber = (int) $gradeMatch[1];
                            }

                            $mapehComponentNamesByGrade = [
                            7 => ['MAPEH - Music & Arts', 'MAPEH - PE & Health', 'Music & Arts', 'PE & Health'],
                            8 => ['MAPEH - Music & Arts', 'MAPEH - PE & Health', 'Music & Arts', 'PE & Health'],
                            9 => ['MAPEH - Music', 'MAPEH - Arts', 'MAPEH - PE', 'MAPEH - Health', 'Music', 'Arts', 'PE', 'Health'],
                            10 => ['MAPEH - Music', 'MAPEH - Arts', 'MAPEH - PE', 'MAPEH - Health', 'Music', 'Arts', 'PE', 'Health'],
                            ];

                            $mapehDisplayPartsByGrade = [
                            7 => ['Music & Arts', 'PE & Health'],
                            8 => ['Music & Arts', 'PE & Health'],
                            9 => ['Music', 'Arts', 'PE', 'Health'],
                            10 => ['Music', 'Arts', 'PE', 'Health'],
                            ];

                            $mapehComponentNames = $mapehComponentNamesByGrade[$gradeNumber] ?? [];
                            $groupedSubjects = collect();
                            $mapehGrouped = false;

                            if (!empty($mapehComponentNames)) {
                            $mapehSubjects = $subjects->filter(function ($subject) use ($mapehComponentNames) {
                            return in_array($subject->name, $mapehComponentNames, true);
                            })->values();

                            if ($mapehSubjects->isNotEmpty()) {
                            $componentIds = $mapehSubjects->pluck('id')->map(fn($id) => (int) $id)->all();
                            $assignmentSource = $mapehSubjects
                            ->map(fn($subject) => $subjectAssignments->get($subject->id))
                            ->filter()
                            ->first(function ($assignment) {
                            return (bool) (optional($assignment)->teacher_id || optional($assignment)->day_of_week || optional($assignment)->room);
                            });

                            if (! $assignmentSource) {
                            $assignmentSource = $subjectAssignments->get($mapehSubjects->first()->id);
                            }

                            $groupedSubjects->push((object) [
                            'id' => (int) $mapehSubjects->first()->id,
                            'name' => 'MAPEH',
                            'description' => 'Includes ' . implode(', ', $mapehDisplayPartsByGrade[$gradeNumber] ?? []),
                            'component_ids' => $componentIds,
                            'assignment' => $assignmentSource,
                            ]);

                            $mapehGrouped = true;
                            }
                            }

                            foreach ($subjects as $subject) {
                            if ($mapehGrouped && in_array($subject->name, $mapehComponentNames, true)) {
                            continue;
                            }

                            $groupedSubjects->push((object) [
                            'id' => (int) $subject->id,
                            'name' => $subject->name,
                            'description' => $subject->description,
                            'component_ids' => [(int) $subject->id],
                            'assignment' => $subjectAssignments->get($subject->id),
                            ]);
                            }

                            $hasAssignmentErrors = $errors->has('teacher_ids')
                            || $errors->has('adviser_id')
                            || $errors->has('schedule')
                            || $errors->has('room')
                            || $errors->has('days')
                            || $errors->has('start_times')
                            || $errors->has('end_times');
                            @endphp
                            <form
                                id="subjectTeacherAssignmentsForm"
                                action="{{ route('admin.sections.assign-subject-teachers', $section) }}"
                                method="POST"
                                data-has-errors="{{ $hasAssignmentErrors ? '1' : '0' }}">
                                @csrf
                                <div class="d-flex justify-content-end gap-2 mb-3">
                                    <button type="button" class="btn btn-outline-success" id="toggleAssignmentsEditBtn">
                                        <i class="fas fa-pen me-1"></i>Edit
                                    </button>
                                    <button type="submit" class="btn btn-success d-none" id="saveAssignmentsBtn">
                                        <i class="fas fa-save me-1"></i>Save All
                                    </button>
                                </div>
                                <div class="row g-3 align-items-end mb-3">
                                    <div class="col-md-6 col-lg-5">
                                        @php
                                        $selectedAdviserId = old('adviser_id', optional($section->adviser)->id);
                                        $selectedAdviser = $facultyMembers->firstWhere('id', $selectedAdviserId);
                                        $selectedAdviserName = $selectedAdviser
                                        ? trim($selectedAdviser->first_name . ' ' . $selectedAdviser->last_name)
                                        : 'No adviser';
                                        @endphp
                                        <label class="form-label">Section Adviser</label>
                                        <select name="adviser_id" class="form-select js-assignment-field js-assignment-select" disabled>
                                            <option value="">No adviser</option>
                                            @foreach($facultyMembers as $faculty)
                                            @php
                                            $assignedSection = $adviserAssignments->get($faculty->id);
                                            $assignedSectionId = (int) optional($assignedSection)->id;
                                            $isAssignedElsewhere = $assignedSectionId > 0 && $assignedSectionId !== (int) $section->id;
                                            $assignedLabel = $assignedSection
                                            ? ('(' . trim((string) $assignedSection->grade_level) . ' ' . trim((string) $assignedSection->name) . ')')
                                            : '';
                                            @endphp
                                            <option
                                                value="{{ $faculty->id }}"
                                                {{ (string) old('adviser_id', optional($section->adviser)->id) === (string) $faculty->id ? 'selected' : '' }}
                                                title="{{ $faculty->first_name }} {{ $faculty->last_name }}"
                                                {{ $isAssignedElsewhere ? 'disabled style=color:#c26b08;font-weight:600;' : '' }}>
                                                {{ $faculty->first_name }} {{ $faculty->last_name }}
                                                {{ $isAssignedElsewhere ? (' ' . $assignedLabel) : '' }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <input type="text" class="form-control js-assignment-readonly" value="{{ $selectedAdviserName }}" readonly>
                                        @if($errors->has('adviser_id'))
                                        <div class="text-danger small mt-1">{{ $errors->first('adviser_id') }}</div>
                                        @endif
                                    </div>
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
                                            @foreach($groupedSubjects as $subject)
                                            @php
                                            $assignment = $subject->assignment;
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
                                                    @if(count($subject->component_ids) > 1)
                                                    @foreach($subject->component_ids as $componentId)
                                                    <input type="hidden" name="group_subject_ids[{{ $subject->id }}][]" value="{{ $componentId }}">
                                                    @endforeach
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                    $teacherDisplay = optional($assignment->teacher)
                                                    ? trim(optional($assignment->teacher)->first_name . ' ' . optional($assignment->teacher)->last_name)
                                                    : 'No teacher';
                                                    @endphp
                                                    <select name="teacher_ids[{{ $subject->id }}]" class="form-select js-assignment-field js-assignment-select" style="min-width: 200px;" disabled>
                                                        <option value="">No teacher</option>
                                                        @foreach($facultyMembers as $faculty)
                                                        <option value="{{ $faculty->id }}" {{ optional($assignment)->teacher_id === $faculty->id ? 'selected' : '' }} title="{{ $faculty->first_name }} {{ $faculty->last_name }}">
                                                            {{ $faculty->first_name }} {{ $faculty->last_name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="text" class="form-control js-assignment-readonly" style="min-width: 200px;" value="{{ $teacherDisplay }}" readonly>
                                                </td>
                                                <td>
                                                    @php
                                                    $dayDisplay = old('days.' . $subject->id, optional($assignment)->day_of_week) ?: 'No day';
                                                    @endphp
                                                    <select name="days[{{ $subject->id }}]" class="form-select js-assignment-field js-assignment-select" disabled>
                                                        <option value="">Select day</option>
                                                        @foreach($dayOptions as $day)
                                                        <option value="{{ $day }}" {{ old('days.' . $subject->id, optional($assignment)->day_of_week) === $day ? 'selected' : '' }}>
                                                            {{ $day }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="text" class="form-control js-assignment-readonly" value="{{ $dayDisplay }}" readonly>
                                                </td>
                                                <td>
                                                    @php
                                                    $startDisplay = $startSelected ?: 'No start time';
                                                    @endphp
                                                    <select name="start_times[{{ $subject->id }}]" class="form-select js-assignment-field js-assignment-select" disabled>
                                                        <option value="">Start time</option>
                                                        @foreach($timeOptions as $time)
                                                        <option value="{{ $time }}" {{ $startSelected === $time ? 'selected' : '' }}>
                                                            {{ $time }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="text" class="form-control js-assignment-readonly" value="{{ $startDisplay }}" readonly>
                                                </td>
                                                <td>
                                                    @php
                                                    $endDisplay = $endSelected ?: 'No end time';
                                                    @endphp
                                                    <select name="end_times[{{ $subject->id }}]" class="form-select js-assignment-field js-assignment-select" disabled>
                                                        <option value="">End time</option>
                                                        @foreach($timeOptions as $time)
                                                        <option value="{{ $time }}" {{ $endSelected === $time ? 'selected' : '' }}>
                                                            {{ $time }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="text" class="form-control js-assignment-readonly" value="{{ $endDisplay }}" readonly>
                                                </td>
                                                <td>
                                                    @php
                                                    $currentRoom = old('rooms.' . $subject->id, optional($assignment)->room);
                                                    $roomDisplay = $currentRoom ?: 'No room';
                                                    @endphp
                                                    <select name="rooms[{{ $subject->id }}]" class="form-select js-assignment-field js-assignment-select" disabled>
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
                                                    <input type="text" class="form-control js-assignment-readonly" value="{{ $roomDisplay }}" readonly>
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
                                            <form action="{{ route('admin.students.assign-section', $student) }}" method="POST" class="d-inline js-remove-student-form" data-student-name="{{ $student->first_name }} {{ $student->last_name }}">
                                                @csrf
                                                <input type="hidden" name="section_id" value="">
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    title="Remove from section" aria-label="Remove from section">
                                                    <i class="fas fa-trash"></i>
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

            </main>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('subjectTeacherAssignmentsForm');
        var toggleBtn = document.getElementById('toggleAssignmentsEditBtn');
        var saveBtn = document.getElementById('saveAssignmentsBtn');

        if (!form || !toggleBtn || !saveBtn) {
            return;
        }

        var fields = Array.from(form.querySelectorAll('.js-assignment-field'));
        var selectFields = Array.from(form.querySelectorAll('.js-assignment-select'));
        var readonlyFields = Array.from(form.querySelectorAll('.js-assignment-readonly'));
        var isEditMode = form.getAttribute('data-has-errors') === '1';
        var initialValues = new Map(fields.map(function(field) {
            return [field.name, field.value];
        }));

        function resetToInitialValues() {
            fields.forEach(function(field) {
                if (initialValues.has(field.name)) {
                    field.value = initialValues.get(field.name);
                }
            });
        }

        function syncReadonlyFields() {
            selectFields.forEach(function(selectField) {
                var readonlyField = selectField.nextElementSibling;
                if (!readonlyField || !readonlyField.classList.contains('js-assignment-readonly')) {
                    return;
                }

                var selectedOption = selectField.options[selectField.selectedIndex];
                readonlyField.value = selectedOption ? selectedOption.text.trim() : 'N/A';
            });
        }

        function applyEditMode() {
            fields.forEach(function(field) {
                field.disabled = !isEditMode;
            });

            selectFields.forEach(function(selectField) {
                selectField.classList.toggle('d-none', !isEditMode);
            });

            readonlyFields.forEach(function(readonlyField) {
                readonlyField.classList.toggle('d-none', isEditMode);
            });

            toggleBtn.innerHTML = isEditMode ?
                '<i class="fas fa-times me-1"></i>Cancel Edit' :
                '<i class="fas fa-pen me-1"></i>Edit';

            saveBtn.classList.toggle('d-none', !isEditMode);
        }

        toggleBtn.addEventListener('click', function() {
            if (isEditMode) {
                resetToInitialValues();
            }
            isEditMode = !isEditMode;
            syncReadonlyFields();
            applyEditMode();
        });

        form.addEventListener('change', function(event) {
            if (event.target && event.target.classList.contains('js-assignment-select')) {
                syncReadonlyFields();
            }
        });

        syncReadonlyFields();
        applyEditMode();
    });
</script>
@endpush

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg add-students-modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStudentModalLabel" style="color: #198754;">
                    <i class="fas fa-user-plus me-2"></i>Add Students to {{ $section->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="studentSearch" class="form-label">Filter Students ({{ $section->grade_level }})</label>
                    <div class="position-relative">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-filter"></i></span>
                            <input type="text"
                                class="form-control compact-control"
                                id="studentSearch"
                                placeholder="Type to filter by name or ID..."
                                autocomplete="off">
                            <button class="btn btn-outline-success compact-control" type="button" id="selectAllStudentsBtn">
                                <i class="fas fa-check-double me-1"></i>Select All
                            </button>
                        </div>
                        <div id="searchSuggestions" class="student-suggestion-panel w-100 bg-white border rounded shadow-sm mt-1"
                            style="display: block;">
                            <!-- All students will be displayed here by default -->
                            <div class="text-center py-3">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <div id="studentPagination" class="d-flex justify-content-between align-items-center mt-2 small text-muted"></div>
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
                        All students in {{ $section->grade_level }} have been assigned to sections.
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var removeForms = document.querySelectorAll('.js-remove-student-form');

        removeForms.forEach(function(removeForm) {
            removeForm.addEventListener('submit', function(event) {
                if (removeForm.dataset.localConfirmPass === 'true') {
                    delete removeForm.dataset.localConfirmPass;
                    return;
                }

                event.preventDefault();
                var studentName = removeForm.getAttribute('data-student-name') || 'this student';

                var confirmPromise;
                if (window.Swal && typeof window.Swal.fire === 'function') {
                    confirmPromise = window.Swal.fire({
                        icon: 'warning',
                        title: 'Remove student from section?',
                        text: 'Remove ' + studentName + ' from this section?',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, remove',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        reverseButtons: true,
                        focusCancel: true,
                    });
                } else if (window.AppSwal && typeof window.AppSwal.confirmDelete === 'function') {
                    confirmPromise = window.AppSwal.confirmDelete();
                } else {
                    confirmPromise = Promise.resolve({
                        isConfirmed: window.confirm('Remove ' + studentName + ' from this section?')
                    });
                }

                confirmPromise.then(function(result) {
                    if (!result || !result.isConfirmed) {
                        return;
                    }

                    if (window.Swal && typeof window.Swal.fire === 'function') {
                        window.Swal.fire({
                            title: 'Processing request...',
                            text: 'Please wait while we remove the student.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showCancelButton: false,
                            showConfirmButton: false,
                            didOpen: function() {
                                window.Swal.showLoading();
                            }
                        });
                    }

                    removeForm.dataset.localConfirmPass = 'true';
                    if (typeof removeForm.requestSubmit === 'function') {
                        removeForm.requestSubmit();
                    } else {
                        removeForm.submit();
                    }
                });
            }, true);
        });
    });
</script>
@endpush

@endsection