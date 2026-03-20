@extends('layouts.app')

@section('title', 'My Subjects - Student Dashboard - Amore Academy')

@section('content')
@vite(['resources/css/layouts/dashboard-roles/dashboard-student.css','resources/css/student/grade-view.css'])

<div class="dashboard-container">
  <div class="container-fluid px-4">
    <div class="row">
      @include('partials.student-sidebar')

      <main class="col-lg-9 col-md-8">
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

        @if($subjects->isNotEmpty())
          <div class="faculty-management-card faculty-management-table">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="mb-0"><i class="fas fa-list me-2 text-success"></i>Subject List</h5>
              <span class="badge bg-success bg-opacity-75">{{ $subjects->count() }} subjects</span>
            </div>

            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead>
                  <tr>
                    <th>Subject Name</th>
                    <th>Description</th>
                    <th>Teacher(s)</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($subjects as $item)
                    <tr>
                      <td><strong>{{ $item['subject']->name }}</strong></td>
                      <td>{{ $item['subject']->description ?? '-' }}</td>
                      <td>
                        @if($item['teachers']->isNotEmpty())
                          @foreach($item['teachers'] as $teacher)
                            <div class="mb-1">
                              <i class="fas fa-user-tie me-1 text-success"></i>
                              {{ $teacher->first_name }} {{ $teacher->last_name }}
                            </div>
                          @endforeach
                        @else
                          <span class="text-muted">No teacher assigned</span>
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
