// Admin Sections page script
// Purpose: lightweight enhancements for sections listing and forms

document.addEventListener("DOMContentLoaded", () => {
    console.log("Admin Sections JS loaded");

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

    // Confirm delete actions if not already handled inline
    document
        .querySelectorAll('form[action*="/admin/sections/"][method="post"]')
        .forEach((form) => {
            const method = form.querySelector('input[name="_method"]');
            if (method && method.value.toUpperCase() === "DELETE") {
                form.addEventListener("submit", (e) => {
                    // If the button didn't already confirm, ensure a prompt
                    const confirmed = window.confirm("Delete this section?");
                    if (!confirmed) e.preventDefault();
                });
            }
        });

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
                const checkedCount = document.querySelectorAll(
                    ".student-checkbox:checked",
                ).length;
                if (checkedCount === 0) {
                    e.preventDefault();
                    alert("Please select at least one student.");
                    return false;
                }

                const confirmed = confirm(
                    `Add ${checkedCount} student${checkedCount > 1 ? "s" : ""} to this section?`,
                );
                if (!confirmed) {
                    e.preventDefault();
                }
            });
        }
    }

    // Add Student Modal Search Functionality
    const studentSearch = document.getElementById("studentSearch");
    const clearFilterBtn = document.getElementById("clearFilterBtn");
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

    let selectedStudents = [];
    let availableStudentsData = [];

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
                return;
            }

            searchSuggestions.innerHTML = students
                .map((student) => {
                    const isChecked = selectedStudents.find(
                        (s) => s.id === student.id,
                    )
                        ? "checked"
                        : "";
                    const pictureHtml = student.picture
                        ? `<img src="/uploads/profile_picture/${student.picture}" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">`
                        : `<div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px; font-size: 16px;">
              ${student.displayName.charAt(0).toUpperCase()}
            </div>`;

                    return `
          <div class="suggestion-item p-3 border-bottom" style="transition: background-color 0.2s;" 
               data-student-id="${student.id}">
            <div class="d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center flex-grow-1">
                <input type="checkbox" 
                       class="form-check-input student-select-checkbox me-3" 
                       data-student-id="${student.id}"
                       data-student-name="${student.displayName}"
                       ${isChecked}
                       style="cursor: pointer; width: 20px; height: 20px;">
                ${pictureHtml}
                <div>
                  <strong class="d-block">${student.displayName}</strong>
                  <small class="text-muted">${student.customId} • ${student.email}</small>
                </div>
              </div>
            </div>
          </div>
        `;
                })
                .join("");

            // Add change handlers to checkboxes
            searchSuggestions
                .querySelectorAll(".student-select-checkbox")
                .forEach((checkbox) => {
                    checkbox.addEventListener("change", function () {
                        const studentId = this.dataset.studentId;
                        const studentName = this.dataset.studentName;

                        if (this.checked) {
                            // Add to selected list
                            if (
                                !selectedStudents.find(
                                    (s) => s.id === studentId,
                                )
                            ) {
                                selectedStudents.push({
                                    id: studentId,
                                    name: studentName,
                                });
                            }
                        } else {
                            // Remove from selected list
                            selectedStudents = selectedStudents.filter(
                                (s) => s.id !== studentId,
                            );
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

                            // Uncheck the checkbox
                            const checkbox = document.querySelector(
                                `.student-select-checkbox[data-student-id="${studentId}"]`,
                            );
                            if (checkbox) checkbox.checked = false;

                            updateSelectedDisplay();
                        });
                    });

                confirmAddBtn.disabled = false;
            } else {
                selectedStudentsDiv.style.display = "none";
                confirmAddBtn.disabled = true;
            }
        }

        // Initialize - show all students immediately
        console.log("About to call filterStudents() for initial display");
        filterStudents();

        // Live filter as you type (instant, no delay)
        studentSearch.addEventListener("input", filterStudents);

        // Clear filter button
        if (clearFilterBtn) {
            clearFilterBtn.addEventListener("click", function () {
                studentSearch.value = "";
                filterStudents();
                studentSearch.focus();
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

                const confirmed = confirm(
                    `Add ${selectedStudents.length} student${selectedStudents.length > 1 ? "s" : ""} to this section?`,
                );
                if (!confirmed) return;

                // Get section ID from URL or data attribute
                const sectionId =
                    document.querySelector("[data-section-id]")?.dataset
                        .sectionId || window.location.pathname.split("/").pop();

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
        }

        // Reset modal on close
        const addStudentModal = document.getElementById("addStudentModal");
        if (addStudentModal) {
            addStudentModal.addEventListener("hidden.bs.modal", function () {
                selectedStudents = [];
                updateSelectedDisplay();
                if (studentSearch) {
                    studentSearch.value = "";
                }
            });
        }
    }
});
