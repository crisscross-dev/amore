@extends('layouts.app')

@section('title', 'Review Grade - Admin Dashboard - Amore Academy')

@section('content')
@vite(['resources/css/layouts/dashboard-roles/dashboard-admin.css','resources/css/admin/faculty-management.css','resources/css/admin/grade-management.css'])

<div class="dashboard-container">
  <div class="container-fluid px-4">
    <div class="row">
      @include('partials.admin-sidebar')

      <main class="col-lg-9 col-md-8">
        <div class="welcome-card mb-4">
          <h4 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Review Grade Submission</h4>
        </div>

        <div class="faculty-management-card p-4">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="fw-semibold">Student</div>
              <div>{{ $grade->student->first_name ?? 'Student' }} {{ $grade->student->last_name ?? '' }}</div>
            </div>
            <div class="col-md-6">
              <div class="fw-semibold">Subject</div>
              <div>{{ $grade->subject->name ?? 'Subject' }}</div>
            </div>
            <div class="col-md-6">
              <div class="fw-semibold">Term</div>
              <div>{{ $grade->term }}</div>
            </div>
            <div class="col-md-6">
              <div class="fw-semibold">Grade Value</div>
              <div>{{ number_format($grade->grade_value, 2) }}</div>
            </div>
            <div class="col-md-6">
              <div class="fw-semibold">Submitted At</div>
              <div>{{ $grade->submitted_at }}</div>
            </div>
          </div>
        </div>

        <div class="faculty-management-card p-4 mt-3">
          <form action="{{ route('admin.grade-approvals.approve', $grade) }}" method="POST" class="d-inline-block me-2">
            @csrf
            @method('PATCH')
            <button class="btn btn-green"><i class="fas fa-check me-2"></i>Approve</button>
          </form>
          <form action="{{ route('admin.grade-approvals.reject', $grade) }}" method="POST" class="d-inline-block">
            @csrf
            @method('PATCH')
            <div class="input-group">
              <input type="text" name="reason" class="form-control" placeholder="Reason for rejection" required>
              <button class="btn btn-outline-danger"><i class="fas fa-times me-2"></i>Reject</button>
            </div>
          </form>
        </div>
      </main>
    </div>
  </div>
</div>
@endsection