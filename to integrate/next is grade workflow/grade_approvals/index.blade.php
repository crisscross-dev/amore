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

<div class="dashboard-container grade-approvals-page">
    <div class="container-fluid px-4">
        <div class="row">
            @include('partials.admin-sidebar')

            <main class="col-lg-9 col-md-8">
                <div class="welcome-card">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-7">
                            <h4 class="mb-2">
                                <i class="fas fa-graduation-cap me-2"></i>
                                Grade Approvals
                            </h4>
                            <p class="mb-0 opacity-90">
                                Review submitted grades, track pending approvals, and coordinate with faculty teams.
                            </p>
                        </div>
                        <div class="col-lg-5">
                            <div class="quick-link-card">
                                <span class="text-uppercase small fw-semibold text-white-50">Quick access</span>
                                <a href="{{ route('admin.faculty-assignments.index') }}">
                                    <i class="fas fa-user-check"></i>
                                    Faculty Assignments
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faculty-management-card mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">
                            <i class="fas fa-list-check me-2"></i>
                            Upcoming Review Tasks
                        </h5>

                        @if(empty($upcomingTasks))
                        <div class="faculty-management-empty">
                            <i class="fas fa-calendar-check"></i>
                            <h5 class="fw-semibold mb-2">No pending tasks right now</h5>
                            <p class="mb-0">All grade submissions are up to date. New tasks will appear here as they arrive.</p>
                        </div>
                    @else
                        <div class="row g-3">
                            @foreach($upcomingTasks as $task)
                                <div class="col-md-6">
                                    <div class="assignment-summary h-100">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="mb-0">{{ $task['title'] }}</h6>
                                            @php
                                                $badgeClass = $task['status'] === 'in-progress' ? 'bg-warning text-dark' : 'bg-success';
                                            @endphp
                                            <span class="badge {{ $badgeClass }} text-uppercase">{{ str_replace('-', ' ', $task['status']) }}</span>
                                        </div>
                                        <p class="mb-2 text-muted">{{ $task['description'] }}</p>
                                        <div class="small text-success fw-semibold">
                                            <i class="fas fa-clock me-1"></i>Due {{ $task['deadline'] }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                <div class="faculty-management-card">
                    <div class="card-body">
                        <h5 class="mb-3 d-flex align-items-center gap-2">
                            <i class="fas fa-inbox"></i>
                            Submitted Grades
                        </h5>

                        @if($submissions->count() === 0)
                            <div class="faculty-management-empty">
                                <i class="fas fa-check-circle"></i>
                                <h5 class="fw-semibold mb-2">No submissions to review</h5>
                                <p class="mb-0">Submitted grades will appear here for approval.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Subject</th>
                                            <th>Term</th>
                                            <th>Grade</th>
                                            <th>Submitted At</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($submissions as $submission)
                                            <tr>
                                                <td>{{ $submission->student->first_name ?? 'Student' }} {{ $submission->student->last_name ?? '' }}</td>
                                                <td>{{ $submission->subject->name ?? 'Subject' }}</td>
                                                <td>{{ $submission->term }}</td>
                                                <td>{{ number_format($submission->grade_value, 2) }}</td>
                                                <td>{{ $submission->submitted_at }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('admin.grade-approvals.show', $submission) }}" class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-eye me-1"></i>Review
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $submissions->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection
