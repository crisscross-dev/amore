/**
 * SHS Admission Form JavaScript
 */

import {
    initializeCommonFormLogic,
    initializePhoneFormatting,
} from "./common.js";

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
    console.log("SHS Admission Form initialized");

    // Initialize common form logic
    initializeCommonFormLogic();

    // Initialize phone formatting
    initializePhoneFormatting();

    // SHS-specific initialization
    initializeSHSForm();

    // Initialize strand selection
    initializeStrandSelection();
});

/**
 * Initialize SHS-specific form functionality
 */
function initializeSHSForm() {
    // Add any SHS-specific logic here
    console.log("SHS-specific form logic initialized");

    // Initialize school type logic
    initializeSchoolTypeLogic();

    // Initialize QVR logic
    initializeQVRLogic();

    // Keep age policy validation while using global flatpickr on the date input
    initializeMinimumAgeValidation();

    // Enable guardian quick-fill for emergency contact
    initializeGuardianEmergencySync();

    // Example: Add track/strand selection if needed
    setupFormAnimations();
}

function initializeMinimumAgeValidation() {
    const dobInput = document.getElementById("date_of_birth");
    const ageError = document.getElementById("age-error");
    const minAge = 15;

    if (!dobInput) {
        return;
    }

    const validateMinimumAge = () => {
        if (!dobInput.value) {
            if (ageError) {
                ageError.style.display = "none";
            }
            dobInput.setCustomValidity("");
            return;
        }

        const birthDate = new Date(dobInput.value);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();

        if (
            monthDiff < 0 ||
            (monthDiff === 0 && today.getDate() < birthDate.getDate())
        ) {
            age--;
        }

        if (age < minAge) {
            if (ageError) {
                ageError.textContent = `The student must be at least ${minAge} years old to enroll in Senior High School.`;
                ageError.style.display = "block";
            }
            dobInput.setCustomValidity("Age requirement not met");
        } else {
            if (ageError) {
                ageError.style.display = "none";
            }
            dobInput.setCustomValidity("");
        }
    };

    dobInput.addEventListener("change", validateMinimumAge);
    validateMinimumAge();
}

function initializeGuardianEmergencySync() {
    const motherCheckbox = document.getElementById("useMotherAsEmergency");
    const fatherCheckbox = document.getElementById("useFatherAsEmergency");
    const motherNameInput = document.querySelector('input[name="mother_name"]');
    const fatherNameInput = document.querySelector('input[name="father_name"]');
    const emergencyNameInput = document.querySelector(
        'input[name="emergency_contact_name"]',
    );
    const emergencyRelationshipInput = document.querySelector(
        'input[name="emergency_contact_relationship"]',
    );

    if (
        !motherCheckbox ||
        !fatherCheckbox ||
        !motherNameInput ||
        !fatherNameInput ||
        !emergencyNameInput ||
        !emergencyRelationshipInput
    ) {
        return;
    }

    const applySource = (source) => {
        if (source === "mother") {
            emergencyNameInput.value = (motherNameInput.value || "").trim();
            emergencyRelationshipInput.value = "Mother";
            motherCheckbox.checked = true;
            fatherCheckbox.checked = false;
            return;
        }

        if (source === "father") {
            emergencyNameInput.value = (fatherNameInput.value || "").trim();
            emergencyRelationshipInput.value = "Father";
            fatherCheckbox.checked = true;
            motherCheckbox.checked = false;
        }
    };

    const clearIfOwnedBy = (source) => {
        const relation = (emergencyRelationshipInput.value || "")
            .trim()
            .toLowerCase();
        if (
            (source === "mother" && relation === "mother") ||
            (source === "father" && relation === "father")
        ) {
            emergencyNameInput.value = "";
            emergencyRelationshipInput.value = "";
        }
    };

    motherCheckbox.addEventListener("change", () => {
        if (motherCheckbox.checked) {
            applySource("mother");
        } else {
            clearIfOwnedBy("mother");
        }
    });

    fatherCheckbox.addEventListener("change", () => {
        if (fatherCheckbox.checked) {
            applySource("father");
        } else {
            clearIfOwnedBy("father");
        }
    });

    motherNameInput.addEventListener("input", () => {
        if (motherCheckbox.checked) {
            emergencyNameInput.value = (motherNameInput.value || "").trim();
        }
    });

    fatherNameInput.addEventListener("input", () => {
        if (fatherCheckbox.checked) {
            emergencyNameInput.value = (fatherNameInput.value || "").trim();
        }
    });

    const initialRelation = (emergencyRelationshipInput.value || "")
        .trim()
        .toLowerCase();
    if (initialRelation === "mother") {
        applySource("mother");
    } else if (initialRelation === "father") {
        applySource("father");
    }
}

/**
 * Initialize strand selection logic
 */
function initializeStrandSelection() {
    const strandSelect = document.getElementById("strand");
    const tvlSpecializationDiv = document.getElementById("tvlSpecialization");
    const tvlSpecializationSelect =
        document.getElementById("tvl_specialization");

    if (strandSelect && tvlSpecializationDiv) {
        // Listen for strand changes
        strandSelect.addEventListener("change", function () {
            handleStrandChange(this.value);
        });

        // Initialize on page load if TVL is already selected
        if (strandSelect.value === "TVL") {
            handleStrandChange("TVL");
        }
    }
}

/**
 * Handle strand selection changes
 */
function handleStrandChange(strand) {
    const tvlSpecializationDiv = document.getElementById("tvlSpecialization");
    const tvlSpecializationSelect =
        document.getElementById("tvl_specialization");

    if (strand === "TVL") {
        // Show TVL specialization options
        tvlSpecializationDiv?.classList.remove("hidden");
        if (tvlSpecializationSelect) {
            tvlSpecializationSelect.required = true;
        }
    } else {
        // Hide TVL specialization options
        tvlSpecializationDiv?.classList.add("hidden");
        if (tvlSpecializationSelect) {
            tvlSpecializationSelect.required = false;
            tvlSpecializationSelect.value = "";
        }
    }
}

/**
 * Setup form animations
 */
function setupFormAnimations() {
    const formSections = document.querySelectorAll(".form-section");

    formSections.forEach((section, index) => {
        section.style.opacity = "0";
        section.style.transform = "translateY(20px)";

        setTimeout(() => {
            section.style.transition = "all 0.5s ease";
            section.style.opacity = "1";
            section.style.transform = "translateY(0)";
        }, index * 100);
    });
}

/**
 * Initialize school type logic (Public/Private, ESC/Non-ESC)
 */
function initializeSchoolTypeLogic() {
    const publicRadio = document.getElementById("school_type_public");
    const privateRadio = document.getElementById("school_type_private");
    const escRadio = document.getElementById("private_type_esc");
    const nonEscRadio = document.getElementById("private_type_non_esc");

    const publicSchool = document.getElementById("publicSchool");
    const privateOptions = document.getElementById("privateOptions");
    const escFields = document.getElementById("escFields");
    const nonEscField = document.getElementById("nonEscField");

    // Handle Public/Private school type selection
    publicRadio?.addEventListener("change", function () {
        if (this.checked) {
            publicSchool?.classList.remove("hidden");
            privateOptions?.classList.add("hidden");
            escFields?.classList.add("hidden");
            nonEscField?.classList.add("hidden");
        }
    });

    privateRadio?.addEventListener("change", function () {
        if (this.checked) {
            publicSchool?.classList.add("hidden");
            privateOptions?.classList.remove("hidden");
        }
    });

    // Handle ESC/Non-ESC selection
    escRadio?.addEventListener("change", function () {
        if (this.checked) {
            escFields?.classList.remove("hidden");
            nonEscField?.classList.add("hidden");
            // Clear QVR fields when switching to ESC
            const qvrApplicant = document.getElementById("qvr_applicant");
            const qvrNumber = document.getElementById("qvr_number");
            const qvrNumberField = document.getElementById("qvrNumberField");
            if (qvrApplicant) qvrApplicant.value = "";
            if (qvrNumber) qvrNumber.value = "";
            qvrNumberField?.classList.add("hidden");
        }
    });

    nonEscRadio?.addEventListener("change", function () {
        if (this.checked) {
            nonEscField?.classList.remove("hidden");
            escFields?.classList.add("hidden");
        }
    });
}

/**
 * Initialize QVR (Qualified Voucher Recipient) logic
 */
function initializeQVRLogic() {
    const qvrApplicant = document.getElementById("qvr_applicant");
    const qvrNumberField = document.getElementById("qvrNumberField");
    const qvrNumber = document.getElementById("qvr_number");

    // Show/hide QVR Number field based on QVR Applicant selection
    qvrApplicant?.addEventListener("change", function () {
        if (this.value === "Yes") {
            qvrNumberField?.classList.remove("hidden");
        } else {
            qvrNumberField?.classList.add("hidden");
            if (qvrNumber) qvrNumber.value = "";
        }
    });

    // Auto-format QVR Number as XXX-XXXXXXXX
    qvrNumber?.addEventListener("input", function () {
        let val = this.value;

        // Remove everything except letters/numbers
        val = val.replace(/[^A-Za-z0-9]/g, "");

        // If user typed/pasted more than 3 characters, insert dash after the first 3
        if (val.length > 3) {
            val = val.slice(0, 3) + "-" + val.slice(3, 11); // max 11 chars after dash
        }

        // Limit total length to 12
        this.value = val.slice(0, 12);
    });
}
