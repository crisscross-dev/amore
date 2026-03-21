@extends('layouts.app-student')

@section('title', 'My Subjects - Student Dashboard - Amore Academy')

@section('content')
@vite(['resources/css/layouts/dashboard-roles/dashboard-student.css','resources/css/student/grade-view.css'])

<div class="dashboard-container">
  <div class="container-fluid px-4">
    <div class="row">
      <main class="col-12">
        <div class="welcome-card mb-4">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h4 class="mb-1"><i class="fas fa-book me-2"></i>My Subjects</h4>
              <p class="mb-0 opacity-90">
                @if($section)
                Section: <strong>{{ $section->name }}</strong> | Grade Level: <strong>{{ $section->grade_level }}</strong>
                @else
                No section assigned
                @endif
              </p>
            </div>
          </div>
        </div>

        @if($message)
        <div class="alert alert-info" role="alert">
          <i class="fas fa-info-circle me-2"></i>{{ $message }}
        </div>
        @endif

        @if($subjectAssignments->isNotEmpty())
        <div class="faculty-management-card faculty-management-table">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0"><i class="fas fa-list me-2 text-success"></i>Subject List</h5>
            <span class="badge bg-success bg-opacity-75">{{ $subjectAssignments->count() }} assignment{{ $subjectAssignments->count() === 1 ? '' : 's' }}</span>
          </div>

          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead>
                <tr>
                  <th>Subject Name</th>
                  <th>Day</th>
                  <th>Time</th>
                  <th>Room</th>
                  <th>Teacher</th>
                </tr>
              </thead>
              <tbody>
                @foreach($subjectAssignments as $assignment)
                <tr>
                  <td><strong>{{ optional($assignment->subject)->name ?? 'N/A' }}</strong></td>
                  <td>{{ $assignment->day_of_week ?: 'TBA' }}</td>
                  <td>
                    @if($assignment->start_time && $assignment->end_time)
                    {{ substr($assignment->start_time, 0, 5) }} - {{ substr($assignment->end_time, 0, 5) }}
                    @else
                    <span class="text-muted">TBA</span>
                    @endif
                  </td>
                  <td>{{ $assignment->room ?: 'TBA' }}</td>
                  <td>
                    @if($assignment->teacher)
                    {{ $assignment->teacher->first_name }} {{ $assignment->teacher->last_name }}
                    @else
                    <span class="text-muted">TBA</span>
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        @elseif(!$message)
        <div class="faculty-management-card">
          <div class="faculty-management-empty">
            <i class="fas fa-book"></i>
            <h5 class="fw-semibold mb-2">No subjects assigned yet</h5>
            <p class="mb-0">Your section doesn't have any subjects assigned. Please contact your administrator.</p>
          </div>
        </div>
        @endif
      </main>
    </div>
  </div>
</div>
@endsection