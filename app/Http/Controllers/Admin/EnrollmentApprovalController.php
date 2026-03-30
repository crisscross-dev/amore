<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\StudentEnrollment;
use App\Models\SchoolYear;
use App\Models\Section;
use Illuminate\Database\Eloquent\Builder;
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
        $selectedSchoolYear = $this->resolveSelectedSchoolYear($request);
        $query = $this->buildEnrollmentsListQuery($request, $selectedSchoolYear, true);

        $enrollments = $query->orderBy('created_at', 'desc')->paginate(20);
        $admissionsReadyQuery = $this->buildAdmissionsReadyQuery($request);

        $admissionsReady = $admissionsReadyQuery
            ->orderByDesc('approved_at')
            ->paginate(10, ['*'], 'admissions_page')
            ->withQueryString();

        $sections = Section::query()
            ->with(['subjectTeachers.subject'])
            ->when($selectedSchoolYear, function ($query) use ($selectedSchoolYear) {
                $query->where('academic_year', $selectedSchoolYear->year_name);
            })
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        // Get statistics for selected school year plus admissions queue waiting for assignment.
        $enrollmentStatsQuery = StudentEnrollment::query()
            ->when($selectedSchoolYear, function ($query) use ($selectedSchoolYear) {
                $query->where('school_year_id', $selectedSchoolYear->id);
            });

        $pendingAdmissionsReadyCount = (clone $admissionsReadyQuery)->count();
        $pendingEnrollmentsCount = (clone $enrollmentStatsQuery)->where('status', 'pending')->count();
        $approvedCount = (clone $enrollmentStatsQuery)->where('status', 'approved')->count();
        $rejectedCount = (clone $enrollmentStatsQuery)->where('status', 'rejected')->count();

        $stats = [
            'pending' => $pendingAdmissionsReadyCount + $pendingEnrollmentsCount,
            'approved' => $approvedCount,
            'rejected' => $rejectedCount,
            'total' => $pendingAdmissionsReadyCount + $pendingEnrollmentsCount + $approvedCount + $rejectedCount,
        ];

        $schoolYears = SchoolYear::orderBy('start_date', 'desc')->get();
        $gradeLevels = ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10'];
        $enrollmentsLiveSignature = $this->buildEnrollmentsLiveSignature($request, $selectedSchoolYear);

        return view('admin.enrollments.index', compact(
            'enrollments',
            'admissionsReady',
            'stats',
            'schoolYears',
            'gradeLevels',
            'sections',
            'selectedSchoolYear',
            'enrollmentsLiveSignature'
        ));
    }

    /**
     * Lightweight polling endpoint for enrollment queue updates.
     */
    public function liveSignature(Request $request)
    {
        $selectedSchoolYear = $this->resolveSelectedSchoolYear($request);

        return response()->json([
            'signature' => $this->buildEnrollmentsLiveSignature($request, $selectedSchoolYear),
            'generated_at' => now()->toIso8601String(),
        ]);
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
            ->route('admin.enrollments.index')
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

    private function resolveSelectedSchoolYear(Request $request): ?SchoolYear
    {
        $selectedSchoolYear = null;

        if ($request->filled('school_year_id')) {
            $selectedSchoolYear = SchoolYear::query()->find($request->school_year_id);
        }

        if (! $selectedSchoolYear) {
            $selectedSchoolYear = SchoolYear::active()->first()
                ?: SchoolYear::query()->orderByDesc('start_date')->first();
        }

        return $selectedSchoolYear;
    }

    private function buildEnrollmentsListQuery(Request $request, ?SchoolYear $selectedSchoolYear, bool $withRelations): Builder
    {
        $query = StudentEnrollment::query();

        if ($withRelations) {
            $query->with(['student', 'schoolYear', 'section']);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('grade_level')) {
            $query->where('enrolling_grade_level', $request->grade_level);
        }

        if ($request->filled('school_year_id')) {
            $query->where('school_year_id', $request->school_year_id);
        } elseif ($selectedSchoolYear) {
            $query->where('school_year_id', $selectedSchoolYear->id);
        }

        if ($request->filled('search')) {
            $search = (string) $request->search;
            $query->whereHas('student', function (Builder $builder) use ($search) {
                $builder->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    private function buildAdmissionsReadyQuery(Request $request): Builder
    {
        $query = Admission::query()
            ->where('status', 'approved')
            ->whereNotNull('user_id')
            ->whereHas('user', function (Builder $builder) {
                $builder->whereNull('section_id');
            });

        if ($request->filled('grade_level')) {
            $query->where('grade_level', $request->grade_level);
        }

        if ($request->filled('search')) {
            $search = (string) $request->search;
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('lrn', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    private function buildEnrollmentsLiveSignature(Request $request, ?SchoolYear $selectedSchoolYear): string
    {
        $enrollmentsQuery = $this->buildEnrollmentsListQuery($request, $selectedSchoolYear, false);
        $admissionsReadyQuery = $this->buildAdmissionsReadyQuery($request);

        $enrollmentCount = (clone $enrollmentsQuery)->count();
        $enrollmentStamp = $this->timestampOrZero((clone $enrollmentsQuery)->max('updated_at'));

        $admissionsReadyCount = (clone $admissionsReadyQuery)->count();
        $admissionsReadyStamp = $this->timestampOrZero((clone $admissionsReadyQuery)->max('updated_at'));

        $statsBase = StudentEnrollment::query()
            ->when($selectedSchoolYear, function (Builder $query) use ($selectedSchoolYear) {
                $query->where('school_year_id', $selectedSchoolYear->id);
            });

        $pendingEnrollmentsCount = (clone $statsBase)->where('status', 'pending')->count();
        $approvedCount = (clone $statsBase)->where('status', 'approved')->count();
        $rejectedCount = (clone $statsBase)->where('status', 'rejected')->count();
        $statsStamp = $this->timestampOrZero((clone $statsBase)->max('updated_at'));

        return implode('|', [
            $selectedSchoolYear?->id ?? 0,
            $enrollmentCount,
            $enrollmentStamp,
            $admissionsReadyCount,
            $admissionsReadyStamp,
            $pendingEnrollmentsCount,
            $approvedCount,
            $rejectedCount,
            $statsStamp,
        ]);
    }

    private function timestampOrZero($value): int
    {
        if (empty($value)) {
            return 0;
        }

        $timestamp = strtotime((string) $value);

        return $timestamp !== false ? $timestamp : 0;
    }
}
