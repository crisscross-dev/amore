<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class SubjectController extends Controller
{
    protected string $viewBase = 'admin.subjects.';

    protected string $routeBase = 'admin.subjects.';

    private const SUBJECT_TYPES = [
        'core' => 'Core',
        'elective' => 'Elective',
        'specialized' => 'Specialized',
        'extracurricular' => 'Extracurricular',
    ];

    private const GRADE_LEVELS = [
        '7' => 'Grade 7',
        '8' => 'Grade 8',
        '9' => 'Grade 9',
        '10' => 'Grade 10',
        '11' => 'Grade 11',
        '12' => 'Grade 12',
        'all' => 'All Levels',
    ];

    private const FILTER_GRADE_LEVELS = [
        '7' => 'Grade 7',
        '8' => 'Grade 8',
        '9' => 'Grade 9',
        '10' => 'Grade 10',
        '11' => 'Grade 11',
        '12' => 'Grade 12',
        'junior' => 'Junior High (7–10)',
        'senior' => 'Senior High (11–12)',
        'all' => 'All Levels',
    ];

    private const SHS_GRADE_LEVELS = [
        '11',
        '12',
    ];

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'subject_type', 'grade_level']);

        $subjects = Subject::query()
            ->with('gradeLevels')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($builder) use ($search) {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($filters['subject_type'] ?? null, fn($query, $type) => $query->where('subject_type', $type))
            ->when($filters['grade_level'] ?? null, function ($query, $level) {
                $this->applyGradeFilter($query, $level);
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return $this->renderView('index', [
            'subjects' => $subjects,
            'subject' => new Subject(),
            'subjectTypes' => self::SUBJECT_TYPES,
            'gradeLevels' => self::GRADE_LEVELS,
            'filterGradeLevels' => self::FILTER_GRADE_LEVELS,
            'filters' => $filters,
            'importSummary' => session('import_summary'),
            'shsGradeLevels' => self::SHS_GRADE_LEVELS,
        ]);
    }

    public function create(): View
    {
        return $this->renderView('create', [
            'subject' => new Subject(),
            'subjectTypes' => self::SUBJECT_TYPES,
            'gradeLevels' => self::GRADE_LEVELS,
            'shsGradeLevels' => self::SHS_GRADE_LEVELS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateSubject($request);

        $gradeLevels = $validated['grade_levels'] ?? [];
        unset($validated['grade_levels']);

        $subject = Subject::create($validated);
        $this->syncGradeLevels($subject, $gradeLevels);

        return redirect()
            ->route($this->routeName('index'))
            ->with('success', 'Subject created successfully.');
    }

    public function edit(Subject $subject): View
    {
        $subject->load('gradeLevels');

        return $this->renderView('edit', [
            'subject' => $subject,
            'subjectTypes' => self::SUBJECT_TYPES,
            'gradeLevels' => self::GRADE_LEVELS,
            'shsGradeLevels' => self::SHS_GRADE_LEVELS,
        ]);
    }

    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $validated = $this->validateSubject($request);

        $gradeLevels = $validated['grade_levels'] ?? [];
        unset($validated['grade_levels']);

        $subject->update($validated);
        $this->syncGradeLevels($subject, $gradeLevels);

        return redirect()
            ->route($this->routeName('index'))
            ->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $subject->delete();

        return redirect()
            ->route($this->routeName('index'))
            ->with('success', 'Subject deleted successfully.');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'import_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $file = $request->file('import_file');
        $path = $file->getRealPath();

        $handle = fopen($path, 'rb');

        if ($handle === false) {
            return back()->withErrors('Unable to read the uploaded file. Please try again.');
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
            $normalized = $this->prepareRowForValidation($rowData);

            $validator = Validator::make($normalized, $this->validationRules(), [], $this->validationAttributes());

            if ($validator->fails()) {
                $errors[] = 'Row ' . $lineNumber . ': ' . implode('; ', $validator->errors()->all());
                continue;
            }

            $payload = $validator->validated();

            $payload['grade_level'] = $payload['grade_level'] ?? 'all';
            $gradeLevels = $this->normalizeGradeLevelsInput([$payload['grade_level']]);
            $payload['subject_type'] = $this->applySubjectTypeRules($payload['subject_type'] ?? null, $gradeLevels);

            $hours = $rowData['hours_per_week'] ?? null;
            $payload['hours_per_week'] = $hours === null || $hours === '' ? null : (int) $hours;

            $subject = Subject::updateOrCreate([
                'name' => $payload['name'],
                'grade_level' => $payload['grade_level'],
            ], $payload);

            $this->syncGradeLevels($subject, $gradeLevels);

            if ($subject->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        fclose($handle);

        $summary = [
            'processed' => $processed,
            'created' => $created,
            'updated' => $updated,
            'errors' => $errors,
        ];

        return redirect()
            ->route($this->routeName('index'))
            ->with('success', 'Subject import completed.')
            ->with('import_summary', $summary);
    }

    private function validateSubject(Request $request): array
    {
        $validated = $request->validate($this->validationRules(), [], $this->validationAttributes());

        $gradeLevels = $this->normalizeGradeLevelsInput($validated['grade_levels'] ?? []);
        if (empty($gradeLevels) && isset($validated['grade_level'])) {
            $gradeLevels = $this->normalizeGradeLevelsInput([$validated['grade_level']]);
        }

        $validated['grade_levels'] = $gradeLevels;
        $validated['grade_level'] = $this->determinePrimaryGradeLevel($gradeLevels);
        $validated['subject_type'] = $this->applySubjectTypeRules($validated['subject_type'] ?? null, $gradeLevels);
        $validated['hours_per_week'] = $validated['hours_per_week'] === null || $validated['hours_per_week'] === ''
            ? null
            : (int) $validated['hours_per_week'];

        return $validated;
    }

    private function validationRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'subject_type' => ['nullable', 'in:' . implode(',', array_keys(self::SUBJECT_TYPES))],
            'grade_level' => ['nullable', 'in:' . implode(',', array_keys(self::GRADE_LEVELS))],
            'grade_levels' => ['required_without:grade_level', 'array', 'min:1'],
            'grade_levels.*' => ['in:' . implode(',', array_keys(self::GRADE_LEVELS))],
            'hours_per_week' => ['nullable', 'integer', 'min:1', 'max:40'],
        ];
    }

    private function validationAttributes(): array
    {
        return [
            'name' => 'subject name',
            'subject_type' => 'subject type',
            'grade_level' => 'grade level',
            'grade_levels' => 'grade levels',
            'hours_per_week' => 'hours per week',
        ];
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

    private function prepareRowForValidation(array $rowData): array
    {
        $prepared = $rowData;

        if (isset($prepared['subject_type'])) {
            $prepared['subject_type'] = $this->normalizeSubjectType($prepared['subject_type']);
        }

        if (isset($prepared['grade_level'])) {
            $prepared['grade_level'] = $this->normalizeGradeLevel($prepared['grade_level']);
        }

        unset($prepared['department']);
        unset($prepared['is_active']);

        return $prepared;
    }

    private function applySubjectTypeRules(?string $subjectType, array $gradeLevels): ?string
    {
        if (! $subjectType) {
            return null;
        }

        if (! $this->hasShsGrade($gradeLevels)) {
            return null;
        }

        return array_key_exists($subjectType, self::SUBJECT_TYPES) ? $subjectType : null;
    }

    private function hasShsGrade(array $gradeLevels): bool
    {
        foreach ($gradeLevels as $level) {
            if (in_array($level, self::SHS_GRADE_LEVELS, true)) {
                return true;
            }
        }

        return false;
    }

    private function normalizeSubjectType(?string $type): ?string
    {
        if (! $type) {
            return null;
        }

        $normalized = strtolower($type);

        return array_key_exists($normalized, self::SUBJECT_TYPES) ? $normalized : null;
    }

    private function normalizeGradeLevel(?string $level): ?string
    {
        if (! $level) {
            return null;
        }

        $normalized = strtolower(trim($level));

        if (in_array($normalized, ['all', 'all levels'], true)) {
            return 'all';
        }

        if (preg_match('/^(grade\s*)?(7|8|9|10|11|12)$/', $normalized, $matches)) {
            return $matches[2];
        }

        return array_key_exists($normalized, self::GRADE_LEVELS) ? $normalized : null;
    }

    private function normalizeGradeLevelsInput(array $gradeLevels): array
    {
        $normalized = [];

        foreach ($gradeLevels as $level) {
            $value = $this->normalizeGradeLevel($level);
            if ($value) {
                $normalized[] = $value;
            }
        }

        $normalized = array_values(array_unique($normalized));

        if (in_array('all', $normalized, true)) {
            return ['7', '8', '9', '10', '11', '12'];
        }

        return $normalized;
    }

    private function determinePrimaryGradeLevel(array $gradeLevels): string
    {
        if (empty($gradeLevels)) {
            return 'all';
        }

        if (count($gradeLevels) === 1) {
            return $gradeLevels[0];
        }

        return 'all';
    }

    private function syncGradeLevels(Subject $subject, array $gradeLevels): void
    {
        if (empty($gradeLevels)) {
            $subject->gradeLevels()->delete();
            return;
        }

        $subject->gradeLevels()->delete();
        $rows = array_map(fn($level) => ['grade_level' => $level], $gradeLevels);
        $subject->gradeLevels()->createMany($rows);
    }

    private function applyGradeFilter($query, string $level): void
    {
        if ($level === 'junior') {
            $grades = ['7', '8', '9', '10'];
            $this->filterByGrades($query, $grades);
            return;
        }

        if ($level === 'senior') {
            $grades = ['11', '12'];
            $this->filterByGrades($query, $grades);
            return;
        }

        if ($level === 'all') {
            $query->where(function ($builder) {
                $builder->whereHas('gradeLevels', function ($subQuery) {
                    $subQuery->whereIn('grade_level', ['7', '8', '9', '10', '11', '12']);
                })->orWhere(function ($subQuery) {
                    $subQuery->whereDoesntHave('gradeLevels')
                        ->where('grade_level', 'all');
                });
            });
            return;
        }

        $this->filterByGrades($query, [$level]);
    }

    private function filterByGrades($query, array $grades): void
    {
        $query->where(function ($builder) use ($grades) {
            $builder->whereHas('gradeLevels', function ($subQuery) use ($grades) {
                $subQuery->whereIn('grade_level', $grades);
            })
                ->orWhere(function ($subQuery) use ($grades) {
                    $subQuery->whereDoesntHave('gradeLevels')
                        ->where(function ($fallback) use ($grades) {
                            $fallback->whereIn('grade_level', $grades)
                                ->orWhere('grade_level', 'all');
                        });
                });
        });
    }

    protected function renderView(string $view, array $data = []): View
    {
        return view($this->viewBase . $view, array_merge([
            'routeBase' => $this->routeBase,
        ], $data));
    }

    protected function routeName(string $suffix): string
    {
        return $this->routeBase . $suffix;
    }
}
