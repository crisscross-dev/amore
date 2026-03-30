export function createDatePickerModal(config) {
    const options = config || {};
    const modalId = options.modalId || "datePickerModal";
    let modalElement = document.getElementById(modalId);

    if (!modalElement) {
        return null;
    }

    const yearList = modalElement.querySelector("#datePickerYearList");
    const monthList = modalElement.querySelector("#datePickerMonthList");
    const dayList = modalElement.querySelector("#datePickerDayList");
    const yearColumn = modalElement.querySelector("#datePickerYearColumn");
    const monthColumn = modalElement.querySelector("#datePickerMonthColumn");
    const dayColumn = modalElement.querySelector("#datePickerDayColumn");
    const applyButton = modalElement.querySelector("#datePickerApplyBtn");
    const clearButton = modalElement.querySelector("#datePickerClearBtn");

    if (modalElement.parentElement !== document.body) {
        document.body.appendChild(modalElement);
    }

    const bootstrapModal =
        window.bootstrap && typeof window.bootstrap.Modal === "function"
            ? new window.bootstrap.Modal(modalElement, { focus: false })
            : null;

    if (!bootstrapModal) {
        return null;
    }

    const currentYear = new Date().getFullYear();
    const minYear = options.minYear || 1950;
    const years = [];
    for (let year = currentYear; year >= minYear; year -= 1) {
        years.push(year);
    }

    const months = [
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December",
    ];

    let selectedYear = null;
    let selectedMonth = null;
    let selectedDay = null;
    let preservedWindowScrollY = 0;
    let preservedWindowScrollX = 0;
    let openerElement = null;
    let preservedScrollTargets = [];
    let scrollRestoreFrame = null;
    let restoreFrameTicks = 0;

    function isScrollable(element) {
        if (
            !element ||
            element === document.body ||
            element === document.documentElement
        ) {
            return false;
        }

        const style = window.getComputedStyle(element);
        const overflowY = style.overflowY;
        return (
            (overflowY === "auto" || overflowY === "scroll") &&
            element.scrollHeight > element.clientHeight
        );
    }

    function getScrollableContainers(startElement) {
        const containers = [];
        let current = startElement;
        while (
            current &&
            current !== document.body &&
            current !== document.documentElement
        ) {
            if (isScrollable(current)) {
                containers.push(current);
            }
            current = current.parentElement;
        }

        return containers;
    }

    function captureScrollPositions() {
        preservedWindowScrollY = window.scrollY || window.pageYOffset || 0;
        preservedWindowScrollX = window.scrollX || window.pageXOffset || 0;
        preservedScrollTargets = getScrollableContainers(openerElement).map(
            (element) => ({
                element,
                top: element.scrollTop,
                left: element.scrollLeft,
            }),
        );
    }

    function restoreScrollPositions() {
        window.scrollTo({
            top: preservedWindowScrollY,
            left: preservedWindowScrollX,
            behavior: "auto",
        });
        preservedScrollTargets.forEach((entry) => {
            if (!entry || !entry.element) {
                return;
            }
            entry.element.scrollTop = entry.top;
            entry.element.scrollLeft = entry.left;
        });
    }

    function startScrollRestoreBurst() {
        if (scrollRestoreFrame) {
            window.cancelAnimationFrame(scrollRestoreFrame);
            scrollRestoreFrame = null;
        }

        restoreFrameTicks = 0;
        const tick = () => {
            restoreScrollPositions();
            restoreFrameTicks += 1;
            if (restoreFrameTicks < 6) {
                scrollRestoreFrame = window.requestAnimationFrame(tick);
            } else {
                scrollRestoreFrame = null;
            }
        };

        scrollRestoreFrame = window.requestAnimationFrame(tick);
    }

    function daysInMonth(year, month) {
        return new Date(year, month + 1, 0).getDate();
    }

    function renderPicker(container, values, selectedValue, disabled, type) {
        container.innerHTML = values
            .map((value) => {
                const classes = ["picker-option"];
                if (selectedValue === value) {
                    classes.push("is-selected");
                }
                if (disabled) {
                    classes.push("is-disabled");
                }

                return `<button type="button" class="${classes.join(" ")}" data-picker="${type}" data-value="${value}" ${disabled ? "disabled" : ""}>${value}</button>`;
            })
            .join("");
    }

    function setSelectedOption(container, type, value) {
        if (!container) {
            return;
        }

        const options = container.querySelectorAll(
            `.picker-option[data-picker="${type}"]`,
        );
        options.forEach((option) => {
            const isSelected =
                option.getAttribute("data-value") === String(value);
            option.classList.toggle("is-selected", isSelected);
        });
    }

    function setDisabledInContainer(container, disabled) {
        if (!container) {
            return;
        }

        const options = container.querySelectorAll(".picker-option");
        options.forEach((option) => {
            option.disabled = !!disabled;
            option.classList.toggle("is-disabled", !!disabled);
        });
    }

    function renderDaysOnly() {
        const hasYear = selectedYear !== null;
        const hasMonth = selectedMonth !== null;
        const dayValues =
            hasYear && hasMonth
                ? Array.from(
                      {
                          length: daysInMonth(
                              selectedYear,
                              months.indexOf(selectedMonth),
                          ),
                      },
                      (_, index) => index + 1,
                  )
                : [];

        if (selectedDay !== null && dayValues.indexOf(selectedDay) === -1) {
            selectedDay = null;
        }

        renderPicker(
            dayList,
            dayValues,
            selectedDay,
            !(hasYear && hasMonth),
            "day",
        );
    }

    function syncColumns() {
        const hasYear = selectedYear !== null;
        const hasMonth = selectedMonth !== null;

        if (yearColumn) yearColumn.classList.remove("is-locked");
        if (monthColumn) monthColumn.classList.toggle("is-locked", !hasYear);
        if (dayColumn)
            dayColumn.classList.toggle("is-locked", !(hasYear && hasMonth));

        setSelectedOption(yearList, "year", selectedYear);
        setSelectedOption(monthList, "month", selectedMonth);
        setDisabledInContainer(monthList, !hasYear);
        renderDaysOnly();
    }

    function getIsoDate() {
        if (
            selectedYear === null ||
            selectedMonth === null ||
            selectedDay === null
        ) {
            return "";
        }

        const monthNumber = months.indexOf(selectedMonth) + 1;
        return `${selectedYear}-${String(monthNumber).padStart(2, "0")}-${String(selectedDay).padStart(2, "0")}`;
    }

    function setFromIsoDate(isoDate) {
        if (!isoDate || !isoDate.includes("-")) {
            selectedYear = null;
            selectedMonth = null;
            selectedDay = null;
            syncColumns();
            return;
        }

        const parts = isoDate.split("-");
        const year = parseInt(parts[0], 10);
        const month = parseInt(parts[1], 10);
        const day = parseInt(parts[2], 10);

        if (
            !Number.isFinite(year) ||
            !Number.isFinite(month) ||
            !Number.isFinite(day)
        ) {
            selectedYear = null;
            selectedMonth = null;
            selectedDay = null;
            syncColumns();
            return;
        }

        selectedYear = year;
        selectedMonth = months[month - 1] || null;
        selectedDay = day;
        syncColumns();
    }

    yearList?.addEventListener("click", (event) => {
        const target = event.target.closest(
            '.picker-option[data-picker="year"]',
        );
        if (!target || target.disabled) return;

        const value = parseInt(target.getAttribute("data-value"), 10);
        if (!Number.isFinite(value)) return;

        const changed = selectedYear !== value;
        selectedYear = value;
        if (changed) {
            selectedMonth = null;
            selectedDay = null;
        }

        syncColumns();
    });

    monthList?.addEventListener("click", (event) => {
        const target = event.target.closest(
            '.picker-option[data-picker="month"]',
        );
        if (!target || target.disabled) return;

        const value = target.getAttribute("data-value");
        if (!value) return;

        const changed = selectedMonth !== value;
        selectedMonth = value;
        if (changed) {
            selectedDay = null;
        }

        syncColumns();
    });

    dayList?.addEventListener("click", (event) => {
        const target = event.target.closest(
            '.picker-option[data-picker="day"]',
        );
        if (!target || target.disabled) return;

        const value = parseInt(target.getAttribute("data-value"), 10);
        if (!Number.isFinite(value)) return;

        selectedDay = value;
        syncColumns();
    });

    applyButton?.addEventListener("click", () => {
        if (typeof options.onApply === "function") {
            options.onApply({
                value: getIsoDate(),
                year: selectedYear,
                month: selectedMonth,
                day: selectedDay,
            });
        }

        bootstrapModal?.hide();
    });

    clearButton?.addEventListener("click", () => {
        selectedYear = null;
        selectedMonth = null;
        selectedDay = null;
        syncColumns();
    });

    modalElement.addEventListener("hidden.bs.modal", () => {
        if (scrollRestoreFrame) {
            window.cancelAnimationFrame(scrollRestoreFrame);
            scrollRestoreFrame = null;
        }

        restoreScrollPositions();
        if (openerElement && typeof openerElement.focus === "function") {
            openerElement.focus({ preventScroll: true });
        }
        if (typeof options.onClose === "function") {
            options.onClose();
        }
    });

    modalElement.addEventListener("show.bs.modal", () => {
        restoreScrollPositions();
    });

    modalElement.addEventListener("shown.bs.modal", () => {
        restoreScrollPositions();
        requestAnimationFrame(() => {
            restoreScrollPositions();
        });
        startScrollRestoreBurst();
    });

    renderPicker(yearList, years, selectedYear, false, "year");
    renderPicker(monthList, months, selectedMonth, true, "month");
    renderDaysOnly();
    syncColumns();

    return {
        open(initialIsoDate, opener) {
            setFromIsoDate(initialIsoDate || "");
            openerElement = opener || null;

            if (openerElement && typeof openerElement.blur === "function") {
                openerElement.blur();
            }

            captureScrollPositions();

            bootstrapModal?.show();
        },
    };
}
