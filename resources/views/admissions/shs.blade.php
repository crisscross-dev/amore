@extends('layouts.admission')

@section('title', 'SHS Admission Form - Amore Academy')

@push('scripts')
    @vite(['resources/js/admissions/shs.js'])
@endpush

@section('content')
<style>
    .radio-group .form-check-input:checked {
        background-color: #198754; /* Bootstrap success green */
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
                                    <i class="fas fa-user-graduate me-2"></i>
                                    Senior High School Admission Form
                                </h2>
                                <p class="enrollment-subtitle">Complete all fields marked with <span class="text-danger">*</span></p>
                            </div>

                            <form id="enrollmentForm" method="POST" action="{{ route('admissions.shs.store') }}" class="enrollment-form">
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
                                            <small class="text-muted">Enter exactly 12 digits</small>
                                            @error('lrn')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Name Fields -->
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="Enter your family name" oninput="this.value = this.value.replace(/[^a-zA-Z\s.'\-]/g, '')" required>
                                            @error('last_name')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name') }}" placeholder="Enter your given name" oninput="this.value = this.value.replace(/[^a-zA-Z\s.'\-]/g, '')" required>
                                            @error('first_name')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Middle Name</label>
                                            <input type="text" class="form-control" id="middle_name" name="middle_name" value="{{ old('middle_name') }}" placeholder="Enter your middle name (optional)" oninput="this.value = this.value.replace(/[^a-zA-Z\s.'\-]/g, '')">
                                            <small class="text-muted">Optional</small>
                                            @error('middle_name')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="date_of_birth" name="dob" value="{{ old('dob') }}" required>
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

                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const dobInput = document.getElementById('date_of_birth');
                                            const ageInput = document.getElementById('age');
                                            const ageError = document.getElementById('age-error');
                                            const minAge = 15;

                                            dobInput.addEventListener('change', function() {
                                                if (this.value) {
                                                    const birthDate = new Date(this.value);
                                                    const today = new Date();
                                                    let age = today.getFullYear() - birthDate.getFullYear();
                                                    const monthDiff = today.getMonth() - birthDate.getMonth();
                                                    
                                                    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                                                        age--;
                                                    }
                                                    
                                                    ageInput.value = age;
                                                    
                                                    if (age < minAge) {
                                                        ageError.textContent = `The student must be at least ${minAge} years old to enroll in Senior High School.`;
                                                        ageError.style.display = 'block';
                                                        dobInput.setCustomValidity('Age requirement not met');
                                                    } else {
                                                        ageError.style.display = 'none';
                                                        dobInput.setCustomValidity('');
                                                    }
                                                }
                                            });
                                        });
                                    </script>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Gender <span class="text-danger">*</span></label>
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
                                            <label class="form-label">Height (cm) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="height" value="{{ old('height') }}" step="0.01" placeholder="e.g., 165.5" onkeydown="return event.key !== 'e' && event.key !== 'E' && event.key !== '+' && event.key !== '-'" required>
                                            @error('height')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Weight (kg) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="weight" value="{{ old('weight') }}" step="0.01" placeholder="e.g., 55.5" onkeydown="return event.key !== 'e' && event.key !== 'E' && event.key !== '+' && event.key !== '-'" required>
                                            @error('weight')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Address <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="address" value="{{ old('address') }}" placeholder="Enter complete address (Street, Barangay, City, Province)" required>
                                            @error('address')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                            <input type="tel" class="form-control" name="phone" value="{{ old('phone') }}" placeholder="09XXXXXXXXX" maxlength="11" pattern="[0-9]{11}" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                                            @error('phone')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="your.email@example.com" required>
                                            @error('email')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Grade Level Applying For <span class="text-danger">*</span></label>
                                            <select class="form-select" name="grade_level" required>
                                                <option value="">-- Select Grade Level --</option>
                                                <option value="Grade 11" {{ old('grade_level') == 'Grade 11' ? 'selected' : '' }}>Grade 11</option>
                                                <option value="Grade 12" {{ old('grade_level') == 'Grade 12' ? 'selected' : '' }}>Grade 12</option>
                                            </select>
                                            @error('grade_level')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
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

        <!-- QVR Applicant -->
        <div class="col-md-6">
            <label class="form-label fw-bold">QVR Applicant? <span class="text-danger">*</span></label>
            <select class="form-select"
                    id="qvr_applicant"
                    name="qvr_applicant">
                <option value="">-- Select --</option>
                <option value="Yes" {{ old('qvr_applicant') == 'Yes' ? 'selected' : '' }}>Yes</option>
                <option value="No" {{ old('qvr_applicant') == 'No' ? 'selected' : '' }}>No</option>
            </select>
        </div>

<div class="col-md-6 hidden" id="qvrNumberField">
    <label class="form-label">QVR Number <span class="text-danger">*</span></label>
    <input type="text"
           class="form-control"
           id="qvr_number"
           name="qvr_number"
           value="{{ old('qvr_number') }}"
           maxlength="12"
           title="QVR Number format: first 3 characters, dash, then 8 letters/numbers">
</div>
        <!-- School Name comes last -->
        <div class="col-12 mt-3">
            <label class="form-label">School Name <span class="text-danger">*</span></label>
            <input type="text"
                   class="form-control"
                   id="non_esc_school_name"
                   name="school_name"
                   value="{{ old('school_name') }}">
        </div>

    </div>
</div>

                                    <hr class="my-4">

                                    <!-- Strand Selection (SHS Only) -->
                                    <div class="mt-4">
                                        <label class="form-label fw-bold">Choose Your Strand <span class="text-danger">*</span></label>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <select class="form-select" id="strand" name="strand" required>
                                                    <option value="">-- Select Strand --</option>
                                                    <option value="STEM" {{ old('strand') == 'STEM' ? 'selected' : '' }}>STEM</option>
                                                    <option value="ABM" {{ old('strand') == 'ABM' ? 'selected' : '' }}>ABM</option>
                                                    <option value="HUMSS" {{ old('strand') == 'HUMSS' ? 'selected' : '' }}>HUMSS</option>
                                                    <option value="TVL" {{ old('strand') == 'TVL' ? 'selected' : '' }}>TVL</option>
                                                </select>
                                                @error('strand')
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- TVL Specialization -->
                                        <div id="tvlSpecialization" class="hidden mt-3">
                                            <label class="form-label fw-bold">TVL Specialization <span class="text-danger">*</span></label>
                                            <select class="form-select" id="tvl_specialization" name="tvl_specialization">
                                                <option value="">-- Select Specialization --</option>
                                                <option value="Beauty/Nail Care (NCII)" {{ old('tvl_specialization') == 'Beauty/Nail Care (NCII)' ? 'selected' : '' }}>Beauty/Nail Care (NCII)</option>
                                                <option value="Bread and Pastry Production (NCII)" {{ old('tvl_specialization') == 'Bread and Pastry Production (NCII)' ? 'selected' : '' }}>Bread and Pastry Production (NCII)</option>
                                                <option value="Caregiving (NCII)" {{ old('tvl_specialization') == 'Caregiving (NCII)' ? 'selected' : '' }}>Caregiving (NCII)</option>
                                                <option value="Computer Systems Servicing (NCII)" {{ old('tvl_specialization') == 'Computer Systems Servicing (NCII)' ? 'selected' : '' }}>Computer Systems Servicing (NCII)</option>
                                                <option value="Contact Center Services (NCII)" {{ old('tvl_specialization') == 'Contact Center Services (NCII)' ? 'selected' : '' }}>Contact Center Services (NCII)</option>
                                                <option value="Cookery (NCII)" {{ old('tvl_specialization') == 'Cookery (NCII)' ? 'selected' : '' }}>Cookery (NCII)</option>
                                                <option value="Dressmaking (NCII)" {{ old('tvl_specialization') == 'Dressmaking (NCII)' ? 'selected' : '' }}>Dressmaking (NCII)</option>
                                                <option value="Electrical Installation and Maintenance (NCII)" {{ old('tvl_specialization') == 'Electrical Installation and Maintenance (NCII)' ? 'selected' : '' }}>Electrical Installation and Maintenance (NCII)</option>
                                                <option value="Food and Beverages Services (NCII)" {{ old('tvl_specialization') == 'Food and Beverages Services (NCII)' ? 'selected' : '' }}>Food and Beverages Services (NCII)</option>
                                                <option value="Front Office Services (NCII)" {{ old('tvl_specialization') == 'Front Office Services (NCII)' ? 'selected' : '' }}>Front Office Services (NCII)</option>
                                                <option value="Hairdressing (NCII)" {{ old('tvl_specialization') == 'Hairdressing (NCII)' ? 'selected' : '' }}>Hairdressing (NCII)</option>
                                                <option value="Housekeeping (NCII)" {{ old('tvl_specialization') == 'Housekeeping (NCII)' ? 'selected' : '' }}>Housekeeping (NCII)</option>
                                                <option value="Tailoring (NCII)" {{ old('tvl_specialization') == 'Tailoring (NCII)' ? 'selected' : '' }}>Tailoring (NCII)</option>
                                                <option value="Tourism Promotion Services (NCII)" {{ old('tvl_specialization') == 'Tourism Promotion Services (NCII)' ? 'selected' : '' }}>Tourism Promotion Services (NCII)</option>
                                                <option value="Wellness Massage (NCII)" {{ old('tvl_specialization') == 'Wellness Massage (NCII)' ? 'selected' : '' }}>Wellness Massage (NCII)</option>
                                            </select>
                                            @error('tvl_specialization')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr class="section-divider">

                                <!-- Family Information Section -->
                                <div class="form-section">
                                    <h4 class="section-title">
                                        <i class="fas fa-users me-2"></i>Family Information
                                    </h4>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Mother's Maiden Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="mother_name" value="{{ old('mother_name') }}" placeholder="Enter mother's maiden name" oninput="this.value = this.value.replace(/[^a-zA-Z\s.'\-]/g, '')" required>
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
                                            <label class="form-label">Father's Maiden Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="father_name" value="{{ old('father_name') }}" placeholder="Enter father's name" oninput="this.value = this.value.replace(/[^a-zA-Z\s.'\-]/g, '')" required>
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