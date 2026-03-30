<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\SectionSubjectTeacher;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
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

        $adviserAssignments = Section::query()
            ->whereNotNull('adviser_id')
            ->get(['id', 'name', 'grade_level', 'adviser_id'])
            ->keyBy('adviser_id');

        [$schoolYears, $subjects] = $this->sectionFormOptions();

        $sectionsLiveSignature = $this->buildSectionsIndexLiveSignature();

        return view('admin.sections.index', compact(
            'sections',
            'facultyMembers',
            'schoolYears',
            'subjects',
            'adviserAssignments',
            'sectionsLiveSignature'
        ));
    }

    public function liveSignatureIndex(Request $request)
    {
        return response()->json([
            'signature' => $this->buildSectionsIndexLiveSignature(),
            'generated_at' => now()->toIso8601String(),
        ]);
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

        $adviserAssignments = Section::query()
            ->whereNotNull('adviser_id')
            ->get(['id', 'name', 'grade_level', 'adviser_id'])
            ->keyBy('adviser_id');

        $subjectAssignments = \App\Models\SectionSubjectTeacher::with(['teacher', 'subject'])
            ->where('section_id', $section->id)
            ->whereHas('subject', function ($query) {
                $query->where('is_active', true);
            })
            ->get()
            ->keyBy('subject_id');

        $occupiedSchedules = \App\Models\SectionSubjectTeacher::query()
            ->select(['section_id', 'subject_id', 'room', 'day_of_week', 'start_time', 'end_time'])
            ->whereNotNull('room')
            ->whereNotNull('day_of_week')
            ->whereNotNull('start_time')
            ->whereNotNull('end_time')
            ->get()
            ->map(function ($assignment) {
                return [
                    'section_id' => (int) $assignment->section_id,
                    'subject_id' => (int) $assignment->subject_id,
                    'room' => (string) $assignment->room,
                    'day' => (string) $assignment->day_of_week,
                    'start' => strlen((string) $assignment->start_time) > 5 ? substr((string) $assignment->start_time, 0, 5) : (string) $assignment->start_time,
                    'end' => strlen((string) $assignment->end_time) > 5 ? substr((string) $assignment->end_time, 0, 5) : (string) $assignment->end_time,
                ];
            })
            ->values();

        $subjects = $subjectAssignments
            ->pluck('subject')
            ->filter()
            ->sortBy('name')
            ->values();

        $sectionShowLiveSignature = $this->buildSectionShowLiveSignature($section);

        return view('admin.sections.show', compact(
            'section',
            'availableStudents',
            'facultyMembers',
            'subjects',
            'subjectAssignments',
            'adviserAssignments',
            'occupiedSchedules',
            'sectionShowLiveSignature'
        ));
    }

    public function liveSignatureShow(Request $request, Section $section)
    {
        return response()->json([
            'signature' => $this->buildSectionShowLiveSignature($section),
            'generated_at' => now()->toIso8601String(),
        ]);
    }

    public function create()
    {
        [$schoolYears, $subjects] = $this->sectionFormOptions();
        $facultyMembers = User::query()
            ->where('account_type', 'faculty')
            ->where('status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name']);
        $adviserAssignments = Section::query()
            ->whereNotNull('adviser_id')
            ->get(['id', 'name', 'grade_level', 'adviser_id'])
            ->keyBy('adviser_id');

        return view('admin.sections.create', [
            'section' => new Section(),
            'schoolYears' => $schoolYears,
            'subjects' => $subjects,
            'facultyMembers' => $facultyMembers,
            'adviserAssignments' => $adviserAssignments,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'grade_level' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
            'adviser_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(function ($query) {
                    return $query->where('account_type', 'faculty')
                        ->where('status', 'active');
                }),
            ],
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

        if (!empty($validated['adviser_id'])) {
            $existingAdviserSection = Section::query()
                ->where('adviser_id', (int) $validated['adviser_id'])
                ->first();

            if ($existingAdviserSection) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'adviser_id' => 'Selected adviser is already assigned to section ' . $existingAdviserSection->name . '. Each faculty can only advise one section.',
                    ]);
            }
        }

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
        $facultyMembers = User::query()
            ->where('account_type', 'faculty')
            ->where('status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name']);
        $adviserAssignments = Section::query()
            ->whereNotNull('adviser_id')
            ->get(['id', 'name', 'grade_level', 'adviser_id'])
            ->keyBy('adviser_id');

        $selectedSubjectIds = SectionSubjectTeacher::query()
            ->where('section_id', $section->id)
            ->pluck('subject_id')
            ->map(fn($id) => (int) $id)
            ->all();

        return view('admin.sections.edit', compact('section', 'schoolYears', 'subjects', 'selectedSubjectIds', 'facultyMembers', 'adviserAssignments'));
    }

    public function update(Request $request, Section $section)
    {
        $validated = $request->validate([
            'grade_level' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
            'adviser_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(function ($query) {
                    return $query->where('account_type', 'faculty')
                        ->where('status', 'active');
                }),
            ],
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

        if (!empty($validated['adviser_id'])) {
            $existingAdviserSection = Section::query()
                ->where('adviser_id', (int) $validated['adviser_id'])
                ->where('id', '!=', $section->id)
                ->first();

            if ($existingAdviserSection) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'adviser_id' => 'Selected adviser is already assigned to section ' . $existingAdviserSection->name . '. Each faculty can only advise one section.',
                    ]);
            }
        }

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
                'MAPEH - MUSIC & ARTS',
                'MAPEH - PE & HEALTH',
                'MUSIC & ARTS',
                'PE & HEALTH',
            ];
        }

        if (in_array($gradeNumber, [9, 10], true)) {
            return [
                'MAPEH - MUSIC',
                'MAPEH - ARTS',
                'MAPEH - PE',
                'MAPEH - HEALTH',
                'MUSIC',
                'ARTS',
                'PE',
                'HEALTH',
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

    private function buildSectionsIndexLiveSignature(): string
    {
        $sectionsCount = Section::query()->count();
        $sectionsStamp = $this->timestampOrZero(Section::query()->max('updated_at'));

        $adviserAssignmentsCount = Section::query()->whereNotNull('adviser_id')->count();
        $adviserAssignmentsStamp = $this->timestampOrZero(
            Section::query()->whereNotNull('adviser_id')->max('updated_at')
        );

        $subjectAssignmentsCount = SectionSubjectTeacher::query()->count();
        $subjectAssignmentsStamp = $this->timestampOrZero(SectionSubjectTeacher::query()->max('updated_at'));

        $facultyQuery = User::query()
            ->where('account_type', 'faculty')
            ->where('status', 'active');
        $facultyCount = (clone $facultyQuery)->count();
        $facultyStamp = $this->timestampOrZero((clone $facultyQuery)->max('updated_at'));

        return implode('|', [
            $sectionsCount,
            $sectionsStamp,
            $adviserAssignmentsCount,
            $adviserAssignmentsStamp,
            $subjectAssignmentsCount,
            $subjectAssignmentsStamp,
            $facultyCount,
            $facultyStamp,
        ]);
    }

    private function buildSectionShowLiveSignature(Section $section): string
    {
        $section->refresh();
        $sectionStamp = $this->timestampOrZero($section->updated_at);

        $sectionStudentsQuery = User::query()
            ->where('account_type', 'student')
            ->where('section_id', $section->id);
        $sectionStudentsCount = (clone $sectionStudentsQuery)->count();
        $sectionStudentsStamp = $this->timestampOrZero((clone $sectionStudentsQuery)->max('updated_at'));

        $availableStudentsQuery = User::query()
            ->where('account_type', 'student')
            ->where('grade_level', $section->grade_level)
            ->where('status', 'active')
            ->whereNull('section_id');
        $availableStudentsCount = (clone $availableStudentsQuery)->count();
        $availableStudentsStamp = $this->timestampOrZero((clone $availableStudentsQuery)->max('updated_at'));

        $facultyQuery = User::query()
            ->where('account_type', 'faculty')
            ->where('status', 'active');
        $facultyCount = (clone $facultyQuery)->count();
        $facultyStamp = $this->timestampOrZero((clone $facultyQuery)->max('updated_at'));

        $sectionAssignmentsQuery = SectionSubjectTeacher::query()
            ->where('section_id', $section->id);
        $sectionAssignmentsCount = (clone $sectionAssignmentsQuery)->count();
        $sectionAssignmentsStamp = $this->timestampOrZero((clone $sectionAssignmentsQuery)->max('updated_at'));

        $globalScheduleStamp = $this->timestampOrZero(
            SectionSubjectTeacher::query()
                ->whereNotNull('room')
                ->whereNotNull('day_of_week')
                ->whereNotNull('start_time')
                ->whereNotNull('end_time')
                ->max('updated_at')
        );

        $activeSubjectsQuery = Subject::query()->where('is_active', true);
        $activeSubjectsCount = (clone $activeSubjectsQuery)->count();
        $activeSubjectsStamp = $this->timestampOrZero((clone $activeSubjectsQuery)->max('updated_at'));

        return implode('|', [
            $section->id,
            $sectionStamp,
            $sectionStudentsCount,
            $sectionStudentsStamp,
            $availableStudentsCount,
            $availableStudentsStamp,
            $facultyCount,
            $facultyStamp,
            $sectionAssignmentsCount,
            $sectionAssignmentsStamp,
            $globalScheduleStamp,
            $activeSubjectsCount,
            $activeSubjectsStamp,
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
