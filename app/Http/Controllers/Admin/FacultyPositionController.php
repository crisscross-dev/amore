<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacultyPosition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacultyPositionController extends Controller
{
    public function index(): View
    {
        $positions = FacultyPosition::orderBy('hierarchy_level')->orderBy('name')->get();

        return view('admin.faculty_positions.index', compact('positions'));
    }

    public function create(): View
    {
        return view('admin.faculty_positions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePosition($request);

        FacultyPosition::create($validated);

        return redirect()
            ->route('admin.faculty-positions.index')
            ->with('success', 'Faculty position created successfully.');
    }

    public function edit(FacultyPosition $position): View
    {
        return view('admin.faculty_positions.edit', compact('position'));
    }

    public function update(Request $request, FacultyPosition $position): RedirectResponse
    {
        $validated = $this->validatePosition($request, $position->id);

        $position->update($validated);

        return redirect()
            ->route('admin.faculty-positions.index')
            ->with('success', 'Faculty position updated successfully.');
    }

    public function destroy(FacultyPosition $position): RedirectResponse
    {
        if ($position->facultyMembers()->exists()) {
            return back()->withErrors('Cannot delete a position that is currently assigned to faculty members.');
        }

        $position->delete();

        return redirect()
            ->route('admin.faculty-positions.index')
            ->with('success', 'Faculty position deleted successfully.');
    }

    private function validatePosition(Request $request, ?int $positionId = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:faculty_positions,code,' . ($positionId ?? 'null')],
            'description' => ['nullable', 'string'],
            'category' => ['required', 'in:administrative,teaching,support'],
            'hierarchy_level' => ['required', 'integer', 'min:1', 'max:15'],
            'is_active' => ['nullable', 'boolean'],
        ], [], [
            'name' => 'position name',
            'code' => 'position code',
            'category' => 'position category',
            'hierarchy_level' => 'hierarchy level',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->boolean('is_active', true);

        return $validated;
    }
}
