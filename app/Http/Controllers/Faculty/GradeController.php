<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\GradeEntry;
use App\Models\Section;
use App\Models\SectionSubjectTeacher;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class GradeController extends Controller
{
    private const QUARTER_TERMS = [
        'First Quarter',
        'Second Quarter',
        'Third Quarter',
        'Fourth Quarter',
    ];

    private const MAPEH_COMPONENTS_BY_GRADE = [
        7 => ['MAPEH - MUSIC & ARTS', 'MAPEH - PE & HEALTH', 'MUSIC & ARTS', 'PE & HEALTH'],
        8 => ['MAPEH - MUSIC & ARTS', 'MAPEH - PE & HEALTH', 'MUSIC & ARTS', 'PE & HEALTH'],
        9 => ['MAPEH - MUSIC', 'MAPEH - ARTS', 'MAPEH - PE', 'MAPEH - HEALTH', 'MUSIC', 'ARTS', 'PE', 'HEALTH'],
        10 => ['MAPEH - MUSIC', 'MAPEH - ARTS', 'MAPEH - PE', 'MAPEH - HEALTH', 'MUSIC', 'ARTS', 'PE', 'HEALTH'],
    ];

    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user && in_array($user->account_type, ['faculty', 'admin'], true), 403);

        $assignmentQuery = SectionSubjectTeacher::query()
            ->with(['subject', 'section'])
            ->when($user->account_type === 'faculty', function ($query) use ($user) {
                $query->where('teacher_id', $user->id);
            })
            ->orderBy('section_id')
            ->orderBy('subject_id');

        $assignments = $assignmentQuery->get();
        $sectionIds = $assignments->pluck('section_id')->unique()->values();
        $studentCounts = User::query()
            ->where('account_type', 'student')
            ->whereIn('section_id', $sectionIds)
            ->selectRaw('section_id, COUNT(*) as total')
            ->groupBy('section_id')
            ->pluck('total', 'section_id');

        return view('faculty.grades.index', compact('assignments', 'studentCounts'));
    }

    public function liveSection(Request $request)
    {
        $user = $request->user();
        abort_unless($user && in_array($user->account_type, ['faculty', 'admin'], true), 403);

        $assignmentQuery = SectionSubjectTeacher::query()
            ->with(['subject', 'section'])
            ->when($user->account_type === 'faculty', function ($query) use ($user) {
                $query->where('teacher_id', $user->id);
            })
            ->orderBy('section_id')
            ->orderBy('subject_id');

        $assignments = $assignmentQuery->get();
        $sectionIds = $assignments->pluck('section_id')->unique()->values();
        $studentCounts = User::query()
            ->where('account_type', 'student')
            ->whereIn('section_id', $sectionIds)
            ->selectRaw('section_id, COUNT(*) as total')
            ->groupBy('section_id')
            ->pluck('total', 'section_id');

        return response()->json([
            'html' => view('faculty.grades.partials.index-live-section', compact('assignments', 'studentCounts'))->render(),
            'generated_at' => now()->toIso8601String(),
        ]);
    }

    public function assignment(Request $request, SectionSubjectTeacher $assignment): View
    {
        $user = $request->user();
        abort_unless($user && in_array($user->account_type, ['faculty', 'admin'], true), 403);

        if ($user->account_type === 'faculty' && (int) $assignment->teacher_id !== (int) $user->id) {
            abort(403);
        }

        $requestedTerm = (string) $request->query('term', self::QUARTER_TERMS[0]);
        $term = in_array($requestedTerm, self::QUARTER_TERMS, true)
            ? $requestedTerm
            : self::QUARTER_TERMS[0];
        $assignment->load(['subject', 'section', 'teacher']);
        $gradeSubjects = $this->resolveGradeSubjects($assignment);
        $subjectIds = $gradeSubjects->pluck('id')->map(fn($id) => (int) $id)->values()->all();

        $students = User::query()
            ->where('account_type', 'student')
            ->where('section_id', $assignment->section_id)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $gradeEntries = GradeEntry::query()
            ->where('section_id', $assignment->section_id)
            ->whereIn('subject_id', $subjectIds)
            ->where('term', $term)
            ->where('created_by', $user->id)
            ->get();

        $gradeEntriesByStudent = $gradeEntries
            ->groupBy('student_id')
            ->map(fn(Collection $entries) => $entries->keyBy('subject_id'));

        $requiredPairCount = $students->count() * max(1, count($subjectIds));
        $lockedRequiredPairCount = $students->sum(function (User $student) use ($subjectIds, $gradeEntriesByStudent) {
            $studentEntries = $gradeEntriesByStudent->get($student->id, collect());

            return collect($subjectIds)->filter(function (int $subjectId) use ($studentEntries) {
                $entry = $studentEntries->get($subjectId);

                return $entry
                    && $entry->grade_value !== null
                    && $entry->status !== 'draft';
            })->count();
        });

        $sheetLocked = $requiredPairCount > 0 && $lockedRequiredPairCount === $requiredPairCount;

        $quarterTerms = self::QUARTER_TERMS;

        return view('faculty.grades.assignment', compact('assignment', 'students', 'gradeEntriesByStudent', 'gradeSubjects', 'term', 'quarterTerms', 'sheetLocked'));
    }

    public function updateAssignmentSheet(Request $request, SectionSubjectTeacher $assignment): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && in_array($user->account_type, ['faculty', 'admin'], true), 403);

        if ($user->account_type === 'faculty' && (int) $assignment->teacher_id !== (int) $user->id) {
            abort(403);
        }

        $gradeSubjects = $this->resolveGradeSubjects($assignment);
        $allowedSubjectIds = $gradeSubjects->pluck('id')->map(fn($id) => (int) $id)->values()->all();

        $data = $request->validate([
            'term' => ['required', 'in:' . implode(',', self::QUARTER_TERMS)],
            'grade_values' => ['required', 'array'],
            'grade_values.*' => ['required', 'array'],
            'grade_values.*.*' => ['nullable', 'numeric', 'min:50', 'max:100'],
            'faculty_remarks' => ['nullable', 'array'],
            'faculty_remarks.*' => ['nullable', 'string', 'max:1000'],
        ]);

        $gradeValues = collect($data['grade_values'] ?? []);
        $remarks = collect($data['faculty_remarks'] ?? []);
        $studentIds = $gradeValues
            ->keys()
            ->merge($remarks->keys())
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        if (empty($studentIds)) {
            return back()->withErrors(['grade_values' => 'No student rows were provided.']);
        }

        $validStudentIds = User::query()
            ->where('account_type', 'student')
            ->where('section_id', $assignment->section_id)
            ->whereIn('id', $studentIds)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();

        $invalidStudentIds = array_diff($studentIds, $validStudentIds);
        if (! empty($invalidStudentIds)) {
            return back()->withErrors(['grade_values' => 'Some students are not part of this section.']);
        }

        $existingEntries = GradeEntry::query()
            ->where('section_id', $assignment->section_id)
            ->whereIn('subject_id', $allowedSubjectIds)
            ->where('term', $data['term'])
            ->where('created_by', $user->id)
            ->whereIn('student_id', $studentIds)
            ->get()
            ->keyBy(fn(GradeEntry $entry) => $entry->student_id . '|' . $entry->subject_id);

        foreach ($studentIds as $studentId) {
            $rowValues = collect($gradeValues->get((string) $studentId, $gradeValues->get($studentId, [])));
            $remarkValue = $remarks->get((string) $studentId, $remarks->get($studentId));
            $remarkValue = is_string($remarkValue) ? trim($remarkValue) : $remarkValue;

            $hasAnyGradeValue = collect($allowedSubjectIds)->contains(function (int $subjectId) use ($rowValues) {
                $value = $rowValues->get((string) $subjectId, $rowValues->get($subjectId));
                return ! ($value === null || $value === '');
            });

            if (! $hasAnyGradeValue && ($remarkValue === null || $remarkValue === '')) {
                continue;
            }

            foreach ($allowedSubjectIds as $subjectId) {
                $existing = $existingEntries->get($studentId . '|' . $subjectId);
                if ($existing && $existing->status !== 'draft') {
                    continue;
                }

                $gradeValue = $rowValues->get((string) $subjectId, $rowValues->get($subjectId));

                if ($gradeValue === null || $gradeValue === '') {
                    if ($existing) {
                        $existing->update(['faculty_remark' => $remarkValue ?: null]);
                    }
                    continue;
                }

                GradeEntry::updateOrCreate(
                    [
                        'section_id' => $assignment->section_id,
                        'subject_id' => $subjectId,
                        'student_id' => $studentId,
                        'term' => $data['term'],
                        'created_by' => $user->id,
                    ],
                    [
                        'grade_value' => $gradeValue,
                        'faculty_remark' => $remarkValue ?: null,
                        'status' => 'draft',
                    ]
                );
            }
        }

        return redirect()
            ->route('faculty.grades.assignment', ['assignment' => $assignment->id, 'term' => $data['term']])
            ->with('success', 'Grade sheet updated successfully.');
    }

    public function uploadAssignmentSheet(Request $request, SectionSubjectTeacher $assignment): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && in_array($user->account_type, ['faculty', 'admin'], true), 403);

        if ($user->account_type === 'faculty' && (int) $assignment->teacher_id !== (int) $user->id) {
            abort(403);
        }

        $gradeSubjects = $this->resolveGradeSubjects($assignment);
        $allowedSubjectIds = $gradeSubjects->pluck('id')->map(fn($id) => (int) $id)->values()->all();

        $data = $request->validate([
            'term' => ['required', 'in:' . implode(',', self::QUARTER_TERMS)],
        ]);

        $entries = GradeEntry::query()
            ->where('section_id', $assignment->section_id)
            ->whereIn('subject_id', $allowedSubjectIds)
            ->where('term', $data['term'])
            ->where('created_by', $user->id)
            ->get();

        if ($entries->isEmpty()) {
            return back()->withErrors(['grade_values' => 'No grade entries found for this sheet.']);
        }

        $studentCount = User::query()
            ->where('account_type', 'student')
            ->where('section_id', $assignment->section_id)
            ->count();

        $requiredEntryCount = $studentCount * max(1, count($allowedSubjectIds));

        $uniqueEntryCount = $entries
            ->map(fn(GradeEntry $entry) => $entry->student_id . '|' . $entry->subject_id)
            ->unique()
            ->count();

        if ($uniqueEntryCount < $requiredEntryCount) {
            return back()->withErrors(['grade_values' => 'Please save grades for all students before uploading the sheet.']);
        }

        if ($entries->contains(fn(GradeEntry $entry) => $entry->grade_value === null)) {
            return back()->withErrors(['grade_values' => 'Please fill in all grades before uploading the sheet.']);
        }

        $entries->each(function (GradeEntry $entry) {
            if ($entry->status === 'draft') {
                $entry->update([
                    'status' => 'submitted',
                    'submitted_at' => now(),
                ]);
            }
        });

        return redirect()
            ->route('faculty.grades.assignment', ['assignment' => $assignment->id, 'term' => $data['term']])
            ->with('success', 'Grade sheet uploaded to admin and locked for editing.');
    }

    public function upsertStudentGrade(Request $request, SectionSubjectTeacher $assignment, User $student): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && in_array($user->account_type, ['faculty', 'admin'], true), 403);

        if ($user->account_type === 'faculty' && (int) $assignment->teacher_id !== (int) $user->id) {
            abort(403);
        }

        if ((int) $student->section_id !== (int) $assignment->section_id || $student->account_type !== 'student') {
            return back()->withErrors(['student' => 'Selected student is not part of this section.']);
        }

        $data = $request->validate([
            'term' => ['required', 'string', 'max:20'],
            'grade_value' => ['required', 'numeric', 'min:50', 'max:100'],
            'faculty_remark' => ['nullable', 'string', 'max:1000'],
        ]);

        $entry = GradeEntry::query()->where([
            'section_id' => $assignment->section_id,
            'subject_id' => $assignment->subject_id,
            'student_id' => $student->id,
            'term' => $data['term'],
            'created_by' => $user->id,
        ])->first();

        if ($entry && $entry->status !== 'draft') {
            return back()->withErrors(['grade_value' => 'Only draft grades can be edited.']);
        }

        GradeEntry::updateOrCreate(
            [
                'section_id' => $assignment->section_id,
                'subject_id' => $assignment->subject_id,
                'student_id' => $student->id,
                'term' => $data['term'],
                'created_by' => $user->id,
            ],
            [
                'grade_value' => $data['grade_value'],
                'faculty_remark' => $data['faculty_remark'] ?? null,
                'status' => 'draft',
            ]
        );

        return redirect()
            ->route('faculty.grades.assignment', ['assignment' => $assignment->id, 'term' => $data['term']])
            ->with('success', 'Grade and remark saved successfully.');
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        abort_unless($user && in_array($user->account_type, ['faculty', 'admin'], true), 403);

        if ($user->account_type === 'faculty') {
            $assignments = SectionSubjectTeacher::where('teacher_id', $user->id)->get();
            $sectionIds = $assignments->pluck('section_id')->unique()->values();
            $subjectIds = $assignments->pluck('subject_id')->unique()->values();

            $subjectSectionMap = $assignments
                ->groupBy('subject_id')
                ->map(fn($items) => $items->pluck('section_id')->unique()->values()->all())
                ->toArray();

            $subjects = Subject::whereIn('id', $subjectIds)->orderBy('name')->get();
            $sections = Section::whereIn('id', $sectionIds)->orderBy('name')->get();
            $students = User::query()
                ->where('account_type', 'student')
                ->whereIn('section_id', $sectionIds)
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get();
        } else {
            $subjects = Subject::orderBy('name')->get();
            $sections = Section::orderBy('name')->get();
            $students = User::query()
                ->where('account_type', 'student')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get();
            $subjectSectionMap = [];
        }

        return view('faculty.grades.create', compact('subjects', 'sections', 'students', 'subjectSectionMap'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && in_array($user->account_type, ['faculty', 'admin'], true), 403);

        $data = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'term' => ['required', 'string', 'max:20'],
            'grade_values' => ['required', 'array'],
            'grade_values.*' => ['nullable', 'integer', 'min:50', 'max:100'],
        ]);

        if ($user->account_type === 'faculty') {
            $assigned = SectionSubjectTeacher::where('teacher_id', $user->id)
                ->where('section_id', $data['section_id'])
                ->where('subject_id', $data['subject_id'])
                ->exists();

            if (! $assigned) {
                return back()->withErrors(['subject_id' => 'You are not assigned to this section and subject.'])->withInput();
            }
        }

        $gradeValues = collect($data['grade_values'] ?? [])
            ->filter(function ($value) {
                return $value !== null && $value !== '';
            });

        if ($gradeValues->isEmpty()) {
            return back()->withErrors(['grade_values' => 'Please enter at least one grade.'])->withInput();
        }

        $studentIds = $gradeValues->keys()->map(fn($id) => (int) $id)->all();
        $validStudentIds = User::query()
            ->where('account_type', 'student')
            ->where('section_id', $data['section_id'])
            ->whereIn('id', $studentIds)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();

        $invalidStudentIds = array_diff($studentIds, $validStudentIds);
        if (! empty($invalidStudentIds)) {
            return back()->withErrors(['grade_values' => 'Some students are not part of the selected section.'])->withInput();
        }

        $existing = GradeEntry::query()
            ->where('subject_id', $data['subject_id'])
            ->where('section_id', $data['section_id'])
            ->where('term', $data['term'])
            ->whereIn('student_id', $studentIds)
            ->exists();

        if ($existing) {
            return back()->withErrors(['grade_values' => 'Grades already exist for one or more students for this subject and term.'])->withInput();
        }

        foreach ($gradeValues as $studentId => $gradeValue) {
            GradeEntry::create([
                'section_id' => $data['section_id'],
                'student_id' => (int) $studentId,
                'subject_id' => $data['subject_id'],
                'term' => $data['term'],
                'grade_value' => $gradeValue,
                'status' => 'draft',
                'created_by' => $user->id,
            ]);
        }

        return redirect()->route('faculty.grades.index')->with('success', 'Grades saved as draft.');
    }

    public function edit(Request $request, GradeEntry $grade): View
    {
        $user = $request->user();
        abort_unless($user && $grade->created_by === $user->id, 403);

        $subjectId = $grade->subject_id;
        $sectionId = $grade->section_id;
        $term = $grade->term;

        if ($user->account_type === 'faculty') {
            $assignments = SectionSubjectTeacher::where('teacher_id', $user->id)->get();
            $sectionIds = $assignments->pluck('section_id')->unique()->values();
            $subjectIds = $assignments->pluck('subject_id')->unique()->values();

            $subjectSectionMap = $assignments
                ->groupBy('subject_id')
                ->map(fn($items) => $items->pluck('section_id')->unique()->values()->all())
                ->toArray();

            $subjects = Subject::whereIn('id', $subjectIds)->orderBy('name')->get();
            $sections = Section::whereIn('id', $sectionIds)->orderBy('name')->get();
            $students = User::query()
                ->where('account_type', 'student')
                ->whereIn('section_id', $sectionIds)
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get();
        } else {
            $subjects = Subject::orderBy('name')->get();
            $sections = Section::orderBy('name')->get();
            $students = User::query()
                ->where('account_type', 'student')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get();
            $subjectSectionMap = [];
        }

        $gradeValuesQuery = GradeEntry::query()
            ->where('subject_id', $subjectId)
            ->where('section_id', $sectionId)
            ->where('term', $term);

        if ($user->account_type === 'faculty') {
            $gradeValuesQuery->where('created_by', $user->id);
        }

        $gradeValues = $gradeValuesQuery
            ->pluck('grade_value', 'student_id')
            ->toArray();

        return view('faculty.grades.edit', [
            'grade' => $grade,
            'subjects' => $subjects,
            'sections' => $sections,
            'students' => $students,
            'subjectSectionMap' => $subjectSectionMap,
            'selectedSubjectId' => $subjectId,
            'selectedSectionId' => $sectionId,
            'selectedTerm' => $term,
            'gradeValues' => $gradeValues,
        ]);
    }

    public function update(Request $request, GradeEntry $grade): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $grade->created_by === $user->id && $grade->status === 'draft', 403);

        $data = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'term' => ['required', 'string', 'max:20'],
            'grade_values' => ['required', 'array'],
            'grade_values.*' => ['nullable', 'integer', 'min:50', 'max:100'],
        ]);

        if ($user->account_type === 'faculty') {
            $assigned = SectionSubjectTeacher::where('teacher_id', $user->id)
                ->where('section_id', $data['section_id'])
                ->where('subject_id', $data['subject_id'])
                ->exists();

            if (! $assigned) {
                return back()->withErrors(['subject_id' => 'You are not assigned to this section and subject.'])->withInput();
            }
        }

        $gradeValues = collect($data['grade_values'] ?? [])
            ->filter(function ($value) {
                return $value !== null && $value !== '';
            });

        if ($gradeValues->isEmpty()) {
            return back()->withErrors(['grade_values' => 'Please enter at least one grade.'])->withInput();
        }

        $studentIds = $gradeValues->keys()->map(fn($id) => (int) $id)->all();
        $validStudentIds = User::query()
            ->where('account_type', 'student')
            ->where('section_id', $data['section_id'])
            ->whereIn('id', $studentIds)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();

        $invalidStudentIds = array_diff($studentIds, $validStudentIds);
        if (! empty($invalidStudentIds)) {
            return back()->withErrors(['grade_values' => 'Some students are not part of the selected section.'])->withInput();
        }

        $existingGrades = GradeEntry::query()
            ->where('subject_id', $data['subject_id'])
            ->where('section_id', $data['section_id'])
            ->where('term', $data['term'])
            ->where('created_by', $user->id)
            ->whereIn('student_id', $studentIds)
            ->get()
            ->keyBy('student_id');

        $missingIds = array_diff($studentIds, $existingGrades->keys()->map(fn($id) => (int) $id)->all());
        if (! empty($missingIds)) {
            return back()->withErrors(['grade_values' => 'Some students do not have existing draft grades to update.'])->withInput();
        }

        foreach ($gradeValues as $studentId => $gradeValue) {
            $entry = $existingGrades->get((int) $studentId);
            if (! $entry || $entry->status !== 'draft') {
                return back()->withErrors(['grade_values' => 'Only draft grades can be updated.'])->withInput();
            }
            $entry->update(['grade_value' => $gradeValue]);
        }

        return redirect()->route('faculty.grades.index')->with('success', 'Grades updated.');
    }

    public function destroy(Request $request, GradeEntry $grade): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $grade->created_by === $user->id && $grade->status === 'draft', 403);

        $grade->delete();
        return redirect()->route('faculty.grades.index')->with('success', 'Grade entry deleted.');
    }

    public function submit(Request $request, GradeEntry $grade): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $grade->created_by === $user->id && $grade->status === 'draft', 403);

        $grade->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('faculty.grades.index')->with('success', 'Grade entry submitted for approval.');
    }

    private function resolveGradeSubjects(SectionSubjectTeacher $assignment): Collection
    {
        $assignment->loadMissing(['section', 'subject']);

        $gradeNumber = $this->extractGradeNumber((string) (optional($assignment->section)->grade_level ?? ''));
        $mapehNames = self::MAPEH_COMPONENTS_BY_GRADE[$gradeNumber] ?? [];
        $assignmentSubjectName = (string) (optional($assignment->subject)->name ?? '');
        $normalizedAssignmentSubjectName = mb_strtoupper($assignmentSubjectName, 'UTF-8');

        if (empty($mapehNames) || ! in_array($normalizedAssignmentSubjectName, $mapehNames, true)) {
            return collect([
                (object) [
                    'id' => (int) $assignment->subject_id,
                    'name' => $assignmentSubjectName !== '' ? $assignmentSubjectName : 'N/A',
                ],
            ]);
        }

        $rows = SectionSubjectTeacher::query()
            ->with('subject')
            ->where('section_id', $assignment->section_id)
            ->where('teacher_id', $assignment->teacher_id)
            ->whereHas('subject', function ($query) use ($mapehNames) {
                $query->whereRaw('UPPER(name) IN (' . implode(',', array_fill(0, count($mapehNames), '?')) . ')', $mapehNames);
            })
            ->get();

        if ($rows->isEmpty()) {
            return collect([
                (object) [
                    'id' => (int) $assignment->subject_id,
                    'name' => $assignmentSubjectName !== '' ? $assignmentSubjectName : 'N/A',
                ],
            ]);
        }

        $subjectsByName = $rows
            ->map(function (SectionSubjectTeacher $row) {
                return [
                    'id' => (int) $row->subject_id,
                    'name' => (string) (optional($row->subject)->name ?? 'N/A'),
                    'normalized_name' => mb_strtoupper((string) (optional($row->subject)->name ?? 'N/A'), 'UTF-8'),
                ];
            })
            ->unique('id')
            ->keyBy('normalized_name');

        $ordered = collect($mapehNames)
            ->map(function (string $name) use ($subjectsByName) {
                return $subjectsByName->get($name);
            })
            ->filter()
            ->values();

        if ($ordered->isEmpty()) {
            return collect([
                (object) [
                    'id' => (int) $assignment->subject_id,
                    'name' => $assignmentSubjectName !== '' ? $assignmentSubjectName : 'N/A',
                ],
            ]);
        }

        return $ordered->map(function (array $subject) {
            return (object) [
                'id' => (int) $subject['id'],
                'name' => $subject['name'],
            ];
        })->values();
    }

    private function extractGradeNumber(string $gradeLevel): ?int
    {
        if (preg_match('/(7|8|9|10|11|12)/', $gradeLevel, $match)) {
            return (int) $match[1];
        }

        return null;
    }
}
