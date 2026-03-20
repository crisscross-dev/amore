<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentEnrollment;
use App\Models\SchoolYear;
use App\Models\StudentEnrollmentDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EnrollmentController extends Controller
{
    /**
     * Display enrollment history and current status
     */
    public function index()
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

        return view('student.enrollment.index', compact(
            'enrollments',
            'activeSchoolYear',
            'canEnroll',
            'hasEnrolled'
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
    public function show(StudentEnrollment $enrollment)
    {
        // Ensure student can only view their own enrollment
        if ($enrollment->student_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $enrollment->load(['schoolYear', 'section', 'documents', 'approvedBy']);

        return view('student.enrollment.show', compact('enrollment'));
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
}
