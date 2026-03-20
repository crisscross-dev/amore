<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacultyPosition;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FacultyAssignmentController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with(['facultyPosition', 'positionAssignee'])
            ->where('account_type', 'faculty');

        if ($search = $request->input('search')) {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($positionId = $request->input('position_id')) {
            $query->where('faculty_position_id', $positionId);
        }

        $facultyMembers = $query->orderBy('last_name')->paginate(15)->withQueryString();
        $positions = FacultyPosition::orderBy('hierarchy_level')->orderBy('name')->get();

        return view('admin.faculty_assignments.index', compact('facultyMembers', 'positions'));
    }

    public function edit(User $user): View
    {
        abort_unless($user->account_type === 'faculty', 404);

        $positions = FacultyPosition::where('is_active', true)->orderBy('hierarchy_level')->orderBy('name')->get();
        $departmentOptions = Subject::orderBy('name')
            ->pluck('name')
            ->unique()
            ->values();

        return view('admin.faculty_assignments.edit', compact('user', 'positions', 'departmentOptions'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->account_type === 'faculty', 404);

        $departmentOptions = Subject::orderBy('name')
            ->pluck('name')
            ->unique()
            ->values()
            ->all();

        $validated = $request->validate([
            'faculty_position_id' => ['nullable', 'exists:faculty_positions,id'],
            'department' => ['nullable', 'string', 'max:255', Rule::in($departmentOptions)],
        ]);

        $user->forceFill([
            'faculty_position_id' => $validated['faculty_position_id'] ?? null,
            'department' => $validated['department'] ?? null,
            'position_assigned_date' => $validated['faculty_position_id'] ? now()->toDateString() : null,
            'assigned_by' => $validated['faculty_position_id'] ? $request->user()->id : null,
        ])->save();

        return redirect()
            ->route('admin.faculty-assignments.index')
            ->with('success', 'Faculty position assignment updated successfully.');
    }
}
