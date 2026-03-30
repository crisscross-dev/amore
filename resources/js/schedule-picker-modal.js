function defaultToMinutes(timeValue) {
    if (!timeValue || timeValue.indexOf(":") === -1) {
        return null;
    }

    var parts = timeValue.split(":");
    var hours = parseInt(parts[0], 10);
    var minutes = parseInt(parts[1], 10);

    if (!Number.isFinite(hours) || !Number.isFinite(minutes)) {
        return null;
    }

    return hours * 60 + minutes;
}

function normalizeRanges(ranges, toMinutes) {
    return (ranges || [])
        .map(function (range) {
            var startRaw = range.start;
            var endRaw = range.end;

            var start =
                typeof startRaw === "number"
                    ? startRaw
                    : toMinutes(startRaw || "");
            var end =
                typeof endRaw === "number" ? endRaw : toMinutes(endRaw || "");

            if (
                start === null ||
                end === null ||
                !Number.isFinite(start) ||
                !Number.isFinite(end) ||
                end <= start
            ) {
                return null;
            }

            return { start: start, end: end };
        })
        .filter(Boolean);
}

function readOptionsFromMarkup(container, selector) {
    if (!container) {
        return [];
    }

    return Array.from(container.querySelectorAll(selector))
        .map(function (button) {
            return (
                button.getAttribute("data-value") || button.textContent || ""
            );
        })
        .map(function (value) {
            return (value || "").trim();
        })
        .filter(Boolean);
}

window.SchedulePickerModal = {
    create: function (config) {
        var opts = config || {};
        var toMinutes = opts.toMinutes || defaultToMinutes;
        var modalElement = document.getElementById(
            opts.modalId || "assignmentTimePickerModal",
        );
        var dayList = document.getElementById(
            opts.dayListId || "assignmentTimePickerDayList",
        );
        var roomList = document.getElementById(
            opts.roomListId || "assignmentTimePickerRoomList",
        );
        var timeList = document.getElementById(
            opts.timeListId || "assignmentTimePickerList",
        );
        var dayColumn = document.getElementById(
            opts.dayColumnId || "assignmentTimePickerDayColumn",
        );
        var roomColumn = document.getElementById(
            opts.roomColumnId || "assignmentTimePickerRoomColumn",
        );
        var timeColumn = document.getElementById(
            opts.timeColumnId || "assignmentTimePickerTimeColumn",
        );
        var summary = document.getElementById(
            opts.summaryId || "assignmentTimePickerSummary",
        );
        var applyButton = document.getElementById(
            opts.applyButtonId || "assignmentTimePickerApplyBtn",
        );
        var clearButton = document.getElementById(
            opts.clearButtonId || "assignmentTimePickerClearBtn",
        );

        if (!modalElement || !dayList || !roomList || !timeList) {
            return null;
        }

        if (modalElement.parentElement !== document.body) {
            document.body.appendChild(modalElement);
        }

        var bootstrapModal = null;
        if (window.bootstrap && typeof window.bootstrap.Modal === "function") {
            if (
                typeof window.bootstrap.Modal.getOrCreateInstance === "function"
            ) {
                bootstrapModal = window.bootstrap.Modal.getOrCreateInstance(
                    modalElement,
                    { focus: false },
                );
            } else {
                bootstrapModal = new window.bootstrap.Modal(modalElement, {
                    focus: false,
                });
            }
        }

        var dayOptions = opts.dayOptions || [];
        var roomOptions = opts.roomOptions || [];
        var timeOptions = opts.timeOptions || [];

        if (!dayOptions.length) {
            dayOptions = readOptionsFromMarkup(
                dayList,
                '.picker-option[data-picker="day"]',
            );
        }

        if (!roomOptions.length) {
            roomOptions = readOptionsFromMarkup(
                roomList,
                '.picker-option[data-picker="room"]',
            );
        }

        if (!timeOptions.length) {
            timeOptions = readOptionsFromMarkup(timeList, ".time-slot");
        }

        var activeRow = null;
        var selectedDay = "";
        var selectedRoom = "";
        var rangeStartIndex = null;
        var rangeEndIndex = null;

        function cleanupBackdropIfStuck() {
            window.setTimeout(function () {
                if (document.querySelector(".modal.show")) {
                    return;
                }

                document.body.classList.remove("modal-open");
                document.body.style.removeProperty("padding-right");
                Array.from(
                    document.querySelectorAll(".modal-backdrop"),
                ).forEach(function (backdrop) {
                    backdrop.remove();
                });
            }, 220);
        }

        function closeModal() {
            if (bootstrapModal) {
                bootstrapModal.hide();
                cleanupBackdropIfStuck();
                return;
            }

            modalElement.classList.remove("show");
            modalElement.style.display = "none";
            modalElement.setAttribute("aria-hidden", "true");
            cleanupBackdropIfStuck();
        }

        function getTimeIndex(timeValue) {
            return timeOptions.indexOf(timeValue || "");
        }

        function getSelection() {
            if (rangeStartIndex === null || rangeEndIndex === null) {
                return { start: "", end: "" };
            }

            var startIdx = Math.min(rangeStartIndex, rangeEndIndex);
            var endIdx = Math.max(rangeStartIndex, rangeEndIndex);

            return {
                start: timeOptions[startIdx] || "",
                end: timeOptions[endIdx] || "",
            };
        }

        function renderPickerList(
            container,
            options,
            selectedValue,
            disabled,
            type,
        ) {
            container.innerHTML = options
                .map(function (optionValue) {
                    var classes = ["picker-option"];
                    if ((selectedValue || "") === optionValue) {
                        classes.push("is-selected");
                    }
                    if (disabled) {
                        classes.push("is-disabled");
                    }

                    return (
                        '<button type="button" class="' +
                        classes.join(" ") +
                        '" data-picker="' +
                        type +
                        '" data-value="' +
                        optionValue +
                        '" ' +
                        (disabled ? "disabled" : "") +
                        ">" +
                        optionValue +
                        "</button>"
                    );
                })
                .join("");
        }

        function getOccupiedRanges() {
            if (!activeRow || !selectedDay || !selectedRoom) {
                return [];
            }

            var rawRanges = [];
            if (typeof opts.getOccupiedRanges === "function") {
                rawRanges =
                    opts.getOccupiedRanges({
                        row: activeRow,
                        day: selectedDay,
                        room: selectedRoom,
                    }) || [];
            }

            return normalizeRanges(rawRanges, toMinutes);
        }

        function renderTimeList() {
            var hasDayAndRoom = !!(selectedDay && selectedRoom);
            var occupiedRanges = getOccupiedRanges();
            var minIndex =
                rangeStartIndex !== null && rangeEndIndex !== null
                    ? Math.min(rangeStartIndex, rangeEndIndex)
                    : null;
            var maxIndex =
                rangeStartIndex !== null && rangeEndIndex !== null
                    ? Math.max(rangeStartIndex, rangeEndIndex)
                    : null;

            timeList.innerHTML = timeOptions
                .map(function (timeValue, index) {
                    var minutes = toMinutes(timeValue);
                    var isOccupied = occupiedRanges.some(function (range) {
                        if (minutes === null) {
                            return false;
                        }
                        return minutes >= range.start && minutes < range.end;
                    });

                    var isEdge =
                        rangeStartIndex === index || rangeEndIndex === index;
                    var isInRange =
                        minIndex !== null &&
                        maxIndex !== null &&
                        index >= minIndex &&
                        index <= maxIndex;

                    var classes = ["time-slot"];
                    if (isEdge) {
                        classes.push("is-edge");
                    } else if (isInRange) {
                        classes.push("is-in-range");
                    }
                    if (isOccupied) {
                        classes.push("is-occupied");
                    }
                    if (!hasDayAndRoom) {
                        classes.push("is-disabled");
                    }

                    var disabled = !hasDayAndRoom || isOccupied;

                    return (
                        '<button type="button" class="' +
                        classes.join(" ") +
                        '" data-index="' +
                        index +
                        '" ' +
                        (disabled ? "disabled" : "") +
                        ">" +
                        timeValue +
                        "</button>"
                    );
                })
                .join("");
        }

        function syncSummary() {
            if (!summary) {
                return;
            }

            var selected = getSelection();
            if (selected.start && selected.end) {
                summary.textContent = selected.start + " - " + selected.end;
                return;
            }

            if (rangeStartIndex !== null && rangeEndIndex === null) {
                summary.textContent =
                    (timeOptions[rangeStartIndex] || "") +
                    " selected, pick end time.";
                return;
            }

            summary.textContent = "Select first and last time from the list.";
        }

        function syncUI() {
            renderPickerList(dayList, dayOptions, selectedDay, false, "day");
            renderPickerList(
                roomList,
                roomOptions,
                selectedRoom,
                !selectedDay,
                "room",
            );
            renderTimeList();
            syncSummary();
        }

        function updateFlow() {
            if (!selectedDay) {
                selectedRoom = "";
            }

            var canPickTime = !!(selectedDay && selectedRoom);
            if (!canPickTime) {
                rangeStartIndex = null;
                rangeEndIndex = null;
            }

            if (dayColumn) {
                dayColumn.classList.remove("is-locked");
            }
            if (roomColumn) {
                roomColumn.classList.toggle("is-locked", !selectedDay);
            }
            if (timeColumn) {
                timeColumn.classList.toggle("is-locked", !canPickTime);
            }

            syncUI();
        }

        function resetState() {
            activeRow = null;
            selectedDay = "";
            selectedRoom = "";
            rangeStartIndex = null;
            rangeEndIndex = null;
        }

        dayList.addEventListener("click", function (event) {
            var dayButton = event.target.closest(
                '.picker-option[data-picker="day"]',
            );
            if (!dayButton || dayButton.disabled) {
                return;
            }

            var nextDay = dayButton.getAttribute("data-value") || "";
            var dayChanged = selectedDay !== nextDay;
            selectedDay = nextDay;
            if (dayChanged) {
                selectedRoom = "";
                rangeStartIndex = null;
                rangeEndIndex = null;
            }

            updateFlow();
        });

        roomList.addEventListener("click", function (event) {
            var roomButton = event.target.closest(
                '.picker-option[data-picker="room"]',
            );
            if (!roomButton || roomButton.disabled) {
                return;
            }

            var nextRoom = roomButton.getAttribute("data-value") || "";
            var roomChanged = selectedRoom !== nextRoom;
            selectedRoom = nextRoom;
            if (roomChanged) {
                rangeStartIndex = null;
                rangeEndIndex = null;
            }

            updateFlow();
        });

        timeList.addEventListener("click", function (event) {
            var timeButton = event.target.closest(".time-slot");
            if (!timeButton || timeButton.disabled) {
                return;
            }

            var pickedIndex = parseInt(
                timeButton.getAttribute("data-index"),
                10,
            );
            if (!Number.isFinite(pickedIndex) || pickedIndex < 0) {
                return;
            }

            if (
                rangeStartIndex === null ||
                (rangeStartIndex !== null && rangeEndIndex !== null)
            ) {
                rangeStartIndex = pickedIndex;
                rangeEndIndex = null;
                syncUI();
                return;
            }

            if (pickedIndex === rangeStartIndex) {
                rangeEndIndex = pickedIndex;
                syncUI();
                return;
            }

            if (pickedIndex < rangeStartIndex) {
                rangeStartIndex = pickedIndex;
                rangeEndIndex = null;
                syncUI();
                return;
            }

            rangeEndIndex = pickedIndex;
            syncUI();
        });

        if (applyButton) {
            applyButton.addEventListener("click", function () {
                if (!activeRow) {
                    closeModal();
                    return;
                }

                var selected = getSelection();
                var hasAnyValue =
                    !!selectedDay ||
                    !!selectedRoom ||
                    !!selected.start ||
                    !!selected.end;

                if (hasAnyValue && !selectedDay) {
                    opts.onError && opts.onError("Select day first.");
                    return;
                }

                if (hasAnyValue && selectedDay && !selectedRoom) {
                    opts.onError &&
                        opts.onError("Select room before choosing time.");
                    return;
                }

                if (hasAnyValue && !!selected.start !== !!selected.end) {
                    opts.onError &&
                        opts.onError("Select both start and end time.");
                    return;
                }

                var startMinutes = toMinutes(selected.start);
                var endMinutes = toMinutes(selected.end);
                if (
                    selected.start &&
                    selected.end &&
                    startMinutes !== null &&
                    endMinutes !== null &&
                    startMinutes >= endMinutes
                ) {
                    opts.onError &&
                        opts.onError("End time must be later than start time.");
                    return;
                }

                if (typeof opts.onApply === "function") {
                    opts.onApply({
                        row: activeRow,
                        day: selectedDay,
                        room: selectedRoom,
                        start: selected.start,
                        end: selected.end,
                    });
                }

                closeModal();
            });
        }

        if (clearButton) {
            clearButton.addEventListener("click", function () {
                selectedDay = "";
                selectedRoom = "";
                rangeStartIndex = null;
                rangeEndIndex = null;
                updateFlow();
            });
        }

        modalElement.addEventListener("hidden.bs.modal", function () {
            resetState();
            cleanupBackdropIfStuck();
        });

        return {
            open: function (row, initialValues, options) {
                var openOptions = options || {};
                activeRow = row;
                var initial = initialValues || {};
                selectedDay = initial.day || "";
                selectedRoom = initial.room || "";

                var startIdx = getTimeIndex(initial.start || "");
                var endIdx = getTimeIndex(initial.end || "");
                rangeStartIndex = startIdx >= 0 ? startIdx : null;
                rangeEndIndex = endIdx >= 0 ? endIdx : null;

                if (rangeStartIndex === null && rangeEndIndex !== null) {
                    rangeStartIndex = rangeEndIndex;
                }

                updateFlow();

                if (bootstrapModal && openOptions.showModal !== false) {
                    bootstrapModal.show();
                }
            },
            refresh: function () {
                if (!activeRow) {
                    return;
                }
                updateFlow();
            },
        };
    },
};
