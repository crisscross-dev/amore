@extends('layouts.app-student')

@section('title', 'Enroll - Student Dashboard - Amore Academy')

@section('content')
@vite(['resources/css/layouts/dashboard-roles/dashboard-student.css'])

<div class="dashboard-container">
    <div class="container-fluid px-4">
        <div class="row">
            <main class="col-12">
                <div class="welcome-card mb-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-2"><i class="fas fa-file-alt me-2"></i>Enroll for {{ $activeSchoolYear->year_name }}</h4>
                        <p class="mb-0 opacity-90">Submit your enrollment application</p>
                    </div>
                    <a href="{{ route('student.enrollment.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back</a>
                </div>

                <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Enrollment Form</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('student.enrollment.store') }}" method="POST">
                        @csrf

                        <!-- Personal Information Review -->
                        <h6 class="fw-bold mb-3 text-success">Personal Information</h6>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <p><strong class="text-success">Name:</strong> <span class="text-dark">{{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }}</span></p>
                                <p><strong class="text-success">Email:</strong> <span class="text-dark">{{ $student->email }}</span></p>
                                <p><strong class="text-success">Contact:</strong> <span class="text-dark">{{ $student->contact ?? 'N/A' }}</span></p>
                                <p class="text-muted small">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Need to update your information? <a href="{{ route('profile.edit') }}">Edit Profile</a>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <!-- Enrollment Details -->
                        <h6 class="fw-bold mb-3 text-success">Enrollment Details</h6>

                        <div class="mb-3">
                            <label for="current_grade_level" class="form-label">Current Grade Level</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="current_grade_level" 
                                name="current_grade_level" 
                                value="{{ old('current_grade_level', $currentGradeLevel) }}" 
                                readonly
                            >
                        </div>

                        <div class="mb-3">
                            <label for="enrolling_grade_level" class="form-label">Enrolling for Grade Level <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                class="form-control @error('enrolling_grade_level') is-invalid @enderror" 
                                id="enrolling_grade_level" 
                                name="enrolling_grade_level" 
                                value="{{ old('enrolling_grade_level', $suggestedGradeLevel) }}"
                                readonly
                            >
                            @error('enrolling_grade_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Automatically set based on your current grade level</small>
                        </div>

                        <hr>

                        <!-- Terms and Conditions -->
                        <h6 class="fw-bold mb-3 text-success">Terms and Conditions</h6>
                        <div class="mb-3">
                            <div class="form-check">
                                <input 
                                    class="form-check-input @error('terms_accepted') is-invalid @enderror" 
                                    type="checkbox" 
                                    id="terms_accepted" 
                                    name="terms_accepted" 
                                    value="1"
                                    {{ old('terms_accepted') ? 'checked' : '' }}
                                    required
                                >
                                <label class="form-check-label text-secondary" for="terms_accepted">
                                    I hereby certify that all information provided is true and correct. I understand that any false information may result in the cancellation of my enrollment.
                                </label>
                                @error('terms_accepted')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Note:</strong> After submitting, your enrollment will be reviewed by the admin.
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('student.enrollment.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>Submit Enrollment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            </main>
        </div>
    </div>
</div>
@endsection

