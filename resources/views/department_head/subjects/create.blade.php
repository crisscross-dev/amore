@extends('layouts.app')

@section('title', 'Create Subject - Department Head Dashboard - Amore Academy')

@section('content')
<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@vite([
    'resources/css/layouts/dashboard-roles/dashboard-admin.css',
    'resources/css/admin/faculty-management.css',
])

<div class="dashboard-container">
    <div class="container-fluid px-4">
        <div class="row">

            <main class="col-12">
                <div class="welcome-card">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-7">
                            <h4 class="mb-2">
                                <i class="fas fa-plus-circle me-2"></i>
                                Add New Subject
                            </h4>
                            <p class="mb-0 opacity-90">
                                Capture essential information for your department's subject offerings.
                            </p>
                        </div>
                        <div class="col-lg-5">
                            <div class="quick-link-card">
                                <span class="text-uppercase small fw-semibold text-white-50">Need a shortcut?</span>
                                <a href="{{ route($routeBase . 'index') }}">
                                    <i class="fas fa-book-open"></i>
                                    View Subject Catalog
                                </a>
                                <a href="{{ route('admin.grade-approvals.index') }}">
                                    <i class="fas fa-graduation-cap"></i>
                                    Review Grade Submissions
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faculty-management-card">
                    <a href="{{ route($routeBase . 'index') }}" class="back-link mb-3">
                        <i class="fas fa-arrow-left"></i>
                        Back to subjects
                    </a>

                    @include('admin.subjects._form', [
                        'subject' => $subject,
                        'subjectTypes' => $subjectTypes,
                        'gradeLevels' => $gradeLevels,
                        'shsGradeLevels' => $shsGradeLevels,
                        'action' => route($routeBase . 'store'),
                        'method' => 'POST',
                        'submitLabel' => 'Create Subject',
                        'routeBase' => $routeBase,
                    ])
                </div>
            </main>
        </div>
    </div>
</div>
@endsection

