import "./bootstrap";
import flatpickr from "flatpickr";
import Swal from "sweetalert2";
import "flatpickr/dist/themes/material_green.css";

window.Swal = Swal;

function resolveBoundaryYear(fp, value, fallback) {
    if (!value) {
        return fallback;
    }

    if (value instanceof Date && !Number.isNaN(value.getTime())) {
        return value.getFullYear();
    }

    if (typeof value === "string") {
        const parsed = fp.parseDate(value, fp.config.dateFormat);
        if (parsed instanceof Date && !Number.isNaN(parsed.getTime())) {
            return parsed.getFullYear();
        }
    }

    return fallback;
}

function getYearBounds(fp) {
    return {
        minYear: 1970,
        maxYear: 2040,
    };
}

function mountYearDropdown(_selectedDates, _dateStr, fp) {
    const currentMonth = fp.calendarContainer?.querySelector(
        ".flatpickr-current-month",
    );
    const yearWrapper = currentMonth?.querySelector(".numInputWrapper");

    if (!currentMonth || !yearWrapper) {
        return;
    }

    let yearSelect = currentMonth.querySelector(".flatpickr-year-dropdown");
    if (!yearSelect) {
        yearSelect = document.createElement("select");
        yearSelect.className = "flatpickr-year-dropdown";
        yearSelect.setAttribute("aria-label", "Select year");
        yearSelect.style.marginLeft = "0.35rem";
        yearSelect.style.border = "1px solid #ced4da";
        yearSelect.style.borderRadius = "0.35rem";
        yearSelect.style.padding = "0.1rem 0.3rem";
        yearSelect.style.backgroundColor = "#fff";

        yearSelect.addEventListener("change", () => {
            const nextYear = Number.parseInt(yearSelect.value, 10);
            if (!Number.isNaN(nextYear)) {
                fp.changeYear(nextYear);
                fp.redraw();
            }
        });

        currentMonth.appendChild(yearSelect);
    }

    const { minYear, maxYear } = getYearBounds(fp);
    const expectedLength = maxYear - minYear + 1;
    const currentFirst = Number.parseInt(
        yearSelect.options[0]?.value || "",
        10,
    );
    const currentLast = Number.parseInt(
        yearSelect.options[yearSelect.options.length - 1]?.value || "",
        10,
    );

    if (
        yearSelect.options.length !== expectedLength ||
        currentFirst !== minYear ||
        currentLast !== maxYear
    ) {
        yearSelect.innerHTML = "";
        for (let year = minYear; year <= maxYear; year += 1) {
            const option = document.createElement("option");
            option.value = String(year);
            option.textContent = String(year);
            yearSelect.appendChild(option);
        }
    }

    yearSelect.value = String(fp.currentYear);
    yearWrapper.style.display = "none";
}

function initializeGlobalDatePickers(root = document) {
    const dateInputs = root.querySelectorAll(
        'input[type="date"]:not([data-flatpickr="off"])',
    );
    const dateTimeInputs = root.querySelectorAll(
        'input[type="datetime-local"]:not([data-flatpickr="off"])',
    );

    dateInputs.forEach((input) => {
        if (input.dataset.fpInitialized === "1") {
            return;
        }

        const readOnlyClickOnly = input.hasAttribute("readonly");
        flatpickr(input, {
            dateFormat: "Y-m-d",
            allowInput: !readOnlyClickOnly,
            disableMobile: true,
            clickOpens: true,
            monthSelectorType: "dropdown",
            onReady: [mountYearDropdown],
            onOpen: [mountYearDropdown],
            onYearChange: [mountYearDropdown],
            onMonthChange: [mountYearDropdown],
        });

        input.dataset.fpInitialized = "1";
    });

    dateTimeInputs.forEach((input) => {
        if (input.dataset.fpInitialized === "1") {
            return;
        }

        const readOnlyClickOnly = input.hasAttribute("readonly");
        flatpickr(input, {
            enableTime: true,
            time_24hr: false,
            dateFormat: "Y-m-d\\TH:i",
            allowInput: !readOnlyClickOnly,
            disableMobile: true,
            clickOpens: true,
            monthSelectorType: "dropdown",
            onReady: [mountYearDropdown],
            onOpen: [mountYearDropdown],
            onYearChange: [mountYearDropdown],
            onMonthChange: [mountYearDropdown],
        });

        input.dataset.fpInitialized = "1";
    });
}

// Reuse one global initializer so date fields added later (modals/AJAX) get Flatpickr too.
window.initializeGlobalDatePickers = initializeGlobalDatePickers;

document.addEventListener("DOMContentLoaded", () => {
    const navbar = document.querySelector(".navbar-custom");
    const threshold = 30;

    function onScroll() {
        if (!navbar) {
            return;
        }

        if (window.scrollY > threshold) {
            navbar.classList.add("scrolled");
        } else {
            navbar.classList.remove("scrolled");
        }
    }

    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });

    initializeGlobalDatePickers(document);

    const observer = new MutationObserver((mutations) => {
        for (const mutation of mutations) {
            for (const node of mutation.addedNodes) {
                if (!(node instanceof HTMLElement)) {
                    continue;
                }

                if (
                    node.matches(
                        'input[type="date"], input[type="datetime-local"]',
                    )
                ) {
                    initializeGlobalDatePickers(node.parentElement || document);
                }

                if (
                    node.querySelector &&
                    node.querySelector(
                        'input[type="date"], input[type="datetime-local"]',
                    )
                ) {
                    initializeGlobalDatePickers(node);
                }
            }
        }
    });

    observer.observe(document.body, { childList: true, subtree: true });
});
