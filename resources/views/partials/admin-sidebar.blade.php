<div class="profile-sidebar"
    data-badge-live-url="{{ route('admin.sidebar-badges') }}"
    data-badge-signature="">
    <!-- Navigation Menu -->
    <nav class="admin-nav-menu">
        <a href="{{ route('dashboard.admin') }}" class="nav-link {{ Request::routeIs('dashboard.admin') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-tachometer-alt"></i>
            </div>
            <div class="nav-label">Dashboard</div>
        </a>

        <div class="nav-section-title">General</div>

        <a href="{{ route('calendar.index') }}" class="nav-link {{ Request::routeIs('calendar.*') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="nav-label">Calendar</div>
        </a>

        <a href="{{ route('announcements.index') }}" class="nav-link {{ Request::routeIs('announcements.*') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-bullhorn"></i>
            </div>
            <div class="nav-label">Announcement</div>
        </a>

        <div class="nav-section-title">Academic</div>

        <a href="{{ route('admin.admissions.index') }}" class="nav-link {{ Request::routeIs('admin.admissions.index') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="nav-label">Admission</div>
            <span class="nav-badge d-none js-admin-sidebar-badge" data-badge-key="admissions_pending">0</span>
        </a>

        <a href="{{ route('admin.subjects.index') }}" class="nav-link {{ Request::routeIs('admin.subjects.*') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-book"></i>
            </div>
            <div class="nav-label">Subject</div>
        </a>

        <a href="{{ route('admin.grade-approvals.index') }}" class="nav-link {{ Request::routeIs('admin.grade-approvals.*') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="nav-label">Grade Approval</div>
            <span class="nav-badge d-none js-admin-sidebar-badge" data-badge-key="grade_approvals_pending">0</span>
        </a>

        <a href="{{ route('admin.sections.index') }}" class="nav-link {{ Request::routeIs('admin.sections.*') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="nav-label">Section</div>
        </a>

        <a href="{{ route('admin.school-years.index') }}" class="nav-link {{ Request::routeIs('admin.school-years.*') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="nav-label">School Year</div>
        </a>

        <a href="{{ route('admin.enrollments.index') }}" class="nav-link {{ Request::routeIs('admin.enrollments.*') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="nav-label">Enrollment</div>
            <span class="nav-badge d-none js-admin-sidebar-badge" data-badge-key="enrollments_pending">0</span>
        </a>

        <div class="nav-section-title">Staff And Accounts</div>

        <a href="{{ route('admin.accounts.manage') }}" class="nav-link {{ Request::routeIs('admin.accounts.manage') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-users-cog"></i>
            </div>
            <div class="nav-label">Account</div>
            <span class="nav-badge d-none js-admin-sidebar-badge" data-badge-key="accounts_pending">0</span>
        </a>

        <a href="{{ route('admin.faculty-assignments.index') }}" class="nav-link {{ Request::routeIs('admin.faculty-assignments.*') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-user-tag"></i>
            </div>
            <div class="nav-label">Faculty Assignment</div>
        </a>

        <a href="{{ route('admin.faculty-positions.index') }}" class="nav-link {{ Request::routeIs('admin.faculty-positions.*') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="nav-label">Manage Position</div>
        </a>

        <div class="nav-section-title">Analytics</div>

        <a href="{{ route('admin.reports.index') }}" class="nav-link {{ Request::routeIs('admin.reports.*') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="nav-label">Reports</div>
        </a>

        <div class="nav-section-title">Settings</div>

        <a href="{{ route('profile.edit') }}" class="nav-link {{ Request::routeIs('profile.edit') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-user-edit"></i>
            </div>
            <div class="nav-label">Edit Profile</div>
        </a>

        <div class="nav-link logout-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <div class="nav-icon">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <div class="nav-label">Logout</div>
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </nav>
</div>

<style>
    .has-app-shell {
        --app-sidebar-width: 260px;
    }

    .profile-sidebar {
        padding: 1rem !important;
    }

    .admin-nav-menu {
        display: flex;
        flex-direction: column;
        gap: 1px;
    }

    .admin-nav-menu .nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 12px;
        border-radius: 6px;
        transition: all 0.2s ease;
        background: transparent;
        color: rgba(255, 255, 255, 0.85);
        text-decoration: none;
        cursor: pointer;
        position: relative;
        width: 100%;
        box-sizing: border-box;
    }

    .admin-nav-menu .nav-link::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 3px;
        height: 0;
        background: white;
        border-radius: 0 3px 3px 0;
        transition: height 0.2s ease;
    }

    .admin-nav-menu .nav-link:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        padding-left: 16px;
    }

    .admin-nav-menu .nav-link:hover::before {
        height: 60%;
    }

    .admin-nav-menu .nav-link.active {
        background: rgba(255, 255, 255, 0.15);
        color: white;
        font-weight: 500;
        padding-left: 16px;
    }

    .admin-nav-menu .nav-link.active::before {
        height: 70%;
    }

    .admin-nav-menu .nav-icon {
        width: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .admin-nav-menu .nav-icon i {
        font-size: 0.95rem;
    }

    .admin-nav-menu .nav-label {
        font-size: 0.875rem;
        flex: 1;
    }

    .admin-nav-menu .nav-badge {
        min-width: 20px;
        height: 20px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.72rem;
        font-weight: 700;
        line-height: 1;
        padding: 0 6px;
        background: #ffc107;
        color: #0f172a;
        box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.18) inset;
    }

    .admin-nav-menu .logout-link {
        background: rgba(220, 53, 69, 0.15);
        margin-top: 4px;
    }

    .admin-nav-menu .logout-link:hover {
        background: rgba(220, 53, 69, 0.25);
    }

    .admin-nav-menu .nav-section-title {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: rgba(255, 255, 255, 0.5);
        padding: 8px 12px 4px;
        margin-top: 4px;
        font-weight: 600;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var sidebar = document.querySelector('.profile-sidebar[data-badge-live-url]');
        if (!sidebar) {
            return;
        }

        var liveUrl = sidebar.getAttribute('data-badge-live-url') || '';
        var liveSignature = sidebar.getAttribute('data-badge-signature') || '';
        var requestInFlight = false;
        var pollTimer = null;

        function applyBadges(badges) {
            if (!badges || typeof badges !== 'object') {
                return;
            }

            Object.keys(badges).forEach(function(key) {
                var rawValue = badges[key];
                var value = Number.isFinite(Number(rawValue)) ? Math.max(0, parseInt(rawValue, 10)) : 0;
                var displayText = value > 99 ? '99+' : String(value);

                document.querySelectorAll('.js-admin-sidebar-badge[data-badge-key="' + key + '"]').forEach(function(node) {
                    node.textContent = displayText;
                    node.classList.toggle('d-none', value < 1);
                });
            });
        }

        function fetchBadgeSnapshot() {
            if (!liveUrl || requestInFlight) {
                return;
            }

            requestInFlight = true;

            fetch(liveUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(function(response) {
                    if (!response.ok) {
                        return null;
                    }

                    return response.json();
                })
                .then(function(payload) {
                    if (!payload) {
                        return;
                    }

                    if (payload.badges) {
                        applyBadges(payload.badges);
                    }

                    if (payload.signature) {
                        liveSignature = payload.signature;
                        sidebar.setAttribute('data-badge-signature', liveSignature);
                    }
                })
                .catch(function(error) {
                    console.debug('Admin sidebar badge polling skipped:', error);
                })
                .finally(function() {
                    requestInFlight = false;
                });
        }

        if (!liveUrl) {
            return;
        }

        fetchBadgeSnapshot();

        pollTimer = window.setInterval(function() {
            if (!document.hidden) {
                fetchBadgeSnapshot();
            }
        }, 10000);

        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                fetchBadgeSnapshot();
            }
        });

        window.addEventListener('beforeunload', function() {
            if (pollTimer) {
                clearInterval(pollTimer);
                pollTimer = null;
            }
        }, {
            once: true
        });
    });
</script>