import "./bootstrap";
import flatpickr from "flatpickr";
import Swal from "sweetalert2";
import "flatpickr/dist/themes/material_green.css";

window.Swal = Swal;

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

        flatpickr(input, {
            dateFormat: "Y-m-d",
            allowInput: true,
            disableMobile: true,
        });

        input.dataset.fpInitialized = "1";
    });

    dateTimeInputs.forEach((input) => {
        if (input.dataset.fpInitialized === "1") {
            return;
        }

        flatpickr(input, {
            enableTime: true,
            time_24hr: false,
            dateFormat: "Y-m-d\\TH:i",
            allowInput: true,
            disableMobile: true,
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
