<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Section;
use App\Models\Subject;
use App\Models\SectionSubjectTeacher;
use App\Models\GradeEntry;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(): View
    {
        // Get summary statistics for the reports page
        $stats = [
            'total_students' => User::where('account_type', 'student')->count(),
            'total_faculty' => User::where('account_type', 'faculty')->count(),
            'total_sections' => Section::count(),
            'total_subjects' => Subject::count(),
            'pending_admissions' => DB::table('admissions')->where('status', 'pending')->count(),
            'approved_grades' => GradeEntry::where('status', 'approved')->count(),
        ];

        return view('admin.reports.index', compact('stats'));
    }

    public function facultyList(Request $request): View
    {
        $query = User::where('account_type', 'faculty')
            ->orderBy('last_name')
            ->orderBy('first_name');

        // Add search filter if provided
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $faculties = $query->get();

        return view('admin.reports.faculty-list', compact('faculties'));
    }

    public function studentList(Request $request): View
    {
        $query = User::with('section')
            ->where('account_type', 'student')
            ->orderBy('last_name')
            ->orderBy('first_name');

        // Add filters
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('lrn', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($sectionId = $request->input('section_id')) {
            $query->where('section_id', $sectionId);
        }

        if ($gradeLevel = $request->input('grade_level')) {
            $query->whereHas('section', function($q) use ($gradeLevel) {
                $q->where('grade_level', $gradeLevel);
            });
        }

        $students = $query->get();
        $sections = Section::orderBy('name')->get();
        $gradeLevels = Section::select('grade_level')->distinct()->orderBy('grade_level')->pluck('grade_level');

        return view('admin.reports.student-list', compact('students', 'sections', 'gradeLevels'));
    }

    public function subjectAssignments(): View
    {
        $assignments = SectionSubjectTeacher::with(['section', 'subject', 'teacher'])
            ->orderBy('section_id')
            ->orderBy('subject_id')
            ->get()
            ->groupBy('section_id');

        return view('admin.reports.subject-assignments', compact('assignments'));
    }

    public function gradesSummary(Request $request): View
    {
        $query = GradeEntry::with(['student', 'subject', 'section'])
            ->where('status', 'approved');

        // Add filters
        if ($term = $request->input('term')) {
            $query->where('term', $term);
        }

        if ($sectionId = $request->input('section_id')) {
            $query->where('section_id', $sectionId);
        }

        $grades = $query->orderBy('student_id')->orderBy('subject_id')->get();
        $sections = Section::orderBy('name')->get();
        $terms = GradeEntry::select('term')->distinct()->orderBy('term')->pluck('term');

        return view('admin.reports.grades-summary', compact('grades', 'sections', 'terms'));
    }
}
