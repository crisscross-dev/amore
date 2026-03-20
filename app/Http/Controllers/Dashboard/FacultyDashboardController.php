<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\GradeEntry;

class FacultyDashboardController extends Controller
{
    /**
     * Display the faculty dashboard.
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

        // Check if user is a faculty
        if ($user->account_type !== 'faculty') {
            abort(403, 'Unauthorized access to faculty dashboard');
        }

        // Get recent announcements (last 5)
        $recentAnnouncements = Announcement::where(function($query) {
                $query->whereIn('target_audience', ['faculty', 'all', 'public']);
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

        // Get upcoming events (next 3)
        $upcomingEvents = Event::where('start_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->take(3)
            ->get();

        // Get recent grade entries by this faculty
        $recentGrades = GradeEntry::where('created_by', $user->id)
            ->with(['student', 'subject'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Format activities for timeline
        $recentActivities = $recentGrades->map(function($grade) {
            return [
                'type' => 'grade',
                'icon' => $grade->status === 'approved' ? 'check-double' : ($grade->status === 'pending' ? 'hourglass-half' : 'file-alt'),
                'iconColor' => $grade->status === 'approved' ? 'success' : ($grade->status === 'pending' ? 'warning' : 'info'),
                'title' => $grade->subject ? "Graded {$grade->subject->name}" : 'Grade Submitted',
                'description' => $grade->student ? "{$grade->student->firstname} {$grade->student->lastname} - Grade: {$grade->grade_value}" : "Grade: {$grade->grade_value}",
                'date' => $grade->created_at,
                'status' => ucfirst($grade->status),
                'statusColor' => $grade->status === 'approved' ? 'success' : ($grade->status === 'pending' ? 'warning' : 'info'),
            ];
        });

        // Get stats
        $stats = [
            'announcements_count' => $recentAnnouncements->count(),
        ];

        // Return faculty dashboard view
        return view('dashboards.faculty', [
            'user' => $user,
            'announcements' => $recentAnnouncements,
            'upcomingEvents' => $upcomingEvents,
            'recentActivities' => $recentActivities,
            'stats' => $stats,
        ]);
    }
}
