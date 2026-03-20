/**
 * Register Form - LRN Validation with Auto-Population
 * Client-side validation for LRN field and auto-population of grade level and strand
 */

document.addEventListener('DOMContentLoaded', function() {
    const lrnInput = document.getElementById('lrn');
    const gradeLevelSelect = document.getElementById('grade_level');
    const accountTypeRadios = document.querySelectorAll('input[name="account_type"]');
    
    if (lrnInput) {
        let typingTimer;
        const typingDelay = 800; // Wait 800ms after user stops typing
        
        // Real-time LRN validation
        lrnInput.addEventListener('input', function() {
            clearTimeout(typingTimer);
            
            const lrnValue = this.value.trim();
            
            // Clear auto-populated fields when LRN changes
            if (gradeLevelSelect) {
                gradeLevelSelect.disabled = false;
                gradeLevelSelect.classList.remove('auto-filled', 'bg-light');
                // Reset to default if it was auto-filled
                if (gradeLevelSelect.dataset.autoFilled === 'true') {
                    resetGradeLevelOptions();
                    gradeLevelSelect.value = '';
                    gradeLevelSelect.dataset.autoFilled = 'false';
                }
            }
            
            // Only validate if LRN is 12 digits
            if (lrnValue.length === 12 && /^[0-9]{12}$/.test(lrnValue)) {
                typingTimer = setTimeout(() => {
                    validateLRN(lrnValue);
                }, typingDelay);
            } else {
                // Clear any previous validation messages
                clearLRNValidation();
            }
        });
    }
    
    /**
     * Validate LRN against database and auto-populate fields
     */
    function validateLRN(lrn) {
        // Create a visual feedback element if it doesn't exist
        let feedbackElement = document.getElementById('lrn-validation-feedback');
        if (!feedbackElement) {
            feedbackElement = document.createElement('div');
            feedbackElement.id = 'lrn-validation-feedback';
            feedbackElement.className = 'mt-2';
            lrnInput.parentElement.appendChild(feedbackElement);
        }
        
        // Show loading state
        feedbackElement.innerHTML = '<small class="text-muted"><i class="fas fa-spinner fa-spin me-1"></i>Validating LRN...</small>';
        
        // Make AJAX request to validate LRN
        fetch('/api/validate-lrn', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ lrn: lrn })
        })
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                // LRN found in database
                feedbackElement.innerHTML = `<small class="text-success"><i class="fas fa-check-circle me-1"></i>${data.message}</small>`;
                lrnInput.classList.remove('is-invalid');
                lrnInput.classList.add('is-valid');
                
                // Auto-populate grade level and strand
                autoPopulateFields(data);
            } else {
                // LRN not found
                feedbackElement.innerHTML = '<small class="text-danger"><i class="fas fa-times-circle me-1"></i>No LRN found in the System</small>';
                lrnInput.classList.remove('is-valid');
                lrnInput.classList.add('is-invalid');
            }
        })
        .catch(error => {
            console.error('LRN validation error:', error);
            feedbackElement.innerHTML = '<small class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>Unable to validate LRN</small>';
        });
    }
    
    /**
     * Auto-populate grade level and strand based on LRN validation response
     */
    function autoPopulateFields(data) {
        if (!gradeLevelSelect) return;
        
        // For JHS students (Grade 7-10)
        if (data.admission_type === 'jhs') {
            // Simply set the grade level value
            gradeLevelSelect.value = data.grade_level;
            gradeLevelSelect.disabled = true;
            gradeLevelSelect.classList.add('auto-filled', 'bg-light');
            gradeLevelSelect.dataset.autoFilled = 'true';
            
            // Add tooltip to indicate auto-filled
            gradeLevelSelect.title = 'Auto-filled from your approved admission';
        }
        
        // For SHS students (Grade 11-12 with strand)
        if (data.admission_type === 'shs') {
            // Combine grade level and strand (e.g., "Grade 11 - STEM")
            const fullGradeLevel = `${data.grade_level} - ${data.strand}`;
            
            // First, trigger the change event to load strand options
            gradeLevelSelect.value = data.grade_level;
            
            // Use setTimeout to wait for the grade level change event to complete
            setTimeout(() => {
                // Now set the full value with strand
                gradeLevelSelect.value = fullGradeLevel;
                gradeLevelSelect.disabled = true;
                gradeLevelSelect.classList.add('auto-filled', 'bg-light');
                gradeLevelSelect.dataset.autoFilled = 'true';
                
                // Add tooltip to indicate auto-filled
                gradeLevelSelect.title = 'Auto-filled from your approved admission';
            }, 100);
        }
    }
    
    /**
     * Reset grade level dropdown to original options
     */
    function resetGradeLevelOptions() {
        if (!gradeLevelSelect) return;
        
        // Reset to original grade level options
        gradeLevelSelect.innerHTML = `
            <option value="" disabled selected>Select Grade Level</option>
            <option value="Grade 7">Grade 7</option>
            <option value="Grade 8">Grade 8</option>
            <option value="Grade 9">Grade 9</option>
            <option value="Grade 10">Grade 10</option>
            <option value="Grade 11">Grade 11</option>
            <option value="Grade 12">Grade 12</option>
        `;
        
        // Remove tooltip
        gradeLevelSelect.removeAttribute('title');
    }
    
    /**
     * Clear LRN validation feedback
     */
    function clearLRNValidation() {
        const feedbackElement = document.getElementById('lrn-validation-feedback');
        if (feedbackElement) {
            feedbackElement.innerHTML = '';
        }
        lrnInput.classList.remove('is-valid', 'is-invalid');
    }
});
