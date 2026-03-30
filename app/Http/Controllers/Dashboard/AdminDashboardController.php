<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\JhsAdmission;
use App\Models\ShsAdmission;
use App\Models\Admission;
use App\Models\Section;
use App\Models\Subject;
use App\Models\GradeEntry;
use App\Models\AdminGradeEditLog;
use App\Models\Event;
use App\Models\Announcement;

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

        // Unified recent logs for key dashboard activities
        $formatName = static function (?string $firstName, ?string $lastName): string {
            return trim(($firstName ?? '') . ' ' . ($lastName ?? '')) ?: 'N/A';
        };

        $formatActor = static function ($user) use ($formatName): string {
            return $user ? $formatName($user->first_name, $user->last_name) : 'System';
        };

        $gradeSubmissionLogs = GradeEntry::with([
                'student:id,first_name,last_name',
                'creator:id,first_name,last_name',
                'section:id,name',
            ])
            ->whereNotNull('submitted_at')
            ->orderByDesc('submitted_at')
            ->take(120)
            ->get()
            ->groupBy(function ($entry) {
                $sectionKey = $entry->section_id ?? 'no-section';
                $submitterKey = $entry->created_by ?? 'no-submitter';
                $minuteKey = $entry->submitted_at?->format('Y-m-d H:i') ?? 'no-time';

                return $sectionKey . '|' . $submitterKey . '|' . $minuteKey;
            })
            ->map(function ($entries) use ($formatName, $formatActor) {
                $entry = $entries->sortByDesc('submitted_at')->first();
                $entryCount = $entries->count();

                $studentName = $entry->student
                    ? $formatName($entry->student->first_name, $entry->student->last_name)
                    : 'Student';

                $teacherName = $entry->creator
                    ? $formatName($entry->creator->first_name, $entry->creator->last_name)
                    : null;

                $description = $entry->section && !empty($entry->section->name)
                    ? 'Submitted grade entries for section ' . $entry->section->name
                    : ($teacherName
                        ? 'Submitted grade entries by ' . $teacherName
                        : 'Submitted grade entries for ' . $studentName);

                if ($entryCount > 1) {
                    $description .= ' (' . $entryCount . ' entries)';
                }

                return [
                    'title' => 'Grades Submitted',
                    'description' => $description,
                    'actor' => $formatActor($entry->creator),
                    'time' => $entry->submitted_at,
                    'status' => 'Submitted',
                    'status_class' => 'pending',
                ];
            })
            ->sortByDesc('time')
            ->values();

        $gradeApprovalLogs = GradeEntry::with([
                'student:id,first_name,last_name',
                'creator:id,first_name,last_name',
                'approver:id,first_name,last_name',
                'section:id,name',
            ])
            ->whereNotNull('approved_at')
            ->orderByDesc('approved_at')
            ->take(120)
            ->get()
            ->groupBy(function ($entry) {
                $sectionKey = $entry->section_id ?? 'no-section';
                $approverKey = $entry->approved_by ?? 'no-approver';
                $minuteKey = $entry->approved_at?->format('Y-m-d H:i') ?? 'no-time';

                return $sectionKey . '|' . $approverKey . '|' . $minuteKey;
            })
            ->map(function ($entries) use ($formatName, $formatActor) {
                $entry = $entries->sortByDesc('approved_at')->first();
                $entryCount = $entries->count();

                $studentName = $entry->student
                    ? $formatName($entry->student->first_name, $entry->student->last_name)
                    : 'Student';

                $teacherName = $entry->creator
                    ? $formatName($entry->creator->first_name, $entry->creator->last_name)
                    : null;

                $description = $entry->section && !empty($entry->section->name)
                    ? 'Approved submitted grades for section ' . $entry->section->name
                    : ($teacherName
                        ? 'Approved submitted grades by ' . $teacherName
                        : 'Approved submitted grades for ' . $studentName);

                if ($entryCount > 1) {
                    $description .= ' (' . $entryCount . ' entries)';
                }

                return [
                    'title' => 'Grades Approved',
                    'description' => $description,
                    'actor' => $formatActor($entry->approver),
                    'time' => $entry->approved_at,
                    'status' => 'Approved',
                    'status_class' => 'active',
                ];
            })
            ->sortByDesc('time')
            ->values();

        $gradeEditLogs = AdminGradeEditLog::with([
                'admin:id,first_name,last_name',
                'teacher:id,first_name,last_name',
                'section:id,name',
            ])
            ->whereNotNull('edited_at')
            ->orderByDesc('edited_at')
            ->take(120)
            ->get()
            ->map(function ($log) use ($formatName, $formatActor) {
                $teacherName = $log->teacher
                    ? $formatName($log->teacher->first_name, $log->teacher->last_name)
                    : null;

                $description = 'Edited grade sheet';

                if ($log->section && !empty($log->section->name)) {
                    $description .= ' for section ' . $log->section->name;
                }

                if (!empty($log->subject_label)) {
                    $description .= ' (' . $log->subject_label . ')';
                }

                if (!empty($log->term)) {
                    $description .= ' - ' . $log->term;
                }

                if ($teacherName) {
                    $description .= ' by ' . $teacherName;
                }

                if ((int) $log->edited_entries_count > 0) {
                    $description .= ' (' . (int) $log->edited_entries_count . ' updated entr' . ((int) $log->edited_entries_count === 1 ? 'y' : 'ies') . ')';
                }

                return [
                    'title' => 'Grades Edited',
                    'description' => $description,
                    'actor' => $formatActor($log->admin),
                    'time' => $log->edited_at,
                    'status' => 'Updated',
                    'status_class' => 'active',
                ];
            })
            ->sortByDesc('time')
            ->values();

        $jhsApprovalLogs = JhsAdmission::with('approvedBy:id,first_name,last_name')
            ->where('status', 'approved')
            ->whereNotNull('approved_at')
            ->orderByDesc('approved_at')
            ->take(8)
            ->get()
            ->map(function ($admission) use ($formatName, $formatActor) {
                $studentName = $formatName($admission->first_name, $admission->last_name);

                return [
                    'title' => 'Enrollment Approved',
                    'description' => 'Approved JHS enrollment for ' . $studentName,
                    'actor' => $formatActor($admission->approvedBy),
                    'time' => $admission->approved_at,
                    'status' => 'Approved',
                    'status_class' => 'active',
                ];
            });

        $shsApprovalLogs = ShsAdmission::with('approvedBy:id,first_name,last_name')
            ->where('status', 'approved')
            ->whereNotNull('approved_at')
            ->orderByDesc('approved_at')
            ->take(8)
            ->get()
            ->map(function ($admission) use ($formatName, $formatActor) {
                $studentName = $formatName($admission->first_name, $admission->last_name);

                return [
                    'title' => 'Enrollment Approved',
                    'description' => 'Approved SHS enrollment for ' . $studentName,
                    'actor' => $formatActor($admission->approvedBy),
                    'time' => $admission->approved_at,
                    'status' => 'Approved',
                    'status_class' => 'active',
                ];
            });

        $recentLogs = collect()
            ->concat($gradeSubmissionLogs)
            ->concat($gradeApprovalLogs)
            ->concat($gradeEditLogs)
            ->concat($jhsApprovalLogs)
            ->concat($shsApprovalLogs)
            ->sortByDesc('time')
            ->values();

        $recentLogsPerPage = 5;
        $currentLogsPage = LengthAwarePaginator::resolveCurrentPage('logs_page');

        $recentLogsPaginated = new LengthAwarePaginator(
            $recentLogs->forPage($currentLogsPage, $recentLogsPerPage)->values(),
            $recentLogs->count(),
            $recentLogsPerPage,
            $currentLogsPage,
            [
                'path' => request()->url(),
                'pageName' => 'logs_page',
            ]
        );

        $recentLogsPaginated->appends(request()->query());

        // Upcoming school events for quick overview
        $upcomingEvents = Event::where('start_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->take(4)
            ->get();

        // Latest active announcements for dashboard highlights
        $recentAnnouncements = Announcement::active()
            ->orderByDesc('is_pinned')
            ->latest()
            ->take(3)
            ->get();

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
            'recentLogs' => $recentLogsPaginated,
            'upcomingEvents' => $upcomingEvents,
            'recentAnnouncements' => $recentAnnouncements,
        ]);
    }

    /**
     * Return live-updated dashboard section content for admin polling.
     */
    public function liveSection()
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        if ($user->account_type !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $formatName = static function (?string $firstName, ?string $lastName): string {
            return trim(($firstName ?? '') . ' ' . ($lastName ?? '')) ?: 'N/A';
        };

        $formatActor = static function ($user) use ($formatName): string {
            return $user ? $formatName($user->first_name, $user->last_name) : 'System';
        };

        $gradeSubmissionLogs = GradeEntry::with([
                'student:id,first_name,last_name',
                'creator:id,first_name,last_name',
                'section:id,name',
            ])
            ->whereNotNull('submitted_at')
            ->orderByDesc('submitted_at')
            ->take(120)
            ->get()
            ->groupBy(function ($entry) {
                $sectionKey = $entry->section_id ?? 'no-section';
                $submitterKey = $entry->created_by ?? 'no-submitter';
                $minuteKey = $entry->submitted_at?->format('Y-m-d H:i') ?? 'no-time';

                return $sectionKey . '|' . $submitterKey . '|' . $minuteKey;
            })
            ->map(function ($entries) use ($formatName, $formatActor) {
                $entry = $entries->sortByDesc('submitted_at')->first();
                $entryCount = $entries->count();

                $studentName = $entry->student
                    ? $formatName($entry->student->first_name, $entry->student->last_name)
                    : 'Student';

                $teacherName = $entry->creator
                    ? $formatName($entry->creator->first_name, $entry->creator->last_name)
                    : null;

                $description = $entry->section && !empty($entry->section->name)
                    ? 'Submitted grade entries for section ' . $entry->section->name
                    : ($teacherName
                        ? 'Submitted grade entries by ' . $teacherName
                        : 'Submitted grade entries for ' . $studentName);

                if ($entryCount > 1) {
                    $description .= ' (' . $entryCount . ' entries)';
                }

                return [
                    'title' => 'Grades Submitted',
                    'description' => $description,
                    'actor' => $formatActor($entry->creator),
                    'time' => $entry->submitted_at,
                    'status' => 'Submitted',
                    'status_class' => 'pending',
                ];
            })
            ->sortByDesc('time')
            ->values();

        $gradeApprovalLogs = GradeEntry::with([
                'student:id,first_name,last_name',
                'creator:id,first_name,last_name',
                'approver:id,first_name,last_name',
                'section:id,name',
            ])
            ->whereNotNull('approved_at')
            ->orderByDesc('approved_at')
            ->take(120)
            ->get()
            ->groupBy(function ($entry) {
                $sectionKey = $entry->section_id ?? 'no-section';
                $approverKey = $entry->approved_by ?? 'no-approver';
                $minuteKey = $entry->approved_at?->format('Y-m-d H:i') ?? 'no-time';

                return $sectionKey . '|' . $approverKey . '|' . $minuteKey;
            })
            ->map(function ($entries) use ($formatName, $formatActor) {
                $entry = $entries->sortByDesc('approved_at')->first();
                $entryCount = $entries->count();

                $studentName = $entry->student
                    ? $formatName($entry->student->first_name, $entry->student->last_name)
                    : 'Student';

                $teacherName = $entry->creator
                    ? $formatName($entry->creator->first_name, $entry->creator->last_name)
                    : null;

                $description = $entry->section && !empty($entry->section->name)
                    ? 'Approved submitted grades for section ' . $entry->section->name
                    : ($teacherName
                        ? 'Approved submitted grades by ' . $teacherName
                        : 'Approved submitted grades for ' . $studentName);

                if ($entryCount > 1) {
                    $description .= ' (' . $entryCount . ' entries)';
                }

                return [
                    'title' => 'Grades Approved',
                    'description' => $description,
                    'actor' => $formatActor($entry->approver),
                    'time' => $entry->approved_at,
                    'status' => 'Approved',
                    'status_class' => 'active',
                ];
            })
            ->sortByDesc('time')
            ->values();

        $gradeEditLogs = AdminGradeEditLog::with([
                'admin:id,first_name,last_name',
                'teacher:id,first_name,last_name',
                'section:id,name',
            ])
            ->whereNotNull('edited_at')
            ->orderByDesc('edited_at')
            ->take(120)
            ->get()
            ->map(function ($log) use ($formatName, $formatActor) {
                $teacherName = $log->teacher
                    ? $formatName($log->teacher->first_name, $log->teacher->last_name)
                    : null;

                $description = 'Edited grade sheet';

                if ($log->section && !empty($log->section->name)) {
                    $description .= ' for section ' . $log->section->name;
                }

                if (!empty($log->subject_label)) {
                    $description .= ' (' . $log->subject_label . ')';
                }

                if (!empty($log->term)) {
                    $description .= ' - ' . $log->term;
                }

                if ($teacherName) {
                    $description .= ' by ' . $teacherName;
                }

                if ((int) $log->edited_entries_count > 0) {
                    $description .= ' (' . (int) $log->edited_entries_count . ' updated entr' . ((int) $log->edited_entries_count === 1 ? 'y' : 'ies') . ')';
                }

                return [
                    'title' => 'Grades Edited',
                    'description' => $description,
                    'actor' => $formatActor($log->admin),
                    'time' => $log->edited_at,
                    'status' => 'Updated',
                    'status_class' => 'active',
                ];
            })
            ->sortByDesc('time')
            ->values();

        $jhsApprovalLogs = JhsAdmission::with('approvedBy:id,first_name,last_name')
            ->where('status', 'approved')
            ->whereNotNull('approved_at')
            ->orderByDesc('approved_at')
            ->take(8)
            ->get()
            ->map(function ($admission) use ($formatName, $formatActor) {
                $studentName = $formatName($admission->first_name, $admission->last_name);

                return [
                    'title' => 'Enrollment Approved',
                    'description' => 'Approved JHS enrollment for ' . $studentName,
                    'actor' => $formatActor($admission->approvedBy),
                    'time' => $admission->approved_at,
                    'status' => 'Approved',
                    'status_class' => 'active',
                ];
            });

        $shsApprovalLogs = ShsAdmission::with('approvedBy:id,first_name,last_name')
            ->where('status', 'approved')
            ->whereNotNull('approved_at')
            ->orderByDesc('approved_at')
            ->take(8)
            ->get()
            ->map(function ($admission) use ($formatName, $formatActor) {
                $studentName = $formatName($admission->first_name, $admission->last_name);

                return [
                    'title' => 'Enrollment Approved',
                    'description' => 'Approved SHS enrollment for ' . $studentName,
                    'actor' => $formatActor($admission->approvedBy),
                    'time' => $admission->approved_at,
                    'status' => 'Approved',
                    'status_class' => 'active',
                ];
            });

        $recentLogs = collect()
            ->concat($gradeSubmissionLogs)
            ->concat($gradeApprovalLogs)
            ->concat($gradeEditLogs)
            ->concat($jhsApprovalLogs)
            ->concat($shsApprovalLogs)
            ->sortByDesc('time')
            ->values();

        $recentLogsPerPage = 5;
        $currentLogsPage = max((int) request('logs_page', 1), 1);

        $recentLogsPaginated = new LengthAwarePaginator(
            $recentLogs->forPage($currentLogsPage, $recentLogsPerPage)->values(),
            $recentLogs->count(),
            $recentLogsPerPage,
            $currentLogsPage,
            [
                'path' => route('dashboard.admin'),
                'pageName' => 'logs_page',
            ]
        );

        $recentLogsPaginated->appends(request()->query());

        $upcomingEvents = Event::where('start_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->take(4)
            ->get();

        $recentAnnouncements = Announcement::active()
            ->orderByDesc('is_pinned')
            ->latest()
            ->take(3)
            ->get();

        return response()->json([
            'html' => view('dashboards.partials.admin-live-section', [
                'recentLogs' => $recentLogsPaginated,
                'upcomingEvents' => $upcomingEvents,
                'recentAnnouncements' => $recentAnnouncements,
            ])->render(),
            'generated_at' => now()->toIso8601String(),
        ]);
    }
}

