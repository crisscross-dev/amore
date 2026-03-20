@extends('layouts.app')

@section('title', 'Edit School Year - Admin Dashboard - Amore Academy')

@section('content')

@vite(['resources/css/layouts/dashboard-roles/dashboard-admin.css'])

<div class="dashboard-container">
    <div class="container-fluid px-4">
        <div class="row">
            <main class="col-12">
                <div class="welcome-card mb-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-2"><i class="fas fa-edit me-2"></i>Edit School Year</h4>
                        <p class="mb-0 opacity-90">Update {{ $schoolYear->year_name }}</p>
                    </div>
                    <a href="{{ route('admin.school-years.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back</a>
                </div>

                <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit {{ $schoolYear->year_name }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.school-years.update', $schoolYear) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="year_name" class="form-label">School Year Name <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                class="form-control @error('year_name') is-invalid @enderror" 
                                id="year_name" 
                                name="year_name" 
                                value="{{ old('year_name', $schoolYear->year_name) }}" 
                                placeholder="e.g., 2026-2027"
                                required
                            >
                            @error('year_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Academic Year Start <span class="text-danger">*</span></label>
                                <input 
                                    type="date" 
                                    class="form-control @error('start_date') is-invalid @enderror" 
                                    id="start_date" 
                                    name="start_date" 
                                    value="{{ old('start_date', $schoolYear->start_date->format('Y-m-d')) }}"
                                    required
                                >
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">Academic Year End <span class="text-danger">*</span></label>
                                <input 
                                    type="date" 
                                    class="form-control @error('end_date') is-invalid @enderror" 
                                    id="end_date" 
                                    name="end_date" 
                                    value="{{ old('end_date', $schoolYear->end_date->format('Y-m-d')) }}"
                                    required
                                >
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="enrollment_start" class="form-label">Enrollment Period Start <span class="text-danger">*</span></label>
                                <input 
                                    type="date" 
                                    class="form-control @error('enrollment_start') is-invalid @enderror" 
                                    id="enrollment_start" 
                                    name="enrollment_start" 
                                    value="{{ old('enrollment_start', $schoolYear->enrollment_start->format('Y-m-d')) }}"
                                    required
                                >
                                @error('enrollment_start')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="enrollment_end" class="form-label">Enrollment Period End <span class="text-danger">*</span></label>
                                <input 
                                    type="date" 
                                    class="form-control @error('enrollment_end') is-invalid @enderror" 
                                    id="enrollment_end" 
                                    name="enrollment_end" 
                                    value="{{ old('enrollment_end', $schoolYear->enrollment_end->format('Y-m-d')) }}"
                                    required
                                >
                                @error('enrollment_end')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('admin.school-years.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>Update School Year
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

