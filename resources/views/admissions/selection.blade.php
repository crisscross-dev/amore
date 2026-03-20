@extends('layouts.admission')

@section('title', 'Admission Selection - Amore Academy')

@section('content')
    <div class="selection-wrapper">
        <div class="selection-container">
            <div class="selection-header">
                <h1><i class="fas fa-school me-3"></i>Admission Portal</h1>
                <p>Select your school level to begin the admission process</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="selection-cards">
                <!-- JHS Card -->
                <div class="selection-card" onclick="window.location.href='{{ route('admissions.jhs') }}'">
                    <div class="selection-card-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h2 class="selection-card-title">Junior High School</h2>
                    <p class="selection-card-description">
                        For students entering Grades 7-10
                    </p>
                    <a href="{{ route('admissions.jhs') }}" class="btn btn-success">
                        <i class="fas fa-arrow-right me-2"></i>Apply for JHS
                    </a>
                </div>

                <!-- SHS Card -->
                <div class="selection-card" onclick="window.location.href='{{ route('admissions.shs') }}'">
                    <div class="selection-card-icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h2 class="selection-card-title">Senior High School</h2>
                    <p class="selection-card-description">
                        For students entering Grades 11-12
                    </p>
                    <a href="{{ route('admissions.shs') }}" class="btn btn-success">
                        <i class="fas fa-arrow-right me-2"></i>Apply for SHS
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
