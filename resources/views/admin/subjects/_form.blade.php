<form action="{{ $action }}" method="POST" class="subject-form" data-subject-form>
    @csrf
    @if(!empty($method) && strtoupper($method) !== 'POST')
    @method($method)
    @endif
    @if(!empty($editSubjectId))
    <input type="hidden" name="edit_subject_id" value="{{ $editSubjectId }}">
    @endif

    @php
    $shsGradeLevels = $shsGradeLevels ?? ['11', '12'];
    $selectedGradeLevel = old('grade_level', $subject->grade_level);
    if (!$selectedGradeLevel && $subject->gradeLevels->isNotEmpty()) {
    $selectedGradeLevel = $subject->gradeLevels->pluck('grade_level')->sort()->first();
    }
    $gradeOptions = collect($gradeLevels)->only(['7', '8', '9', '10', '11', '12', 'all'])->all();
    $subjectNameValue = $subject->exists ? old('name', $subject->name) : '';
    $showSubjectType = in_array((string) $selectedGradeLevel, $shsGradeLevels, true) || $selectedGradeLevel === 'all';
    @endphp

    <div class="subject-form-section">

        <div class="row g-3 align-items-start subject-basic-row">
            <div class="col-lg-6">
                <label class="form-label">Subject Name <span class="text-danger">*</span></label>
                <div class="subject-input-icon-wrap">
                    <i class="fas fa-book subject-input-icon" aria-hidden="true"></i>
                    <input
                        type="text"
                        name="name"
                        class="form-control subject-modal-control ps-5 @error('name') is-invalid @enderror"
                        value="{{ $subjectNameValue }}"
                        placeholder="e.g. Mathematics 7"
                        autocomplete="off"
                        required>
                </div>
                @error('name')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-lg-6 {{ $showSubjectType ? '' : 'd-none' }}" data-subject-type-wrapper>
                <label class="form-label subject-type-label">
                    <span>Subject Type</span>
                    <button
                        type="button"
                        class="btn btn-link p-0 text-muted subject-type-help"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Subject type appears only when Grade 11, Grade 12, or All Levels is selected.">
                        <i class="fas fa-circle-question"></i>
                    </button>
                </label>
                <div class="subject-input-icon-wrap">
                    <i class="fas fa-layer-group subject-input-icon" aria-hidden="true"></i>
                    <select
                        name="subject_type"
                        class="form-select subject-modal-control subject-dropdown-select ps-5 @error('subject_type') is-invalid @enderror"
                        data-subject-type-select
                        {{ $showSubjectType ? '' : 'disabled' }}>
                        <option value="" {{ old('subject_type', $subject->subject_type) ? '' : 'selected' }}>No specific type</option>
                        @foreach($subjectTypes as $value => $label)
                        <option value="{{ $value }}" {{ old('subject_type', $subject->subject_type) === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @error('subject_type')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="subject-form-section">
        <div class="row g-3">
            <div class="col-lg-6">
                <label class="form-label d-flex align-items-center gap-2">
                    <span>Grade Level <span class="text-danger">*</span></span>
                    <i class="fas fa-graduation-cap text-muted small" aria-hidden="true"></i>
                </label>
                <div class="subject-input-icon-wrap">
                    <i class="fas fa-graduation-cap subject-input-icon" aria-hidden="true"></i>
                    <select
                        name="grade_level"
                        class="form-select subject-modal-control subject-dropdown-select subject-grade-select ps-5 @error('grade_level') is-invalid @enderror @error('grade_levels') is-invalid @enderror"
                        data-subject-grade-select
                        data-shs-grades="{{ implode(',', $shsGradeLevels) }}">
                        <option value="">Select grade level</option>
                        @foreach($gradeOptions as $value => $label)
                        <option value="{{ $value }}" {{ (string) $selectedGradeLevel === (string) $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @error('grade_level')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                @error('grade_levels')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-lg-6">
                <label class="form-label">Hours per Week</label>
                <div class="subject-input-icon-wrap">
                    <i class="fas fa-clock subject-input-icon" aria-hidden="true"></i>
                    <input
                        type="number"
                        name="hours_per_week"
                        class="form-control subject-modal-control ps-5 @error('hours_per_week') is-invalid @enderror"
                        value="{{ old('hours_per_week', $subject->hours_per_week) }}"
                        min="1"
                        max="40"
                        placeholder="e.g. 5">
                </div>
                @error('hours_per_week')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="subject-form-section subject-form-section-last">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">Description</label>
                <textarea
                    name="description"
                    rows="5"
                    maxlength="500"
                    class="form-control @error('description') is-invalid @enderror"
                    placeholder="Example: Covers foundational algebra, number sense, and problem-solving skills."
                    data-description-input>{{ old('description', $subject->description) }}</textarea>
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <small class="text-muted">Up to 500 characters.</small>
                    <small class="text-muted" data-description-count>0/500</small>
                </div>
                @error('description')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="subject-form-actions sticky-bottom">
        <div class="d-flex justify-content-end gap-2">
            @if(!empty($isModal))
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                <i class="fas fa-times me-2"></i>Cancel
            </button>
            @else
            <a href="{{ route(($routeBase ?? 'admin.subjects.') . 'index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-times me-2"></i>Cancel
            </a>
            @endif
            <button type="submit" class="btn btn-success" data-submit-button data-submit-label="{{ $submitLabel ?? 'Create Subject' }}" data-loading-label="Creating Subject...">
                <i class="fas fa-check-circle me-2"></i>{{ $submitLabel ?? 'Create Subject' }}
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const forms = Array.from(document.querySelectorAll('[data-subject-form]'));
        if (!forms.length) {
            return;
        }

        forms.forEach(function(form) {
            const gradeSelect = form.querySelector('[data-subject-grade-select]');
            const typeWrapper = form.querySelector('[data-subject-type-wrapper]');
            const typeSelect = form.querySelector('[data-subject-type-select]');
            const descriptionInput = form.querySelector('[data-description-input]');
            const descriptionCount = form.querySelector('[data-description-count]');
            const submitButton = form.querySelector('[data-submit-button]');

            if (!gradeSelect || !typeWrapper || !typeSelect) {
                return;
            }

            const shsGrades = (gradeSelect.dataset.shsGrades || '')
                .split(',')
                .map(function(value) {
                    return value.trim();
                })
                .filter(function(value) {
                    return value.length > 0;
                });

            const getSelectedValues = function() {
                return Array.from(gradeSelect.selectedOptions).map(function(option) {
                    return option.value;
                });
            };

            const toggleSubjectType = function() {
                const selectedValues = getSelectedValues();
                const isShs = selectedValues.includes('all') || selectedValues.some(function(value) {
                    return shsGrades.includes(value);
                });
                typeWrapper.classList.toggle('d-none', !isShs);
                typeSelect.disabled = !isShs;

                if (!isShs) {
                    typeSelect.value = '';
                }
            };

            const updateDescriptionCounter = function() {
                if (!descriptionInput || !descriptionCount) {
                    return;
                }

                const max = descriptionInput.getAttribute('maxlength') || 500;
                const count = descriptionInput.value.length;
                descriptionCount.textContent = count + '/' + max;
            };

            const setFieldValidationState = function(field) {
                if (!field.classList.contains('is-invalid')) {
                    if (field.value && String(field.value).trim().length > 0) {
                        field.classList.add('is-valid');
                    } else {
                        field.classList.remove('is-valid');
                    }
                }
            };

            toggleSubjectType();
            gradeSelect.addEventListener('change', toggleSubjectType);
            updateDescriptionCounter();

            const parentModal = form.closest('.modal');
            if (parentModal && parentModal.id === 'createSubjectModal' && window.bootstrap) {
                parentModal.addEventListener('shown.bs.modal', function() {
                    if (parentModal.getAttribute('data-open-on-load') === '1') {
                        return;
                    }

                    const subjectNameInput = form.querySelector('input[name="name"]');
                    if (subjectNameInput) {
                        subjectNameInput.value = '';
                    }
                });
            }

            if (descriptionInput) {
                descriptionInput.addEventListener('input', updateDescriptionCounter);
            }

            Array.from(form.querySelectorAll('.form-control, .form-select')).forEach(function(field) {
                field.addEventListener('blur', function() {
                    setFieldValidationState(field);
                });
            });

            if (submitButton) {
                form.addEventListener('submit', function() {
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' + (submitButton.dataset.loadingLabel || 'Saving...');
                });
            }

            if (window.bootstrap && window.bootstrap.Tooltip) {
                Array.from(form.querySelectorAll('[data-bs-toggle="tooltip"]')).forEach(function(el) {
                    window.bootstrap.Tooltip.getOrCreateInstance(el);
                });
            }
        });
    });
</script>
@endpush