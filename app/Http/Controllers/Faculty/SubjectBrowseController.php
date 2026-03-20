<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectBrowseController extends Controller
{
    protected string $viewBase = 'faculty.subjects.';

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

    public function index(Request $request): View
    {
        // Optional: restrict to faculty accounts; allow admin to preview
        $user = $request->user();
        if (! $user || ! in_array($user->account_type, ['faculty', 'admin'], true)) {
            abort(403);
        }

        $filters = $request->only(['search', 'subject_type', 'grade_level']);

        $subjects = Subject::query()
            ->with([
                'gradeLevels',
                'sectionTeachers' => function ($query) use ($user) {
                    $query->where('teacher_id', $user->id)
                        ->with(['section.students']);
                },
            ])
            ->whereHas('sectionTeachers', function ($query) use ($user) {
                $query->where('teacher_id', $user->id);
            })
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($builder) use ($search) {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($filters['subject_type'] ?? null, fn ($query, $type) => $query->where('subject_type', $type))
            ->when($filters['grade_level'] ?? null, function ($query, $level) {
                $this->applyGradeFilter($query, $level);
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view($this->viewBase . 'index', [
            'subjects' => $subjects,
            'subjectTypes' => self::SUBJECT_TYPES,
            'gradeLevels' => self::GRADE_LEVELS,
            'filterGradeLevels' => self::FILTER_GRADE_LEVELS,
            'filters' => $filters,
        ]);
    }

    private function applyGradeFilter($query, string $level): void
    {
        if ($level === 'junior') {
            $this->filterByGrades($query, ['7', '8', '9', '10']);
            return;
        }

        if ($level === 'senior') {
            $this->filterByGrades($query, ['11', '12']);
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
}
