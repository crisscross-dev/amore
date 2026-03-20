<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user()->loadMissing(['facultyPosition', 'positionAssignee']);
        $view = $this->resolveViewForRole($user->account_type);

        return view($view, compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $accountType = $user->account_type;

        $request->validate($this->rulesForRole($accountType));

        $user->first_name = $request->first_name;
        $user->middle_name = $request->middle_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->contact_number = $request->contact_number;

        if ($accountType === 'student' && $request->filled('grade_level')) {
            $user->grade_level = $request->grade_level;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);

            if ($accountType === 'student') {
                $user->first_login = false;
            }
        }

        if ($request->hasFile('profile_picture')) {
            $this->replaceProfilePicture($user, $request->file('profile_picture'));
        }

        $user->save();

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Profile updated successfully.');
    }

    private function resolveViewForRole(?string $accountType): string
    {
        return match ($accountType) {
            'admin' => 'admin.edit',
            'faculty' => 'faculty.edit',
            default => 'student.edit',
        };
    }

    private function rulesForRole(?string $accountType): array
    {
        $baseRules = [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contact_number' => 'required|string|max:20',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'nullable|string|min:8|confirmed',
        ];

        if ($accountType === 'student') {
            $baseRules['grade_level'] = 'nullable|string|max:50';
        }

        return $baseRules;
    }

    private function replaceProfilePicture($user, $uploadedFile): void
    {
        $uploadsPath = public_path('uploads/profile_picture');

        if ($user->profile_picture) {
            $existingPath = $uploadsPath . DIRECTORY_SEPARATOR . $user->profile_picture;

            if (file_exists($existingPath)) {
                @unlink($existingPath);
            }
        }

        $imageName = time() . '.' . $uploadedFile->getClientOriginalExtension();
        $uploadedFile->move($uploadsPath, $imageName);

        $user->profile_picture = $imageName;
    }
}
