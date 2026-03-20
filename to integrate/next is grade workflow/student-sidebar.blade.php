<div class="col-lg-3 col-md-4 d-none d-md-block mb-4">
    <div class="profile-sidebar">
        <!-- Profile Section -->
        <div class="text-center">
            <!-- Profile Picture -->
            <div class="profile-pic-container">
                <img src="{{ asset('uploads/profile_picture/' . Auth::user()->profile_picture) }}" 
                        alt="Profile Picture" 
                        class="rounded-circle profile-pic"
                        width="120"
                        height="120">
                <div class="profile-badge">
                    <i class="fas fa-star text-white" style="font-size: 12px;"></i>
                </div>
            </div>
            
            <!-- Name -->
            <h4 class="text-white mb-2 fw-bold">
                {{ Auth::user()->first_name ?? 'First Name' }} {{ Auth::user()->last_name ?? 'Last Name' }}
            </h4>
            
            <!-- Student Badge -->
            <div class="badge bg-light text-success mb-2 px-3 py-2">
                <i class="fas fa-id-badge me-1"></i>
                {{ Auth::user()->custom_id ?? 'STU-0001' }}
            </div>
            
            <!-- School ID -->
            <p class="text-white-50 small mb-0">
                
                <i class="fas fa-user-graduate me-1"></i>
                {{  Auth::user()->grade_level ?? 'Student' }}
            </p>
        </div>
        
        <hr class="bg-white opacity-25 my-4">
        
        <!-- Navigation Links -->
        <div class="d-grid gap-3">
            <a href="{{ route('dashboard.student') }}" class="btn sidebar-nav-btn text-start {{ Request::routeIs('dashboard.student') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
            
            <a href="{{ route('calendar.index') }}" class="btn sidebar-nav-btn text-start {{ Request::routeIs('calendar.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt me-2"></i>View Calendar
            </a>
            
            <a href="{{ route('announcements.index') }}" class="btn sidebar-nav-btn text-start {{ Request::routeIs('announcements.*') ? 'active' : '' }}">
                <i class="fas fa-bullhorn me-2"></i>View Announcement
            </a>

            <a href="{{ route('student.grades.index') }}" class="btn sidebar-nav-btn text-start {{ Request::routeIs('student.grades.*') ? 'active' : '' }}">
                <i class="fas fa-graduation-cap me-2"></i>View Grades
            </a>
            
            <a href="#" class="btn sidebar-nav-btn text-start">
                <i class="fas fa-book me-2"></i>View Subjects
            </a>
            
            <a href="#" class="btn sidebar-nav-btn text-start">
                <i class="fas fa-user-plus me-2"></i>Enroll
            </a>
            
            <button 
                class="btn logout-btn text-start w-100"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
            >
                <i class="fas fa-sign-out-alt me-2"></i>Logout
            </button>
            
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>
</div>