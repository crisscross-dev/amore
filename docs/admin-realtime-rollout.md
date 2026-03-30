# Admin Realtime Rollout Plan

Last updated: March 29, 2026

## Goal
Provide a clear, review-friendly rollout list for admin pages that should get realtime updates next.

## Already Implemented
- [x] Admin Dashboard live section
  - Route: `dashboard.admin.live-section`
  - File: `resources/views/dashboards/admin.blade.php`
- [x] Calendar live signature
  - Route: `calendar.live-signature`
  - Files: `resources/views/admin/calendar.blade.php`, `resources/views/faculty/calendar.blade.php`, `resources/views/student/calendar.blade.php`
- [x] Announcements live signature
  - Route: `announcements.live-signature`
  - Files: `resources/views/admin/announcements/index.blade.php`, `resources/views/faculty/announcements/index.blade.php`, `resources/views/student/announcements/index.blade.php`
- [x] Faculty Assignments live signature
  - Route: `admin.faculty-assignments.live-signature`
  - File: `resources/views/admin/faculty_assignments/index.blade.php`
- [x] Grade Approvals live section
  - Route: `admin.grade-approvals.live-section`
  - File: `resources/views/admin/grade_approvals/index.blade.php`
- [x] Admissions queue live signature
  - Route: `admin.admissions.live-signature`
  - Files: `resources/views/admin/admissions/index.blade.php`, `resources/views/admin/admissions/approved.blade.php`
- [x] Enrollments queue live signature
  - Route: `admin.enrollments.live-signature`
  - File: `resources/views/admin/enrollments/index.blade.php`
- [x] Accounts management live signature
  - Route: `admin.accounts.live-signature`
  - File: `resources/views/admin/accounts/manage.blade.php`

## Priority Rollout (Admin)

### 1) Admissions Queue (Highest Priority)
- [x] `resources/views/admin/admissions/index.blade.php`
- [x] `resources/views/admin/admissions/approved.blade.php`
- Why: frequent approve/reject/bulk actions and high chance of stale data.

### 2) Enrollments Queue (Highest Priority)
- [x] `resources/views/admin/enrollments/index.blade.php`
- Why: same high-concurrency behavior as admissions.

### 3) Accounts Management
- [x] `resources/views/admin/accounts/manage.blade.php`
- Why: approvals/rejections and status edits are multi-admin sensitive.

### 4) Sections Assignment Workspace
- [x] `resources/views/admin/sections/show.blade.php`
- [x] `resources/views/admin/sections/index.blade.php`
- Why: adviser/teacher/schedule assignment changes can become stale quickly.

### 5) Teaching Loads
- [ ] `resources/views/admin/sections/teaching-loads.blade.php`
- Why: operational assignment data; stale views can cause overlaps/conflicts.

### 6) Subjects Management
- [ ] `resources/views/admin/subjects/index.blade.php`
- Why: medium priority; changes are less frequent than queues.

### 7) School Years
- [ ] `resources/views/admin/school_years/index.blade.php`
- Why: low-frequency updates but useful for consistency.

## Usually Not Needed for Realtime
- Create/Edit form pages (unless conflict warnings are required)
- Print/report pages

## Suggested Implementation Pattern
Use the same pattern already adopted in the project:
1. Add a lightweight endpoint (`live-signature` or `live-section`).
2. Put static route before dynamic routes when needed.
3. Add page root attributes:
   - `data-live-url`
   - `data-live-signature`
4. Poll every 10s with visibility-aware checks.
5. Reload or replace section only when signature changes.

## Suggested Next Steps
1. Implement realtime for `admin/sections/teaching-loads`.
2. Implement realtime for `admin/subjects/index`.
3. Reuse shared polling utility if multiple pages adopt the same behavior.
