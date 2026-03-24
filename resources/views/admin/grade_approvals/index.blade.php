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
                            <i class="fas fa-clipboard-check"></i>
                            Submitted Grades Awaiting Approval
                        </h5>

                        @if($submissions->isEmpty())
                        <div class="faculty-management-empty">
                            <i class="fas fa-hourglass-half"></i>
                            <h5 class="fw-semibold mb-2">No submitted grades</h5>
                            <p class="mb-0">Grade submissions from faculty will appear here for review and approval.</p>
                        </div>
                        @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Subject</th>
                                        <th>Term</th>
                                        <th>Grade</th>
                                        <th>Submitted By</th>
                                        <th>Submitted At</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($submissions as $grade)
                                    <tr>
                                        <td>{{ $grade->student->first_name }} {{ $grade->student->last_name }}</td>
                                        <td>{{ $grade->subject->name }}</td>
                                        <td><span class="badge bg-info">{{ $grade->term }}</span></td>
                                        <td><strong>{{ number_format($grade->grade_value, 2) }}</strong></td>
                                        <td>{{ $grade->creator->first_name }} {{ $grade->creator->last_name }}</td>
                                        <td>{{ $grade->submitted_at->format('M d, Y h:i A') }}</td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.grade-approvals.show', $grade) }}" class="btn btn-outline-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <form action="{{ route('admin.grade-approvals.approve', $grade) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button class="btn btn-success" onclick="return confirm('Approve this grade?')">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                </form>
                                            </div>
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