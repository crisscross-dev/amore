@extends('layouts.app')

@section('title', 'Calendar - Admin Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Admin Dashboard CSS -->
@vite(['resources/css/layouts/dashboard-roles/dashboard-admin.css', 'resources/js/admin-calendar.js'])

<div id="admin-calendar-page" class="dashboard-container">
    <div class="container-fluid px-4">
        <div class="row">
            <!-- Admin Sidebar Partial -->

            <!-- Main Calendar Content -->
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

                <!-- Success/Error Messages -->


                <!-- Page Header -->
                <div class="welcome-card mb-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-2">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Add Activity
                            </h4>
                            <p class="mb-0 opacity-90">
                                Add dates, events, and schedules for Amore Academy
                            </p>
                        </div>
                        <div class="ms-auto">
                            <a href="{{ route('calendar.index') }}" class="btn btn-warning">
                                <i class="fas fa-user-edit me-2"></i>Back to Calendar
                            </a>
                        </div>
                        
                    </div>
                </div>

                        <!-- Add Event Card -->
                        <div class="activity-card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="fas fa-calendar-plus me-2"></i>
                                    Activity Details
                                </span>
                                <span class="badge bg-white text-success">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Required Fields *
                                </span>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('calendar.store') }}">
                                    @csrf

                                    <!-- Event Title -->
                                    <div class="mb-3">
                                        <label for="title" class="form-label fw-bold">
                                            Activity Title <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('title') is-invalid @enderror" 
                                               id="title" 
                                               name="title" 
                                               value="{{ old('title') }}" 
                                               required 
                                               maxlength="255"
                                               placeholder="e.g., Monthly Faculty Meeting">
                                        @error('title')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle me-1"></i>
                                                {{ $message }}
                                            </div>
                                        @enderror
                                        <div class="form-text">
                                            <i class="fas fa-lightbulb me-1"></i>
                                            Enter a clear and descriptive title for the Activity
                                        </div>
                                    </div>

                                    <!-- Start Date & Time -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="start_date" class="form-label fw-bold">
                                                Start Date <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" 
                                                   class="form-control @error('start_date') is-invalid @enderror" 
                                                   id="start_date" 
                                                   name="start_date" 
                                                   value="{{ old('start_date') }}" 
                                                   required>
                                            @error('start_date')
                                                <div class="invalid-feedback">
                                                    <i class="fas fa-exclamation-circle me-1"></i>
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="start_time" class="form-label fw-bold">
                                                Start Time <span id="time-required" class="text-danger">*</span>
                                            </label>
                                            <input type="time" 
                                                   class="form-control @error('start_time') is-invalid @enderror" 
                                                   id="start_time" 
                                                   name="start_time" 
                                                   value="{{ old('start_time') }}">
                                            @error('start_time')
                                                <div class="invalid-feedback">
                                                    <i class="fas fa-exclamation-circle me-1"></i>
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- End Date & Time -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="end_date" class="form-label fw-bold">
                                                End Date
                                            </label>
                                            <input type="date" 
                                                   class="form-control @error('end_date') is-invalid @enderror" 
                                                   id="end_date" 
                                                   name="end_date" 
                                                   value="{{ old('end_date') }}">
                                            @error('end_date')
                                                <div class="invalid-feedback">
                                                    <i class="fas fa-exclamation-circle me-1"></i>
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                            <div class="form-text">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Leave blank if same as start date
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="end_time" class="form-label fw-bold">
                                                End Time
                                            </label>
                                            <input type="time" 
                                                   class="form-control @error('end_time') is-invalid @enderror" 
                                                   id="end_time" 
                                                   name="end_time" 
                                                   value="{{ old('end_time') }}">
                                            @error('end_time')
                                                <div class="invalid-feedback">
                                                    <i class="fas fa-exclamation-circle me-1"></i>
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- All Day Event Checkbox -->
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="is_all_day" 
                                                   name="is_all_day" 
                                                   value="1"
                                                   {{ old('is_all_day') ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold text-success" for="is_all_day">
                                                <i class="fas fa-sun me-1"></i>
                                                This is an all-day event
                                            </label>
                                        </div>
                                        <div class="form-text ms-4">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Check this if the event lasts the entire day (no specific time)
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div class="mb-4">
                                        <label for="description" class="form-label fw-bold">
                                            Activity Description
                                        </label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                                  id="description" 
                                                  name="description" 
                                                  rows="4" 
                                                  placeholder="Provide additional details about the event (optional)">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle me-1"></i>
                                                {{ $message }}
                                            </div>
                                        @enderror
                                        <div class="form-text">
                                            <i class="fas fa-pen me-1"></i>
                                            Add any important details, agenda, or notes about the event
                                        </div>
                                    </div>

                                    <!-- Form Actions -->
                                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                        <a href="{{ route('calendar.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times me-2"></i>Cancel
                                        </a>
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fas fa-calendar-plus me-2"></i>Create Activity
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        
                
            </main>
        </div>
    </div>
</div>

<!-- All Day Event Toggle Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const allDayCheckbox = document.getElementById('is_all_day');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const timeRequiredLabel = document.getElementById('time-required');
    
    // Function to toggle time inputs
    function toggleTimeInputs() {
        if (allDayCheckbox.checked) {
            startTimeInput.disabled = true;
            endTimeInput.disabled = true;
            startTimeInput.value = '';
            endTimeInput.value = '';
            startTimeInput.classList.remove('is-invalid');
            endTimeInput.classList.remove('is-invalid');
            if (timeRequiredLabel) {
                timeRequiredLabel.style.display = 'none';
            }
        } else {
            startTimeInput.disabled = false;
            endTimeInput.disabled = false;
            if (timeRequiredLabel) {
                timeRequiredLabel.style.display = 'inline';
            }
        }
    }
    
    // Initial state
    toggleTimeInputs();
    
    // Listen for checkbox changes
    allDayCheckbox.addEventListener('change', toggleTimeInputs);
    
    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const startTime = document.getElementById('start_time').value;
        const endTime = document.getElementById('end_time').value;
        const isAllDay = document.getElementById('is_all_day').checked;
        
        // Skip time validation if all-day event
        if (isAllDay) {
            // Only check date validation for all-day events
            if (endDate && startDate && new Date(endDate) < new Date(startDate)) {
                e.preventDefault();
                alert('End date cannot be before start date!');
                return false;
            }
            return true;
        }
        
        // Check if end date is before start date
        if (endDate && startDate && new Date(endDate) < new Date(startDate)) {
            e.preventDefault();
            alert('End date cannot be before start date!');
            return false;
        }
        
        // Check if end time is before or same as start time when dates are the same or end date is empty
        const effectiveEndDate = endDate || startDate;
        if (startDate && effectiveEndDate && startTime && endTime) {
            const startDateTime = new Date(startDate + 'T' + startTime);
            const endDateTime = new Date(effectiveEndDate + 'T' + endTime);
            
            if (endDateTime < startDateTime) {
                e.preventDefault();
                if (startDate === effectiveEndDate) {
                    alert('End time cannot be earlier than start time on the same day!');
                } else {
                    alert('End date/time cannot be earlier than start date/time!');
                }
                return false;
            }
            
            // Check if same date and same time
            if (startDate === effectiveEndDate && startTime === endTime) {
                e.preventDefault();
                alert('End time cannot be the same as start time on the same day!');
                return false;
            }
        }
    });
    
    // Real-time validation feedback
    const endDateInput = document.getElementById('end_date');
    const endTimeInput_validate = document.getElementById('end_time');
    
    function validateDateTime() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const startTime = document.getElementById('start_time').value;
        const endTime = document.getElementById('end_time').value;
        const isAllDay = document.getElementById('is_all_day').checked;
        
        // Reset validation styles
        endDateInput.classList.remove('is-invalid');
        endTimeInput_validate.classList.remove('is-invalid');
        
        if (isAllDay) return;
        
        // Validate end date
        if (endDate && startDate && new Date(endDate) < new Date(startDate)) {
            endDateInput.classList.add('is-invalid');
        }
        
        // Validate end time
        const effectiveEndDate = endDate || startDate;
        if (startDate && effectiveEndDate && startTime && endTime) {
            const startDateTime = new Date(startDate + 'T' + startTime);
            const endDateTime = new Date(effectiveEndDate + 'T' + endTime);
            
            if (endDateTime < startDateTime) {
                endTimeInput_validate.classList.add('is-invalid');
            }
            
            // Check if same date and same time
            if (startDate === effectiveEndDate && startTime === endTime) {
                endTimeInput_validate.classList.add('is-invalid');
            }
        }
    }
    
    // Attach validation listeners
    document.getElementById('start_date').addEventListener('change', validateDateTime);
    document.getElementById('end_date').addEventListener('change', validateDateTime);
    document.getElementById('start_time').addEventListener('change', validateDateTime);
    document.getElementById('end_time').addEventListener('change', validateDateTime);
});
</script>

<!-- Event Type Indicator Styles -->
<style>
.event-type-indicator {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    flex-shrink: 0;
}

.form-control:focus,
.form-select:focus {
    border-color: var(--color-primary);
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
}

.form-check-input:checked {
    background-color: var(--color-primary);
    border-color: var(--color-primary);
}

.btn-success {
    background: linear-gradient(135deg, var(--color-primary) 0%, #20c997 100%);
    border: none;
    transition: all 0.3s ease;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(25, 135, 84, 0.3);
}
</style>

@endsection

