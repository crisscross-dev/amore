/**
 * Common admission form functionality
 * Used by both JHS and SHS forms
 */

export function initializeCommonFormLogic() {
    // Age calculation from date of birth
    const dobInput = document.getElementById('date_of_birth');
    const ageInput = document.getElementById('age');
    
    if (dobInput && ageInput) {
        dobInput.addEventListener('change', function() {
            const dob = new Date(this.value);
            const today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            const monthDiff = today.getMonth() - dob.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                age--;
            }
            
            ageInput.value = age >= 0 ? age : '';
        });
    }

    // LRN digit-only validation
    initializeLRNValidation();

    // Keep composed address field in sync
    initializeAddressComposer();

    // School type selection logic
    const schoolTypeRadios = document.querySelectorAll('input[name="school_type"]');
    const publicSchoolDiv = document.getElementById('publicSchool');
    const privateOptionsDiv = document.getElementById('privateOptions');
    
    if (schoolTypeRadios.length > 0) {
        schoolTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                handleSchoolTypeChange(this.value);
            });
        });
        
        // Initialize on page load
        const checkedRadio = document.querySelector('input[name="school_type"]:checked');
        if (checkedRadio) {
            handleSchoolTypeChange(checkedRadio.value);
        }
    }

    // Private school type selection logic
    const privateTypeRadios = document.querySelectorAll('input[name="private_type"]');
    const escFieldsDiv = document.getElementById('escFields');
    const nonEscFieldDiv = document.getElementById('nonEscField');
    
    if (privateTypeRadios.length > 0) {
        privateTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                handlePrivateTypeChange(this.value);
            });
        });
        
        // Initialize on page load
        const checkedPrivateRadio = document.querySelector('input[name="private_type"]:checked');
        if (checkedPrivateRadio) {
            handlePrivateTypeChange(checkedPrivateRadio.value);
        }
    }

    // Form validation
    const form = document.getElementById('enrollmentForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
            }
        });
    }
}

/**
 * Keep the combined address field synchronized with split inputs.
 */
function initializeAddressComposer() {
    const streetInput = document.querySelector('input[name="address_street"]');
    const barangayInput = document.querySelector('input[name="address_barangay"]');
    const cityInput = document.querySelector('input[name="address_city"]');
    const provinceInput = document.querySelector('input[name="address_province"]');
    const addressInput = document.querySelector('input[name="address"]');
    const form = document.getElementById('enrollmentForm');

    if (!streetInput || !barangayInput || !cityInput || !provinceInput || !addressInput || !form) {
        return;
    }

    const syncAddress = () => {
        const parts = [
            streetInput.value.trim(),
            barangayInput.value.trim(),
            cityInput.value.trim(),
            provinceInput.value.trim(),
        ].filter(Boolean);

        addressInput.value = parts.join(', ');
    };

    [streetInput, barangayInput, cityInput, provinceInput].forEach((input) => {
        input.addEventListener('input', syncAddress);
        input.addEventListener('change', syncAddress);
    });

    form.addEventListener('submit', syncAddress);
    syncAddress();
}

/**
 * Handle school type change (Public/Private)
 */
function handleSchoolTypeChange(schoolType) {
    const publicSchoolDiv = document.getElementById('publicSchool');
    const privateOptionsDiv = document.getElementById('privateOptions');
    const escFieldsDiv = document.getElementById('escFields');
    const nonEscFieldDiv = document.getElementById('nonEscField');
    
    // Clear all school-related fields
    clearSchoolFields();
    
    if (schoolType === 'Public') {
        publicSchoolDiv?.classList.remove('hidden');
        privateOptionsDiv?.classList.add('hidden');
        escFieldsDiv?.classList.add('hidden');
        nonEscFieldDiv?.classList.add('hidden');
        
        // Enable public school fields
        const publicSchoolName = document.getElementById('public_school_name');
        if (publicSchoolName) publicSchoolName.disabled = false;
    } else if (schoolType === 'Private') {
        publicSchoolDiv?.classList.add('hidden');
        privateOptionsDiv?.classList.remove('hidden');
        
        // Check if private type is already selected
        const checkedPrivateRadio = document.querySelector('input[name="private_type"]:checked');
        if (checkedPrivateRadio) {
            handlePrivateTypeChange(checkedPrivateRadio.value);
        } else {
            escFieldsDiv?.classList.add('hidden');
            nonEscFieldDiv?.classList.add('hidden');
        }
    }
}

/**
 * Handle private school type change (ESC/Non-ESC)
 */
function handlePrivateTypeChange(privateType) {
    const escFieldsDiv = document.getElementById('escFields');
    const nonEscFieldDiv = document.getElementById('nonEscField');
    
    // Clear private school fields
    clearPrivateSchoolFields();
    
    if (privateType === 'ESC') {
        escFieldsDiv?.classList.remove('hidden');
        nonEscFieldDiv?.classList.add('hidden');
        
        // Enable ESC fields
        const escNo = document.getElementById('esc_no');
        const escSchoolId = document.getElementById('esc_school_id');
        const escSchoolName = document.getElementById('esc_school_name');
        
        if (escNo) escNo.disabled = false;
        if (escSchoolId) escSchoolId.disabled = false;
        if (escSchoolName) escSchoolName.disabled = false;
    } else if (privateType === 'Non-ESC') {
        escFieldsDiv?.classList.add('hidden');
        nonEscFieldDiv?.classList.remove('hidden');
        
        // Enable Non-ESC fields
        const nonEscSchoolName = document.getElementById('non_esc_school_name');
        if (nonEscSchoolName) nonEscSchoolName.disabled = false;
    }
}

/**
 * Clear all school-related fields
 */
function clearSchoolFields() {
    // Clear and disable public school field
    const publicSchoolName = document.getElementById('public_school_name');
    if (publicSchoolName) {
        publicSchoolName.value = '';
        publicSchoolName.disabled = true;
    }
    
    // Clear private school selection
    const privateTypeRadios = document.querySelectorAll('input[name="private_type"]');
    privateTypeRadios.forEach(radio => {
        radio.checked = false;
    });
    
    clearPrivateSchoolFields();
}

/**
 * Clear private school fields
 */
function clearPrivateSchoolFields() {
    // Clear and disable ESC fields
    const escNo = document.getElementById('esc_no');
    const escSchoolId = document.getElementById('esc_school_id');
    const escSchoolName = document.getElementById('esc_school_name');
    
    if (escNo) {
        escNo.value = '';
        escNo.disabled = true;
    }
    if (escSchoolId) {
        escSchoolId.value = '';
        escSchoolId.disabled = true;
    }
    if (escSchoolName) {
        escSchoolName.value = '';
        escSchoolName.disabled = true;
    }
    
    // Clear and disable Non-ESC field
    const nonEscSchoolName = document.getElementById('non_esc_school_name');
    if (nonEscSchoolName) {
        nonEscSchoolName.value = '';
        nonEscSchoolName.disabled = true;
    }
}

/**
 * Validate form before submission
 */
function validateForm() {
    let isValid = true;
    const errors = [];
    
    // Validate LRN (12 digits)
    const lrn = document.getElementById('lrn');
    if (lrn && lrn.value.length !== 12) {
        errors.push('LRN must be exactly 12 digits');
        isValid = false;
    }
    
    // Validate school type specific fields
    const schoolType = document.querySelector('input[name="school_type"]:checked');
    if (schoolType) {
        if (schoolType.value === 'Public') {
            const publicSchoolName = document.getElementById('public_school_name');
            if (publicSchoolName && !publicSchoolName.value.trim()) {
                errors.push('School name is required for public school');
                isValid = false;
            }
        } else if (schoolType.value === 'Private') {
            const privateType = document.querySelector('input[name="private_type"]:checked');
            if (!privateType) {
                errors.push('Please select ESC or Non-ESC for private school');
                isValid = false;
            } else if (privateType.value === 'ESC') {
                const escNo = document.getElementById('esc_no');
                const escSchoolId = document.getElementById('esc_school_id');
                const escSchoolName = document.getElementById('esc_school_name');
                
                if (escNo && escNo.value.length !== 8) {
                    errors.push('Student ESC No. must be exactly 8 characters');
                    isValid = false;
                }
                if (escSchoolId && escSchoolId.value.length !== 6) {
                    errors.push('ESC School ID must be exactly 6 characters');
                    isValid = false;
                }
                if (escSchoolName && !escSchoolName.value.trim()) {
                    errors.push('School name is required');
                    isValid = false;
                }
            } else if (privateType.value === 'Non-ESC') {
                const nonEscSchoolName = document.getElementById('non_esc_school_name');
                if (nonEscSchoolName && !nonEscSchoolName.value.trim()) {
                    errors.push('School name is required');
                    isValid = false;
                }
            }
        }
    }
    
    // Display errors if any
    if (!isValid) {
        alert('Please fix the following errors:\n\n' + errors.join('\n'));
    }
    
    return isValid;
}

/**
 * Format phone number input
 */
export function formatPhoneNumber(input) {
    let value = input.value.replace(/\D/g, '');
    if (value.length > 11) {
        value = value.substring(0, 11);
    }
    input.value = value;
}

/**
 * Initialize phone number formatting
 */
export function initializePhoneFormatting() {
    const phoneInput = document.querySelector('input[name="phone"]');
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            formatPhoneNumber(this);
        });
    }
}

/**
 * Initialize LRN digit-only validation
 */
export function initializeLRNValidation() {
    const lrnInput = document.getElementById('lrn');
    if (lrnInput) {
        lrnInput.addEventListener('input', function(e) {
            // Remove any non-digit characters
            const cursorPosition = this.selectionStart;
            const originalValue = this.value;
            this.value = this.value.replace(/\D/g, '');
            
            // Restore cursor position if it was moved due to character removal
            if (this.value.length < originalValue.length) {
                this.setSelectionRange(cursorPosition - 1, cursorPosition - 1);
            } else {
                this.setSelectionRange(cursorPosition, cursorPosition);
            }
        });

        // Handle paste events
        lrnInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const digitsOnly = pastedText.replace(/\D/g, '');
            const cursorPosition = this.selectionStart;
            const beforeCursor = this.value.substring(0, cursorPosition);
            const afterCursor = this.value.substring(this.selectionEnd);
            this.value = beforeCursor + digitsOnly + afterCursor;
            this.setSelectionRange(cursorPosition + digitsOnly.length, cursorPosition + digitsOnly.length);
        });
    }
}
