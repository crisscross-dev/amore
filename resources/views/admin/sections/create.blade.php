@extends('layouts.app')

@section('title', 'Create Section - Admin Dashboard - Amore Academy')

@section('content')

@vite(['resources/css/layouts/dashboard-roles/dashboard-admin.css'])

@push('styles')
<style>
    .admissions-card .form-control,
    .admissions-card .form-select {
        color: rgba(33, 37, 41, 0.75);
    }

    .admissions-card .form-control::placeholder {
        color: rgba(108, 117, 125, 0.65);
    }

    .admissions-card .form-select option {
        color: rgba(33, 37, 41, 0.75);
    }
</style>
@endpush

<div class="dashboard-container">
    <div class="container-fluid px-4">
        <div class="row">
            <main class="col-12">
                <div class="welcome-card mb-4">
                    <h4 class="mb-2"><i class="fas fa-plus me-2"></i>Create Section</h4>
                    <p class="mb-0 opacity-90">Add a new section for students</p>
                </div>

                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="admissions-card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.sections.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                @php
                                $oldName = old('name');
                                $defaultSectionNames = collect(['Rizal', 'Mabini', 'Aguinaldo', 'del pilar']);
                                @endphp
                                <select name="name" class="form-select @error('name') is-invalid @enderror" required>
                                    <option value="">Select Section Name</option>
                                    @foreach($defaultSectionNames as $defaultName)
                                    <option value="{{ $defaultName }}" {{ $oldName === $defaultName ? 'selected' : '' }}>{{ $defaultName }}</option>
                                    @endforeach
                                    @if($oldName && ! $sectionNames->contains($oldName) && ! $defaultSectionNames->contains($oldName))
                                    <option value="{{ $oldName }}" selected>{{ $oldName }}</option>
                                    @endif
                                    @foreach($sectionNames as $sectionName)
                                    @continue($defaultSectionNames->contains($sectionName))
                                    <option value="{{ $sectionName }}" {{ $oldName === $sectionName ? 'selected' : '' }}>{{ $sectionName }}</option>
                                    @endforeach
                                </select>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Grade Level <span class="text-danger">*</span></label>
                                <select name="grade_level" class="form-select @error('grade_level') is-invalid @enderror" required>
                                    <option value="">Select Grade Level</option>
                                    <option value="Grade 7" {{ old('grade_level') == 'Grade 7' ? 'selected' : '' }}>Grade 7</option>
                                    <option value="Grade 8" {{ old('grade_level') == 'Grade 8' ? 'selected' : '' }}>Grade 8</option>
                                    <option value="Grade 9" {{ old('grade_level') == 'Grade 9' ? 'selected' : '' }}>Grade 9</option>
                                    <option value="Grade 10" {{ old('grade_level') == 'Grade 10' ? 'selected' : '' }}>Grade 10</option>
                                    <option value="Grade 11" {{ old('grade_level') == 'Grade 11' ? 'selected' : '' }}>Grade 11</option>
                                    <option value="Grade 12" {{ old('grade_level') == 'Grade 12' ? 'selected' : '' }}>Grade 12</option>
                                </select>
                                @error('grade_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Academic Year</label>
                                @php
                                $academicYearOptions = ['2026-2027', '2027-2028', '2028-2029', '2029-2030'];
                                $oldAcademicYear = old('academic_year');
                                @endphp
                                <select name="academic_year" class="form-select @error('academic_year') is-invalid @enderror">
                                    <option value="">Select Academic Year</option>
                                    @foreach($academicYearOptions as $academicYear)
                                    <option value="{{ $academicYear }}" {{ $oldAcademicYear === $academicYear ? 'selected' : '' }}>{{ $academicYear }}</option>
                                    @endforeach
                                    @if($oldAcademicYear && !in_array($oldAcademicYear, $academicYearOptions))
                                    <option value="{{ $oldAcademicYear }}" selected>{{ $oldAcademicYear }}</option>
                                    @endif
                                </select>
                                @error('academic_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Subjects <span class="text-danger">*</span></label>
                                <div class="form-text mb-2">Select the subjects included in this section.</div>

                                <div class="border rounded p-2" style="max-height: 220px; overflow-y: auto;">
                                    @forelse($subjects as $subject)
                                    @php
                                    $subjectGrade = (string) ($subject->grade_level ?? 'all');
                                    preg_match('/(7|8|9|10|11|12)/', $subjectGrade, $gradeMatch);
                                    $subjectGradeNumber = $gradeMatch[1] ?? '';
                                    @endphp
                                    <div class="form-check subject-option" data-subject-grade="{{ $subjectGradeNumber }}">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="subject_ids[]"
                                            value="{{ $subject->id }}"
                                            id="subject_{{ $subject->id }}"
                                            {{ in_array($subject->id, old('subject_ids', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label d-flex justify-content-between w-100" for="subject_{{ $subject->id }}">
                                            <span>{{ $subject->name }}</span>
                                            <small class="text-muted">{{ $subject->grade_level ?: 'All' }}</small>
                                        </label>
                                    </div>
                                    @empty
                                    <p class="text-muted mb-0">No active subjects available.</p>
                                    @endforelse
                                </div>

                                @error('subject_ids')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                @error('subject_ids.*')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Capacity</label>
                                <input type="number" id="capacity-input" name="capacity" class="form-control @error('capacity') is-invalid @enderror" value="{{ old('capacity') }}" min="1" max="100" step="1">
                                <div class="form-text text-danger d-none" id="capacity-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Capacity cannot exceed 100.
                                </div>
                                @error('capacity')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.sections.index') }}" class="btn btn-secondary">Cancel</a>
                                <button class="btn btn-primary" type="submit"><i class="fas fa-save me-2"></i>Create Section</button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const capacityInput = document.getElementById('capacity-input');
        const warning = document.getElementById('capacity-warning');
        const gradeLevelSelect = document.querySelector('select[name="grade_level"]');
        const subjectOptions = document.querySelectorAll('.subject-option');

        if (!capacityInput || !warning) {
            return;
        }

        const toggleWarning = () => {
            const value = Number(capacityInput.value);
            const isOverLimit = Number.isFinite(value) && value > 100;
            warning.classList.toggle('d-none', !isOverLimit);
            capacityInput.setCustomValidity(isOverLimit ? 'Capacity cannot exceed 100 students.' : '');
        };

        const extractGradeNumber = (value) => {
            const matched = (value || '').match(/(7|8|9|10|11|12)/);
            return matched ? matched[1] : '';
        };

        const filterSubjectsByGrade = () => {
            const selectedGradeNumber = extractGradeNumber(gradeLevelSelect ? gradeLevelSelect.value : '');

            subjectOptions.forEach((option) => {
                const subjectGrade = option.getAttribute('data-subject-grade');
                const isVisible = !selectedGradeNumber || !subjectGrade || subjectGrade === selectedGradeNumber;
                option.classList.toggle('d-none', !isVisible);
            });
        };

        capacityInput.addEventListener('input', toggleWarning);
        if (gradeLevelSelect) {
            gradeLevelSelect.addEventListener('change', filterSubjectsByGrade);
        }

        toggleWarning();
        filterSubjectsByGrade();
    });
</script>
@endpush