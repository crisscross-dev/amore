<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\GradeEntry;
use App\Models\Section;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

        return view('faculty.grades.index', compact('entries', 'filters'));
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        abort_unless($user && in_array($user->account_type, ['faculty', 'admin'], true), 403);

        $subjects = Subject::orderBy('name')->get();
        $sections = Section::orderBy('name')->get();
        $students = User::query()
            ->where('account_type', 'student')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('faculty.grades.create', compact('subjects', 'sections', 'students'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && in_array($user->account_type, ['faculty', 'admin'], true), 403);

        $data = $request->validate([
            'section_id' => ['required', 'exists:sections,id'],
            'student_id' => [
                'required',
                Rule::exists('users', 'id')
                    ->where('account_type', 'student')
                    ->where('section_id', $request->input('section_id')),
            ],
            'subject_id' => ['required', 'exists:subjects,id'],
            'term' => ['required', 'string', 'max:20'],
            'grade_value' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $data['status'] = 'draft';
        $data['created_by'] = $user->id;

        GradeEntry::create($data);

        return redirect()->route('faculty.grades.index')->with('success', 'Grade entry saved as draft.');
    }

    public function edit(Request $request, GradeEntry $grade): View
    {
        $user = $request->user();
        abort_unless($user && $grade->created_by === $user->id, 403);

        $subjects = Subject::orderBy('name')->get();
        $sections = Section::orderBy('name')->get();
        $students = User::query()
            ->where('account_type', 'student')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('faculty.grades.edit', compact('grade', 'subjects', 'sections', 'students'));
    }

    public function update(Request $request, GradeEntry $grade): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $grade->created_by === $user->id && $grade->status === 'draft', 403);

        $data = $request->validate([
            'section_id' => ['required', 'exists:sections,id'],
            'student_id' => [
                'required',
                Rule::exists('users', 'id')
                    ->where('account_type', 'student')
                    ->where('section_id', $request->input('section_id')),
            ],
            'subject_id' => ['required', 'exists:subjects,id'],
            'term' => ['required', 'string', 'max:20'],
            'grade_value' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $grade->update($data);

        return redirect()->route('faculty.grades.index')->with('success', 'Grade entry updated.');
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
