@extends('layouts.app')

@section('title', 'Subject Assignments Report - Admin Dashboard - Amore Academy')

@section('content')
@vite(['resources/css/layouts/dashboard-roles/dashboard-admin.css'])

<div class="dashboard-container">
  <div class="container-fluid px-4">
    <div class="row">

      <main class="col-12">
        <div class="welcome-card mb-4">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h4 class="mb-1"><i class="fas fa-book me-2"></i>Subject Assignments Report</h4>
              <p class="mb-0 opacity-90">Subject-teacher assignments by section</p>
            </div>
            <div>
              <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-light me-2">
                <i class="fas fa-arrow-left me-2"></i>Back to Reports
              </a>
              <!-- <button onclick="window.print()" class="btn btn-success">
                <i class="fas fa-print me-2"></i>Print
              </button> -->
            </div>
          </div>
        </div>

        @forelse($assignments as $sectionId => $sectionAssignments)
          @php
            $section = $sectionAssignments->first()->section;
          @endphp
          <div class="faculty-management-card mb-4">
            <div class="p-3 bg-success bg-opacity-10 border-bottom border-success">
              <h5 class="mb-1 text-white"><i class="fas fa-layer-group me-2"></i>{{ $section->name }}</h5>
              <small class="text-white-50">Grade Level: {{ $section->grade_level }}</small>
            </div>
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead>
                  <tr>
                    <th>Subject</th>
                    <th>Teacher</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($sectionAssignments as $assignment)
                    <tr>
                      <td><strong>{{ $assignment->subject->name }}</strong></td>
                      <td>
                        <i class="fas fa-user-tie me-2 text-success"></i>
                        {{ $assignment->teacher->first_name ?? 'N/A' }} {{ $assignment->teacher->last_name ?? '' }}
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        @empty
          <div class="faculty-management-card">
            <div class="faculty-management-empty">
              <i class="fas fa-book"></i>
              <h5 class="fw-semibold mb-2">No assignments found</h5>
              <p class="mb-0">No subject assignments have been created yet.</p>
            </div>
          </div>
        @endforelse
      </main>
    </div>
  </div>
</div>

<style>
@media print {
  .welcome-card button,
  .welcome-card a,
  aside,
  .btn {
    display: none !important;
  }
  .welcome-card {
    background: white !important;
    color: black !important;
  }
  .table {
    color: black !important;
  }
}
</style>
@endsection

