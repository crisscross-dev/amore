@extends('layouts.app')

@section('title', 'Manage Accounts - Admin Dashboard - Amore Academy')

@section('content')

@php
$activeTab = request('tab', 'students');
if (!in_array($activeTab, ['students', 'faculty', 'for-approval'], true)) {
$activeTab = 'students';
}
@endphp

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Admin Dashboard CSS -->
@vite(['resources/css/layouts/dashboard-roles/dashboard-admin.css', 'resources/js/admin-accounts.js', 'resources/js/student-sections.js'])

<style>
    /* Remove default modal backdrop */
    .modal-backdrop {
        display: none !important;
    }

    /* Set z-index on modal wrapper */
    .modal {
        z-index: 1050 !important;
    }

    /* Make modal text readable */
    .modal-content {
        color: #212529;
    }

    .modal-body {
        color: #212529;
    }

    .modal-header .modal-title {
        color: #212529;
    }

    .modal-body h5 {
        color: #212529;
    }

    .js-account-row {
        cursor: pointer;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Remove any leftover backdrops
        const removeBackdrops = function() {
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        };

        // Add event listeners to all modals
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            modal.addEventListener('hidden.bs.modal', removeBackdrops);
        });

        document.querySelectorAll('.js-account-row[data-view-modal]').forEach(function(row) {
            row.addEventListener('dblclick', function(event) {
                if (event.target.closest('button, a, input, select, textarea, label, form')) {
                    return;
                }

                const selector = row.getAttribute('data-view-modal');
                if (!selector) {
                    return;
                }

                const modalElement = document.querySelector(selector);
                if (!modalElement || !window.bootstrap || !window.bootstrap.Modal) {
                    return;
                }

                window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
            });
        });

        // Remove backdrops on page load
        removeBackdrops();
    });
</script>

<div class="dashboard-container">
    <div class="container-fluid px-4">
        <div class="row">
            <!-- Left Profile Sidebar -->

            <!-- Main Content -->
            <main class="col-12">
                <!-- Mobile Profile (Hidden on Desktop) -->
                <div class="d-md-none mobile-profile mb-4">
                    <div class="text-center">
                        <img src="{{ asset('uploads/profile_picture/' . Auth::user()->profile_picture) }}"
                            alt="Profile Picture"
                            class="rounded-circle mb-3 border border-3 border-white"
                            width="80"
                            height="80">

                        <h5 class="text-white mb-1">{{ Auth::user()->first_name ?? 'Admin' }} {{ Auth::user()->last_name ?? 'Name' }}</h5>
                        <p class="text-white-50 small mb-3">
                            Administrator | {{ Auth::user()->custom_id ?? 'ADMIN-0001' }}
                        </p>

                        <!-- Mobile Navigation -->
                        <div class="row g-2">
                            <div class="col-4">
                                <a href="{{ route('dashboard.admin') }}" class="btn mobile-nav-btn w-100">
                                    <i class="fas fa-tachometer-alt d-block mb-1"></i>
                                    <small>Dashboard</small>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('calendar.index') }}" class="btn mobile-nav-btn w-100">
                                    <i class="fas fa-calendar-alt d-block mb-1"></i>
                                    <small>Calendar</small>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('announcements.index') }}" class="btn mobile-nav-btn w-100">
                                    <i class="fas fa-bullhorn d-block mb-1"></i>
                                    <small>Announce</small>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('admin.admissions.index') }}" class="btn mobile-nav-btn w-100">
                                    <i class="fas fa-user-check d-block mb-1"></i>
                                    <small>Approvals</small>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('admin.accounts.manage') }}" class="btn mobile-nav-btn w-100 active">
                                    <i class="fas fa-users-cog d-block mb-1"></i>
                                    <small>Accounts</small>
                                </a>
                            </div>
                        </div>

                        <hr class="bg-white opacity-25 my-3">

                        <button
                            class="btn logout-btn w-100"
                            onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </button>

                        <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>


                <div class="header-title d-flex align-items-center justify-content-between mb-2">
                    <h5 class="mb-2 fw-semibold text-success">
                        Manage Accounts
                    </h5>
                    <!-- <div class="d-none d-lg-block">
                        <a href="{{ route('calendar.create') }}" class="btn btn-success btn-m">
                            <i class="fas fa-plus me-2"></i>Add Activity
                        </a>
                    </div> -->
                </div>


                <!-- Flash Messages -->
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i>
                    <strong>Success!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <strong>Error!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <!-- Accounts Management -->
                <div class="admissions-card">
                    <div class="card-header">
                        <ul class="nav nav-tabs" id="accountTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $activeTab === 'students' ? 'active' : '' }}" id="students-tab" data-bs-toggle="tab" data-bs-target="#students" type="button" role="tab" aria-controls="students" aria-selected="{{ $activeTab === 'students' ? 'true' : 'false' }}">
                                    <i class="fas fa-user-graduate me-2"></i>Students
                                    <span class="badge bg-success ms-2">{{ $students->total() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $activeTab === 'faculty' ? 'active' : '' }}" id="faculty-tab" data-bs-toggle="tab" data-bs-target="#faculty" type="button" role="tab" aria-controls="faculty" aria-selected="{{ $activeTab === 'faculty' ? 'true' : 'false' }}">
                                    <i class="fas fa-chalkboard-teacher me-2"></i>Faculty
                                    <span class="badge bg-success ms-2">{{ $faculty->total() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $activeTab === 'for-approval' ? 'active' : '' }}" id="for-approval-tab" data-bs-toggle="tab" data-bs-target="#for-approval" type="button" role="tab" aria-controls="for-approval" aria-selected="{{ $activeTab === 'for-approval' ? 'true' : 'false' }}">
                                    <i class="fas fa-clock me-2"></i>For Approval
                                    <span class="badge bg-success ms-2">{{ $pending->total() }}</span>
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content" id="accountTabsContent">
                            <!-- Students Tab -->
                            <div class="tab-pane fade {{ $activeTab === 'students' ? 'show active' : '' }}" id="students" role="tabpanel" aria-labelledby="students-tab">
                                @include('admin.accounts._students_table')
                            </div>

                            <!-- Faculty Tab -->
                            <div class="tab-pane fade {{ $activeTab === 'faculty' ? 'show active' : '' }}" id="faculty" role="tabpanel" aria-labelledby="faculty-tab">
                                @include('admin.accounts._faculty_table')
                            </div>

                            <!-- For Approval -->
                            <div class="tab-pane fade {{ $activeTab === 'for-approval' ? 'show active' : '' }}" id="for-approval" role="tabpanel" aria-labelledby="for-approval-tab">
                                @include('admin.accounts._for_approval_table')
                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>
</div>

@endsection