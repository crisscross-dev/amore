/**
 * JHS Admission Form JavaScript
 */

import { initializeCommonFormLogic, initializePhoneFormatting } from './common.js';

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('JHS Admission Form initialized');
    
    // Initialize common form logic
    initializeCommonFormLogic();
    
    // Initialize phone formatting
    initializePhoneFormatting();
    
    // JHS-specific initialization
    initializeJHSForm();
});

/**
 * Initialize JHS-specific form functionality
 */
function initializeJHSForm() {
    // Add any JHS-specific logic here
    console.log('JHS-specific form logic initialized');
    
    // Initialize school type logic
    initializeSchoolTypeLogic();
    
    // Example: Add grade level validation or specific field handling
    setupFormAnimations();
}

/**
 * Setup form animations
 */
function setupFormAnimations() {
    const formSections = document.querySelectorAll('.form-section');
    
    formSections.forEach((section, index) => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            section.style.transition = 'all 0.5s ease';
            section.style.opacity = '1';
            section.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

/**
 * Initialize school type logic (Public/Private, ESC/Non-ESC)
 */
function initializeSchoolTypeLogic() {
    const publicRadio = document.getElementById('school_type_public');
    const privateRadio = document.getElementById('school_type_private');
    const escRadio = document.getElementById('private_type_esc');
    const nonEscRadio = document.getElementById('private_type_non_esc');
    
    const publicSchool = document.getElementById('publicSchool');
    const privateOptions = document.getElementById('privateOptions');
    const escFields = document.getElementById('escFields');
    const nonEscField = document.getElementById('nonEscField');
    
    // Handle Public/Private school type selection
    publicRadio?.addEventListener('change', function() {
        if (this.checked) {
            publicSchool?.classList.remove('hidden');
            privateOptions?.classList.add('hidden');
            escFields?.classList.add('hidden');
            nonEscField?.classList.add('hidden');
        }
    });
    
    privateRadio?.addEventListener('change', function() {
        if (this.checked) {
            publicSchool?.classList.add('hidden');
            privateOptions?.classList.remove('hidden');
        }
    });
    
    // Handle ESC/Non-ESC selection
    escRadio?.addEventListener('change', function() {
        if (this.checked) {
            escFields?.classList.remove('hidden');
            nonEscField?.classList.add('hidden');
        }
    });
    
    nonEscRadio?.addEventListener('change', function() {
        if (this.checked) {
            nonEscField?.classList.remove('hidden');
            escFields?.classList.add('hidden');
        }
    });
}
