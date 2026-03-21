<div class="profile-sidebar">
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
            <div class="profile-badge">
                <i class="fas fa-user-graduate"></i>
            </div>
        </div>

        <!-- Profile Info -->
        <h5 class="text-white mb-2 mt-3">{{ Auth::user()->first_name ?? 'Student' }} {{ Auth::user()->last_name ?? 'Name' }}</h5>
        <p class="text-white-50 small mb-2">
            <i class="fas fa-id-badge me-1"></i>{{ Auth::user()->custom_id ?? 'STD-0001' }}
        </p>
        <p class="text-white-50 small mb-0">
            <i class="fas fa-graduation-cap me-1"></i>{{ Auth::user()->grade_level ?? 'Grade Level' }}
        </p>
    </div>

    <hr class="bg-white opacity-25 my-3">

    <!-- Navigation Menu -->
    <nav class="student-nav-menu">
        <a href="{{ route('dashboard.student') }}" class="nav-link {{ Request::routeIs('dashboard.student') ? 'active' : '' }}">
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

        <a href="{{ route('student.subjects.index') }}" class="nav-link {{ Request::routeIs('student.subjects.*') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-book"></i>
            </div>
            <div class="nav-label">My Subjects</div>
        </a>

        <a href="{{ route('student.grades.index') }}" class="nav-link {{ Request::routeIs('student.grades.*') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="nav-label">My Grades</div>
        </a>

        <a href="{{ route('student.enrollment.index') }}" class="nav-link {{ Request::routeIs('student.enrollment.*') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="nav-label">Enrollment</div>
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

    .student-nav-menu {
        display: flex;
        flex-direction: column;
        gap: 1px;
    }

    .student-nav-menu .nav-link {
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

    .student-nav-menu .nav-link::before {
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

    .student-nav-menu .nav-link:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        padding-left: 16px;
    }

    .student-nav-menu .nav-link:hover::before {
        height: 60%;
    }

    .student-nav-menu .nav-link.active {
        background: rgba(255, 255, 255, 0.15);
        color: white;
        font-weight: 500;
        padding-left: 16px;
    }

    .student-nav-menu .nav-link.active::before {
        height: 70%;
    }

    .student-nav-menu .nav-icon {
        width: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .student-nav-menu .nav-icon i {
        font-size: 0.95rem;
    }

    .student-nav-menu .nav-label {
        font-size: 0.875rem;
        flex: 1;
    }

    .student-nav-menu .logout-link {
        background: rgba(220, 53, 69, 0.15);
        margin-top: 4px;
    }

    .student-nav-menu .logout-link:hover {
        background: rgba(220, 53, 69, 0.25);
    }

    .student-nav-menu .nav-section-title {
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

    .profile-badge {
        position: absolute;
        bottom: 5px;
        right: 5px;
        background: #0d6efd;
        border: 3px solid #fff;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .profile-badge i {
        color: white;
        font-size: 14px;
    }
</style>