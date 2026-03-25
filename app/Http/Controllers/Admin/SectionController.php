<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\SectionSubjectTeacher;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SectionController extends Controller
{
    public function index(Request $request)
    {
        $sections = Section::query()
            ->with(['adviser', 'subjectTeachers:id,section_id,subject_id'])
            ->orderBy('grade_level')
            ->orderBy('name')
            ->paginate(15, ['*'], 'sections_page')
            ->withQueryString();

        $facultyMembers = User::query()
            ->where('account_type', 'faculty')
            ->where('status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        [$schoolYears, $subjects] = $this->sectionFormOptions();

        return view('admin.sections.index', compact('sections', 'facultyMembers', 'schoolYears', 'subjects'));
    }

    public function teachingLoads(Request $request)
    {
        $facultyMembers = User::query()
            ->where('account_type', 'faculty')
            ->where('status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $sectionOptions = Section::query()
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get(['id', 'name', 'grade_level']);

        $subjects = Subject::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $teachingLoadsQuery = SectionSubjectTeacher::query()
            ->with(['teacher', 'section', 'subject']);

        if ($search = trim((string) $request->input('search'))) {
            $teachingLoadsQuery->where(function ($query) use ($search) {
                $query->whereHas('teacher', function ($teacherQuery) use ($search) {
                    $teacherQuery->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$search}%"]);
                })->orWhereHas('section', function ($sectionQuery) use ($search) {
                    $sectionQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($gradeLevel = $request->input('grade_level')) {
            $teachingLoadsQuery->whereHas('section', function ($query) use ($gradeLevel) {
                $query->where('grade_level', $gradeLevel);
            });
        }

        if ($subjectId = $request->input('subject_id')) {
            $teachingLoadsQuery->where('subject_id', $subjectId);
        }

        $teachingLoads = $teachingLoadsQuery
            ->orderByDesc('id')
            ->paginate(15, ['*'], 'loads_page')
            ->withQueryString();

        $gradeLevels = Section::query()
            ->select('grade_level')
            ->distinct()
            ->orderBy('grade_level')
            ->pluck('grade_level');

        return view('admin.sections.teaching-loads', compact(
            'facultyMembers',
            'sectionOptions',
            'subjects',
            'teachingLoads',
            'gradeLevels'
        ));
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

        $subjectAssignments = \App\Models\SectionSubjectTeacher::with(['teacher', 'subject'])
            ->where('section_id', $section->id)
            ->whereHas('subject', function ($query) {
                $query->where('is_active', true);
            })
            ->get()
            ->keyBy('subject_id');

        $subjects = $subjectAssignments
            ->pluck('subject')
            ->filter()
            ->sortBy('name')
            ->values();

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
        [$schoolYears, $subjects] = $this->sectionFormOptions();

        return view('admin.sections.create', [
            'section' => new Section(),
            'schoolYears' => $schoolYears,
            'subjects' => $subjects,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'grade_level' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
            'academic_year' => [
                'nullable',
                'string',
                'max:255',
                Rule::exists('school_years', 'year_name'),
            ],
            'is_active' => 'nullable|boolean',
            'subject_ids' => 'required|array|min:1',
            'subject_ids.*' => [
                'integer',
                'distinct',
                Rule::exists('subjects', 'id')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
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

        DB::transaction(function () use ($validated) {
            $subjectIds = $this->expandGroupedSubjectIds(
                array_unique(array_map('intval', $validated['subject_ids'] ?? [])),
                $validated['grade_level'] ?? null
            );
            unset($validated['subject_ids']);

            $section = Section::create($validated);

            foreach ($subjectIds as $subjectId) {
                SectionSubjectTeacher::create([
                    'section_id' => $section->id,
                    'subject_id' => $subjectId,
                    'teacher_id' => null,
                ]);
            }
        });

        return redirect()->route('admin.sections.index')->with('success', 'Section created successfully');
    }

    public function edit(Section $section)
    {
        [$schoolYears, $subjects] = $this->sectionFormOptions();

        $selectedSubjectIds = SectionSubjectTeacher::query()
            ->where('section_id', $section->id)
            ->pluck('subject_id')
            ->map(fn($id) => (int) $id)
            ->all();

        return view('admin.sections.edit', compact('section', 'schoolYears', 'subjects', 'selectedSubjectIds'));
    }

    public function update(Request $request, Section $section)
    {
        $validated = $request->validate([
            'grade_level' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
            'academic_year' => [
                'nullable',
                'string',
                'max:255',
                Rule::exists('school_years', 'year_name'),
            ],
            'is_active' => 'nullable|boolean',
            'subject_ids' => 'required|array|min:1',
            'subject_ids.*' => [
                'integer',
                'distinct',
                Rule::exists('subjects', 'id')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
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

        DB::transaction(function () use ($section, $validated) {
            $subjectIds = $this->expandGroupedSubjectIds(
                array_unique(array_map('intval', $validated['subject_ids'] ?? [])),
                $validated['grade_level'] ?? null
            );
            unset($validated['subject_ids']);

            $section->update($validated);

            SectionSubjectTeacher::query()
                ->where('section_id', $section->id)
                ->whereNotIn('subject_id', $subjectIds)
                ->delete();

            $existingSubjectIds = SectionSubjectTeacher::query()
                ->where('section_id', $section->id)
                ->pluck('subject_id')
                ->map(fn($id) => (int) $id)
                ->all();

            foreach ($subjectIds as $subjectId) {
                if (in_array($subjectId, $existingSubjectIds, true)) {
                    continue;
                }

                SectionSubjectTeacher::create([
                    'section_id' => $section->id,
                    'subject_id' => $subjectId,
                    'teacher_id' => null,
                ]);
            }
        });

        return redirect()->route('admin.sections.index')->with('success', 'Section updated successfully');
    }

    public function destroy(Section $section)
    {
        $section->delete();
        return redirect()->route('admin.sections.index')->with('success', 'Section deleted successfully');
    }

    private function sectionFormOptions(): array
    {
        $schoolYears = SchoolYear::query()
            ->orderByDesc('start_date')
            ->get(['id', 'year_name', 'is_active']);

        $subjects = Subject::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'grade_level', 'description']);

        return [$schoolYears, $subjects];
    }

    private function expandGroupedSubjectIds(array $subjectIds, ?string $gradeLevel): array
    {
        $subjectIds = array_values(array_unique(array_map('intval', $subjectIds)));
        if (empty($subjectIds)) {
            return [];
        }

        $gradeNumber = $this->extractGradeNumber($gradeLevel);
        if (! in_array($gradeNumber, [7, 8, 9, 10], true)) {
            return $subjectIds;
        }

        $componentNames = $this->mapehComponentNamesForGrade($gradeNumber);
        if (empty($componentNames)) {
            return $subjectIds;
        }

        $selectedHasMapehComponent = Subject::query()
            ->whereIn('id', $subjectIds)
            ->whereIn('name', $componentNames)
            ->exists();

        if (! $selectedHasMapehComponent) {
            return $subjectIds;
        }

        $allMapehComponentIds = Subject::query()
            ->whereIn('name', $componentNames)
            ->whereIn('grade_level', [(string) $gradeNumber, 'Grade ' . $gradeNumber])
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();

        return array_values(array_unique(array_merge($subjectIds, $allMapehComponentIds)));
    }

    private function mapehComponentNamesForGrade(?int $gradeNumber): array
    {
        if (in_array($gradeNumber, [7, 8], true)) {
            return [
                'MAPEH - Music & Arts',
                'MAPEH - PE & Health',
                'Music & Arts',
                'PE & Health',
            ];
        }

        if (in_array($gradeNumber, [9, 10], true)) {
            return [
                'MAPEH - Music',
                'MAPEH - Arts',
                'MAPEH - PE',
                'MAPEH - Health',
                'Music',
                'Arts',
                'PE',
                'Health',
            ];
        }

        return [];
    }

    private function extractGradeNumber(?string $gradeLevel): ?int
    {
        if (! $gradeLevel) {
            return null;
        }

        if (preg_match('/(7|8|9|10|11|12)/', $gradeLevel, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }
}
