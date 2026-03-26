<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GradeEntry;
use App\Models\SectionSubjectTeacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class GradeApprovalController extends Controller
{
    private const MAPEH_COMPONENTS_BY_GRADE = [
        7 => ['MAPEH - Music & Arts', 'MAPEH - PE & Health', 'Music & Arts', 'PE & Health'],
        8 => ['MAPEH - Music & Arts', 'MAPEH - PE & Health', 'Music & Arts', 'PE & Health'],
        9 => ['MAPEH - Music', 'MAPEH - Arts', 'MAPEH - PE', 'MAPEH - Health', 'Music', 'Arts', 'PE', 'Health'],
        10 => ['MAPEH - Music', 'MAPEH - Arts', 'MAPEH - PE', 'MAPEH - Health', 'Music', 'Arts', 'PE', 'Health'],
    ];

    public function index(): View
    {
        $sheetGroups = GradeEntry::query()
            ->with(['section', 'subject', 'creator'])
            ->where('status', 'submitted')
            ->orderBy('submitted_at', 'desc')
            ->get()
            ->groupBy(function (GradeEntry $grade) {
                $subjectName = trim((string) ($grade->subject->name ?? ''));
                $isMapehComponent = str_starts_with(strtoupper($subjectName), 'MAPEH');
                $subjectGroupKey = $isMapehComponent ? 'mapeh' : ('subject-' . $grade->subject_id);

                return $grade->section_id . '-' . $subjectGroupKey . '-' . $grade->term . '-' . $grade->created_by;
            })
            ->map(function ($grades) {
                $first = $grades->sortByDesc('submitted_at')->first();
                $isMapehGroup = $grades->contains(function (GradeEntry $entry) {
                    $name = trim((string) ($entry->subject->name ?? ''));

                    return str_starts_with(strtoupper($name), 'MAPEH');
                });

                return [
                    'representative' => $first,
                    'dedupe_key' => $first->section_id . '-' . ($isMapehGroup ? 'mapeh' : $first->subject_id) . '-' . $first->term,
                    'section_name' => $first->section->name ?? 'N/A',
                    'grade_level' => $first->section->grade_level ?? 'N/A',
                    'subject_name' => $isMapehGroup ? 'MAPEH' : ($first->subject->name ?? 'N/A'),
                    'teacher_name' => trim(($first->creator->first_name ?? '') . ' ' . ($first->creator->last_name ?? '')),
                    'term' => $first->term,
                    'student_count' => $grades->pluck('student_id')->unique()->count(),
                    'submitted_at_raw' => $grades->sortByDesc('submitted_at')->first()?->submitted_at,
                    'submitted_at' => optional($grades->sortByDesc('submitted_at')->first()?->submitted_at)->format('M d, Y h:i A'),
                ];
            })
            ->sortByDesc('submitted_at_raw')
            ->unique('dedupe_key')
            ->values();

        return view('admin.grade_approvals.index', compact('sheetGroups'));
    }

    public function show(GradeEntry $grade): View
    {
        $assignment = $this->resolveAssignmentFromGrade($grade);
        $gradeSubjects = $this->resolveGradeSubjects($assignment);
        $allowedSubjectIds = $gradeSubjects->pluck('id')->map(fn($id) => (int) $id)->values()->all();

        $term = $grade->term;

        $students = User::query()
            ->where('account_type', 'student')
            ->where('section_id', $assignment->section_id)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $gradeEntries = GradeEntry::query()
            ->where('section_id', $assignment->section_id)
            ->whereIn('subject_id', $allowedSubjectIds)
            ->where('term', $term)
            ->where('created_by', $assignment->teacher_id)
            ->where('status', 'submitted')
            ->get();

        $gradeEntriesByStudent = $gradeEntries
            ->groupBy('student_id')
            ->map(fn(Collection $entries) => $entries->keyBy('subject_id'));

        return view('admin.grade_approvals.show', compact('grade', 'assignment', 'students', 'gradeEntriesByStudent', 'gradeSubjects', 'term'));
    }

    public function update(Request $request, GradeEntry $grade): RedirectResponse
    {
        abort_unless($grade->status === 'submitted', 403, 'Only submitted sheets can be edited by admin.');

        $assignment = $this->resolveAssignmentFromGrade($grade);
        $gradeSubjects = $this->resolveGradeSubjects($assignment);
        $allowedSubjectIds = $gradeSubjects->pluck('id')->map(fn($id) => (int) $id)->values()->all();

        $data = $request->validate([
            'term' => ['required', 'string', 'max:20'],
            'grade_values' => ['required', 'array'],
            'grade_values.*' => ['required', 'array'],
            'grade_values.*.*' => ['nullable', 'numeric', 'min:50', 'max:100'],
            'faculty_remarks' => ['nullable', 'array'],
            'faculty_remarks.*' => ['nullable', 'string', 'max:1000'],
        ]);

        $gradeValues = collect($data['grade_values'] ?? []);
        $remarks = collect($data['faculty_remarks'] ?? []);
        $studentIds = $gradeValues->keys()
            ->merge($remarks->keys())
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $existingEntries = GradeEntry::query()
            ->where('section_id', $assignment->section_id)
            ->whereIn('subject_id', $allowedSubjectIds)
            ->where('term', $data['term'])
            ->where('created_by', $assignment->teacher_id)
            ->where('status', 'submitted')
            ->whereIn('student_id', $studentIds)
            ->get()
            ->keyBy(fn(GradeEntry $entry) => $entry->student_id . '|' . $entry->subject_id);

        foreach ($studentIds as $studentId) {
            $rowValues = collect($gradeValues->get((string) $studentId, $gradeValues->get($studentId, [])));
            $remarkValue = $remarks->get((string) $studentId, $remarks->get($studentId));
            $remarkValue = is_string($remarkValue) ? trim($remarkValue) : $remarkValue;

            foreach ($allowedSubjectIds as $subjectId) {
                $existing = $existingEntries->get($studentId . '|' . $subjectId);
                if (! $existing) {
                    continue;
                }

                $gradeValue = $rowValues->get((string) $subjectId, $rowValues->get($subjectId));
                if ($gradeValue === null || $gradeValue === '') {
                    continue;
                }

                $existing->update([
                    'grade_value' => $gradeValue,
                    'faculty_remark' => $remarkValue ?: null,
                ]);
            }
        }

        return redirect()
            ->route('admin.grade-approvals.show', $grade)
            ->with('success', 'Grade sheet updated successfully.');
    }

    public function approve(Request $request, GradeEntry $grade): RedirectResponse
    {
        abort_unless($grade->status === 'submitted', 403, 'Only submitted grades can be approved.');

        $assignment = $this->resolveAssignmentFromGrade($grade);
        $gradeSubjects = $this->resolveGradeSubjects($assignment);
        $allowedSubjectIds = $gradeSubjects->pluck('id')->map(fn($id) => (int) $id)->values()->all();

        GradeEntry::query()
            ->where('section_id', $grade->section_id)
            ->whereIn('subject_id', $allowedSubjectIds)
            ->where('term', $grade->term)
            ->where('created_by', $grade->created_by)
            ->where('status', 'submitted')
            ->update([
                'status' => 'approved',
                'approved_by' => $request->user()->id,
                'approved_at' => now(),
                'rejection_reason' => null,
            ]);

        return redirect()->route('admin.grade-approvals.index')->with('success', 'Grade approved.');
    }

    public function reject(Request $request, GradeEntry $grade): RedirectResponse
    {
        abort_unless($grade->status === 'submitted', 403, 'Only submitted grades can be rejected.');

        $data = $request->validate(['reason' => ['required', 'string']]);

        $assignment = $this->resolveAssignmentFromGrade($grade);
        $gradeSubjects = $this->resolveGradeSubjects($assignment);
        $allowedSubjectIds = $gradeSubjects->pluck('id')->map(fn($id) => (int) $id)->values()->all();

        GradeEntry::query()
            ->where('section_id', $grade->section_id)
            ->whereIn('subject_id', $allowedSubjectIds)
            ->where('term', $grade->term)
            ->where('created_by', $grade->created_by)
            ->where('status', 'submitted')
            ->update([
                'status' => 'rejected',
                'approved_by' => $request->user()->id,
                'approved_at' => now(),
                'rejection_reason' => $data['reason'],
            ]);

        return redirect()->route('admin.grade-approvals.index')->with('success', 'Grade rejected.');
    }

    private function resolveAssignmentFromGrade(GradeEntry $grade): SectionSubjectTeacher
    {
        $assignment = SectionSubjectTeacher::query()
            ->with(['subject', 'section', 'teacher'])
            ->where('section_id', $grade->section_id)
            ->where('subject_id', $grade->subject_id)
            ->where('teacher_id', $grade->created_by)
            ->first();

        abort_unless($assignment, 404);

        return $assignment;
    }

    private function resolveGradeSubjects(SectionSubjectTeacher $assignment): Collection
    {
        $assignment->loadMissing(['section', 'subject']);

        $gradeNumber = $this->extractGradeNumber((string) (optional($assignment->section)->grade_level ?? ''));
        $mapehNames = self::MAPEH_COMPONENTS_BY_GRADE[$gradeNumber] ?? [];
        $assignmentSubjectName = (string) (optional($assignment->subject)->name ?? '');

        if (empty($mapehNames) || ! in_array($assignmentSubjectName, $mapehNames, true)) {
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
                $query->whereIn('name', $mapehNames);
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
                ];
            })
            ->unique('id')
            ->keyBy('name');

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
