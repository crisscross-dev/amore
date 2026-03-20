@extends('layouts.app')

@section('title', 'Grades Summary Report - Admin Dashboard - Amore Academy')

@section('content')
@vite(['resources/css/layouts/dashboard-roles/dashboard-admin.css'])

<div class="dashboard-container">
  <div class="container-fluid px-4">
    <div class="row">

      <main class="col-12">
        <div class="welcome-card mb-4">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h4 class="mb-1"><i class="fas fa-graduation-cap me-2"></i>Grades Summary Report</h4>
              <p class="mb-0 opacity-90">Approved grades by term and section</p>
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
            <div class="col-md-5">
              <select name="section_id" class="form-select">
                <option value="">All Sections</option>
                @foreach($sections as $section)
                  <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                    {{ $section->name }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-5">
              <select name="term" class="form-select">
                <option value="">All Terms</option>
                @foreach($terms as $term)
                  <option value="{{ $term }}" {{ request('term') == $term ? 'selected' : '' }}>{{ $term }}</option>
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
            <h5 class="mb-0"><i class="fas fa-list me-2 text-success"></i>Approved Grades</h5>
            <span class="badge bg-success bg-opacity-75">{{ $grades->count() }} records</span>
          </div>

          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Student</th>
                  <th>Section</th>
                  <th>Subject</th>
                  <th>Term</th>
                  <th>Grade</th>
                  <th>Approved Date</th>
                </tr>
              </thead>
              <tbody>
                @forelse($grades as $index => $grade)
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $grade->student->last_name ?? 'N/A' }}, {{ $grade->student->first_name ?? '' }}</td>
                    <td>{{ $grade->section->name ?? 'N/A' }}</td>
                    <td>{{ $grade->subject->name ?? 'N/A' }}</td>
                    <td>{{ $grade->term }}</td>
                    <td><strong>{{ number_format($grade->grade_value, 2) }}</strong></td>
                    <td>{{ $grade->approved_at ? $grade->approved_at->format('M d, Y') : 'N/A' }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7">
                      <div class="faculty-management-empty">
                        <i class="fas fa-graduation-cap"></i>
                        <h5 class="fw-semibold mb-2">No grades found</h5>
                        <p class="mb-0">No approved grades match your filter criteria.</p>
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

