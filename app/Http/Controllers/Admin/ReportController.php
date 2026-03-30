<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Event;
use App\Models\FacultyPosition;
use App\Models\SectionSubjectTeacher;
use App\Models\GradeEntry;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $activeTab = $request->get('tab', 'faculty');
        if (!in_array($activeTab, ['faculty', 'students', 'sections', 'events'], true)) {
            $activeTab = 'faculty';
        }

        // Get summary statistics for the reports page
        $stats = [
            'total_students' => User::where('account_type', 'student')->count(),
            'total_faculty' => User::where('account_type', 'faculty')->count(),
            'total_sections' => Section::count(),
            'total_subjects' => Subject::count(),
            'pending_admissions' => DB::table('admissions')->where('status', 'pending')->count(),
            'approved_grades' => GradeEntry::where('status', 'approved')->count(),
        ];

        $facultyQuery = User::with('facultyPosition')
            ->where('account_type', 'faculty');

        if ($facultyName = trim((string) $request->get('faculty_name', ''))) {
            $facultyQuery->where(function ($q) use ($facultyName) {
                $q->where('first_name', 'like', "%{$facultyName}%")
                    ->orWhere('middle_name', 'like', "%{$facultyName}%")
                    ->orWhere('last_name', 'like', "%{$facultyName}%");
            });
        }

        if ($facultyDepartment = $request->get('faculty_department')) {
            $facultyQuery->where('department', $facultyDepartment);
        }

        if ($facultyPositionId = $request->get('faculty_position_id')) {
            $facultyQuery->where('faculty_position_id', $facultyPositionId);
        }

        $facultyView = $request->get('faculty_view', 'latest');
        if (!in_array($facultyView, ['latest', 'by_departments'], true)) {
            $facultyView = 'latest';
        }

        if ($facultyView === 'by_departments') {
            // Grouping-friendly sort: department first, then faculty names.
            $facultyQuery
                ->orderByRaw("CASE WHEN department IS NULL OR department = '' THEN 1 ELSE 0 END")
                ->orderBy('department')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->orderByDesc('created_at');
        } else {
            // Show newest faculty first so recently added records appear at the top.
            $facultyQuery
                ->orderByDesc('created_at')
                ->orderByDesc('id');
        }

        $faculties = $facultyQuery
            ->paginate(10, ['*'], 'faculty_page')
            ->withQueryString();

        $facultyDepartments = User::where('account_type', 'faculty')
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->select('department')
            ->distinct()
            ->orderBy('department')
            ->pluck('department');

        $facultyPositions = FacultyPosition::where('is_active', true)
            ->orderBy('hierarchy_level')
            ->orderBy('name')
            ->get(['id', 'name', 'hierarchy_level']);

        $studentsQuery = User::with('section')
            ->where('account_type', 'student');

        if ($studentQuery = trim((string) $request->get('student_query', ''))) {
            $studentsQuery->where(function ($q) use ($studentQuery) {
                $q->where('first_name', 'like', "%{$studentQuery}%")
                    ->orWhere('middle_name', 'like', "%{$studentQuery}%")
                    ->orWhere('last_name', 'like', "%{$studentQuery}%")
                    ->orWhere('lrn', 'like', "%{$studentQuery}%");
            });
        }

        if ($studentSectionId = $request->get('student_section_id')) {
            $studentsQuery->where('section_id', $studentSectionId);
        }

        if ($studentGradeLevel = $request->get('student_grade_level')) {
            $studentsQuery->whereHas('section', function ($q) use ($studentGradeLevel) {
                $q->where('grade_level', $studentGradeLevel);
            });
        }

        $students = $studentsQuery
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'students_page')
            ->withQueryString();

        $studentSections = Section::select('id', 'name')
            ->orderBy('name')
            ->get();

        $studentGradeLevels = Section::whereNotNull('grade_level')
            ->where('grade_level', '!=', '')
            ->select('grade_level')
            ->distinct()
            ->orderBy('grade_level')
            ->pluck('grade_level');

        $sectionGradeLevels = collect(range(7, 12))
            ->map(fn (int $level) => "Grade {$level}");

        $sectionsQuery = Section::with('adviser')
            ->withCount('students');

        if ($sectionGradeLevel = $request->get('section_grade_level')) {
            if (preg_match('/(\d+)/', (string) $sectionGradeLevel, $matches)) {
                $gradeNumber = $matches[1];
                $sectionsQuery->where(function ($q) use ($sectionGradeLevel, $gradeNumber) {
                    $q->where('grade_level', $sectionGradeLevel)
                        ->orWhere('grade_level', $gradeNumber);
                });
            } else {
                $sectionsQuery->where('grade_level', $sectionGradeLevel);
            }
        }

        $sections = $sectionsQuery
            ->orderByRaw("CASE
                WHEN grade_level IN ('Grade 7', '7') THEN 7
                WHEN grade_level IN ('Grade 8', '8') THEN 8
                WHEN grade_level IN ('Grade 9', '9') THEN 9
                WHEN grade_level IN ('Grade 10', '10') THEN 10
                WHEN grade_level IN ('Grade 11', '11') THEN 11
                WHEN grade_level IN ('Grade 12', '12') THEN 12
                ELSE 99
            END")
            ->orderBy('name')
            ->paginate(10, ['*'], 'sections_page')
            ->withQueryString();

        $events = Event::with('creator')
            ->orderBy('start_date', 'desc')
            ->paginate(10, ['*'], 'events_page')
            ->withQueryString();

        return view('admin.reports.index', compact(
            'stats',
            'faculties',
            'students',
            'sections',
            'events',
            'activeTab',
            'facultyDepartments',
            'facultyPositions',
            'facultyView',
            'studentSections',
            'studentGradeLevels',
            'sectionGradeLevels'
        ));
    }

    public function printTab(Request $request): View
    {
        $activeTab = $request->get('tab', 'faculty');
        if (!in_array($activeTab, ['faculty', 'students', 'sections', 'events'], true)) {
            $activeTab = 'faculty';
        }

        $facultyView = $request->get('faculty_view', 'latest');
        if (!in_array($facultyView, ['latest', 'by_departments'], true)) {
            $facultyView = 'latest';
        }

        $faculties = collect();
        $students = collect();
        $sections = collect();
        $events = collect();

        if ($activeTab === 'faculty') {
            $facultyQuery = User::with('facultyPosition')
                ->where('account_type', 'faculty');

            if ($facultyName = trim((string) $request->get('faculty_name', ''))) {
                $facultyQuery->where(function ($q) use ($facultyName) {
                    $q->where('first_name', 'like', "%{$facultyName}%")
                        ->orWhere('middle_name', 'like', "%{$facultyName}%")
                        ->orWhere('last_name', 'like', "%{$facultyName}%");
                });
            }

            if ($facultyDepartment = $request->get('faculty_department')) {
                $facultyQuery->where('department', $facultyDepartment);
            }

            if ($facultyPositionId = $request->get('faculty_position_id')) {
                $facultyQuery->where('faculty_position_id', $facultyPositionId);
            }

            // Print layout requires grouping by department, then alphabetical faculty names.
            $facultyQuery
                ->orderByRaw("CASE WHEN department IS NULL OR department = '' THEN 1 ELSE 0 END")
                ->orderBy('department')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->orderBy('middle_name')
                ->orderBy('id');

            $faculties = $facultyQuery->get();
        }

        if ($activeTab === 'students') {
            $studentsQuery = User::with('section')
                ->where('account_type', 'student');

            if ($studentQuery = trim((string) $request->get('student_query', ''))) {
                $studentsQuery->where(function ($q) use ($studentQuery) {
                    $q->where('first_name', 'like', "%{$studentQuery}%")
                        ->orWhere('middle_name', 'like', "%{$studentQuery}%")
                        ->orWhere('last_name', 'like', "%{$studentQuery}%")
                        ->orWhere('lrn', 'like', "%{$studentQuery}%");
                });
            }

            if ($studentSectionId = $request->get('student_section_id')) {
                $studentsQuery->where('section_id', $studentSectionId);
            }

            if ($studentGradeLevel = $request->get('student_grade_level')) {
                $studentsQuery->whereHas('section', function ($q) use ($studentGradeLevel) {
                    $q->where('grade_level', $studentGradeLevel);
                });
            }

            $students = $studentsQuery
                ->leftJoin('sections as student_sections', 'student_sections.id', '=', 'users.section_id')
                ->select('users.*')
                ->orderByRaw("CASE
                    WHEN student_sections.grade_level IN ('Grade 7', '7') THEN 7
                    WHEN student_sections.grade_level IN ('Grade 8', '8') THEN 8
                    WHEN student_sections.grade_level IN ('Grade 9', '9') THEN 9
                    WHEN student_sections.grade_level IN ('Grade 10', '10') THEN 10
                    WHEN student_sections.grade_level IN ('Grade 11', '11') THEN 11
                    WHEN student_sections.grade_level IN ('Grade 12', '12') THEN 12
                    ELSE 99
                END")
                ->orderByRaw("CASE WHEN student_sections.name IS NULL OR student_sections.name = '' THEN 1 ELSE 0 END")
                ->orderBy('student_sections.name')
                ->orderBy('users.last_name')
                ->orderBy('users.first_name')
                ->orderBy('users.middle_name')
                ->orderBy('users.id')
                ->get();
        }

        if ($activeTab === 'sections') {
            $sections = Section::with('adviser')
                ->withCount('students')
                ->orderByRaw("CASE
                    WHEN grade_level IN ('Grade 7', '7') THEN 7
                    WHEN grade_level IN ('Grade 8', '8') THEN 8
                    WHEN grade_level IN ('Grade 9', '9') THEN 9
                    WHEN grade_level IN ('Grade 10', '10') THEN 10
                    WHEN grade_level IN ('Grade 11', '11') THEN 11
                    WHEN grade_level IN ('Grade 12', '12') THEN 12
                    ELSE 99
                END")
                ->orderBy('name')
                ->get();
        }

        if ($activeTab === 'events') {
            $events = Event::orderBy('start_date', 'desc')->get();
        }

        return view('admin.reports.print', compact(
            'activeTab',
            'facultyView',
            'faculties',
            'students',
            'sections',
            'events'
        ));
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

        if ($department = $request->input('department')) {
            $query->where('department', $department);
        }

        $faculties = $query->paginate(10)->withQueryString();
        $departments = User::where('account_type', 'faculty')
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->select('department')
            ->distinct()
            ->orderBy('department')
            ->pluck('department');

        return view('admin.reports.faculty-list', compact('faculties', 'departments'));
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
