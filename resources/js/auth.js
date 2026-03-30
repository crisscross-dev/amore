/**
 * Auth Pages JavaScript
 * Handles password visibility toggle and form interactions
 */

console.log('AUTH.JS IS LOADED');

document.addEventListener('DOMContentLoaded', function () {

    /* ================================
       PASSWORD VISIBILITY (SYNCED)
    ================================= */
    const togglePassword = document.getElementById('togglePassword');
    const togglePasswordIcon = document.getElementById('togglePasswordIcon');
    const passwordField = document.getElementById('password');
    const passwordConfirmField = document.getElementById('password_confirmation');
    const nameFields = Array.from(
        document.querySelectorAll('input[name="first_name"], input[name="middle_name"], input[name="last_name"]'),
    );
    const contactNumberField = document.querySelector('input[name="contact_number"]');

    if (togglePassword && passwordField && togglePasswordIcon) {
        togglePassword.addEventListener('click', function () {
            const isHidden = passwordField.type === 'password';
            const newType = isHidden ? 'text' : 'password';

            // Toggle BOTH fields
            passwordField.type = newType;
            if (passwordConfirmField) {
                passwordConfirmField.type = newType;
            }

            // Toggle icon
            togglePasswordIcon.classList.toggle('bi-eye', !isHidden);
            togglePasswordIcon.classList.toggle('bi-eye-slash', isHidden);
        });
    }

    /* ================================
       AUTO-DISMISS ALERTS
    ================================= */
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            const closeButton = alert.querySelector('.btn-close');
            if (closeButton) closeButton.click();
        }, 5000);
    });

    /* ================================
       FORM SUBMIT LOADING STATE
    ================================= */
    const forms = document.querySelectorAll('form');
    forms.forEach(function (form) {
        form.addEventListener('submit', function () {
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                const originalText = submitButton.textContent;
                submitButton.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

                setTimeout(function () {
                    submitButton.disabled = false;
                    submitButton.textContent = originalText;
                }, 3000);
            }
        });
    });

    /* ================================
       EMAIL VALIDATION
    ================================= */
    const emailField = document.getElementById('email');
    if (emailField) {
        emailField.addEventListener('blur', function () {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (this.value && !emailRegex.test(this.value)) {
                this.classList.add('is-invalid');

                if (
                    !this.nextElementSibling ||
                    !this.nextElementSibling.classList.contains('invalid-feedback')
                ) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    errorDiv.textContent = 'Please enter a valid email address.';
                    this.parentNode.appendChild(errorDiv);
                }
            } else {
                this.classList.remove('is-invalid');
                const errorDiv = this.nextElementSibling;
                if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
                    errorDiv.remove();
                }
            }
        });
    }

    /* ================================
       NAME VALIDATION (NO NUMBERS)
    ================================= */
    if (nameFields.length) {
        nameFields.forEach(function (field) {
            field.addEventListener('keydown', function (event) {
                if (
                    event.ctrlKey ||
                    event.metaKey ||
                    [
                        'Backspace',
                        'Delete',
                        'Tab',
                        'ArrowLeft',
                        'ArrowRight',
                        'Home',
                        'End',
                    ].includes(event.key)
                ) {
                    return;
                }

                if (/^[0-9]$/.test(event.key)) {
                    event.preventDefault();
                }
            });

            field.addEventListener('input', function () {
                const sanitizedValue = String(field.value || '').replace(/[0-9]/g, '');
                if (field.value !== sanitizedValue) {
                    field.value = sanitizedValue;
                }
            });

            field.addEventListener('paste', function () {
                setTimeout(function () {
                    field.value = String(field.value || '').replace(/[0-9]/g, '');
                }, 0);
            });
        });
    }

    /* ================================
       CONTACT NUMBER VALIDATION
    ================================= */
    if (contactNumberField) {
        const validateContactNumber = function () {
            const digits = String(contactNumberField.value || '')
                .replace(/\D/g, '')
                .slice(0, 11);

            if (contactNumberField.value !== digits) {
                contactNumberField.value = digits;
            }

            if (digits.length === 0) {
                contactNumberField.setCustomValidity('');
                return;
            }

            if (digits.length !== 11) {
                contactNumberField.setCustomValidity('Contact number must be exactly 11 digits.');
                return;
            }

            contactNumberField.setCustomValidity('');
        };

        contactNumberField.addEventListener('keydown', function (event) {
            if (
                event.ctrlKey ||
                event.metaKey ||
                [
                    'Backspace',
                    'Delete',
                    'Tab',
                    'ArrowLeft',
                    'ArrowRight',
                    'Home',
                    'End',
                ].includes(event.key)
            ) {
                return;
            }

            if (['e', 'E', '+', '-', '.'].includes(event.key)) {
                event.preventDefault();
                return;
            }

            if (!/^[0-9]$/.test(event.key)) {
                event.preventDefault();
                return;
            }

            const hasSelection =
                typeof contactNumberField.selectionStart === 'number' &&
                typeof contactNumberField.selectionEnd === 'number' &&
                contactNumberField.selectionEnd > contactNumberField.selectionStart;

            if (!hasSelection && String(contactNumberField.value || '').length >= 11) {
                event.preventDefault();
            }
        });

        contactNumberField.addEventListener('input', validateContactNumber);
        contactNumberField.addEventListener('blur', function () {
            validateContactNumber();
            contactNumberField.reportValidity();
        });
        contactNumberField.addEventListener('paste', function () {
            setTimeout(validateContactNumber, 0);
        });
    }

    /* ================================
       PASSWORD MATCH VALIDATION
    ================================= */
    if (passwordField && passwordConfirmField) {
        passwordConfirmField.addEventListener('input', function () {
            if (this.value !== passwordField.value) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    }

    /* ================================
       PASSWORD STRENGTH METER
    ================================= */
    const strengthBar = document.getElementById('passwordStrength');
    const strengthText = document.getElementById('strengthText');

    if (passwordField && strengthBar && strengthText) {
        passwordField.addEventListener('input', function () {
            const val = passwordField.value;
            let strength = 0;

            if (val.length >= 6) strength += 20;
            if (val.length >= 8) strength += 10;
            if (/[A-Z]/.test(val)) strength += 20;
            if (/[a-z]/.test(val)) strength += 20;
            if (/[0-9]/.test(val)) strength += 20;
            if (/[^A-Za-z0-9]/.test(val)) strength += 10;

            strengthBar.style.width = strength + '%';

            if (strength < 40) {
                strengthBar.className = 'progress-bar bg-danger';
                strengthText.className = 'fw-bold text-danger';
                strengthText.textContent = 'Weak';
            } else if (strength < 70) {
                strengthBar.className = 'progress-bar bg-warning';
                strengthText.className = 'fw-bold text-warning';
                strengthText.textContent = 'Moderate';
            } else {
                strengthBar.className = 'progress-bar bg-success';
                strengthText.className = 'fw-bold text-success';
                strengthText.textContent = 'Strong';
            }
        });
    }
});
