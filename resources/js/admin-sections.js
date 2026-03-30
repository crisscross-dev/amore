import "./schedule-picker-modal";

// Admin Sections page script
// Purpose: lightweight enhancements for sections listing and forms

document.addEventListener("DOMContentLoaded", () => {
    console.log("Admin Sections JS loaded");

    function askConfirmation(config) {
        if (window.Swal) {
            return window.Swal.fire({
                icon: config.icon || "question",
                title: config.title || "Confirm action",
                text: config.text || "Do you want to continue?",
                showCancelButton: true,
                confirmButtonText: config.confirmButtonText || "Yes, continue",
                cancelButtonText: "Cancel",
                confirmButtonColor: config.confirmButtonColor || "#198754",
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

    const assignAdviserForm = document.getElementById("assignAdviserForm");
    const assignAdviserSectionName = document.getElementById(
        "assignAdviserSectionName",
    );
    const assignAdviserSelect = document.getElementById("assignAdviserSelect");
    const adviserModalButtons = document.querySelectorAll(
        ".js-open-adviser-modal",
    );

    adviserModalButtons.forEach((button) => {
        button.addEventListener("click", () => {
            const sectionId = button.dataset.sectionId;
            const sectionName = button.dataset.sectionName || "—";
            const adviserId = button.dataset.adviserId || "";

            if (assignAdviserForm) {
                assignAdviserForm.action = `/admin/sections/${sectionId}/adviser`;
            }
            if (assignAdviserSectionName) {
                assignAdviserSectionName.textContent = sectionName;
            }
            if (assignAdviserSelect) {
                assignAdviserSelect.value = adviserId;
            }
        });
    });

    const editTeachingLoadForm = document.getElementById(
        "editTeachingLoadForm",
    );
    const editTeacherId = document.getElementById("editTeacherId");
    const editSectionId = document.getElementById("editSectionId");
    const editSubjectId = document.getElementById("editSubjectId");
    const editLoadButtons = document.querySelectorAll(
        ".js-open-edit-load-modal",
    );

    editLoadButtons.forEach((button) => {
        button.addEventListener("click", () => {
            if (editTeachingLoadForm) {
                editTeachingLoadForm.action = button.dataset.updateUrl;
            }
            if (editTeacherId) {
                editTeacherId.value = button.dataset.teacherId || "";
            }
            if (editSectionId) {
                editSectionId.value = button.dataset.sectionId || "";
            }
            if (editSubjectId) {
                editSubjectId.value = button.dataset.subjectId || "";
            }
        });
    });

    const scheduleConflictMessage = document.getElementById(
        "scheduleConflictMessage",
    );
    if (scheduleConflictMessage && scheduleConflictMessage.dataset.message) {
        alert(scheduleConflictMessage.dataset.message);
    }

    // Auto format grade level input to remove spaces
    document.querySelectorAll('input[name="grade_level"]').forEach((input) => {
        input.addEventListener("blur", () => {
            input.value = (input.value || "").trim();
        });
    });

    // Bulk student assignment functionality
    const selectAllCheckbox = document.getElementById("selectAll");
    const studentCheckboxes = document.querySelectorAll(".student-checkbox");
    const bulkAddBtn = document.getElementById("bulkAddBtn");
    const selectedCountSpan = document.getElementById("selectedCount");

    if (selectAllCheckbox && studentCheckboxes.length > 0) {
        // Select/deselect all
        selectAllCheckbox.addEventListener("change", function () {
            studentCheckboxes.forEach((checkbox) => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });

        // Update count when individual checkboxes change
        studentCheckboxes.forEach((checkbox) => {
            checkbox.addEventListener("change", updateSelectedCount);
        });

        function updateSelectedCount() {
            const checkedCount = document.querySelectorAll(
                ".student-checkbox:checked",
            ).length;
            if (selectedCountSpan) {
                selectedCountSpan.textContent = checkedCount;
            }
            if (bulkAddBtn) {
                bulkAddBtn.disabled = checkedCount === 0;
            }

            // Update select all checkbox state
            if (selectAllCheckbox) {
                selectAllCheckbox.checked =
                    checkedCount === studentCheckboxes.length &&
                    checkedCount > 0;
                selectAllCheckbox.indeterminate =
                    checkedCount > 0 && checkedCount < studentCheckboxes.length;
            }
        }

        // Confirm bulk assignment
        const bulkForm = document.getElementById("bulkAssignForm");
        if (bulkForm) {
            bulkForm.addEventListener("submit", function (e) {
                if (bulkForm.dataset.localConfirmPass === "true") {
                    delete bulkForm.dataset.localConfirmPass;
                    return;
                }

                const checkedCount = document.querySelectorAll(
                    ".student-checkbox:checked",
                ).length;
                if (checkedCount === 0) {
                    e.preventDefault();
                    alert("Please select at least one student.");
                    return false;
                }

                e.preventDefault();
                askConfirmation({
                    icon: "question",
                    title: "Add students to section",
                    text: `Add ${checkedCount} student${checkedCount > 1 ? "s" : ""} to this section?`,
                    confirmButtonText: "Yes, add",
                    confirmButtonColor: "#198754",
                }).then((confirmed) => {
                    if (!confirmed) {
                        return;
                    }

                    bulkForm.dataset.localConfirmPass = "true";
                    if (typeof bulkForm.requestSubmit === "function") {
                        bulkForm.requestSubmit();
                    } else {
                        bulkForm.submit();
                    }
                });
            });
        }
    }

    // Add Student Modal Search Functionality
    const studentSearch = document.getElementById("studentSearch");
    const selectAllStudentsBtn = document.getElementById(
        "selectAllStudentsBtn",
    );
    const studentItems = document.querySelectorAll(".student-item");
    const noResults = document.getElementById("noResults");
    const selectedStudentsDiv = document.getElementById("selectedStudents");
    const selectedStudentsList = document.getElementById(
        "selectedStudentsList",
    );
    const selectedStudentCount = document.getElementById(
        "selectedStudentCount",
    );
    const confirmAddBtn = document.getElementById("confirmAddStudents");
    const searchSuggestions = document.getElementById("searchSuggestions");
    const studentPagination = document.getElementById("studentPagination");

    let selectedStudents = [];
    let availableStudentsData = [];
    let currentFilteredStudents = [];
    let currentPage = 1;
    const pageSize = 20;

    function getPageStats(totalItems) {
        const totalPages = Math.max(1, Math.ceil(totalItems / pageSize));
        if (currentPage > totalPages) {
            currentPage = totalPages;
        }
        if (currentPage < 1) {
            currentPage = 1;
        }

        const startIndex = (currentPage - 1) * pageSize;
        const endIndex = Math.min(startIndex + pageSize, totalItems);

        return {
            totalPages,
            startIndex,
            endIndex,
        };
    }

    function renderPagination(totalItems) {
        if (!studentPagination) {
            return;
        }

        if (totalItems === 0) {
            studentPagination.innerHTML = "";
            return;
        }

        const stats = getPageStats(totalItems);
        studentPagination.innerHTML = `
      <span>Showing ${stats.startIndex + 1}-${stats.endIndex} of ${totalItems}</span>
      <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-sm btn-outline-secondary" id="studentPagePrev" ${currentPage <= 1 ? "disabled" : ""}>Prev</button>
        <span>Page ${currentPage} of ${stats.totalPages}</span>
        <button type="button" class="btn btn-sm btn-outline-secondary" id="studentPageNext" ${currentPage >= stats.totalPages ? "disabled" : ""}>Next</button>
      </div>
    `;

        const prevBtn = document.getElementById("studentPagePrev");
        const nextBtn = document.getElementById("studentPageNext");

        if (prevBtn) {
            prevBtn.addEventListener("click", function () {
                if (currentPage > 1) {
                    currentPage -= 1;
                    renderStudentList(currentFilteredStudents);
                }
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener("click", function () {
                const totalPages = Math.max(
                    1,
                    Math.ceil(currentFilteredStudents.length / pageSize),
                );
                if (currentPage < totalPages) {
                    currentPage += 1;
                    renderStudentList(currentFilteredStudents);
                }
            });
        }
    }

    if (studentSearch) {
        console.log(
            "studentSearch found, studentItems count:",
            studentItems.length,
        );

        // Build available students data array
        studentItems.forEach((item) => {
            availableStudentsData.push({
                id: item.dataset.studentId,
                name: item.dataset.studentName,
                displayName: item.dataset.studentDisplayName,
                customId: item.dataset.studentCustomId,
                email: item.dataset.studentEmail,
                lrn: item.dataset.studentLrn,
                picture: item.dataset.studentPicture,
                element: item,
            });
        });

        console.log(
            `Loaded ${availableStudentsData.length} available students`,
        );

        // Function to render student list
        function renderStudentList(students) {
            currentFilteredStudents = students;
            console.log(
                "renderStudentList called with",
                students.length,
                "students",
            );

            if (students.length === 0) {
                const searchTerm = studentSearch.value.trim();
                searchSuggestions.innerHTML = `
          <div class="p-4 text-center text-muted">
            <i class="fas fa-user-slash fa-2x mb-3 opacity-25"></i>
            <p class="mb-0">${searchTerm ? `No students found matching "${searchTerm}"` : "No students available"}</p>
          </div>
        `;
                renderPagination(0);
                return;
            }

            const stats = getPageStats(students.length);
            const pagedStudents = students.slice(
                stats.startIndex,
                stats.endIndex,
            );

            searchSuggestions.innerHTML = pagedStudents
                .map((student) => {
                    const isSelected = selectedStudents.find(
                        (s) => s.id === student.id,
                    );
                    const pictureHtml = student.picture
                        ? `<img src="/uploads/profile_picture/${student.picture}" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">`
                        : `<div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px; font-size: 16px;">
              ${student.displayName.charAt(0).toUpperCase()}
            </div>`;

                    return `
          <div class="suggestion-item p-3 border-bottom ${isSelected ? "bg-success-subtle" : ""}" style="transition: background-color 0.2s; cursor: pointer;" 
               data-student-id="${student.id}">
            <div class="d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center flex-grow-1">
                ${pictureHtml}
                <div>
                  <strong class="d-block">${student.displayName}</strong>
                  <small class="text-muted">${student.customId} • ${student.email}</small>
                </div>
              </div>
              <div class="ms-2 text-${isSelected ? "success" : "muted"}">
                <i class="fas ${isSelected ? "fa-check-circle" : "fa-circle"}"></i>
              </div>
            </div>
          </div>
        `;
                })
                .join("");

            renderPagination(students.length);

            // Click whole student box to toggle selection
            searchSuggestions
                .querySelectorAll(".suggestion-item")
                .forEach((item) => {
                    item.addEventListener("click", function () {
                        const studentId = this.dataset.studentId;
                        const student = availableStudentsData.find(
                            (s) => s.id === studentId,
                        );

                        if (!student) {
                            return;
                        }

                        const existing = selectedStudents.find(
                            (s) => s.id === studentId,
                        );

                        if (existing) {
                            selectedStudents = selectedStudents.filter(
                                (s) => s.id !== studentId,
                            );
                        } else {
                            selectedStudents.push({
                                id: studentId,
                                name: student.displayName,
                            });
                        }

                        updateSelectedDisplay();
                    });
                });
        }

        // Function to filter and display students
        function filterStudents() {
            console.log("filterStudents called");
            const searchTerm = studentSearch.value.toLowerCase().trim();
            console.log("Search term:", searchTerm);
            currentPage = 1;

            // Filter students
            let filtered = availableStudentsData.filter((student) => {
                // If search is empty, show all
                if (searchTerm === "") {
                    return true;
                }

                // Otherwise filter by search term
                return (
                    student.name.includes(searchTerm) ||
                    student.customId.includes(searchTerm) ||
                    student.email.toLowerCase().includes(searchTerm)
                );
            });

            console.log("Filtered students count:", filtered.length);
            renderStudentList(filtered);
        }

        // Function to update selected students display
        function updateSelectedDisplay() {
            if (selectedStudents.length > 0) {
                selectedStudentsDiv.style.display = "block";
                selectedStudentCount.textContent = selectedStudents.length;

                selectedStudentsList.innerHTML = selectedStudents
                    .map(
                        (student) => `
          <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded mb-2">
            <span>${student.name}</span>
            <button type="button" class="btn btn-sm btn-outline-danger remove-selected-btn" data-student-id="${student.id}">
              <i class="fas fa-times"></i>
            </button>
          </div>
        `,
                    )
                    .join("");

                // Add remove handlers
                selectedStudentsList
                    .querySelectorAll(".remove-selected-btn")
                    .forEach((btn) => {
                        btn.addEventListener("click", function () {
                            const studentId = this.dataset.studentId;
                            selectedStudents = selectedStudents.filter(
                                (s) => s.id !== studentId,
                            );
                            updateSelectedDisplay();
                        });
                    });

                confirmAddBtn.disabled = false;
            } else {
                selectedStudentsDiv.style.display = "none";
                confirmAddBtn.disabled = true;
            }

            renderStudentList(currentFilteredStudents);

            if (selectAllStudentsBtn) {
                const stats = getPageStats(currentFilteredStudents.length);
                const pageStudents = currentFilteredStudents.slice(
                    stats.startIndex,
                    stats.endIndex,
                );
                const visibleCount = pageStudents.length;
                const selectedVisibleCount = pageStudents.filter((student) =>
                    selectedStudents.some((picked) => picked.id === student.id),
                ).length;

                selectAllStudentsBtn.disabled = visibleCount === 0;
                if (visibleCount > 0 && selectedVisibleCount === visibleCount) {
                    selectAllStudentsBtn.innerHTML =
                        '<i class="fas fa-eraser me-1"></i>Clear All';
                } else {
                    selectAllStudentsBtn.innerHTML =
                        '<i class="fas fa-check-double me-1"></i>Select All';
                }
            }
        }

        // Initialize - show all students immediately
        console.log("About to call filterStudents() for initial display");
        filterStudents();
        updateSelectedDisplay();

        // Live filter as you type (instant, no delay)
        studentSearch.addEventListener("input", filterStudents);

        if (selectAllStudentsBtn) {
            selectAllStudentsBtn.addEventListener("click", function () {
                const stats = getPageStats(currentFilteredStudents.length);
                const visibleStudents = currentFilteredStudents.slice(
                    stats.startIndex,
                    stats.endIndex,
                );
                if (!visibleStudents.length) {
                    return;
                }

                const allVisibleSelected = visibleStudents.every((student) =>
                    selectedStudents.some((picked) => picked.id === student.id),
                );

                if (allVisibleSelected) {
                    const visibleIds = new Set(
                        visibleStudents.map((student) => student.id),
                    );
                    selectedStudents = selectedStudents.filter(
                        (student) => !visibleIds.has(student.id),
                    );
                } else {
                    visibleStudents.forEach((student) => {
                        if (
                            !selectedStudents.some(
                                (picked) => picked.id === student.id,
                            )
                        ) {
                            selectedStudents.push({
                                id: student.id,
                                name: student.displayName,
                            });
                        }
                    });
                }

                updateSelectedDisplay();
            });
        }

        // Enter key also filters
        studentSearch.addEventListener("keypress", function (e) {
            if (e.key === "Enter") {
                e.preventDefault();
                filterStudents();
            }
        });

        // Confirm add students
        if (confirmAddBtn) {
            confirmAddBtn.addEventListener("click", function () {
                if (selectedStudents.length === 0) return;

                askConfirmation({
                    icon: "question",
                    title: "Add students to section",
                    text: `Add ${selectedStudents.length} student${selectedStudents.length > 1 ? "s" : ""} to this section?`,
                    confirmButtonText: "Yes, add",
                    confirmButtonColor: "#198754",
                }).then((confirmed) => {
                    if (!confirmed) return;

                    // Get section ID from URL or data attribute
                    const sectionId =
                        document.querySelector("[data-section-id]")?.dataset
                            .sectionId ||
                        window.location.pathname.split("/").pop();

                    // Create form and submit
                    const form = document.createElement("form");
                    form.method = "POST";
                    form.action = "/admin/students/bulk-assign-section";

                    // CSRF token
                    const csrfInput = document.createElement("input");
                    csrfInput.type = "hidden";
                    csrfInput.name = "_token";
                    csrfInput.value =
                        document.querySelector('meta[name="csrf-token"]')
                            ?.content || "";
                    form.appendChild(csrfInput);

                    // Section ID
                    const sectionInput = document.createElement("input");
                    sectionInput.type = "hidden";
                    sectionInput.name = "section_id";
                    sectionInput.value = sectionId;
                    form.appendChild(sectionInput);

                    // Student IDs
                    selectedStudents.forEach((student) => {
                        const studentInput = document.createElement("input");
                        studentInput.type = "hidden";
                        studentInput.name = "student_ids[]";
                        studentInput.value = student.id;
                        form.appendChild(studentInput);
                    });

                    document.body.appendChild(form);
                    form.submit();
                });
            });
        }

        // Reset modal on close
        const addStudentModal = document.getElementById("addStudentModal");
        if (addStudentModal) {
            addStudentModal.addEventListener("hidden.bs.modal", function () {
                selectedStudents = [];
                currentPage = 1;
                updateSelectedDisplay();
                if (studentSearch) {
                    studentSearch.value = "";
                }
                filterStudents();
            });
        }
    }
});
