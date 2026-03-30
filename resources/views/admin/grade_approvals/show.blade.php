@extends('layouts.app')

@section('title', 'Review Grade Sheet - Admin Dashboard - Amore Academy')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@vite(['resources/css/layouts/dashboard-roles/dashboard-admin.css','resources/css/admin/faculty-management.css','resources/css/admin/grade-management.css'])

<style>
    .sheet-readonly input,
    .sheet-readonly textarea {
        background-color: #f8fafc;
    }

    .grade-sheet-table th,
    .grade-sheet-table td {
        padding-top: 0.6rem;
        padding-bottom: 0.6rem;
        vertical-align: middle;
    }

    .grade-sheet-input {
        min-height: 2.1rem;
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
        font-size: 0.92rem;
    }

    .grade-sheet-input[type="number"]::-webkit-outer-spin-button,
    .grade-sheet-input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .grade-sheet-input[type="number"] {
        -moz-appearance: textfield;
        appearance: textfield;
    }

    .approval-summary-card {
        border: 1px solid rgba(22, 101, 52, 0.15);
        border-radius: 14px;
    }

    .approval-summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
        gap: 0.75rem;
        margin-top: 0.9rem;
    }

    .approval-summary-item {
        background: #f8fcfa;
        border: 1px solid rgba(22, 101, 52, 0.12);
        border-radius: 10px;
        padding: 0.55rem 0.7rem;
    }

    .approval-summary-label {
        font-size: 0.74rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        margin-bottom: 0.12rem;
    }

    .approval-summary-value {
        font-size: 0.94rem;
        font-weight: 600;
        color: #14532d;
        line-height: 1.25;
    }
</style>

<div class="dashboard-container">
    <div class="container-fluid px-4">
        <div class="row">
            <main class="col-12">
                <div class="faculty-management-card approval-summary-card mb-4">
                    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                        <div>
                            <h5 class="mb-1 text-success fw-semibold">
                                <i class="fas fa-file-signature me-2"></i>Review Submitted Grade Sheet
                            </h5>
                            <p class="mb-0 text-muted small">Verify entries, adjust if needed, then approve the sheet.</p>
                        </div>
                        <a href="{{ route('admin.grade-approvals.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to Approval
                        </a>
                    </div>

                    <div class="approval-summary-grid">
                        <div class="approval-summary-item">
                            <div class="approval-summary-label">Subject</div>
                            <div class="approval-summary-value">{{ $assignment->subject->name ?? 'N/A' }}</div>
                        </div>
                        <div class="approval-summary-item">
                            <div class="approval-summary-label">Section</div>
                            <div class="approval-summary-value">{{ $assignment->section->name ?? 'N/A' }}</div>
                        </div>
                        <div class="approval-summary-item">
                            <div class="approval-summary-label">Teacher</div>
                            <div class="approval-summary-value">{{ trim(($assignment->teacher->first_name ?? '') . ' ' . ($assignment->teacher->last_name ?? '')) ?: 'N/A' }}</div>
                        </div>
                        <div class="approval-summary-item">
                            <div class="approval-summary-label">Term</div>
                            <div class="approval-summary-value">{{ $term }}</div>
                        </div>
                    </div>
                </div>


                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @include('admin.grade_approvals._sheet')
            </main>
        </div>
    </div>
</div>
@endsection
