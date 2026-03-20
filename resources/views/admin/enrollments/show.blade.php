@extends('layouts.app')

@section('title', 'Review Enrollment - Admin Dashboard - Amore Academy')

@section('content')

@vite(['resources/css/layouts/dashboard-roles/dashboard-admin.css'])

<div class="dashboard-container">
    <div class="container-fluid px-4">
        <div class="row">
            <main class="col-12">
                <div class="welcome-card mb-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-2"><i class="fas fa-clipboard-check me-2"></i>Review Enrollment</h4>
                        <p class="mb-0 opacity-90">Review and process student enrollment request</p>
                    </div>
                    <a href="{{ route('admin.enrollments.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back to List</a>
                </div>

                @if(session('success'))
                    <x-ui.alert type="success" :dismissible="true">
                        {{ session('success') }}
                    </x-ui.alert>
                @endif

                @if(session('error'))
                    <x-ui.alert type="danger" :dismissible="true">
                        {{ session('error') }}
                    </x-ui.alert>
                @endif

                <div class="row">
        <div class="col-lg-8">
            <!-- Student Information -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-person me-2"></i>Student Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong class="text-success">Name:</strong> <span class="text-dark">{{ $enrollment->student->first_name }} {{ $enrollment->student->middle_name }} {{ $enrollment->student->last_name }}</span></p>
                            <p><strong class="text-success">Email:</strong> <span class="text-dark">{{ $enrollment->student->email }}</span></p>
                            <p><strong class="text-success">Contact:</strong> <span class="text-dark">{{ $enrollment->student->contact ?? 'N/A' }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong class="text-success">Current Grade Level:</strong> <span class="text-dark">{{ $enrollment->current_grade_level }}</span></p>
                            <p><strong class="text-success">Enrolling for:</strong> <span class="badge bg-info">{{ $enrollment->enrolling_grade_level }}</span></p>
                            <p><strong class="text-success">School Year:</strong> <span class="text-dark">{{ $enrollment->schoolYear->year_name }}</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enrollment Details -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Enrollment Details</h5>
                </div>
                <div class="card-body">
                    <p><strong class="text-success">Enrollment Date:</strong> <span class="text-dark">{{ $enrollment->enrollment_date->format('M d, Y h:i A') }}</span></p>
                    <p><strong class="text-success">Status:</strong> 
                        @if($enrollment->status === 'pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($enrollment->status === 'approved')
                            <span class="badge bg-success">Approved</span>
                        @elseif($enrollment->status === 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                        @endif
                    </p>
                    @if($enrollment->section)
                        <p><strong class="text-success">Assigned Section:</strong> <span class="text-dark">{{ $enrollment->section->name }}</span></p>
                    @endif
                    @if($enrollment->admin_remarks)
                        <div class="alert alert-info">
                            <strong class="text-success">Remarks:</strong><br>
                            <span class="text-dark">{{ $enrollment->admin_remarks }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar - Actions -->
        <div class="col-lg-4">
            @if($enrollment->status === 'pending')
                <!-- Approve Form -->
                <div class="card mb-3 border-success">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="bi bi-check-circle me-2"></i>Approve Enrollment</h6>
                    </div>
                    <div class="card-body">
                        <form id="approveForm" action="{{ route('admin.enrollments.approve', $enrollment) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="section_id" class="form-label">Assign Section</label>
                                <select name="section_id" id="section_id" class="form-select">
                                    <option value="">No section yet</option>
                                    @foreach($sections as $section)
                                        <option value="{{ $section->id }}">{{ $section->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">You can assign section later</small>
                            </div>
                            <div class="mb-3">
                                <label for="admin_remarks_approve" class="form-label">Remarks (Optional)</label>
                                <textarea name="admin_remarks" id="admin_remarks_approve" class="form-control" rows="2"></textarea>
                            </div>
                            <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#approveModal">
                                <i class="bi bi-check-circle me-1"></i>Approve
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Reject Form -->
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0"><i class="bi bi-x-circle me-2"></i>Reject Enrollment</h6>
                    </div>
                    <div class="card-body">
                        <form id="rejectForm" action="{{ route('admin.enrollments.reject', $enrollment) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="admin_remarks_reject" class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                                <textarea name="admin_remarks" id="admin_remarks_reject" class="form-control" rows="3" required></textarea>
                            </div>
                            <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="bi bi-x-circle me-1"></i>Reject
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center">
                        <p class="text-muted">This enrollment has been {{ $enrollment->status }}.</p>
                        @if($enrollment->approvedBy)
                            <p class="small">
                                By: {{ $enrollment->approvedBy->first_name }} {{ $enrollment->approvedBy->last_name }}<br>
                                On: {{ $enrollment->approved_at->format('M d, Y h:i A') }}
                            </p>
                        @endif
                    </div>
                </div>
            @endif

            </div>
                </div>
            </main>
        </div>
    </div>
</div>

<!-- Approve Confirmation Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="approveModalLabel"><i class="bi bi-check-circle me-2"></i>Confirm Approval</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-dark">Are you sure you want to <strong>approve</strong> this enrollment?</p>
                <p class="text-muted small mb-0">The student will be notified and given access to the enrolled grade level.</p>
                <div id="noSectionWarning" class="alert alert-warning mt-3 mb-0" style="display: none;">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Reminder:</strong> No section has been assigned to this student. You can assign a section later from the student management page.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="document.getElementById('approveForm').submit();">
                    <i class="bi bi-check-circle me-1"></i>Yes, Approve
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sectionSelect = document.getElementById('section_id');
    const approveModal = document.getElementById('approveModal');
    
    if (approveModal) {
        approveModal.addEventListener('show.bs.modal', function () {
            const warning = document.getElementById('noSectionWarning');
            if (sectionSelect && sectionSelect.value === '') {
                warning.style.display = 'block';
            } else {
                warning.style.display = 'none';
            }
        });
    }
});
</script>

<!-- Reject Confirmation Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectModalLabel"><i class="bi bi-x-circle me-2"></i>Confirm Rejection</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to <strong>reject</strong> this enrollment?</p>
                <p class="text-danger small mb-0"><i class="bi bi-exclamation-triangle me-1"></i>This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="document.getElementById('rejectForm').submit();">
                    <i class="bi bi-x-circle me-1"></i>Yes, Reject
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

