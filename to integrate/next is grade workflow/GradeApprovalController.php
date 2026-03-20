<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GradeEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GradeApprovalController extends Controller
{
    public function index(): View
    {
        $submissions = GradeEntry::query()
            ->where('status', 'submitted')
            ->orderBy('submitted_at', 'desc')
            ->paginate(12);

        return view('admin.grade_approvals.index', compact('submissions'));
    }

    public function show(GradeEntry $grade): View
    {
        return view('admin.grade_approvals.show', compact('grade'));
    }

    public function approve(GradeEntry $grade): RedirectResponse
    {
        if ($grade->status !== 'submitted') {
            return redirect()->route('admin.grade-approvals.index')->with('error', 'Only submitted grades can be approved.');
        }

        $grade->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        return redirect()->route('admin.grade-approvals.index')->with('success', 'Grade approved.');
    }

    public function reject(GradeEntry $grade, \Illuminate\Http\Request $request): RedirectResponse
    {
        if ($grade->status !== 'submitted') {
            return redirect()->route('admin.grade-approvals.index')->with('error', 'Only submitted grades can be rejected.');
        }

        $data = $request->validate(['reason' => ['required', 'string']]);
        $grade->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $data['reason'],
        ]);

        return redirect()->route('admin.grade-approvals.index')->with('success', 'Grade rejected.');
    }
}
