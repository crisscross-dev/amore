<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\JhsAdmission;
use App\Models\ShsAdmission;
use App\Models\Admission;
use App\Models\Section;
use App\Models\Subject;
use App\Models\GradeEntry;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Get authenticated user
        $user = Auth::user();

        // Check if user is an admin
        if ($user->account_type !== 'admin') {
            abort(403, 'Unauthorized access to admin dashboard');
        }

        // Get current school year (July to June)
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $currentSchoolYear = $currentMonth >= 7 ? $currentYear : $currentYear - 1;
        $lastSchoolYear = $currentSchoolYear - 1;

        // Total Enrolled Students (count active students from users table)
        $totalStudents = User::where('account_type', 'student')
            ->whereIn('status', ['approved', 'active'])
            ->count();

        // Students from last year (for comparison)
        $lastYearStudents = User::where('account_type', 'student')
            ->whereIn('status', ['approved', 'active'])
            ->whereYear('created_at', '<=', $lastSchoolYear)
            ->count();

        // Also count from admissions tables for reference
        $approvedJhs = JhsAdmission::where('status', 'approved')->count();
        $approvedShs = ShsAdmission::where('status', 'approved')->count();
        $totalApprovedAdmissions = $approvedJhs + $approvedShs;

        // Calculate percentage change
        $studentPercentChange = 0;
        if ($lastYearStudents > 0) {
            $studentPercentChange = round((($totalStudents - $lastYearStudents) / $lastYearStudents) * 100, 1);
        }

        // Pending Admissions (from all sources)
        $pendingJhs = JhsAdmission::where('status', 'pending')->count();
        $pendingShs = ShsAdmission::where('status', 'pending')->count();
        $pendingAdmissions = Admission::where('status', 'pending')->count();
        
        // Also check for student users pending approval (students only, not faculty)
        $pendingStudentUsers = User::where('account_type', 'student')
            ->whereIn('status', ['pending', 'for_approval'])
            ->count();
        
        $totalPendingAdmissions = $pendingJhs + $pendingShs + $pendingAdmissions + $pendingStudentUsers;

        // Active Faculty
        $activeFaculty = User::where('account_type', 'faculty')
            ->whereIn('status', ['approved', 'active'])
            ->count();

        // Pending Faculty
        $pendingFaculty = User::where('account_type', 'faculty')
            ->whereIn('status', ['pending', 'for_approval'])
            ->count();

        // Total Sections
        $totalSections = Section::count();

        // Sections without assigned teachers - removed since sections table doesn't have teacher_id
        $unassignedSections = 0;

        // System Alerts
        // Missing grades - students without any grade entries
        $studentsWithoutGrades = User::where('account_type', 'student')
            ->whereIn('status', ['approved', 'active'])
            ->whereDoesntHave('gradeEntries')
            ->count();

        // Unassigned subjects - removed since subjects table doesn't have faculty_id
        $unassignedSubjects = 0;

        // Overdue approvals - pending admissions older than 7 days
        $overdueAdmissions = JhsAdmission::where('status', 'pending')
            ->where('created_at', '<', now()->subDays(7))
            ->count() + 
            ShsAdmission::where('status', 'pending')
            ->where('created_at', '<', now()->subDays(7))
            ->count();

        // Create stats array for additional data
        $stats = [
            'approved' => $totalStudents,
            'pending' => $totalPendingAdmissions,
            'jhs_approved' => $approvedJhs,
            'shs_approved' => $approvedShs,
        ];

        // Return admin dashboard view with all statistics
        return view('dashboards.admin', [
            'user' => $user,
            'totalStudents' => $totalStudents,
            'studentPercentChange' => $studentPercentChange,
            'currentSchoolYear' => $currentSchoolYear . '-' . ($currentSchoolYear + 1),
            'totalPendingAdmissions' => $totalPendingAdmissions,
            'activeFaculty' => $activeFaculty,
            'pendingFaculty' => $pendingFaculty,
            'totalSections' => $totalSections,
            'unassignedSections' => $unassignedSections,
            'studentsWithoutGrades' => $studentsWithoutGrades,
            'unassignedSubjects' => $unassignedSubjects,
            'overdueAdmissions' => $overdueAdmissions,
            'stats' => $stats,
        ]);
    }
}

