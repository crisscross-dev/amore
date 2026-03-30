<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\SectionSubjectTeacher;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user && $user->account_type === 'student', 403);

        // Get the student's section
        $sectionId = $user->section_id;

        if (!$sectionId) {
            return view('student.subjects.index', [
                'subjectAssignments' => collect([]),
                'section' => null,
                'message' => 'You are not assigned to any section yet. Please contact your administrator.',
                'subjectsLiveSignature' => $this->buildLiveSignature($user),
            ]);
        }

        $subjectAssignments = SectionSubjectTeacher::with(['subject', 'teacher'])
            ->where('section_id', $sectionId)
            ->whereHas('subject', function ($query) {
                $query->where('is_active', true);
            })
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->orderBy('subject_id')
            ->get();

        $subjectsLiveSignature = $this->buildLiveSignature($user);

        return view('student.subjects.index', [
            'subjectAssignments' => $subjectAssignments,
            'section' => $user->section,
            'message' => null,
            'subjectsLiveSignature' => $subjectsLiveSignature,
        ]);
    }

    public function liveSignature(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user && $user->account_type === 'student', 403);

        return response()->json([
            'signature' => $this->buildLiveSignature($user),
            'generated_at' => now()->toIso8601String(),
        ]);
    }

    private function buildLiveSignature(User $student): string
    {
        $sectionId = (int) ($student->section_id ?? 0);
        $assignmentQuery = SectionSubjectTeacher::query()->where('section_id', $sectionId);

        $subjectIds = $sectionId > 0
            ? (clone $assignmentQuery)->pluck('subject_id')->filter()->unique()->values()->all()
            : [];

        $teacherIds = $sectionId > 0
            ? (clone $assignmentQuery)->pluck('teacher_id')->filter()->unique()->values()->all()
            : [];

        $payload = [
            'student_id' => (int) $student->id,
            'student_section_id' => $sectionId,
            'student_updated_at' => $this->timestampOrZero($student->updated_at),
            'section_updated_at' => $sectionId > 0
                ? $this->timestampOrZero(Section::query()->whereKey($sectionId)->max('updated_at'))
                : '0',
            'assignment_count' => $sectionId > 0 ? (clone $assignmentQuery)->count() : 0,
            'assignment_updated_at' => $sectionId > 0
                ? $this->timestampOrZero((clone $assignmentQuery)->max('updated_at'))
                : '0',
            'subject_updated_at' => !empty($subjectIds)
                ? $this->timestampOrZero(Subject::query()->whereIn('id', $subjectIds)->max('updated_at'))
                : '0',
            'teacher_updated_at' => !empty($teacherIds)
                ? $this->timestampOrZero(User::query()->whereIn('id', $teacherIds)->max('updated_at'))
                : '0',
        ];

        return hash('sha256', json_encode($payload));
    }

    private function timestampOrZero(mixed $value): string
    {
        if (empty($value)) {
            return '0';
        }

        if ($value instanceof \DateTimeInterface) {
            return (string) $value->getTimestamp();
        }

        $timestamp = strtotime((string) $value);
        return $timestamp ? (string) $timestamp : '0';
    }
}
