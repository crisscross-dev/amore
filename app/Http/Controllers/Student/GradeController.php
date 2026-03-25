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
            ->with(['subject', 'approver'])
            ->where('student_id', $user->id)
            ->where(function ($query) {
                $query->where('status', 'approved')
                    ->orWhere(function ($approvedQuery) {
                        $approvedQuery->whereNotNull('approved_by')
                            ->whereNotNull('approved_at');
                    });
            })
            ->orderByDesc('approved_at')
            ->paginate(12);

        return view('student.grades.index', compact('entries'));
    }
}
