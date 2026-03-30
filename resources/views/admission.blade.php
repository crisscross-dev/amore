@extends('layouts.app-public')

@section('title', 'Admission Application - Amore Academy')

@section('content')
<div class="admission-container">
    <div class="container">
        <div class="admission-card">
            <!-- Header -->
            <div class="admission-header">
                
                <h1><i class="fas fa-graduation-cap me-2"></i>Admission Application</h1>
                <p class="subtitle">
                    <span class="badge bg-success px-3 py-2">
                        <i class="fas fa-calendar-alt me-1"></i>School Year 2025-2026
                    </span>
                </p>

                <!-- Mode Selector (Search or Apply) -->
                <div class="mode-selector">
                    <button type="button" class="mode-btn active" data-mode="search" id="searchModeBtn">
                        <i class="fas fa-search"></i>
                        <div>Search Application</div>
                        <small>Check your status</small>
                    </button>
                    <button type="button" class="mode-btn" data-mode="apply" id="applyModeBtn">
                        <i class="fas fa-file-alt"></i>
                        <div>New Application</div>
                        <small>Apply now</small>
                    </button>
                </div>
            </div>
            
            <!-- Success Message -->

            <!-- Application Status Search (Default Visible) -->
            <div class="application-status-search mb-4" id="searchSection">
                <div class="search-header">
                    <h5><i class="fas fa-search me-2"></i>Check Application Status</h5>
                    <p class="text-muted mb-0">Already applied? Enter your Application ID or LRN to check your status</p>
                </div>
                <div class="search-body">
                    <div class="search-form">
                        <div class="search-input-group">
                            <i class="fas fa-id-card"></i>
                            <input type="text" id="statusSearchInput" class="form-control" 
                                   placeholder="Enter Application ID (e.g., APP-2025-0001) or LRN">
                            <button type="button" id="statusSearchBtn" class="btn btn-success">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </div>
                    
                    <!-- Search Results -->
                    <div id="searchResults" style="display: none;">
                        <div class="search-result-card">
                            <div class="result-header">
                                <h6><i class="fas fa-user-graduate me-2"></i><span id="resultName"></span></h6>
                                <span id="resultStatusBadge"></span>
                            </div>
                            <div class="result-body">
                                <div class="result-grid">
                                    <div class="result-item">
                                        <label><i class="fas fa-id-card me-1"></i>Application ID:</label>
                                        <span id="resultAppId"></span>
                                    </div>
                                    <div class="result-item">
                                        <label><i class="fas fa-hashtag me-1"></i>LRN:</label>
                                        <span id="resultLrn"></span>
                                    </div>
                                    <div class="result-item">
                                        <label><i class="fas fa-graduation-cap me-1"></i>Type:</label>
                                        <span id="resultType"></span>
                                    </div>
                                    <div class="result-item">
                                        <label><i class="fas fa-layer-group me-1"></i>Grade:</label>
                                        <span id="resultGrade"></span>
                                    </div>
                                    <div class="result-item">
                                        <label><i class="fas fa-calendar me-1"></i>Applied On:</label>
                                        <span id="resultDate"></span>
                                    </div>
                                    <div class="result-item" id="approvedDateContainer" style="display: none;">
                                        <label><i class="fas fa-check-circle me-1"></i>Approved On:</label>
                                        <span id="resultApprovedDate"></span>
                                    </div>
                                </div>
                                <div id="resultNotesContainer" style="display: none;" class="mt-3">
                                    <label class="d-block mb-2"><i class="fas fa-comment-dots me-1"></i>Notes:</label>
                                    <div class="alert alert-info mb-0" id="resultNotes"></div>
                                </div>
                                <div id="resultRejectionContainer" style="display: none;" class="mt-3">
                                    <label class="d-block mb-2"><i class="fas fa-exclamation-triangle me-1"></i>Rejection Reason:</label>
                                    <div class="alert alert-danger mb-0" id="resultRejection"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- No Results -->
                    <div id="noResults" style="display: none;">
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>No application found.</strong> Please check your Application ID or LRN and try again.
                        </div>
                    </div>

                    <!-- Search Error -->
                    <div id="searchError" style="display: none;">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>Error!</strong> <span id="searchErrorMessage"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="divider-section" id="dividerSection" style="display: none;">
                <span>OR</span>
            </div>

            <!-- Admission Form Section (Hidden by Default) -->
            <div id="admissionFormSection" style="display: none;">
            <div class="admission-header">
                <!-- Admission Type Selector -->
                <div class="admission-type-selector">
                <div class="admission-type-selector">
                    <button type="button" class="admission-type-btn active" data-type="jhs" id="jhsBtn">
                        <i class="fas fa-school"></i>
                        <div>Junior High School</div>
                        <small>(Grades 7-10)</small>
                    </button>
                    <button type="button" class="admission-type-btn" data-type="shs" id="shsBtn">
                        <i class="fas fa-graduation-cap"></i>
                        <div>Senior High School</div>
                        <small>(Grades 11-12)</small>
                    </button>
                </div>
            </div>

            <!-- Admission Type Indicator -->
            <div class="admission-type-indicator" id="admissionTypeIndicator">
                <div class="indicator-content">
                    <div class="indicator-icon">
                        <i class="fas fa-school"></i>
                    </div>
                    <div class="indicator-text">
                        <div class="indicator-label">Currently Applying For:</div>
                        <div class="indicator-type">Junior High School (JHS)</div>
                        <div class="indicator-grades">Grades 7-10</div>
                    </div>
                </div>
            </div>

            <!-- Progress Steps -->
            <div class="progress-steps">
                <div class="progress-line" id="progressLine" style="width: 0%;"></div>
                <div class="step-item active" data-step="1">
                    <div class="step-circle">1</div>
                    <div class="step-label">Personal Info</div>
                </div>
                <div class="step-item" data-step="2">
                    <div class="step-circle">2</div>
                    <div class="step-label">Academic Info</div>
                </div>
                <div class="step-item" data-step="3">
                    <div class="step-circle">3</div>
                    <div class="step-label">Parent Info</div>
                </div>
                <div class="step-item" data-step="4">
                    <div class="step-circle">4</div>
                    <div class="step-label">Review</div>
                </div>
            </div>

            <!-- Error Messages -->
            @if($errors->any())
                <div class="error-message">
                    <strong><i class="fas fa-exclamation-circle"></i> Please fix the following errors:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Success Message -->

            <!-- Form -->
            <form method="POST" action="{{ route('admission.store') }}" id="admissionForm">
                @csrf
                <input type="hidden" name="admission_type" id="admissionType" value="jhs">
                <input type="hidden" name="application_date" value="{{ now() }}">

                <!-- Step 1: Personal Information -->
                <div class="form-step active" data-step="1">
                    <h3 class="section-title"><i class="fas fa-user"></i> Personal Information</h3>
                    
                    <div class="info-box">
                        <i class="fas fa-info-circle"></i>
                        <p>Please provide accurate personal information. All fields marked with <span class="text-danger">*</span> are required.</p>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="lrn">LRN (Learner Reference Number) <span class="required">*</span></label>
                            <input type="text" class="form-control" id="lrn" name="lrn" 
                                   value="{{ old('lrn') }}" required maxlength="12" 
                                   placeholder="Enter 12-digit LRN">
                        </div>
                        <div class="form-group">
                            <label for="applicant_id">Applicant ID</label>
                            <input type="text" class="form-control" id="applicant_id" name="applicant_id" 
                                   value="{{ 'APP-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT) }}" 
                                   readonly>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name <span class="required">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                   value="{{ old('first_name') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="middle_name">Middle Name</label>
                            <input type="text" class="form-control" id="middle_name" name="middle_name" 
                                   value="{{ old('middle_name') }}">
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name <span class="required">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                   value="{{ old('last_name') }}" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="birthdate">Birthdate <span class="required">*</span></label>
                            <input type="date" class="form-control" id="birthdate" name="birthdate" 
                                   value="{{ old('birthdate') }}" required max="{{ date('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <label for="age">Age <span class="required">*</span></label>
                            <input type="number" class="form-control" id="age" name="age" 
                                   value="{{ old('age') }}" required min="10" max="25" readonly>
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender <span class="required">*</span></label>
                            <select class="form-control" id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                    </div>

                    <!-- SHS-Specific Fields -->
                    <div class="shs-fields" style="display: none;">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="citizenship">Citizenship <span class="required">*</span></label>
                                <input type="text" class="form-control" id="citizenship" name="citizenship" 
                                       value="{{ old('citizenship', 'Filipino') }}">
                            </div>
                            <div class="form-group">
                                <label for="height">Height (cm)</label>
                                <input type="number" class="form-control" id="height" name="height" 
                                       value="{{ old('height') }}" step="0.01" min="100" max="250">
                            </div>
                            <div class="form-group">
                                <label for="weight">Weight (kg)</label>
                                <input type="number" class="form-control" id="weight" name="weight" 
                                       value="{{ old('weight') }}" step="0.01" min="20" max="200">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone_number">Phone Number <span class="required">*</span></label>
                                <input type="tel" class="form-control" id="phone_number" name="phone_number" 
                                       value="{{ old('phone_number') }}" placeholder="09XX-XXX-XXXX">
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address <span class="required">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ old('email') }}" placeholder="your.email@example.com">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="religion">Religion <span class="required">*</span></label>
                        <input type="text" class="form-control" id="religion" name="religion" 
                               value="{{ old('religion') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="address">Complete Address <span class="required">*</span></label>
                        <textarea class="form-control" id="address" name="address" rows="3" 
                                  required>{{ old('address') }}</textarea>
                    </div>
                </div>

                <!-- Step 2: Academic Information -->
                <div class="form-step" data-step="2">
                    <h3 class="section-title"><i class="fas fa-book"></i> Academic Information</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="applying_for_grade">Applying for Grade <span class="required">*</span></label>
                            <select class="form-control" id="applying_for_grade" name="applying_for_grade" required>
                                <option value="">Select Grade Level</option>
                                <!-- JHS Options -->
                                <optgroup label="Junior High School" class="jhs-grades">
                                    <option value="Grade 7" {{ old('applying_for_grade') == 'Grade 7' ? 'selected' : '' }}>Grade 7</option>
                                    <option value="Grade 8" {{ old('applying_for_grade') == 'Grade 8' ? 'selected' : '' }}>Grade 8</option>
                                    <option value="Grade 9" {{ old('applying_for_grade') == 'Grade 9' ? 'selected' : '' }}>Grade 9</option>
                                    <option value="Grade 10" {{ old('applying_for_grade') == 'Grade 10' ? 'selected' : '' }}>Grade 10</option>
                                </optgroup>
                                <!-- SHS Options -->
                                <optgroup label="Senior High School" class="shs-grades" style="display: none;">
                                    <option value="Grade 11" {{ old('applying_for_grade') == 'Grade 11' ? 'selected' : '' }}>Grade 11</option>
                                    <option value="Grade 12" {{ old('applying_for_grade') == 'Grade 12' ? 'selected' : '' }}>Grade 12</option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="school_year">School Year <span class="required">*</span></label>
                            <input type="text" class="form-control" id="school_year" name="school_year" 
                                   value="2025-2026" readonly required>
                        </div>
                    </div>

                    <!-- SHS Strand Selection -->
                    <div class="shs-fields" style="display: none;">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="strand">Academic Strand <span class="required">*</span></label>
                                <select class="form-control" id="strand" name="strand">
                                    <option value="">Select Strand</option>
                                    <option value="STEM" {{ old('strand') == 'STEM' ? 'selected' : '' }}>STEM - Science, Technology, Engineering, and Mathematics</option>
                                    <option value="ABM" {{ old('strand') == 'ABM' ? 'selected' : '' }}>ABM - Accountancy, Business, and Management</option>
                                    <option value="HUMSS" {{ old('strand') == 'HUMSS' ? 'selected' : '' }}>HUMSS - Humanities and Social Sciences</option>
                                    <option value="GAS" {{ old('strand') == 'GAS' ? 'selected' : '' }}>GAS - General Academic Strand</option>
                                    <option value="TVL" {{ old('strand') == 'TVL' ? 'selected' : '' }}>TVL - Technical-Vocational-Livelihood</option>
                                </select>
                            </div>
                            <div class="form-group tvl-field" style="display: none;">
                                <label for="tvl_specialization">TVL Specialization</label>
                                <select class="form-control" id="tvl_specialization" name="tvl_specialization">
                                    <option value="">Select Specialization</option>
                                    <option value="ICT" {{ old('tvl_specialization') == 'ICT' ? 'selected' : '' }}>Information and Communications Technology</option>
                                    <option value="Home Economics" {{ old('tvl_specialization') == 'Home Economics' ? 'selected' : '' }}>Home Economics</option>
                                    <option value="Agri-Fishery Arts" {{ old('tvl_specialization') == 'Agri-Fishery Arts' ? 'selected' : '' }}>Agri-Fishery Arts</option>
                                    <option value="Industrial Arts" {{ old('tvl_specialization') == 'Industrial Arts' ? 'selected' : '' }}>Industrial Arts</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="previous_school">Previous School <span class="required">*</span></label>
                            <input type="text" class="form-control" id="previous_school" name="previous_school" 
                                   value="{{ old('previous_school') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="school_type">School Type <span class="required">*</span></label>
                            <select class="form-control" id="school_type" name="school_type" required>
                                <option value="">Select Type</option>
                                <option value="Public" {{ old('school_type') == 'Public' ? 'selected' : '' }}>Public</option>
                                <option value="Private" {{ old('school_type') == 'Private' ? 'selected' : '' }}>Private</option>
                            </select>
                        </div>
                    </div>

                    <!-- SHS ESC Fields -->
                    <div class="shs-fields" style="display: none;">
                        <div class="info-box">
                            <i class="fas fa-info-circle"></i>
                            <p>If you are an ESC (Education Service Contracting) grantee, please provide your ESC details.</p>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="private_school_type">Private School Type</label>
                                <select class="form-control" id="private_school_type" name="private_school_type">
                                    <option value="">Select Type</option>
                                    <option value="Sectarian" {{ old('private_school_type') == 'Sectarian' ? 'selected' : '' }}>Sectarian</option>
                                    <option value="Non-Sectarian" {{ old('private_school_type') == 'Non-Sectarian' ? 'selected' : '' }}>Non-Sectarian</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="esc_student_no">ESC Student Number</label>
                                <input type="text" class="form-control" id="esc_student_no" name="esc_student_no" 
                                       value="{{ old('esc_student_no') }}" placeholder="If applicable">
                            </div>
                            <div class="form-group">
                                <label for="esc_school_id">ESC School ID</label>
                                <input type="text" class="form-control" id="esc_school_id" name="esc_school_id" 
                                       value="{{ old('esc_school_id') }}" placeholder="If applicable">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Parent Information -->
                <div class="form-step" data-step="3">
                    <h3 class="section-title"><i class="fas fa-users"></i> Parent/Guardian Information</h3>

                    <div class="info-box">
                        <i class="fas fa-info-circle"></i>
                        <p>Please provide information about your parent or legal guardian.</p>
                    </div>

                    <h5 class="mt-4 mb-3"><i class="fas fa-male"></i> Father's Information</h5>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="father_name">Father's Full Name <span class="required">*</span></label>
                            <input type="text" class="form-control" id="father_name" name="father_name" 
                                   value="{{ old('father_name') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="father_occupation">Father's Occupation <span class="required">*</span></label>
                            <input type="text" class="form-control" id="father_occupation" name="father_occupation" 
                                   value="{{ old('father_occupation') }}" required>
                        </div>
                    </div>

                    <h5 class="mt-4 mb-3"><i class="fas fa-female"></i> Mother's Information</h5>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="mother_maiden_name">Mother's Maiden Name <span class="required">*</span></label>
                            <input type="text" class="form-control" id="mother_maiden_name" name="mother_maiden_name" 
                                   value="{{ old('mother_maiden_name') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="mother_occupation">Mother's Occupation <span class="required">*</span></label>
                            <input type="text" class="form-control" id="mother_occupation" name="mother_occupation" 
                                   value="{{ old('mother_occupation') }}" required>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Review and Submit -->
                <div class="form-step" data-step="4">
                    <h3 class="section-title"><i class="fas fa-check-circle"></i> Review Your Application</h3>

                    <div class="warning-box">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Important:</strong> Please review all information carefully before submitting. Make sure all details are accurate and complete.
                    </div>

                    <div id="reviewContent" class="review-section">
                        <!-- Review content will be populated by JavaScript -->
                    </div>

                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="confirm_details" name="confirm_details" value="1" required>
                            <label for="confirm_details">
                                I hereby certify that all information provided is true and correct to the best of my knowledge. <span class="required">*</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="button" class="btn-admission btn-secondary-admission" id="prevBtn" style="display: none;">
                        <i class="fas fa-arrow-left"></i> Previous
                    </button>
                    <div></div>
                    <button type="button" class="btn-admission btn-primary-admission" id="nextBtn">
                        Next <i class="fas fa-arrow-right"></i>
                    </button>
                    <button type="submit" class="btn-admission btn-primary-admission" id="submitBtn" style="display: none;">
                        <i class="fas fa-paper-plane"></i> Submit Application
                    </button>
                </div>
            </form>
            </div>
            <!-- End Admission Form Section -->
        </div>
    </div>
</div>

<!-- Admission Form JavaScript -->
@vite(['resources/js/admission.js'])

@endsection
