@extends('layouts.app')

@section('title', 'School Year Management - Admin Dashboard - Amore Academy')

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
                                <a href="{{ route('admin.school-years.index') }}" class="btn mobile-nav-btn w-100 active">
                                    <i class="fas fa-calendar-check d-block mb-1"></i>
                                    <small>School Years</small>
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
                                <i class="fas fa-calendar-check me-2"></i>
                                School Year Management
                            </h4>
                            <p class="mb-0 opacity-90">
                                Manage academic years and enrollment periods
                            </p>
                        </div>
                        <div class="d-none d-lg-block">
                            <a href="{{ route('admin.school-years.create') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-plus me-2"></i>Add School Year
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Mobile Add Button -->
                <div class="d-lg-none mb-3">
                    <a href="{{ route('admin.school-years.create') }}" class="btn btn-primary w-100">
                        <i class="fas fa-plus me-2"></i>Add School Year
                    </a>
                </div>

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- School Years Card -->
                <div class="admissions-card mb-4">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-calendar-check me-2"></i>School Years
                    </div>
        <div class="card-body">
            @if($schoolYears->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>School Year</th>
                                <th>Academic Period</th>
                                <th>Enrollment Period</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schoolYears as $schoolYear)
                                <tr>
                                    <td>
                                        <strong>{{ $schoolYear->year_name }}</strong>
                                        @if($schoolYear->is_active)
                                            <span class="badge bg-success ms-2">Active</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $schoolYear->start_date->format('M d, Y') }} - 
                                        {{ $schoolYear->end_date->format('M d, Y') }}
                                    </td>
                                    <td>
                                        {{ $schoolYear->enrollment_start->format('M d, Y') }} - 
                                        {{ $schoolYear->enrollment_end->format('M d, Y') }}
                                        @if($schoolYear->isEnrollmentPeriod())
                                            <span class="badge bg-info text-dark ms-2">Open</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($schoolYear->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$schoolYear->is_active)
                                            <form action="{{ route('admin.school-years.activate', $schoolYear) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success me-1" title="Activate">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.school-years.edit', $schoolYear) }}" class="btn btn-sm btn-warning me-1" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.school-years.destroy', $schoolYear) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this school year?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <tr>
                    <td colspan="5" class="text-center py-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted mb-3">No school years found. Create your first school year to get started.</p>
                        <a href="{{ route('admin.school-years.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create School Year
                        </a>
                    </td>
                </tr>
            @endif
        </div>
    </div>
            </main>
        </div>
    </div>
</div>
@endsection

