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
                'subjectAssignments' => collect([]),
                'section' => null,
                'message' => 'You are not assigned to any section yet. Please contact your administrator.'
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

        return view('student.subjects.index', [
            'subjectAssignments' => $subjectAssignments,
            'section' => $user->section,
            'message' => null
        ]);
    }
}
