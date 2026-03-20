<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SectionSubjectTeacher;
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
                'subjects' => collect([]),
                'section' => null,
                'message' => 'You are not assigned to any section yet. Please contact your administrator.'
            ]);
        }

        // Get all subject assignments for this section with teacher information
        $subjectAssignments = SectionSubjectTeacher::with(['subject', 'teacher', 'section'])
            ->where('section_id', $sectionId)
            ->orderBy('subject_id')
            ->get();

        // Group by subject to handle multiple teachers for the same subject
        $subjects = $subjectAssignments->groupBy('subject_id')->map(function ($assignments) {
            $firstAssignment = $assignments->first();
            return [
                'subject' => $firstAssignment->subject,
                'teachers' => $assignments->map(fn($a) => $a->teacher)->filter(),
                'section' => $firstAssignment->section,
            ];
        })->values();

        return view('student.subjects.index', [
            'subjects' => $subjects,
            'section' => $user->section,
            'message' => null
        ]);
    }
}
