<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionBrowseController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (! $user || ! in_array($user->account_type, ['faculty', 'admin'], true)) {
            abort(403);
        }

        $query = Section::with(['adviser', 'students'])
            ->orderBy('grade_level')
            ->orderBy('name');

        if ($user->account_type === 'faculty') {
            $query->whereHas('subjectTeachers', function ($assignmentQuery) use ($user) {
                $assignmentQuery->where('teacher_id', $user->id);
            });
        }

        // Filter by grade level
        if ($request->filled('grade_level')) {
            $query->where('grade_level', $request->grade_level);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active' ? 1 : 0);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $sections = $query->paginate(15)->withQueryString();

        $gradeLevels = Section::query()
            ->when($user->account_type === 'faculty', function ($levelQuery) use ($user) {
                $levelQuery->whereHas('subjectTeachers', function ($assignmentQuery) use ($user) {
                    $assignmentQuery->where('teacher_id', $user->id);
                });
            })
            ->select('grade_level')
            ->distinct()
            ->orderBy('grade_level')
            ->pluck('grade_level');

        return view('faculty.sections.index', compact('sections', 'gradeLevels'));
    }

    public function show(Request $request, \App\Models\Section $section)
    {
        $user = $request->user();

        if (! $user || ! in_array($user->account_type, ['faculty', 'admin'], true)) {
            abort(403);
        }

        if ($user->account_type === 'faculty') {
            $isAssigned = $section->subjectTeachers()
                ->where('teacher_id', $user->id)
                ->exists();

            abort_unless($isAssigned, 403);
        }

        $section->load(['adviser', 'students']);

        $subjectAssignments = $section->subjectTeachers()
            ->with(['subject', 'teacher'])
            ->whereHas('subject', function ($query) {
                $query->where('is_active', true);
            })
            ->orderBy('subject_id')
            ->get();

        return view('faculty.sections.show', compact('section', 'subjectAssignments'));
    }
}
