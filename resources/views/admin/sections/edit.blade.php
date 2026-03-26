@extends('layouts.app')

@section('title', 'Edit Section - Admin Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@vite(['resources/css/layouts/dashboard-roles/dashboard-admin.css'])

<div class="dashboard-container">
    <div class="container-fluid px-4">
        <div class="row">
            <main class="col-12">
                <div class="welcome-card mb-4">
                    <h4 class="mb-2"><i class="fas fa-edit me-2"></i>Edit Section</h4>
                    <p class="mb-0 opacity-90">Update section information</p>
                </div>

                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="admissions-card">
                    <div class="card-body">
                        @include('admin.sections._form', [
                        'section' => $section,
                        'schoolYears' => $schoolYears,
                        'subjects' => $subjects,
                        'facultyMembers' => $facultyMembers,
                        'adviserAssignments' => $adviserAssignments,
                        'selectedSubjectIds' => $selectedSubjectIds,
                        'action' => route('admin.sections.update', $section),
                        'method' => 'PUT',
                        'submitLabel' => 'Save Changes',
                        'isModal' => false,
                        'routeBase' => 'admin.sections.',
                        ])
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection