/**
 * JHS Admission Form JavaScript
 */

import {
    initializeCommonFormLogic,
    initializePhoneFormatting,
} from "./common.js";

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
    console.log("JHS Admission Form initialized");

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
    console.log("JHS-specific form logic initialized");

    // Keep age policy validation while using global flatpickr on the date input
    initializeMinimumAgeValidation();
    initializeGuardianEmergencySync();

    // Force stable visible state without animation to reduce input lag
    const formSections = document.querySelectorAll(".form-section");
    formSections.forEach((section) => {
        section.style.transition = "none";
        section.style.opacity = "1";
        section.style.transform = "none";
    });
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

function initializeMinimumAgeValidation() {
    const dobInput = document.getElementById("date_of_birth");
    const ageError = document.getElementById("age-error");
    const minAge = 11;

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
                ageError.textContent = `The student must be at least ${minAge} years old to enroll in Junior High School.`;
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
