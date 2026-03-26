@extends('layouts.app')

@section('title', 'Manage Assignment Grades - Faculty Dashboard - Amore Academy')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@vite(['resources/css/layouts/dashboard-roles/dashboard-faculty.css','resources/css/faculty/grade-management.css'])

<style>
    .dashboard-container .container-fluid {
        max-width: 100%;
        padding-left: 0.75rem !important;
        padding-right: 0.75rem !important;
    }

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

    .faculty-management-table h5 {
        font-size: 1.15rem;
    }

    .grade-sheet-table th {
        font-size: 0.92rem;
        font-weight: 700;
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

    .grade-column-header,
    .grade-column-cell {
        width: 110px;
        min-width: 110px;
    }

    .grade-column-header {
        white-space: normal;
    }

    .grade-entry-input {
        width: 88px;
        min-width: 88px;
        margin: 0 auto;
    }

    .average-column-header,
    .average-column-cell {
        width: 110px;
        min-width: 110px;
    }

    .average-entry-input {
        width: 88px;
        min-width: 88px;
        margin: 0 auto;
    }

    /* Hide number input arrows for a cleaner grade entry UI. */
    .grade-sheet-input[type="number"]::-webkit-outer-spin-button,
    .grade-sheet-input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .grade-sheet-input[type="number"] {
        -moz-appearance: textfield;
        appearance: textfield;
    }

    .grade-sheet-input.unsaved-draft {
        background-color: #fff3e0 !important;
        border-color: #f1b977;
    }

    .grade-sheet-input.approved-grade {
        background-color: #e9f9ef !important;
        border-color: #9bd5b2;
    }

    .grade-sheet-input.quarter-approved,
    .quarter-approved {
        background-color: #e9f9ef !important;
        border-color: #9bd5b2;
    }
</style>

<div class="dashboard-container">
    <div class="container-fluid px-4">
        <div class="row">
            <main class="col-12">


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

                @php
                $isMapehGroup = $gradeSubjects->count() > 1;
                $formatGradeValue = function ($value) {
                if ($value === null || $value === '') {
                return '';
                }

                $formatted = rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
                return $formatted === '' ? '0' : $formatted;
                };
                $allQuarterEntries = $gradeEntriesByStudent->flatMap(function ($studentEntries) {
                return $studentEntries->values();
                });
                $hasAnyApprovedEntry = $allQuarterEntries->contains(function ($entry) {
                return $entry
                && ($entry->status === 'approved'
                || ($entry->approved_by !== null && $entry->approved_at !== null));
                });
                $quarterApproved = $sheetLocked && $hasAnyApprovedEntry;
                @endphp

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
                            @php
                            $hasEditableGradeValue = $students->contains(function ($student) use ($gradeSubjects, $gradeEntriesByStudent, $sheetLocked) {
                            $studentEntries = $gradeEntriesByStudent->get($student->id, collect());

                            return $gradeSubjects->contains(function ($gradeSubject) use ($studentEntries, $sheetLocked) {
                            $entry = $studentEntries->get($gradeSubject->id);
                            $fieldLocked = $sheetLocked || ($entry && $entry->status !== 'draft');

                            if ($fieldLocked) {
                            return false;
                            }

                            return $entry && $entry->grade_value !== null;
                            });
                            });

                            $sheetViewLabel = $hasEditableGradeValue ? 'Edit' : 'Input Grades';
                            $sheetViewIcon = 'fa-pen';
                            @endphp
                            <button
                                type="button"
                                class="btn btn-outline-success"
                                id="toggleSheetEditBtn"
                                data-view-label="{{ $sheetViewLabel }}"
                                data-view-icon="{{ $sheetViewIcon }}"
                                @if($sheetLocked) disabled aria-disabled="true" style="pointer-events: none;" @endif>
                                <i class="fas {{ $sheetLocked ? 'fa-lock' : $sheetViewIcon }} me-1"></i>{{ $sheetLocked ? 'Locked Edit' : $sheetViewLabel }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="faculty-management-card faculty-management-table">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 text-success">
                            <i class="fas fa-users me-2"></i>Student Grades in {{ $isMapehGroup ? 'MAPEH' : ($assignment->subject->name ?? 'N/A') }}
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
                        <form action="{{ route('faculty.grades.assignment.update', $assignment) }}" method="POST" id="sheetUpdateForm" class="sheet-readonly" data-has-errors="{{ $errors->any() ? '1' : '0' }}" data-assignment-id="{{ $assignment->id }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="term" value="{{ $term }}">

                            <table class="table table-hover align-middle mb-0 grade-sheet-table">
                                <thead>
                                    <tr>
                                        <th style="width: 20px;">#</th>
                                        <th>Student</th>
                                        <th>Student ID</th>
                                        @if($isMapehGroup)
                                        @foreach($gradeSubjects as $gradeSubject)
                                        <th class="grade-column-header">{{ str_replace('MAPEH - ', '', $gradeSubject->name) }}</th>
                                        @endforeach
                                        <th class="average-column-header">Average</th>
                                        @else
                                        <th class="grade-column-header">{{ $assignment->subject->name ?? ($gradeSubjects->first()->name ?? 'Subject') }}</th>
                                        @endif
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
                                        <td class="text-muted">{{ $loop->iteration }}</td>
                                        <td>{{ $student->last_name }}, {{ $student->first_name }}</td>
                                        <td>{{ $student->custom_id ?? 'N/A' }}</td>
                                        @if($isMapehGroup)
                                        @foreach($gradeSubjects as $gradeSubject)
                                        @php
                                        $entry = $studentEntries->get($gradeSubject->id);
                                        $fieldLocked = $sheetLocked || ($entry && $entry->status !== 'draft');
                                        $isApproved = $entry && ($entry->status === 'approved' || ($entry->approved_by !== null && $entry->approved_at !== null));
                                        @endphp
                                        <td class="grade-column-cell">
                                            <input
                                                type="number"
                                                name="grade_values[{{ $student->id }}][{{ $gradeSubject->id }}]"
                                                class="form-control form-control-sm js-sheet-field grade-sheet-input js-mapeh-grade grade-entry-input {{ ($isApproved || $quarterApproved) ? 'approved-grade quarter-approved' : '' }}"
                                                data-student-id="{{ $student->id }}"
                                                min="50"
                                                max="100"
                                                step="0.01"
                                                value="{{ old('grade_values.' . $student->id . '.' . $gradeSubject->id, $formatGradeValue($entry->grade_value ?? null)) }}"
                                                {{ $fieldLocked ? 'disabled data-lock="1"' : 'disabled' }}>
                                        </td>
                                        @endforeach
                                        <td class="average-column-cell">
                                            <input
                                                type="text"
                                                class="form-control form-control-sm grade-sheet-input average-field js-mapeh-average average-entry-input {{ $quarterApproved ? 'quarter-approved' : '' }}"
                                                data-student-id="{{ $student->id }}"
                                                value=""
                                                readonly
                                                disabled>
                                        </td>
                                        @else
                                        @php
                                        $singleSubject = $gradeSubjects->first();
                                        $singleSubjectId = $singleSubject->id ?? null;
                                        $entry = $singleSubjectId ? $studentEntries->get($singleSubjectId) : null;
                                        $fieldLocked = $sheetLocked || ($entry && $entry->status !== 'draft');
                                        $isApproved = $entry && ($entry->status === 'approved' || ($entry->approved_by !== null && $entry->approved_at !== null));
                                        @endphp
                                        <td class="grade-column-cell">
                                            <input
                                                type="number"
                                                name="grade_values[{{ $student->id }}][{{ $singleSubjectId }}]"
                                                class="form-control form-control-sm js-sheet-field grade-sheet-input grade-entry-input {{ ($isApproved || $quarterApproved) ? 'approved-grade quarter-approved' : '' }}"
                                                data-student-id="{{ $student->id }}"
                                                min="50"
                                                max="100"
                                                step="0.01"
                                                value="{{ old('grade_values.' . $student->id . '.' . $singleSubjectId, $formatGradeValue($entry->grade_value ?? null)) }}"
                                                {{ $fieldLocked ? 'disabled data-lock="1"' : 'disabled' }}>
                                        </td>
                                        @endif
                                        <td>
                                            <input
                                                type="text"
                                                name="faculty_remarks[{{ $student->id }}]"
                                                class="form-control form-control-sm js-sheet-field grade-sheet-input {{ $quarterApproved ? 'quarter-approved' : '' }}"
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
        var termInput = form ? form.querySelector('input[name="term"]') : null;
        var assignmentId = form ? form.getAttribute('data-assignment-id') : '';
        var termValue = termInput ? termInput.value : '';
        var draftKey = 'grade-sheet-draft:' + assignmentId + ':' + termValue;
        var initialGradeValues = {};

        function collectInitialGradeValues() {
            if (!form) {
                return;
            }

            form.querySelectorAll('input.js-sheet-field[type="number"]').forEach(function(field) {
                initialGradeValues[field.name] = field.value;
            });
        }

        function updateUnsavedState(field) {
            if (!field || field.type !== 'number') {
                return;
            }

            var initialValue = Object.prototype.hasOwnProperty.call(initialGradeValues, field.name) ? initialGradeValues[field.name] : '';
            var currentValue = field.value;

            if (String(currentValue) !== String(initialValue)) {
                field.classList.add('unsaved-draft');
            } else {
                field.classList.remove('unsaved-draft');
            }
        }

        function clearUnsavedStates() {
            if (!form) {
                return;
            }

            form.querySelectorAll('input.js-sheet-field[type="number"].unsaved-draft').forEach(function(field) {
                field.classList.remove('unsaved-draft');
            });
        }

        function loadDraftCache() {
            if (!form || !assignmentId || !termValue) {
                return;
            }

            var raw = localStorage.getItem(draftKey);
            if (!raw) {
                return;
            }

            try {
                var cachedValues = JSON.parse(raw);
                Object.keys(cachedValues).forEach(function(fieldName) {
                    var field = form.querySelector('[name="' + fieldName.replace(/"/g, '\\"') + '"]');
                    if (!field) {
                        return;
                    }

                    if (field.value === '' || field.classList.contains('js-sheet-field')) {
                        field.value = cachedValues[fieldName];
                    }

                    updateUnsavedState(field);
                });
            } catch (error) {
                localStorage.removeItem(draftKey);
            }
        }

        function saveDraftCache() {
            if (!form || !assignmentId || !termValue) {
                return;
            }

            var values = {};
            var gradeFields = form.querySelectorAll('input.js-sheet-field[type="number"]');

            gradeFields.forEach(function(field) {
                values[field.name] = field.value;
            });

            localStorage.setItem(draftKey, JSON.stringify(values));
        }

        function normalizeGradeInput(field, preserveCaret) {
            if (!field || field.type !== 'number') {
                return;
            }

            var caretPos = preserveCaret && typeof field.selectionStart === 'number' ? field.selectionStart : null;

            if (field.value === '' || field.value === null) {
                field.setCustomValidity('');
                field.classList.remove('is-invalid');
                field.dataset.decimalMode = '';
                return;
            }

            var raw = String(field.value).replace(',', '.').replace(/[^0-9.]/g, '');
            if (raw === '' || raw === '.') {
                field.value = '';
                field.setCustomValidity('');
                field.classList.remove('is-invalid');
                return;
            }

            var firstDot = raw.indexOf('.');
            if (firstDot !== -1) {
                raw = raw.slice(0, firstDot + 1) + raw.slice(firstDot + 1).replace(/\./g, '');

                var parts = raw.split('.');
                if ((parts[0] || '').length < 2) {
                    raw = parts[0] || '';
                    parts = [raw, ''];
                }
                if (parts.length > 1) {
                    parts[1] = parts[1].slice(0, 2);
                    raw = parts[0] + (parts[1].length ? '.' + parts[1] : '.');
                }
            }

            var numericParts = raw.split('.');
            var intPart = numericParts[0] || '';
            var decPart = numericParts[1] || '';

            if (intPart.length > 2 && intPart !== '100') {
                raw = '100';
                intPart = '100';
                decPart = '';
            }

            if (intPart === '100' && decPart.length > 0) {
                raw = '100';
            }

            if (field.value !== raw) {
                field.value = raw;
            }

            var value = parseFloat(raw);
            if (!Number.isFinite(value)) {
                field.value = '';
                field.setCustomValidity('');
                field.classList.remove('is-invalid');
                return;
            }

            if (value < 50) {
                field.setCustomValidity('This field must not be less than 50.');
                field.classList.add('is-invalid');
                return;
            }

            if (value > 100) {
                field.setCustomValidity('This field must not be more than 100.');
                field.classList.add('is-invalid');
                return;
            }

            if (String(field.value || '').indexOf('.') !== -1) {
                field.dataset.pendingDecimal = '';
                field.dataset.decimalMode = '1';
            }

            field.setCustomValidity('');
            field.classList.remove('is-invalid');

            if (caretPos !== null && document.activeElement === field) {
                var nextPos = Math.min(caretPos, String(field.value || '').length);
                field.setSelectionRange(nextPos, nextPos);
            }
        }

        function blockInvalidGradeKey(field, event) {
            if (!field || field.type !== 'number') {
                return;
            }

            var key = event.key;
            var code = event.code;
            var keyCode = event.keyCode || event.which;
            var allowedControlKeys = ['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight', 'Home', 'End'];

            if (event.ctrlKey || event.metaKey || allowedControlKeys.indexOf(key) !== -1) {
                return;
            }

            var isDotKey = key === '.' || key === ',' || key === 'Decimal' || code === 'NumpadDecimal' || code === 'Period' || keyCode === 110 || keyCode === 190;
            if (isDotKey) {
                var currentWithSelection = String(field.value || '');
                var selStart = typeof field.selectionStart === 'number' ? field.selectionStart : 0;
                var selEnd = typeof field.selectionEnd === 'number' ? field.selectionEnd : selStart;
                var nextWithDot = currentWithSelection.slice(0, selStart) + '.' + currentWithSelection.slice(selEnd);

                if ((nextWithDot.match(/\./g) || []).length > 1) {
                    event.preventDefault();
                    return;
                }

                var intPartForDot = (currentWithSelection.split('.')[0] || '').replace(/[^0-9]/g, '');
                if (intPartForDot === '100') {
                    event.preventDefault();
                    return;
                }

                if (intPartForDot.length < 2) {
                    event.preventDefault();
                    return;
                }

                field.dataset.pendingDecimal = '1';
                field.dataset.decimalMode = '1';

                // Let browser insert the decimal naturally to avoid focus jump issues.
                return;
            }

            if (!/^[0-9]$/.test(key)) {
                event.preventDefault();
                return;
            }

            // If user already started decimal entry before, let them continue decimals
            // even when browser temporarily drops trailing dot after edits.
            if (field.dataset.decimalMode === '1') {
                var decimalCurrent = String(field.value || '');
                var decimalIntPart = (decimalCurrent.split('.')[0] || '').replace(/[^0-9]/g, '');
                if (decimalCurrent.indexOf('.') === -1 && decimalIntPart.length >= 2 && decimalIntPart !== '100') {
                    event.preventDefault();
                    field.value = decimalCurrent + '.' + key;
                    field.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
                    return;
                }
            }

            if (field.dataset.pendingDecimal === '1') {
                var pendingCurrent = String(field.value || '');
                var pendingIntPart = pendingCurrent.split('.')[0] || '';
                if (pendingCurrent.indexOf('.') === -1 && pendingIntPart.length >= 2 && pendingIntPart !== '100') {
                    event.preventDefault();
                    field.value = pendingCurrent + '.' + key;
                    field.dataset.pendingDecimal = '';
                    field.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
                    return;
                }
            }

            var start = typeof field.selectionStart === 'number' ? field.selectionStart : 0;
            var end = typeof field.selectionEnd === 'number' ? field.selectionEnd : 0;
            var current = String(field.value || '');
            var next = current.slice(0, start) + key + current.slice(end);

            if (!/^\d*\.?\d*$/.test(next)) {
                event.preventDefault();
                return;
            }

            var parts = next.split('.');
            var intPart = parts[0] || '';
            var decPart = parts[1] || '';

            if ((next.match(/\./g) || []).length > 1) {
                event.preventDefault();
                return;
            }

            if (decPart.length > 2) {
                event.preventDefault();
                return;
            }

            if (intPart.length > 3) {
                event.preventDefault();
                return;
            }

            if (intPart.length === 3 && intPart !== '100') {
                event.preventDefault();
                return;
            }

            if (intPart.length > 2 && intPart !== '100' && decPart.length === 0) {
                event.preventDefault();
                return;
            }

            if (intPart === '100' && decPart.length > 0) {
                event.preventDefault();
                return;
            }

            field.dataset.pendingDecimal = '';
        }

        function setupLogoutDraftClear() {
            var namespace = 'grade-sheet-draft:';

            function clearGradeDrafts() {
                Object.keys(localStorage).forEach(function(key) {
                    if (key.indexOf(namespace) === 0) {
                        localStorage.removeItem(key);
                    }
                });
            }

            document.querySelectorAll('a[href*="logout"], form[action*="logout"]').forEach(function(el) {
                var eventName = el.tagName.toLowerCase() === 'form' ? 'submit' : 'click';
                el.addEventListener(eventName, clearGradeDrafts);
            });
        }

        collectInitialGradeValues();
        loadDraftCache();
        setupLogoutDraftClear();

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
                averageField.value = String(Math.round(average));
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
        form.querySelectorAll('input.js-sheet-field[type="number"]').forEach(function(field) {
            updateUnsavedState(field);
        });

        fields
            .filter(function(field) {
                return field.type === 'number';
            })
            .forEach(function(field) {
                field.addEventListener('input', function() {
                    normalizeGradeInput(field, true);
                    updateUnsavedState(field);
                    saveDraftCache();
                });

                field.addEventListener('blur', function() {
                    normalizeGradeInput(field);
                    field.reportValidity();
                    field.dataset.pendingDecimal = '';
                    field.dataset.decimalMode = '';
                    updateUnsavedState(field);
                    saveDraftCache();
                });

                field.addEventListener('keydown', function(event) {
                    blockInvalidGradeKey(field, event);
                });
            });

        form.addEventListener('submit', function(event) {
            var hasInvalid = false;

            fields
                .filter(function(field) {
                    return field.type === 'number';
                })
                .forEach(function(field) {
                    normalizeGradeInput(field);
                    if (!field.checkValidity()) {
                        hasInvalid = true;
                    }
                });

            if (hasInvalid) {
                event.preventDefault();
                form.reportValidity();
                return;
            }

            localStorage.removeItem(draftKey);
            clearUnsavedStates();
        });

        var isEditMode = form.getAttribute('data-has-errors') === '1';

        function applyEditMode() {
            form.classList.toggle('sheet-readonly', !isEditMode);
            fields.forEach(function(field) {
                if (field.hasAttribute('data-lock')) {
                    return;
                }
                field.disabled = !isEditMode;
            });

            var viewLabel = toggleBtn.getAttribute('data-view-label') || 'Edit';
            var viewIcon = toggleBtn.getAttribute('data-view-icon') || 'fa-pen';

            toggleBtn.innerHTML = isEditMode ?
                '<i class="fas fa-times me-1"></i>Cancel Edit' :
                '<i class="fas ' + viewIcon + ' me-1"></i>' + viewLabel;

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