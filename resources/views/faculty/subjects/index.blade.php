@extends('layouts.app')

@section('title', 'Subjects - Faculty Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@vite(['resources/css/layouts/dashboard-roles/dashboard-faculty.css'])

<div class="dashboard-container" id="faculty-subjects-page">
    <div class="container-fluid px-4">
        <div class="row">

            <main class="col-12">
                <div class="welcome-card mb-4">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-8">
                            <h4 class="mb-2">
                                <i class="fas fa-book-open me-2"></i>
                                Subjects Overview
                            </h4>
                            <p class="mb-0 opacity-90">Browse available subjects with filters by type and grade level.</p>
                        </div>
                    </div>
                </div>

                <div class="card mb-4 p-3">
                    <form method="GET" action="{{ route('faculty.subjects.index') }}" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label text-success">Search</label>
                            <input type="text" name="search" class="form-control" value="{{ $filters['search'] ?? '' }}" placeholder="Subject name or description">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-success">Subject Type</label>
                            <select name="subject_type" class="form-select">
                                <option value="">All Types</option>
                                @foreach($subjectTypes as $key => $label)
                                    <option value="{{ $key }}" {{ ($filters['subject_type'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-success">Grade Level</label>
                            <select name="grade_level" class="form-select">
                                <option value="">All Levels</option>
                                @foreach($filterGradeLevels as $key => $label)
                                    <option value="{{ $key }}" {{ ($filters['grade_level'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn btn-green w-100">Apply Filters</button>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <a href="{{ route('faculty.subjects.index') }}" class="btn btn-outline-success w-100">Reset</a>
                        </div>
                    </form>
                </div>

                <div class="faculty-management-card faculty-management-table">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 text-success">
                            <i class="fas fa-table me-2 text-success"></i>
                            Subjects Overview
                        </h5>
                        <span class="badge bg-success bg-opacity-75">{{ $subjects->total() }} subject{{ $subjects->total() === 1 ? '' : 's' }}</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subjects as $subject)
                                    @php
                                        $detailId = 'subject-details-' . $subject->id;
                                    @endphp
                                    <tr>
                                        <td>
                                            <button
                                                type="button"
                                                class="btn btn-link p-0 subject-name-link fw-semibold text-decoration-none"
                                                data-collapse-target="#{{ $detailId }}"
                                                aria-expanded="false"
                                                aria-controls="{{ $detailId }}"
                                            >
                                                {{ $subject->name }}
                                            </button>
                                            <small class="text-muted">{{ $subject->description ?: 'No description provided' }}</small>
                                        </td>
                                        <td class="text-capitalize">
                                            {{ $subject->subject_type ? ($subjectTypes[$subject->subject_type] ?? ucfirst($subject->subject_type)) : '—' }}
                                        </td>
                                        <td>
                                            @php
                                                $mappedLevels = $subject->gradeLevels->pluck('grade_level')->unique()->sort()->values();
                                            @endphp
                                            @if($mappedLevels->isEmpty())
                                                {{ $gradeLevels[$subject->grade_level] ?? strtoupper($subject->grade_level) }}
                                            @elseif($mappedLevels->count() === 6)
                                                All Levels
                                            @else
                                                Grade {{ $mappedLevels->implode(', ') }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="subject-detail-row">
                                        <td colspan="3" class="p-0 border-0">
                                            <div class="collapse" id="{{ $detailId }}">
                                                <div class="p-3">
                                                    @forelse($subject->sectionTeachers as $assignment)
                                                        <div class="mb-3 subject-section-card">
                                                            <div class="fw-semibold text-success">
                                                                {{ optional($assignment->section)->name ?? 'Section' }}
                                                                @if(optional($assignment->section)->grade_level)
                                                                    <span class="text-muted">(Grade {{ $assignment->section->grade_level }})</span>
                                                                @endif
                                                            </div>
                                                            <div class="row g-2 mt-1">
                                                                <div class="col-md-4">
                                                                    <span class="text-success fw-bold">Schedule:</span>
                                                                    @if($assignment->day_of_week && $assignment->start_time && $assignment->end_time)
                                                                        {{ $assignment->day_of_week }} {{ $assignment->start_time }}-{{ $assignment->end_time }}
                                                                    @else
                                                                        {{ $assignment->schedule ?: '—' }}
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <span class="text-success fw-bold">Room:</span>
                                                                    {{ $assignment->room ?: '—' }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <div class="text-muted">No assigned sections for this subject.</div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">
                                            <div class="faculty-management-empty text-success">
                                                <i class="fas fa-book text-success"></i>
                                                <h5 class="fw-semibold mb-2 text-success">No subjects found</h5>
                                                <p class="mb-0 text-success">Try adjusting your filters to find more results.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $subjects->links() }}
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var subjectButtons = document.querySelectorAll('#faculty-subjects-page .subject-name-link');
    if (!window.bootstrap || !subjectButtons.length) {
        return;
    }

    subjectButtons.forEach(function (button) {
        button.addEventListener('click', function (event) {
            event.preventDefault();

            var targetSelector = button.getAttribute('data-collapse-target');
            if (!targetSelector) {
                return;
            }

            var target = document.querySelector(targetSelector);
            if (!target) {
                return;
            }

            var collapseInstance = bootstrap.Collapse.getOrCreateInstance(target, { toggle: false });
            var isShown = target.classList.contains('show');

            if (isShown) {
                collapseInstance.hide();
                button.setAttribute('aria-expanded', 'false');
            } else {
                collapseInstance.show();
                button.setAttribute('aria-expanded', 'true');
            }
        });
    });
});
</script>
@endpush

