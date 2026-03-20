<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;

class StudentSectionController extends Controller
{
    public function assign(Request $request, User $user)
    {
        if (auth()->user()->account_type !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'section_id' => 'nullable|exists:sections,id'
        ]);

        if ($user->account_type !== 'student') {
            return back()->with('error', 'Only students can be assigned to sections.');
        }

        if (!empty($validated['section_id'])) {
            $section = Section::find($validated['section_id']);
            if ($section && $user->grade_level && $section->grade_level !== (string)$user->grade_level) {
                return back()->with('error', 'Section grade level must match the student\'s grade level.');
            }
        }

        $user->section_id = $validated['section_id'] ?? null;
        $user->save();

        return back()->with('success', 'Section assignment updated.');
    }

    public function listByGrade(Request $request)
    {
        $grade = $request->query('grade_level');
        $sections = Section::where('is_active', true)
            ->when($grade, fn($q) => $q->where('grade_level', $grade))
            ->orderBy('name')
            ->get(['id', 'name', 'grade_level']);
        return response()->json($sections);
    }
}
