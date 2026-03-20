<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    /**
     * Display the manage accounts page with separate student and faculty lists.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function manageAccounts(Request $request)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Get authenticated user
        $user = Auth::user();

        // Check if user is an admin
        if ($user->account_type !== 'admin') {
            abort(403, 'Unauthorized access to admin accounts management');
        }

        $gradeLevel = $request->query('student_grade_level');
        $sectionId = $request->query('student_section_id');

        // Get students with pagination
        $students = User::where('account_type', 'student')
            ->select(['id', 'first_name', 'last_name', 'email', 'grade_level', 'lrn', 'status', 'created_at', 'section_id'])
            ->when($gradeLevel, fn ($q) => $q->where('grade_level', $gradeLevel))
            ->when($sectionId, fn ($q) => $q->where('section_id', $sectionId))
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Get faculty with pagination
        $faculty = User::where('account_type', 'faculty')
            ->select(['id', 'first_name', 'last_name', 'email', 'department', 'status', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get accounts pending approval (for both students and faculty)
        $pending = User::where('status', 'for_approval')
            ->select(['id', 'first_name', 'last_name', 'email', 'account_type', 'grade_level', 'lrn', 'department', 'status', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Sections grouped by grade level
        $sectionsByGrade = \App\Models\Section::where('is_active', true)
            ->get()
            ->groupBy('grade_level');

        return view('admin.accounts.manage', compact('students', 'faculty', 'pending', 'sectionsByGrade', 'gradeLevel', 'sectionId'));
    }

    /**
     * Approve a pending account.
     */
    public function approve(Request $request, User $user)
    {
        if (auth()->user()->account_type !== 'admin') {
            abort(403);
        }

        if ($user->status !== 'for_approval') {
            return redirect()->back()->with('error', 'Account is not pending approval.');
        }

        $user->status = 'active';
        $user->save();

        // Send approval email only if requested
        if ($request->boolean('send_email')) {
            try {
                // Synchronous sending (immediate)
                Mail::to($user->email)->send(new \App\Mail\AccountApproved($user));

                // Queued sending (for future use - requires queue:work)
                // Mail::to($user->email)->queue(new \App\Mail\AccountApproved($user));
            } catch (\Exception $e) {
                // Log the error but don't fail the approval
                \Log::error('Failed to send approval email: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Account approved successfully.');
    }

    /**
     * Reject a pending account.
     */
    public function reject(Request $request, User $user)
    {
        if (auth()->user()->account_type !== 'admin') {
            abort(403);
        }

        if ($user->status !== 'for_approval') {
            return redirect()->back()->with('error', 'Account is not pending approval.');
        }

    // Delete the user to reject their pending account
    $user->delete();

    return redirect()->back()->with('success', 'Account rejected and removed.');
    }

    /**
     * Show account details
     */
    public function show(User $user)
    {
        if (auth()->user()->account_type !== 'admin') {
            abort(403);
        }

        return view('admin.accounts.show', compact('user'));
    }

    /**
     * Show edit form
     */
    public function edit(User $user)
    {
        if (auth()->user()->account_type !== 'admin') {
            abort(403);
        }

        return view('admin.accounts.edit', compact('user'));
    }

    /**
     * Update account details
     */
    public function update(Request $request, User $user)
    {
        if (auth()->user()->account_type !== 'admin') {
            abort(403);
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'contact_number' => 'nullable|string|max:20',
            'grade_level' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:255',
        ]);

        $user->first_name = $request->first_name;
        $user->middle_name = $request->middle_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->contact_number = $request->contact_number;
        $user->grade_level = $request->grade_level;
        $user->department = $request->department;
        $user->save();

        return redirect()->route('admin.accounts.show', $user)->with('success', 'Account updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request, User $user)
    {
        if (auth()->user()->account_type !== 'admin') {
            abort(403);
        }

        $user->delete();

        return redirect()->route('admin.accounts.manage')->with('success', 'Account deleted successfully.');
    }
}