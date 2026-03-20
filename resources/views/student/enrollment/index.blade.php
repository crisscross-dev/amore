@extends('layouts.app')

@section('title', 'My Enrollments - Student Dashboard - Amore Academy')

@section('content')
@vite(['resources/css/layouts/dashboard-roles/dashboard-student.css'])

<div class="dashboard-container">
    <div class="container-fluid px-4">
        <div class="row">
            @include('partials.student-sidebar')

            <main class="col-lg-9 col-md-8">
                <div class="welcome-card mb-4">
                    <h4 class="mb-2"><i class="fas fa-clipboard-list me-2"></i>My Enrollments</h4>
                    <p class="mb-0 opacity-90">View your enrollment status and history</p>
                </div>

                @if(session('success'))
                    <x-ui.alert type="success" :dismissible="true">
                        {{ session('success') }}
                    </x-ui.alert>
                @endif

                @if(session('error'))
                    <x-ui.alert type="danger" :dismissible="true">
                        {{ session('error') }}
                    </x-ui.alert>
                @endif

    <!-- Enrollment Status Card -->
    @if($activeSchoolYear)
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-calendar-check me-2"></i>Enrollment for {{ $activeSchoolYear->year_name }}
            </div>
            <div class="card-body">
                @if($canEnroll && !$hasEnrolled)
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Enrollment is now open!</strong> 
                        Enrollment period: {{ $activeSchoolYear->enrollment_start->format('M d, Y') }} - {{ $activeSchoolYear->enrollment_end->format('M d, Y') }}
                    </div>
                    <a href="{{ route('student.enrollment.create') }}" class="btn btn-success btn-lg">
                        <i class="bi bi-file-earmark-plus me-2"></i>Enroll Now
                    </a>
                @elseif($hasEnrolled)
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>
                        <strong>You have already enrolled for {{ $activeSchoolYear->year_name }}</strong>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Enrollment is currently closed.</strong>
                        @if($activeSchoolYear->enrollment_start > now())
                            Enrollment will open on {{ $activeSchoolYear->enrollment_start->format('M d, Y') }}.
                        @else
                            Enrollment period has ended.
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Enrollment History -->
    <div class="card">
        <div class="card-header bg-success text-white">
            <i class="bi bi-clock-history me-2"></i>Enrollment History
        </div>
        <div class="card-body">
            @if($enrollments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>School Year</th>
                                <th>Grade Level</th>
                                <th>Section</th>
                                <th>Enrollment Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enrollments as $enrollment)
                                <tr>
                                    <td>{{ $enrollment->schoolYear->year_name }}</td>
                                    <td>
                                        {{ $enrollment->current_grade_level }} → <strong>{{ $enrollment->enrolling_grade_level }}</strong>
                                    </td>
                                    <td>{{ $enrollment->section->name ?? 'Not assigned' }}</td>
                                    <td>{{ $enrollment->enrollment_date->format('M d, Y') }}</td>
                                    <td>
                                        @if($enrollment->status === 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @elseif($enrollment->status === 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($enrollment->status === 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @else
                                            <span class="badge bg-info">{{ ucfirst($enrollment->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('student.enrollment.show', $enrollment) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye me-1"></i>View Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted text-center py-4">No enrollment history found.</p>
            @endif
        </div>
    </div>
            </main>
        </div>
    </div>
</div>
@endsection
