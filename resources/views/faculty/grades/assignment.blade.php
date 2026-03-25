@extends('layouts.app')

@section('title', 'Manage Assignment Grades - Faculty Dashboard - Amore Academy')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@vite(['resources/css/layouts/dashboard-roles/dashboard-faculty.css','resources/css/faculty/grade-management.css'])

<style>
    .quarter-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .quarter-tab-btn {
        border: 1px solid rgba(22, 101, 52, 0.25);
        background: #fff;
        color: #166534;
        border-radius: 999px;
        padding: 0.35rem 0.85rem;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .quarter-tab-btn.active {
        background: #166534;
        color: #fff;
        border-color: #166534;
    }

    .sheet-readonly input,
    .sheet-readonly textarea {
        background-color: #f8fafc;
    }

    .grade-sheet-table th,
    .grade-sheet-table td {
        padding-top: 0.6rem;
        padding-bottom: 0.6rem;
        vertical-align: middle;
    }

    .grade-sheet-input {
        min-height: 2.1rem;
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
        font-size: 0.92rem;
    }

    .grade-component-row {
        display: flex;
        align-items: center;
        gap: 0.45rem;
    }

    .grade-component-label {
        min-width: 112px;
        font-size: 0.77rem;
        font-weight: 600;
        color: #475569;
    }

    .average-field {
        background-color: #f8fafc;
        font-weight: 700;
        text-align: center;
        color: #14532d;
    }
</style>

<div class="dashboard-container">
    <div class="container-fluid px-4">
        <div class="row">
            <main class="col-12">
                <div class="welcome-card mb-4">
                    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                        <div>
                            @php
                            $isMapehGroup = $gradeSubjects->count() > 1;
                            @endphp
                            <p class="mb-1 opacity-90"><strong>Subject:</strong> {{ $isMapehGroup ? 'MAPEH' : ($assignment->subject->name ?? 'N/A') }}</p>
                            <p class="mb-1 opacity-90"><strong>Section:</strong> {{ $assignment->section->name ?? 'N/A' }}</p>
                            <p class="mb-0 opacity-90"><strong>Teacher:</strong> {{ $assignment->teacher->first_name ?? '' }} {{ $assignment->teacher->last_name ?? '' }}</p>
                        </div>
                        <a href="{{ route('faculty.grades.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to cards
                        </a>
                    </div>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <div class="faculty-management-card p-4 mb-3">
                    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                        <div>
                            <div class="form-label mb-2">Term</div>
                            <div class="quarter-tabs">
                                @foreach($quarterTerms as $quarterTerm)
                                <form method="GET" action="{{ route('faculty.grades.assignment', $assignment) }}" class="d-inline">
                                    <input type="hidden" name="term" value="{{ $quarterTerm }}">
                                    <button type="submit" class="quarter-tab-btn {{ $term === $quarterTerm ? 'active' : '' }}">{{ $quarterTerm }}</button>
                                </form>
                                @endforeach
                            </div>
                        </div>
                        <div class="d-flex flex-column align-items-end gap-2">
                            <button type="button" class="btn btn-outline-success" id="toggleSheetEditBtn" {{ $sheetLocked ? 'disabled' : '' }}>
                                <i class="fas {{ $sheetLocked ? 'fa-lock' : 'fa-pen' }} me-1"></i>Edit
                            </button>
                        </div>
                    </div>
                </div>

                <div class="faculty-management-card faculty-management-table">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 text-success">
                            <i class="fas fa-users me-2"></i>Students and Grades
                        </h5>
                        <span class="badge bg-success bg-opacity-75">{{ $students->count() }} student{{ $students->count() === 1 ? '' : 's' }}</span>
                    </div>

                    @if($students->isEmpty())
                    <div class="faculty-management-empty">
                        <i class="fas fa-users"></i>
                        <h5 class="fw-semibold mb-2 text-success">No students in this section</h5>
                        <p class="mb-0">Assign students first before entering grades.</p>
                    </div>
                    @else
                    <div class="table-responsive">
                        <form action="{{ route('faculty.grades.assignment.update', $assignment) }}" method="POST" id="sheetUpdateForm" class="sheet-readonly" data-has-errors="{{ $errors->any() ? '1' : '0' }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="term" value="{{ $term }}">

                            <table class="table table-hover align-middle mb-0 grade-sheet-table">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Student ID</th>
                                        <th>Subject</th>
                                        <th style="width: 300px;">Grade</th>
                                        <th style="width: 150px;">Total Average</th>
                                        <th>Remark</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $student)
                                    @php
                                    $studentEntries = $gradeEntriesByStudent->get($student->id, collect());
                                    $rowLocked = $sheetLocked || $gradeSubjects->every(function ($gradeSubject) use ($studentEntries) {
                                    $entry = $studentEntries->get($gradeSubject->id);
                                    return $entry && $entry->status !== 'draft';
                                    });
                                    $remarkSource = $studentEntries->first(function ($entry) {
                                    return filled($entry->faculty_remark ?? null);
                                    });
                                    @endphp
                                    <tr>
                                        <td>{{ $student->last_name }}, {{ $student->first_name }}</td>
                                        <td>{{ $student->custom_id ?? 'N/A' }}</td>
                                        <td>
                                            @if($gradeSubjects->count() === 1)
                                            {{ $gradeSubjects->first()->name ?? ($assignment->subject->name ?? 'N/A') }}
                                            @else
                                            <div class="d-flex flex-column gap-1">
                                                @foreach($gradeSubjects as $gradeSubject)
                                                <span class="badge rounded-pill bg-light text-dark border">{{ str_replace('MAPEH - ', '', $gradeSubject->name) }}</span>
                                                @endforeach
                                            </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column gap-2">
                                                @foreach($gradeSubjects as $gradeSubject)
                                                @php
                                                $entry = $studentEntries->get($gradeSubject->id);
                                                $fieldLocked = $sheetLocked || ($entry && $entry->status !== 'draft');
                                                $fieldLabel = str_replace('MAPEH - ', '', $gradeSubject->name);
                                                @endphp
                                                <div class="grade-component-row">
                                                    @if($gradeSubjects->count() > 1)
                                                    <span class="grade-component-label">{{ $fieldLabel }}</span>
                                                    @endif
                                                    <input
                                                        type="number"
                                                        name="grade_values[{{ $student->id }}][{{ $gradeSubject->id }}]"
                                                        class="form-control form-control-sm js-sheet-field grade-sheet-input {{ $gradeSubjects->count() > 1 ? 'js-mapeh-grade' : '' }}"
                                                        data-student-id="{{ $student->id }}"
                                                        min="50"
                                                        max="100"
                                                        step="0.01"
                                                        value="{{ old('grade_values.' . $student->id . '.' . $gradeSubject->id, $entry->grade_value ?? '') }}"
                                                        {{ $fieldLocked ? 'disabled data-lock="1"' : 'disabled' }}>
                                                </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            @if($gradeSubjects->count() > 1)
                                            <input
                                                type="text"
                                                class="form-control form-control-sm grade-sheet-input average-field js-mapeh-average"
                                                data-student-id="{{ $student->id }}"
                                                value=""
                                                readonly
                                                disabled>
                                            @else
                                            <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <input
                                                type="text"
                                                name="faculty_remarks[{{ $student->id }}]"
                                                class="form-control form-control-sm js-sheet-field grade-sheet-input"
                                                value="{{ old('faculty_remarks.' . $student->id, $remarkSource->faculty_remark ?? '') }}"
                                                {{ $rowLocked ? 'disabled data-lock="1"' : 'disabled' }}>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="d-none mt-3 d-flex gap-2 flex-wrap" id="sheetSaveActions">
                                <button class="btn btn-green"><i class="fas fa-save me-1"></i>Save Grade Sheet</button>
                                <button
                                    type="submit"
                                    formaction="{{ route('faculty.grades.assignment.upload', $assignment) }}"
                                    formmethod="POST"
                                    class="btn btn-outline-success">
                                    <i class="fas fa-upload me-1"></i>Upload Grades
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
            </main>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var toggleBtn = document.getElementById('toggleSheetEditBtn');
        var form = document.getElementById('sheetUpdateForm');
        var saveActions = document.getElementById('sheetSaveActions');

        function initializeMapehAverages(targetForm) {
            if (!targetForm) {
                return;
            }

            var gradeInputs = Array.from(targetForm.querySelectorAll('.js-mapeh-grade'));
            if (!gradeInputs.length) {
                return;
            }

            function recalculateForStudent(studentId) {
                var studentInputs = gradeInputs.filter(function(input) {
                    return input.getAttribute('data-student-id') === String(studentId);
                });
                var averageField = targetForm.querySelector('.js-mapeh-average[data-student-id="' + studentId + '"]');
                if (!averageField) {
                    return;
                }

                var numericValues = studentInputs
                    .map(function(input) {
                        var value = parseFloat(input.value);
                        return Number.isFinite(value) ? value : null;
                    })
                    .filter(function(value) {
                        return value !== null;
                    });

                if (numericValues.length !== studentInputs.length || numericValues.length === 0) {
                    averageField.value = '';
                    return;
                }

                var total = numericValues.reduce(function(sum, value) {
                    return sum + value;
                }, 0);
                var average = total / numericValues.length;
                averageField.value = average.toFixed(2);
            }

            var studentIds = Array.from(new Set(gradeInputs.map(function(input) {
                return input.getAttribute('data-student-id');
            })));

            studentIds.forEach(function(studentId) {
                recalculateForStudent(studentId);
            });

            gradeInputs.forEach(function(input) {
                input.addEventListener('input', function() {
                    var studentId = input.getAttribute('data-student-id');
                    recalculateForStudent(studentId);
                });
            });
        }

        initializeMapehAverages(form);

        if (!toggleBtn || !form) {
            return;
        }

        if (toggleBtn.disabled) {
            return;
        }

        var fields = Array.from(form.querySelectorAll('.js-sheet-field'));
        var isEditMode = form.getAttribute('data-has-errors') === '1';

        function applyEditMode() {
            form.classList.toggle('sheet-readonly', !isEditMode);
            fields.forEach(function(field) {
                if (field.hasAttribute('data-lock')) {
                    return;
                }
                field.disabled = !isEditMode;
            });

            toggleBtn.innerHTML = isEditMode ?
                '<i class="fas fa-eye me-1"></i>Read-only' :
                '<i class="fas fa-pen me-1"></i>Edit';

            if (saveActions) {
                saveActions.classList.toggle('d-none', !isEditMode);
            }
        }

        toggleBtn.addEventListener('click', function() {
            isEditMode = !isEditMode;
            applyEditMode();
        });

        applyEditMode();
    });
</script>
@endsection