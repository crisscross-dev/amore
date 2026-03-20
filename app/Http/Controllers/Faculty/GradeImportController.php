<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\GradeEntry;
use App\Models\SectionSubjectTeacher;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;

class GradeImportController extends Controller
{
    public function create(Request $request): View
    {
        $user = $request->user();
        abort_unless($user && in_array($user->account_type, ['faculty', 'admin'], true), 403);

        return view('faculty.grades.import');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && in_array($user->account_type, ['faculty', 'admin'], true), 403);

        $validated = $request->validate([
            'import_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
            'submit_after_import' => ['nullable', 'boolean'],
        ]);

        $submit = (bool)($validated['submit_after_import'] ?? false);
        $path = $request->file('import_file')->getRealPath();

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            return back()->withErrors(['import_file' => 'Unable to read the uploaded file. Please try again.']);
        }

        $header = null;
        $lineNumber = 0;
        $processed = 0;
        $created = 0;
        $updated = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            $lineNumber++;

            if ($lineNumber === 1) {
                $header = array_map('trim', $row);
                continue;
            }

            if (! $header) {
                $errors[] = 'The uploaded file is missing a header row.';
                break;
            }

            if ($this->isRowEmpty($row)) {
                continue;
            }

            $processed++;
            $rowData = $this->mapRowToData($header, $row);
            $normalized = $this->normalizeRow($rowData);

            $validator = Validator::make($normalized, [
                'lrn' => ['required', 'digits:12'],
                'subject' => ['required', 'string'],
                'term' => ['required', 'string', 'in:Q1,Q2,Q3,Q4,Midterm,Final'],
                'grade_value' => ['required', 'numeric', 'min:0', 'max:100'],
            ], [], [
                'lrn' => 'LRN',
                'subject' => 'subject name/code',
                'term' => 'term',
                'grade_value' => 'grade value',
            ]);

            if ($validator->fails()) {
                $errors[] = 'Row ' . $lineNumber . ': ' . implode('; ', $validator->errors()->all());
                continue;
            }

            $data = $validator->validated();

            $student = User::where('lrn', $data['lrn'])->where('account_type', 'student')->first();
            if (! $student) {
                $errors[] = 'Row ' . $lineNumber . ': student LRN not found.';
                continue;
            }

            if ($user->account_type === 'faculty' && ! $student->section_id) {
                $errors[] = 'Row ' . $lineNumber . ': student has no section assignment.';
                continue;
            }

            $subject = Subject::where('name', $data['subject'])->first();
            if (! $subject) {
                $errors[] = 'Row ' . $lineNumber . ': subject not found.';
                continue;
            }

            if ($user->account_type === 'faculty') {
                $assigned = SectionSubjectTeacher::where('teacher_id', $user->id)
                    ->where('subject_id', $subject->id)
                    ->where('section_id', $student->section_id)
                    ->exists();

                if (! $assigned) {
                    $errors[] = 'Row ' . $lineNumber . ': subject not assigned to this student section.';
                    continue;
                }
            }

            $payload = [
                'section_id' => $student->section_id,
                'student_id' => $student->id,
                'subject_id' => $subject->id,
                'term' => $data['term'],
                'grade_value' => (float) $data['grade_value'],
            ];

            $existing = GradeEntry::where('student_id', $student->id)
                ->where('subject_id', $subject->id)
                ->where('term', $data['term'])
                ->first();

            if ($existing) {
                if ($existing->status !== 'draft') {
                    $errors[] = 'Row ' . $lineNumber . ': existing non-draft entry found (status: ' . $existing->status . ').';
                    continue;
                }

                $existing->update(array_merge($payload, [
                    'status' => $submit ? 'submitted' : 'draft',
                    'submitted_at' => $submit ? now() : null,
                ]));
                $updated++;
            } else {
                GradeEntry::create(array_merge($payload, [
                    'status' => $submit ? 'submitted' : 'draft',
                    'submitted_at' => $submit ? now() : null,
                    'created_by' => $user->id,
                ]));
                $created++;
            }
        }

        fclose($handle);

        $summary = compact('processed', 'created', 'updated', 'errors');

        return redirect()
            ->route('faculty.grades.import.result')
            ->with('import_summary', $summary)
            ->with('success', 'Grade import completed.');
    }

    private function mapRowToData(array $header, array $row): array
    {
        $row = array_pad($row, count($header), null);
        $mapped = array_combine($header, $row) ?: [];
        return array_change_key_case(array_map('trim', $mapped), CASE_LOWER);
    }

    private function isRowEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }
        return true;
    }

    private function normalizeRow(array $row): array
    {
        $subject = $row['subject_code'] ?? $row['subject_name'] ?? $row['subject'] ?? null;
        return [
            'lrn' => $row['lrn'] ?? null,
            'subject' => $subject,
            'term' => $row['term'] ?? null,
            'grade_value' => $row['grade_value'] ?? null,
        ];
    }
}
