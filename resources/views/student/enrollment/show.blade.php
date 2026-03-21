@extends('layouts.app-student')

@section('title', 'Enrollment Details - Student Dashboard - Amore Academy')

@section('content')
@vite(['resources/css/layouts/dashboard-roles/dashboard-student.css'])

<div class="dashboard-container">
    <div class="container-fluid px-4">
        <div class="row">
            <main class="col-12">
                <div class="welcome-card mb-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-2"><i class="fas fa-file-alt me-2"></i>Enrollment Details</h4>
                        <p class="mb-0 opacity-90">View your enrollment information</p>
                    </div>
                    <a href="{{ route('student.enrollment.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back</a>
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

                <!-- Enrollment Information Card -->
                <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Enrollment Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong class="text-success">School Year:</strong> <span class="text-dark">{{ $enrollment->schoolYear->year_name }}</span></p>
                            <p><strong class="text-success">Current Grade Level:</strong> <span class="text-dark">{{ $enrollment->current_grade_level }}</span></p>
                            <p><strong class="text-success">Enrolling for:</strong> <span class="badge bg-info">{{ $enrollment->enrolling_grade_level }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong class="text-success">Enrollment Date:</strong> <span class="text-dark">{{ $enrollment->enrollment_date->format('M d, Y h:i A') }}</span></p>
                            <p><strong class="text-success">Status:</strong> 
                                @if($enrollment->status === 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($enrollment->status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($enrollment->status === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </p>
                            @if($enrollment->section)
                                <p><strong class="text-success">Assigned Section:</strong> <span class="text-dark">{{ $enrollment->section->name }}</span></p>
                            @endif
                        </div>
                    </div>

                    @if($enrollment->admin_remarks)
                        <div class="alert alert-info">
                            <strong>Admin Remarks:</strong><br>
                            {{ $enrollment->admin_remarks }}
                        </div>
                    @endif

                    @if($enrollment->status === 'approved' && $enrollment->approvedBy)
                        <p class="text-muted small">
                            Approved by {{ $enrollment->approvedBy->first_name }} {{ $enrollment->approvedBy->last_name }} 
                            on {{ $enrollment->approved_at->format('M d, Y h:i A') }}
                        </p>
                    @endif
                </div>
            </div>


                </div>
            </main>
        </div>
    </div>
</div>
@endsection

