@extends('layouts.app')

@section('title', 'View Announcement - Admin Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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
                                <i class="fas fa-bullhorn me-2"></i>
                                Announcement Details
                            </h4>
                            <p class="mb-0 opacity-90">
                                View full announcement information
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
                    <!-- Announcement Content -->
                    <div class="col-lg-8 mb-4">
                        <div class="activity-card" style="border-left: 4px solid {{ $announcement->priority_color }};">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    @if($announcement->is_pinned)
                                        <i class="fas fa-thumbtack text-warning me-2"></i>
                                    @endif
                                    <i class="fas {{ $announcement->priority_icon }} me-2" style="color: {{ $announcement->priority_color }};"></i>
                                    <strong>{{ ucfirst($announcement->priority) }} Priority</strong>
                                </div>
                                <span class="badge bg-info">{{ $announcement->audience_label }}</span>
                            </div>
                            
                            <div class="card-body">
                                <h3 class="text-success fw-bold mb-4">{{ $announcement->title }}</h3>
                                
                                <div class="announcement-content mb-4">
                                    {!! $announcement->content !!}
                                </div>

                                @if($announcement->attachments && count($announcement->attachments) > 0)
                                    <hr class="my-4">
                                    <h6 class="text-success mb-3"><i class="fas fa-paperclip me-2"></i>Attachments</h6>
                                    
                                    {{-- Image Attachments --}}
                                    @php
                                        $imageAttachments = collect($announcement->attachments)->filter(function($att) {
                                            return in_array(strtolower($att['type']), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                        });
                                        $otherAttachments = collect($announcement->attachments)->filter(function($att) {
                                            return !in_array(strtolower($att['type']), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                        });
                                    @endphp

                                    @if($imageAttachments->count() > 0)
                                        <div class="row g-3 mb-3">
                                            @foreach($imageAttachments as $attachment)
                                                <div class="col-md-6 col-lg-4">
                                                    <div class="card h-100">
                                                        <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank">
                                                            <img src="{{ asset('storage/' . $attachment['path']) }}" 
                                                                 class="card-img-top" 
                                                                 alt="{{ $attachment['name'] }}"
                                                                 style="height: 150px; object-fit: cover;">
                                                        </a>
                                                        <div class="card-body p-2">
                                                            <p class="card-text small text-truncate mb-1" title="{{ $attachment['name'] }}">
                                                                {{ $attachment['name'] }}
                                                            </p>
                                                            <a href="{{ asset('storage/' . $attachment['path']) }}" 
                                                               class="btn btn-sm btn-outline-success w-100" 
                                                               download="{{ $attachment['name'] }}">
                                                                <i class="fas fa-download me-1"></i>Download
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Other Attachments (PDF, DOC, etc.) --}}
                                    @if($otherAttachments->count() > 0)
                                        <div class="list-group">
                                            @foreach($otherAttachments as $attachment)
                                                @php
                                                    $fileType = strtolower($attachment['type']);
                                                    $iconClass = match($fileType) {
                                                        'pdf' => 'fa-file-pdf text-danger',
                                                        'doc', 'docx' => 'fa-file-word text-primary',
                                                        default => 'fa-file-alt text-secondary'
                                                    };
                                                @endphp
                                                <a href="{{ asset('storage/' . $attachment['path']) }}" 
                                                   class="list-group-item list-group-item-action attachment-item" 
                                                   download="{{ $attachment['name'] }}"
                                                   target="_blank">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas {{ $iconClass }} fa-2x me-3"></i>
                                                        <div class="flex-grow-1">
                                                            <strong>{{ $attachment['name'] }}</strong>
                                                            <p class="small text-muted mb-0">
                                                                <span class="badge bg-secondary">{{ strtoupper($fileType) }}</span>
                                                                {{ number_format($attachment['size'] / 1024, 2) }} KB
                                                            </p>
                                                        </div>
                                                        <i class="fas fa-download text-muted"></i>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                @endif

                                <hr class="my-4">

                                <!-- Action Buttons -->
                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="{{ route('announcements.edit', $announcement) }}" class="btn btn-success">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>
                                    <form method="POST" action="{{ route('announcements.pin', $announcement) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fas fa-thumbtack me-1"></i>{{ $announcement->is_pinned ? 'Unpin' : 'Pin' }}
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                        <i class="fas fa-trash me-1"></i>Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Info -->
                    <div class="col-lg-4">
                        <!-- Announcement Info -->
                        <div class="activity-card mb-4">
                            <div class="card-header bg-success text-white">
                                <i class="fas fa-info-circle me-2"></i>Announcement Info
                            </div>
                            <div class="card-body">
                                <div class="event-info-item mb-3">
                                    <small class="text-muted d-block">Created By</small>
                                    <strong>{{ $announcement->createdBy->first_name ?? 'Admin' }} {{ $announcement->createdBy->last_name ?? '' }}</strong>
                                </div>
                                
                                <div class="event-info-item mb-3">
                                    <small class="text-muted d-block">Created On</small>
                                    <strong>{{ $announcement->created_at->format('M d, Y g:i A') }}</strong>
                                </div>
                                
                                @if($announcement->updated_by)
                                    <div class="event-info-item mb-3">
                                        <small class="text-muted d-block">Last Updated By</small>
                                        <strong>{{ $announcement->updatedBy->first_name ?? 'Admin' }} {{ $announcement->updatedBy->last_name ?? '' }}</strong>
                                    </div>
                                    
                                    <div class="event-info-item mb-3">
                                        <small class="text-muted d-block">Last Updated</small>
                                        <strong>{{ $announcement->updated_at->format('M d, Y g:i A') }}</strong>
                                    </div>
                                @endif
                                
                                @if($announcement->expires_at)
                                    <div class="event-info-item">
                                        <small class="text-muted d-block">Expires On</small>
                                        <strong class="{{ $announcement->is_expired ? 'text-danger' : 'text-success' }}">
                                            {{ $announcement->expires_at->format('M d, Y g:i A') }}
                                            @if($announcement->is_expired)
                                                <span class="badge bg-danger ms-1">Expired</span>
                                            @endif
                                        </strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Status Card -->
                        <div class="activity-card">
                            <div class="card-header">
                                <i class="fas fa-chart-bar me-2"></i>Status
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-flag fa-2x me-3" style="color: {{ $announcement->priority_color }};"></i>
                                    <div>
                                        <small class="text-muted d-block">Priority Level</small>
                                        <strong>{{ ucfirst($announcement->priority) }}</strong>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-users fa-2x text-success me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">Target Audience</small>
                                        <strong>{{ $announcement->audience_label }}</strong>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-{{ $announcement->is_pinned ? 'thumbtack' : 'circle' }} fa-2x text-warning me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">Pin Status</small>
                                        <strong>{{ $announcement->is_pinned ? 'Pinned' : 'Not Pinned' }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

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

<style>
.announcement-content {
    font-size: 1.05rem;
    line-height: 1.8;
    color: #333;
}
</style>

@endsection

