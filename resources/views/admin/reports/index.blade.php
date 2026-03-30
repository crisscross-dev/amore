@extends('layouts.app')

@section('title', 'Generate Reports - Admin Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Admin Dashboard CSS -->
@vite(['resources/css/layouts/dashboard-roles/dashboard-admin.css'])

<div class="dashboard-container">
  <div class="container-fluid px-4">
    <div class="row">
      <!-- Left Profile Sidebar -->

      <!-- Main Content -->
      <main class="col-12">
        <!-- Mobile Profile (Hidden on Desktop) -->
        <div class="d-md-none mobile-profile mb-4">
          <div class="text-center">
            <img src="{{ asset('uploads/profile_picture/' . Auth::user()->profile_picture) }}"
              alt="Profile Picture"
              class="rounded-circle mb-3 border border-3 border-white"
              width="80"
              height="80">

            <h5 class="text-white mb-1">{{ Auth::user()->first_name ?? 'Admin' }} {{ Auth::user()->last_name ?? 'Name' }}</h5>
            <p class="text-white-50 small mb-3">
              Administrator | {{ Auth::user()->custom_id ?? 'ADMIN-0001' }}
            </p>

            <!-- Mobile Navigation -->
            <div class="row g-2">
              <div class="col-4">
                <a href="{{ route('dashboard.admin') }}" class="btn mobile-nav-btn w-100">
                  <i class="fas fa-tachometer-alt d-block mb-1"></i>
                  <small>Dashboard</small>
                </a>
              </div>
              <div class="col-4">
                <a href="{{ route('calendar.index') }}" class="btn mobile-nav-btn w-100">
                  <i class="fas fa-calendar-alt d-block mb-1"></i>
                  <small>Calendar</small>
                </a>
              </div>
              <div class="col-4">
                <a href="{{ route('announcements.index') }}" class="btn mobile-nav-btn w-100">
                  <i class="fas fa-bullhorn d-block mb-1"></i>
                  <small>Announce</small>
                </a>
              </div>
              <div class="col-4">
                <a href="{{ route('admin.reports.index') }}" class="btn mobile-nav-btn w-100 active">
                  <i class="fas fa-chart-line d-block mb-1"></i>
                  <small>Reports</small>
                </a>
              </div>
              <div class="col-4">
                <a href="{{ route('admin.subjects.index') }}" class="btn mobile-nav-btn w-100">
                  <i class="fas fa-book d-block mb-1"></i>
                  <small>Subjects</small>
                </a>
              </div>
              <div class="col-4">
                <a href="{{ route('admin.admissions.index') }}" class="btn mobile-nav-btn w-100">
                  <i class="fas fa-user-check d-block mb-1"></i>
                  <small>Admissions</small>
                </a>
              </div>
            </div>

            <hr class="bg-white opacity-25 my-3">

            <button
              class="btn logout-btn w-100"
              onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
              <i class="fas fa-sign-out-alt me-2"></i>Logout
            </button>

            <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="d-none">
              @csrf
            </form>
          </div>
        </div>

        <div class="header-title d-flex align-items-center justify-content-between mb-2">
          <h5 class="mb-2 fw-semibold text-success">
            <i class="fas fa-chart-line me-2"></i>
            Generate Reports
          </h5>
          <!-- <div class="d-none d-lg-block">
            <a href="{{ route('admin.faculty-positions.create') }}" class="btn btn-success btn-sm">
              <i class="fas fa-plus me-2"></i>New Position
            </a>
          </div> -->
        </div>


        <!-- Statistics Overview -->
        <div class="row mb-2 reports-stats-row">
          <div class="col-md-4 mb-3">
            <div class="admissions-card bg-success bg-opacity-10 border-0">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="text-white-50 mb-1">Total Students</h6>
                    <h3 class="mb-0 text-white">{{ $stats['total_students'] }}</h3>
                  </div>
                  <div class="fs-1 text-success opacity-50">
                    <i class="fas fa-user-graduate"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="admissions-card bg-info bg-opacity-10 border-0">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="text-white-50 mb-1">Total Faculty</h6>
                    <h3 class="mb-0 text-white">{{ $stats['total_faculty'] }}</h3>
                  </div>
                  <div class="fs-1 text-info opacity-50">
                    <i class="fas fa-chalkboard-teacher"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="admissions-card bg-warning bg-opacity-10 border-0">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="text-white-50 mb-1">Total Sections</h6>
                    <h3 class="mb-0 text-white">{{ $stats['total_sections'] }}</h3>
                  </div>
                  <div class="fs-1 text-warning opacity-50">
                    <i class="fas fa-layer-group"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Report Tables -->
        <div class="faculty-management-card p-4 reports-tabbed-card">
          @php
            $printQuery = request()->except(['faculty_page', 'students_page', 'sections_page', 'events_page']);
            $printQuery['tab'] = $activeTab;
          @endphp
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="mb-0 text-success"><i class="fas fa-file-alt me-2 text-success"></i>Available Reports</h5>
            <a href="{{ route('admin.reports.print', $printQuery) }}" target="_blank" class="btn btn-success btn-sm">
              <i class="fas fa-print me-1"></i>Print
            </a>
          </div>

          <div class="reports-tab-nav mb-3">
            <a href="{{ route('admin.reports.index', ['tab' => 'faculty']) }}"
              class="reports-tab-link {{ $activeTab === 'faculty' ? 'active' : '' }}">
              Faculty
            </a>
            <a href="{{ route('admin.reports.index', ['tab' => 'students']) }}"
              class="reports-tab-link {{ $activeTab === 'students' ? 'active' : '' }}">
              Student
            </a>
            <a href="{{ route('admin.reports.index', ['tab' => 'sections']) }}"
              class="reports-tab-link {{ $activeTab === 'sections' ? 'active' : '' }}">
              Section
            </a>
            <a href="{{ route('admin.reports.index', ['tab' => 'events']) }}"
              class="reports-tab-link {{ $activeTab === 'events' ? 'active' : '' }}">
              Event
            </a>
          </div>

          @if($activeTab === 'faculty')
            <form id="faculty-report-filters" method="GET" action="{{ route('admin.reports.index') }}" class="row g-2 mb-3 reports-filter-row">
              <input type="hidden" name="tab" value="faculty">
              <div class="col-xl-3 col-lg-6 col-md-6">
                <input
                  type="text"
                  name="faculty_name"
                  value="{{ request('faculty_name') }}"
                  class="form-control reports-filter-input"
                  placeholder="Filter by faculty name">
              </div>
              <div class="col-xl-3 col-lg-6 col-md-6">
                <select name="faculty_department" class="form-select reports-filter-input">
                  <option value="">All Departments</option>
                  @foreach($facultyDepartments as $department)
                    <option value="{{ $department }}" {{ request('faculty_department') === $department ? 'selected' : '' }}>
                      {{ $department }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-xl-3 col-lg-6 col-md-6">
                <select name="faculty_position_id" class="form-select reports-filter-input">
                  <option value="">All Positions (Higher to Lower)</option>
                  @foreach($facultyPositions as $position)
                    <option value="{{ $position->id }}" {{ (string) request('faculty_position_id') === (string) $position->id ? 'selected' : '' }}>
                      {{ $position->name }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-xl-2 col-lg-6 col-md-6">
                <select name="faculty_view" class="form-select reports-filter-input">
                  <option value="latest" {{ $facultyView === 'latest' ? 'selected' : '' }}>Latest First</option>
                  <option value="by_departments" {{ $facultyView === 'by_departments' ? 'selected' : '' }}>By Departments</option>
                </select>
              </div>
              <div class="col-xl-1 col-lg-6 col-md-6 d-flex justify-content-md-end align-items-stretch">
                <a href="{{ route('admin.reports.index', ['tab' => 'faculty']) }}" class="btn btn-outline-secondary w-100">Reset</a>
              </div>
            </form>
          @elseif($activeTab === 'students')
            <form id="student-report-filters" method="GET" action="{{ route('admin.reports.index') }}" class="row g-2 mb-3 reports-filter-row">
              <input type="hidden" name="tab" value="students">
              <div class="col-lg-5 col-md-6">
                <input
                  type="text"
                  name="student_query"
                  value="{{ request('student_query') }}"
                  class="form-control reports-filter-input"
                  placeholder="Filter by student name or LRN">
              </div>
              <div class="col-lg-3 col-md-6">
                <select name="student_section_id" class="form-select reports-filter-input">
                  <option value="">All Sections</option>
                  @foreach($studentSections as $studentSection)
                    <option value="{{ $studentSection->id }}" {{ (string) request('student_section_id') === (string) $studentSection->id ? 'selected' : '' }}>
                      {{ $studentSection->name }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-lg-3 col-md-6">
                <select name="student_grade_level" class="form-select reports-filter-input">
                  <option value="">All Grade Levels</option>
                  @foreach($studentGradeLevels as $studentGradeLevel)
                    <option value="{{ $studentGradeLevel }}" {{ (string) request('student_grade_level') === (string) $studentGradeLevel ? 'selected' : '' }}>
                      {{ $studentGradeLevel }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-lg-1 col-md-6 d-flex justify-content-md-end align-items-stretch">
                <a href="{{ route('admin.reports.index', ['tab' => 'students']) }}" class="btn btn-outline-secondary w-100">Reset</a>
              </div>
            </form>
          @endif

          <div class="table-responsive report-tab-table-wrapper">
            @if($activeTab === 'faculty')
              <table class="table report-list-table align-middle mb-0">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Position</th>
                  </tr>
                </thead>
                <tbody>
                  @if($faculties->isEmpty())
                    <tr>
                      <td colspan="4" class="text-center py-4 text-muted">No faculty records found.</td>
                    </tr>
                  @elseif($facultyView === 'by_departments')
                    @php
                      $displayIndex = $faculties->firstItem();
                      $groupedFaculties = $faculties->getCollection()->groupBy(function ($faculty) {
                          return $faculty->department ?: 'Not Assigned';
                      });
                    @endphp

                    @foreach($groupedFaculties as $department => $departmentFaculties)
                      <tr class="department-group-row">
                        <td colspan="4" class="department-group-cell">
                          <span class="department-group-label">{{ $department }} Department</span>
                        </td>
                      </tr>
                      @foreach($departmentFaculties as $faculty)
                        <tr>
                          <td>{{ $displayIndex }}</td>
                          <td>{{ trim($faculty->first_name . ' ' . $faculty->middle_name . ' ' . $faculty->last_name) }}</td>
                          <td>{{ $faculty->department ?? 'Not Assigned' }}</td>
                          <td>{{ $faculty->facultyPosition->name ?? 'Not Assigned' }}</td>
                        </tr>
                        @php($displayIndex++)
                      @endforeach
                    @endforeach
                  @else
                    @foreach($faculties as $index => $faculty)
                      <tr>
                        <td>{{ $faculties->firstItem() + $index }}</td>
                        <td>{{ trim($faculty->first_name . ' ' . $faculty->middle_name . ' ' . $faculty->last_name) }}</td>
                        <td>{{ $faculty->department ?? 'Not Assigned' }}</td>
                        <td>{{ $faculty->facultyPosition->name ?? 'Not Assigned' }}</td>
                      </tr>
                    @endforeach
                  @endif
                </tbody>
              </table>
            @elseif($activeTab === 'students')
              <table class="table report-list-table align-middle mb-0">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>LRN</th>
                    <th>Name</th>
                    <th>Section</th>
                    <th>Grade Level</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($students as $index => $student)
                    <tr>
                      <td>{{ $students->firstItem() + $index }}</td>
                      <td>{{ $student->lrn ?? 'N/A' }}</td>
                      <td>{{ trim($student->last_name . ', ' . $student->first_name . ' ' . $student->middle_name) }}</td>
                      <td>{{ $student->section->name ?? 'Not Assigned' }}</td>
                      <td>{{ $student->section->grade_level ?? 'N/A' }}</td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="5" class="text-center py-4 text-muted">No student records found.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            @elseif($activeTab === 'sections')
              <table class="table report-list-table align-middle mb-0">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Section</th>
                    <th>Grade Level</th>
                    <th>Adviser</th>
                    <th>Student</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($sections as $index => $section)
                    <tr>
                      <td>{{ $sections->firstItem() + $index }}</td>
                      <td>{{ $section->name }}</td>
                      <td>{{ preg_match('/(\d+)/', (string) $section->grade_level, $gradeMatch) ? 'Grade ' . $gradeMatch[1] : ($section->grade_level ?? 'N/A') }}</td>
                      <td>
                        {{ $section->adviser
                            ? trim($section->adviser->first_name . ' ' . $section->adviser->last_name)
                            : 'Not Assigned' }}
                      </td>
                      <td>{{ $section->students_count }}</td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="5" class="text-center py-4 text-muted">No section records found.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            @else
              <table class="table report-list-table align-middle mb-0">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($events as $index => $event)
                    <tr>
                      <td>{{ $events->firstItem() + $index }}</td>
                      <td>{{ $event->title }}</td>
                      <td>{{ $event->type_name }}</td>
                      <td>{{ $event->start_date ? $event->start_date->format('M d, Y h:i A') : 'N/A' }}</td>
                      <td>{{ $event->end_date ? $event->end_date->format('M d, Y h:i A') : '-' }}</td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="5" class="text-center py-4 text-muted">No event records found.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            @endif
          </div>

          @if($activeTab === 'faculty' && $faculties->hasPages())
            <div class="mt-3 d-flex justify-content-center">
              {{ $faculties->appends(['tab' => 'faculty'])->links('pagination::bootstrap-5') }}
            </div>
          @elseif($activeTab === 'students' && $students->hasPages())
            <div class="mt-3 d-flex justify-content-center">
              {{ $students->appends(['tab' => 'students'])->links('pagination::bootstrap-5') }}
            </div>
          @elseif($activeTab === 'sections' && $sections->hasPages())
            <div class="mt-3 d-flex justify-content-center">
              {{ $sections->appends(['tab' => 'sections'])->links('pagination::bootstrap-5') }}
            </div>
          @elseif($activeTab === 'events' && $events->hasPages())
            <div class="mt-3 d-flex justify-content-center">
              {{ $events->appends(['tab' => 'events'])->links('pagination::bootstrap-5') }}
            </div>
          @endif
        </div>
      </main>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const bindAutoFilterForm = (formId, inputSelector) => {
    const form = document.getElementById(formId);
    if (!form) {
      return;
    }

    const submitFilters = () => form.submit();

    form.querySelectorAll('select').forEach((select) => {
      select.addEventListener('change', submitFilters);
    });

    const input = form.querySelector(inputSelector);
    if (!input) {
      return;
    }

    let typingTimer;
    input.addEventListener('input', () => {
      clearTimeout(typingTimer);
      typingTimer = setTimeout(submitFilters, 450);
    });

    input.addEventListener('keydown', (event) => {
      if (event.key === 'Enter') {
        event.preventDefault();
        clearTimeout(typingTimer);
        submitFilters();
      }
    });
  };

  bindAutoFilterForm('faculty-report-filters', 'input[name="faculty_name"]');
  bindAutoFilterForm('student-report-filters', 'input[name="student_query"]');
});
</script>

<style>
.reports-stats-row {
  margin-bottom: 0.2rem !important;
}

.reports-stats-row .col-md-4 {
  margin-bottom: 0.4rem !important;
}

.reports-tabbed-card {
  margin-top: -0.9rem !important;
}

.reports-tab-nav {
  display: flex;
  gap: 0.75rem;
  border-bottom: 1px solid rgba(26, 43, 84, 0.18);
  overflow-x: auto;
}

.reports-filter-row {
  margin-top: 0.1rem;
}

.reports-filter-input {
  border: 1px solid rgba(31, 49, 89, 0.15);
  min-height: 42px;
}

.reports-tab-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.65rem 1.1rem;
  margin-bottom: -1px;
  font-weight: 700;
  font-size: 1.05rem;
  color: #5f6f89;
  text-decoration: none;
  border-bottom: 3px solid transparent;
  transition: color 0.2s ease, border-color 0.2s ease;
  white-space: nowrap;
}

.reports-tab-link:hover {
  color: #1f3159;
}

.reports-tab-link.active {
  color: #0f2b59;
  border-bottom-color: #d7af59;
}

.report-tab-table-wrapper {
  border-radius: 1.2rem;
  overflow: hidden;
  border: 1px solid rgba(31, 49, 89, 0.08);
}

.report-list-table {
  margin-bottom: 0;
  color: #3f4a5b;
}

.report-list-table thead th {
  background: #1c2f59;
  color: #ffffff;
  font-size: 1.05rem;
  font-weight: 700;
  border-bottom: 0;
  padding: 1.1rem 1.3rem;
}

.report-list-table tbody td {
  padding: 1rem 1.3rem;
  border-color: #e8ebf1;
  font-size: 1.05rem;
}

.report-list-table tbody tr:nth-child(odd) {
  background: #f8f9fb;
}

.report-list-table tbody tr.department-group-row {
  background: #eaf1ff !important;
}

.report-list-table tbody tr.department-group-row td {
  font-weight: 700;
  color: #1c2f59;
  border-top: 1px solid #d7e3ff;
  border-bottom: 1px solid #d7e3ff;
}

.report-list-table tbody tr.department-group-row td.department-group-cell {
  text-align: center;
}

@media (max-width: 768px) {
  .reports-tab-link {
    font-size: 1rem;
    padding: 0.6rem 0.9rem;
  }

  .report-list-table thead th,
  .report-list-table tbody td {
    font-size: 0.95rem;
    padding: 0.85rem 0.75rem;
  }
}
</style>
@endsection