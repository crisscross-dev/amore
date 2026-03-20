@extends('layouts.app')

@section('title', 'Account Details - Admin')

@section('content')

@vite(['resources/css/layouts/dashboard-roles/dashboard-admin.css'])

<div class="dashboard-container">
    <div class="container-fluid px-4">
        <div class="row">
            <!-- Left Profile Sidebar -->

            <!-- Main Content -->
            <main class="col-12">
                <!-- Page Header -->
                <div class="welcome-card mb-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                        <div>
                            <h4 class="mb-2">
                                <i class="fas fa-user me-2"></i>
                                Account Details
                            </h4>
                            <p class="mb-0 opacity-90">View full account information</p>
                        </div>
                        <div class="d-none d-lg-block">
                            <a href="{{ route('admin.accounts.manage') }}" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>Back to List
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Delete Account Modal -->
                <div class="modal" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title text-danger"><i class="fas fa-trash me-2"></i>Delete Account</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="POST" action="{{ route('admin.accounts.destroy', $user) }}">
                                @csrf
                                @method('DELETE')
                                <div class="modal-body">
                                    <p class="text-dark">Are you sure you want to permanently delete this account? This action cannot be undone.</p>
                                    <p class="small text-muted">User: <strong>{{ $user->first_name }} {{ $user->last_name }}</strong> ({{ $user->custom_id }})</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-danger">Delete Account</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Mobile Back Button -->
                <div class="d-lg-none mb-3">
                    <a href="{{ route('admin.accounts.manage') }}" class="btn btn-outline-success w-100">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>

                <!-- Show credentials if user is a student with temp password -->
                @if($user->account_type === 'student' && isset($admission) && $admission && $admission->temp_password)
                    <div class="alert alert-success mb-4" role="alert" id="passwordSection">
                        <div class="p-3 bg-white rounded border border-success">
                            <h6 class="mb-3"><i class="fas fa-key me-2"></i>Student Login Credentials:</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <strong>Email:</strong><br>
                                    <code class="text-dark fs-6">{{ $user->email }}</code>
                                </div>
                                <div class="col-md-6">
                                    <strong>Password:</strong><br>
                                    <div class="d-flex align-items-center gap-2">
                                        <code class="text-danger fs-6" id="generatedPassword" data-password="{{ $admission->temp_password }}">••••••••</code>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="togglePassword" onclick="togglePasswordVisibility()">
                                            <i class="fas fa-eye" id="toggleIcon"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="copyPassword('{{ $admission->temp_password }}')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-3">
                                <i class="fas fa-info-circle"></i> These credentials were generated during admission approval.
                            </small>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <!-- Account Details Column -->
                    <div class="col-lg-8 mb-4">
                        <div class="activity-card" style="border-left: 4px solid #198754;">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user me-2" style="color: #198754;"></i>
                                    <strong>Account Details</strong>
                                </div>
                                <div>
                                    @if($user->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($user->status === 'for_approval')
                                        <span class="badge bg-warning text-dark">For Approval</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <img src="{{ asset('uploads/profile_picture/' . ($user->profile_picture ?? 'default.png')) }}" alt="Profile" class="rounded-circle border border-3" width="96" height="96" style="object-fit: cover;">
                                    <div>
                                        <h3 class="mb-0 fw-bold text-success" style="letter-spacing: -0.5px;">{{ $user->first_name }} {{ $user->last_name }}</h3>
                                        <div class="text-muted small">
                                            <i class="fas fa-id-badge me-1"></i>
                                            {{ $user->custom_id }} &middot; {{ ucfirst($user->account_type) }}
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <h6 class="text-success fw-bold mb-3">Contact</h6>
                                        <ul class="list-unstyled mb-0">
                                            <li class="mb-2">
                                                <i class="fas fa-envelope me-2 text-success"></i>
                                                <span class="fw-semibold">{{ $user->email }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-phone me-2 text-success"></i>
                                                {{ $user->contact_number ?? '-' }}
                                            </li>
                                            @if($user->account_type === 'student')
                                                <li class="mb-2">
                                                    <i class="fas fa-graduation-cap me-2 text-success"></i>
                                                    <span class="fw-semibold">Grade</span>: {{ $user->grade_level ?? '-' }}
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-hashtag me-2 text-success"></i>
                                                    <span class="text-monospace">{{ $user->lrn ?? '-' }}</span>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-success fw-bold mb-3">Details</h6>
                                        <ul class="list-unstyled mb-0">
                                            @if($user->account_type === 'faculty')
                                                <li class="mb-2">
                                                    <i class="fas fa-building me-2 text-success"></i>
                                                    <span class="fw-semibold">Department</span>: {{ $user->department ?? '-' }}
                                                </li>
                                            @endif
                                            <li class="mb-2">
                                                <i class="fas fa-calendar-alt me-2 text-success"></i>
                                                <span class="fw-semibold">Registered</span>: {{ $user->created_at->format('M d, Y') }}
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-user me-2 text-success"></i>
                                                <span class="fw-semibold">Account Type</span>: {{ ucfirst($user->account_type) }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="{{ route('admin.accounts.edit', $user) }}" class="btn btn-success">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                                        <i class="fas fa-trash me-1"></i>Delete
                                    </button>
                                </div>
                            </div>
                        </div>

                        @if($user->account_type === 'student' && isset($admission) && $admission)
                        <!-- Emergency Contact Section -->
                        <div class="activity-card mt-4" style="border-left: 4px solid #dc3545;">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-phone-alt me-2" style="color: #dc3545;"></i>
                                    <strong>Emergency Contact</strong>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <h6 class="text-danger fw-bold mb-2">Contact Name</h6>
                                        <p class="mb-0">{{ $admission->emergency_contact_name ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="text-danger fw-bold mb-2">Relationship</h6>
                                        <p class="mb-0">{{ $admission->emergency_contact_relationship ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="text-danger fw-bold mb-2">Phone Number</h6>
                                        <p class="mb-0">
                                            <i class="fas fa-phone me-2 text-danger"></i>
                                            {{ $admission->emergency_contact_phone ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Sidebar Info -->
                    <div class="col-lg-4">
                        <div class="activity-card mb-4">
                            <div class="card-header bg-success text-white">
                                <i class="fas fa-info-circle me-2"></i>Account Info
                            </div>
                            <div class="card-body">
                                <div class="event-info-item mb-3">
                                    <small class="text-muted d-block">Account ID</small>
                                    <strong>{{ $user->custom_id }}</strong>
                                </div>
                                <div class="event-info-item mb-3">
                                    <small class="text-muted d-block">Status</small>
                                    <strong>{{ ucfirst(str_replace('_', ' ', $user->status)) }}</strong>
                                </div>
                                <div class="event-info-item">
                                    <small class="text-muted d-block">Created On</small>
                                    <strong>{{ $user->created_at->format('M d, Y g:i A') }}</strong>
                                </div>
                                
                                {{-- Debug info - remove after testing --}}
                                @if($user->account_type === 'student')
                                    <div class="alert alert-info mt-3 small">
                                        <strong>Debug Info:</strong><br>
                                        Account Type: {{ $user->account_type }}<br>
                                        Admission Exists: {{ isset($admission) && $admission ? 'Yes' : 'No' }}<br>
                                        @if(isset($admission) && $admission)
                                            Admission Status: {{ $admission->status ?? 'N/A' }}<br>
                                            Has Password: {{ $admission->temp_password ? 'Yes' : 'No' }}<br>
                                            LRN: {{ $admission->lrn ?? 'N/A' }}
                                        @endif
                                    </div>
                                @endif
                                
                                @if($user->account_type === 'student' && isset($admission) && $admission && $admission->temp_password)
                                    <hr class="my-3">
                                    <button type="button" class="btn btn-primary w-100" onclick="scrollToPassword()">
                                        <i class="fas fa-eye me-1"></i> Show Generated Password
                                    </button>
                                    <small class="text-muted d-block mt-2 text-center">
                                        <i class="fas fa-info-circle"></i> View login credentials
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>
</div>

<style>
    /* Remove Bootstrap modal backdrop */
    .modal-backdrop {
        display: none !important;
    }
    
    .modal {
        z-index: 1050 !important;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.02); box-shadow: 0 0 20px rgba(25, 135, 84, 0.4); }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var deleteModal = document.getElementById('deleteAccountModal');
        if (deleteModal) {
            deleteModal.addEventListener('hidden.bs.modal', function() {
                // Remove any leftover backdrops
                document.querySelectorAll('.modal-backdrop').forEach(function(backdrop) {
                    backdrop.remove();
                });
            });
        }
        
        // Password visibility state management
        const storageKey = 'passwordVisible_account_{{ $user->id }}';
        let passwordVisible = localStorage.getItem(storageKey) === 'true';
        
        // Apply saved state on page load
        if (passwordVisible) {
            const passwordElement = document.getElementById('generatedPassword');
            const toggleIcon = document.getElementById('toggleIcon');
            if (passwordElement && toggleIcon) {
                const actualPassword = passwordElement.getAttribute('data-password');
                passwordElement.textContent = actualPassword;
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            }
        }
    });
    
    function scrollToPassword() {
        const passwordSection = document.getElementById('passwordSection');
        if (passwordSection) {
            passwordSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
            // Add a highlight animation
            passwordSection.style.animation = 'pulse 1s ease-in-out';
            setTimeout(() => {
                passwordSection.style.animation = '';
            }, 1000);
        }
    }
    
    function togglePasswordVisibility() {
        const passwordElement = document.getElementById('generatedPassword');
        const toggleIcon = document.getElementById('toggleIcon');
        const actualPassword = passwordElement.getAttribute('data-password');
        const storageKey = 'passwordVisible_account_{{ $user->id }}';
        let passwordVisible = localStorage.getItem(storageKey) === 'true';
        
        if (passwordVisible) {
            passwordElement.textContent = '••••••••';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        } else {
            passwordElement.textContent = actualPassword;
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        }
        
        passwordVisible = !passwordVisible;
        localStorage.setItem(storageKey, passwordVisible);
    }
    
    function copyPassword(password) {
        navigator.clipboard.writeText(password).then(() => {
            // Show temporary success feedback
            const btn = event.target.closest('button');
            const icon = btn.querySelector('i');
            icon.classList.remove('fa-copy');
            icon.classList.add('fa-check');
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-success');
            
            setTimeout(() => {
                icon.classList.remove('fa-check');
                icon.classList.add('fa-copy');
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-primary');
            }, 2000);
        });
    }
</script>

@endsection

