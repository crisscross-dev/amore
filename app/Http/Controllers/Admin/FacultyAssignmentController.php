<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacultyPosition;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FacultyAssignmentController extends Controller
{
    /**
     * Allowed department options for faculty assignment.
     *
     * @return array<int, string>
     */
    private function departmentOptions(): array
    {
        return [
            'Junior High School',
            'Senior High School',
        ];
    }

    public function index(Request $request): View
    {
        $query = $this->buildFacultyMembersQuery($request);
        $orderBy = (string) $request->input('order_by', 'newest');

        $facultyMembers = $query->paginate(15)->withQueryString();
        $positions = FacultyPosition::orderBy('hierarchy_level')->orderBy('name')->get();
        $departmentOptions = collect($this->departmentOptions());
        $facultyAssignmentsLiveSignature = $this->buildFacultyAssignmentsLiveSignature(
            $this->buildFacultyMembersQuery($request)
        );

        return view('admin.faculty_assignments.index', compact(
            'facultyMembers',
            'positions',
            'departmentOptions',
            'orderBy',
            'facultyAssignmentsLiveSignature'
        ));
    }

    /**
     * Lightweight polling endpoint to detect list updates for faculty assignments.
     */
    public function liveSignature(Request $request)
    {
        $query = $this->buildFacultyMembersQuery($request);

        return response()->json([
            'signature' => $this->buildFacultyAssignmentsLiveSignature($query),
            'generated_at' => now()->toIso8601String(),
        ]);
    }

    public function edit(User $user): View
    {
        abort_unless($user->account_type === 'faculty', 404);

        $positions = FacultyPosition::where('is_active', true)->orderBy('hierarchy_level')->orderBy('name')->get();
        $departmentOptions = collect($this->departmentOptions());

        return view('admin.faculty_assignments.edit', compact('user', 'positions', 'departmentOptions'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->account_type === 'faculty', 404);

        $departmentOptions = $this->departmentOptions();

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

    private function buildFacultyMembersQuery(Request $request): Builder
    {
        $query = User::with(['facultyPosition', 'positionAssignee'])
            ->where('account_type', 'faculty');

        $orderBy = (string) $request->input('order_by', 'newest');

        if ($search = $request->input('search')) {
            $query->where(function (Builder $subQuery) use ($search) {
                $subQuery->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($positionId = $request->input('position_id')) {
            $query->where('faculty_position_id', $positionId);
        }

        if ($department = $request->input('department')) {
            $query->where('department', $department);
        }

        switch ($orderBy) {
            case 'oldest':
                $query->orderBy('created_at');
                break;
            case 'position_asc':
                $query->orderByRaw("CASE WHEN users.faculty_position_id IS NULL THEN 1 ELSE 0 END ASC")
                    ->orderByRaw("COALESCE((SELECT hierarchy_level FROM faculty_positions WHERE faculty_positions.id = users.faculty_position_id), 999) ASC")
                    ->orderBy('last_name');
                break;
            case 'position_desc':
                $query->orderByRaw("CASE WHEN users.faculty_position_id IS NULL THEN 1 ELSE 0 END ASC")
                    ->orderByRaw("COALESCE((SELECT hierarchy_level FROM faculty_positions WHERE faculty_positions.id = users.faculty_position_id), -1) DESC")
                    ->orderBy('last_name');
                break;
            case 'department_asc':
                $query->orderByRaw("COALESCE(users.department, 'ZZZ') ASC")
                    ->orderBy('last_name');
                break;
            case 'department_desc':
                $query->orderByRaw("COALESCE(users.department, '') DESC")
                    ->orderBy('last_name');
                break;
            case 'newest':
            default:
                $query->orderByDesc('created_at');
                break;
        }

        return $query;
    }

    private function buildFacultyAssignmentsLiveSignature(Builder $query): string
    {
        $facultyCount = (clone $query)->count();
        $facultyUpdatedStamp = $this->timestampOrZero((clone $query)->max('updated_at'));
        $positionCount = FacultyPosition::query()->count();
        $positionUpdatedStamp = $this->timestampOrZero(FacultyPosition::query()->max('updated_at'));

        return implode('|', [
            $facultyCount,
            $facultyUpdatedStamp,
            $positionCount,
            $positionUpdatedStamp,
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
}
