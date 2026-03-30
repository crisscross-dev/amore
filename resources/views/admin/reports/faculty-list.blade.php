@extends('layouts.app')

@section('title', 'Faculty List Report - Admin Dashboard - Amore Academy')

@section('content')
@vite(['resources/css/layouts/dashboard-roles/dashboard-admin.css'])

<div class="dashboard-container">
  <div class="container-fluid px-4">
    <div class="row">

      <main class="col-12">
        <div class="welcome-card mb-4">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h4 class="mb-1"><i class="fas fa-users me-2"></i>Faculty List Report</h4>
              <p class="mb-0 opacity-90">Complete list of all faculty members</p>
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

        <!-- Search Filter -->
        <div class="faculty-management-card mb-4 p-3">
          <form method="GET" class="row g-3">
            <div class="col-md-6">
              <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
              <select name="department" class="form-select">
                <option value="">All Departments</option>
                @foreach($departments as $department)
                  <option value="{{ $department }}" {{ request('department') === $department ? 'selected' : '' }}>{{ $department }}</option>
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
            <h5 class="mb-0 text-success"><i class="fas fa-list me-2"></i>Faculty Members</h5>
            <span class="badge bg-success bg-opacity-75">{{ $faculties->total() }} Faculty</span>
          </div>

          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Department</th>
                </tr>
              </thead>
              <tbody>
                @forelse($faculties as $index => $faculty)
                  <tr>
                    <td>{{ $faculties->firstItem() + $index }}</td>
                    <td>{{ $faculty->last_name }}, {{ $faculty->first_name }} {{ $faculty->middle_name }}</td>
                    <td>{{ $faculty->email }}</td>
                    <td>{{ $faculty->department ?? 'Not Assigned' }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4">
                      <div class="faculty-management-empty">
                        <i class="fas fa-users"></i>
                        <h5 class="fw-semibold mb-2">No faculty members found</h5>
                        <p class="mb-0">No faculty members match your search criteria.</p>
                      </div>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          @if($faculties->hasPages())
          <div class="mt-3 d-flex justify-content-center">
            {{ $faculties->links('pagination::bootstrap-5') }}
          </div>
          @endif
        </div>
      </main>
    </div>
  </div>
</div>

<style>
.faculty-management-table h5,
.faculty-management-table th,
.faculty-management-table td {
  color: #1f2937;
}

.faculty-management-table thead th {
  font-weight: 700;
}

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

