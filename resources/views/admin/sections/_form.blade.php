@php
$section = $section ?? new \App\Models\Section();
$schoolYears = $schoolYears ?? collect();
$subjects = $subjects ?? collect();
$selectedSubjectIds = collect(old('subject_ids', $selectedSubjectIds ?? []))
->map(fn ($id) => (int) $id)
->all();
$sectionNameValue = old('name', $section->name);
$selectedGradeLevel = old('grade_level', $section->grade_level);
$selectedAcademicYear = old('academic_year', $section->academic_year);
$modalContext = $modalContext ?? null;
$gradeLevels = ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];

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

$mapehRepresentativeByGrade = [];
$mapehComponentIdsByGrade = [];

foreach ($subjects as $subjectItem) {
$subjectGradeValue = (string) ($subjectItem->grade_level ?? '');
if (!preg_match('/(7|8|9|10|11|12)/', $subjectGradeValue, $matches)) {
continue;
}

$gradeNumber = (int) $matches[1];
if (!isset($mapehComponentNamesByGrade[$gradeNumber])) {
continue;
}

if (!in_array($subjectItem->name, $mapehComponentNamesByGrade[$gradeNumber], true)) {
continue;
}

$mapehComponentIdsByGrade[$gradeNumber][] = (int) $subjectItem->id;
if (!isset($mapehRepresentativeByGrade[$gradeNumber])) {
$mapehRepresentativeByGrade[$gradeNumber] = (int) $subjectItem->id;
}
}
@endphp

<form action="{{ $action }}" method="POST" class="section-form section-form-compact" data-section-form>
    @csrf
    @if(!empty($modalContext))
    <input type="hidden" name="modal_context" value="{{ $modalContext }}">
    @endif
    @if(!empty($method) && strtoupper($method) !== 'POST')
    @method($method)
    @endif

    <div class="section-form-shell">
        <div class="section-form-section">
            <div class="row g-2">
                <div class="col-lg-6">
                    <label class="form-label">Section Name <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        name="name"
                        class="form-control section-control @error('name') is-invalid @enderror"
                        value="{{ $sectionNameValue }}"
                        placeholder="Type section name, e.g. Rizal"
                        autocomplete="off"
                        required>
                    @error('name')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-6">
                    <label class="form-label d-flex align-items-center gap-2">
                        <span>Grade Level <span class="text-danger">*</span></span>
                    </label>
                    <select
                        name="grade_level"
                        class="form-select section-control section-select @error('grade_level') is-invalid @enderror"
                        data-section-grade-select
                        required>
                        <option value="">Select grade level</option>
                        @foreach($gradeLevels as $gradeLevel)
                        <option value="{{ $gradeLevel }}" {{ (string) $selectedGradeLevel === (string) $gradeLevel ? 'selected' : '' }}>
                            {{ $gradeLevel }}
                        </option>
                        @endforeach
                    </select>
                    @error('grade_level')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="section-form-section">
            <div class="row g-2">
                <div class="col-lg-6">
                    <label class="form-label">Academic Year</label>
                    <select name="academic_year" class="form-select section-control section-select @error('academic_year') is-invalid @enderror">
                        <option value="">Select academic year</option>
                        @forelse($schoolYears as $schoolYear)
                        <option value="{{ $schoolYear->year_name }}" {{ (string) $selectedAcademicYear === (string) $schoolYear->year_name ? 'selected' : '' }}>
                            {{ $schoolYear->year_name }}
                            @if($schoolYear->is_active)
                            (Active)
                            @endif
                        </option>
                        @empty
                        <option value="" disabled>No school years available</option>
                        @endforelse
                        @if($selectedAcademicYear && ! $schoolYears->contains('year_name', $selectedAcademicYear))
                        <option value="{{ $selectedAcademicYear }}" selected>{{ $selectedAcademicYear }}</option>
                        @endif
                    </select>
                    @error('academic_year')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-6">
                    <label class="form-label">Capacity</label>
                    <input
                        type="number"
                        name="capacity"
                        class="form-control section-control @error('capacity') is-invalid @enderror"
                        value="{{ old('capacity', $section->capacity) }}"
                        min="1"
                        max="100"
                        placeholder="Enter section capacity">
                    @error('capacity')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="section-form-section section-subject-section">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                <div>
                    <label class="form-label mb-1">Subjects <span class="text-danger">*</span></label>
                    <p class="section-help-text mb-0">Select a grade first, then choose the matching subjects.</p>
                </div>
                <span class="badge rounded-pill text-bg-light section-subject-count" data-subject-count>0 selected</span>
            </div>

            @if($subjects->isNotEmpty())
            <div class="section-subject-empty-state" data-subject-empty-state>
                <div class="section-subject-empty-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h6 class="mb-1" data-subject-empty-title>Select a grade level</h6>
                <p class="mb-0" data-subject-empty-text>Matching subjects will appear here.</p>
            </div>
            @endif

            <div class="row g-2" data-subject-grid>
                @forelse($subjects as $subject)
                @php
                $subjectGradeValue = (string) ($subject->grade_level ?? '');
                $subjectGradeNumber = '';
                if (preg_match('/(7|8|9|10|11|12)/', $subjectGradeValue, $gradeMatch)) {
                $subjectGradeNumber = $gradeMatch[1];
                }

                $gradeNumberInt = $subjectGradeNumber !== '' ? (int) $subjectGradeNumber : null;
                $isMapehComponent = $gradeNumberInt !== null
                && isset($mapehComponentNamesByGrade[$gradeNumberInt])
                && in_array($subject->name, $mapehComponentNamesByGrade[$gradeNumberInt], true);

                if ($isMapehComponent) {
                $representativeId = $mapehRepresentativeByGrade[$gradeNumberInt] ?? null;
                if ((int) $subject->id !== (int) $representativeId) {
                continue;
                }
                }

                if (strcasecmp($subject->name, 'MAPEH') === 0) {
                continue;
                }

                if ($isMapehComponent && $gradeNumberInt !== null) {
                $isChecked = count(array_intersect(
                $selectedSubjectIds,
                $mapehComponentIdsByGrade[$gradeNumberInt] ?? []
                )) > 0;
                } else {
                $isChecked = in_array((int) $subject->id, $selectedSubjectIds, true);
                }

                $subjectLabel = $subjectGradeValue ? ('Grade ' . $subjectGradeValue) : 'All Levels';
                $subjectTitle = $isMapehComponent ? 'MAPEH' : $subject->name;
                $mapehParts = $isMapehComponent && $gradeNumberInt !== null
                ? ($mapehDisplayPartsByGrade[$gradeNumberInt] ?? [])
                : [];
                @endphp
                <div class="col-md-6 col-xl-4 section-subject-option-wrap {{ $isChecked ? 'is-selected' : '' }}" data-subject-option data-subject-grade="{{ $subjectGradeNumber }}">
                    <label class="section-subject-option {{ $isChecked ? 'is-selected' : '' }}">
                        <input
                            class="form-check-input section-subject-check visually-hidden"
                            type="checkbox"
                            name="subject_ids[]"
                            value="{{ $subject->id }}"
                            id="subject_{{ $subject->id }}"
                            data-subject-checkbox
                            {{ $isChecked ? 'checked' : '' }}>
                        <span class="section-subject-option-body">
                            <span class="d-flex justify-content-between align-items-start gap-2">
                                <span class="fw-semibold text-dark">{{ $subjectTitle }}</span>
                                <span class="badge rounded-pill text-bg-light section-subject-badge">{{ $subjectLabel }}</span>
                            </span>
                            @if(!empty($mapehParts))
                            <span class="section-mapeh-parts mt-1 d-block">
                                @foreach($mapehParts as $part)
                                <span class="section-mapeh-part-item">{{ $part }}</span>
                                @endforeach
                            </span>
                            @endif
                        </span>
                    </label>
                </div>
                @empty
                <div class="col-12">
                    <div class="section-subject-empty-state section-subject-empty-state-static">
                        <div class="section-subject-empty-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <h6 class="mb-1">No active subjects available</h6>
                        <p class="mb-0">Create active subjects before adding a section.</p>
                    </div>
                </div>
                @endforelse
            </div>

            @error('subject_ids')
            <div class="text-danger small mt-2">{{ $message }}</div>
            @enderror
            @error('subject_ids.*')
            <div class="text-danger small mt-2">{{ $message }}</div>
            @enderror
        </div>

        <div class="section-form-section">
            <div class="row g-2">
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea
                        name="description"
                        rows="4"
                        class="form-control section-control @error('description') is-invalid @enderror"
                        placeholder="Add a short note about this section.">{{ old('description', $section->description) }}</textarea>
                    @error('description')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="section-form-footer">
            <label class="section-active-check section-active-box" for="is_active">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $section->is_active ?? true) ? 'checked' : '' }}>
                <span class="section-active-text">Active section</span>
            </label>

            <div class="d-flex justify-content-end gap-2">
                @if(!empty($isModal))
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
                @else
                <a href="{{ route(($routeBase ?? 'admin.sections.') . 'index') }}" class="btn btn-outline-secondary">
                    Cancel
                </a>
                @endif
                <button type="submit" class="btn btn-success section-submit-btn" data-submit-button data-loading-label="Saving section...">
                    <i class="fas fa-save me-2"></i>{{ $submitLabel ?? 'Save Section' }}
                </button>
            </div>
        </div>
    </div>
</form>

@push('styles')
<style>
    .section-form.section-form-compact .section-form-shell {
        display: grid;
        gap: 0.8rem;
    }

    .section-form.section-form-compact .section-form-section {
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 1rem;
        padding: 0.9rem 1rem;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.04);
    }

    .section-form-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding-top: 0.25rem;
        flex-wrap: wrap;
    }

    .section-form.section-form-compact .section-control {
        min-height: 2.7rem;
        border-radius: 0.85rem;
        border-color: rgba(15, 23, 42, 0.12);
        box-shadow: none;
    }

    .section-control:focus {
        border-color: #1f8f4d;
        box-shadow: 0 0 0 0.2rem rgba(31, 143, 77, 0.12);
    }

    .section-select {
        background-position: right 1rem center;
    }

    .section-field-tip,
    .section-help-text {
        font-size: 0.85rem;
        color: #6b7280;
    }

    .section-subject-section {
        position: relative;
    }

    .section-subject-count {
        font-weight: 600;
        padding: 0.35rem 0.7rem;
        border: 1px solid rgba(15, 23, 42, 0.08);
    }

    .section-subject-empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 1.5rem 1rem;
        border: 1px dashed rgba(15, 23, 42, 0.12);
        border-radius: 1rem;
        background: rgba(15, 23, 42, 0.02);
        color: #4b5563;
        margin-bottom: 1rem;
    }

    .section-subject-empty-state-static {
        margin-bottom: 0;
    }

    .section-subject-empty-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(31, 143, 77, 0.12);
        color: #1f8f4d;
        margin-bottom: 0.75rem;
        font-size: 1.1rem;
    }

    .section-subject-option-wrap {
        transition: transform 0.2s ease, opacity 0.2s ease;
    }

    .section-subject-option-wrap.is-hidden {
        display: none !important;
    }

    .section-subject-option {
        height: 100%;
        display: flex;
        gap: 0.7rem;
        align-items: flex-start;
        padding: 0.85rem;
        border: 1px solid rgba(15, 23, 42, 0.1);
        border-radius: 0.9rem;
        background: #fff;
        cursor: pointer;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
    }

    .section-subject-option:hover {
        border-color: rgba(31, 143, 77, 0.28);
        box-shadow: 0 12px 24px rgba(31, 143, 77, 0.08);
        transform: translateY(-1px);
    }

    .section-subject-option-wrap.is-selected .section-subject-option {
        border-color: #1f8f4d;
        box-shadow: 0 8px 20px rgba(31, 143, 77, 0.16);
        background: #e7f8ee;
    }

    .section-active-box {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        border: 1px solid rgba(15, 23, 42, 0.16);
        border-radius: 0.8rem;
        background: #ffffff;
        padding: 0.55rem 0.85rem;
        cursor: pointer;
        user-select: none;
        min-height: 2.55rem;
    }

    .section-active-box:hover {
        border-color: rgba(31, 143, 77, 0.45);
        background: rgba(31, 143, 77, 0.06);
    }

    .section-active-box .form-check-input {
        margin: 0;
        border-color: rgba(15, 23, 42, 0.28);
    }

    .section-active-box .form-check-input:checked {
        background-color: #1f8f4d;
        border-color: #1f8f4d;
    }

    .section-active-text {
        color: #1f2937;
        font-weight: 600;
        line-height: 1;
    }

    .section-subject-check.visually-hidden {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        padding: 0 !important;
        margin: -1px !important;
        overflow: hidden !important;
        clip: rect(0, 0, 0, 0) !important;
        white-space: nowrap !important;
        border: 0 !important;
    }

    .section-subject-option-body {
        flex: 1 1 auto;
        min-width: 0;
    }

    .section-mapeh-parts {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
    }

    .section-mapeh-part-item {
        display: inline-flex;
        align-items: center;
        padding: 0.15rem 0.5rem;
        border-radius: 999px;
        font-size: 0.72rem;
        color: #1f8f4d;
        background: rgba(31, 143, 77, 0.1);
    }

    .section-subject-badge {
        font-size: 0.72rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .section-submit-btn {
        min-width: 11rem;
    }

    @media (max-width: 767.98px) {
        .section-form-section {
            padding: 1rem;
            border-radius: 1rem;
        }

        .section-form-footer {
            align-items: stretch;
        }

        .section-submit-btn {
            width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('[data-section-form]').forEach(function(form) {
            const gradeSelect = form.querySelector('[data-section-grade-select]');
            const subjectOptions = Array.from(form.querySelectorAll('[data-subject-option]'));
            const subjectCount = form.querySelector('[data-subject-count]');
            const emptyState = form.querySelector('[data-subject-empty-state]');
            const emptyTitle = form.querySelector('[data-subject-empty-title]');
            const emptyText = form.querySelector('[data-subject-empty-text]');
            const submitButton = form.querySelector('[data-submit-button]');

            const extractGradeNumber = function(value) {
                const match = (value || '').match(/(7|8|9|10|11|12)/);
                return match ? match[1] : '';
            };

            const updateCheckedState = function() {
                let checkedCount = 0;

                subjectOptions.forEach(function(option) {
                    const checkbox = option.querySelector('[data-subject-checkbox]');
                    if (!checkbox) {
                        return;
                    }

                    option.classList.toggle('is-selected', checkbox.checked);
                    if (checkbox.checked) {
                        checkedCount += 1;
                    }
                });

                if (subjectCount) {
                    subjectCount.textContent = checkedCount + (checkedCount === 1 ? ' selected' : ' selected');
                }
            };

            const filterSubjects = function() {
                const selectedGrade = extractGradeNumber(gradeSelect ? gradeSelect.value : '');
                let visibleCount = 0;

                subjectOptions.forEach(function(option) {
                    const optionGrade = option.getAttribute('data-subject-grade') || '';
                    const shouldShow = selectedGrade ? (!optionGrade || optionGrade === selectedGrade) : false;
                    option.classList.toggle('is-hidden', !shouldShow);

                    if (shouldShow) {
                        visibleCount += 1;
                    }
                });

                if (emptyState) {
                    emptyState.classList.toggle('d-none', visibleCount > 0);
                }

                if (emptyTitle && emptyText) {
                    if (!selectedGrade) {
                        emptyTitle.textContent = 'Select a grade level';
                        emptyText.textContent = 'Matching subjects will appear here.';
                    } else if (visibleCount === 0) {
                        emptyTitle.textContent = 'No matching subjects';
                        emptyText.textContent = 'There are no active subjects for the selected grade level.';
                    } else {
                        emptyTitle.textContent = 'Subjects ready';
                        emptyText.textContent = 'Choose the subjects you want to assign to this section.';
                    }
                }

                updateCheckedState();
            };

            if (gradeSelect) {
                gradeSelect.addEventListener('change', filterSubjects);
            }

            subjectOptions.forEach(function(option) {
                const checkbox = option.querySelector('[data-subject-checkbox]');
                if (!checkbox) {
                    return;
                }

                checkbox.addEventListener('change', updateCheckedState);
            });

            if (submitButton) {
                form.addEventListener('submit', function() {
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' + (submitButton.dataset.loadingLabel || 'Saving...');
                });
            }

            filterSubjects();
        });

        const modalToOpen = document.querySelector('.modal[data-open-on-load="1"]');
        if (modalToOpen && window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modalToOpen).show();
        }
    });
</script>
@endpush