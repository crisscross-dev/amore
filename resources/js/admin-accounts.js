// Admin Accounts Management JavaScript
document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin Accounts JS loaded');

    // Initialize Bootstrap tabs
    const triggerTabList = [].slice.call(document.querySelectorAll('#accountTabs button'));
    triggerTabList.forEach(function (triggerEl) {
        const tabTrigger = new bootstrap.Tab(triggerEl);

        triggerEl.addEventListener('click', function (event) {
            event.preventDefault();
            tabTrigger.show();
        });
    });

    // Handle action button clicks
    document.addEventListener('click', function(e) {
        // View Details buttons
        if (e.target.closest('.btn-outline-primary')) {
            e.preventDefault();
            const button = e.target.closest('.btn-outline-primary');
            const row = button.closest('tr');
            const accountId = row.cells[0].textContent.trim();

            // TODO: Implement view details modal or redirect
            alert('View details for account: ' + accountId);
        }

        // Edit buttons
        if (e.target.closest('.btn-outline-warning')) {
            e.preventDefault();
            const button = e.target.closest('.btn-outline-warning');
            const row = button.closest('tr');
            const accountId = row.cells[0].textContent.trim();

            // TODO: Implement edit modal or redirect
            alert('Edit account: ' + accountId);
        }

    // Deactivate/Reactivate buttons
    if (e.target.closest('.btn-outline-danger') || e.target.closest('.btn-outline-success')) {
            e.preventDefault();
            const button = e.target.closest('.btn-outline-danger, .btn-outline-success');
            const row = button.closest('tr');
            const accountId = row.cells[0].textContent.trim();
            const isActive = button.classList.contains('btn-outline-danger');

            const action = isActive ? 'deactivate' : 'reactivate';
            const confirmMessage = `Are you sure you want to ${action} this account?`;

            if (confirm(confirmMessage)) {
                // TODO: Implement account status change
                alert(`${action.charAt(0).toUpperCase() + action.slice(1)} account: ${accountId}`);
            }
        }

        // Approve/Reject pending accounts (for approval tab)
        if (e.target.closest('form[action*="/admin/accounts/"][method="POST"] button') || e.target.closest('form[action*="/admin/accounts/"][method="PATCH"] button')) {
            // We rely on the form submit; add a confirmation step for destructive actions
            const button = e.target.closest('button');
            if (!button) return;
            const form = button.closest('form');
            const isReject = button.classList.contains('btn-danger');
            const message = isReject ? 'Are you sure you want to reject this account?' : 'Are you sure you want to approve this account?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        }
    });

    // Handle approve button clicks to show modal
    document.querySelectorAll('.approve-btn').forEach(button => {
        button.addEventListener('click', function() {
            // Get data from button attributes
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;
            const userEmail = this.dataset.userEmail;
            const accountType = this.dataset.accountType;
            const gradeLevel = this.dataset.gradeLevel;
            const lrn = this.dataset.lrn;
            const department = this.dataset.department;
            const registered = this.dataset.registered;

            // Populate modal with user data
            document.getElementById('modalUserName').textContent = userName;
            document.getElementById('modalUserEmail').textContent = userEmail;
            document.getElementById('modalAccountType').textContent = accountType.charAt(0).toUpperCase() + accountType.slice(1);
            document.getElementById('modalAccountType').className = accountType === 'student' ? 'badge bg-primary' : 'badge bg-info';
            document.getElementById('modalRegisteredDate').textContent = registered;

            // Show/hide conditional fields based on account type
            const studentFields = document.querySelectorAll('.student-field');
            const facultyFields = document.querySelectorAll('.faculty-field');

            if (accountType === 'student') {
                studentFields.forEach(field => field.style.display = '');
                facultyFields.forEach(field => field.style.display = 'none');
                document.getElementById('modalGradeLevel').textContent = gradeLevel || 'Not set';
                document.getElementById('modalLRN').textContent = lrn || 'Not set';
            } else {
                studentFields.forEach(field => field.style.display = 'none');
                facultyFields.forEach(field => field.style.display = '');
                document.getElementById('modalDepartment').textContent = department || 'Not set';
            }

            // Set form action with user ID
            const form = document.getElementById('approveAccountForm');
            form.action = `/admin/accounts/${userId}/approve`;

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('approveAccountModal'));
            modal.show();
        });
    });

    // Handle approve form submission
    const approveForm = document.getElementById('approveAccountForm');
    if (approveForm) {
        approveForm.addEventListener('submit', function(e) {
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Approving...';
        });
    }

    // Handle send email checkbox change
    const sendEmailCheckbox = document.getElementById('sendEmail');
    const sendEmailInput = document.getElementById('sendEmailInput');
    
    if (sendEmailCheckbox && sendEmailInput) {
        sendEmailCheckbox.addEventListener('change', function() {
            // Update hidden input value based on checkbox state
            sendEmailInput.value = this.checked ? '1' : '0';
        });
    }

    // Add search functionality (if needed in future)
    // const searchInput = document.getElementById('accountSearch');
    // if (searchInput) {
    //     searchInput.addEventListener('input', function() {
    //         // TODO: Implement search functionality
    //     });
    // }

    // Add status filter functionality (if needed in future)
    // const statusFilter = document.getElementById('statusFilter');
    // if (statusFilter) {
    //     statusFilter.addEventListener('change', function() {
    //         // TODO: Implement status filtering
    //     });
    // }
});