@extends('layouts.app-student')

@section('title', 'Student Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Student Dashboard CSS -->
@vite(['resources/css/layouts/dashboard-roles/dashboard-student.css'])

<style>
    .student-dashboard-meta {
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.9);
    }

    .student-dashboard-contact {
        font-size: 0.82rem;
        color: rgba(255, 255, 255, 0.78);
    }

    .quick-stats-row .stat-card {
        border-radius: 16px;
        padding: 1.05rem;
        box-shadow: 0 8px 20px rgba(2, 6, 23, 0.08);
        border: 1px solid rgba(22, 101, 52, 0.1);
        height: 100%;
    }

    .quick-stats-row .stat-card-link {
        display: block;
        text-decoration: none;
        color: inherit;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .quick-stats-row .stat-card-link:hover {
        transform: translateY(-2px);
        border-color: rgba(22, 101, 52, 0.25);
        box-shadow: 0 12px 24px rgba(2, 6, 23, 0.12);
    }

    .quick-stats-row .stat-icon {
        font-size: 1.2rem;
        color: #166534;
        width: 1.4rem;
        text-align: center;
    }

    .quick-stats-row .stat-label {
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        margin-bottom: 0.3rem;
    }

    .quick-stats-row .stat-value {
        font-size: 1.55rem;
        font-weight: 700;
        color: #166534;
        line-height: 1.1;
    }

    .quick-stats-row .stat-subtext {
        margin-top: 0.25rem;
        font-size: 0.82rem;
        color: #6b7280;
    }

    .dashboard-notifications-card .card-header .notification-badge {
        border-radius: 999px;
        background: #fee2e2;
        color: #b91c1c;
        font-size: 0.78rem;
        font-weight: 700;
        padding: 0.18rem 0.56rem;
    }

    .notification-item {
        border: none;
        border-bottom: 1px solid #e2e8f0;
        padding: 0.9rem 1rem;
    }

    .notification-item:last-child {
        border-bottom: none;
    }

    .notification-icon {
        width: 18px;
        min-width: 18px;
        margin-top: 0.2rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
    }

    .notification-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 0.45rem;
        background: #22c55e;
    }

    .notification-dot.read {
        background: #cbd5e1;
    }

    .notification-meta {
        font-size: 0.78rem;
        color: #64748b;
    }

    .announcement-side-card .list-group-item {
        border: none;
        border-bottom: 1px solid #e2e8f0;
        padding: 0.85rem 1rem;
        transition: background-color 0.2s ease, border-left-color 0.2s ease;
    }

    .announcement-side-card {
        display: flex;
        flex-direction: column;
    }

    .announcement-side-card .card-body {
        flex: 1 1 auto;
        min-height: 0;
    }

    .announcement-side-card .announcement-scroll-area {
        height: 100%;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .announcement-side-card .announcement-scroll-area::-webkit-scrollbar {
        width: 7px;
    }

    .announcement-side-card .announcement-scroll-area::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 999px;
    }

    .announcement-side-card .announcement-item {
        box-sizing: border-box;
        border-left: 3px solid transparent;
    }

    .announcement-side-card .announcement-item.announcement-unread {
        background: #f0fdf4;
        border-left-color: #22c55e;
    }

    .announcement-side-card .announcement-title {
        font-size: 1.08rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.2rem;
        line-height: 1.25;
    }

    .announcement-side-card .announcement-date {
        display: inline-flex;
        align-items: center;
        gap: 0.32rem;
        font-size: 0.75rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        margin-bottom: 0.35rem;
    }

    .announcement-side-card .announcement-description {
        font-size: 0.92rem;
        color: #475569;
        margin-bottom: 0;
        line-height: 1.45;
        display: -webkit-box;
        line-clamp: 2;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .announcement-side-card .announcement-new {
        display: inline-flex;
        align-items: center;
        margin-left: 0.4rem;
        border-radius: 999px;
        background: #dcfce7;
        color: #166534;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.08rem 0.4rem;
    }

    .announcement-side-card .list-group-item:last-child {
        border-bottom: none;
    }

</style>

<div class="dashboard-container">
    <div class="container-fluid px-4">
        <div class="row">
            <!-- Main Dashboard Content -->
            <main class="col-12">
                <!-- Mobile Profile (Hidden on Desktop) -->
                <div class="d-md-none mobile-profile mb-4">
                    <div class="text-center">
                        <img src="{{ asset('uploads/profile_picture/' . Auth::user()->profile_picture) }}"
                            alt="Profile Picture"
                            class="rounded-circle mb-3 border border-3 border-white"
                            width="80"
                            height="80">

                        <h5 class="text-white mb-1">{{ Auth::user()->first_name ?? 'Student' }} {{ Auth::user()->last_name ?? 'Name' }}</h5>
                        <p class="text-white-50 small mb-3">
                            {{ Auth::user()->custom_id ?? '2024-0001' }} | {{ Auth::user()->grade_level ?? 'Grade Level' }}
                        </p>

                        <!-- Mobile Navigation -->
                        <div class="row g-2">
                            <div class="col-4">
                                <a href="{{ route('student.grades.index') }}" class="btn mobile-nav-btn w-100">
                                    <i class="fas fa-chart-bar d-block mb-1"></i>
                                    <small>Grades</small>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="" class="btn mobile-nav-btn w-100">
                                    <i class="fas fa-bullhorn d-block mb-1"></i>
                                    <small>News</small>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('profile.edit') }}" class="btn mobile-nav-btn w-100">
                                    <i class="fas fa-user-edit d-block mb-1"></i>
                                    <small>Profile</small>
                                </a>
                            </div>
                        </div>

                        <hr class="bg-white opacity-25 my-3">

                        <button
                            class="btn logout-btn w-100"
                            onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </button>

                        <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>

                <!-- Welcome Message -->
                <div class="welcome-card">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <h4 class="mb-1">
                                <i class="fas fa-hand-wave me-2"></i>
                                Hello, {{ Auth::user()->first_name ?? 'Student' }} {{ Auth::user()->last_name ?? '' }}
                            </h4>
                            <p class="mb-1 student-dashboard-meta">
                                <i class="fas fa-calendar-day me-1"></i>{{ $dateLabel }}
                                <span class="mx-2">•</span>
                                <i class="fas fa-book-open me-1"></i>{{ $semesterLabel }}
                            </p>
                            <p class="mb-0 student-dashboard-contact">
                                <i class="fas fa-envelope me-1"></i>{{ Auth::user()->email }}
                                @if(Auth::user()->contact_number)
                                <span class="ms-3"><i class="fas fa-phone me-1"></i>{{ Auth::user()->contact_number }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <a href="{{ route('profile.edit') }}" class="btn btn-warning btn-lg">
                                <i class="fas fa-user-edit me-2"></i>Edit Profile
                            </a>
                            <div class="d-none d-xl-block">
                                <i class="fas fa-school" style="font-size: 3rem; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 my-4 quick-stats-row">
                    <div class="col-md-4">
                        <a href="{{ route('student.registration-form.download') }}" class="bg-white stat-card stat-card-link" data-search-target="download registration form pdf file">
                            <div class="stat-label">Quick Action</div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-file-pdf stat-icon"></i>
                                <div class="stat-value" style="font-size: 1.2rem;">Download Registration Form</div>
                            </div>
                            <div class="stat-subtext">PDF format</div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('student.registration-form.preview') }}" target="_blank" class="bg-white stat-card stat-card-link" data-search-target="preview registration form browser">
                            <div class="stat-label">Quick Action</div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-eye stat-icon"></i>
                                <div class="stat-value" style="font-size: 1.2rem;">Preview Registration Form</div>
                            </div>
                            <div class="stat-subtext">View in browser</div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('student.enrollment.index') }}" class="bg-white stat-card stat-card-link" data-search-target="enrollment status requirements">
                            <div class="stat-label">Quick Action</div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-user-plus stat-icon"></i>
                                <div class="stat-value" style="font-size: 1.2rem;">Enrollment Status</div>
                            </div>
                            <div class="stat-subtext">{{ $stats['enrollment_status'] ?? 'Not enrolled' }}</div>
                        </a>
                    </div>
                </div>
                
                <div class="row g-4 mb-4">
                    <div class="col-lg-8">
                        <div class="activity-card dashboard-notifications-card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="fas fa-bell me-2"></i>
                                    Notifications
                                </span>
                                <span class="notification-badge">{{ $unreadNotifications }} unread</span>
                            </div>
                            <div class="card-body p-0">
                                @if($notifications->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($notifications as $notification)
                                    <a href="{{ $notification['url'] }}" class="list-group-item list-group-item-action notification-item">
                                        <div class="d-flex align-items-start gap-2">
                                            <span class="notification-icon text-{{ $notification['color'] }}">
                                                <i class="fas fa-{{ $notification['icon'] }}"></i>
                                            </span>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                                                    <h6 class="mb-1 fw-semibold text-dark">
                                                        <span class="notification-dot {{ $notification['is_unread'] ? '' : 'read' }}"></span>
                                                        {{ $notification['title'] }}
                                                    </h6>
                                                    <small class="notification-meta">{{ $notification['date']->format('M d, Y h:i A') }}</small>
                                                </div>
                                                <p class="mb-0 text-muted small">{{ $notification['description'] }}</p>
                                            </div>
                                        </div>
                                    </a>
                                    @endforeach
                                </div>
                                @if($notifications->hasPages())
                                <div class="p-3 border-top bg-white">
                                    {{ $notifications->onEachSide(1)->links() }}
                                </div>
                                @endif
                                @else
                                <div class="p-4 text-center text-muted">
                                    <i class="fas fa-bell-slash fa-3x mb-3 opacity-50"></i>
                                    <p class="mb-0">No notifications available right now.</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="activity-card announcement-side-card h-100">
                            <div class="card-header d-flex align-items-center">
                                <span>
                                    <i class="fas fa-bullhorn me-2"></i>
                                    Announcement
                                </span>
                            </div>
                            <div class="card-body p-0">
                                @if($announcements->count() > 0)
                                <div class="announcement-scroll-area">
                                    <div class="list-group list-group-flush">
                                    @foreach($announcements->take(4) as $announcement)
                                    @php
                                    $isUnreadAnnouncement = $announcement->created_at->gte(now()->subDays(2));
                                    @endphp
                                    <a href="{{ route('announcements.show', $announcement->id) }}" class="list-group-item list-group-item-action announcement-item {{ $isUnreadAnnouncement ? 'announcement-unread' : '' }}">
                                        <h6 class="announcement-title">
                                            {{ $announcement->title }}
                                            @if($isUnreadAnnouncement)
                                            <span class="announcement-new">New</span>
                                            @endif
                                        </h6>
                                        <small class="announcement-date">
                                            <i class="fas fa-calendar-day"></i>
                                            {{ $announcement->created_at->format('M d, Y') }}
                                        </small>
                                        <p class="announcement-description">{{ \Str::limit(strip_tags($announcement->content), 130) }}</p>
                                    </a>
                                    @endforeach
                                    </div>
                                </div>
                                @else
                                <div class="p-4 text-center text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                    <p class="mb-0">No announcements yet. Check back later!</p>
                                </div>
                                @endif
                            </div>
                            <div class="card-footer bg-white border-0 border-top p-3 text-center">
                                <a href="{{ route('announcements.index') }}" class="btn btn-sm btn-outline-success px-4">
                                    View All
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
@if(session('popupToast'))
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="popupToast" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                {{ session('popupToast') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
@endif

<!-- First Login Password Change Modal -->
@if($first_login)
<div class="modal fade" id="firstLoginModal" tabindex="-1" aria-labelledby="firstLoginModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-dark" id="firstLoginModalLabel">
                    <i class="fas fa-key me-2"></i>Change Your Password
                </h5>
            </div>
            <div class="modal-body">
                <p class="mb-3 text-dark">Welcome to Amore Academy! For security reasons, you must change your temporary password before proceeding.</p>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Your temporary password was generated using your last name and birth month/day. Please update it to something more secure.
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                    <i class="fas fa-user-edit me-2"></i>Change Password
                </a>
            </div>
        </div>
    </div>
</div>
@endif

<div id="studentDashboardFlags" data-first-login="{{ $first_login ? '1' : '0' }}" class="d-none" aria-hidden="true"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var flagsEl = document.getElementById('studentDashboardFlags');
        var isFirstLogin = flagsEl && flagsEl.dataset.firstLogin === '1';
        var toastEl = document.getElementById('popupToast');
        if (toastEl) {
            var toast = new bootstrap.Toast(toastEl, {
                delay: 3000
            });
            toast.show();
        }

        var eventColorElements = document.querySelectorAll('[data-event-color]');
        eventColorElements.forEach(function(el) {
            var color = el.getAttribute('data-event-color');
            if (!color) {
                return;
            }

            if (el.classList.contains('event-color-chip')) {
                el.style.backgroundColor = color + '20';
                var icon = el.querySelector('.event-color-icon');
                if (icon) {
                    icon.style.color = color;
                }
            }

            if (el.classList.contains('event-type-badge')) {
                el.style.backgroundColor = color + '20';
                el.style.color = color;
            }
        });

        // Show first login modal if applicable
        if (isFirstLogin) {
            var firstLoginModal = new bootstrap.Modal(document.getElementById('firstLoginModal'), {
                backdrop: 'static',
                keyboard: false
            });
            firstLoginModal.show();
        }
    });
</script>

@endsection