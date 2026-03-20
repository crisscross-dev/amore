<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcement;
use App\Models\StudentEnrollment;
use App\Models\Event;
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

        // Get stats
        $stats = [
            'total_subjects' => 8, // Placeholder - update when subjects are implemented
            'pending_enrollments' => StudentEnrollment::where('student_id', $user->id)->where('status', 'pending')->count(),
            'approved_enrollments' => StudentEnrollment::where('student_id', $user->id)->where('status', 'approved')->count(),
            'announcements_count' => $recentAnnouncements->count(),
        ];

        // Return student dashboard view
        return view('dashboards.student', [
            'user' => $user,
            'first_login' => $user->first_login,
            'recentActivities' => $recentActivities,
            'upcomingEvents' => $upcomingEvents,
            'stats' => $stats,
            'announcements' => $recentAnnouncements,
        ]);
    }
}
