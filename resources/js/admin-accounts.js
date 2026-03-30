// Admin Accounts Management JavaScript
document.addEventListener("DOMContentLoaded", function () {
    console.log("Admin Accounts JS loaded");

    const liveContainer = document.querySelector(".accounts-live-page");
    const liveUrl = liveContainer ? (liveContainer.dataset.liveUrl || "") : "";
    let liveSignature = liveContainer
        ? (liveContainer.dataset.liveSignature || "")
        : "";
    let liveRequestInFlight = false;
    let livePollTimer = null;
    const livePollIntervalMs = 10000;

    const setTabInUrl = (tabId) => {
        const url = new URL(window.location.href);
        url.searchParams.set("tab", tabId);
        window.history.replaceState({}, "", url);
    };

    function askConfirmation(message, isDanger) {
        if (window.Swal) {
            return window.Swal.fire({
                icon: isDanger ? "warning" : "question",
                title: isDanger ? "Please confirm action" : "Confirm action",
                text: message,
                showCancelButton: true,
                confirmButtonText: "Yes, continue",
                cancelButtonText: "Cancel",
                confirmButtonColor: isDanger ? "#dc3545" : "#198754",
                cancelButtonColor: "#6c757d",
                reverseButtons: true,
                focusCancel: true,
            }).then((result) => result.isConfirmed);
        }

        if (
            window.AppSwal &&
            typeof window.AppSwal.confirmUpdateAdmin === "function"
        ) {
            return window.AppSwal.confirmUpdateAdmin().then(
                (result) => result.isConfirmed,
            );
        }

        return Promise.resolve(true);
    }

    // Initialize Bootstrap tabs
    const triggerTabList = [].slice.call(
        document.querySelectorAll("#accountTabs button"),
    );
    triggerTabList.forEach(function (triggerEl) {
        const tabTrigger = new bootstrap.Tab(triggerEl);

        triggerEl.addEventListener("click", function (event) {
            event.preventDefault();
            tabTrigger.show();
        });

        triggerEl.addEventListener("shown.bs.tab", function (event) {
            const targetSelector =
                event.target.getAttribute("data-bs-target") || "";
            const tabId = targetSelector.replace("#", "");
            if (tabId) {
                setTabInUrl(tabId);
            }
        });
    });

    // Handle action button clicks
    document.addEventListener("click", function (e) {
        // View Details buttons
        if (e.target.closest(".btn-outline-primary")) {
            e.preventDefault();
            const button = e.target.closest(".btn-outline-primary");
            const row = button.closest("tr");
            const accountId = row.cells[0].textContent.trim();

            // TODO: Implement view details modal or redirect
            alert("View details for account: " + accountId);
        }

        // Edit buttons
        if (e.target.closest(".btn-outline-warning")) {
            e.preventDefault();
            const button = e.target.closest(".btn-outline-warning");
            const row = button.closest("tr");
            const accountId = row.cells[0].textContent.trim();

            // TODO: Implement edit modal or redirect
            alert("Edit account: " + accountId);
        }

        // Deactivate/Reactivate buttons
        if (
            e.target.closest(".btn-outline-danger") ||
            e.target.closest(".btn-outline-success")
        ) {
            e.preventDefault();
            const button = e.target.closest(
                ".btn-outline-danger, .btn-outline-success",
            );
            const row = button.closest("tr");
            const accountId = row.cells[0].textContent.trim();
            const isActive = button.classList.contains("btn-outline-danger");

            const action = isActive ? "deactivate" : "reactivate";
            const confirmMessage = `Are you sure you want to ${action} this account?`;

            askConfirmation(confirmMessage, isActive).then((confirmed) => {
                if (!confirmed) return;
                // TODO: Implement account status change
                alert(
                    `${action.charAt(0).toUpperCase() + action.slice(1)} account: ${accountId}`,
                );
            });
        }

        // Approve/Reject pending accounts only (do not hijack modal cancel/edit buttons)
        const submitButton = e.target.closest('button[type="submit"]');
        if (submitButton) {
            const form = submitButton.closest(
                'form[action*="/admin/accounts/"]',
            );
            const action = (form && form.getAttribute("action")) || "";
            const isApprovalAction =
                /\/admin\/accounts\/\d+\/(approve|reject)$/i.test(action);

            if (!form || !isApprovalAction) {
                return;
            }

            const isReject = /\/reject$/i.test(action);
            const message = isReject
                ? "Are you sure you want to reject this account?"
                : "Are you sure you want to approve this account?";

            e.preventDefault();
            askConfirmation(message, isReject).then((confirmed) => {
                if (!confirmed) return;
                if (typeof form.requestSubmit === "function") {
                    form.requestSubmit(submitButton);
                } else {
                    form.submit();
                }
            });
        }
    });

    // Handle approve button clicks to show modal
    document.querySelectorAll(".approve-btn").forEach((button) => {
        button.addEventListener("click", function () {
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
            document.getElementById("modalUserName").textContent = userName;
            document.getElementById("modalUserEmail").textContent = userEmail;
            document.getElementById("modalAccountType").textContent =
                accountType.charAt(0).toUpperCase() + accountType.slice(1);
            document.getElementById("modalAccountType").className =
                accountType === "student"
                    ? "badge bg-primary"
                    : "badge bg-info";
            document.getElementById("modalRegisteredDate").textContent =
                registered;

            // Show/hide conditional fields based on account type
            const studentFields = document.querySelectorAll(".student-field");
            const facultyFields = document.querySelectorAll(".faculty-field");

            if (accountType === "student") {
                studentFields.forEach((field) => (field.style.display = ""));
                facultyFields.forEach(
                    (field) => (field.style.display = "none"),
                );
                document.getElementById("modalGradeLevel").textContent =
                    gradeLevel || "Not set";
                document.getElementById("modalLRN").textContent =
                    lrn || "Not set";
            } else {
                studentFields.forEach(
                    (field) => (field.style.display = "none"),
                );
                facultyFields.forEach((field) => (field.style.display = ""));
                document.getElementById("modalDepartment").textContent =
                    department || "Not set";
            }

            // Set form action with user ID
            const form = document.getElementById("approveAccountForm");
            form.action = `/admin/accounts/${userId}/approve`;

            // Show modal
            const modal = new bootstrap.Modal(
                document.getElementById("approveAccountModal"),
            );
            modal.show();
        });
    });

    // Handle approve form submission
    const approveForm = document.getElementById("approveAccountForm");
    if (approveForm) {
        approveForm.addEventListener("submit", function (e) {
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML =
                '<span class="spinner-border spinner-border-sm me-1"></span>Approving...';
        });
    }

    // Handle send email checkbox change
    const sendEmailCheckbox = document.getElementById("sendEmail");
    const sendEmailInput = document.getElementById("sendEmailInput");

    if (sendEmailCheckbox && sendEmailInput) {
        sendEmailCheckbox.addEventListener("change", function () {
            // Update hidden input value based on checkbox state
            sendEmailInput.value = this.checked ? "1" : "0";
        });
    }

    const anyModalOpen = () => !!document.querySelector(".modal.show");

    const buildLiveUrl = () => {
        const url = new URL(liveUrl, window.location.origin);
        const currentParams = new URLSearchParams(window.location.search);

        currentParams.forEach((value, key) => {
            url.searchParams.set(key, value);
        });

        return url;
    };

    const checkLiveSignature = async () => {
        if (!liveUrl || liveRequestInFlight) {
            return;
        }

        liveRequestInFlight = true;

        try {
            const response = await fetch(buildLiveUrl(), {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                },
            });

            if (!response.ok) {
                return;
            }

            const payload = await response.json();
            const nextSignature = payload?.signature || "";

            if (!nextSignature) {
                return;
            }

            if (!liveSignature) {
                liveSignature = nextSignature;
                if (liveContainer) {
                    liveContainer.dataset.liveSignature = nextSignature;
                }
                return;
            }

            if (nextSignature !== liveSignature) {
                if (anyModalOpen()) {
                    return;
                }

                window.location.reload();
            }
        } catch (error) {
            console.debug("Admin accounts live polling skipped:", error);
        } finally {
            liveRequestInFlight = false;
        }
    };

    if (liveUrl) {
        livePollTimer = window.setInterval(() => {
            if (!document.hidden) {
                checkLiveSignature();
            }
        }, livePollIntervalMs);

        document.addEventListener("visibilitychange", () => {
            if (!document.hidden) {
                checkLiveSignature();
            }
        });

        window.addEventListener(
            "beforeunload",
            () => {
                if (livePollTimer) {
                    clearInterval(livePollTimer);
                    livePollTimer = null;
                }
            },
            { once: true },
        );
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
