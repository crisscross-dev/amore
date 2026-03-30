<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentEnrollment;
use App\Models\SchoolYear;
use App\Models\StudentEnrollmentDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EnrollmentController extends Controller
{
    /**
     * Display enrollment history and current status
     */
    public function index(Request $request)
    {
        $student = Auth::user();
        $enrollments = StudentEnrollment::where('student_id', $student->id)
            ->with(['schoolYear', 'section'])
            ->orderBy('created_at', 'desc')
            ->get();

        $activeSchoolYear = SchoolYear::active()->first();
        $canEnroll = $activeSchoolYear && $activeSchoolYear->canEnrollNow();
        
        // Check if already enrolled for active school year
        $hasEnrolled = false;
        if ($activeSchoolYear) {
            $hasEnrolled = StudentEnrollment::where('student_id', $student->id)
                ->where('school_year_id', $activeSchoolYear->id)
                ->exists();
        }

        $enrollmentLiveSignature = $this->buildEnrollmentLiveSignature($student);

        return view('student.enrollment.index', compact(
            'enrollments',
            'activeSchoolYear',
            'canEnroll',
            'hasEnrolled',
            'enrollmentLiveSignature'
        ));
    }

    /**
     * Show enrollment form
     */
    public function create()
    {
        $student = Auth::user();
        $activeSchoolYear = SchoolYear::active()->first();

        // Check if enrollment is open
        if (!$activeSchoolYear || !$activeSchoolYear->canEnrollNow()) {
            return redirect()->route('student.enrollment.index')
                ->with('error', 'Enrollment is currently closed.');
        }

        // Check if already enrolled
        $existingEnrollment = StudentEnrollment::where('student_id', $student->id)
            ->where('school_year_id', $activeSchoolYear->id)
            ->first();

        if ($existingEnrollment) {
            return redirect()->route('student.enrollment.index')
                ->with('error', 'You have already enrolled for this school year.');
        }

        // Suggest next grade level
        $currentGradeLevel = $student->current_grade_level ?? $student->grade_level;
        $suggestedGradeLevel = $this->getNextGradeLevel($currentGradeLevel);

        return view('student.enrollment.create', compact(
            'student',
            'activeSchoolYear',
            'currentGradeLevel',
            'suggestedGradeLevel'
        ));
    }

    /**
     * Store enrollment request
     */
    public function store(Request $request)
    {
        $student = Auth::user();
        $activeSchoolYear = SchoolYear::active()->first();

        // Validate enrollment eligibility
        if (!$activeSchoolYear || !$activeSchoolYear->canEnrollNow()) {
            return redirect()->route('student.enrollment.index')
                ->with('error', 'Enrollment is currently closed.');
        }

        // Check for duplicate enrollment
        $existingEnrollment = StudentEnrollment::where('student_id', $student->id)
            ->where('school_year_id', $activeSchoolYear->id)
            ->first();

        if ($existingEnrollment) {
            return redirect()->route('student.enrollment.index')
                ->with('error', 'You have already enrolled for this school year.');
        }

        $validated = $request->validate([
            'current_grade_level' => 'nullable|string',
            'enrolling_grade_level' => 'required|string',
            'terms_accepted' => 'required|accepted',
        ]);

        // Use current_grade_level from form, or fallback to student's grade_level
        $currentGradeLevel = $validated['current_grade_level'] ?? $student->current_grade_level ?? $student->grade_level;

        // Create enrollment
        $enrollment = StudentEnrollment::create([
            'student_id' => $student->id,
            'school_year_id' => $activeSchoolYear->id,
            'current_grade_level' => $currentGradeLevel,
            'enrolling_grade_level' => $validated['enrolling_grade_level'],
            'status' => 'pending',
            'enrollment_date' => now(),
        ]);

        // Send email notification
        // Mail::to($student->email)->send(new EnrollmentReceived($enrollment));

        return redirect()->route('student.enrollment.show', $enrollment)
            ->with('success', 'Enrollment request submitted successfully! Your enrollment will be reviewed by the admin.');
    }

    /**
     * Show enrollment details
     */
    public function show(Request $request, StudentEnrollment $enrollment)
    {
        // Ensure student can only view their own enrollment
        if ($enrollment->student_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $enrollment->load(['schoolYear', 'section', 'documents', 'approvedBy']);

        $enrollmentShowLiveSignature = $this->buildEnrollmentLiveSignature($request->user(), $enrollment);

        return view('student.enrollment.show', compact('enrollment', 'enrollmentShowLiveSignature'));
    }

    public function liveSignatureIndex(Request $request): JsonResponse
    {
        $student = $request->user();
        abort_unless($student && $student->account_type === 'student', 403);

        return response()->json([
            'signature' => $this->buildEnrollmentLiveSignature($student),
            'generated_at' => now()->toIso8601String(),
        ]);
    }

    public function liveSignatureShow(Request $request, StudentEnrollment $enrollment): JsonResponse
    {
        $student = $request->user();
        abort_unless($student && $student->account_type === 'student', 403);

        if ((int) $enrollment->student_id !== (int) $student->id) {
            abort(403, 'Unauthorized action.');
        }

        return response()->json([
            'signature' => $this->buildEnrollmentLiveSignature($student, $enrollment),
            'generated_at' => now()->toIso8601String(),
        ]);
    }



    /**
     * Get next grade level suggestion
     */
    private function getNextGradeLevel($currentGradeLevel)
    {
        $gradeLevels = [
            'Grade 7' => 'Grade 8',
            'Grade 8' => 'Grade 9',
            'Grade 9' => 'Grade 10',
            'Grade 10' => 'Grade 11',
        ];

        return $gradeLevels[$currentGradeLevel] ?? $currentGradeLevel;
    }

    private function buildEnrollmentLiveSignature($student, ?StudentEnrollment $enrollment = null): string
    {
        $enrollmentBaseQuery = StudentEnrollment::query()->where('student_id', $student->id);

        $statusCounts = (clone $enrollmentBaseQuery)
            ->selectRaw('LOWER(status) as status_key, COUNT(*) as total')
            ->groupBy('status_key')
            ->pluck('total', 'status_key')
            ->toArray();
        ksort($statusCounts);

        $activeSchoolYear = SchoolYear::active()->first();
        $hasEnrolledInActiveYear = false;
        if ($activeSchoolYear) {
            $hasEnrolledInActiveYear = (clone $enrollmentBaseQuery)
                ->where('school_year_id', $activeSchoolYear->id)
                ->exists();
        }

        $payload = [
            'student_id' => (int) $student->id,
            'student_section_id' => (int) ($student->section_id ?? 0),
            'student_updated_at' => $this->timestampOrZero($student->updated_at),
            'enrollment_count' => (clone $enrollmentBaseQuery)->count(),
            'enrollment_updated_at' => $this->timestampOrZero((clone $enrollmentBaseQuery)->max('updated_at')),
            'status_counts' => $statusCounts,
            'active_school_year_id' => (int) optional($activeSchoolYear)->id,
            'active_school_year_updated_at' => $this->timestampOrZero(optional($activeSchoolYear)->updated_at),
            'active_enrollment_start' => $this->timestampOrZero(optional($activeSchoolYear)->enrollment_start),
            'active_enrollment_end' => $this->timestampOrZero(optional($activeSchoolYear)->enrollment_end),
            'active_can_enroll' => $activeSchoolYear ? (int) $activeSchoolYear->canEnrollNow() : 0,
            'has_enrolled_in_active_year' => (int) $hasEnrolledInActiveYear,
        ];

        if ($enrollment) {
            $payload['focus_enrollment_id'] = (int) $enrollment->id;
            $payload['focus_enrollment_updated_at'] = $this->timestampOrZero($enrollment->updated_at);
            $payload['focus_enrollment_status'] = (string) ($enrollment->status ?? '');
            $payload['focus_enrollment_section_id'] = (int) ($enrollment->section_id ?? 0);
            $payload['focus_enrollment_approved_at'] = $this->timestampOrZero($enrollment->approved_at);
            $payload['focus_enrollment_remarks_hash'] = md5((string) ($enrollment->admin_remarks ?? ''));
        }

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
