@extends('layouts.app')

@section('title', 'View Sections - Faculty Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@vite(['resources/css/layouts/dashboard-roles/dashboard-faculty.css'])

<style>
    .section-cards-wrap {
        display: grid;
        gap: 1rem;
    }

    .section-card {
        display: block;
        border: 1px solid rgba(22, 101, 52, 0.2);
        border-radius: 14px;
        background: #ffffff;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        text-decoration: none;
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
    }

    .section-card:hover {
        transform: translateY(-2px);
        border-color: rgba(22, 101, 52, 0.35);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.1);
    }

    .section-card:focus-visible {
        outline: 3px solid rgba(22, 163, 74, 0.35);
        outline-offset: 2px;
    }

    .section-card .section-card-preview {
        padding: 1rem;
    }

    .section-card .card-head {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .section-card .hierarchy-label {
        font-size: 0.72rem;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 0.1rem;
    }

    .section-card .hierarchy-value {
        color: #0f172a;
        font-weight: 600;
    }

    .section-card .subject-stack {
        margin-top: 0.85rem;
        display: grid;
        gap: 0.5rem;
    }

    .section-card .subject-item {
        border: 1px solid #dcfce7;
        background: #f8fafc;
        border-radius: 10px;
        padding: 0.65rem 0.75rem;
    }

    .section-card .subject-item strong {
        color: #14532d;
    }

    .section-card .card-expand-hint {
        color: #15803d;
        font-size: 0.8rem;
        font-weight: 600;
    }
</style>

<div class="dashboard-container">
    <div class="container-fluid px-4">
        <div class="row">

            <main class="col-12">

                <!-- Section Cards -->
                <div class="faculty-management-card faculty-management-table">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 text-success">
                            <i class="fas fa-layer-group me-2 text-success"></i>
                            Sections Overview
                        </h5>
                        <span class="badge bg-success bg-opacity-75">{{ $sections->total() }} section{{ $sections->total() === 1 ? '' : 's' }}</span>
                    </div>

                    <div class="section-cards-wrap">
                        @forelse($sections as $section)
                        @php
                        $isAdviser = (int) $section->adviser_id === (int) $user->id;
                        $facultyAssignments = $section->subjectTeachers->where('teacher_id', $user->id);
                        $gradeNumber = null;
                        if (preg_match('/(7|8|9|10|11|12)/', (string) ($section->grade_level ?? ''), $gradeMatch)) {
                        $gradeNumber = (int) $gradeMatch[1];
                        }

                        $mapehComponentNamesByGrade = [
                        7 => ['MAPEH - Music & Arts', 'MAPEH - PE & Health', 'Music & Arts', 'PE & Health'],
                        8 => ['MAPEH - Music & Arts', 'MAPEH - PE & Health', 'Music & Arts', 'PE & Health'],
                        9 => ['MAPEH - Music', 'MAPEH - Arts', 'MAPEH - PE', 'MAPEH - Health', 'Music', 'Arts', 'PE', 'Health'],
                        10 => ['MAPEH - Music', 'MAPEH - Arts', 'MAPEH - PE', 'MAPEH - Health', 'Music', 'Arts', 'PE', 'Health'],
                        ];
                        $mapehComponentNames = $mapehComponentNamesByGrade[$gradeNumber] ?? [];

                        $displayAssignments = collect();
                        $mapehAssignments = collect();

                        foreach ($facultyAssignments as $assignment) {
                        $subjectName = (string) (optional($assignment->subject)->name ?? '');
                        $normalizedSubjectName = strtolower(trim($subjectName));

                        $isMapeh =
                        str_contains($normalizedSubjectName, 'mapeh')
                        || (!empty($mapehComponentNames) && in_array($subjectName, $mapehComponentNames, true));

                        if ($isMapeh) {
                        $mapehAssignments->push($assignment);
                        continue;
                        }

                        $displayAssignments->push((object) [
                        'assignment' => $assignment,
                        'subject_name' => $subjectName ?: 'N/A',
                        ]);
                        }

                        if ($mapehAssignments->isNotEmpty()) {
                        $representative = $mapehAssignments->first(function ($assignment) {
                        return (bool) (optional($assignment)->day_of_week || optional($assignment)->start_time || optional($assignment)->room);
                        }) ?? $mapehAssignments->first();

                        $displayAssignments->prepend((object) [
                        'assignment' => $representative,
                        'subject_name' => 'MAPEH',
                        ]);
                        }
                        $studentCount = $section->students->count();
                        @endphp

                        <a href="{{ route('faculty.sections.show', $section) }}" class="section-card">
                            <div class="section-card-preview">
                                <div class="card-head">
                                    <div>
                                        <div class="hierarchy-label">Section</div>
                                        <div class="hierarchy-value">{{ $section->name }}</div>
                                        <div class="mt-2 d-flex flex-wrap gap-2">
                                            <span class="badge bg-info">{{ str_starts_with((string) $section->grade_level, 'Grade') ? $section->grade_level : 'Grade ' . $section->grade_level }}</span>
                                            @if($isAdviser)
                                            <span class="badge bg-warning text-dark">You are the Adviser</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="text-md-end">
                                        <div class="hierarchy-label">Students</div>
                                        <div class="hierarchy-value">
                                            {{ $studentCount }}
                                            @if($section->capacity)
                                            / {{ $section->capacity }}
                                            @endif
                                        </div>
                                        <div class="hierarchy-label mt-2">Academic Year</div>
                                        <div class="hierarchy-value">{{ $section->academic_year ?? 'N/A' }}</div>
                                    </div>
                                </div>

                                <div class="subject-stack">
                                    <div class="hierarchy-label">Subjects You Handle</div>
                                    @forelse($displayAssignments as $display)
                                    @php
                                    $assignment = $display->assignment;
                                    @endphp
                                    <div class="subject-item">
                                        <div><strong>{{ $display->subject_name }}</strong></div>
                                        <div class="small text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            @if($assignment->day_of_week && $assignment->start_time && $assignment->end_time)
                                            {{ $assignment->day_of_week }}, {{ substr($assignment->start_time, 0, 5) }} - {{ substr($assignment->end_time, 0, 5) }}
                                            @else
                                            TBA
                                            @endif
                                            <span class="ms-3"><i class="fas fa-door-open me-1"></i>{{ $assignment->room ?: 'TBA' }}</span>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="subject-item text-muted">No active subject assignment found.</div>
                                    @endforelse
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="faculty-management-empty text-success">
                            <i class="fas fa-layer-group text-success"></i>
                            <h5 class="fw-semibold mb-2 text-success">No sections found</h5>
                            <p class="mb-0 text-success">Try adjusting your filters to find more results.</p>
                        </div>
                        @endforelse
                    </div>

                    <div class="mt-4">
                        {{ $sections->links() }}
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

@endsection