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
</style>

<div class="dashboard-container">
    <div class="container-fluid px-4">
        <div class="row">
            <main class="col-12">
                <div class="welcome-card mb-4">
                    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                        <div>
                            <p class="mb-1 opacity-90"><strong>Subject:</strong> {{ $assignment->subject->name ?? 'N/A' }}</p>
                            <p class="mb-1 opacity-90"><strong>Section:</strong> {{ $assignment->section->name ?? 'N/A' }}</p>
                            <p class="mb-0 opacity-90"><strong>Teacher:</strong> {{ $assignment->teacher->first_name ?? '' }} {{ $assignment->teacher->last_name ?? '' }}</p>
                        </div>
                        <a href="{{ route('admin.grade-approvals.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to approvals
                        </a>
                    </div>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

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