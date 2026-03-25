@extends('layouts.app')

@section('title', 'Manage Grades - Faculty Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@vite(['resources/css/layouts/dashboard-roles/dashboard-faculty.css','resources/css/faculty/grade-management.css'])

<style>
  .grade-cards-wrap {
    display: grid;
    gap: 1rem;
  }

  .grade-card {
    display: block;
    border: 1px solid rgba(22, 101, 52, 0.2);
    border-radius: 14px;
    background: #ffffff;
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
    text-decoration: none;
    transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
  }

  .grade-card:hover {
    transform: translateY(-2px);
    border-color: rgba(22, 101, 52, 0.35);
    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.1);
  }

  .grade-card:focus-visible {
    outline: 3px solid rgba(22, 163, 74, 0.35);
    outline-offset: 2px;
  }

  .grade-card .grade-card-preview {
    padding: 1rem;
  }

  .grade-card .card-head {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
  }

  .grade-card .hierarchy-label {
    font-size: 0.72rem;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: #64748b;
    margin-bottom: 0.1rem;
  }

  .grade-card .hierarchy-value {
    color: #0f172a;
    font-weight: 600;
  }

  .grade-card .meta-item {
    border: 1px solid #dcfce7;
    background: #f8fafc;
    border-radius: 10px;
    padding: 0.65rem 0.75rem;
    margin-top: 0.75rem;
    color: #14532d;
  }

  .grade-card .open-hint {
    margin-top: 0.75rem;
    color: #15803d;
    font-size: 0.8rem;
    font-weight: 600;
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

        <div class="faculty-management-card faculty-management-table">
          @php
          $mapehComponentNamesByGrade = [
          7 => ['MAPEH - Music & Arts', 'MAPEH - PE & Health', 'Music & Arts', 'PE & Health'],
          8 => ['MAPEH - Music & Arts', 'MAPEH - PE & Health', 'Music & Arts', 'PE & Health'],
          9 => ['MAPEH - Music', 'MAPEH - Arts', 'MAPEH - PE', 'MAPEH - Health', 'Music', 'Arts', 'PE', 'Health'],
          10 => ['MAPEH - Music', 'MAPEH - Arts', 'MAPEH - PE', 'MAPEH - Health', 'Music', 'Arts', 'PE', 'Health'],
          ];

          $groupedAssignments = $assignments
          ->groupBy(function ($assignment) use ($mapehComponentNamesByGrade) {
          $gradeNumber = null;
          if (preg_match('/(7|8|9|10|11|12)/', (string) (optional($assignment->section)->grade_level ?? ''), $gradeMatch)) {
          $gradeNumber = (int) $gradeMatch[1];
          }

          $subjectName = (string) (optional($assignment->subject)->name ?? '');
          $mapehNames = $mapehComponentNamesByGrade[$gradeNumber] ?? [];
          $normalizedSubject = !empty($mapehNames) && in_array($subjectName, $mapehNames, true)
          ? 'MAPEH'
          : strtolower(trim($subjectName ?: 'n/a'));

          return ((int) $assignment->section_id) . '|' . $normalizedSubject;
          })
          ->map(function ($items) use ($mapehComponentNamesByGrade) {
          $representative = $items->first(function ($assignment) {
          return (bool) (optional($assignment)->day_of_week || optional($assignment)->start_time || optional($assignment)->room);
          }) ?? $items->first();

          $gradeNumber = null;
          if (preg_match('/(7|8|9|10|11|12)/', (string) (optional($representative->section)->grade_level ?? ''), $gradeMatch)) {
          $gradeNumber = (int) $gradeMatch[1];
          }

          $mapehNames = $mapehComponentNamesByGrade[$gradeNumber] ?? [];
          $isMapeh = $items->contains(function ($assignment) use ($mapehNames) {
          $name = (string) (optional($assignment->subject)->name ?? '');
          return !empty($mapehNames) && in_array($name, $mapehNames, true);
          });

          return (object) [
          'assignment' => $representative,
          'subject_name' => $isMapeh ? 'MAPEH' : (optional($representative->subject)->name ?? 'N/A'),
          ];
          })
          ->values();
          @endphp

          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 text-success">
              <i class="fas fa-table me-2 text-success"></i>
              Subject Grade Entry
            </h5>
            <span class="badge bg-success bg-opacity-75">{{ $groupedAssignments->count() }} assignment{{ $groupedAssignments->count() === 1 ? '' : 's' }}</span>
          </div>

          <div class="grade-cards-wrap">
            @forelse($groupedAssignments as $grouped)
            @php
            $assignment = $grouped->assignment;
            $section = $assignment->section;
            $studentsTotal = (int) ($studentCounts[$assignment->section_id] ?? 0);
            @endphp
            <a href="{{ route('faculty.grades.assignment', $assignment) }}" class="grade-card">
              <div class="grade-card-preview">
                <div class="card-head">
                  <div>
                    <div class="hierarchy-label">Subject</div>
                    <div class="hierarchy-value">{{ $grouped->subject_name }}</div>
                  </div>
                  <div class="text-md-end">
                    <div class="hierarchy-label">Section</div>
                    <div class="hierarchy-value">{{ $section->name ?? 'N/A' }}</div>
                  </div>
                </div>

                <div class="meta-item">
                  <div class="d-flex flex-wrap gap-3">
                    <span><strong>Grade Level:</strong> {{ $section->grade_level ?? 'N/A' }}</span>
                    <span><strong>Students:</strong> {{ $studentsTotal }}</span>
                    <span><strong>Schedule:</strong>
                      @if($assignment->day_of_week && $assignment->start_time && $assignment->end_time)
                      {{ $assignment->day_of_week }}, {{ substr($assignment->start_time, 0, 5) }} - {{ substr($assignment->end_time, 0, 5) }}
                      @else
                      TBA
                      @endif
                    </span>
                    <span><strong>Room:</strong> {{ $assignment->room ?: 'TBA' }}</span>
                  </div>
                </div>

                <div class="open-hint"><i class="fas fa-arrow-right me-1"></i>Click card to manage student grades</div>
              </div>
            </a>
            @empty
            <div class="faculty-management-empty">
              <i class="fas fa-graduation-cap"></i>
              <h5 class="fw-semibold mb-2 text-success">No subject assignments found</h5>
              <p class="mb-0">Ask admin to assign a section and subject to start grading.</p>
            </div>
            @endforelse
          </div>
        </div>
      </main>
    </div>
  </div>
</div>
@endsection