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
            <i class="fas fa-users me-2"></i>Uploaded Grade Sheet
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
                        <th style="width: 130px;">Grade</th>
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    @php
                    $entry = $gradeEntries->get($student->id);
                    @endphp
                    <tr>
                        <td>{{ $student->last_name }}, {{ $student->first_name }}</td>
                        <td>{{ $student->custom_id ?? 'N/A' }}</td>
                        <td>
                            <input
                                type="number"
                                name="grade_values[{{ $student->id }}]"
                                class="form-control form-control-sm js-sheet-field grade-sheet-input"
                                min="50"
                                max="100"
                                step="0.01"
                                value="{{ old('grade_values.' . $student->id, $entry->grade_value ?? '') }}"
                                disabled>
                        </td>
                        <td>
                            <input
                                type="text"
                                name="faculty_remarks[{{ $student->id }}]"
                                class="form-control form-control-sm js-sheet-field grade-sheet-input"
                                value="{{ old('faculty_remarks.' . $student->id, $entry->faculty_remark ?? '') }}"
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

        if (!toggleBtn || !form) {
            return;
        }

        var fields = Array.from(form.querySelectorAll('.js-sheet-field'));
        var isEditMode = form.getAttribute('data-has-errors') === '1';

        function applyEditMode() {
            form.classList.toggle('sheet-readonly', !isEditMode);
            fields.forEach(function(field) {
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