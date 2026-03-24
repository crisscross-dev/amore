<div class="profile-sidebar">
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
            <div class="nav-label">Announcements</div>
        </a>

        <div class="nav-section-title">Academic</div>

        <a href="{{ route('admin.admissions.index') }}" class="nav-link {{ Request::routeIs('admin.admissions.index') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="nav-label">Admissions</div>
        </a>

        <a href="{{ route('admin.subjects.index') }}" class="nav-link {{ Request::routeIs('admin.subjects.*') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-book"></i>
            </div>
            <div class="nav-label">Subjects</div>
        </a>

        <a href="{{ route('admin.grade-approvals.index') }}" class="nav-link {{ Request::routeIs('admin.grade-approvals.*') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="nav-label">Grade Approvals</div>
        </a>

        <a href="{{ route('admin.sections.index') }}" class="nav-link {{ Request::routeIs('admin.sections.*') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="nav-label">Sections</div>
        </a>

        <a href="{{ route('admin.school-years.index') }}" class="nav-link {{ Request::routeIs('admin.school-years.*') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="nav-label">School Years</div>
        </a>

        <a href="{{ route('admin.enrollments.index') }}" class="nav-link {{ Request::routeIs('admin.enrollments.*') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="nav-label">Enrollments</div>
        </a>

        <div class="nav-section-title">Staff And Accounts</div>

        <a href="{{ route('admin.accounts.manage') }}" class="nav-link {{ Request::routeIs('admin.accounts.manage') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-users-cog"></i>
            </div>
            <div class="nav-label">Accounts</div>
        </a>

        <a href="{{ route('admin.faculty-assignments.index') }}" class="nav-link {{ Request::routeIs('admin.faculty-assignments.*') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-user-tag"></i>
            </div>
            <div class="nav-label">Faculty Assignments</div>
        </a>

        <a href="{{ route('admin.faculty-positions.index') }}" class="nav-link {{ Request::routeIs('admin.faculty-positions.*') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="nav-label">Manage Positions</div>
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