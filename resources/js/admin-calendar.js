/**
 * Admin Calendar - Traditional Navigation (No AJAX)
 * Handles month navigation using standard page loads
 */

class AdminCalendar {
    constructor() {
        this.currentYear = null;
        this.currentMonth = null;
        this.init();
    }

    init() {
        // Get current month/year from page
        const monthElement = document.querySelector('[data-calendar-month]');
        const yearElement = document.querySelector('[data-calendar-year]');
        
        if (monthElement && yearElement) {
            this.currentMonth = parseInt(monthElement.dataset.calendarMonth);
            this.currentYear = parseInt(yearElement.dataset.calendarYear);
        } else {
            // Fallback to current date
            const now = new Date();
            this.currentMonth = now.getMonth() + 1;
            this.currentYear = now.getFullYear();
        }

        this.bindEvents();
        
        // Check URL for highlight parameter
        this.checkHighlightFromUrl();
        
        console.log('Admin Calendar initialized:', this.currentYear, this.currentMonth);
    }

    /**
     * Check URL for highlight parameter and highlight the day
     */
    checkHighlightFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        const highlightDay = urlParams.get('highlight');
        
        if (highlightDay) {
            const dayNumber = parseInt(highlightDay);
            if (dayNumber > 0 && dayNumber <= 31) {
                // Small delay to ensure DOM is ready
                setTimeout(() => {
                    this.highlightCalendarDay(dayNumber);
                }, 100);
            }
        }
    }

    bindEvents() {
        // Navigation buttons
        const prevBtn = document.getElementById('calendar-prev-btn');
        const nextBtn = document.getElementById('calendar-next-btn');
        const todayBtn = document.getElementById('calendar-today-btn');
        const removeHighlightBtn = document.getElementById('calendar-remove-highlight-btn');

        if (prevBtn) {
            prevBtn.addEventListener('click', () => this.navigateMonth(-1));
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => this.navigateMonth(1));
        }

        if (todayBtn) {
            todayBtn.addEventListener('click', () => this.navigateToToday());
        }

        if (removeHighlightBtn) {
            removeHighlightBtn.addEventListener('click', () => this.removeHighlight());
        }

        // Calendar day clicks
        document.querySelectorAll('.calendar-day:not(.empty)').forEach(day => {
            day.addEventListener('click', (e) => this.handleDayClick(e));
        });

        // Upcoming event clicks to navigate to event month
        document.querySelectorAll('.upcoming-event-item').forEach(item => {
            item.addEventListener('click', (e) => this.navigateToEventMonth(e));
            
            // Keyboard accessibility (Enter or Space key)
            item.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.navigateToEventMonth(e);
                }
            });
        });
    }

    navigateMonth(direction) {
        let newMonth = this.currentMonth + direction;
        let newYear = this.currentYear;

        if (newMonth > 12) {
            newMonth = 1;
            newYear++;
        } else if (newMonth < 1) {
            newMonth = 12;
            newYear--;
        }

        // Navigate to new month using traditional page load
        window.location.href = `/calendar/${newYear}/${newMonth}`;
    }

    navigateToToday() {
        const now = new Date();
        const month = now.getMonth() + 1;
        const year = now.getFullYear();

        if (month === this.currentMonth && year === this.currentYear) {
            return; // Already on current month
        }

        // Navigate to current month
        window.location.href = `/calendar/${year}/${month}`;
    }

    handleDayClick(e) {
        const dayElement = e.currentTarget;
        const day = dayElement.dataset.day;
        
        if (!day) return;

        // Navigate to the event details page for this day
        window.location.href = `/calendar/show/${this.currentYear}/${this.currentMonth}/${day}`;
    }

    /**
     * Navigate to event's month when upcoming event is clicked
     * Navigates to the month and highlights the day via URL parameter
     */
    navigateToEventMonth(e) {
        const eventItem = e.currentTarget;
        const eventMonth = parseInt(eventItem.dataset.eventMonth);
        const eventYear = parseInt(eventItem.dataset.eventYear);
        const eventDay = parseInt(eventItem.dataset.eventDay);
        
        if (!eventMonth || !eventYear || !eventDay) {
            console.error('Event data not found');
            return;
        }

        // Navigate to the event's month with highlight parameter
        console.log(`Navigating to ${eventYear}/${eventMonth} and highlighting day ${eventDay}`);
        window.location.href = `/calendar/${eventYear}/${eventMonth}?highlight=${eventDay}`;
    }

    /**
     * Highlight a calendar day with yellow border
     * Used for visual feedback when clicking upcoming events
     */
    highlightCalendarDay(dayNumber) {
        // Remove previous highlights
        document.querySelectorAll('.calendar-day.highlighted').forEach(cell => {
            cell.classList.remove('highlighted');
        });

        // Find and highlight the matching day
        const targetCell = document.querySelector(`.calendar-day[data-day="${dayNumber}"]`);
        if (targetCell && !targetCell.classList.contains('empty')) {
            targetCell.classList.add('highlighted');
            console.log(`Highlighted day ${dayNumber}`);
            
            // Show the remove highlight button
            this.toggleRemoveHighlightButton(true);
        }
    }

    /**
     * Remove highlight from calendar day
     */
    removeHighlight() {
        // Remove all highlights
        document.querySelectorAll('.calendar-day.highlighted').forEach(cell => {
            cell.classList.remove('highlighted');
        });

        // Hide the remove highlight button
        this.toggleRemoveHighlightButton(false);

        // Remove highlight parameter from URL without page reload
        const url = new URL(window.location);
        url.searchParams.delete('highlight');
        window.history.replaceState({}, '', url);

        console.log('Highlight removed');
    }

    /**
     * Show or hide the remove highlight button
     */
    toggleRemoveHighlightButton(show) {
        const btn = document.getElementById('calendar-remove-highlight-btn');
        if (btn) {
            if (show) {
                btn.classList.remove('d-none');
                // Add fade-in animation
                btn.style.animation = 'fadeIn 0.3s ease';
            } else {
                btn.classList.add('d-none');
            }
        }
    }
}

// Initialize calendar when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        const calendarPage = document.getElementById('admin-calendar-page') || 
                           document.getElementById('faculty-calendar-page') ||
                           document.getElementById('student-calendar-page');
        if (calendarPage) {
            window.adminCalendar = new AdminCalendar();
        }
    });
} else {
    const calendarPage = document.getElementById('admin-calendar-page') || 
                       document.getElementById('faculty-calendar-page') ||
                       document.getElementById('student-calendar-page');
    if (calendarPage) {
        window.adminCalendar = new AdminCalendar();
    }
}

// Handle browser back/forward
window.addEventListener('popstate', (event) => {
    if (event.state && event.state.year && event.state.month) {
        window.location.reload(); // Simple reload for now
    }
});

export default AdminCalendar;
