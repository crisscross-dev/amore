@extends('layouts.app')

@section('title', 'Edit Announcement - Admin Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- TinyMCE CDN -->
<script src="/vendor/tinymce/tinymce.min.js" referrerpolicy="origin"></script>

<!-- Admin Dashboard CSS -->
@vite(['resources/css/layouts/dashboard-roles/dashboard-admin.css', 'resources/js/announcements.js'])

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
                                <i class="fas fa-edit me-2"></i>
                                Edit Announcement
                            </h4>
                            <p class="mb-0 opacity-90">
                                Update announcement: {{ $announcement->title }}
                            </p>
                        </div>
                        <div class="d-none d-lg-block">
                            <a href="{{ route('announcements.index') }}" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>Back to List
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Mobile Back Button -->
                <div class="d-lg-none mb-3">
                    <a href="{{ route('announcements.index') }}" class="btn btn-outline-success w-100">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>

                <div class="row">
                    <!-- Form Section -->
                    <div class="col-lg-8 mb-4">
                        <div class="activity-card">
                            <div class="card-header">
                                <i class="fas fa-edit me-2"></i>Announcement Details
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('announcements.update', $announcement) }}" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <!-- Title -->
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('title') is-invalid @enderror"
                                            id="title"
                                            name="title"
                                            value="{{ old('title', $announcement->title) }}"
                                            required
                                            maxlength="255"
                                            placeholder="Enter announcement title">
                                        @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Content -->
                                    <div class="mb-3">
                                        <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('content') is-invalid @enderror"
                                            id="content"
                                            name="content"
                                            rows="8"
                                            required
                                            placeholder="Enter announcement content...">{{ old('content', $announcement->content) }}</textarea>
                                        @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">You can use formatting in the content.</small>
                                    </div>

                                    <!-- Target Audience -->
                                    <div class="mb-3">
                                        <label class="form-label">Visibility <span class="text-danger">*</span></label>
                                        <div class="row">
                                            <div class="col-md-6 col-sm-12 mb-2">
                                                <div class="form-check card p-3 h-100">
                                                    <input class="form-check-input"
                                                        type="radio"
                                                        name="target_audience"
                                                        id="target_public"
                                                        value="public"
                                                        {{ old('target_audience', $announcement->target_audience ?? 'all') == 'public' ? 'checked' : '' }}>
                                                    <label class="form-check-label w-100" for="target_public">
                                                        <strong><i class="fas fa-globe text-primary"></i> Public</strong>
                                                        <small class="d-block text-muted">Visible to everyone (including guests)</small>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12 mb-2">
                                                <div class="form-check card p-3 h-100">
                                                    <input class="form-check-input"
                                                        type="radio"
                                                        name="target_audience"
                                                        id="target_all"
                                                        value="all"
                                                        {{ old('target_audience', $announcement->target_audience ?? 'all') == 'all' ? 'checked' : '' }}>
                                                    <label class="form-check-label w-100" for="target_all">
                                                        <strong><i class="fas fa-users text-success"></i> All Users</strong>
                                                        <small class="d-block text-muted">Visible to all logged-in users</small>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12 mb-2">
                                                <div class="form-check card p-3 h-100">
                                                    <input class="form-check-input"
                                                        type="radio"
                                                        name="target_audience"
                                                        id="target_students"
                                                        value="students"
                                                        {{ old('target_audience', $announcement->target_audience) == 'students' ? 'checked' : '' }}>
                                                    <label class="form-check-label w-100" for="target_students">
                                                        <strong><i class="fas fa-graduation-cap text-info"></i> Students Only</strong>
                                                        <small class="d-block text-muted">Visible to students only</small>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12 mb-2">
                                                <div class="form-check card p-3 h-100">
                                                    <input class="form-check-input"
                                                        type="radio"
                                                        name="target_audience"
                                                        id="target_faculty"
                                                        value="faculty"
                                                        {{ old('target_audience', $announcement->target_audience) == 'faculty' ? 'checked' : '' }}>
                                                    <label class="form-check-label w-100" for="target_faculty">
                                                        <strong><i class="fas fa-chalkboard-teacher text-warning"></i> Faculty Only</strong>
                                                        <small class="d-block text-muted">Visible to faculty only</small>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        @error('target_audience')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Expiration Date -->
                                    <div class="mb-3">
                                        <label for="expires_at" class="form-label">Expiration Date (Optional)</label>
                                        <input type="datetime-local"
                                            class="form-control @error('expires_at') is-invalid @enderror"
                                            id="expires_at"
                                            name="expires_at"
                                            value="{{ old('expires_at', $announcement->expires_at ? $announcement->expires_at->format('Y-m-d\TH:i') : '') }}"
                                            min="{{ now()->format('Y-m-d\TH:i') }}">
                                        @error('expires_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Leave empty for permanent announcement</small>
                                    </div>

                                    <!-- Pin Announcement -->
                                    <div class="mb-3 form-check">
                                        <input type="checkbox"
                                            class="form-check-input"
                                            id="is_pinned"
                                            name="is_pinned"
                                            value="1"
                                            {{ old('is_pinned', $announcement->is_pinned) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_pinned" style="color: #333;">
                                            <i class="fas fa-thumbtack me-1"></i> Pin this announcement (appears at top)
                                        </label>
                                    </div>

                                    <!-- Existing Attachments -->
                                    @if($announcement->attachments && count($announcement->attachments) > 0)
                                    <div class="mb-3">
                                        <label class="form-label">Current Attachments</label>
                                        <div class="list-group">
                                            @foreach($announcement->attachments as $attachment)
                                            <div class="list-group-item">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-file-{{ $attachment['type'] == 'pdf' ? 'pdf' : 'alt' }} text-success fa-2x me-3"></i>
                                                    <div class="flex-grow-1">
                                                        <strong>{{ $attachment['name'] }}</strong>
                                                        <p class="small text-muted mb-0">
                                                            {{ number_format($attachment['size'] / 1024, 2) }} KB
                                                        </p>
                                                    </div>
                                                    <a href="{{ asset('storage/' . $attachment['path']) }}"
                                                        class="btn btn-sm btn-outline-success"
                                                        target="_blank">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                    <!-- File Attachments -->
                                    <div class="mb-3">
                                        <label for="attachments" class="form-label">Add New Attachments (Optional)</label>
                                        <input type="file"
                                            class="form-control @error('attachments.*') is-invalid @enderror"
                                            id="attachments"
                                            name="attachments[]"
                                            multiple
                                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                        @error('attachments.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Accepted: PDF, DOC, DOCX, JPG, PNG (Max: 10MB per file)
                                        </small>
                                    </div>

                                    <hr class="my-4">

                                    <!-- Submit Buttons -->
                                    <div class="d-flex justify-content-between flex-wrap gap-2">
                                        <a href="{{ route('announcements.index') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-1"></i>Cancel
                                        </a>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                                <i class="fas fa-trash me-1"></i>Delete
                                            </button>
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-save me-1"></i>Update Announcement
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Info -->
                    <div class="col-lg-4">
                        <!-- Current Info -->
                        <div class="activity-card mb-4">
                            <div class="card-header announcement-priority-header text-white" data-priority-color="{{ $announcement->priority_color }}">
                                <i class="fas fa-info-circle me-2"></i>Current Info
                            </div>
                            <div class="card-body">
                                <div class="event-info-item mb-3">
                                    <small class="text-muted d-block">Priority</small>
                                    <strong>{{ ucfirst($announcement->priority) }}</strong>
                                </div>

                                <div class="event-info-item mb-3">
                                    <small class="text-muted d-block">Audience</small>
                                    <strong>{{ $announcement->audience_label }}</strong>
                                </div>

                                <div class="event-info-item mb-3">
                                    <small class="text-muted d-block">Created</small>
                                    <strong>{{ $announcement->created_at->format('M d, Y g:i A') }}</strong>
                                </div>

                                <div class="event-info-item">
                                    <small class="text-muted d-block">Created By</small>
                                    <strong>{{ $announcement->createdBy->first_name ?? 'Admin' }} {{ $announcement->createdBy->last_name ?? '' }}</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Guidelines Card -->
                        <div class="activity-card">
                            <div class="card-header bg-success text-white">
                                <i class="fas fa-lightbulb me-2"></i>Edit Tips
                            </div>
                            <div class="card-body">
                                <ul class="guidelines-list mb-0" style="color: #333;">
                                    <li><i class="fas fa-check text-success me-2"></i>Changes are saved immediately</li>
                                    <li><i class="fas fa-check text-success me-2"></i>New files will be added to existing attachments</li>
                                    <li><i class="fas fa-check text-success me-2"></i>All users will see updated announcement</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Use delete button to remove permanently</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const priorityHeader = document.querySelector('.announcement-priority-header');
        if (!priorityHeader) {
            return;
        }

        const color = priorityHeader.dataset.priorityColor;
        if (color) {
            priorityHeader.style.background = color;
        }
    });
</script>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); border: none;">
                <h5 class="modal-title text-white fw-bold">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <i class="fas fa-trash-alt text-danger" style="font-size: 3rem; opacity: 0.5;"></i>
                </div>
                <p class="text-center mb-3">Are you sure you want to delete this announcement?</p>
                <div class="alert alert-warning d-flex align-items-start mb-0">
                    <i class="fas fa-exclamation-circle me-2 mt-1"></i>
                    <div>
                        <strong>Warning:</strong> The announcement "<strong>{{ $announcement->title }}</strong>" will be permanently deleted.
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <form method="POST" action="{{ route('announcements.destroy', $announcement) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection