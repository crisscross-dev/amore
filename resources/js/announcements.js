/**
 * Announcements JavaScript
 * Handles interactions and enhancements for announcement pages
 */

class AnnouncementsManager {
    constructor() {
        this.liveContainer = document.querySelector('.announcement-live-page');
        this.liveUrl = this.liveContainer?.dataset.liveUrl || null;
        this.liveSignature = this.liveContainer?.dataset.liveSignature || '';
        this.livePollIntervalMs = 10000;
        this.livePollTimer = null;
        this.liveRequestInFlight = false;

        this.init();
    }

    init() {
        console.log('Announcements Manager initialized');
        
        // Initialize features
        this.initTinyMCE();
        this.handleFormValidation();
        this.handleFilePreview();
        this.handleConfirmations();
        this.handlePrioritySelection();
        this.handleCharacterCount();
        this.initTooltips();
        this.initLiveUpdates();
    }

    /**
     * Initialize TinyMCE Rich Text Editor
     */
    initTinyMCE() {
        // Check if TinyMCE is loaded
        if (typeof tinymce === 'undefined') {
            console.warn('TinyMCE not loaded');
            return;
        }

        const contentTextarea = document.getElementById('content');
        
        if (contentTextarea) {
            // Remove required attribute as TinyMCE will handle validation
            contentTextarea.removeAttribute('required');
            
            tinymce.init({
                selector: '#content',
                base_url: '/vendor/tinymce',
                suffix: '.min',
                license_key: 'gpl',
                height: 400,
                menubar: false,
                
                // Plugins
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount'
                ],
                
                // Toolbar configuration
                toolbar: 'undo redo | formatselect | bold italic underline strikethrough | ' +
                        'alignleft aligncenter alignright alignjustify | ' +
                        'bullist numlist outdent indent | link image media | ' +
                        'removeformat code fullscreen | help',
                
                // Content styling to match dashboard theme
                content_style: `
                    body {
                        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                        font-size: 14px;
                        line-height: 1.6;
                        color: #2d3748;
                        padding: 15px;
                    }
                    a {
                        color: #198754;
                    }
                    a:hover {
                        color: #146c43;
                    }
                `,
                
                // Image upload settings
                images_upload_url: '/upload-editor-image',
                automatic_uploads: true,
                images_reuse_filename: true,
                
                // File picker type
                file_picker_types: 'image',
                
                // Additional settings
                branding: false,
                promotion: false,
                statusbar: true,
                elementpath: false,
                resize: true,
                
                // Mobile configuration
                mobile: {
                    menubar: false,
                    toolbar_mode: 'sliding'
                },
                
                // Success callback
                setup: function(editor) {
                    editor.on('init', function() {
                        console.log('TinyMCE editor initialized successfully');
                    });
                    
                    // Add custom button color
                    editor.on('init', function() {
                        const toolbar = editor.getContainer().querySelector('.tox-toolbar');
                        if (toolbar) {
                            toolbar.style.borderBottom = '2px solid #198754';
                        }
                    });
                    
                    // Sync content before form submission
                    editor.on('submit', function() {
                        editor.save();
                    });
                }
            });
        }
    }

    /**
     * Form Validation
     */
    handleFormValidation() {
        const forms = document.querySelectorAll('form[method="POST"]');
        
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                // Sync TinyMCE content before validation
                if (typeof tinymce !== 'undefined') {
                    tinymce.triggerSave();
                }
                
                // Custom validation for TinyMCE content
                const contentTextarea = document.getElementById('content');
                if (contentTextarea && contentTextarea.value.trim() === '') {
                    e.preventDefault();
                    e.stopPropagation();
                    alert('Please enter announcement content.');
                    return false;
                }
                
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });
    }

    /**
     * File Upload Preview
     */
    handleFilePreview() {
        const fileInput = document.getElementById('attachments');
        
        if (fileInput) {
            fileInput.addEventListener('change', (e) => {
                const files = e.target.files;
                let previewHTML = '';
                
                if (files.length > 0) {
                    previewHTML = '<div class="alert alert-info mt-2"><strong>Selected Files:</strong><ul class="mb-0 mt-2">';
                    
                    Array.from(files).forEach(file => {
                        const fileSize = (file.size / 1024).toFixed(2);
                        previewHTML += `<li>${file.name} (${fileSize} KB)</li>`;
                    });
                    
                    previewHTML += '</ul></div>';
                }
                
                // Remove previous preview
                const existingPreview = fileInput.parentElement.querySelector('.alert-info');
                if (existingPreview) {
                    existingPreview.remove();
                }
                
                // Add new preview
                if (previewHTML) {
                    fileInput.insertAdjacentHTML('afterend', previewHTML);
                }
            });
        }
    }

    /**
     * Confirmation Dialogs
     */
    handleConfirmations() {
        // Delete confirmations
        const deleteButtons = document.querySelectorAll('[data-bs-toggle="modal"]');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                const announcement = button.closest('.announcement-card');
                if (announcement) {
                    announcement.classList.add('border-danger');
                    
                    // Remove highlight when modal closes
                    const modalId = button.getAttribute('data-bs-target');
                    const modal = document.querySelector(modalId);
                    
                    if (modal) {
                        modal.addEventListener('hidden.bs.modal', () => {
                            announcement.classList.remove('border-danger');
                        }, { once: true });
                    }
                }
            });
        });

        // Pin/Unpin confirmations (optional visual feedback)
        const pinForms = document.querySelectorAll('form[action*="/pin"]');
        
        pinForms.forEach(form => {
            form.addEventListener('submit', (e) => {
                const button = form.querySelector('button[type="submit"]');
                const icon = button.querySelector('i');
                
                // Add loading state
                button.disabled = true;
                icon.classList.add('fa-spin', 'fa-spinner');
                
                // Note: Form will submit and page will reload
                // This is just visual feedback before reload
            });
        });
    }

    /**
     * Priority Selection Visual Feedback
     */
    handlePrioritySelection() {
        const prioritySelect = document.getElementById('priority');
        
        if (prioritySelect) {
            const updatePriorityVisual = () => {
                const value = prioritySelect.value;
                const colors = {
                    'high': '#dc3545',
                    'medium': '#ffc107',
                    'low': '#198754'
                };
                
                prioritySelect.style.borderColor = colors[value] || '#ced4da';
                prioritySelect.style.borderWidth = '2px';
            };
            
            // Initial state
            updatePriorityVisual();
            
            // On change
            prioritySelect.addEventListener('change', updatePriorityVisual);
        }
    }

    /**
     * Character Count for Title
     */
    handleCharacterCount() {
        const titleInput = document.getElementById('title');
        
        if (titleInput) {
            const maxLength = titleInput.getAttribute('maxlength') || 255;
            
            // Create counter element
            const counter = document.createElement('small');
            counter.className = 'form-text text-muted';
            counter.style.float = 'right';
            titleInput.parentElement.appendChild(counter);
            
            const updateCounter = () => {
                const remaining = maxLength - titleInput.value.length;
                counter.textContent = `${remaining} characters remaining`;
                
                if (remaining < 20) {
                    counter.classList.remove('text-muted');
                    counter.classList.add('text-warning');
                }
                
                if (remaining < 10) {
                    counter.classList.remove('text-warning');
                    counter.classList.add('text-danger');
                }
                
                if (remaining >= 20) {
                    counter.classList.remove('text-warning', 'text-danger');
                    counter.classList.add('text-muted');
                }
            };
            
            // Initial count
            updateCounter();
            
            // On input
            titleInput.addEventListener('input', updateCounter);
        }
    }

    /**
     * Initialize Bootstrap Tooltips
     */
    initTooltips() {
        if (typeof bootstrap !== 'undefined') {
            const tooltipTriggerList = [].slice.call(
                document.querySelectorAll('[data-bs-toggle="tooltip"]')
            );
            
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    }

    initLiveUpdates() {
        if (!this.liveContainer || !this.liveUrl) {
            return;
        }

        this.startLivePolling();

        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                this.checkLiveSignature();
            }
        });

        window.addEventListener('beforeunload', () => {
            this.stopLivePolling();
        }, { once: true });
    }

    startLivePolling() {
        this.stopLivePolling();

        this.livePollTimer = window.setInterval(() => {
            if (!document.hidden) {
                this.checkLiveSignature();
            }
        }, this.livePollIntervalMs);
    }

    stopLivePolling() {
        if (this.livePollTimer) {
            clearInterval(this.livePollTimer);
            this.livePollTimer = null;
        }
    }

    buildLiveUrl() {
        const url = new URL(this.liveUrl, window.location.origin);
        const currentParams = new URLSearchParams(window.location.search);

        currentParams.forEach((value, key) => {
            url.searchParams.set(key, value);
        });

        return url;
    }

    async checkLiveSignature() {
        if (!this.liveUrl || this.liveRequestInFlight) {
            return;
        }

        this.liveRequestInFlight = true;

        try {
            const response = await fetch(this.buildLiveUrl(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                return;
            }

            const payload = await response.json();
            const nextSignature = payload?.signature || '';

            if (!nextSignature) {
                return;
            }

            if (!this.liveSignature) {
                this.liveSignature = nextSignature;
                this.liveContainer.dataset.liveSignature = nextSignature;
                return;
            }

            if (nextSignature !== this.liveSignature) {
                window.location.reload();
            }
        } catch (error) {
            console.debug('Announcement live polling skipped:', error);
        } finally {
            this.liveRequestInFlight = false;
        }
    }

    /**
     * Search Debouncing (for future AJAX implementation)
     */
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.announcementsManager = new AnnouncementsManager();
    });
} else {
    window.announcementsManager = new AnnouncementsManager();
}

// Auto-dismiss alerts after 5 seconds
document.addEventListener('DOMContentLoaded', () => {
    const alerts = document.querySelectorAll('.alert:not(.alert-warning)');
    
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});

// Export for module usage
export default AnnouncementsManager;
