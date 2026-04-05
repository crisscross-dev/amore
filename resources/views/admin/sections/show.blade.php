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

    .assignment-schedule-trigger {
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: space-between;
        border-color: #198754;
        color: #198754;
        min-height: 42px;
        padding: 0.5rem 0.85rem;
        font-size: 0.95rem;
    }

    .assignment-schedule-trigger .label {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .assignment-schedule-trigger:hover {
        background-color: #e8f6ee;
        color: #146c43;
        border-color: #146c43;
    }

    #subjectTeacherAssignmentsForm.is-edit-mode .js-schedule-col-day,
    #subjectTeacherAssignmentsForm.is-edit-mode .js-schedule-col-room,
    #subjectTeacherAssignmentsForm.is-edit-mode .js-schedule-col-day-cell,
    #subjectTeacherAssignmentsForm.is-edit-mode .js-schedule-col-room-cell {
        display: none;
    }

    #subjectTeacherAssignmentsForm.is-edit-mode .js-time-title-readonly {
        display: none;
    }

    #subjectTeacherAssignmentsForm:not(.is-edit-mode) .js-time-title-edit {
        display: none;
    }

    #subjectTeacherAssignmentsForm.is-edit-mode .js-schedule-col-time {
        width: 32% !important;
    }

    #subjectTeacherAssignmentsForm:not(.is-edit-mode) .js-schedule-col-time {
        width: 16% !important;
    }
</style>
@endpush

<div class="dashboard-container sections-show-live-page"
    data-section-id="{{ $section->id }}"
    data-live-url="{{ route('admin.sections.show.live-signature', $section) }}"
    data-live-signature="{{ $sectionShowLiveSignature ?? '' }}">
    <div class="container-fluid px-4">
        <div class="row">
            <main class="col-12">



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
                        $dayShortMap = [
                        'Monday' => 'Mon',
                        'Tuesday' => 'Tue',
                        'Wednesday' => 'Wed',
                        'Thursday' => 'Thu',
                        'Friday' => 'Fri',
                        'Saturday' => 'Sat',
                        ];
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
                            7 => ['MAPEH - MUSIC & ARTS', 'MAPEH - PE & HEALTH', 'MUSIC & ARTS', 'PE & HEALTH'],
                            8 => ['MAPEH - MUSIC & ARTS', 'MAPEH - PE & HEALTH', 'MUSIC & ARTS', 'PE & HEALTH'],
                            9 => ['MAPEH - MUSIC', 'MAPEH - ARTS', 'MAPEH - PE', 'MAPEH - HEALTH', 'MUSIC', 'ARTS', 'PE', 'HEALTH'],
                            10 => ['MAPEH - MUSIC', 'MAPEH - ARTS', 'MAPEH - PE', 'MAPEH - HEALTH', 'MUSIC', 'ARTS', 'PE', 'HEALTH'],
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
                            return in_array(mb_strtoupper((string) $subject->name, 'UTF-8'), $mapehComponentNames, true);
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
                            if ($mapehGrouped && in_array(mb_strtoupper((string) $subject->name, 'UTF-8'), $mapehComponentNames, true)) {
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
                                data-has-errors="{{ $hasAssignmentErrors ? '1' : '0' }}"
                                data-section-id="{{ $section->id }}">
                                @csrf
                                <div class="d-flex justify-content-end gap-2 mb-3">
                                    <button type="submit" class="btn btn-success d-none" id="saveAssignmentsBtn">
                                        <i class="fas fa-save me-1"></i>Save All
                                    </button>
                                    <button type="button" class="btn btn-outline-success" id="toggleAssignmentsEditBtn">
                                        <i class="fas fa-pen me-1"></i>Edit
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
                                        <thead class="text-center">
                                            <tr>
                                                <th style="width: 25%;">Subject</th>
                                                <th style="width: 30%;">Assigned Teacher</th>
                                                <th class="js-schedule-col-day" style="width: 8%;">Day</th>
                                                <th class="js-schedule-col-room" style="width: 8%;">Room</th>
                                                <th class="js-schedule-col-time" style="width: 16%;">
                                                    <span class="js-time-title-readonly">Time</span>
                                                    <span class="js-time-title-edit">Day / Room / Time</span>
                                                </th>
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
                                            <tr class="js-assignment-row" data-subject-id="{{ $subject->id }}" data-group-subject-ids="{{ implode(',', $subject->component_ids) }}">
                                                <td>
                                                    <div class="fw-semibold">{{ $subject->name }}</div>
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
                                                    : '';
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
                                                <td class="js-schedule-col-day-cell">
                                                    @php
                                                    $dayValue = old('days.' . $subject->id, optional($assignment)->day_of_week) ?: '';
                                                    $dayDisplay = $dayValue !== '' ? ($dayShortMap[$dayValue] ?? $dayValue) : '';
                                                    @endphp
                                                    <select name="days[{{ $subject->id }}]" class="form-select js-assignment-field js-assignment-select js-assignment-day js-assignment-schedule-select" disabled>
                                                        <option value="">Select day</option>
                                                        @foreach($dayOptions as $day)
                                                        <option value="{{ $day }}" {{ old('days.' . $subject->id, optional($assignment)->day_of_week) === $day ? 'selected' : '' }}>
                                                            {{ $dayShortMap[$day] ?? $day }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="text" class="form-control js-assignment-readonly js-assignment-schedule-readonly" value="{{ $dayDisplay }}" readonly>
                                                </td>
                                                <td class="js-schedule-col-room-cell">
                                                    @php
                                                    $currentRoom = old('rooms.' . $subject->id, optional($assignment)->room);
                                                    $roomDisplay = $currentRoom ?: '';
                                                    @endphp
                                                    <select name="rooms[{{ $subject->id }}]" class="form-select js-assignment-field js-assignment-select js-assignment-room js-assignment-schedule-select" disabled>
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
                                                    <input type="text" class="form-control js-assignment-readonly js-assignment-schedule-readonly" value="{{ $roomDisplay }}" readonly>
                                                </td>
                                                <td class="js-schedule-col-time-cell">
                                                    @php
                                                    $startDisplay = $startSelected ?: '';
                                                    $endDisplay = $endSelected ?: '';
                                                    $timeDisplay = ($startDisplay && $endDisplay) ? ($startDisplay . ' - ' . $endDisplay) : '';
                                                    @endphp
                                                    <div class="js-assignment-time-select-wrap js-assignment-editonly d-none">
                                                        <input type="hidden" name="start_times[{{ $subject->id }}]" class="js-assignment-field js-assignment-start" value="{{ $startSelected }}" disabled>
                                                        <input type="hidden" name="end_times[{{ $subject->id }}]" class="js-assignment-field js-assignment-end" value="{{ $endSelected }}" disabled>
                                                        <button type="button" class="btn assignment-schedule-trigger js-assignment-field js-assignment-schedule-trigger" data-bs-toggle="modal" data-bs-target="#assignmentTimePickerModal">
                                                            <span class="label js-assignment-schedule-trigger-label">{{ $timeDisplay ?: 'Set Day / Room / Time' }}</span>
                                                            <i class="fas fa-clock ms-2"></i>
                                                        </button>
                                                    </div>
                                                    <input type="text" class="form-control js-assignment-readonly js-assignment-time-readonly" value="{{ $timeDisplay }}" readonly>
                                                    <div class="text-muted small mt-1 d-none js-assignment-time-hint"></div>
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
                                <thead class="text-center">
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
<script id="section-occupied-schedules" type="application/json">
    {
        !!json_encode($occupiedSchedules ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!
    }
</script>
<script id="section-time-options" type="application/json">
    {
        !!json_encode($timeOptions ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!
    }
</script>
<script id="section-day-options" type="application/json">
    {
        !!json_encode($dayOptions ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!
    }
</script>
<script id="section-room-options" type="application/json">
    {
        !!json_encode($roomOptions ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var liveContainer = document.querySelector('.sections-show-live-page');
        var liveUrl = liveContainer ? (liveContainer.getAttribute('data-live-url') || '') : '';
        var liveSignature = liveContainer ? (liveContainer.getAttribute('data-live-signature') || '') : '';
        var liveRequestInFlight = false;
        var livePollTimer = null;

        function isUnsafeToReload() {
            if (document.querySelector('.modal.show')) {
                return true;
            }

            var assignmentForm = document.getElementById('subjectTeacherAssignmentsForm');
            return !!(assignmentForm && assignmentForm.classList.contains('is-edit-mode'));
        }

        function buildLiveUrl() {
            var url = new URL(liveUrl, window.location.origin);
            var currentParams = new URLSearchParams(window.location.search);

            currentParams.forEach(function(value, key) {
                url.searchParams.set(key, value);
            });

            return url.toString();
        }

        function checkLiveSignature() {
            if (!liveUrl || liveRequestInFlight) {
                return;
            }

            liveRequestInFlight = true;

            fetch(buildLiveUrl(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(function(response) {
                    if (!response.ok) {
                        return null;
                    }

                    return response.json();
                })
                .then(function(payload) {
                    if (!payload || !payload.signature) {
                        return;
                    }

                    var nextSignature = payload.signature;

                    if (!liveSignature) {
                        liveSignature = nextSignature;
                        if (liveContainer) {
                            liveContainer.setAttribute('data-live-signature', nextSignature);
                        }
                        return;
                    }

                    if (nextSignature !== liveSignature) {
                        if (isUnsafeToReload()) {
                            return;
                        }

                        window.location.reload();
                    }
                })
                .catch(function(error) {
                    console.debug('Sections show live polling skipped:', error);
                })
                .finally(function() {
                    liveRequestInFlight = false;
                });
        }

        if (liveUrl) {
            livePollTimer = window.setInterval(function() {
                if (!document.hidden) {
                    checkLiveSignature();
                }
            }, 10000);

            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    checkLiveSignature();
                }
            });

            window.addEventListener('beforeunload', function() {
                if (livePollTimer) {
                    clearInterval(livePollTimer);
                    livePollTimer = null;
                }
            }, {
                once: true
            });
        }

        var form = document.getElementById('subjectTeacherAssignmentsForm');
        var toggleBtn = document.getElementById('toggleAssignmentsEditBtn');
        var saveBtn = document.getElementById('saveAssignmentsBtn');
        var occupiedSchedulesPayload = document.getElementById('section-occupied-schedules');
        var timeOptionsPayload = document.getElementById('section-time-options');
        var dayOptionsPayload = document.getElementById('section-day-options');
        var roomOptionsPayload = document.getElementById('section-room-options');
        var scheduleModalElement = document.getElementById('assignmentTimePickerModal');
        var scheduleApplyButton = document.getElementById('assignmentTimePickerApplyBtn');
        var schedulePicker = null;
        var activeScheduleRow = null;
        var occupiedSchedules = [];
        var timeOptions = [];
        var dayOptions = [];
        var roomOptions = [];

        if (occupiedSchedulesPayload) {
            try {
                occupiedSchedules = JSON.parse(occupiedSchedulesPayload.textContent || '[]');
            } catch (error) {
                occupiedSchedules = [];
            }
        }

        if (timeOptionsPayload) {
            try {
                timeOptions = JSON.parse(timeOptionsPayload.textContent || '[]');
            } catch (error) {
                timeOptions = [];
            }
        }

        if (dayOptionsPayload) {
            try {
                dayOptions = JSON.parse(dayOptionsPayload.textContent || '[]');
            } catch (error) {
                dayOptions = [];
            }
        }

        if (roomOptionsPayload) {
            try {
                roomOptions = JSON.parse(roomOptionsPayload.textContent || '[]');
            } catch (error) {
                roomOptions = [];
            }
        }

        if (!Array.isArray(dayOptions) || dayOptions.length === 0) {
            dayOptions = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        }

        if (!Array.isArray(roomOptions) || roomOptions.length === 0) {
            roomOptions = ['201', '202', '203', '204', '205'];
        }

        if (!Array.isArray(timeOptions) || timeOptions.length === 0) {
            timeOptions = [];
            for (var hour = 7; hour <= 17; hour++) {
                [0, 30].forEach(function(minute) {
                    if (hour === 17 && minute === 30) {
                        return;
                    }

                    timeOptions.push(String(hour).padStart(2, '0') + ':' + String(minute).padStart(2, '0'));
                });
            }
        }

        if (!form || !toggleBtn || !saveBtn) {
            return;
        }

        var fields = Array.from(form.querySelectorAll('.js-assignment-field'));
        var selectFields = Array.from(form.querySelectorAll('.js-assignment-select'));
        var readonlyFields = Array.from(form.querySelectorAll('.js-assignment-readonly'));
        var timeTriggerFields = Array.from(form.querySelectorAll('.js-assignment-schedule-trigger'));
        var editOnlyFields = Array.from(form.querySelectorAll('.js-assignment-editonly'));
        var isEditMode = form.getAttribute('data-has-errors') === '1';
        var initialValues = new Map(fields.map(function(field) {
            return [field.name, field.value];
        }));

        function getDraftSchedules(excludedRow) {
            var drafts = [];

            Array.from(form.querySelectorAll('.js-assignment-row')).forEach(function(row) {
                if (excludedRow && row === excludedRow) {
                    return;
                }

                var dayField = row.querySelector('.js-assignment-day');
                var roomField = row.querySelector('.js-assignment-room');
                var startField = row.querySelector('.js-assignment-start');
                var endField = row.querySelector('.js-assignment-end');

                if (!dayField || !roomField || !startField || !endField) {
                    return;
                }

                var dayValue = dayField.value || '';
                var roomValue = roomField.value || '';
                var startValue = startField.value || '';
                var endValue = endField.value || '';
                var startMinutes = toMinutes(startValue);
                var endMinutes = toMinutes(endValue);

                if (!dayValue || !roomValue || startMinutes === null || endMinutes === null || startMinutes >= endMinutes) {
                    return;
                }

                drafts.push({
                    day: dayValue,
                    room: roomValue,
                    start: startMinutes,
                    end: endMinutes
                });
            });

            return drafts;
        }

        function getModalOccupiedRangesFor(row, dayValue, roomValue) {
            if (!row || !dayValue || !roomValue) {
                return [];
            }

            var sectionId = parseInt(form.getAttribute('data-section-id'), 10);
            var ignoredSubjectIds = getIgnoredSubjectIds(row);

            var dbRanges = occupiedSchedules.filter(function(slot) {
                if ((slot.day || '') !== dayValue || (slot.room || '') !== roomValue) {
                    return false;
                }

                var slotSectionId = parseInt(slot.section_id, 10);
                var slotSubjectId = parseInt(slot.subject_id, 10);
                if (slotSectionId === sectionId && ignoredSubjectIds.indexOf(slotSubjectId) !== -1) {
                    return false;
                }

                var slotStart = toMinutes(slot.start || '');
                var slotEnd = toMinutes(slot.end || '');
                return slotStart !== null && slotEnd !== null && slotEnd > slotStart;
            }).map(function(slot) {
                return {
                    start: toMinutes(slot.start || ''),
                    end: toMinutes(slot.end || '')
                };
            });

            var draftRanges = getDraftSchedules(row).filter(function(slot) {
                return slot.day === dayValue && slot.room === roomValue;
            }).map(function(slot) {
                return {
                    start: slot.start,
                    end: slot.end
                };
            });

            return dbRanges.concat(draftRanges);
        }

        function syncTimeTriggerLabel(row) {
            var dayField = row.querySelector('.js-assignment-day');
            var roomField = row.querySelector('.js-assignment-room');
            var startField = row.querySelector('.js-assignment-start');
            var endField = row.querySelector('.js-assignment-end');
            var labels = row.querySelectorAll('.js-assignment-schedule-trigger-label');
            if (!dayField || !roomField || !startField || !endField || !labels.length) {
                return;
            }

            var dayValue = dayField.value ? dayField.value.trim() : '';
            var roomValue = roomField.value ? roomField.value.trim() : '';
            var startValue = startField.value ? startField.value.trim() : '';
            var endValue = endField.value ? endField.value.trim() : '';
            var timeValue = startValue && endValue ? (startValue + ' - ' + endValue) : '';
            var labelValue = [dayValue, roomValue, timeValue].filter(function(value) {
                return !!value;
            }).join(' | ');

            if (!labelValue) {
                labelValue = 'Set Day / Room / Time';
            }

            labels.forEach(function(label) {
                label.textContent = labelValue;
            });
        }

        function getModalSelectedRange() {
            if (!scheduleModalElement) {
                return {
                    start: '',
                    end: ''
                };
            }

            var selectedSlots = Array.from(scheduleModalElement.querySelectorAll('#assignmentTimePickerList .time-slot.is-edge, #assignmentTimePickerList .time-slot.is-in-range'));
            if (!selectedSlots.length) {
                return {
                    start: '',
                    end: ''
                };
            }

            var sortedSlots = selectedSlots.slice().sort(function(a, b) {
                var indexA = parseInt(a.getAttribute('data-index'), 10);
                var indexB = parseInt(b.getAttribute('data-index'), 10);
                if (!Number.isFinite(indexA)) {
                    indexA = -1;
                }
                if (!Number.isFinite(indexB)) {
                    indexB = -1;
                }

                return indexA - indexB;
            });

            var first = sortedSlots[0];
            var last = sortedSlots[sortedSlots.length - 1];

            return {
                start: first ? (first.textContent || '').trim() : '',
                end: last ? (last.textContent || '').trim() : ''
            };
        }

        function resolveActiveScheduleRow() {
            if (activeScheduleRow && form.contains(activeScheduleRow)) {
                return activeScheduleRow;
            }

            if (!scheduleModalElement) {
                return null;
            }

            var subjectId = scheduleModalElement.getAttribute('data-active-subject-id') || '';
            if (!subjectId) {
                return null;
            }

            var rows = Array.from(form.querySelectorAll('.js-assignment-row'));
            for (var i = 0; i < rows.length; i++) {
                if ((rows[i].getAttribute('data-subject-id') || '') === subjectId) {
                    return rows[i];
                }
            }

            return null;
        }

        function hideScheduleModalSafely() {
            if (!scheduleModalElement) {
                return;
            }

            var modalInstance = null;
            if (window.bootstrap && window.bootstrap.Modal) {
                if (typeof window.bootstrap.Modal.getInstance === 'function') {
                    modalInstance = window.bootstrap.Modal.getInstance(scheduleModalElement);
                }
                if (!modalInstance && typeof window.bootstrap.Modal.getOrCreateInstance === 'function') {
                    modalInstance = window.bootstrap.Modal.getOrCreateInstance(scheduleModalElement);
                }
            }

            if (modalInstance) {
                modalInstance.hide();
                return;
            }

            scheduleModalElement.classList.remove('show');
            scheduleModalElement.style.display = 'none';
            scheduleModalElement.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('padding-right');
            Array.from(document.querySelectorAll('.modal-backdrop')).forEach(function(backdrop) {
                backdrop.remove();
            });
        }

        function applyScheduleModalFallback() {
            var row = resolveActiveScheduleRow();
            if (!row || !scheduleModalElement) {
                return;
            }

            var dayButton = scheduleModalElement.querySelector('#assignmentTimePickerDayList .picker-option.is-selected');
            var roomButton = scheduleModalElement.querySelector('#assignmentTimePickerRoomList .picker-option.is-selected');
            var selectedRange = getModalSelectedRange();
            var dayValue = dayButton ? ((dayButton.getAttribute('data-value') || dayButton.textContent || '').trim()) : '';
            var roomValue = roomButton ? ((roomButton.getAttribute('data-value') || roomButton.textContent || '').trim()) : '';
            var startValue = selectedRange.start || '';
            var endValue = selectedRange.end || '';

            if (!dayValue || !roomValue || !startValue || !endValue) {
                return;
            }

            var startMinutes = toMinutes(startValue);
            var endMinutes = toMinutes(endValue);
            if (startMinutes === null || endMinutes === null || startMinutes >= endMinutes) {
                return;
            }

            var rowDayField = row.querySelector('.js-assignment-day');
            var rowRoomField = row.querySelector('.js-assignment-room');
            var rowStartField = row.querySelector('.js-assignment-start');
            var rowEndField = row.querySelector('.js-assignment-end');
            if (!rowDayField || !rowRoomField || !rowStartField || !rowEndField) {
                return;
            }

            rowDayField.value = dayValue;
            rowRoomField.value = roomValue;
            rowStartField.value = startValue;
            rowEndField.value = endValue;
            updateRowFlow(row);
            syncReadonlyFields();
            hideScheduleModalSafely();
        }

        function openTimePickerForRow(row, retryCount) {
            var currentRetry = typeof retryCount === 'number' ? retryCount : 0;

            if (!isEditMode) {
                return;
            }

            initSchedulePicker();
            if (!schedulePicker) {
                if (currentRetry < 20) {
                    window.setTimeout(function() {
                        openTimePickerForRow(row, currentRetry + 1);
                    }, 100);
                } else if (window.AppSwal && typeof window.AppSwal.showError === 'function') {
                    window.AppSwal.showError('Schedule picker did not load. Please refresh the page.');
                }

                return;
            }

            var dayField = row.querySelector('.js-assignment-day');
            var roomField = row.querySelector('.js-assignment-room');
            var startField = row.querySelector('.js-assignment-start');
            var endField = row.querySelector('.js-assignment-end');

            if (!dayField || !roomField || !startField || !endField) {
                return;
            }

            activeScheduleRow = row;
            if (scheduleModalElement) {
                scheduleModalElement.setAttribute('data-active-subject-id', row.getAttribute('data-subject-id') || '');
            }

            schedulePicker.open(row, {
                day: dayField.value || '',
                room: roomField.value || '',
                start: startField.value || '',
                end: endField.value || ''
            }, {
                showModal: false
            });
        }

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
                var selectedValue = selectField.value ? selectField.value.trim() : '';
                readonlyField.value = selectedValue && selectedOption ? selectedOption.text.trim() : '';
            });

            Array.from(form.querySelectorAll('.js-assignment-row')).forEach(function(row) {
                var startField = row.querySelector('.js-assignment-start');
                var endField = row.querySelector('.js-assignment-end');
                var timeReadonly = row.querySelector('.js-assignment-time-readonly');

                if (!startField || !endField || !timeReadonly) {
                    return;
                }

                var startValue = startField.value ? startField.value.trim() : '';
                var endValue = endField.value ? endField.value.trim() : '';
                timeReadonly.value = startValue && endValue ? (startValue + ' - ' + endValue) : '';
                syncTimeTriggerLabel(row);
            });
        }

        function toMinutes(timeValue) {
            if (!timeValue || timeValue.indexOf(':') === -1) {
                return null;
            }

            var parts = timeValue.split(':');
            var hours = parseInt(parts[0], 10);
            var minutes = parseInt(parts[1], 10);

            if (!Number.isFinite(hours) || !Number.isFinite(minutes)) {
                return null;
            }

            return (hours * 60) + minutes;
        }

        function rangesOverlap(startA, endA, startB, endB) {
            return startA < endB && endA > startB;
        }

        function getIgnoredSubjectIds(row) {
            var raw = row.getAttribute('data-group-subject-ids') || row.getAttribute('data-subject-id') || '';
            return raw.split(',').map(function(part) {
                return parseInt((part || '').trim(), 10);
            }).filter(function(value) {
                return Number.isFinite(value) && value > 0;
            });
        }

        function getConflicts(row, day, room, start, end) {
            var sectionId = parseInt(form.getAttribute('data-section-id'), 10);
            var ignoredSubjectIds = getIgnoredSubjectIds(row);
            var startMinutes = toMinutes(start);
            var endMinutes = toMinutes(end);

            if (!day || !room || startMinutes === null || endMinutes === null) {
                return [];
            }

            var dbConflicts = occupiedSchedules.filter(function(slot) {
                if ((slot.day || '') !== day || (slot.room || '') !== room) {
                    return false;
                }

                var slotSectionId = parseInt(slot.section_id, 10);
                var slotSubjectId = parseInt(slot.subject_id, 10);
                if (slotSectionId === sectionId && ignoredSubjectIds.indexOf(slotSubjectId) !== -1) {
                    return false;
                }

                var slotStart = toMinutes(slot.start || '');
                var slotEnd = toMinutes(slot.end || '');
                if (slotStart === null || slotEnd === null) {
                    return false;
                }

                return rangesOverlap(startMinutes, endMinutes, slotStart, slotEnd);
            });

            var draftConflicts = getDraftSchedules(row).filter(function(slot) {
                if (slot.day !== day || slot.room !== room) {
                    return false;
                }

                return rangesOverlap(startMinutes, endMinutes, slot.start, slot.end);
            });

            return dbConflicts.concat(draftConflicts);
        }

        function initSchedulePicker() {
            if (schedulePicker) {
                return true;
            }

            if (!window.SchedulePickerModal || typeof window.SchedulePickerModal.create !== 'function') {
                return false;
            }

            schedulePicker = window.SchedulePickerModal.create({
                dayOptions: dayOptions,
                roomOptions: roomOptions,
                timeOptions: timeOptions,
                toMinutes: toMinutes,
                getOccupiedRanges: function(payload) {
                    return getModalOccupiedRangesFor(payload.row, payload.day, payload.room);
                },
                onApply: function(payload) {
                    var rowDayField = payload.row.querySelector('.js-assignment-day');
                    var rowRoomField = payload.row.querySelector('.js-assignment-room');
                    var rowStartField = payload.row.querySelector('.js-assignment-start');
                    var rowEndField = payload.row.querySelector('.js-assignment-end');

                    if (!rowDayField || !rowRoomField || !rowStartField || !rowEndField) {
                        return;
                    }

                    rowDayField.value = payload.day || '';
                    rowRoomField.value = payload.room || '';
                    rowStartField.value = payload.start || '';
                    rowEndField.value = payload.end || '';
                    updateRowFlow(payload.row);
                    syncReadonlyFields();
                },
                onError: function(message) {
                    if (window.AppSwal && typeof window.AppSwal.showError === 'function') {
                        window.AppSwal.showError(message);
                    }
                }
            });

            return !!schedulePicker;
        }

        function bootstrapSchedulePickerWhenReady(attempt) {
            var currentAttempt = typeof attempt === 'number' ? attempt : 0;

            if (initSchedulePicker()) {
                return;
            }

            if (currentAttempt >= 100) {
                return;
            }

            window.setTimeout(function() {
                bootstrapSchedulePickerWhenReady(currentAttempt + 1);
            }, 100);
        }

        function buildTimeHint(row) {
            var hint = row.querySelector('.js-assignment-time-hint');
            var dayField = row.querySelector('.js-assignment-day');
            var roomField = row.querySelector('.js-assignment-room');
            var startField = row.querySelector('.js-assignment-start');
            var endField = row.querySelector('.js-assignment-end');

            if (!hint || !dayField || !roomField || !startField || !endField) {
                return;
            }

            hint.classList.add('d-none');
            hint.classList.remove('text-danger');
            hint.classList.add('text-muted');
            hint.textContent = '';

            if (!isEditMode) {
                return;
            }

            var conflicts = getConflicts(row, dayField.value, roomField.value, startField.value, endField.value);
            if (conflicts.length > 0) {
                hint.textContent = 'Selected time is occupied for this room and day.';
                hint.classList.remove('text-muted');
                hint.classList.add('text-danger');
                hint.classList.remove('d-none');
                return;
            }

        }

        function updateRowFlow(row) {
            var dayField = row.querySelector('.js-assignment-day');
            var roomField = row.querySelector('.js-assignment-room');
            var startField = row.querySelector('.js-assignment-start');
            var endField = row.querySelector('.js-assignment-end');
            var timeTriggers = Array.from(row.querySelectorAll('.js-assignment-schedule-trigger'));

            if (!dayField || !roomField || !startField || !endField || !timeTriggers.length) {
                return;
            }

            if (!isEditMode) {
                roomField.disabled = true;
                startField.disabled = true;
                endField.disabled = true;
                timeTriggers.forEach(function(trigger) {
                    trigger.disabled = true;
                });
                buildTimeHint(row);
                return;
            }

            roomField.disabled = !dayField.value;

            if (!dayField.value) {
                roomField.value = '';
                startField.value = '';
                endField.value = '';
            }

            var canChooseTime = !!dayField.value && !!roomField.value;
            startField.disabled = !canChooseTime;
            endField.disabled = !canChooseTime;
            timeTriggers.forEach(function(trigger) {
                trigger.disabled = false;
            });

            if (!canChooseTime) {
                startField.value = '';
                endField.value = '';
            }

            if (startField.value && endField.value) {
                var startMinutes = toMinutes(startField.value);
                var endMinutes = toMinutes(endField.value);
                if (startMinutes !== null && endMinutes !== null && startMinutes >= endMinutes) {
                    endField.value = '';
                }
            }

            buildTimeHint(row);
        }

        function updateAllRowsFlow() {
            Array.from(form.querySelectorAll('.js-assignment-row')).forEach(function(row) {
                updateRowFlow(row);
            });
        }

        function applyEditMode() {
            fields.forEach(function(field) {
                if (field.classList.contains('js-assignment-schedule-trigger')) {
                    return;
                }
                field.disabled = !isEditMode;
            });

            selectFields.forEach(function(selectField) {
                if (selectField.classList.contains('js-assignment-schedule-select')) {
                    selectField.classList.add('d-none');
                    return;
                }
                selectField.classList.toggle('d-none', !isEditMode);
            });

            editOnlyFields.forEach(function(editOnlyField) {
                editOnlyField.classList.toggle('d-none', !isEditMode);
            });

            readonlyFields.forEach(function(readonlyField) {
                if (readonlyField.classList.contains('js-assignment-schedule-readonly')) {
                    readonlyField.classList.remove('d-none');
                    return;
                }
                readonlyField.classList.toggle('d-none', isEditMode);
            });

            form.classList.toggle('is-edit-mode', isEditMode);

            toggleBtn.innerHTML = isEditMode ?
                '<i class="fas fa-times me-1"></i>Cancel Edit' :
                '<i class="fas fa-pen me-1"></i>Edit';

            saveBtn.classList.toggle('d-none', !isEditMode);
            updateAllRowsFlow();
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
                var changedRow = event.target.closest('.js-assignment-row');
                if (changedRow) {
                    updateRowFlow(changedRow);
                }
                syncReadonlyFields();
                if (schedulePicker && typeof schedulePicker.refresh === 'function') {
                    schedulePicker.refresh();
                }
            }
        });

        timeTriggerFields.forEach(function(trigger) {
            trigger.addEventListener('click', function() {
                var row = trigger.closest('.js-assignment-row');
                if (row) {
                    openTimePickerForRow(row);
                }
            });
        });

        if (scheduleApplyButton) {
            scheduleApplyButton.addEventListener('click', function() {
                window.setTimeout(function() {
                    if (scheduleModalElement && scheduleModalElement.classList.contains('show')) {
                        applyScheduleModalFallback();
                    }
                }, 0);
            });
        }

        if (scheduleModalElement) {
            scheduleModalElement.addEventListener('hidden.bs.modal', function() {
                activeScheduleRow = null;
                scheduleModalElement.removeAttribute('data-active-subject-id');
            });
        }

        form.addEventListener('submit', function(event) {
            if (!isEditMode) {
                return;
            }

            var hasConflict = false;

            Array.from(form.querySelectorAll('.js-assignment-row')).forEach(function(row) {
                var dayField = row.querySelector('.js-assignment-day');
                var roomField = row.querySelector('.js-assignment-room');
                var startField = row.querySelector('.js-assignment-start');
                var endField = row.querySelector('.js-assignment-end');

                if (!dayField || !roomField || !startField || !endField) {
                    return;
                }

                if (!dayField.value || !roomField.value || !startField.value || !endField.value) {
                    return;
                }

                var conflicts = getConflicts(row, dayField.value, roomField.value, startField.value, endField.value);
                if (conflicts.length > 0) {
                    hasConflict = true;
                    buildTimeHint(row);
                }
            });

            if (hasConflict) {
                event.preventDefault();
                if (window.AppSwal && typeof window.AppSwal.showError === 'function') {
                    window.AppSwal.showError('One or more selected schedules are occupied. Please pick a free time slot.');
                }
            }
        });

        bootstrapSchedulePickerWhenReady();
        syncReadonlyFields();
        applyEditMode();
    });
</script>
@endpush

@include('admin.sections.partials.schedule_picker_modal')

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