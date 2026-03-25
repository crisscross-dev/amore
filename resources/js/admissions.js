/**
 * Admissions Management JavaScript
 * Handles bulk selection, filters, and approval/rejection actions
 */

import { Alert, Modal } from "bootstrap";

class AdmissionsManager {
    constructor() {
        this.selectedAdmissions = new Set();
        this.init();
    }

    init() {
        this.setupBulkSelection();
        this.setupBulkActions();
        this.setupFilters();
        this.setupRowPreviewModals();
    }

    /**
     * Open admission modal when a table row is clicked/double-clicked
     */
    setupRowPreviewModals() {
        const openModalFromEvent = (event) => {
            const target = event.target;
            if (!target) {
                return;
            }

            if (
                target.closest(
                    'input, button, a, label, textarea, select, [data-bs-toggle="modal"]',
                )
            ) {
                return;
            }

            const row = target.closest(
                ".admissions-dashboard__table-row[data-modal-target]",
            );
            if (!row) {
                return;
            }

            const modalId = row.dataset.modalTarget;
            const modalElement = modalId
                ? document.getElementById(modalId)
                : null;
            if (!modalElement) {
                return;
            }

            Modal.getOrCreateInstance(modalElement).show();
        };

        // Delegated listener is more reliable after cached/partial DOM updates on hosted setups.
        document.addEventListener("dblclick", openModalFromEvent);
    }

    /**
     * Setup bulk selection functionality
     */
    setupBulkSelection() {
        // Select all checkbox
        const selectAllCheckbox = document.getElementById("selectAllCheckbox");
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener("change", (e) => {
                const checkboxes =
                    document.querySelectorAll(".admission-select");
                checkboxes.forEach((checkbox) => {
                    checkbox.checked = e.target.checked;
                    this.toggleSelection(checkbox);
                });
            });
        }

        // Individual checkboxes
        const checkboxes = document.querySelectorAll(".admission-select");
        checkboxes.forEach((checkbox) => {
            checkbox.addEventListener("change", () => {
                this.toggleSelection(checkbox);
                this.updateSelectAllCheckbox();
            });
        });
    }

    /**
     * Toggle selection of an admission
     */
    toggleSelection(checkbox) {
        const type = checkbox.dataset.type;
        const id = checkbox.dataset.id;
        const key = `${type}-${id}`;

        if (checkbox.checked) {
            this.selectedAdmissions.add(key);
        } else {
            this.selectedAdmissions.delete(key);
        }

        this.updateBulkActionsBar();
    }

    /**
     * Update the select all checkbox state
     */
    updateSelectAllCheckbox() {
        const selectAllCheckbox = document.getElementById("selectAllCheckbox");
        const checkboxes = document.querySelectorAll(".admission-select");

        if (selectAllCheckbox && checkboxes.length > 0) {
            const checkedCount = Array.from(checkboxes).filter(
                (cb) => cb.checked,
            ).length;
            selectAllCheckbox.checked = checkedCount === checkboxes.length;
            selectAllCheckbox.indeterminate =
                checkedCount > 0 && checkedCount < checkboxes.length;
        }
    }

    /**
     * Update bulk actions bar visibility and count
     */
    updateBulkActionsBar() {
        const bulkActionsBar = document.getElementById("bulkActionsBar");
        const selectedCount = document.getElementById("selectedCount");

        if (bulkActionsBar && selectedCount) {
            const count = this.selectedAdmissions.size;
            selectedCount.textContent = count;
            bulkActionsBar.style.display = count > 0 ? "block" : "none";
        }
    }

    /**
     * Setup bulk action buttons
     */
    setupBulkActions() {
        // Approve button
        const bulkApproveBtn = document.getElementById("bulkApproveBtn");
        if (bulkApproveBtn) {
            bulkApproveBtn.addEventListener("click", () => {
                this.showBulkApproveModal();
            });
        }

        // Reject button
        const bulkRejectBtn = document.getElementById("bulkRejectBtn");
        if (bulkRejectBtn) {
            bulkRejectBtn.addEventListener("click", () => {
                this.showBulkRejectModal();
            });
        }

        // Clear selection button
        const clearSelectionBtn = document.getElementById("clearSelectionBtn");
        if (clearSelectionBtn) {
            clearSelectionBtn.addEventListener("click", () => {
                this.clearSelection();
            });
        }
    }

    /**
     * Show bulk approve modal
     */
    showBulkApproveModal() {
        const modal = new Modal(document.getElementById("bulkApproveModal"));
        const count = document.getElementById("bulkApproveCount");
        const input = document.getElementById("bulkApproveAdmissionsInput");

        if (count) {
            count.textContent = this.selectedAdmissions.size;
        }

        if (input) {
            input.innerHTML = "";
            this.selectedAdmissions.forEach((key) => {
                const [type, id] = key.split("-");
                input.innerHTML += `
                    <input type="hidden" name="admissions[][type]" value="${type}">
                    <input type="hidden" name="admissions[][id]" value="${id}">
                `;
            });
        }

        modal.show();
    }

    /**
     * Show bulk reject modal
     */
    showBulkRejectModal() {
        const modal = new Modal(document.getElementById("bulkRejectModal"));
        const count = document.getElementById("bulkRejectCount");
        const input = document.getElementById("bulkAdmissionsInput");

        if (count) {
            count.textContent = this.selectedAdmissions.size;
        }

        if (input) {
            input.innerHTML = "";
            this.selectedAdmissions.forEach((key) => {
                const [type, id] = key.split("-");
                input.innerHTML += `
                    <input type="hidden" name="admissions[][type]" value="${type}">
                    <input type="hidden" name="admissions[][id]" value="${id}">
                `;
            });
        }

        modal.show();
    }

    /**
     * Clear all selections
     */
    clearSelection() {
        this.selectedAdmissions.clear();

        const checkboxes = document.querySelectorAll(".admission-select");
        checkboxes.forEach((checkbox) => {
            checkbox.checked = false;
        });

        const selectAllCheckbox = document.getElementById("selectAllCheckbox");
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        }

        this.updateBulkActionsBar();
    }

    /**
     * Setup filter form
     */
    setupFilters() {
        const filterForm = document.getElementById("filterForm");
        if (filterForm) {
            const typeFilter = document.getElementById("typeFilter");
            const statusFilter = document.getElementById("statusFilter");

            if (typeFilter) {
                typeFilter.addEventListener("change", () => {
                    filterForm.submit();
                });
            }

            if (statusFilter) {
                statusFilter.addEventListener("change", () => {
                    // Optionally auto-submit
                    // filterForm.submit();
                });
            }
        }
    }
}

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
    const admissionsManager = new AdmissionsManager();

    // Make it globally accessible if needed
    window.admissionsManager = admissionsManager;

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll(".alert");
    alerts.forEach((alert) => {
        setTimeout(() => {
            const bsAlert = new Alert(alert);
            if (bsAlert) {
                bsAlert.close();
            }
        }, 5000);
    });
});
