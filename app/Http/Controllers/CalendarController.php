<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CalendarController extends Controller
{
    /**
     * Display calendar for authenticated users (admin, faculty, student).
     */
    public function index($year = null, $month = null)
    {
        $user = Auth::user();
        
        // Default to current month and cast to integers
        $year = (int) ($year ?? now()->year);
        $month = (int) ($month ?? now()->month);

        // Get events for the month (all users can view)
        $events = Event::forMonth($year, $month)->get();
        $upcomingEvents = Event::upcoming(10)->get();

        // Get recent announcements (active, not expired)
        $announcements = Announcement::active()
            ->latest()
            ->take(5)
            ->get();

        // Determine view based on role
        $view = match($user->account_type) {
            'admin' => 'admin.calendar',
            'faculty' => 'faculty.calendar',
            'student' => 'student.calendar',
            default => abort(403, 'Unauthorized access')
        };

        return view($view, [
            'user' => $user,
            'events' => $events,
            'upcomingEvents' => $upcomingEvents,
            'announcements' => $announcements,
            'currentYear' => $year,
            'currentMonth' => $month,
            'canEdit' => $user->account_type === 'admin' // Permission flag
        ]);
    }

    /**
     * Display all events in a printable format.
     */
    public function allEvents(Request $request)
    {
        $user = Auth::user();
        
        // Get filter parameters
        $filterYear = $request->get('year', now()->year);
        $filterMonth = $request->get('month');
        
        // Build query
        $query = Event::orderBy('start_date', 'asc');
        
        // Filter by year
        $query->whereYear('start_date', $filterYear);
        
        // Filter by month if specified
        if ($filterMonth) {
            $query->whereMonth('start_date', $filterMonth);
        }
        
        $events = $query->get();
        
        // Group events by month for better organization
        $eventsByMonth = $events->groupBy(function($event) {
            return Carbon::parse($event->start_date)->format('F Y');
        });
        
        // Get available years for filter dropdown
        $availableYears = Event::selectRaw('YEAR(start_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
        
        // Add current year if not in list
        if (!in_array(now()->year, $availableYears)) {
            $availableYears[] = now()->year;
            rsort($availableYears);
        }
        
        // Determine view based on role
        $view = match($user->account_type) {
            'admin' => 'admin.calendar.all',
            'faculty' => 'faculty.calendar.all',
            'student' => 'student.calendar.all',
            default => abort(403, 'Unauthorized access')
        };
        
        return view($view, [
            'events' => $events,
            'eventsByMonth' => $eventsByMonth,
            'filterYear' => $filterYear,
            'filterMonth' => $filterMonth,
            'availableYears' => $availableYears,
            'canEdit' => $user->account_type === 'admin'
        ]);
    }

    /**
     * Show create event form (admin only).
     */
    public function create()
    {
        if (Auth::user()->account_type !== 'admin') {
            abort(403, 'Only administrators can create events');
        }

        return view('admin.calendar.create');
    }

    /**
     * Store a new event (admin only, traditional form).
     */
    public function store(Request $request)
    {
        if (Auth::user()->account_type !== 'admin') {
            abort(403, 'Only administrators can create events');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'end_time' => 'nullable|date_format:H:i',
            'is_all_day' => 'boolean'
        ]);

        // Custom validation: end datetime must not be earlier than or equal to start datetime
        if (!empty($validated['end_date']) && !empty($validated['start_time']) && !empty($validated['end_time'])) {
            $startDateTime = Carbon::parse($validated['start_date'] . ' ' . $validated['start_time']);
            $endDateTime = Carbon::parse($validated['end_date'] . ' ' . $validated['end_time']);
            
            if ($endDateTime->lt($startDateTime)) {
                return back()->withErrors(['end_time' => 'End date/time cannot be earlier than start date/time.'])->withInput();
            }
            
            // Check if same date and same time
            if ($validated['start_date'] === $validated['end_date'] && $validated['start_time'] === $validated['end_time']) {
                return back()->withErrors(['end_time' => 'End time cannot be the same as start time on the same day.'])->withInput();
            }
        } elseif (!empty($validated['start_time']) && !empty($validated['end_time']) && empty($validated['end_date'])) {
            // Same day event - compare times (end date defaults to start date)
            if ($validated['end_time'] < $validated['start_time']) {
                return back()->withErrors(['end_time' => 'End time cannot be earlier than start time on the same day.'])->withInput();
            }
            // Check if same time on same day
            if ($validated['start_time'] === $validated['end_time']) {
                return back()->withErrors(['end_time' => 'End time cannot be the same as start time on the same day.'])->withInput();
            }
        }

        $validated['created_by'] = Auth::id();
        $validated['is_all_day'] = $request->has('is_all_day') ? true : false;
        $validated['event_type'] = 'meeting'; // Default event type
        $validated['color'] = '#198754'; // Default green color

        // Combine date and time into datetime fields
        if (!$validated['is_all_day'] && !empty($validated['start_time'])) {
            $validated['start_date'] = Carbon::parse($validated['start_date'] . ' ' . $validated['start_time']);
        } else {
            $validated['start_date'] = Carbon::parse($validated['start_date'])->startOfDay();
        }

        if (!empty($validated['end_date'])) {
            if (!$validated['is_all_day'] && !empty($validated['end_time'])) {
                $validated['end_date'] = Carbon::parse($validated['end_date'] . ' ' . $validated['end_time']);
            } else {
                $validated['end_date'] = Carbon::parse($validated['end_date'])->endOfDay();
            }
        } elseif (!$validated['is_all_day'] && !empty($validated['end_time'])) {
            // End date defaults to start date if not provided
            $validated['end_date'] = Carbon::parse($validated['start_date']->format('Y-m-d') . ' ' . $validated['end_time']);
        }

        // Remove separate time fields (not in database)
        unset($validated['start_time'], $validated['end_time']);

        Event::create($validated);

        return redirect()->route('calendar.index')->with('success', 'Event created successfully!');
    }

    /**
     * Show event details for a specific day (all roles can view).
     */
    public function show($year, $month, $day)
    {
        $user = Auth::user();
        
        // Cast to integers for Carbon
        $year = (int) $year;
        $month = (int) $month;
        $day = (int) $day;
        
        $startOfDay = Carbon::create($year, $month, $day)->startOfDay();
        $endOfDay = Carbon::create($year, $month, $day)->endOfDay();
        
        $events = Event::whereBetween('start_date', [$startOfDay, $endOfDay])
            ->orderBy('start_date')
            ->get();

        $canEdit = $user->account_type === 'admin';

        // Determine view based on role
        $view = match($user->account_type) {
            'admin' => 'admin.calendar.show',
            'faculty' => 'faculty.calendar.show',
            'student' => 'student.calendar.show',
            default => abort(403, 'Unauthorized access')
        };

        return view($view, compact('events', 'year', 'month', 'day', 'canEdit'));
    }

    /**
     * Show edit event form (admin only).
     */
    public function edit(Event $event)
    {
        if (Auth::user()->account_type !== 'admin') {
            abort(403, 'Only administrators can edit events');
        }

        return view('admin.calendar.edit', compact('event'));
    }

    /**
     * Update an existing event (admin only, traditional form).
     */
    public function update(Request $request, Event $event)
    {
        if (Auth::user()->account_type !== 'admin') {
            abort(403, 'Only administrators can update events');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'end_time' => 'nullable|date_format:H:i',
            'event_type' => 'required|in:meeting,deadline,exam,holiday,sports',
            'is_all_day' => 'boolean'
        ]);

        // Custom validation: end datetime must not be earlier than or equal to start datetime
        if (!empty($validated['end_date']) && !empty($validated['start_time']) && !empty($validated['end_time'])) {
            $startDateTime = Carbon::parse($validated['start_date'] . ' ' . $validated['start_time']);
            $endDateTime = Carbon::parse($validated['end_date'] . ' ' . $validated['end_time']);
            
            if ($endDateTime->lt($startDateTime)) {
                return back()->withErrors(['end_time' => 'End date/time cannot be earlier than start date/time.'])->withInput();
            }
            
            // Check if same date and same time
            if ($validated['start_date'] === $validated['end_date'] && $validated['start_time'] === $validated['end_time']) {
                return back()->withErrors(['end_time' => 'End time cannot be the same as start time on the same day.'])->withInput();
            }
        } elseif (!empty($validated['start_time']) && !empty($validated['end_time']) && empty($validated['end_date'])) {
            // Same day event - compare times (end date defaults to start date)
            if ($validated['end_time'] < $validated['start_time']) {
                return back()->withErrors(['end_time' => 'End time cannot be earlier than start time on the same day.'])->withInput();
            }
            // Check if same time on same day
            if ($validated['start_time'] === $validated['end_time']) {
                return back()->withErrors(['end_time' => 'End time cannot be the same as start time on the same day.'])->withInput();
            }
        }

        $validated['is_all_day'] = $request->has('is_all_day') ? true : false;
        $validated['color'] = $this->getDefaultColor($validated['event_type']);

        // Combine date and time into datetime fields
        if (!$validated['is_all_day'] && !empty($validated['start_time'])) {
            $validated['start_date'] = Carbon::parse($validated['start_date'] . ' ' . $validated['start_time']);
        } else {
            $validated['start_date'] = Carbon::parse($validated['start_date'])->startOfDay();
        }

        if (!empty($validated['end_date'])) {
            if (!$validated['is_all_day'] && !empty($validated['end_time'])) {
                $validated['end_date'] = Carbon::parse($validated['end_date'] . ' ' . $validated['end_time']);
            } else {
                $validated['end_date'] = Carbon::parse($validated['end_date'])->endOfDay();
            }
        } elseif (!$validated['is_all_day'] && !empty($validated['end_time'])) {
            // End date defaults to start date if not provided
            $validated['end_date'] = Carbon::parse($validated['start_date']->format('Y-m-d') . ' ' . $validated['end_time']);
        }

        // Remove separate time fields (not in database)
        unset($validated['start_time'], $validated['end_time']);
        
        $event->update($validated);

        return redirect()->route('calendar.index')->with('success', 'Event updated successfully!');
    }

    /**
     * Delete an event (admin only, traditional form).
     */
    public function destroy(Event $event)
    {
        if (Auth::user()->account_type !== 'admin') {
            abort(403, 'Only administrators can delete events');
        }

        $event->delete();
        
        return redirect()->route('calendar.index')->with('success', 'Event deleted successfully!');
    }

    /**
     * Get default color for event type.
     */
    private function getDefaultColor($type)
    {
        return match($type) {
            'meeting' => '#007bff',
            'deadline' => '#dc3545',
            'exam' => '#6f42c1',
            'holiday' => '#fd7e14',
            'sports' => '#198754',
            default => '#6c757d',
        };
    }
}
