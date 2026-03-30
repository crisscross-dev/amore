<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\GradeEntry;
use App\Models\StudentEnrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    /**
     * Realtime badge counts for admin sidebar navigation.
     */
    public function sidebarBadges(Request $request): JsonResponse
    {
        if (!Auth::check() || Auth::user()->account_type !== 'admin') {
            abort(403, 'Unauthorized access to admin sidebar notifications');
        }

        $badges = $this->buildSidebarBadgeCounts();

        return response()->json([
            'badges' => $badges,
            'signature' => hash('sha256', json_encode($badges)),
            'generated_at' => now()->toIso8601String(),
        ]);
    }

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

        $students = $this->buildStudentsQuery($request)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'students_page')
            ->withQueryString();

        $faculty = $this->buildFacultyQuery()
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'faculty_page')
            ->withQueryString();

        $pending = $this->buildPendingQuery()
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'pending_page')
            ->withQueryString();

        $studentGradeLevels = User::where('account_type', 'student')
            ->whereNotNull('grade_level')
            ->where('grade_level', '!=', '')
            ->selectRaw('grade_level, COUNT(*) as total')
            ->groupBy('grade_level')
            ->get()
            ->sortBy(function ($row) {
                if (preg_match('/(\d+)/', (string) $row->grade_level, $match)) {
                    return (int) $match[1];
                }

                return PHP_INT_MAX;
            })
            ->values();

        // Sections grouped by grade level
        $sectionsByGrade = \App\Models\Section::where('is_active', true)
            ->get()
            ->groupBy('grade_level');

        $accountsLiveSignature = $this->buildAccountsLiveSignature($request);

        return view('admin.accounts.manage', compact(
            'students',
            'faculty',
            'pending',
            'sectionsByGrade',
            'gradeLevel',
            'studentGradeLevels',
            'accountsLiveSignature'
        ));
    }

    /**
     * Lightweight polling endpoint for admin accounts manage page.
     */
    public function liveSignature(Request $request)
    {
        if (!Auth::check() || Auth::user()->account_type !== 'admin') {
            abort(403, 'Unauthorized access to admin accounts management');
        }

        return response()->json([
            'signature' => $this->buildAccountsLiveSignature($request),
            'generated_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * Approve a pending account.
     */
    public function approve(Request $request, User $user)
    {
        if (Auth::user()->account_type !== 'admin') {
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
                Log::error('Failed to send approval email: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Account approved successfully.');
    }

    /**
     * Reject a pending account.
     */
    public function reject(Request $request, User $user)
    {
        if (Auth::user()->account_type !== 'admin') {
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
        if (Auth::user()->account_type !== 'admin') {
            abort(403);
        }

        // Get admission data if student
        $admission = null;
        if ($user->account_type === 'student' && $user->lrn) {
            $admission = \App\Models\Admission::where('lrn', $user->lrn)->first();
        }

        return view('admin.accounts.show', compact('user', 'admission'));
    }

    /**
     * Show edit form
     */
    public function edit(User $user)
    {
        if (Auth::user()->account_type !== 'admin') {
            abort(403);
        }

        return view('admin.accounts.edit', compact('user'));
    }

    /**
     * Update account details
     */
    public function update(Request $request, User $user)
    {
        if (Auth::user()->account_type !== 'admin') {
            abort(403);
        }

        $rules = [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'contact_number' => 'nullable|string|max:20',
            'grade_level' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ];

        if ($user->account_type === 'faculty') {
            $rules['department'] = 'required|string|in:elementary,junior high,senior high';
            $rules['contact_number'] = ['required', 'regex:/^\d{11}$/'];
        }

        $validated = $request->validate($rules, [
            'contact_number.required' => 'Contact number is required.',
            'contact_number.regex' => 'Contact number must be exactly 11 digits.',
        ]);

        $user->first_name = $validated['first_name'];
        $user->middle_name = $validated['middle_name'] ?? null;
        $user->last_name = $validated['last_name'];
        $user->email = $validated['email'];
        $user->contact_number = $validated['contact_number'] ?? null;
        $user->grade_level = $validated['grade_level'] ?? null;
        $user->department = $validated['department'] ?? null;

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if ($request->filled('tab')) {
            return redirect()->route('admin.accounts.manage', [
                'tab' => $request->input('tab'),
            ])->with('success', 'Account updated successfully.');
        }

        return redirect()->back()->with('success', 'Account updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request, User $user)
    {
        if (Auth::user()->account_type !== 'admin') {
            abort(403);
        }

        $user->delete();

        return redirect()->route('admin.accounts.manage')->with('success', 'Account deleted successfully.');
    }

    private function buildStudentsQuery(Request $request): Builder
    {
        $gradeLevel = $request->query('student_grade_level');

        return User::query()
            ->where('account_type', 'student')
            ->select([
                'id',
                'custom_id',
                'first_name',
                'middle_name',
                'last_name',
                'email',
                'contact_number',
                'grade_level',
                'department',
                'lrn',
                'status',
                'created_at',
                'updated_at',
                'section_id',
                'profile_picture',
            ])
            ->when($gradeLevel, function (Builder $query) use ($gradeLevel) {
                $query->where('grade_level', $gradeLevel);
            });
    }

    private function buildFacultyQuery(): Builder
    {
        return User::query()
            ->where('account_type', 'faculty')
            ->select([
                'id',
                'custom_id',
                'first_name',
                'middle_name',
                'last_name',
                'email',
                'contact_number',
                'grade_level',
                'department',
                'status',
                'created_at',
                'updated_at',
                'profile_picture',
            ]);
    }

    private function buildPendingQuery(): Builder
    {
        return User::query()
            ->where('status', 'for_approval')
            ->select([
                'id',
                'first_name',
                'last_name',
                'email',
                'account_type',
                'grade_level',
                'lrn',
                'department',
                'status',
                'created_at',
                'updated_at',
            ]);
    }

    private function buildAccountsLiveSignature(Request $request): string
    {
        $studentsQuery = $this->buildStudentsQuery($request);
        $facultyQuery = $this->buildFacultyQuery();
        $pendingQuery = $this->buildPendingQuery();

        $studentsCount = (clone $studentsQuery)->count();
        $studentsStamp = $this->timestampOrZero((clone $studentsQuery)->max('updated_at'));
        $facultyCount = (clone $facultyQuery)->count();
        $facultyStamp = $this->timestampOrZero((clone $facultyQuery)->max('updated_at'));
        $pendingCount = (clone $pendingQuery)->count();
        $pendingStamp = $this->timestampOrZero((clone $pendingQuery)->max('updated_at'));

        $globalUsersCount = User::query()->count();
        $globalUsersStamp = $this->timestampOrZero(User::query()->max('updated_at'));

        return implode('|', [
            (string) ($request->query('tab', 'students')),
            (string) ($request->query('student_grade_level', '')),
            $studentsCount,
            $studentsStamp,
            $facultyCount,
            $facultyStamp,
            $pendingCount,
            $pendingStamp,
            $globalUsersCount,
            $globalUsersStamp,
        ]);
    }

    private function timestampOrZero($value): int
    {
        if (empty($value)) {
            return 0;
        }

        $timestamp = strtotime((string) $value);

        return $timestamp !== false ? $timestamp : 0;
    }

    private function buildSidebarBadgeCounts(): array
    {
        $pendingAdmissions = Admission::query()
            ->where('status', 'pending')
            ->count();

        $pendingEnrollments = StudentEnrollment::query()
            ->where('status', 'pending')
            ->count();

        $admissionsReadyForEnrollment = Admission::query()
            ->where('status', 'approved')
            ->whereNotNull('user_id')
            ->whereHas('user', function (Builder $builder) {
                $builder->whereNull('section_id');
            })
            ->count();

        $pendingAccounts = User::query()
            ->where('status', 'for_approval')
            ->count();

        $pendingGradeSheets = GradeEntry::query()
            ->where('status', 'submitted')
            ->select(['section_id', 'subject_id', 'term', 'created_by'])
            ->distinct()
            ->count();

        return [
            'admissions_pending' => $pendingAdmissions,
            'enrollments_pending' => $pendingEnrollments + $admissionsReadyForEnrollment,
            'accounts_pending' => $pendingAccounts,
            'grade_approvals_pending' => $pendingGradeSheets,
        ];
    }
}
