@extends('layouts.app')

@section('title', 'View Admission Application - Admin Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Admin Dashboard CSS -->
@vite(['resources/css/layouts/dashboard-roles/dashboard-admin.css'])

<style>
/* Remove default modal backdrop */
.modal-backdrop {
    display: none !important;
}

/* Custom backdrop for approve modal */
.custom-approve-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: linear-gradient(135deg, rgba(25, 135, 84, 0.3) 0%, rgba(32, 201, 151, 0.3) 100%);
    backdrop-filter: blur(4px);
    z-index: -1;
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
}

.custom-approve-backdrop.show {
    opacity: 1;
}

/* Set z-index on the modal wrapper to appear above backdrop */
.modal {
    z-index: 1050 !important;
}

/* Enhanced modal content styling */
.modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        transform: translateY(-50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.modal-header {
    background: linear-gradient(135deg, #198754 0%, #20c997 100%);
    color: white;
    border-bottom: none;
    padding: 1.5rem;
}

.modal-header .modal-title {
    color: white;
    font-weight: bold;
    font-size: 1.25rem;
}

.modal-header .btn-close {
    filter: brightness(0) invert(1);
    opacity: 0.8;
    transition: opacity 0.3s;
}

.modal-header .btn-close:hover {
    opacity: 1;
}

.modal-body {
    padding: 2rem;
}

.modal-footer {
    border-top: 1px solid #e9ecef;
    padding: 1.25rem 2rem;
    background: #f8f9fa;
}

.modal-footer .btn {
    border-radius: 8px;
    padding: 0.6rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s;
}

.modal-footer .btn-success {
    background: linear-gradient(135deg, #198754 0%, #20c997 100%);
    border: none;
    box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
}

.modal-footer .btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(25, 135, 84, 0.4);
}

.modal-footer .btn-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    border: none;
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}

.modal-footer .btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
}

.modal-footer .btn-secondary {
    background: white;
    color: #6c757d;
    border: 2px solid #dee2e6;
}

.modal-footer .btn-secondary:hover {
    background: #f8f9fa;
    border-color: #198754;
    color: #198754;
}
</style>

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
                                <a href="{{ route('admin.admissions.index') }}" class="btn mobile-nav-btn w-100 active">
                                    <i class="fas fa-user-check d-block mb-1"></i>
                                    <small>Approvals</small>
                                </a>
                            </div>
                        </div>
                        
                        <hr class="bg-white opacity-25 my-3">
                        
                        <button 
                            class="btn logout-btn w-100"
                            onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();"
                        >
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </button>
                        
                        <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>

<div class="admission-detail-container">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ route('admin.admissions.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
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

    <!-- Show credentials if admission is approved and has temp password -->
    @if($admission->status === 'approved' && $admission->temp_password)
        <div class="alert alert-success" role="alert">
            <div class="mt-3 p-3 bg-white rounded border border-success">
                <h6 class="mb-2"><i class="fas fa-key me-2"></i>Student Login Credentials:</h6>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Email:</strong><br>
                        <code class="text-dark fs-6">{{ $admission->email }}</code>
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
                <small class="text-muted d-block mt-2">
                    <i class="fas fa-info-circle"></i> Please share these credentials with the student securely.
                </small>
            </div>
            
            <script>
                // Use a unique key for this admission
                const storageKey = 'passwordVisible_{{ $admission->id }}';
                let passwordVisible = localStorage.getItem(storageKey) === 'true';
                
                // Apply saved state on page load
                document.addEventListener('DOMContentLoaded', function() {
                    if (passwordVisible) {
                        const passwordElement = document.getElementById('generatedPassword');
                        const toggleIcon = document.getElementById('toggleIcon');
                        const actualPassword = passwordElement.getAttribute('data-password');
                        
                        passwordElement.textContent = actualPassword;
                        toggleIcon.classList.remove('fa-eye');
                        toggleIcon.classList.add('fa-eye-slash');
                    }
                });
                
                function togglePasswordVisibility() {
                    const passwordElement = document.getElementById('generatedPassword');
                    const toggleIcon = document.getElementById('toggleIcon');
                    const actualPassword = passwordElement.getAttribute('data-password');
                    
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
                    // Save state to localStorage
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
        </div>
    @endif

    <!-- Application Header -->
    <div class="admission-detail-header">
        <div>
            <h1><i class="fas fa-user-graduate me-2"></i>{{ $admission->full_name }}</h1>
            <p class="text-muted mb-2">
                <span class="badge bg-primary">{{ strtoupper($admission->school_level) }}</span>
                <!-- <span class="ms-2">Applicant ID: <strong>{{ $admission->applicant_id ?? 'N/A' }}</strong></span> -->
            </p>
        </div>
        <div class="admission-header-status">
            @include('admin.admissions._status_badge', ['status' => $admission->status])
        </div>
    </div>

    <div class="row">
        <!-- Left Column: Application Details -->
        <div class="col-lg-8">
            <!-- Personal Information -->
            <div class="info-card">
                <div class="info-card-header">
                    <h5><i class="fas fa-user"></i> Personal Information</h5>
                </div>
                <div class="info-card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Full Name</label>
                            <div class="info-value">{{ $admission->full_name }}</div>
                        </div>
                        <div class="info-item">
                            <label>LRN</label>
                            <div class="info-value">{{ $admission->lrn }}</div>
                        </div>
                        <div class="info-item">
                            <label>Birthdate</label>
                            <div class="info-value">{{ $admission->dob->format('F d, Y') }}</div>
                        </div>
                        <div class="info-item">
                            <label>Age</label>
                            <div class="info-value">{{ $admission->age }} years old</div>
                        </div>
                        <div class="info-item">
                            <label>Gender</label>
                            <div class="info-value">{{ ucfirst($admission->gender) }}</div>
                        </div>
                        <div class="info-item">
                            <label>Religion</label>
                            <div class="info-value">{{ $admission->religion ?? 'N/A' }}</div>
                        </div>
                        @if(strtoupper($admission->school_level) === 'SHS')
                            <div class="info-item">
                                <label>Citizenship</label>
                                <div class="info-value">{{ $admission->citizenship ?? 'N/A' }}</div>
                            </div>
                            <div class="info-item">
                                <label>Phone Number</label>
                                <div class="info-value">{{ $admission->phone ?? 'N/A' }}</div>
                            </div>
                            <div class="info-item">
                                <label>Email</label>
                                <div class="info-value">{{ $admission->email ?? 'N/A' }}</div>
                            </div>
                            @if($admission->height)
                                <div class="info-item">
                                    <label>Height</label>
                                    <div class="info-value">{{ $admission->height }} cm</div>
                                </div>
                            @endif
                            @if($admission->weight)
                                <div class="info-item">
                                    <label>Weight</label>
                                    <div class="info-value">{{ $admission->weight }} kg</div>
                                </div>
                            @endif
                        @endif
                        <div class="info-item full-width">
                            <label>Address</label>
                            <div class="info-value">{{ $admission->address }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Information -->
            <div class="info-card">
                <div class="info-card-header">
                    <h5><i class="fas fa-book"></i> Academic Information</h5>
                </div>
                <div class="info-card-body">
                    <div class="info-grid">
                        @if(strtoupper($admission->school_level) === 'SHS')
                            <div class="info-item">
                                <label>Strand</label>
                                <div class="info-value">
                                    <span class="badge bg-info">{{ $admission->strand ?? 'N/A' }}</span>
                                </div>
                            </div>
                            @if($admission->tvl_specialization)
                                <div class="info-item">
                                    <label>TVL Specialization</label>
                                    <div class="info-value">{{ $admission->tvl_specialization }}</div>
                                </div>
                            @endif
                        @endif
                        <div class="info-item">
                            <label>Previous School</label>
                            <div class="info-value">{{ $admission->school_name }}</div>
                        </div>
                        <div class="info-item">
                            <label>School Type</label>
                            <div class="info-value">{{ ucfirst($admission->school_type) }}</div>
                        </div>
                        @if($admission->private_type)
                            <div class="info-item">
                                <label>Private School Type</label>
                                <div class="info-value">{{ $admission->private_type }}</div>
                            </div>
                        @endif
                        @if($admission->student_esc_no)
                            <div class="info-item">
                                <label>ESC Student No.</label>
                                <div class="info-value">{{ $admission->student_esc_no }}</div>
                            </div>
                            <div class="info-item">
                                <label>ESC School ID</label>
                                <div class="info-value">{{ $admission->esc_school_id }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Parent Information -->
            <div class="info-card">
                <div class="info-card-header">
                    <h5><i class="fas fa-users"></i> Parent/Guardian Information</h5>
                </div>
                <div class="info-card-body">
                    <div class="parent-section">
                        <h6><i class="fas fa-male me-2"></i>Father's Information</h6>
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Name</label>
                                <div class="info-value">{{ $admission->father_name ?? 'N/A' }}</div>
                            </div>
                            <div class="info-item">
                                <label>Occupation</label>
                                <div class="info-value">{{ $admission->father_occupation ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="parent-section mt-3">
                        <h6><i class="fas fa-female me-2"></i>Mother's Information</h6>
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Name</label>
                                <div class="info-value">{{ $admission->mother_name ?? 'N/A' }}</div>
                            </div>
                            <div class="info-item">
                                <label>Occupation</label>
                                <div class="info-value">{{ $admission->mother_occupation ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approval/Rejection Details -->
            @if($admission->status === 'approved' || $admission->status === 'rejected')
                <div class="info-card">
                    <div class="info-card-header">
                        <h5>
                            <i class="fas {{ $admission->status === 'approved' ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }}"></i>
                            {{ ucfirst($admission->status) }} Details
                        </h5>
                    </div>
                    <div class="info-card-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <label>{{ ucfirst($admission->status) }} By</label>
                                <div class="info-value">
                                    {{ $admission->approvedBy ? $admission->approvedBy->name : 'N/A' }}
                                </div>
                            </div>
                            <div class="info-item">
                                <label>{{ ucfirst($admission->status) }} At</label>
                                <div class="info-value">
                                    {{ $admission->approved_at ? $admission->approved_at->format('F d, Y h:i A') : 'N/A' }}
                                </div>
                            </div>
                            @if($admission->approval_notes)
                                <div class="info-item full-width">
                                    <label>Approval Notes</label>
                                    <div class="info-value">{{ $admission->approval_notes }}</div>
                                </div>
                            @endif
                            @if($admission->rejection_reason)
                                <div class="info-item full-width">
                                    <label>Rejection Reason</label>
                                    <div class="info-value text-danger">{{ $admission->rejection_reason }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column: Actions -->
        <div class="col-lg-4">
            <!-- Application Timeline -->
            <div class="info-card">
                <div class="info-card-header">
                    <h5><i class="fas fa-history"></i> Application Timeline</h5>
                </div>
                <div class="info-card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-icon timeline-icon-primary">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-title">Application Submitted</div>
                                <div class="timeline-date">{{ $admission->created_at->format('M d, Y h:i A') }}</div>
                            </div>
                        </div>
                        
                        @if($admission->approved_at)
                            <div class="timeline-item">
                                <div class="timeline-icon {{ $admission->status === 'approved' ? 'timeline-icon-success' : 'timeline-icon-danger' }}">
                                    <i class="fas {{ $admission->status === 'approved' ? 'fa-check' : 'fa-times' }}"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-title">{{ ucfirst($admission->status) }}</div>
                                    <div class="timeline-date">{{ $admission->approved_at->format('M d, Y h:i A') }}</div>
                                    @if($admission->approvedBy)
                                        <div class="timeline-meta">by {{ $admission->approvedBy->name }}</div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            @if($admission->status === 'pending')
                <div class="info-card">
                    <div class="info-card-header">
                        <h5><i class="fas fa-tasks"></i> Actions</h5>
                    </div>
                    <div class="info-card-body">
                        <!-- Approve Button -->
                        <button type="button" class="btn btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#approveModal">
                            <i class="fas fa-check"></i> Approve Application
                        </button>
                        
                        <!-- Remove Application Button -->
                        <button type="button" class="btn btn-danger w-100 mb-2" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash-alt"></i> Remove Application
                        </button>
                    </div>
                </div>
            @endif

            <!-- Show Generated Password Button for Approved Admissions -->
            @if($admission->status === 'approved' && $admission->temp_password)
                <div class="info-card">
                    <div class="info-card-header">
                        <h5><i class="fas fa-key"></i> Login Credentials</h5>
                    </div>
                    <div class="info-card-body">
                        <button type="button" class="btn btn-primary w-100" onclick="scrollToPassword()">
                            <i class="fas fa-eye"></i> Show Generated Password
                        </button>
                        <small class="text-muted d-block mt-2 text-center">
                            <i class="fas fa-info-circle"></i> View student login credentials
                        </small>
                    </div>
                </div>
            @endif

            <!-- Quick Info -->
            <div class="info-card">
                <div class="info-card-header">
                    <h5><i class="fas fa-info-circle"></i> Quick Information</h5>
                </div>
                <div class="info-card-body">
                    <div class="quick-info-item">
                        <i class="fas fa-graduation-cap text-primary"></i>
                        <div>
                            <div class="quick-info-label">Admission Type</div>
                            <div class="quick-info-value">{{ strtoupper($admission->school_level) }}</div>
                        </div>
                    </div>
                    <div class="quick-info-item">
                        <i class="fas fa-calendar-alt text-info"></i>
                        <div>
                            <div class="quick-info-label">Applied On</div>
                            <div class="quick-info-value">{{ $admission->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <script>
                function scrollToPassword() {
                    const passwordSection = document.querySelector('.alert.alert-success');
                    if (passwordSection) {
                        passwordSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        // Add a highlight animation
                        passwordSection.style.animation = 'pulse 1s ease-in-out';
                        setTimeout(() => {
                            passwordSection.style.animation = '';
                        }, 1000);
                    }
                }
            </script>
            
            <style>
                @keyframes pulse {
                    0%, 100% { transform: scale(1); }
                    50% { transform: scale(1.02); box-shadow: 0 0 20px rgba(25, 135, 84, 0.4); }
                }
            </style>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-check-circle me-2"></i>Approve Application</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.admissions.approve', ['type' => $admission->school_level, 'id' => $admission->id]) }}">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        You are about to approve the application of <strong>{{ $admission->full_name }}</strong>.
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> A student account will be automatically created with the following credentials:
                        <ul class="mb-0 mt-2">
                            <li><strong>Email:</strong> {{ $admission->email }}</li>
                            <li><strong>Password:</strong> <span class="text-muted">Random 8-character alphanumeric password (will be displayed after approval)</span></li>
                        </ul>
                    </div>
                    
                    <div class="mb-3">
                        <label for="approvalNotes" class="form-label">Approval Notes (Optional)</label>
                        <textarea class="form-control" id="approvalNotes" name="approval_notes" 
                                  rows="3" placeholder="Add any notes about this approval..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Approve & Create Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal" id="rejectModal" tabindex="1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-times-circle me-2"></i>Reject Application</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.admissions.reject', ['type' => $admission->school_level, 'id' => $admission->id]) }}">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        You are about to reject the application of <strong>{{ $admission->full_name }}</strong>.
                    </div>
                    
                    <div class="mb-3">
                        <label for="rejectionReason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejectionReason" name="rejection_reason" 
                                  rows="4" required placeholder="Provide a clear reason for rejection..."></textarea>
                        <small class="text-muted">This reason will be recorded for future reference.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Reject Application
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-trash-alt me-2"></i>Remove Application</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.admissions.destroy', ['type' => $admission->school_level, 'id' => $admission->id]) }}">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Warning:</strong> This action cannot be undone!
                    </div>
                    
                    <p>You are about to permanently remove the application of:</p>
                    <div class="bg-light p-3 rounded mb-3">
                        <h6 class="mb-2"><i class="fas fa-user me-2"></i>{{ $admission->full_name }}</h6>
                        <small class="text-muted">
                            <strong>LRN:</strong> {{ $admission->lrn }}<br>
                            <strong>Email:</strong> {{ $admission->email }}<br>
                            <strong>Level:</strong> {{ strtoupper($admission->school_level) }}
                        </small>
                    </div>
                    
                    <p class="text-danger mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        <strong>Note:</strong> All application data will be permanently deleted from the system.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-1"></i> Yes, Remove Application
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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

    // Custom backdrop for approve modal
    let customBackdrop = null;
    const approveModal = document.getElementById('approveModal');
    
    if (approveModal) {
        // Show custom backdrop when modal opens
        approveModal.addEventListener('show.bs.modal', function() {
            // Create custom backdrop
            customBackdrop = document.createElement('div');
            customBackdrop.className = 'custom-approve-backdrop';
            document.body.appendChild(customBackdrop);
            
            // Trigger animation
            setTimeout(() => {
                customBackdrop.classList.add('show');
            }, 10);
            
            // Prevent body scroll
            document.body.style.overflow = 'hidden';
        });
        
        // Hide custom backdrop when modal closes
        approveModal.addEventListener('hidden.bs.modal', function() {
            if (customBackdrop) {
                customBackdrop.classList.remove('show');
                setTimeout(() => {
                    if (customBackdrop && customBackdrop.parentNode) {
                        customBackdrop.remove();
                    }
                    customBackdrop = null;
                }, 300);
            }
            document.body.style.removeProperty('overflow');
        });
        
        // Close modal when clicking on backdrop
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('custom-approve-backdrop')) {
                const modalInstance = bootstrap.Modal.getInstance(approveModal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            }
        });
    }

    // Add event listeners to all modals
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('hidden.bs.modal', removeBackdrops);
    });

    // Remove backdrops on page load
    removeBackdrops();
});
</script>

            </main>
        </div>
    </div>
</div>

@endsection

