<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\StudentEnrollment;
use App\Models\SchoolYear;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnrollmentApprovalController extends Controller
{
    /**
     * Display a listing of enrollments
     */
    public function index(Request $request)
    {
        $query = StudentEnrollment::with(['student', 'schoolYear', 'section']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by grade level
        if ($request->filled('grade_level')) {
            $query->where('enrolling_grade_level', $request->grade_level);
        }

        // Filter by school year
        if ($request->filled('school_year_id')) {
            $query->where('school_year_id', $request->school_year_id);
        } else {
            // Default to active school year
            $activeSchoolYear = SchoolYear::active()->first();
            if ($activeSchoolYear) {
                $query->where('school_year_id', $activeSchoolYear->id);
            }
        }

        // Search by student name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $enrollments = $query->orderBy('created_at', 'desc')->paginate(20);

        $admissionsReadyQuery = Admission::query()
            ->where('status', 'approved')
            ->whereNotNull('user_id')
            ->whereHas('user', function ($query) {
                $query->whereNull('section_id');
            });

        if ($request->filled('grade_level')) {
            $admissionsReadyQuery->where('grade_level', $request->grade_level);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $admissionsReadyQuery->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('lrn', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $admissionsReady = $admissionsReadyQuery
            ->orderByDesc('approved_at')
            ->paginate(10, ['*'], 'admissions_page')
            ->withQueryString();

        // Get statistics
        $activeSchoolYear = SchoolYear::active()->first();
        $stats = [
            'pending' => StudentEnrollment::where('school_year_id', $activeSchoolYear->id ?? null)->pending()->count(),
            'approved' => StudentEnrollment::where('school_year_id', $activeSchoolYear->id ?? null)->approved()->count(),
            'rejected' => StudentEnrollment::where('school_year_id', $activeSchoolYear->id ?? null)->where('status', 'rejected')->count(),
            'total' => StudentEnrollment::where('school_year_id', $activeSchoolYear->id ?? null)->count(),
        ];

        $schoolYears = SchoolYear::orderBy('start_date', 'desc')->get();
        $gradeLevels = ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10'];

        return view('admin.enrollments.index', compact(
            'enrollments',
            'admissionsReady',
            'stats',
            'schoolYears',
            'gradeLevels'
        ));
    }

    /**
     * Review an approved admission for section assignment.
     */
    public function reviewApprovedAdmission(Admission $admission)
    {
        if ($admission->status !== 'approved' || ! $admission->user_id) {
            return redirect()->route('admin.enrollments.index')
                ->with('error', 'This admission is not available for enrollment processing.');
        }

        $activeSchoolYear = SchoolYear::active()->first();
        $fallbackSchoolYear = $activeSchoolYear ?: SchoolYear::query()->orderByDesc('start_date')->first();
        $normalizedAdmissionGrade = $this->normalizeGradeLevel($admission->grade_level);

        $sections = Section::query()
            ->with(['subjectTeachers.subject'])
            ->when($normalizedAdmissionGrade, function ($query) use ($normalizedAdmissionGrade) {
                $query->where('grade_level', 'like', "%{$normalizedAdmissionGrade}%");
            })
            ->when($fallbackSchoolYear, function ($query) use ($fallbackSchoolYear) {
                $query->where('academic_year', $fallbackSchoolYear->year_name);
            })
            ->orderBy('name')
            ->get();

        return view('admin.enrollments.review-admission', [
            'admission' => $admission,
            'sections' => $sections,
            'activeSchoolYear' => $activeSchoolYear,
            'selectedSchoolYear' => $fallbackSchoolYear,
        ]);
    }

    /**
     * Finalize approved admission: assign section to existing student account.
     */
    public function enrollApprovedAdmission(Request $request, Admission $admission)
    {
        if ($admission->status !== 'approved' || ! $admission->user_id) {
            return redirect()->route('admin.enrollments.index')
                ->with('error', 'This admission is not available for enrollment processing.');
        }

        $validated = $request->validate([
            'section_id' => 'required|exists:sections,id',
            'admin_remarks' => 'nullable|string|max:500',
        ]);

        $selectedSection = Section::findOrFail($validated['section_id']);
        $resolvedSchoolYear = $this->resolveSchoolYearForSection($selectedSection);

        if (! $resolvedSchoolYear) {
            return back()->with('error', 'No school year found. Please create a school year first.');
        }

        $sectionGrade = $this->normalizeGradeLevel($selectedSection->grade_level);
        $admissionGrade = $this->normalizeGradeLevel($admission->grade_level);

        if ($sectionGrade && $admissionGrade && $sectionGrade !== $admissionGrade) {
            return back()->with('error', 'Selected section grade level does not match the approved admission grade level.');
        }

        $studentUser = $admission->user;
        if (! $studentUser) {
            return back()->with('error', 'Linked student account was not found for this admission.');
        }

        DB::transaction(function () use ($admission, $validated, $selectedSection, $resolvedSchoolYear, $studentUser) {
            $studentUser->update([
                'grade_level' => $admission->grade_level,
                'current_grade_level' => $admission->grade_level,
                'section_id' => $validated['section_id'],
            ]);

            StudentEnrollment::updateOrCreate(
                [
                    'student_id' => $studentUser->id,
                    'school_year_id' => $resolvedSchoolYear->id,
                ],
                [
                    'current_grade_level' => $admission->grade_level,
                    'enrolling_grade_level' => $admission->grade_level,
                    'section_id' => $selectedSection->id,
                    'status' => 'approved',
                    'admin_remarks' => $validated['admin_remarks'] ?? null,
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                    'enrollment_date' => now(),
                ]
            );

            $admission->update([
                'approval_notes' => $validated['admin_remarks'] ?? $admission->approval_notes,
            ]);
        });

        return redirect()
            ->route('admin.enrollments.review-admission', $admission)
            ->with('success', 'Student enrollment is complete and section has been assigned.');
    }

    /**
     * Display the specified enrollment
     */
    public function show(StudentEnrollment $enrollment)
    {
        $enrollment->load(['student', 'schoolYear', 'section', 'documents', 'approvedBy']);

        // Get available sections for the enrolling grade level
        $sections = Section::where('grade_level', $enrollment->enrolling_grade_level)
            ->where('academic_year', $enrollment->schoolYear->year_name)
            ->get();

        return view('admin.enrollments.show', compact('enrollment', 'sections'));
    }

    /**
     * Approve an enrollment
     */
    public function approve(Request $request, StudentEnrollment $enrollment)
    {
        // Ensure enrollment is pending
        if ($enrollment->status !== 'pending') {
            return redirect()->route('admin.enrollments.show', $enrollment)
                ->with('error', 'Only pending enrollments can be approved.');
        }

        $validated = $request->validate([
            'section_id' => 'nullable|exists:sections,id',
            'admin_remarks' => 'nullable|string|max:500',
        ]);

        $enrollment->approve(
            $validated['section_id'] ?? null,
            Auth::id(),
            $validated['admin_remarks'] ?? null
        );

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Enrollment approved successfully!');
    }

    /**
     * Reject an enrollment
     */
    public function reject(Request $request, StudentEnrollment $enrollment)
    {
        // Ensure enrollment is pending
        if ($enrollment->status !== 'pending') {
            return redirect()->route('admin.enrollments.show', $enrollment)
                ->with('error', 'Only pending enrollments can be rejected.');
        }

        $validated = $request->validate([
            'admin_remarks' => 'required|string|max:500',
        ]);

        $enrollment->reject(
            Auth::id(),
            $validated['admin_remarks']
        );

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Enrollment rejected.');
    }

    private function normalizeGradeLevel(?string $gradeLevel): ?string
    {
        if (! $gradeLevel) {
            return null;
        }

        if (preg_match('/(7|8|9|10|11|12)/', $gradeLevel, $matches)) {
            return $matches[1];
        }

        return trim((string) $gradeLevel);
    }

    private function resolveSchoolYearForSection(Section $section): ?SchoolYear
    {
        if (! empty($section->academic_year)) {
            $matched = SchoolYear::query()
                ->where('year_name', $section->academic_year)
                ->first();

            if ($matched) {
                return $matched;
            }
        }

        return SchoolYear::active()->first()
            ?: SchoolYear::query()->orderByDesc('start_date')->first();
    }
}
