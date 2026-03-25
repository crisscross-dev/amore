<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GradeEntry;
use App\Models\SectionSubjectTeacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradeApprovalController extends Controller
{
    public function index(): View
    {
        $sheetGroups = GradeEntry::query()
            ->with(['section', 'subject', 'creator'])
            ->where('status', 'submitted')
            ->orderBy('submitted_at', 'desc')
            ->get()
            ->groupBy(function (GradeEntry $grade) {
                return $grade->section_id . '-' . $grade->subject_id . '-' . $grade->term . '-' . $grade->created_by;
            })
            ->map(function ($grades) {
                $first = $grades->first();

                return [
                    'representative' => $first,
                    'section_name' => $first->section->name ?? 'N/A',
                    'grade_level' => $first->section->grade_level ?? 'N/A',
                    'subject_name' => $first->subject->name ?? 'N/A',
                    'teacher_name' => trim(($first->creator->first_name ?? '') . ' ' . ($first->creator->last_name ?? '')),
                    'term' => $first->term,
                    'student_count' => $grades->count(),
                    'submitted_at' => optional($grades->sortByDesc('submitted_at')->first()?->submitted_at)->format('M d, Y h:i A'),
                ];
            })
            ->values();

        return view('admin.grade_approvals.index', compact('sheetGroups'));
    }

    public function show(GradeEntry $grade): View
    {
        $assignment = SectionSubjectTeacher::query()
            ->with(['subject', 'section', 'teacher'])
            ->where('section_id', $grade->section_id)
            ->where('subject_id', $grade->subject_id)
            ->where('teacher_id', $grade->created_by)
            ->first();

        abort_unless($assignment, 404);

        $term = $grade->term;

        $students = User::query()
            ->where('account_type', 'student')
            ->where('section_id', $assignment->section_id)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $gradeEntries = GradeEntry::query()
            ->where('section_id', $assignment->section_id)
            ->where('subject_id', $assignment->subject_id)
            ->where('term', $term)
            ->where('created_by', $assignment->teacher_id)
            ->where('status', 'submitted')
            ->get()
            ->keyBy('student_id');

        return view('admin.grade_approvals.show', compact('grade', 'assignment', 'students', 'gradeEntries', 'term'));
    }

    public function update(Request $request, GradeEntry $grade): RedirectResponse
    {
        abort_unless($grade->status === 'submitted', 403, 'Only submitted sheets can be edited by admin.');

        $data = $request->validate([
            'term' => ['required', 'string', 'max:20'],
            'grade_values' => ['required', 'array'],
            'grade_values.*' => ['nullable', 'numeric', 'min:50', 'max:100'],
            'faculty_remarks' => ['nullable', 'array'],
            'faculty_remarks.*' => ['nullable', 'string', 'max:1000'],
        ]);

        $assignment = SectionSubjectTeacher::query()
            ->where('section_id', $grade->section_id)
            ->where('subject_id', $grade->subject_id)
            ->where('teacher_id', $grade->created_by)
            ->firstOrFail();

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
            ->where('subject_id', $assignment->subject_id)
            ->where('term', $data['term'])
            ->where('created_by', $assignment->teacher_id)
            ->where('status', 'submitted')
            ->whereIn('student_id', $studentIds)
            ->get()
            ->keyBy('student_id');

        foreach ($studentIds as $studentId) {
            $existing = $existingEntries->get($studentId);
            if (! $existing) {
                continue;
            }

            $gradeValue = $gradeValues->get((string) $studentId, $gradeValues->get($studentId));
            $remarkValue = $remarks->get((string) $studentId, $remarks->get($studentId));
            $remarkValue = is_string($remarkValue) ? trim($remarkValue) : $remarkValue;

            if ($gradeValue === null || $gradeValue === '') {
                continue;
            }

            $existing->update([
                'grade_value' => $gradeValue,
                'faculty_remark' => $remarkValue ?: null,
            ]);
        }

        return redirect()
            ->route('admin.grade-approvals.show', $grade)
            ->with('success', 'Grade sheet updated successfully.');
    }

    public function approve(Request $request, GradeEntry $grade): RedirectResponse
    {
        abort_unless($grade->status === 'submitted', 403, 'Only submitted grades can be approved.');

        GradeEntry::query()
            ->where('section_id', $grade->section_id)
            ->where('subject_id', $grade->subject_id)
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

        GradeEntry::query()
            ->where('section_id', $grade->section_id)
            ->where('subject_id', $grade->subject_id)
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
}
