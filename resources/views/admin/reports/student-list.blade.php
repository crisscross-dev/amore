@extends('layouts.app')

@section('title', 'Student List Report - Admin Dashboard - Amore Academy')

@section('content')
@vite(['resources/css/layouts/dashboard-roles/dashboard-admin.css'])

<div class="dashboard-container">
  <div class="container-fluid px-4">
    <div class="row">

      <main class="col-12">
        <div class="welcome-card mb-4">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h4 class="mb-1"><i class="fas fa-user-graduate me-2"></i>Student List Report</h4>
              <p class="mb-0 opacity-90">Complete student roster with section assignments</p>
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

        <!-- Filters -->
        <div class="faculty-management-card mb-4 p-3">
          <form method="GET" class="row g-3">
            <div class="col-md-4">
              <input type="text" name="search" class="form-control" placeholder="Search by name, LRN, or email..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
              <select name="grade_level" class="form-select">
                <option value="">All Grade Levels</option>
                @foreach($gradeLevels as $level)
                  <option value="{{ $level }}" {{ request('grade_level') == $level ? 'selected' : '' }}>{{ $level }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <select name="section_id" class="form-select">
                <option value="">All Sections</option>
                @foreach($sections as $section)
                  <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <button type="submit" class="btn btn-success w-100">
                <i class="fas fa-filter me-2"></i>Filter
              </button>
            </div>
          </form>
        </div>

        <div class="faculty-management-card faculty-management-table">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 text-success"><i class="fas fa-list me-2 text-success"></i>Students</h5>
            <span class="badge bg-success bg-opacity-75">{{ $students->count() }} students</span>
          </div>

          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead>
                <tr>
                  <th>#</th>
                  <th>LRN</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Section</th>
                  <th>Grade Level</th>
                </tr>
              </thead>
              <tbody>
                @forelse($students as $index => $student)
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $student->lrn }}</td>
                    <td>{{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }}</td>
                    <td>{{ $student->email }}</td>
                    <td>{{ $student->section->name ?? 'Not Assigned' }}</td>
                    <td>{{ $student->section->grade_level ?? 'N/A' }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6">
                      <div class="faculty-management-empty">
                        <i class="fas fa-user-graduate"></i>
                        <h5 class="fw-semibold mb-2">No students found</h5>
                        <p class="mb-0">No students match your search criteria.</p>
                      </div>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </div>
</div>

<style>
@media print {
  .welcome-card button,
  .welcome-card a,
  .faculty-management-card form,
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

