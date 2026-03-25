<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Dashboard\StudentDashboardController;
use App\Http\Controllers\Dashboard\FacultyDashboardController;
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Faculty\SubjectBrowseController;
use App\Http\Controllers\Faculty\SectionBrowseController;
use App\Http\Controllers\Admin\GradeApprovalController;
use App\Http\Controllers\Faculty\GradeController as FacultyGradeController;
use App\Http\Controllers\Faculty\GradeImportController;
use App\Http\Controllers\Student\GradeController as StudentGradeController;
use App\Http\Controllers\Student\SubjectController as StudentSubjectController;
use App\Http\Controllers\Faculty\DepartmentHeadSubjectController;
use App\Http\Controllers\Shared\ProfileController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\SectionAssignmentController;
use App\Http\Controllers\Admin\StudentSectionController;
use App\Http\Controllers\Admin\BulkStudentSectionController;
use App\Http\Controllers\Admin\SchoolYearController;
use App\Http\Controllers\Admin\EnrollmentApprovalController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Student\EnrollmentController;
use App\Models\Announcement;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $publicAnnouncements = Announcement::where('target_audience', 'public')
        ->active()
        ->orderBy('is_pinned', 'desc')
        ->orderBy('created_at', 'desc')
        ->take(6)
        ->get();

    return view('welcome', compact('publicAnnouncements'));
})->name('welcome');

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login/auth', [LoginController::class, 'login'])->name('loginAuth');

// Registration Routes
Route::get('/register', [RegistrationController::class, 'showRegistration'])->name('register');
Route::post('/register', [RegistrationController::class, 'register'])->name('register.store');

// LRN Validation API Route (for AJAX validation)
Route::post('/api/validate-lrn', [RegistrationController::class, 'validateLRN'])->name('api.validate-lrn');

// Password Reset Routes
Route::get('/forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'store'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');

// Protected Routes (require authentication)
Route::middleware(['auth'])->group(function () {
    // Role-based dashboard routing
    Route::get('/dashboard', function () {
        $user = request()->user();

        // Route to appropriate dashboard based on account type
        return match ($user->account_type) {
            'admin' => app(AdminDashboardController::class)->index(),
            'faculty' => app(FacultyDashboardController::class)->index(),
            default => app(StudentDashboardController::class)->index()
        };
    })->name('dashboard');

    // Individual dashboard routes (optional direct access)
    Route::get('/dashboard/student', [StudentDashboardController::class, 'index'])->name('dashboard.student');
    Route::get('/dashboard/faculty', [FacultyDashboardController::class, 'index'])->name('dashboard.faculty');
    Route::get('/dashboard/admin', [AdminDashboardController::class, 'index'])->name('dashboard.admin');

    // Calendar Routes (accessible by all authenticated users)
    Route::prefix('calendar')->name('calendar.')->group(function () {
        // CRUD operations (admin only) - Must be BEFORE dynamic routes
        Route::get('/create', [CalendarController::class, 'create'])->name('create');
        Route::post('/store', [CalendarController::class, 'store'])->name('store');
        Route::get('/all', [CalendarController::class, 'allEvents'])->name('all');
        Route::get('/show/{year}/{month}/{day}', [CalendarController::class, 'show'])->name('show');
        Route::get('/{event}/edit', [CalendarController::class, 'edit'])->name('edit');
        Route::put('/{event}', [CalendarController::class, 'update'])->name('update');
        Route::delete('/{event}', [CalendarController::class, 'destroy'])->name('destroy');

        // View calendar (all roles) - Must be LAST because of optional params
        Route::get('/{year?}/{month?}', [CalendarController::class, 'index'])->name('index');
    });

    // Announcement Routes (Admin only)
    Route::prefix('announcements')->name('announcements.')->group(function () {
        Route::get('/', [AnnouncementController::class, 'index'])->name('index');
        Route::get('/create', [AnnouncementController::class, 'create'])->name('create');
        Route::post('/', [AnnouncementController::class, 'store'])->name('store');
        Route::get('/{announcement}', [AnnouncementController::class, 'show'])->name('show');
        Route::get('/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('edit');
        Route::put('/{announcement}', [AnnouncementController::class, 'update'])->name('update');
        Route::delete('/{announcement}', [AnnouncementController::class, 'destroy'])->name('destroy');
        Route::patch('/{announcement}/pin', [AnnouncementController::class, 'pin'])->name('pin');
    });

    // Admin Admission Approval Routes
    Route::prefix('admin/admissions')->name('admin.admissions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdmissionController::class, 'index'])->name('index');
        Route::get('/approved', [\App\Http\Controllers\Admin\AdmissionController::class, 'approved'])->name('approved');
        Route::get('/{type}/{id}', [\App\Http\Controllers\Admin\AdmissionController::class, 'show'])->name('show');
        Route::patch('/{type}/{id}/approve', [\App\Http\Controllers\Admin\AdmissionController::class, 'approve'])->name('approve');
        Route::patch('/{type}/{id}/reject', [\App\Http\Controllers\Admin\AdmissionController::class, 'reject'])->name('reject');
        Route::delete('/{type}/{id}', [\App\Http\Controllers\Admin\AdmissionController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-action', [\App\Http\Controllers\Admin\AdmissionController::class, 'bulkAction'])->name('bulk-action');
    });

    // Admin Account Management Routes
    Route::prefix('admin/accounts')->name('admin.accounts.')->group(function () {
        Route::get('/manage', [\App\Http\Controllers\Admin\AdminController::class, 'manageAccounts'])->name('manage');
        Route::patch('/{user}/approve', [\App\Http\Controllers\Admin\AdminController::class, 'approve'])->name('approve');
        Route::patch('/{user}/reject', [\App\Http\Controllers\Admin\AdminController::class, 'reject'])->name('reject');
        Route::get('/{user}', [\App\Http\Controllers\Admin\AdminController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [\App\Http\Controllers\Admin\AdminController::class, 'edit'])->name('edit');
        Route::put('/{user}', [\App\Http\Controllers\Admin\AdminController::class, 'update'])->name('update');
        Route::delete('/{user}', [\App\Http\Controllers\Admin\AdminController::class, 'destroy'])->name('destroy');
    });

    // Faculty Position Management
    Route::prefix('admin/faculty-positions')->name('admin.faculty-positions.')->middleware('admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\FacultyPositionController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\FacultyPositionController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\FacultyPositionController::class, 'store'])->name('store');
        Route::get('/{position}/edit', [\App\Http\Controllers\Admin\FacultyPositionController::class, 'edit'])->name('edit');
        Route::put('/{position}', [\App\Http\Controllers\Admin\FacultyPositionController::class, 'update'])->name('update');
        Route::delete('/{position}', [\App\Http\Controllers\Admin\FacultyPositionController::class, 'destroy'])->name('destroy');
    });

    // Section Management (Admin only)
    Route::prefix('admin/sections')->name('admin.sections.')->middleware('admin')->group(function () {
        Route::get('/', [SectionController::class, 'index'])->name('index');
        Route::get('/create', [SectionController::class, 'create'])->name('create');
        Route::post('/', [SectionController::class, 'store'])->name('store');
        Route::get('/{section}', [SectionController::class, 'show'])->name('show');
        Route::get('/{section}/edit', [SectionController::class, 'edit'])->name('edit');
        Route::put('/{section}', [SectionController::class, 'update'])->name('update');
        Route::delete('/{section}', [SectionController::class, 'destroy'])->name('destroy');
    });

    Route::post('/admin/sections/{section}/adviser', [SectionAssignmentController::class, 'updateAdviser'])
        ->middleware('admin')
        ->name('admin.sections.assign-adviser');
    Route::post('/admin/sections/{section}/subjects/{subject}/teacher', [SectionAssignmentController::class, 'updateSubjectTeacher'])
        ->middleware('admin')
        ->name('admin.sections.assign-subject-teacher');
    Route::post('/admin/sections/{section}/subjects/teachers', [SectionAssignmentController::class, 'updateSubjectTeachers'])
        ->middleware('admin')
        ->name('admin.sections.assign-subject-teachers');

    Route::prefix('admin/teaching-loads')->name('admin.teaching-loads.')->middleware('admin')->group(function () {
        Route::get('/', [SectionController::class, 'teachingLoads'])->name('index');
        Route::post('/', [SectionAssignmentController::class, 'storeTeachingLoad'])->name('store');
        Route::put('/{teachingAssignment}', [SectionAssignmentController::class, 'updateTeachingLoad'])->name('update');
        Route::delete('/{teachingAssignment}', [SectionAssignmentController::class, 'destroyTeachingLoad'])->name('destroy');
    });

    // Student Section assignment
    Route::post('/admin/students/{user}/assign-section', [StudentSectionController::class, 'assign'])
        ->name('admin.students.assign-section');
    Route::post('/admin/students/bulk-assign-section', [BulkStudentSectionController::class, 'bulkAssign'])
        ->name('admin.students.bulk-assign-section');
    Route::get('/admin/sections/by-grade', [StudentSectionController::class, 'listByGrade'])
        ->name('admin.sections.by-grade');

    // Faculty Assignment Management
    Route::prefix('admin/faculty-assignments')->name('admin.faculty-assignments.')->middleware('admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\FacultyAssignmentController::class, 'index'])->name('index');
        Route::get('/{user}/edit', [\App\Http\Controllers\Admin\FacultyAssignmentController::class, 'edit'])->name('edit');
        Route::put('/{user}', [\App\Http\Controllers\Admin\FacultyAssignmentController::class, 'update'])->name('update');
    });

    // Subject Management
    Route::prefix('admin/subjects')->name('admin.subjects.')->middleware('admin')->group(function () {
        Route::get('/', [SubjectController::class, 'index'])->name('index');
        Route::get('/create', [SubjectController::class, 'create'])->name('create');
        Route::post('/', [SubjectController::class, 'store'])->name('store');
        Route::get('/{subject}/edit', [SubjectController::class, 'edit'])->name('edit');
        Route::put('/{subject}', [SubjectController::class, 'update'])->name('update');
        Route::delete('/{subject}', [SubjectController::class, 'destroy'])->name('destroy');
        Route::post('/import', [SubjectController::class, 'import'])->name('import');
    });

    // Faculty Subject Browse (read-only)
    Route::prefix('faculty/subjects')->name('faculty.subjects.')->group(function () {
        Route::get('/', [SubjectBrowseController::class, 'index'])->name('index');
    });

    // Faculty Section Browse (read-only)
    Route::prefix('faculty/sections')->name('faculty.sections.')->group(function () {
        Route::get('/', [SectionBrowseController::class, 'index'])->name('index');
        Route::get('/{section}', [SectionBrowseController::class, 'show'])->name('show');
    });

    Route::prefix('department-head/subjects')->name('department-head.subjects.')->middleware('department-head')->group(function () {
        Route::get('/', [DepartmentHeadSubjectController::class, 'index'])->name('index');
        Route::get('/create', [DepartmentHeadSubjectController::class, 'create'])->name('create');
        Route::post('/', [DepartmentHeadSubjectController::class, 'store'])->name('store');
        Route::get('/{subject}/edit', [DepartmentHeadSubjectController::class, 'edit'])->name('edit');
        Route::put('/{subject}', [DepartmentHeadSubjectController::class, 'update'])->name('update');
        Route::delete('/{subject}', [DepartmentHeadSubjectController::class, 'destroy'])->name('destroy');
        Route::post('/import', [DepartmentHeadSubjectController::class, 'import'])->name('import');
    });

    Route::prefix('admin/grade-approvals')->name('admin.grade-approvals.')->middleware('subject-manager')->group(function () {
        Route::get('/', [GradeApprovalController::class, 'index'])->name('index');
        Route::get('/{grade}', [GradeApprovalController::class, 'show'])->name('show');
        Route::patch('/{grade}', [GradeApprovalController::class, 'update'])->name('update');
        Route::patch('/{grade}/approve', [GradeApprovalController::class, 'approve'])->name('approve');
        Route::patch('/{grade}/reject', [GradeApprovalController::class, 'reject'])->name('reject');
    });
    // Faculty Manage Grades
    Route::prefix('faculty/grades')->name('faculty.grades.')->group(function () {
        Route::get('/', [FacultyGradeController::class, 'index'])->name('index');
        Route::get('/assignment/{assignment}', [FacultyGradeController::class, 'assignment'])->name('assignment');
        Route::patch('/assignment/{assignment}', [FacultyGradeController::class, 'updateAssignmentSheet'])->name('assignment.update');
        Route::patch('/assignment/{assignment}/upload', [FacultyGradeController::class, 'uploadAssignmentSheet'])->name('assignment.upload');
        Route::patch('/assignment/{assignment}/student/{student}', [FacultyGradeController::class, 'upsertStudentGrade'])->name('assignment.student.update');
        Route::get('/create', [FacultyGradeController::class, 'create'])->name('create');
        Route::post('/', [FacultyGradeController::class, 'store'])->name('store');
        Route::get('/{grade}/edit', [FacultyGradeController::class, 'edit'])->name('edit');
        Route::put('/{grade}', [FacultyGradeController::class, 'update'])->name('update');
        Route::delete('/{grade}', [FacultyGradeController::class, 'destroy'])->name('destroy');
        Route::patch('/{grade}/submit', [FacultyGradeController::class, 'submit'])->name('submit');

        // Import (CSV for now)
        Route::get('/import', [GradeImportController::class, 'create'])->name('import.create');
        Route::post('/import', [GradeImportController::class, 'store'])->name('import.store');
        Route::get('/import/result', function () {
            return view('faculty.grades.import_result', [
                'summary' => session('import_summary'),
            ]);
        })->name('import.result');
    });

    // Student View Grades
    Route::prefix('student/grades')->name('student.grades.')->group(function () {
        Route::get('/', [StudentGradeController::class, 'index'])->name('index');
    });

    // Student View Subjects
    Route::prefix('student/subjects')->name('student.subjects.')->middleware('student')->group(function () {
        Route::get('/', [StudentSubjectController::class, 'index'])->name('index');
    });

    // Student Enrollment Routes
    Route::prefix('student/enrollment')->name('student.enrollment.')->middleware('student')->group(function () {
        Route::get('/', [EnrollmentController::class, 'index'])->name('index');
        Route::get('/create', [EnrollmentController::class, 'create'])->name('create');
        Route::post('/', [EnrollmentController::class, 'store'])->name('store');
        Route::get('/{enrollment}', [EnrollmentController::class, 'show'])->name('show');
    });

    // Student Registration Form PDF Routes
    Route::prefix('student/registration-form')->name('student.registration-form.')->middleware('student')->group(function () {
        Route::get('/download', [\App\Http\Controllers\Student\RegistrationFormController::class, 'download'])->name('download');
        Route::get('/preview', [\App\Http\Controllers\Student\RegistrationFormController::class, 'preview'])->name('preview');
    });

    // Admin School Year Management
    Route::prefix('admin/school-years')->name('admin.school-years.')->middleware('admin')->group(function () {
        Route::get('/', [SchoolYearController::class, 'index'])->name('index');
        Route::get('/create', [SchoolYearController::class, 'create'])->name('create');
        Route::post('/', [SchoolYearController::class, 'store'])->name('store');
        Route::get('/{schoolYear}/edit', [SchoolYearController::class, 'edit'])->name('edit');
        Route::put('/{schoolYear}', [SchoolYearController::class, 'update'])->name('update');
        Route::delete('/{schoolYear}', [SchoolYearController::class, 'destroy'])->name('destroy');
        Route::post('/{schoolYear}/activate', [SchoolYearController::class, 'activate'])->name('activate');
    });

    // Admin Enrollment Approval
    Route::prefix('admin/enrollments')->name('admin.enrollments.')->middleware('admin')->group(function () {
        Route::get('/', [EnrollmentApprovalController::class, 'index'])->name('index');
        Route::get('/admissions/{admission}/review', [EnrollmentApprovalController::class, 'reviewApprovedAdmission'])->name('review-admission');
        Route::post('/admissions/{admission}/enroll', [EnrollmentApprovalController::class, 'enrollApprovedAdmission'])->name('enroll-admission');
        Route::get('/{enrollment}', [EnrollmentApprovalController::class, 'show'])->name('show');
        Route::post('/{enrollment}/approve', [EnrollmentApprovalController::class, 'approve'])->name('approve');
        Route::post('/{enrollment}/reject', [EnrollmentApprovalController::class, 'reject'])->name('reject');
    });

    // Admin Reports
    Route::prefix('admin/reports')->name('admin.reports.')->middleware('admin')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/faculty-list', [ReportController::class, 'facultyList'])->name('faculty-list');
        Route::get('/student-list', [ReportController::class, 'studentList'])->name('student-list');
        Route::get('/subject-assignments', [ReportController::class, 'subjectAssignments'])->name('subject-assignments');
        Route::get('/grades-summary', [ReportController::class, 'gradesSummary'])->name('grades-summary');
    });

    // Profile Routes
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// New Admission Routes (JHS and SHS)
Route::prefix('admissions')->name('admissions.')->group(function () {
    Route::get('/', [AdmissionController::class, 'selection'])->name('selection');
    Route::get('/jhs', [AdmissionController::class, 'jhsForm'])->name('jhs');
    Route::post('/jhs', [AdmissionController::class, 'jhsStore'])->name('jhs.store');
    Route::get('/shs', [AdmissionController::class, 'shsForm'])->name('shs');
    Route::post('/shs', [AdmissionController::class, 'shsStore'])->name('shs.store');
    Route::get('/requirements/{id}', [AdmissionController::class, 'requirements'])->name('requirements');
});

// Admission Routes
Route::get('/admission', [AdmissionController::class, 'showAdmissionForm'])->name('admission');
Route::post('/admission', [AdmissionController::class, 'storeAdmission'])->name('admission.store');
Route::get('/admission/search', [AdmissionController::class, 'searchAdmission'])->name('admission.search');
