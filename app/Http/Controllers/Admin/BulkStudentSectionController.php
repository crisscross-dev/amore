<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;

class BulkStudentSectionController extends Controller
{
    public function bulkAssign(Request $request)
    {
        if (auth()->user()->account_type !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'section_id' => 'required|exists:sections,id',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:users,id'
        ]);

        $section = Section::findOrFail($validated['section_id']);
        $studentIds = $validated['student_ids'];

        // Verify all students are in the correct grade level
        $students = User::whereIn('id', $studentIds)
            ->where('account_type', 'student')
            ->get();

        $invalidStudents = $students->filter(function($student) use ($section) {
            return (string)$student->grade_level !== (string)$section->grade_level;
        });

        if ($invalidStudents->count() > 0) {
            return back()->with('error', 'Some students do not match the section grade level.');
        }

        // Assign all students to section
        User::whereIn('id', $studentIds)->update(['section_id' => $section->id]);

        $count = count($studentIds);
        return back()->with('success', "{$count} student" . ($count > 1 ? 's' : '') . " added to section successfully.");
    }
}
