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

        $gradeLevels = ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];

        return view('faculty.sections.index', compact('sections', 'gradeLevels'));
    }

    public function show(Request $request, \App\Models\Section $section)
    {
        $user = $request->user();

        if (! $user || ! in_array($user->account_type, ['faculty', 'admin'], true)) {
            abort(403);
        }

        $section->load(['adviser', 'students']);

        return view('faculty.sections.show', compact('section'));
    }
}
