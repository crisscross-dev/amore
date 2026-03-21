<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\SectionSubjectTeacher;
use App\Models\Subject;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SectionAssignmentController extends Controller
{
    public function updateAdviser(Request $request, Section $section): RedirectResponse
    {
        $validated = $request->validate([
            'adviser_id' => ['nullable', 'exists:users,id'],
        ]);

        if ($validated['adviser_id'] ?? null) {
            $isFaculty = \App\Models\User::where('id', $validated['adviser_id'])
                ->where('account_type', 'faculty')
                ->exists();
            if (! $isFaculty) {
                return back()->withErrors(['adviser_id' => 'Selected adviser must be a faculty account.']);
            }
        }

        $section->update([
            'adviser_id' => $validated['adviser_id'] ?? null,
        ]);

        return back()->with('success', 'Section adviser updated successfully.');
    }

    public function updateSubjectTeacher(Request $request, Section $section, Subject $subject): RedirectResponse
    {
        $validated = $request->validate([
            'teacher_id' => ['nullable', 'exists:users,id'],
            'room' => ['nullable', 'string', 'max:255'],
            'day_of_week' => ['nullable', 'string', 'max:20'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
        ]);

        $teacherId = isset($validated['teacher_id']) ? (int) $validated['teacher_id'] : null;

        if ($teacherId) {
            $isFaculty = \App\Models\User::where('id', $teacherId)
                ->where('account_type', 'faculty')
                ->exists();
            if (! $isFaculty) {
                return back()->withErrors(['teacher_id' => 'Selected teacher must be a faculty account.']);
            }
        }

        $sectionGrade = $this->normalizeGradeLevel($section->grade_level);
        $validSubject = $this->subjectMatchesGrade($subject, $sectionGrade);

        if (! $validSubject) {
            return back()->withErrors(['subject_id' => 'Selected subject does not match the section grade level.']);
        }

        $room = $validated['room'] ?? null;
        $day = $validated['day_of_week'] ?? null;
        $start = $validated['start_time'] ?? null;
        $end = $validated['end_time'] ?? null;

        if (($day || $start || $end) && (! $day || ! $start || ! $end)) {
            return back()->withErrors(['schedule' => 'Day, start time, and end time are required together.']);
        }

        if (($day || $start || $end) && ! $room) {
            return back()->withErrors(['room' => 'Room is required when a schedule is set.']);
        }

        if ($start && $end && $start >= $end) {
            return back()->withErrors(['schedule' => 'End time must be later than start time.']);
        }

        if ($room && $day && $start && $end) {
            $conflictMessage = $this->detectScheduleConflict(
                $room,
                $day,
                $start,
                $end,
                $section->id,
                $subject->id,
                $teacherId ? (int) $teacherId : null
            );
            if ($conflictMessage) {
                return back()->withErrors(['schedule' => $conflictMessage]);
            }
        }

        SectionSubjectTeacher::updateOrCreate(
            ['section_id' => $section->id, 'subject_id' => $subject->id],
            [
                'teacher_id' => $teacherId,
                'room' => $room,
                'day_of_week' => $day,
                'start_time' => $start,
                'end_time' => $end,
                'schedule' => $this->formatSchedule($day, $start, $end),
            ]
        );

        return back()->with('success', 'Subject teacher assignment updated successfully.');
    }

    public function updateSubjectTeachers(Request $request, Section $section): RedirectResponse
    {
        $validated = $request->validate([
            'teacher_ids' => ['array'],
            'teacher_ids.*' => ['nullable', 'exists:users,id'],
            'rooms' => ['array'],
            'rooms.*' => ['nullable', 'string', 'max:255'],
            'days' => ['array'],
            'days.*' => ['nullable', 'string', 'max:20'],
            'start_times' => ['array'],
            'start_times.*' => ['nullable', 'date_format:H:i'],
            'end_times' => ['array'],
            'end_times.*' => ['nullable', 'date_format:H:i'],
        ]);

        $teacherIdsBySubject = $validated['teacher_ids'] ?? [];
        $roomsBySubject = $validated['rooms'] ?? [];
        $daysBySubject = $validated['days'] ?? [];
        $startTimesBySubject = $validated['start_times'] ?? [];
        $endTimesBySubject = $validated['end_times'] ?? [];

        if (! empty($teacherIdsBySubject)) {
            $teacherIds = array_values(array_filter($teacherIdsBySubject));

            if (! empty($teacherIds)) {
                $invalidTeachers = \App\Models\User::whereIn('id', $teacherIds)
                    ->where('account_type', '!=', 'faculty')
                    ->pluck('id')
                    ->all();

                if (! empty($invalidTeachers)) {
                    return back()->withErrors(['teacher_ids' => 'Selected teachers must be faculty accounts.']);
                }
            }
        }

        $sectionGrade = $this->normalizeGradeLevel($section->grade_level);
        $subjects = Subject::query()
            ->with('gradeLevels')
            ->where('is_active', true)
            ->get()
            ->filter(function (Subject $subject) use ($sectionGrade) {
                return $this->subjectMatchesGrade($subject, $sectionGrade);
            });

        $allowedSubjectIds = $subjects->pluck('id')->all();
        $invalidSubjectIds = array_diff(array_keys($teacherIdsBySubject), $allowedSubjectIds);

        if (! empty($invalidSubjectIds)) {
            return back()->withErrors(['teacher_ids' => 'Some subjects are not valid for this section.']);
        }

        foreach ($teacherIdsBySubject as $subjectId => $teacherId) {
            $room = $roomsBySubject[$subjectId] ?? null;
            $day = $daysBySubject[$subjectId] ?? null;
            $start = $startTimesBySubject[$subjectId] ?? null;
            $end = $endTimesBySubject[$subjectId] ?? null;
            $resolvedTeacherId = $teacherId ? (int) $teacherId : null;
            $subjectIdInt = (int) $subjectId;

            if (($day || $start || $end) && (! $day || ! $start || ! $end)) {
                return back()->withErrors(['schedule' => 'Day, start time, and end time are required together.']);
            }

            if (($day || $start || $end) && ! $room) {
                return back()->withErrors(['room' => 'Room is required when a schedule is set.']);
            }

            if ($start && $end && $start >= $end) {
                return back()->withErrors(['schedule' => 'End time must be later than start time.']);
            }

            if ($room && $day && $start && $end) {
                $conflictMessage = $this->detectScheduleConflict(
                    $room,
                    $day,
                    $start,
                    $end,
                    $section->id,
                    $subjectIdInt,
                    $resolvedTeacherId ? (int) $resolvedTeacherId : null
                );
                if ($conflictMessage) {
                    return back()->withErrors(['schedule' => $conflictMessage]);
                }
            }

            SectionSubjectTeacher::updateOrCreate(
                ['section_id' => $section->id, 'subject_id' => $subjectIdInt],
                [
                    'teacher_id' => $resolvedTeacherId,
                    'room' => $room,
                    'day_of_week' => $day,
                    'start_time' => $start,
                    'end_time' => $end,
                    'schedule' => $this->formatSchedule($day, $start, $end),
                ]
            );
        }

        return back()->with('success', 'Subject teacher assignments updated successfully.');
    }

    public function storeTeachingLoad(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'teacher_id' => ['required', 'exists:users,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'subject_id' => [
                'required',
                'exists:subjects,id',
                Rule::unique('section_subject_teachers')->where(function ($query) use ($request) {
                    return $query->where('section_id', $request->integer('section_id'))
                        ->where('subject_id', $request->integer('subject_id'));
                }),
            ],
        ], [
            'subject_id.unique' => 'This section and subject already has an assigned faculty.',
        ]);

        $isFaculty = \App\Models\User::where('id', $validated['teacher_id'])
            ->where('account_type', 'faculty')
            ->exists();

        if (! $isFaculty) {
            return back()->withErrors(['teacher_id' => 'Selected faculty must be a faculty account.']);
        }

        SectionSubjectTeacher::create([
            'teacher_id' => (int) $validated['teacher_id'],
            'section_id' => (int) $validated['section_id'],
            'subject_id' => (int) $validated['subject_id'],
        ]);

        return back()->with('success', 'Teaching load assigned successfully.');
    }

    public function updateTeachingLoad(Request $request, SectionSubjectTeacher $teachingAssignment): RedirectResponse
    {
        $validated = $request->validate([
            'teacher_id' => ['required', 'exists:users,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'subject_id' => [
                'required',
                'exists:subjects,id',
                Rule::unique('section_subject_teachers')->where(function ($query) use ($request) {
                    return $query->where('section_id', $request->integer('section_id'))
                        ->where('subject_id', $request->integer('subject_id'));
                })->ignore($teachingAssignment->id),
            ],
        ], [
            'subject_id.unique' => 'This section and subject already has an assigned faculty.',
        ]);

        $isFaculty = \App\Models\User::where('id', $validated['teacher_id'])
            ->where('account_type', 'faculty')
            ->exists();

        if (! $isFaculty) {
            return back()->withErrors(['teacher_id' => 'Selected faculty must be a faculty account.']);
        }

        $teachingAssignment->update([
            'teacher_id' => (int) $validated['teacher_id'],
            'section_id' => (int) $validated['section_id'],
            'subject_id' => (int) $validated['subject_id'],
        ]);

        return back()->with('success', 'Teaching load updated successfully.');
    }

    public function destroyTeachingLoad(SectionSubjectTeacher $teachingAssignment): RedirectResponse
    {
        $teachingAssignment->delete();

        return back()->with('success', 'Teaching load removed successfully.');
    }

    private function normalizeGradeLevel(?string $gradeLevel): ?string
    {
        if (! $gradeLevel) {
            return null;
        }

        if (preg_match('/(7|8|9|10|11|12)/', $gradeLevel, $matches)) {
            return $matches[1];
        }

        return $gradeLevel;
    }

    private function subjectMatchesGrade(Subject $subject, ?string $grade): bool
    {
        if (! $grade) {
            return true;
        }

        $gradeLevels = $subject->gradeLevels()->pluck('grade_level')->all();

        if (! empty($gradeLevels)) {
            return in_array($grade, $gradeLevels, true);
        }

        $fallback = $this->normalizeGradeLevel($subject->grade_level);

        return $fallback === 'all' || $fallback === $grade;
    }

    private function detectScheduleConflict(
        string $room,
        string $day,
        string $start,
        string $end,
        int $sectionId,
        int $subjectId,
        ?int $teacherId = null
    ): ?string {
        $roomConflict = SectionSubjectTeacher::query()
            ->where('room', $room)
            ->where('day_of_week', $day)
            ->where(function ($query) use ($start, $end) {
                $query->where('start_time', '<', $end)
                    ->where('end_time', '>', $start);
            })
            ->where(function ($query) use ($sectionId, $subjectId) {
                $query->where('section_id', '!=', $sectionId)
                    ->orWhere('subject_id', '!=', $subjectId);
            })
            ->exists();

        if ($roomConflict) {
            return 'Room is already booked for that time.';
        }

        if ($teacherId) {
            $teacherConflict = SectionSubjectTeacher::query()
                ->where('teacher_id', $teacherId)
                ->where('day_of_week', $day)
                ->where(function ($query) use ($start, $end) {
                    $query->where('start_time', '<', $end)
                        ->where('end_time', '>', $start);
                })
                ->where(function ($query) use ($sectionId, $subjectId) {
                    $query->where('section_id', '!=', $sectionId)
                        ->orWhere('subject_id', '!=', $subjectId);
                })
                ->exists();

            if ($teacherConflict) {
                return 'Faculty already has a class scheduled during that time.';
            }
        }

        return null;
    }

    private function formatSchedule(?string $day, ?string $start, ?string $end): ?string
    {
        if (! $day || ! $start || ! $end) {
            return null;
        }

        return sprintf('%s %s-%s', $day, $start, $end);
    }
}
