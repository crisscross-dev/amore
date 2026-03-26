<div class="faculty-management-card p-4 mb-3">
    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
        <div>
            <div class="form-label mb-2">Term</div>
            <span class="badge bg-success px-3 py-2">{{ $term }}</span>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-outline-success" id="toggleSheetEditBtn">
                <i class="fas fa-pen me-1"></i>Edit
            </button>
            <form action="{{ route('admin.grade-approvals.approve', $grade) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <button class="btn btn-success">
                    <i class="fas fa-check me-1"></i>Approve Sheet
                </button>
            </form>
        </div>
    </div>
</div>

<div class="faculty-management-card faculty-management-table">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 text-success">
            @php
            $isMapehGroup = $gradeSubjects->count() > 1;
            $formatGradeValue = function ($value) {
            if ($value === null || $value === '') {
            return '';
            }

            $formatted = rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
            return $formatted === '' ? '0' : $formatted;
            };
            @endphp
            <i class="fas fa-users me-2"></i>Student Grades in {{ $isMapehGroup ? 'MAPEH' : ($assignment->subject->name ?? 'N/A') }}
        </h5>
        <span class="badge bg-success bg-opacity-75">{{ $students->count() }} student{{ $students->count() === 1 ? '' : 's' }}</span>
    </div>

    @if($students->isEmpty())
    <div class="faculty-management-empty">
        <i class="fas fa-users"></i>
        <h5 class="fw-semibold mb-2 text-success">No students in this section</h5>
        <p class="mb-0">This uploaded sheet has no students to display.</p>
    </div>
    @else
    <div class="table-responsive">
        <form action="{{ route('admin.grade-approvals.update', $grade) }}" method="POST" id="sheetUpdateForm" class="sheet-readonly" data-has-errors="{{ $errors->any() ? '1' : '0' }}">
            @csrf
            @method('PATCH')
            <input type="hidden" name="term" value="{{ $term }}">

            <table class="table table-hover align-middle mb-0 grade-sheet-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Student ID</th>
                        @if($isMapehGroup)
                        @foreach($gradeSubjects as $gradeSubject)
                        <th style="width: 120px;">{{ str_replace('MAPEH - ', '', $gradeSubject->name) }}</th>
                        @endforeach
                        <th style="width: 120px;">Average</th>
                        @else
                        <th style="width: 130px;">{{ $assignment->subject->name ?? ($gradeSubjects->first()->name ?? 'Subject') }}</th>
                        @endif
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    @php
                    $studentEntries = $gradeEntriesByStudent->get($student->id, collect());
                    $remarkSource = $studentEntries->first(function ($entry) {
                    return filled($entry->faculty_remark ?? null);
                    });
                    @endphp
                    <tr>
                        <td>{{ $student->last_name }}, {{ $student->first_name }}</td>
                        <td>{{ $student->custom_id ?? 'N/A' }}</td>
                        @if($isMapehGroup)
                        @foreach($gradeSubjects as $gradeSubject)
                        @php
                        $entry = $studentEntries->get($gradeSubject->id);
                        @endphp
                        <td>
                            <input
                                type="number"
                                name="grade_values[{{ $student->id }}][{{ $gradeSubject->id }}]"
                                class="form-control form-control-sm js-sheet-field grade-sheet-input js-mapeh-grade"
                                data-student-id="{{ $student->id }}"
                                min="50"
                                max="100"
                                step="0.01"
                                value="{{ old('grade_values.' . $student->id . '.' . $gradeSubject->id, $formatGradeValue($entry->grade_value ?? null)) }}"
                                disabled>
                        </td>
                        @endforeach
                        <td>
                            <input
                                type="text"
                                class="form-control form-control-sm grade-sheet-input js-mapeh-average"
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
                        @endphp
                        <td>
                            <input
                                type="number"
                                name="grade_values[{{ $student->id }}][{{ $singleSubjectId }}]"
                                class="form-control form-control-sm js-sheet-field grade-sheet-input"
                                data-student-id="{{ $student->id }}"
                                min="50"
                                max="100"
                                step="0.01"
                                value="{{ old('grade_values.' . $student->id . '.' . $singleSubjectId, $formatGradeValue($entry->grade_value ?? null)) }}"
                                disabled>
                        </td>
                        @endif
                        <td>
                            <input
                                type="text"
                                name="faculty_remarks[{{ $student->id }}]"
                                class="form-control form-control-sm js-sheet-field grade-sheet-input"
                                value="{{ old('faculty_remarks.' . $student->id, $remarkSource->faculty_remark ?? '') }}"
                                disabled>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="d-none mt-3" id="sheetSaveActions">
                <button class="btn btn-green"><i class="fas fa-save me-1"></i>Save Grade Sheet</button>
            </div>
        </form>
    </div>
    @endif
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
                averageField.value = String(Math.round(total / numericValues.length));
            }

            var studentIds = Array.from(new Set(gradeInputs.map(function(input) {
                return input.getAttribute('data-student-id');
            })));

            studentIds.forEach(function(studentId) {
                recalculateForStudent(studentId);
            });

            gradeInputs.forEach(function(input) {
                input.addEventListener('input', function() {
                    recalculateForStudent(input.getAttribute('data-student-id'));
                });
            });
        }

        initializeMapehAverages(form);

        if (!toggleBtn || !form) {
            return;
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

            // Keep decimal typing smooth after deleting decimal digits.
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

        var fields = Array.from(form.querySelectorAll('.js-sheet-field'));
        fields
            .filter(function(field) {
                return field.type === 'number';
            })
            .forEach(function(field) {
                field.addEventListener('input', function() {
                    normalizeGradeInput(field, true);
                });

                field.addEventListener('blur', function() {
                    normalizeGradeInput(field);
                    field.reportValidity();
                    field.dataset.pendingDecimal = '';
                    field.dataset.decimalMode = '';
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
            }
        });

        var isEditMode = form.getAttribute('data-has-errors') === '1';

        function applyEditMode() {
            form.classList.toggle('sheet-readonly', !isEditMode);
            fields.forEach(function(field) {
                field.disabled = !isEditMode;
            });

            toggleBtn.innerHTML = isEditMode ?
                '<i class="fas fa-times me-1"></i>Cancel Edit' :
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