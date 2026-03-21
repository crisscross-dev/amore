<div class="profile-sidebar">
    @php
    $currentUser = Auth::user();
    $positionName = optional($currentUser->facultyPosition)->name;
    $department = $currentUser->department ?? 'No department assigned';
    $accountType = strtolower((string) ($currentUser->account_type ?? ''));
    $positionCode = strtoupper((string) optional($currentUser->facultyPosition)->code);
    $isAdmin = $accountType === 'admin';
    $isFaculty = $accountType === 'faculty';
    $isDepartmentHead = $isFaculty && $positionCode === 'DEPARTMENT_HEAD';
    @endphp

    <!-- Profile Section -->
    <div class="text-center mb-4">
        <!-- Profile Picture -->
        <div class="profile-pic-container">
            <img src="{{ asset('uploads/profile_picture/' . Auth::user()->profile_picture) }}"
                alt="Profile Picture"
                class="rounded-circle profile-pic"
                width="120"
                height="120"
                style="object-fit: cover;">
            <div class="profile-badge faculty-badge">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
        </div>

        <!-- Profile Info -->
        <h5 class="text-white mb-2 mt-3">{{ Auth::user()->first_name ?? 'Faculty' }} {{ Auth::user()->last_name ?? 'Name' }}</h5>
        <p class="text-white-50 small mb-2">
            <i class="fas fa-id-badge me-1"></i>{{ Auth::user()->custom_id ?? 'FAC-0001' }}
        </p>
        <p class="text-white-50 small mb-2">
            <i class="fas fa-user-tie me-1"></i>{{ $positionName ?? 'Faculty' }}
        </p>
        <p class="text-white-50 small mb-0">
            <i class="fas fa-building me-1"></i>{{ $department }}
        </p>
    </div>

    <hr class="bg-white opacity-25 my-3">

    <!-- Navigation Menu -->
    <nav class="faculty-nav-menu">
        <a href="{{ route('dashboard.faculty') }}" class="nav-link {{ Request::routeIs('dashboard.faculty') ? 'active' : '' }}">
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

        @if($isDepartmentHead)
        <a href="{{ route('department-head.subjects.index') }}" class="nav-link {{ Request::routeIs('department-head.subjects.*') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-book"></i>
            </div>
            <div class="nav-label">Manage Subjects</div>
        </a>
        @elseif($isFaculty)
        <a href="{{ route('faculty.subjects.index') }}" class="nav-link {{ Request::routeIs('faculty.subjects.*') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-book"></i>
            </div>
            <div class="nav-label">Manage Subjects</div>
        </a>

        <a href="{{ route('faculty.sections.index') }}" class="nav-link {{ Request::routeIs('faculty.sections.*') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="nav-label">View Sections</div>
        </a>
        @endif

        @if($isDepartmentHead || $isFaculty)
        <a href="{{ route('faculty.grades.index') }}" class="nav-link {{ (Request::routeIs('faculty.grades.*') && !Request::routeIs('faculty.grades.import.*')) ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="nav-label">Manage Grades</div>
        </a>

        <a href="{{ route('faculty.grades.import.create') }}" class="nav-link {{ Request::routeIs('faculty.grades.import.*') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-file-import"></i>
            </div>
            <div class="nav-label">Import Grades</div>
        </a>
        @endif

        @if($isAdmin)
        <a href="{{ route('admin.reports.index') }}" class="nav-link {{ Request::routeIs('admin.reports.*') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="nav-label">Reports</div>
        </a>
        @endif

        <button type="button" class="nav-link logout-link w-100 text-start border-0" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <div class="nav-icon">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <div class="nav-label">Logout</div>
        </button>

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

    .faculty-nav-menu {
        display: flex;
        flex-direction: column;
        gap: 1px;
    }

    .faculty-nav-menu .nav-link {
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

    .faculty-nav-menu .nav-link::before {
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

    .faculty-nav-menu .nav-link:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        padding-left: 16px;
    }

    .faculty-nav-menu .nav-link:hover::before {
        height: 60%;
    }

    .faculty-nav-menu .nav-link.active {
        background: rgba(255, 255, 255, 0.15);
        color: white;
        font-weight: 500;
        padding-left: 16px;
    }

    .faculty-nav-menu .nav-link.active::before {
        height: 70%;
    }

    .faculty-nav-menu .nav-icon {
        width: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .faculty-nav-menu .nav-icon i {
        font-size: 0.95rem;
    }

    .faculty-nav-menu .nav-label {
        font-size: 0.875rem;
        flex: 1;
    }

    .faculty-nav-menu .logout-link {
        background: rgba(220, 53, 69, 0.15);
        margin-top: 4px;
    }

    .faculty-nav-menu button.nav-link {
        width: 100%;
        font: inherit;
    }

    .faculty-nav-menu .logout-link:hover {
        background: rgba(220, 53, 69, 0.25);
    }

    .faculty-nav-menu .nav-section-title {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: rgba(255, 255, 255, 0.5);
        padding: 8px 12px 4px;
        margin-top: 4px;
        font-weight: 600;
    }

    /* Profile Section Styles */
    .profile-pic-container {
        position: relative;
        display: inline-block;
    }

    .profile-pic {
        border: 4px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s ease;
    }

    .profile-pic:hover {
        transform: scale(1.05);
    }

    .profile-badge.faculty-badge {
        position: absolute;
        bottom: 5px;
        right: 5px;
        background: #17a2b8;
        border: 3px solid #fff;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .profile-badge.faculty-badge i {
        color: white;
        font-size: 14px;
    }
</style>