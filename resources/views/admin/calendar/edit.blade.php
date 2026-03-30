@extends('layouts.app')

@section('title', 'Edit Event - Admin Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Admin Dashboard CSS -->
@vite(['resources/css/layouts/dashboard-roles/dashboard-admin.css'])

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
                                <a href="{{ route('calendar.index') }}" class="btn mobile-nav-btn w-100 active">
                                    <i class="fas fa-calendar-alt"></i>
                                    <small>Calendar</small>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="#" class="btn mobile-nav-btn w-100">
                                    <i class="fas fa-bullhorn d-block mb-1"></i>
                                    <small>Announce</small>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="#" class="btn mobile-nav-btn w-100">
                                    <i class="fas fa-users-cog d-block mb-1"></i>
                                    <small>Accounts</small>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="#" class="btn mobile-nav-btn w-100">
                                    <i class="fas fa-chart-line d-block mb-1"></i>
                                    <small>Reports</small>
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

                <!-- Page Header -->
                <div class="welcome-card mb-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                        <div>
                            <h4 class="mb-2">
                                <i class="fas fa-calendar-edit me-2"></i>
                                Edit Event
                            </h4>
                            <p class="mb-0 opacity-90">
                                Update event details for {{ $event->title }}
                            </p>
                        </div>
                        <div class="ms-auto">
                            <a href="{{ route('calendar.index') }}" class="btn btn-warning">
                                <i class="fas fa-user-edit me-2"></i>Back to Calendar
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Mobile Back Button -->
                <div class="d-lg-none mb-3">
                    <a href="{{ route('calendar.index') }}" class="btn btn-outline-success w-100">
                        <i class="fas fa-arrow-left me-2"></i>Back to Calendar
                    </a>
                </div>

                <!-- Success/Error Messages -->


                <div class="row">
                    <!-- Form Section -->
                    <div class="col-lg-8 mb-4">
                        <div class="activity-card">
                            <div class="card-header">
                                <i class="fas fa-edit me-2"></i>Event Details
                            </div>
                            <div class="card-body">
                            <form method="POST" action="{{ route('calendar.update', $event) }}">
                                @csrf
                                @method('PUT')

                                <!-- Event Title -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">Event Title <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title', $event->title) }}" 
                                           required 
                                           maxlength="255"
                                           placeholder="Enter event title">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Event Type -->
                                <div class="mb-3">
                                    <label for="event_type" class="form-label">Event Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('event_type') is-invalid @enderror" 
                                            id="event_type" 
                                            name="event_type" 
                                            required>
                                        <option value="">Select event type</option>
                                        <option value="meeting" {{ old('event_type', $event->event_type) == 'meeting' ? 'selected' : '' }}>
                                            <i class="fas fa-users"></i> Meeting
                                        </option>
                                        <option value="deadline" {{ old('event_type', $event->event_type) == 'deadline' ? 'selected' : '' }}>
                                            <i class="fas fa-clock"></i> Deadline
                                        </option>
                                        <option value="exam" {{ old('event_type', $event->event_type) == 'exam' ? 'selected' : '' }}>
                                            <i class="fas fa-file-alt"></i> Exam
                                        </option>
                                        <option value="holiday" {{ old('event_type', $event->event_type) == 'holiday' ? 'selected' : '' }}>
                                            <i class="fas fa-umbrella-beach"></i> Holiday
                                        </option>
                                        <option value="sports" {{ old('event_type', $event->event_type) == 'sports' ? 'selected' : '' }}>
                                            <i class="fas fa-trophy"></i> Sports
                                        </option>
                                    </select>
                                    @error('event_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Start Date -->
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                    <input type="datetime-local" 
                                           class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" 
                                           name="start_date" 
                                           value="{{ old('start_date', \Carbon\Carbon::parse($event->start_date)->format('Y-m-d\TH:i')) }}" 
                                           required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- End Date -->
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="datetime-local" 
                                           class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" 
                                           name="end_date" 
                                           value="{{ old('end_date', $event->end_date ? \Carbon\Carbon::parse($event->end_date)->format('Y-m-d\TH:i') : '') }}">
                                    <small class="form-text text-muted">Leave empty if same as start date</small>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- All Day Event -->
                                <div class="mb-3 form-check">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           id="is_all_day" 
                                           name="is_all_day" 
                                           value="1"
                                           {{ old('is_all_day', $event->is_all_day) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_all_day">
                                        All-day event
                                    </label>
                                </div>

                                <!-- Description -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="4" 
                                              placeholder="Enter event description (optional)">{{ old('description', $event->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Submit Buttons -->
                                <div class="d-flex justify-content-between flex-wrap gap-2">
                                    <div>
                                        <a href="{{ route('calendar.index') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-1"></i>Cancel
                                        </a>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                            <i class="fas fa-trash me-1"></i>Delete
                                        </button>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save me-1"></i>Update Event
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Info -->
                <div class="col-lg-4">
                    <!-- Current Event Info -->
                    <div class="activity-card mb-4">
                        <div class="card-header" style="background: <?php echo e($event->color); ?>; color: white;">
                            <i class="{{ $event->type_icon }} me-2"></i>Current Event
                        </div>
                        <div class="card-body">
                            <h6 class="text-success fw-bold mb-3">{{ $event->title }}</h6>
                            
                            <div class="event-info-item mb-2">
                                <small class="text-muted d-block">Event Type</small>
                                <strong>{{ ucfirst($event->type_name ?? $event->event_type) }}</strong>
                            </div>
                            
                            <div class="event-info-item mb-2">
                                <small class="text-muted d-block">Start Date</small>
                                <strong>{{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y g:i A') }}</strong>
                            </div>
                            
                            @if($event->end_date)
                                <div class="event-info-item mb-2">
                                    <small class="text-muted d-block">End Date</small>
                                    <strong>{{ \Carbon\Carbon::parse($event->end_date)->format('M d, Y g:i A') }}</strong>
                                </div>
                            @endif
                            
                            <hr class="my-3">
                            
                            <div class="event-info-item mb-2">
                                <small class="text-muted d-block">Created On</small>
                                <strong>{{ $event->created_at->format('M d, Y g:i A') }}</strong>
                            </div>
                            
                            <div class="event-info-item">
                                <small class="text-muted d-block">Created By</small>
                                <strong>{{ $event->creator->first_name ?? 'Admin' }} {{ $event->creator->last_name ?? '' }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Guidelines Card -->
                    <div class="activity-card">
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-info-circle me-2"></i>Edit Guidelines
                        </div>
                        <div class="card-body">
                            <ul class="guidelines-list mb-0">
                                <li><i class="fas fa-check text-success me-2"></i>Changes are saved immediately</li>
                                <li><i class="fas fa-check text-success me-2"></i>End date must be after start date</li>
                                <li><i class="fas fa-check text-success me-2"></i>All users will see the updated event</li>
                                <li><i class="fas fa-check text-success me-2"></i>Use the delete button to remove this event</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Event Type Legend -->
                    <div class="activity-card mt-4">
                        <div class="card-header">
                            <i class="fas fa-palette me-2"></i>Event Type Colors
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-users me-2" style="color: #0d6efd; font-size: 1.2rem;"></i>
                                <small>Meeting</small>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-clock me-2" style="color: #dc3545; font-size: 1.2rem;"></i>
                                <small>Deadline</small>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-file-alt me-2" style="color: #ffc107; font-size: 1.2rem;"></i>
                                <small>Exam</small>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-umbrella-beach me-2" style="color: #198754; font-size: 1.2rem;"></i>
                                <small>Holiday</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-trophy me-2" style="color: #fd7e14; font-size: 1.2rem;"></i>
                                <small>Sports</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); border: none;">
                <h5 class="modal-title text-white fw-bold" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <i class="fas fa-trash-alt text-danger" style="font-size: 3rem; opacity: 0.5;"></i>
                </div>
                <p class="text-center mb-3">Are you sure you want to delete this event?</p>
                <div class="alert alert-warning d-flex align-items-start mb-0">
                    <i class="fas fa-exclamation-circle me-2 mt-1"></i>
                    <div>
                        <strong>Warning:</strong> This action cannot be undone. The event "<strong>{{ $event->title }}</strong>" will be permanently deleted.
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <form method="POST" action="{{ route('calendar.destroy', $event) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Delete Event
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- All-Day Checkbox Toggle Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const allDayCheckbox = document.getElementById('is_all_day');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    function toggleTimeInputs() {
        if (allDayCheckbox.checked) {
            // Convert to date-only inputs
            startDateInput.type = 'date';
            endDateInput.type = 'date';
            
            // Convert current values to date format
            if (startDateInput.value) {
                startDateInput.value = startDateInput.value.split('T')[0];
            }
            if (endDateInput.value) {
                endDateInput.value = endDateInput.value.split('T')[0];
            }
        } else {
            // Convert to datetime inputs
            startDateInput.type = 'datetime-local';
            endDateInput.type = 'datetime-local';
        }
    }
    
    // Run on page load
    toggleTimeInputs();
    
    // Run when checkbox changes
    allDayCheckbox.addEventListener('change', toggleTimeInputs);
});
</script>

@endsection

