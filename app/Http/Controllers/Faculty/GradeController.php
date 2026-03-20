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
use Illuminate\View\View;

class GradeController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user && in_array($user->account_type, ['faculty', 'admin'], true), 403);

        $filters = $request->only(['search', 'term', 'status']);

        $entries = GradeEntry::query()
            ->where('created_by', $user->id)
            ->when($filters['term'] ?? null, fn ($q, $term) => $q->where('term', $term))
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->orderByDesc('updated_at')
            ->paginate(12)
            ->withQueryString();

        if ($user->account_type === 'faculty') {
            $assignments = SectionSubjectTeacher::where('teacher_id', $user->id)->get();
            $sectionIds = $assignments->pluck('section_id')->unique()->values();
            $subjectIds = $assignments->pluck('subject_id')->unique()->values();

            $subjectSectionMap = $assignments
                ->groupBy('subject_id')
                ->map(fn ($items) => $items->pluck('section_id')->unique()->values()->all())
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

        $gradeLevels = $sections->pluck('grade_level')->filter()->unique()->values();

        return view('faculty.grades.index', compact('entries', 'filters', 'subjects', 'sections', 'students', 'subjectSectionMap', 'gradeLevels'));
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
                ->map(fn ($items) => $items->pluck('section_id')->unique()->values()->all())
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

        $studentIds = $gradeValues->keys()->map(fn ($id) => (int) $id)->all();
        $validStudentIds = User::query()
            ->where('account_type', 'student')
            ->where('section_id', $data['section_id'])
            ->whereIn('id', $studentIds)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
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
                ->map(fn ($items) => $items->pluck('section_id')->unique()->values()->all())
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

        $studentIds = $gradeValues->keys()->map(fn ($id) => (int) $id)->all();
        $validStudentIds = User::query()
            ->where('account_type', 'student')
            ->where('section_id', $data['section_id'])
            ->whereIn('id', $studentIds)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
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

        $missingIds = array_diff($studentIds, $existingGrades->keys()->map(fn ($id) => (int) $id)->all());
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
}
