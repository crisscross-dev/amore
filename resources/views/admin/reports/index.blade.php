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
              onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();"
            >
              <i class="fas fa-sign-out-alt me-2"></i>Logout
            </button>
            
            <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="d-none">
              @csrf
            </form>
          </div>
        </div>

        <!-- Page Header -->
        <div class="welcome-card mb-4">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <h4 class="mb-2">
                <i class="fas fa-chart-line me-2"></i>
                Generate Reports
              </h4>
              <p class="mb-0 opacity-90">
                View and export various reports for your institution
              </p>
            </div>
          </div>
        </div>

        <!-- Statistics Overview -->
        <div class="row mb-4">
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

        <!-- Report Options -->
        <div class="faculty-management-card p-4">
          <h5 class="mb-4"><i class="fas fa-file-alt me-2 text-success"></i>Available Reports</h5>
          
          <div class="row g-4">
            <!-- Faculty List Report -->
            <div class="col-md-6">
              <div class="admissions-card bg-dark border-success h-100">
                <div class="card-body">
                  <div class="d-flex align-items-start mb-3">
                    <div class="fs-3 text-success me-3">
                      <i class="fas fa-users"></i>
                    </div>
                    <div class="flex-grow-1">
                      <h5 class="card-title text-white mb-2">Faculty List</h5>
                      <p class="card-text text-white-50 small mb-3">
                        Complete list of all faculty members with their contact information and details.
                      </p>
                      <a href="{{ route('admin.reports.faculty-list') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-eye me-2"></i>View Report
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Student List Report -->
            <div class="col-md-6">
              <div class="admissions-card bg-dark border-info h-100">
                <div class="card-body">
                  <div class="d-flex align-items-start mb-3">
                    <div class="fs-3 text-info me-3">
                      <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="flex-grow-1">
                      <h5 class="card-title text-white mb-2">Student List</h5>
                      <p class="card-text text-white-50 small mb-3">
                        Complete student roster with LRN, section assignments, and contact information.
                      </p>
                      <a href="{{ route('admin.reports.student-list') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-eye me-2"></i>View Report
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Subject Assignments Report -->
            <div class="col-md-6">
              <div class="admissions-card bg-dark border-warning h-100">
                <div class="card-body">
                  <div class="d-flex align-items-start mb-3">
                    <div class="fs-3 text-warning me-3">
                      <i class="fas fa-book"></i>
                    </div>
                    <div class="flex-grow-1">
                      <h5 class="card-title text-white mb-2">Subject Assignments</h5>
                      <p class="card-text text-white-50 small mb-3">
                        Overview of all subject-teacher assignments organized by section.
                      </p>
                      <a href="{{ route('admin.reports.subject-assignments') }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-eye me-2"></i>View Report
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Grades Summary Report -->
            <div class="col-md-6">
              <div class="admissions-card bg-dark border-danger h-100">
                <div class="card-body">
                  <div class="d-flex align-items-start mb-3">
                    <div class="fs-3 text-danger me-3">
                      <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="flex-grow-1">
                      <h5 class="card-title text-white mb-2">Grades Summary</h5>
                      <p class="card-text text-white-50 small mb-3">
                        Approved grades report with filtering by term and section.
                      </p>
                      <a href="{{ route('admin.reports.grades-summary') }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-eye me-2"></i>View Report
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</div>
@endsection

