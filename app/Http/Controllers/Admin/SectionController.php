<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SectionController extends Controller
{
    public function index()
    {
        $sections = Section::orderBy('grade_level')->orderBy('name')->paginate(15);
        return view('admin.sections.index', compact('sections'));
    }

    public function show(Section $section)
    {
        $section->load(['students', 'adviser']);
        
        // Get available students for this grade level who don't have a section yet
        // Include students with 'active' status (approved/enrolled students)
        $availableStudents = \App\Models\User::where('account_type', 'student')
            ->where('grade_level', $section->grade_level)
            ->where('status', 'active') // Only show enrolled/active students
            ->whereNull('section_id')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $facultyMembers = \App\Models\User::where('account_type', 'faculty')
            ->where('status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $sectionGrade = $this->normalizeGradeLevel($section->grade_level);

        $subjects = \App\Models\Subject::query()
            ->with('gradeLevels')
            ->where('is_active', true)
            ->where(function ($query) use ($sectionGrade) {
                $query->whereHas('gradeLevels', function ($builder) use ($sectionGrade) {
                    $builder->where('grade_level', $sectionGrade);
                })
                ->orWhere(function ($builder) use ($sectionGrade) {
                    $builder->whereDoesntHave('gradeLevels')
                        ->where(function ($fallback) use ($sectionGrade) {
                            $fallback->where('grade_level', 'all')
                                ->orWhere('grade_level', $sectionGrade);
                        });
                });
            })
            ->orderBy('name')
            ->get();

        $subjectAssignments = \App\Models\SectionSubjectTeacher::with('teacher')
            ->where('section_id', $section->id)
            ->get()
            ->keyBy('subject_id');
            
        return view('admin.sections.show', compact(
            'section',
            'availableStudents',
            'facultyMembers',
            'subjects',
            'subjectAssignments'
        ));
    }

    public function create()
    {
        $sectionNames = Section::query()
            ->select('name')
            ->distinct()
            ->orderBy('name')
            ->pluck('name');

        return view('admin.sections.create', compact('sectionNames'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'grade_level' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
            'academic_year' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sections', 'name')->where(function ($query) use ($request) {
                    return $query->where('grade_level', $request->input('grade_level'))
                        ->where('academic_year', $request->input('academic_year'));
                }),
            ],
        ], [
            'name.unique' => 'That section already exists for the selected grade level and academic year.',
        ]);

        // Handle checkbox: if not checked, it won't be in request
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        Section::create($validated);
        return redirect()->route('admin.sections.index')->with('success', 'Section created successfully');
    }

    public function edit(Section $section)
    {
        return view('admin.sections.edit', compact('section'));
    }

    public function update(Request $request, Section $section)
    {
        $validated = $request->validate([
            'grade_level' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
            'academic_year' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sections', 'name')
                    ->where(function ($query) use ($request) {
                        return $query->where('grade_level', $request->input('grade_level'))
                            ->where('academic_year', $request->input('academic_year'));
                    })
                    ->ignore($section->id),
            ],
        ], [
            'name.unique' => 'That section already exists for the selected grade level and academic year.',
        ]);

        // Handle checkbox: if not checked, it won't be in request
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $section->update($validated);
        return redirect()->route('admin.sections.index')->with('success', 'Section updated successfully');
    }

    public function destroy(Section $section)
    {
        $section->delete();
        return redirect()->route('admin.sections.index')->with('success', 'Section deleted successfully');
    }

    private function normalizeGradeLevel(?string $gradeLevel): ?string
    {
        if (! $gradeLevel) {
            return null;
        }

        if (preg_match('/(7|8|9|10|11|12)/', $gradeLevel, $matches)) {
            return $matches[1];
        }

        return $gradeLevel;
    }
}
