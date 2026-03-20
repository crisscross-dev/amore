<form action="{{ $action }}" method="POST" class="row g-4">
    @csrf
    @if(!empty($method) && strtoupper($method) !== 'POST')
        @method($method)
    @endif

    @php
        $shsGradeLevels = $shsGradeLevels ?? ['11', '12'];
        $selectedGradeLevels = old('grade_levels');
        if (!is_array($selectedGradeLevels)) {
            $selectedGradeLevels = $subject->gradeLevels->pluck('grade_level')->all();
        }
        if (empty($selectedGradeLevels)) {
            $selectedGradeLevels = [$subject->grade_level ?? 'all'];
        }
        $showSubjectType = !empty(array_intersect($selectedGradeLevels, $shsGradeLevels)) || in_array('all', $selectedGradeLevels, true);
    @endphp

    <div class="col-md-6">
        <label class="form-label">Subject Name <span class="text-danger">*</span></label>
        <input
            type="text"
            name="name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $subject->name) }}"
            required
        >
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 {{ $showSubjectType ? '' : 'd-none' }}" data-subject-type-wrapper>
        <label class="form-label">Subject Type</label>
        <select
            name="subject_type"
            class="form-select @error('subject_type') is-invalid @enderror"
            data-subject-type-select
            {{ $showSubjectType ? '' : 'disabled' }}
        >
            <option value="" {{ old('subject_type', $subject->subject_type) ? '' : 'selected' }}>No specific type</option>
            @foreach($subjectTypes as $value => $label)
                <option value="{{ $value }}" {{ old('subject_type', $subject->subject_type) === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        <small class="text-muted">Subject type is available for SHS grade levels only.</small>
        @error('subject_type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Grade Level</label>
        <select
            name="grade_levels[]"
            class="form-select @error('grade_level') is-invalid @enderror @error('grade_levels') is-invalid @enderror"
            data-subject-grade-select
            data-shs-grades="{{ implode(',', $shsGradeLevels) }}"
            multiple
        >
            @foreach($gradeLevels as $value => $label)
                <option value="{{ $value }}" {{ in_array($value, $selectedGradeLevels, true) ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        <small class="text-muted">Select one or more grade levels. Use All Levels to apply across grades.</small>
        @error('grade_level')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        @error('grade_levels')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea
            name="description"
            rows="4"
            class="form-control @error('description') is-invalid @enderror"
            placeholder="Briefly describe the subject's focus"
        >{{ old('description', $subject->description) }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Hours per Week</label>
        <input
            type="number"
            name="hours_per_week"
            class="form-control @error('hours_per_week') is-invalid @enderror"
            value="{{ old('hours_per_week', $subject->hours_per_week) }}"
            min="1"
            max="40"
            placeholder="Optional"
        >
        <small class="text-muted">Leave blank if schedule is flexible.</small>
        @error('hours_per_week')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 d-flex justify-content-end gap-2">
        <a href="{{ route(($routeBase ?? 'admin.subjects.') . 'index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-times me-2"></i>Cancel
        </a>
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save me-2"></i>{{ $submitLabel ?? 'Save Subject' }}
        </button>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const gradeSelect = document.querySelector('[data-subject-grade-select]');
    const typeWrapper = document.querySelector('[data-subject-type-wrapper]');
    const typeSelect = document.querySelector('[data-subject-type-select]');

    if (!gradeSelect || !typeWrapper || !typeSelect) {
        return;
    }

    const shsGrades = (gradeSelect.dataset.shsGrades || '')
        .split(',')
        .map(function (value) {
            return value.trim();
        })
        .filter(function (value) {
            return value.length > 0;
        });

    const getSelectedValues = function () {
        return Array.from(gradeSelect.selectedOptions).map(function (option) {
            return option.value;
        });
    };

    const toggleSubjectType = function () {
        const selectedValues = getSelectedValues();
        const isShs = selectedValues.includes('all') || selectedValues.some(function (value) {
            return shsGrades.includes(value);
        });
        typeWrapper.classList.toggle('d-none', !isShs);
        typeSelect.disabled = !isShs;

        if (!isShs) {
            typeSelect.value = '';
        }
    };

    toggleSubjectType();
    gradeSelect.addEventListener('change', toggleSubjectType);
});
</script>
@endpush
