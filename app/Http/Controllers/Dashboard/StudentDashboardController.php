<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcement;
use App\Models\GradeEntry;
use App\Models\StudentEnrollment;
use App\Models\Event;
use App\Models\SectionSubjectTeacher;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class StudentDashboardController extends Controller
{
    /**
     * Display the student dashboard.
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

        $today = now();
        $monthNumber = (int) $today->format('n');
        $semesterLabel = ($monthNumber >= 8 || $monthNumber <= 1)
            ? 'First Semester'
            : 'Second Semester';
        $dateLabel = $today->format('l, M d, Y');

        // Check if user is a student
        if ($user->account_type !== 'student') {
            abort(403, 'Unauthorized access to student dashboard');
        }

        // Get recent announcements (last 5)
        $recentAnnouncements = Announcement::where(function($query) {
                $query->whereIn('target_audience', ['students', 'all', 'public']);
            })
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->with('createdBy')
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get student's enrollment history
        $enrollmentHistory = StudentEnrollment::where('student_id', $user->id)
            ->with(['schoolYear', 'section'])
            ->latest()
            ->take(3)
            ->get();

        $approvedGrades = GradeEntry::query()
            ->with(['subject', 'approver'])
            ->where('student_id', $user->id)
            ->where(function ($query) {
                $query->where('status', 'approved')
                    ->orWhere(function ($approvedQuery) {
                        $approvedQuery->whereNotNull('approved_by')
                            ->whereNotNull('approved_at');
                    });
            })
            ->orderByDesc('approved_at')
            ->orderByDesc('created_at')
            ->take(6)
            ->get();

        $latestEnrollment = StudentEnrollment::where('student_id', $user->id)
            ->with(['documents'])
            ->latest()
            ->first();

        // Get upcoming events (next 3)
        $upcomingEvents = Event::where('start_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->take(3)
            ->get();

        // Combine activities for timeline
        $recentActivities = collect();

        // Add enrollments to activities
        foreach ($enrollmentHistory as $enrollment) {
            $statusText = ucfirst($enrollment->status);
            $recentActivities->push([
                'type' => 'enrollment',
                'title' => 'Enrollment ' . $statusText,
                'description' => ($enrollment->schoolYear->year_name ?? 'School Year') . ' - ' . $enrollment->enrolling_grade_level,
                'date' => $enrollment->created_at,
                'icon' => 'user-plus',
                'color' => $enrollment->status === 'approved' ? 'success' : ($enrollment->status === 'pending' ? 'warning' : 'danger'),
            ]);
        }

        // Sort by date and take latest 5
        $recentActivities = $recentActivities->sortByDesc('date')->take(5);

        $activeSectionId = $user->section_id ?: optional($latestEnrollment)->section_id;
        $subjectCount = $activeSectionId
            ? SectionSubjectTeacher::query()
                ->where('section_id', $activeSectionId)
                ->distinct('subject_id')
                ->count('subject_id')
            : 0;

        $requirementsCount = $latestEnrollment ? $latestEnrollment->documents->count() : 0;
        $enrollmentStatus = $latestEnrollment ? ucfirst((string) $latestEnrollment->status) : 'Not enrolled';

        // Get stats
        $stats = [
            'total_subjects' => $subjectCount,
            'requirements_count' => $requirementsCount,
            'enrollment_status' => $enrollmentStatus,
            'pending_enrollments' => StudentEnrollment::where('student_id', $user->id)->where('status', 'pending')->count(),
            'approved_enrollments' => StudentEnrollment::where('student_id', $user->id)->where('status', 'approved')->count(),
            'announcements_count' => $recentAnnouncements->count(),
        ];

        $notifications = collect();

        foreach ($approvedGrades as $gradeEntry) {
            $approvedDate = $gradeEntry->approved_at ?? $gradeEntry->created_at;
            $approverName = trim((string) ((optional($gradeEntry->approver)->first_name ?? '') . ' ' . (optional($gradeEntry->approver)->last_name ?? '')));
            if ($approverName === '') {
                $approverName = 'Admin';
            }

            $notifications->push([
                'type' => 'grade_approved',
                'title' => 'Grade Approved by ' . $approverName,
                'description' => ($gradeEntry->subject->name ?? 'Subject')
                    . ' • ' . ($gradeEntry->term ?? 'Term')
                    . ' • Grade: ' . number_format((float) ($gradeEntry->grade_value ?? 0), 2),
                'date' => $approvedDate,
                'icon' => 'check-circle',
                'color' => 'success',
                'url' => route('student.grades.index'),
                'is_unread' => $approvedDate ? $approvedDate->gte(now()->subDays(7)) : false,
            ]);
        }

        foreach ($recentAnnouncements as $announcement) {
            $notifications->push([
                'type' => 'announcement',
                'title' => $announcement->title,
                'description' => Str::limit(strip_tags((string) $announcement->content), 95),
                'date' => $announcement->created_at,
                'icon' => 'bullhorn',
                'color' => 'primary',
                'url' => route('announcements.show', $announcement->id),
                'is_unread' => $announcement->created_at->gte(now()->subDays(7)),
            ]);
        }

        foreach ($enrollmentHistory as $enrollment) {
            $statusText = strtolower((string) $enrollment->status);
            $statusTitle = 'Enrollment Update';
            if ($statusText === 'approved') {
                $statusTitle = 'Enrollment Approved';
            } elseif ($statusText === 'pending') {
                $statusTitle = 'Enrollment Pending Review';
            } elseif ($statusText === 'rejected') {
                $statusTitle = 'Enrollment Rejected';
            }

            $notifications->push([
                'type' => 'enrollment',
                'title' => $statusTitle,
                'description' => ($enrollment->schoolYear->year_name ?? 'School Year')
                    . ' • '
                    . (string) $enrollment->enrolling_grade_level,
                'date' => $enrollment->created_at,
                'icon' => 'user-plus',
                'color' => $enrollment->status === 'approved' ? 'success' : ($enrollment->status === 'pending' ? 'warning' : 'danger'),
                'url' => route('student.enrollment.index'),
                'is_unread' => $enrollment->status === 'pending' || $enrollment->created_at->gte(now()->subDays(7)),
            ]);
        }

        foreach ($upcomingEvents as $event) {
            $notifications->push([
                'type' => 'event',
                'title' => $event->title,
                'description' => $event->start_date->format('M d, Y') . ($event->is_all_day ? ' (All Day)' : ' at ' . $event->start_date->format('g:i A')),
                'date' => $event->start_date,
                'icon' => 'calendar-day',
                'color' => 'info',
                'url' => route('calendar.index'),
                'is_unread' => $event->start_date->lte(now()->addDays(7)),
            ]);
        }

        $notifications = $notifications
            ->sortByDesc('date')
            ->values();

        $unreadNotifications = $notifications->where('is_unread', true)->count();

        $notificationsPerPage = 5;
        $notificationsPageName = 'notifications_page';
        $currentNotificationsPage = LengthAwarePaginator::resolveCurrentPage($notificationsPageName);
        $currentNotificationsItems = $notifications
            ->slice(($currentNotificationsPage - 1) * $notificationsPerPage, $notificationsPerPage)
            ->values();

        $notifications = new LengthAwarePaginator(
            $currentNotificationsItems,
            $notifications->count(),
            $notificationsPerPage,
            $currentNotificationsPage,
            [
                'path' => request()->url(),
                'pageName' => $notificationsPageName,
                'query' => request()->query(),
            ]
        );

        // Return student dashboard view
        return view('dashboards.student', [
            'user' => $user,
            'first_login' => $user->first_login,
            'recentActivities' => $recentActivities,
            'upcomingEvents' => $upcomingEvents,
            'stats' => $stats,
            'announcements' => $recentAnnouncements,
            'notifications' => $notifications,
            'unreadNotifications' => $unreadNotifications,
            'dateLabel' => $dateLabel,
            'semesterLabel' => $semesterLabel,
        ]);
    }
}
