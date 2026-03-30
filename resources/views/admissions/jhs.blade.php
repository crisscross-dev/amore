@extends('layouts.admission')

@section('title', 'JHS Admission Form - Amore Academy')

@push('scripts')
@vite(['resources/js/admissions/jhs.js'])
@endpush

@section('content')
<style>
    .radio-group .form-check-input:checked {
        background-color: #198754;
        /* Bootstrap success green */
        border-color: #198754;
    }

    .radio-group .form-check-input:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
    }
</style>
<div class="enrollment-wrapper">
    <div class="container-fluid px-4">
        <div class="row">
            <main class="col-12">
                <div class="enrollment-card">
                    <div class="enrollment-header">
                        <h2 class="enrollment-title" style="color: white;">
                            <i class="fas fa-graduation-cap me-2"></i>
                            Junior High School Admission Form
                        </h2>
                        <p class="enrollment-subtitle">Complete all fields marked with <span class="text-danger">*</span></p>
                    </div>

                    <form id="enrollmentForm" method="POST" action="{{ route('admissions.jhs.store') }}" class="enrollment-form">
                        @csrf

                        <!-- Personal Information Section -->
                        <div class="form-section">
                            <h4 class="section-title">
                                <i class="fas fa-user me-2"></i>Personal Information
                            </h4>

                            <!-- LRN Field -->
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">LRN (Learner Reference Number - 12 digits) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="lrn" name="lrn" value="{{ old('lrn') }}" maxlength="12" placeholder="000000000000" required>
                                    @error('lrn')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Name Fields -->
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="Enter your family name" oninput="this.value = this.value.replace(/[^a-zA-Z\s.'\-]/g, '')" required>
                                    @error('last_name')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name') }}" placeholder="Enter your given name" oninput="this.value = this.value.replace(/[^a-zA-Z\s.'\-]/g, '')" required>
                                    @error('first_name')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Middle Name</label>
                                    <input type="text" class="form-control" id="middle_name" name="middle_name" value="{{ old('middle_name') }}" placeholder="Enter your middle name (optional)" oninput="this.value = this.value.replace(/[^a-zA-Z\s.'\-]/g, '')">
                                    <small class="text-muted">Optional</small>
                                    @error('middle_name')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Suffix</label>
                                    <input type="text" class="form-control" id="suffix" name="suffix" value="{{ old('suffix') }}" placeholder="e.g., Jr., Sr., III" oninput="this.value = this.value.replace(/[^a-zA-Z0-9.\s]/g, '')">
                                    <small class="text-muted">Optional</small>
                                    @error('suffix')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="date_of_birth" name="dob" value="{{ old('dob') }}" required readonly inputmode="none" autocomplete="off">
                                    <div id="age-error" class="text-danger small mt-1" style="display: none;"></div>
                                    @error('dob')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Age <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="age" name="age" value="{{ old('age') }}" min="1" max="100" readonly>
                                    <small class="text-muted">Auto-calculated from date of birth</small>
                                    @error('age')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Sex <span class="text-danger">*</span></label>
                                    <select class="form-select" name="gender" required>
                                        <option value="">-- Select --</option>
                                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                    @error('gender')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Citizenship <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="citizenship" value="{{ old('citizenship') }}" placeholder="e.g., Filipino" oninput="this.value = this.value.replace(/[^a-zA-Z\s.'\-]/g, '')" required>
                                    @error('citizenship')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Religion <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="religion" value="{{ old('religion') }}" placeholder="e.g., Catholic, Protestant, Muslim" oninput="this.value = this.value.replace(/[^a-zA-Z\s.'\-]/g, '')" required>
                                    @error('religion')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Street <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="address_street" value="{{ old('address_street') }}" placeholder="e.g., 123 Mabini St." required>
                                    @error('address_street')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Barangay <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="address_barangay" value="{{ old('address_barangay') }}" placeholder="e.g., Barangay San Isidro" required>
                                    @error('address_barangay')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">City / Municipality <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="address_city" value="{{ old('address_city') }}" placeholder="e.g., Quezon City" required>
                                    @error('address_city')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Province <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="address_province" value="{{ old('address_province') }}" placeholder="e.g., Metro Manila" required>
                                    @error('address_province')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" name="phone" value="{{ old('phone') }}" placeholder="09XXXXXXXXX" maxlength="11" pattern="[0-9]{11}" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                                    @error('phone')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <input type="hidden" id="address" name="address" value="{{ old('address') }}">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="your.email@example.com" required>
                                    @error('email')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Grade Level Applying For <span class="text-danger">*</span></label>
                                    <select class="form-select" name="grade_level" required>
                                        <option value="">-- Select Grade Level --</option>
                                        <option value="Grade 7" {{ old('grade_level') == 'Grade 7' ? 'selected' : '' }}>Grade 7</option>
                                        <option value="Grade 8" {{ old('grade_level') == 'Grade 8' ? 'selected' : '' }}>Grade 8</option>
                                        <option value="Grade 9" {{ old('grade_level') == 'Grade 9' ? 'selected' : '' }}>Grade 9</option>
                                        <option value="Grade 10" {{ old('grade_level') == 'Grade 10' ? 'selected' : '' }}>Grade 10</option>
                                    </select>
                                    @error('grade_level')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3">

                            </div>
                        </div>

                        <hr class="section-divider">

                        <!-- Academic Information Section -->
                        <div class="form-section">
                            <h4 class="section-title">
                                <i class="fas fa-school me-2"></i>Academic Information
                            </h4>

                            <label class="form-label fw-bold">Type of Previous School <span class="text-danger">*</span></label>
                            <div class="radio-group mb-3">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="school_type" id="school_type_public" value="Public" {{ old('school_type') == 'Public' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="school_type_public">Public</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="school_type" id="school_type_private" value="Private" {{ old('school_type') == 'Private' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="school_type_private">Private</label>
                                </div>
                            </div>
                            @error('school_type')
                            <div class="text-danger small mb-3">{{ $message }}</div>
                            @enderror

                            <!-- Public School -->
                            <div id="publicSchool" class="hidden">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Previous School Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="public_school_name" name="school_name" value="{{ old('school_name') }}" placeholder="Enter school name">
                                        @error('school_name')
                                        <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Private School Options -->
                            <div id="privateOptions" class="hidden">
                                <label class="form-label fw-bold">Private School Category <span class="text-danger">*</span></label>
                                <div class="radio-group mb-3">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="private_type" id="private_type_esc" value="ESC" {{ old('private_type') == 'ESC' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="private_type_esc">ESC</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="private_type" id="private_type_non_esc" value="Non-ESC" {{ old('private_type') == 'Non-ESC' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="private_type_non_esc">Non-ESC</label>
                                    </div>
                                </div>
                                <small class="text-muted d-block mb-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <strong>ESC</strong> (Education Service Contracting) is a government program that provides subsidies to students from public schools to attend participating private schools.
                                </small>
                                @error('private_type')
                                <div class="text-danger small mb-3">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- ESC Fields -->
                            <div id="escFields" class="hidden">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Student ESC No. <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="esc_no" name="student_esc_no" value="{{ old('student_esc_no') }}" maxlength="8" placeholder="ESC Number">
                                        @error('student_esc_no')
                                        <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">ESC School ID <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="esc_school_id" name="esc_school_id" value="{{ old('esc_school_id') }}" maxlength="6" placeholder="School ID">
                                        @error('esc_school_id')
                                        <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">School Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="esc_school_name" name="school_name" value="{{ old('school_name') }}" placeholder="Enter school name">
                                        @error('school_name')
                                        <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Non-ESC Field -->
                            <div id="nonEscField" class="hidden">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">School Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="non_esc_school_name" name="school_name" value="{{ old('school_name') }}" placeholder="Enter school name">
                                        @error('school_name')
                                        <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="section-divider">

                        <!-- Family Information Section -->
                        <div class="form-section">
                            <h4 class="section-title">
                                <i class="fas fa-users me-2"></i>Guardian Information
                            </h4>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Mother's Maiden Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="mother_name" value="{{ old('mother_name') }}" placeholder="Enter mother's maiden name" oninput="this.value = this.value.replace(/[^a-zA-Z\s.'\-]/g, '')" required>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="useMotherAsEmergency">
                                        <label class="form-check-label small text-muted" for="useMotherAsEmergency">use this as emergency contact</label>
                                    </div>
                                    @error('mother_name')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Occupation <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="mother_occupation" value="{{ old('mother_occupation') }}" placeholder="e.g., Teacher, Nurse, Business Owner" oninput="this.value = this.value.replace(/[^a-zA-Z\s.'\-]/g, '')" required>
                                    @error('mother_occupation')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Father's Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="father_name" value="{{ old('father_name') }}" placeholder="Enter father's name" oninput="this.value = this.value.replace(/[^a-zA-Z\s.'\-]/g, '')" required>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="useFatherAsEmergency">
                                        <label class="form-check-label small text-muted" for="useFatherAsEmergency">use this as emergency contact</label>
                                    </div>
                                    @error('father_name')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Occupation <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="father_occupation" value="{{ old('father_occupation') }}" placeholder="e.g., Engineer, Driver, Self-employed" oninput="this.value = this.value.replace(/[^a-zA-Z\s.'\-]/g, '')" required>
                                    @error('father_occupation')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <hr class="section-divider">

                        <!-- Emergency Contact Section -->
                        <div class="form-section">
                            <h4 class="section-title">
                                <i class="fas fa-phone-alt me-2"></i>Emergency Contact
                            </h4>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Emergency Contact Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" placeholder="Enter emergency contact name" oninput="this.value = this.value.replace(/[^a-zA-Z\s.'\-]/g, '')" required>
                                    @error('emergency_contact_name')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Relationship <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship') }}" placeholder="e.g., Mother, Father, Guardian" oninput="this.value = this.value.replace(/[^a-zA-Z\s.'\-]/g, '')" required>
                                    @error('emergency_contact_relationship')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" placeholder="09XXXXXXXXX" maxlength="11" pattern="[0-9]{11}" oninput="this.value = this.value.replace(/[^0-9]/g, '')" title="Enter an 11-digit Philippine mobile number starting with 09" required>
                                    @error('emergency_contact_phone')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <hr class="section-divider">



                        <!-- Submit Button -->
                        <div class="form-actions mt-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <a href="{{ route('admissions.selection') }}" class="btn btn-outline-secondary w-100">
                                        <i class="fas fa-arrow-left me-2"></i>Back to Selection
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-paper-plane me-2"></i>Submit Admission
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
        </div>
        </main>
    </div>
</div>
</div>
</div>
@endsection