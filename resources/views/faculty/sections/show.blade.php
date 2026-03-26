@extends('layouts.app')

@section('title', $section->name . ' - Section Details - Faculty Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@vite(['resources/css/layouts/dashboard-roles/dashboard-faculty.css'])

<style>
    .section-subjects-toggle {
        margin-bottom: 0;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid rgba(25, 135, 84, 0.12);
    }

    .section-subjects-body {
        display: block;
        padding-top: 0.75rem;
    }

    .section-subjects-row {
        display: table-row;
    }

    .section-subjects-body.is-collapsed .section-subjects-row {
        display: none;
    }

    .section-subjects-body.is-collapsed .subject-row-highlight {
        display: table-row;
    }

    .section-subjects-body.is-visible .section-subjects-row {
        display: table-row;
    }

    .subject-row-highlight {
        background: rgba(25, 135, 84, 0.12);
        border-left: 4px solid #198754;
        cursor: pointer;
    }

    .subject-row-highlight td {
        vertical-align: middle;
    }

    .section-subjects-body.is-collapsed .section-subjects-row:not(.subject-row-highlight) {
        display: none;
    }
</style>

<div class="dashboard-container">
    <div class="container-fluid px-4">
        <div class="row">

            <main class="col-12">
                <!-- Page Header -->
                <div class="welcome-card mb-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h4 class="mb-2">
                                <i class="fas fa-layer-group me-2"></i>{{ $section->name }}
                                <span class="badge bg-info ms-2">{{ $section->grade_level }}</span>
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
                                <i class="fas fa-users ms-3 me-1"></i>
                                {{ $section->students->count() }}{{ $section->capacity ? ' / ' . $section->capacity : '' }} students
                            </p>
                        </div>
                        <a href="{{ route('faculty.sections.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                    @if($section->description)
                    <div class="mt-3">
                        <p class="mb-0">{{ $section->description }}</p>
                    </div>
                    @endif
                </div>

                <!-- Adviser Card -->
                <div class="faculty-management-card mb-4">
                    <div class="d-flex align-items-center gap-3 p-1">
                        <div>
                            <i class="fas fa-user-check fa-lg text-success me-2"></i>
                        </div>
                        <div>
                            @if($section->adviser)
                            <strong class="text-dark"><span class="text-success">Adviser</span> {{ $section->adviser->first_name }} {{ $section->adviser->last_name }}</strong>
                            @else
                            <strong class="text-dark"><span class="text-success">Adviser</span> faculty Salcedo</strong>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Selected Subjects -->
                <div class="faculty-management-card faculty-management-table mb-4">
                    @php
                    $gradeNumber = null;
                    if (preg_match('/(7|8|9|10|11|12)/', (string) ($section->grade_level ?? ''), $gradeMatch)) {
                    $gradeNumber = (int) $gradeMatch[1];
                    }

                    $mapehComponentNamesByGrade = [
                    7 => ['MAPEH - Music & Arts', 'MAPEH - PE & Health', 'Music & Arts', 'PE & Health'],
                    8 => ['MAPEH - Music & Arts', 'MAPEH - PE & Health', 'Music & Arts', 'PE & Health'],
                    9 => ['MAPEH - Music', 'MAPEH - Arts', 'MAPEH - PE', 'MAPEH - Health', 'Music', 'Arts', 'PE', 'Health'],
                    10 => ['MAPEH - Music', 'MAPEH - Arts', 'MAPEH - PE', 'MAPEH - Health', 'Music', 'Arts', 'PE', 'Health'],
                    ];

                    $mapehComponentNames = $mapehComponentNamesByGrade[$gradeNumber] ?? [];
                    $mapehKnownNames = collect($mapehComponentNamesByGrade)
                    ->flatten()
                    ->push('MAPEH')
                    ->map(fn ($name) => strtolower(trim((string) $name)))
                    ->unique()
                    ->values()
                    ->all();

                    $groupedAssignments = collect();
                    $mapehAssignments = collect();

                    foreach ($subjectAssignments as $assignment) {
                    $subjectName = (string) (optional($assignment->subject)->name ?? '');
                    $normalizedSubjectName = strtolower(trim($subjectName));
                    $isMapeh =
                    str_contains($normalizedSubjectName, 'mapeh')
                    || in_array($normalizedSubjectName, $mapehKnownNames, true)
                    || (!empty($mapehComponentNames) && in_array($subjectName, $mapehComponentNames, true));

                    if ($isMapeh) {
                    $mapehAssignments->push($assignment);
                    continue;
                    }

                    $groupedAssignments->push((object) [
                    'assignment' => $assignment,
                    'subject_name' => $subjectName ?: 'N/A',
                    ]);
                    }

                    if ($mapehAssignments->isNotEmpty()) {
                    $representative = $mapehAssignments->first(function ($assignment) {
                    return (bool) (optional($assignment)->teacher_id || optional($assignment)->day_of_week || optional($assignment)->room);
                    }) ?? $mapehAssignments->first();

                    $groupedAssignments->prepend((object) [
                    'assignment' => $representative,
                    'subject_name' => 'MAPEH',
                    ]);
                    }

                    $viewerIsAdviser = (int) $section->adviser_id === (int) auth()->id();
                    @endphp

                    <div class="d-flex justify-content-between align-items-center mb-3 section-subjects-toggle">
                        <h5 class="mb-0 text-success">
                            <i class="fas fa-book-open me-2 text-success"></i>
                            Selected Subjects
                        </h5>
                        <span class="badge bg-success bg-opacity-75">
                            {{ $groupedAssignments->count() }} subject{{ $groupedAssignments->count() === 1 ? '' : 's' }}
                        </span>
                    </div>

                    @if($groupedAssignments->isNotEmpty())
                    <div
                        class="section-subjects-body {{ $viewerIsAdviser ? 'is-visible' : 'is-collapsed' }}"
                        id="selectedSubjectsTable"
                        data-lock-expanded="{{ $viewerIsAdviser ? '1' : '0' }}">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>Teacher</th>
                                        <th>Schedule</th>
                                        <th>Room</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($groupedAssignments as $grouped)
                                    @php
                                    $assignment = $grouped->assignment;
                                    @endphp
                                    <tr class="section-subjects-row {{ (int) $assignment->teacher_id === (int) auth()->id() ? 'subject-row-highlight' : '' }}">
                                        <td>
                                            <strong>{{ $grouped->subject_name }}</strong>
                                        </td>
                                        <td>
                                            @if($assignment->teacher)
                                            {{ $assignment->teacher->first_name }} {{ $assignment->teacher->last_name }}
                                            @else
                                            <span class="text-muted">TBA</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($assignment->day_of_week && $assignment->start_time && $assignment->end_time)
                                            {{ $assignment->day_of_week }}, {{ substr($assignment->start_time, 0, 5) }} - {{ substr($assignment->end_time, 0, 5) }}
                                            @else
                                            <span class="text-muted">TBA</span>
                                            @endif
                                        </td>
                                        <td>{{ $assignment->room ?: 'TBA' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @else
                    <div class="faculty-management-empty text-success">
                        <i class="fas fa-book text-success"></i>
                        <h5 class="fw-semibold mb-2 text-success">No subjects selected yet</h5>
                        <p class="mb-0 text-success">This section does not have selected subjects yet.</p>
                    </div>
                    @endif
                </div>

                <!-- Students Table -->
                <div class="faculty-management-card faculty-management-table">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 text-success">
                            <i class="fas fa-users me-2 text-success"></i>
                            Students in this Section
                        </h5>
                        <span class="badge bg-success bg-opacity-75">
                            {{ $section->students->count() }} student{{ $section->students->count() === 1 ? '' : 's' }}
                        </span>
                    </div>

                    @if($section->students->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student</th>
                                    <th>Student ID</th>
                                    <th>LRN</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($section->students->sortBy('last_name') as $index => $student)
                                <tr>
                                    <td class="text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($student->profile_picture)
                                            <img src="{{ asset('uploads/profile_picture/' . $student->profile_picture) }}"
                                                class="rounded-circle" width="36" height="36"
                                                style="object-fit: cover; flex-shrink: 0;">
                                            @else
                                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center"
                                                style="width: 36px; height: 36px; font-size: 14px; flex-shrink: 0;">
                                                {{ strtoupper(substr($student->first_name, 0, 1)) }}
                                            </div>
                                            @endif
                                            <div>
                                                <strong>{{ $student->last_name }}, {{ $student->first_name }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $student->custom_id ?? '—' }}</td>
                                    <td>{{ $student->lrn ?? '—' }}</td>
                                    <td>{{ $student->email }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="faculty-management-empty text-success">
                        <i class="fas fa-users text-success"></i>
                        <h5 class="fw-semibold mb-2 text-success">No students assigned</h5>
                        <p class="mb-0 text-success">No students have been assigned to this section yet.</p>
                    </div>
                    @endif
                </div>
            </main>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var table = document.getElementById('selectedSubjectsTable');
        var rows = table ? table.querySelectorAll('.section-subjects-row') : [];
        var lockExpanded = table && table.getAttribute('data-lock-expanded') === '1';

        if (!table || !rows.length) {
            return;
        }

        if (lockExpanded) {
            table.classList.remove('is-collapsed');
            table.classList.add('is-visible');
            return;
        }

        rows.forEach(function(row) {
            row.setAttribute('role', 'button');
            row.setAttribute('tabindex', '0');
        });

        var toggleTable = function() {
            table.classList.toggle('is-collapsed');
            table.classList.toggle('is-visible');
        };

        rows.forEach(function(row) {
            row.addEventListener('click', toggleTable);

            row.addEventListener('keydown', function(event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    toggleTable();
                }
            });
        });
    });
</script>

@endsection