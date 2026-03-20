<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentEnrollment;
use App\Models\SchoolYear;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            $query->whereHas('student', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $enrollments = $query->orderBy('created_at', 'desc')->paginate(20);

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
            'stats',
            'schoolYears',
            'gradeLevels'
        ));
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
}
