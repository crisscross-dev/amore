@extends('layouts.app')

@section('title', 'Subject Management - Admin Dashboard - Amore Academy')

@section('content')
<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@vite([
'resources/css/layouts/dashboard-roles/dashboard-admin.css',
'resources/css/admin/faculty-management.css',
'resources/css/admin/subject-management.css',
])

<div class="dashboard-container subject-management-page">
    <div id="subjectModalState" data-old-edit-subject-id="{{ old('edit_subject_id') }}"></div>
    <div class="container-fluid px-4">
        <div class="row">

            <main class="col-12">
                <div class="header-title d-flex align-items-center justify-content-between mb-2">
                    <h5 class="mb-2 fw-semibold text-success">
                        Subject
                    </h5>
                    <div>
                        <button type="button" class="btn btn-primary btn-m" data-bs-toggle="modal" data-bs-target="#createSubjectModal">
                            <i class="fas fa-plus-circle"></i>
                            Create New Subject
                        </button>
                    </div>
                </div>


                @if($errors->any())
                <x-ui.alert type="danger" :dismissible="true">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-ui.alert>
                @endif

                @if(!empty($importSummary))
                <div class="faculty-management-card mb-4">
                    <h5 class="mb-3">
                        <i class="fas fa-file-import me-2 text-success"></i>
                        Last Import Summary
                    </h5>
                    <div class="row g-3">
                        <div class="col-sm-3">
                            <div class="fw-semibold">Processed</div>
                            <div>{{ $importSummary['processed'] }}</div>
                        </div>
                        <div class="col-sm-3">
                            <div class="fw-semibold text-success">Created</div>
                            <div>{{ $importSummary['created'] }}</div>
                        </div>
                        <div class="col-sm-3">
                            <div class="fw-semibold text-primary">Updated</div>
                            <div>{{ $importSummary['updated'] }}</div>
                        </div>
                        <div class="col-sm-3">
                            <div class="fw-semibold text-danger">Errors</div>
                            <div>{{ count($importSummary['errors']) }}</div>
                        </div>
                    </div>
                    @if(!empty($importSummary['errors']))
                    <div class="alert alert-warning mt-3 mb-0">
                        <strong>Import warnings:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($importSummary['errors'] as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
                @endif



                <div class="faculty-management-card mb-4">
                    <form method="GET" action="{{ route($routeBase . 'index') }}" class="row g-3 align-items-end" id="subjectFiltersForm">
                        <div class="col-md-4">
                            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="form-control" placeholder="Search subjects...">
                        </div>
                        <div class="col-md-4">
                            <select name="subject_type" class="form-select js-auto-filter">
                                <option value="">Select Subject</option>
                                @foreach($subjectTypes as $value => $label)
                                <option value="{{ $value }}" {{ ($filters['subject_type'] ?? '') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="grade_level" class="form-select js-auto-filter">
                                <option value="">All grade levels</option>
                                @foreach($filterGradeLevels as $value => $label)
                                <option value="{{ $value }}" {{ ($filters['grade_level'] ?? '') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>

                <div class="faculty-management-card faculty-management-table">
                    @php
                    $subjectRows = $subjects->getCollection()
                    ->groupBy(function ($item) {
                    return strtolower(trim((string) $item->name)) . '|' . strtolower((string) $item->subject_type);
                    })
                    ->map(function ($items) {
                    $primary = $items->first();

                    $gradeLevels = $items
                    ->flatMap(function ($item) {
                    if ($item->gradeLevels->isNotEmpty()) {
                    return $item->gradeLevels->pluck('grade_level');
                    }

                    return collect([$item->grade_level]);
                    })
                    ->filter()
                    ->unique()
                    ->sort()
                    ->values();

                    $teachers = $items
                    ->flatMap(function ($item) {
                    return $item->sectionTeachers;
                    })
                    ->filter(function ($assignment) {
                    return $assignment->teacher;
                    })
                    ->groupBy('teacher_id')
                    ->map(function ($assignments) {
                    $teacher = $assignments->first()->teacher;
                    $name = collect([
                    $teacher->first_name,
                    $teacher->middle_name,
                    $teacher->last_name,
                    ])
                    ->filter(function ($part) {
                    return filled($part);
                    })
                    ->implode(' ');

                    return (object) [
                    'id' => $teacher->id,
                    'name' => $name !== '' ? $name : ($teacher->email ?? 'Unknown Teacher'),
                    'email' => $teacher->email,
                    'assignments' => $assignments->count(),
                    ];
                    })
                    ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
                    ->values();

                    return (object) [
                    'subject' => $primary,
                    'description' => $items->pluck('description')->filter()->first() ?: null,
                    'grade_levels' => $gradeLevels,
                    'teachers' => $teachers,
                    ];
                    })
                    ->values();
                    @endphp

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="fas fa-table me-2 text-success"></i>
                            Subject Overview
                        </h5>
                        <span class="badge bg-success bg-opacity-75">{{ $subjectRows->count() }} subject{{ $subjectRows->count() === 1 ? '' : 's' }}</span>
                    </div>
                    <p class="text-muted small mb-3">Double-click a subject row to view teachers currently handling that subject.</p>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Grade</th>
                                    <!-- <th>Hours/Week</th> -->
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subjectRows as $rowIndex => $row)
                                @php
                                $subject = $row->subject;
                                $mappedLevels = $row->grade_levels;
                                $modalId = 'subjectTeachersModal-' . $subject->id . '-' . $rowIndex;
                                @endphp
                                <tr class="subject-overview-row" data-subject-teachers-modal-id="{{ $modalId }}" title="Double-click to view teachers">
                                    <td>
                                        <div class="fw-semibold text-success">{{ $subject->name }}</div>
                                        <small class="text-muted">{{ $row->description ?: 'No description provided' }}</small>
                                    </td>
                                    <td class="text-capitalize">
                                        {{ $subject->subject_type ? ($subjectTypes[$subject->subject_type] ?? ucfirst($subject->subject_type)) : '—' }}
                                    </td>
                                    <td>
                                        @if($mappedLevels->isEmpty())
                                        {{ $gradeLevels[$subject->grade_level] ?? strtoupper($subject->grade_level) }}
                                        @elseif($mappedLevels->count() === 6)
                                        All Levels
                                        @else
                                        Grade {{ $mappedLevels->implode(', ') }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#editSubjectModal-{{ $subject->id }}">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <form action="{{ route($routeBase . 'destroy', $subject) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4">
                                        <div class="faculty-management-empty">
                                            <i class="fas fa-book"></i>
                                            <h5 class="fw-semibold mb-2">No subjects available yet</h5>
                                            <p class="mb-0">Create your first subject to populate the catalog.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @foreach($subjectRows as $rowIndex => $row)
                    @php
                    $subject = $row->subject;
                    $mappedLevels = $row->grade_levels;
                    $modalId = 'subjectTeachersModal-' . $subject->id . '-' . $rowIndex;
                    $gradeLabel = $mappedLevels->isEmpty()
                    ? ($gradeLevels[$subject->grade_level] ?? strtoupper($subject->grade_level))
                    : ($mappedLevels->count() === 6 ? 'All Levels' : 'Grade ' . $mappedLevels->implode(', '));
                    @endphp
                    <div class="modal fade subject-teachers-modal" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <div>
                                        <h5 class="modal-title" id="{{ $modalId }}Label">
                                            <i class="fas fa-chalkboard-teacher me-2"></i>
                                            {{ $subject->name }} Teachers
                                        </h5>
                                        <small class="text-muted">{{ $gradeLabel }}</small>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    @if($row->teachers->isEmpty())
                                    <div class="alert alert-light border mb-0">
                                        No teacher assignments found for this subject yet.
                                    </div>
                                    @else
                                    <ul class="list-group list-group-flush">
                                        @foreach($row->teachers as $teacher)
                                        <li class="list-group-item d-flex justify-content-between align-items-start gap-2 px-0">
                                            <div>
                                                <div class="fw-semibold">{{ $teacher->name }}</div>
                                                @if(!empty($teacher->email))
                                                <small class="text-muted">{{ $teacher->email }}</small>
                                                @endif
                                            </div>
                                            <span class="badge bg-light text-success border">{{ $teacher->assignments }} class{{ $teacher->assignments === 1 ? '' : 'es' }}</span>
                                        </li>
                                        @endforeach
                                    </ul>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <div class="mt-4 faculty-assignments-pagination d-flex justify-content-center">
                        {{ $subjects->links('pagination::bootstrap-5') }}
                    </div>
                </div>

                <div class="modal fade subject-create-modal" id="createSubjectModal" tabindex="-1" aria-labelledby="createSubjectModalLabel" aria-hidden="true" data-open-on-load="{{ $errors->any() && !old('edit_subject_id') && (old('name') || old('description') || old('subject_type') || old('grade_level')) ? '1' : '0' }}">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <div>
                                    <h5 class="modal-title" id="createSubjectModalLabel">
                                        <i class="fas fa-plus-circle me-2"></i>
                                        Create New Subject
                                    </h5>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @include('admin.subjects._form', [
                                'subject' => $subject,
                                'subjectTypes' => $subjectTypes,
                                'gradeLevels' => $gradeLevels,
                                'shsGradeLevels' => $shsGradeLevels,
                                'action' => route($routeBase . 'store'),
                                'method' => 'POST',
                                'submitLabel' => 'Create Subject',
                                'routeBase' => $routeBase,
                                'isModal' => true,
                                ])
                            </div>
                        </div>
                    </div>
                </div>

                @foreach($subjects as $subjectItem)
                <div class="modal fade subject-create-modal" id="editSubjectModal-{{ $subjectItem->id }}" tabindex="-1" aria-labelledby="editSubjectModalLabel-{{ $subjectItem->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <div>
                                    <h5 class="modal-title" id="editSubjectModalLabel-{{ $subjectItem->id }}">
                                        <i class="fas fa-pen-to-square me-2"></i>
                                        Edit Subject
                                    </h5>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @include('admin.subjects._form', [
                                'subject' => $subjectItem,
                                'subjectTypes' => $subjectTypes,
                                'gradeLevels' => $gradeLevels,
                                'shsGradeLevels' => $shsGradeLevels,
                                'action' => route($routeBase . 'update', $subjectItem),
                                'method' => 'PUT',
                                'submitLabel' => 'Update Subject',
                                'routeBase' => $routeBase,
                                'isModal' => true,
                                'editSubjectId' => $subjectItem->id,
                                ])
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </main>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var filterForm = document.getElementById('subjectFiltersForm');
        if (filterForm) {
            var autoFilters = filterForm.querySelectorAll('.js-auto-filter');
            autoFilters.forEach(function(selectElement) {
                selectElement.addEventListener('change', function() {
                    filterForm.submit();
                });
            });
        }

        var createModalElement = document.getElementById('createSubjectModal');
        var shouldOpenModal = createModalElement && createModalElement.getAttribute('data-open-on-load') === '1';
        if (shouldOpenModal && window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(createModalElement).show();
        }

        var subjectRows = document.querySelectorAll('[data-subject-teachers-modal-id]');
        subjectRows.forEach(function(row) {
            row.addEventListener('dblclick', function(event) {
                if (event.target.closest('button, a, form, input, select, textarea, label')) {
                    return;
                }

                var modalId = row.getAttribute('data-subject-teachers-modal-id');
                if (!modalId || !window.bootstrap || !window.bootstrap.Modal) {
                    return;
                }

                var modalElement = document.getElementById(modalId);
                if (modalElement) {
                    window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
                }
            });
        });

        var modalStateElement = document.getElementById('subjectModalState');
        var oldEditSubjectId = modalStateElement ? (modalStateElement.getAttribute('data-old-edit-subject-id') || '') : '';
        if (oldEditSubjectId && window.bootstrap && window.bootstrap.Modal) {
            var editModalElement = document.getElementById('editSubjectModal-' + oldEditSubjectId);
            if (editModalElement) {
                window.bootstrap.Modal.getOrCreateInstance(editModalElement).show();
            }
        }
    });
</script>
@endsection
