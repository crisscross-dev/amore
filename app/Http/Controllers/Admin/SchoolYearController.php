<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
use Illuminate\Http\Request;

class SchoolYearController extends Controller
{
    /**
     * Display a listing of school years
     */
    public function index()
    {
        $schoolYears = SchoolYear::orderBy('start_date', 'desc')->get();
        return view('admin.school_years.index', compact('schoolYears'));
    }

    /**
     * Show the form for creating a new school year
     */
    public function create()
    {
        return view('admin.school_years.create');
    }

    /**
     * Store a newly created school year
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'year_name' => 'required|string|unique:school_years,year_name',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'enrollment_start' => 'required|date',
            'enrollment_end' => 'required|date|after:enrollment_start',
        ]);

        SchoolYear::create($validated);

        return redirect()->route('admin.school-years.index')
            ->with('success', 'School year created successfully!');
    }

    /**
     * Show the form for editing the specified school year
     */
    public function edit(SchoolYear $schoolYear)
    {
        return view('admin.school_years.edit', compact('schoolYear'));
    }

    /**
     * Update the specified school year
     */
    public function update(Request $request, SchoolYear $schoolYear)
    {
        $validated = $request->validate([
            'year_name' => 'required|string|unique:school_years,year_name,' . $schoolYear->id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'enrollment_start' => 'required|date',
            'enrollment_end' => 'required|date|after:enrollment_start',
        ]);

        $schoolYear->update($validated);

        return redirect()->route('admin.school-years.index')
            ->with('success', 'School year updated successfully!');
    }

    /**
     * Remove the specified school year
     */
    public function destroy(SchoolYear $schoolYear)
    {
        // Check if school year has enrollments
        if ($schoolYear->enrollments()->count() > 0) {
            return redirect()->route('admin.school-years.index')
                ->with('error', 'Cannot delete school year with existing enrollments.');
        }

        $schoolYear->delete();

        return redirect()->route('admin.school-years.index')
            ->with('success', 'School year deleted successfully!');
    }

    /**
     * Activate a school year
     */
    public function activate(SchoolYear $schoolYear)
    {
        // Deactivate all other school years
        SchoolYear::where('is_active', true)->update(['is_active' => false]);

        // Activate selected school year
        $schoolYear->update(['is_active' => true]);

        return redirect()->route('admin.school-years.index')
            ->with('success', 'School year ' . $schoolYear->year_name . ' is now active!');
    }
}
