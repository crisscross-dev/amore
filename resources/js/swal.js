(function () {
    "use strict";

    if (window.__appSwalInitialized) {
        return;
    }
    window.__appSwalInitialized = true;

    var hasSwal = typeof window.Swal !== "undefined";

    var palette = {
        create: "#198754",
        update: "#0d6efd",
        delete: "#dc3545",
        cancel: "#6c757d",
    };

    var baseSwalConfig = {
        background: "#f6fef7",
        color: "#555",
        allowEscapeKey: true,
        allowEnterKey: true,
        reverseButtons: true,
        focusCancel: true,
        showCancelButton: true,
        cancelButtonText: "Cancel",
        confirmButtonText: "Confirm",
        confirmButtonColor: palette.create,
        cancelButtonColor: palette.cancel,
        customClass: {
            popup: "swal2-green-theme",
            title: "swal2-green-title",
            htmlContainer: "swal2-green-text",
            actions: "swal2-green-actions",
            confirmButton: "swal2-green-confirm",
            cancelButton: "swal2-green-cancel",
        },
        showClass: {
            popup: "swal2-green-popup-in",
            backdrop: "swal2-green-backdrop-in",
        },
        hideClass: {
            popup: "swal2-green-popup-out",
            backdrop: "swal2-green-backdrop-out",
        },
    };

    function ensureSwal() {
        if (hasSwal) {
            return Promise.resolve();
        }

        return Promise.reject(new Error("SweetAlert2 is not loaded."));
    }

    function deepMerge(target, source) {
        var output = target || {};
        var key;

        if (!source) {
            return output;
        }

        for (key in source) {
            if (!Object.prototype.hasOwnProperty.call(source, key)) {
                continue;
            }

            if (
                source[key] &&
                typeof source[key] === "object" &&
                !Array.isArray(source[key])
            ) {
                output[key] = deepMerge(output[key] || {}, source[key]);
            } else {
                output[key] = source[key];
            }
        }

        return output;
    }

    function withBaseConfig(config) {
        return deepMerge(deepMerge({}, baseSwalConfig), config || {});
    }

    function modalOptions(config) {
        return withBaseConfig({
            icon: config.icon || "question",
            title: config.title,
            text: config.text,
            confirmButtonText: config.confirmText || "Confirm",
            cancelButtonText: config.cancelText || "Cancel",
            confirmButtonColor: config.confirmColor,
            allowOutsideClick: function () {
                return !window.Swal.isLoading();
            },
        });
    }

    function runConfirm(config) {
        return ensureSwal().then(function () {
            return window.Swal.fire(modalOptions(config));
        });
    }

    function confirmCreate() {
        return runConfirm({
            icon: "question",
            title: "Create record",
            text: "Do you want to create this record now?",
            confirmText: "Yes, create",
            confirmColor: palette.create,
        });
    }

    function confirmUpdateAdmin() {
        return runConfirm({
            icon: "question",
            title: "Update record",
            text: "Are you sure you want to update this record?",
            confirmText: "Yes, update",
            confirmColor: palette.update,
        });
    }

    function confirmUpdateFaculty() {
        return runConfirm({
            icon: "question",
            title: "Update student data",
            text: "Confirm updating student data?",
            confirmText: "Yes, continue",
            confirmColor: palette.update,
        });
    }

    function confirmDelete() {
        return runConfirm({
            icon: "warning",
            title: "Delete record",
            text: "This action cannot be undone. Proceed with deletion?",
            confirmText: "Yes, delete",
            confirmColor: palette.delete,
        });
    }

    function confirmLogout() {
        return runConfirm({
            icon: "question",
            title: "Sign out",
            text: "Are you sure you want to log out?",
            confirmText: "Yes, log out",
            confirmColor: palette.update,
        });
    }

    function showSuccess(message) {
        return ensureSwal().then(function () {
            return window.Swal.fire(
                withBaseConfig({
                    icon: "success",
                    title: "Success",
                    text: message || "Operation completed successfully.",
                    timer: 1800,
                    showCancelButton: false,
                    showConfirmButton: false,
                }),
            );
        });
    }

    function showSuccessToast(message) {
        return ensureSwal().then(function () {
            return window.Swal.fire(
                withBaseConfig({
                    toast: true,
                    position: "top-end",
                    icon: "success",
                    title: message || "Saved successfully",
                    showConfirmButton: false,
                    showCancelButton: false,
                    timer: 2200,
                    timerProgressBar: true,
                    customClass: {
                        popup: "swal2-green-theme",
                        title: "swal2-green-title",
                    },
                }),
            );
        });
    }

    function showError(message) {
        return ensureSwal().then(function () {
            return window.Swal.fire(
                withBaseConfig({
                    icon: "error",
                    title: "Error",
                    text: message || "Something went wrong. Please try again.",
                    showCancelButton: false,
                    confirmButtonColor: palette.delete,
                }),
            );
        });
    }

    function showSubmittingState(actionText) {
        return ensureSwal().then(function () {
            window.Swal.fire(
                withBaseConfig({
                    title: actionText || "Processing...",
                    text: "Please wait while we process your request.",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showCancelButton: false,
                    showConfirmButton: false,
                    didRender: function () {
                        var actions = document.querySelector(".swal2-actions");
                        if (!actions) {
                            return;
                        }

                        actions
                            .querySelectorAll("button")
                            .forEach(function (btn) {
                                btn.disabled = true;
                            });
                    },
                    didOpen: function () {
                        window.Swal.showLoading();
                    },
                }),
            );

            // Do not wait for modal close; submission should continue immediately.
            return true;
        });
    }

    function resolveForm(element) {
        if (!element) {
            return null;
        }

        var selector = element.getAttribute("data-form");
        if (selector) {
            return document.querySelector(selector);
        }

        if (element.form) {
            return element.form;
        }

        return element.closest("form");
    }

    function completeSubmission(form, submitter) {
        form.dataset.swalConfirmed = "true";

        var isSubmitterOwnedByForm = submitter && submitter.form === form;

        if (
            submitter &&
            typeof form.requestSubmit === "function" &&
            submitter.tagName === "BUTTON" &&
            isSubmitterOwnedByForm
        ) {
            form.requestSubmit(submitter);
            return;
        }

        form.submit();
    }

    function submitWithConfirmation(trigger, confirmationFn, loadingText) {
        var form = resolveForm(trigger);
        if (!form || typeof confirmationFn !== "function") {
            return Promise.resolve(false);
        }

        if (form.dataset.swalPending === "true") {
            return Promise.resolve(false);
        }

        form.dataset.swalPending = "true";

        return confirmationFn()
            .then(function (result) {
                if (!result.isConfirmed) {
                    if (trigger && trigger.disabled) {
                        trigger.disabled = false;
                    }
                    delete form.dataset.swalPending;
                    return false;
                }

                return showSubmittingState(loadingText)
                    .catch(function () {
                        return null;
                    })
                    .then(function () {
                        delete form.dataset.swalPending;
                        completeSubmission(form, trigger);
                        return true;
                    });
            })
            .catch(function () {
                delete form.dataset.swalPending;
                return showError(
                    "Unable to open confirmation dialog right now.",
                ).then(function () {
                    return false;
                });
            });
    }

    function bindCrudDelegates() {
        function resolveLogoutForm(trigger) {
            var form = resolveForm(trigger);
            if (form) {
                return form;
            }

            var inlineOnclick = String(trigger.getAttribute("onclick") || "");
            var idMatch = inlineOnclick.match(
                /getElementById\((['\"])([^'\"]+)\1\)\.submit\(\)/,
            );

            if (idMatch && idMatch[2]) {
                return document.getElementById(idMatch[2]);
            }

            return document.querySelector('form[action*="/logout"]');
        }

        function handleLogoutTrigger(event, trigger) {
            var logoutForm = resolveLogoutForm(trigger);
            if (!logoutForm) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();
            if (typeof event.stopImmediatePropagation === "function") {
                event.stopImmediatePropagation();
            }

            confirmLogout()
                .then(function (result) {
                    if (!result.isConfirmed) {
                        return;
                    }

                    return showSubmittingState("Signing out...")
                        .catch(function () {
                            return null;
                        })
                        .then(function () {
                            completeSubmission(logoutForm, trigger);
                        });
                })
                .catch(function () {
                    showError("Unable to confirm logout right now.");
                });
        }

        // Capture phase ensures we run before inline onclick handlers that call form.submit().
        document.addEventListener(
            "click",
            function (event) {
                var logoutTrigger = event.target.closest(
                    ".logout-link, .logout-btn, [data-swal=logout], [data-logout-trigger]",
                );

                if (!logoutTrigger) {
                    return;
                }

                handleLogoutTrigger(event, logoutTrigger);
            },
            true,
        );

        function inferModeFromForm(form, submitter) {
            var explicitMode = form.getAttribute("data-swal");
            if (explicitMode) {
                return explicitMode;
            }

            var methodInput = form.querySelector('input[name="_method"]');
            var spoofedMethod = methodInput
                ? String(methodInput.value || "").toUpperCase()
                : "";

            if (spoofedMethod === "DELETE") {
                return "delete";
            }

            var action = String(
                form.getAttribute("action") || "",
            ).toLowerCase();
            if (action.indexOf("/logout") !== -1) {
                return "logout";
            }

            if (spoofedMethod === "PUT" || spoofedMethod === "PATCH") {
                var bodyAccountType = String(
                    document.body.getAttribute("data-account-type") || "",
                ).toLowerCase();

                if (bodyAccountType === "faculty") {
                    return "update-faculty";
                }

                return "update-admin";
            }

            if (
                submitter &&
                submitter.matches &&
                (submitter.matches(".btn-create") ||
                    submitter.matches("[data-swal=create]"))
            ) {
                return "create";
            }

            if (
                form.getAttribute("method") &&
                form.getAttribute("method").toLowerCase() === "post"
            ) {
                if (form.hasAttribute("data-crud-create")) {
                    return "create";
                }
            }

            return "";
        }

        document.addEventListener("click", function (event) {
            var createBtn = event.target.closest(".btn-create");
            if (createBtn) {
                event.preventDefault();
                submitWithConfirmation(
                    createBtn,
                    confirmCreate,
                    "Creating record...",
                );
                return;
            }

            var updateAdminBtn = event.target.closest(
                ".btn-update-admin, [data-swal=update-admin]",
            );
            if (updateAdminBtn) {
                event.preventDefault();
                submitWithConfirmation(
                    updateAdminBtn,
                    confirmUpdateAdmin,
                    "Updating record...",
                );
                return;
            }

            var updateFacultyBtn = event.target.closest(
                ".btn-update-faculty, [data-swal=update-faculty]",
            );
            if (updateFacultyBtn) {
                event.preventDefault();
                submitWithConfirmation(
                    updateFacultyBtn,
                    confirmUpdateFaculty,
                    "Updating student data...",
                );
                return;
            }

            var deleteBtn = event.target.closest(
                ".btn-delete, [data-swal=delete]",
            );
            if (deleteBtn) {
                event.preventDefault();
                submitWithConfirmation(
                    deleteBtn,
                    confirmDelete,
                    "Deleting record...",
                );
            }
        });

        document.addEventListener("submit", function (event) {
            var form = event.target;

            if (!(form instanceof HTMLFormElement)) {
                return;
            }

            if (form.dataset.swalPending === "true") {
                event.preventDefault();
                return;
            }

            var mode = inferModeFromForm(form, event.submitter);
            if (!mode || form.dataset.swalConfirmed === "true") {
                return;
            }

            var map = {
                create: confirmCreate,
                "update-admin": confirmUpdateAdmin,
                "update-faculty": confirmUpdateFaculty,
                delete: confirmDelete,
                logout: confirmLogout,
            };

            var confirmFn = map[mode];
            if (!confirmFn) {
                return;
            }

            event.preventDefault();
            form.dataset.swalPending = "true";

            confirmFn()
                .then(function (result) {
                    if (!result.isConfirmed) {
                        if (event.submitter && event.submitter.disabled) {
                            event.submitter.disabled = false;
                        }
                        delete form.dataset.swalPending;
                        return;
                    }

                    showSubmittingState("Processing request...")
                        .catch(function () {
                            return null;
                        })
                        .then(function () {
                            delete form.dataset.swalPending;
                            completeSubmission(form, event.submitter);
                        });
                })
                .catch(function () {
                    delete form.dataset.swalPending;
                    showError("Unable to confirm this action at the moment.");
                });
        });
    }

    window.AppSwal = {
        baseSwalConfig: baseSwalConfig,
        confirmCreate: confirmCreate,
        confirmUpdateAdmin: confirmUpdateAdmin,
        confirmUpdateFaculty: confirmUpdateFaculty,
        confirmDelete: confirmDelete,
        confirmLogout: confirmLogout,
        showSuccess: showSuccess,
        showSuccessToast: showSuccessToast,
        showError: showError,
        submitWithConfirmation: submitWithConfirmation,
    };

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", bindCrudDelegates);
    } else {
        bindCrudDelegates();
    }
})();
