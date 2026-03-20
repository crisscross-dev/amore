<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of announcements.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Announcement::with(['createdBy', 'updatedBy'])
            ->active()
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc');

        // Filter based on user type and authentication status
        if ($user) {
            if ($user->account_type === 'student') {
                $query->whereIn('target_audience', ['public', 'all', 'students']);
            } elseif ($user->account_type === 'faculty') {
                $query->whereIn('target_audience', ['public', 'all', 'faculty']);
            }
            // Admin sees all announcements - no filter needed
        } else {
            // Guests see only 'public' and 'all' announcements
            $query->whereIn('target_audience', ['public', 'all']);
        }

        // Search functionality
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by audience
        if ($request->filled('audience')) {
            $query->where('audience', $request->audience);
        }

        $announcements = $query->paginate(10);
        
        // Determine view based on role
        $view = match($user->account_type) {
            'admin' => 'admin.announcements.index',
            'faculty' => 'faculty.announcements.index',
            'student' => 'student.announcements.index',
            default => abort(403, 'Unauthorized access')
        };

        return view($view, compact('announcements'));
    }

    /**
     * Show the form for creating a new announcement.
     */
    public function create()
    {
        return view('admin.announcements.create');
    }

    /**
     * Store a newly created announcement in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_audience' => 'required|in:public,all,students,faculty',
            'expires_at' => 'nullable|date|after:now',
            'is_pinned' => 'boolean',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);

        // Set default values for priority and audience
        $validated['priority'] = 'medium';
        $validated['audience'] = 'all';

        // Handle file uploads
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('announcements', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getClientOriginalExtension(),
                ];
            }
        }

        $announcement = Announcement::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'priority' => $validated['priority'],
            'audience' => $validated['audience'],
            'target_audience' => $validated['target_audience'],
            'expires_at' => $validated['expires_at'] ?? null,
            'is_pinned' => $request->boolean('is_pinned'),
            'attachments' => !empty($attachments) ? $attachments : null,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement created successfully!');
    }

    /**
     * Display the specified announcement.
     */
    public function show(Announcement $announcement)
    {
        $user = Auth::user();
        
        $announcement->load(['createdBy', 'updatedBy']);
        
        $canEdit = $user->account_type === 'admin';
        
        // Determine view based on role
        $view = match($user->account_type) {
            'admin' => 'admin.announcements.show',
            'faculty' => 'faculty.announcements.show',
            'student' => 'student.announcements.show',
            default => abort(403, 'Unauthorized access')
        };
        
        return view($view, compact('announcement', 'canEdit'));
    }

    /**
     * Show the form for editing the specified announcement.
     */
    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    /**
     * Update the specified announcement in storage.
     */
    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'audience' => 'required|in:all,faculty,students',
            'target_audience' => 'required|in:public,all,students,faculty',
            'expires_at' => 'nullable|date|after:now',
            'is_pinned' => 'boolean',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);

        // Handle new file uploads
        $attachments = $announcement->attachments ?? [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('announcements', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getClientOriginalExtension(),
                ];
            }
        }

        $announcement->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'priority' => $validated['priority'],
            'audience' => $validated['audience'],
            'target_audience' => $validated['target_audience'],
            'expires_at' => $validated['expires_at'] ?? null,
            'is_pinned' => $request->boolean('is_pinned'),
            'attachments' => !empty($attachments) ? $attachments : null,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement updated successfully!');
    }

    /**
     * Remove the specified announcement from storage.
     */
    public function destroy(Announcement $announcement)
    {
        // Delete associated files
        if ($announcement->attachments) {
            foreach ($announcement->attachments as $attachment) {
                Storage::disk('public')->delete($attachment['path']);
            }
        }

        $announcement->delete();

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement deleted successfully!');
    }

    /**
     * Toggle pin status of the announcement.
     */
    public function pin(Announcement $announcement)
    {
        $announcement->update([
            'is_pinned' => !$announcement->is_pinned,
            'updated_by' => Auth::id(),
        ]);

        $message = $announcement->is_pinned 
            ? 'Announcement pinned successfully!' 
            : 'Announcement unpinned successfully!';

        return redirect()->back()->with('success', $message);
    }
}
