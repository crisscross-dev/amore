@extends('layouts.app')

@section('title', 'Grade Approvals - Admin Dashboard - Amore Academy')

@section('content')
<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@vite([
'resources/css/layouts/dashboard-roles/dashboard-admin.css',
'resources/css/admin/faculty-management.css',
'resources/css/admin/grade-approvals.css',
])

<style>
    .grade-approval-card {
        padding: 1rem 1.1rem;
    }

    .grade-approval-card h5,
    .grade-approval-card h6 {
        margin-bottom: 0.25rem;
    }

    .grade-approval-card .small {
        line-height: 1.25;
    }
</style>

<div class="dashboard-container grade-approvals-page">
    <div class="container-fluid px-4">
        <div class="row">

            <main class="col-12">
                <div class="header-title d-flex align-items-center justify-content-between mb-2">
                    <h5 class="mb-2 fw-semibold text-success">
                        <i class="fas fa-graduation-cap me-2"></i>
                        Grade Approvals
                    </h5>
                    <!-- <div class="d-none d-lg-block">
                        <a href="{{ route('admin.sections.create') }}" class="btn btn-primary btn-m">
                            <i class="fas fa-plus me-2"></i>New Section
                        </a>
                    </div> -->
                </div>

                <div class="faculty-management-card">
                    <div class="card-body">
                        <h5 class="mb-3 d-flex align-items-center gap-2">
                            <i class="fas fa-clipboard-check"></i>
                            Submitted Grade Sheets Awaiting Approval
                        </h5>

                        @if($sheetGroups->isEmpty())
                        <div class="faculty-management-empty">
                            <i class="fas fa-hourglass-half"></i>
                            <h5 class="fw-semibold mb-2">No submitted grades</h5>
                            <p class="mb-0">Grade sheets from faculty will appear here for review and approval.</p>
                        </div>
                        @else
                        <div class="row g-3">
                            @foreach($sheetGroups as $sheet)
                            <div class="col-12">
                                <a href="{{ route('admin.grade-approvals.show', $sheet['representative']) }}" class="assignment-summary grade-approval-card d-block text-decoration-none h-100">
                                    <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                                        <div>
                                            <h5 class="mb-1 text-success">{{ $sheet['grade_level'] }} - {{ $sheet['section_name'] }}</h5>
                                            <div class="small text-muted">{{ $sheet['subject_name'] }}</div>
                                            <div class="small text-muted">{{ $sheet['teacher_name'] }}</div>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-success text-uppercase">{{ $sheet['student_count'] }} students</span>
                                            <div class="small text-muted mt-1">{{ $sheet['term'] }}</div>
                                        </div>
                                    </div>
                                    <div class="mt-2 small text-muted">
                                        <i class="fas fa-clock me-1"></i>{{ $sheet['submitted_at'] }}
                                    </div>
                                </a>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection