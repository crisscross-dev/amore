<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\GradeEntry;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradeController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user && $user->account_type === 'student', 403);

        $entries = GradeEntry::query()
            ->where('student_id', $user->id)
            ->where('status', 'approved')
            ->orderByDesc('approved_at')
            ->paginate(12);

        return view('student.grades.index', compact('entries'));
    }
}
