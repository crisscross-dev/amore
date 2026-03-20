/**
 * Admission Form Multi-Step Logic
 * Handles JHS/SHS switching and form navigation
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Mode Switcher (Search or Apply)
    initializeModeSwitcher();

    // Initialize Application Status Search
    initializeStatusSearch();

    // Elements
    const form = document.getElementById('admissionForm');
    const formSteps = document.querySelectorAll('.form-step');
    const stepItems = document.querySelectorAll('.step-item');
    const progressLine = document.getElementById('progressLine');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    const jhsBtn = document.getElementById('jhsBtn');
    const shsBtn = document.getElementById('shsBtn');
    const admissionTypeInput = document.getElementById('admissionType');
    
    let currentStep = 1;
    const totalSteps = formSteps.length;

    // Initialize
    updateProgress();
    setupEventListeners();
    calculateAge(); // Calculate age on load if birthdate exists

    /**
     * Initialize Mode Switcher between Search and Apply
     */
    function initializeModeSwitcher() {
        const searchModeBtn = document.getElementById('searchModeBtn');
        const applyModeBtn = document.getElementById('applyModeBtn');
        const searchSection = document.getElementById('searchSection');
        const admissionFormSection = document.getElementById('admissionFormSection');
        const dividerSection = document.getElementById('dividerSection');

        if (!searchModeBtn || !applyModeBtn) return;

        // Default: Show search, hide form
        searchSection.style.display = 'block';
        admissionFormSection.style.display = 'none';
        dividerSection.style.display = 'none';

        // Search mode button
        searchModeBtn.addEventListener('click', function() {
            // Update button states
            searchModeBtn.classList.add('active');
            applyModeBtn.classList.remove('active');

            // Show search, hide form
            searchSection.style.display = 'block';
            admissionFormSection.style.display = 'none';
            dividerSection.style.display = 'none';

            // Smooth scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Apply mode button
        applyModeBtn.addEventListener('click', function() {
            // Update button states
            applyModeBtn.classList.add('active');
            searchModeBtn.classList.remove('active');

            // Hide search, show form
            searchSection.style.display = 'none';
            admissionFormSection.style.display = 'block';
            dividerSection.style.display = 'none';

            // Smooth scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /**
     * Initialize Application Status Search
     */
    function initializeStatusSearch() {
        const searchBtn = document.getElementById('statusSearchBtn');
        const searchInput = document.getElementById('statusSearchInput');
        const searchResults = document.getElementById('searchResults');
        const noResults = document.getElementById('noResults');
        const searchError = document.getElementById('searchError');

        if (!searchBtn || !searchInput) return;

        // Handle search button click
        searchBtn.addEventListener('click', performSearch);

        // Handle Enter key in search input
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch();
            }
        });

        function performSearch() {
            const query = searchInput.value.trim();

            // Reset previous results
            searchResults.style.display = 'none';
            noResults.style.display = 'none';
            searchError.style.display = 'none';

            if (!query) {
                showError('Please enter an Application ID or LRN');
                return;
            }

            // Show loading state
            searchBtn.disabled = true;
            searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';

            // Perform AJAX search
            fetch(`/admission/search?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayResults(data.data);
                    } else {
                        noResults.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    showError('An error occurred while searching. Please try again.');
                })
                .finally(() => {
                    // Reset button state
                    searchBtn.disabled = false;
                    searchBtn.innerHTML = '<i class="fas fa-search"></i> Search';
                });
        }

        function displayResults(data) {
            // Populate result fields
            document.getElementById('resultName').textContent = data.full_name;
            document.getElementById('resultAppId').textContent = data.applicant_id;
            document.getElementById('resultLrn').textContent = data.lrn;
            document.getElementById('resultType').textContent = data.admission_type_full;
            document.getElementById('resultGrade').textContent = data.applying_for_grade;
            document.getElementById('resultDate').textContent = data.application_date;

            // Set status badge
            const statusBadge = getStatusBadge(data.status);
            document.getElementById('resultStatusBadge').innerHTML = statusBadge;

            // Show/hide approved date
            const approvedDateContainer = document.getElementById('approvedDateContainer');
            if (data.approved_at) {
                document.getElementById('resultApprovedDate').textContent = data.approved_at;
                approvedDateContainer.style.display = 'block';
            } else {
                approvedDateContainer.style.display = 'none';
            }

            // Show/hide approval notes
            const notesContainer = document.getElementById('resultNotesContainer');
            if (data.approval_notes) {
                document.getElementById('resultNotes').textContent = data.approval_notes;
                notesContainer.style.display = 'block';
            } else {
                notesContainer.style.display = 'none';
            }

            // Show/hide rejection reason
            const rejectionContainer = document.getElementById('resultRejectionContainer');
            if (data.rejection_reason) {
                document.getElementById('resultRejection').textContent = data.rejection_reason;
                rejectionContainer.style.display = 'block';
            } else {
                rejectionContainer.style.display = 'none';
            }

            // Show results
            searchResults.style.display = 'block';

            // Scroll to results
            searchResults.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function getStatusBadge(status) {
            const badges = {
                'pending': '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pending Review</span>',
                'approved': '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Approved</span>',
                'rejected': '<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Rejected</span>',
                'waitlisted': '<span class="badge bg-secondary"><i class="fas fa-list me-1"></i>Waitlisted</span>'
            };
            return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
        }

        function showError(message) {
            document.getElementById('searchErrorMessage').textContent = message;
            searchError.style.display = 'block';
        }
    }

    /**
     * Setup all event listeners
     */
    function setupEventListeners() {
        // Navigation buttons
        nextBtn.addEventListener('click', handleNext);
        prevBtn.addEventListener('click', handlePrevious);
        
        // Admission type buttons
        jhsBtn.addEventListener('click', () => switchAdmissionType('jhs'));
        shsBtn.addEventListener('click', () => switchAdmissionType('shs'));
        
        // Age calculation
        const birthdateInput = document.getElementById('birthdate');
        if (birthdateInput) {
            birthdateInput.addEventListener('change', calculateAge);
        }

        // Strand selection for TVL
        const strandSelect = document.getElementById('strand');
        if (strandSelect) {
            strandSelect.addEventListener('change', handleStrandChange);
        }

        // Form submission
        form.addEventListener('submit', handleSubmit);
    }

    /**
     * Switch between JHS and SHS admission types
     */
    function switchAdmissionType(type) {
        // Update buttons
        jhsBtn.classList.toggle('active', type === 'jhs');
        shsBtn.classList.toggle('active', type === 'shs');
        
        // Update hidden input
        admissionTypeInput.value = type;
        
        // Update the admission type indicator
        updateAdmissionIndicator(type);
        
        // Show/hide SHS-specific fields
        const shsFields = document.querySelectorAll('.shs-fields');
        shsFields.forEach(field => {
            field.style.display = type === 'shs' ? 'block' : 'none';
            
            // Update required status for SHS fields
            const inputs = field.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                if (type === 'shs') {
                    // Make SHS-specific required fields required
                    if (['citizenship', 'phone_number', 'email', 'strand'].includes(input.name)) {
                        input.required = true;
                    }
                } else {
                    // Remove required from SHS fields when in JHS mode
                    input.required = false;
                }
            });
        });
        
        // Update grade options
        const jhsGrades = document.querySelector('.jhs-grades');
        const shsGrades = document.querySelector('.shs-grades');
        if (jhsGrades && shsGrades) {
            jhsGrades.style.display = type === 'jhs' ? 'block' : 'none';
            shsGrades.style.display = type === 'shs' ? 'block' : 'none';
        }
        
        // Reset grade selection
        const gradeSelect = document.getElementById('applying_for_grade');
        if (gradeSelect) {
            gradeSelect.value = '';
        }
        
        // Reset strand if switching to JHS
        if (type === 'jhs') {
            const strandSelect = document.getElementById('strand');
            if (strandSelect) {
                strandSelect.value = '';
            }
            hideTVLField();
        }
    }

    /**
     * Update admission type indicator
     */
    function updateAdmissionIndicator(type) {
        const indicator = document.getElementById('admissionTypeIndicator');
        if (!indicator) return;

        // Add transition class
        indicator.classList.add('transitioning');

        // Update content after a brief delay for animation
        setTimeout(() => {
            const iconElement = indicator.querySelector('.indicator-icon i');
            const typeElement = indicator.querySelector('.indicator-type');
            const gradesElement = indicator.querySelector('.indicator-grades');

            if (type === 'jhs') {
                iconElement.className = 'fas fa-school';
                typeElement.textContent = 'Junior High School (JHS)';
                gradesElement.textContent = 'Grades 7-10';
            } else {
                iconElement.className = 'fas fa-graduation-cap';
                typeElement.textContent = 'Senior High School (SHS)';
                gradesElement.textContent = 'Grades 11-12';
            }

            // Remove transition class
            setTimeout(() => {
                indicator.classList.remove('transitioning');
            }, 100);
        }, 250);
    }

    /**
     * Handle strand selection change
     */
    function handleStrandChange(e) {
        const tvlField = document.querySelector('.tvl-field');
        const tvlSelect = document.getElementById('tvl_specialization');
        
        if (e.target.value === 'TVL') {
            tvlField.style.display = 'block';
            tvlSelect.required = true;
        } else {
            hideTVLField();
        }
    }

    /**
     * Hide TVL specialization field
     */
    function hideTVLField() {
        const tvlField = document.querySelector('.tvl-field');
        const tvlSelect = document.getElementById('tvl_specialization');
        if (tvlField) {
            tvlField.style.display = 'none';
            tvlSelect.required = false;
            tvlSelect.value = '';
        }
    }

    /**
     * Calculate age from birthdate
     */
    function calculateAge() {
        const birthdateInput = document.getElementById('birthdate');
        const ageInput = document.getElementById('age');
        
        if (birthdateInput && ageInput && birthdateInput.value) {
            const birthDate = new Date(birthdateInput.value);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            
            ageInput.value = age;
        }
    }

    /**
     * Handle next button click
     */
    function handleNext() {
        // Validate current step
        if (!validateStep(currentStep)) {
            return;
        }
        
        // Move to next step
        if (currentStep < totalSteps) {
            currentStep++;
            updateProgress();
            
            // Populate review if on last step
            if (currentStep === totalSteps) {
                populateReview();
            }
        }
    }

    /**
     * Handle previous button click
     */
    function handlePrevious() {
        if (currentStep > 1) {
            currentStep--;
            updateProgress();
        }
    }

    /**
     * Validate current step
     */
    function validateStep(step) {
        const currentStepElement = document.querySelector(`.form-step[data-step="${step}"]`);
        const inputs = currentStepElement.querySelectorAll('input[required], select[required], textarea[required]');
        
        let isValid = true;
        
        inputs.forEach(input => {
            // Remove previous error states
            input.classList.remove('is-invalid');
            const errorMsg = input.parentElement.querySelector('.invalid-feedback');
            if (errorMsg) {
                errorMsg.remove();
            }
            
            // Validate
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('is-invalid');
                showFieldError(input, 'This field is required');
            } else if (input.type === 'email' && !isValidEmail(input.value)) {
                isValid = false;
                input.classList.add('is-invalid');
                showFieldError(input, 'Please enter a valid email address');
            } else if (input.name === 'lrn' && input.value.length !== 12) {
                isValid = false;
                input.classList.add('is-invalid');
                showFieldError(input, 'LRN must be 12 digits');
            }
        });
        
        if (!isValid) {
            // Scroll to first error
            const firstError = currentStepElement.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        }
        
        return isValid;
    }

    /**
     * Show field error message
     */
    function showFieldError(input, message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        input.parentElement.appendChild(errorDiv);
    }

    /**
     * Validate email format
     */
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    /**
     * Update progress indicators
     */
    function updateProgress() {
        // Update form steps visibility
        formSteps.forEach((step, index) => {
            step.classList.toggle('active', index + 1 === currentStep);
        });
        
        // Update step indicators
        stepItems.forEach((item, index) => {
            const stepNum = index + 1;
            item.classList.remove('active', 'completed');
            
            if (stepNum < currentStep) {
                item.classList.add('completed');
                item.querySelector('.step-circle').innerHTML = '<i class="fas fa-check"></i>';
            } else if (stepNum === currentStep) {
                item.classList.add('active');
                item.querySelector('.step-circle').textContent = stepNum;
            } else {
                item.querySelector('.step-circle').textContent = stepNum;
            }
        });
        
        // Update progress line
        const progressPercent = ((currentStep - 1) / (totalSteps - 1)) * 100;
        progressLine.style.width = progressPercent + '%';
        
        // Update buttons visibility
        prevBtn.style.display = currentStep === 1 ? 'none' : 'inline-flex';
        nextBtn.style.display = currentStep === totalSteps ? 'none' : 'inline-flex';
        submitBtn.style.display = currentStep === totalSteps ? 'inline-flex' : 'none';
        
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    /**
     * Populate review section
     */
    function populateReview() {
        const reviewContent = document.getElementById('reviewContent');
        const admissionType = admissionTypeInput.value.toUpperCase();
        
        const formData = {
            'Admission Type': admissionType === 'JHS' ? 'Junior High School' : 'Senior High School',
            'Applicant ID': getFieldValue('applicant_id'),
            'LRN': getFieldValue('lrn'),
            'Full Name': `${getFieldValue('first_name')} ${getFieldValue('middle_name')} ${getFieldValue('last_name')}`.trim(),
            'Birthdate': formatDate(getFieldValue('birthdate')),
            'Age': getFieldValue('age'),
            'Gender': getFieldValue('gender'),
            'Religion': getFieldValue('religion'),
            'Address': getFieldValue('address'),
            'Applying for Grade': getFieldValue('applying_for_grade'),
            'School Year': getFieldValue('school_year'),
            'Previous School': getFieldValue('previous_school'),
            'School Type': getFieldValue('school_type'),
            "Father's Name": getFieldValue('father_name'),
            "Father's Occupation": getFieldValue('father_occupation'),
            "Mother's Maiden Name": getFieldValue('mother_maiden_name'),
            "Mother's Occupation": getFieldValue('mother_occupation')
        };
        
        // Add SHS-specific fields
        if (admissionType === 'SHS') {
            formData['Citizenship'] = getFieldValue('citizenship');
            formData['Height'] = getFieldValue('height') ? getFieldValue('height') + ' cm' : 'N/A';
            formData['Weight'] = getFieldValue('weight') ? getFieldValue('weight') + ' kg' : 'N/A';
            formData['Phone Number'] = getFieldValue('phone_number');
            formData['Email'] = getFieldValue('email');
            formData['Strand'] = getFieldValue('strand');
            
            if (getFieldValue('strand') === 'TVL') {
                formData['TVL Specialization'] = getFieldValue('tvl_specialization');
            }
            
            if (getFieldValue('esc_student_no')) {
                formData['ESC Student No.'] = getFieldValue('esc_student_no');
            }
        }
        
        // Generate HTML
        let html = '';
        for (const [label, value] of Object.entries(formData)) {
            if (value && value !== 'N/A') {
                html += `
                    <div class="review-item">
                        <div class="review-label">${label}:</div>
                        <div class="review-value">${value}</div>
                    </div>
                `;
            }
        }
        
        reviewContent.innerHTML = html;
    }

    /**
     * Get field value by name
     */
    function getFieldValue(name) {
        const field = document.querySelector(`[name="${name}"]`);
        if (!field) return '';
        
        if (field.type === 'checkbox') {
            return field.checked ? 'Yes' : 'No';
        }
        
        return field.value || '';
    }

    /**
     * Format date to readable format
     */
    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
    }

    /**
     * Handle form submission
     */
    function handleSubmit(e) {
        // Validate the confirmation checkbox
        const confirmCheckbox = document.getElementById('confirm_details');
        if (!confirmCheckbox.checked) {
            e.preventDefault();
            alert('Please confirm that all details are accurate before submitting.');
            confirmCheckbox.focus();
            return false;
        }
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<div class="loading-spinner"></div> Submitting...';
        
        // Form will submit normally
        return true;
    }

    // Handle browser back button
    window.addEventListener('popstate', function(e) {
        if (currentStep > 1) {
            e.preventDefault();
            handlePrevious();
        }
    });
});
