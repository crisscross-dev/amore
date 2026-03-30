<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        if (!($user instanceof User)) {
            abort(403);
        }
        /** @var User $user */

        $user->loadMissing(['facultyPosition', 'positionAssignee']);
        $view = $this->resolveViewForRole($user->account_type);

        return view($view, compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        if (!($user instanceof User)) {
            abort(403);
        }
        /** @var User $user */

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

    public function liveSignature(Request $request)
    {
        $user = Auth::user();
        if (!($user instanceof User)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        if ($user->account_type !== 'faculty') {
            abort(Response::HTTP_FORBIDDEN);
        }

        $user->loadMissing(['facultyPosition', 'positionAssignee']);

        return response()->json([
            'signature' => $this->buildFacultyProfileLiveSignature($user),
            'generated_at' => now()->toIso8601String(),
        ]);
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
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contact_number' => 'required|string|max:20',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'current_password' => 'nullable|required_with:password|current_password',
            'password' => 'nullable|string|min:8|confirmed',
        ];

        if ($accountType === 'student') {
            $baseRules['grade_level'] = 'nullable|string|max:50';
        }

        return $baseRules;
    }

    private function replaceProfilePicture(User $user, UploadedFile $uploadedFile): void
    {
        $uploadsDirectory = 'uploads/profile_picture';
        $disk = Storage::disk('public');

        if ($user->profile_picture) {
            $disk->delete($uploadsDirectory . '/' . $user->profile_picture);

            $legacyPath = public_path($uploadsDirectory . DIRECTORY_SEPARATOR . $user->profile_picture);
            if (is_file($legacyPath)) {
                @unlink($legacyPath);
            }
        }

        $imageName = time() . '_' . uniqid() . '.' . $uploadedFile->getClientOriginalExtension();
        $disk->putFileAs($uploadsDirectory, $uploadedFile, $imageName);

        $user->profile_picture = $imageName;
    }

    private function buildFacultyProfileLiveSignature(User $user): string
    {
        return implode('|', [
            (int) ($user->id ?? 0),
            (int) ($user->faculty_position_id ?? 0),
            (string) ($user->department ?? ''),
            (int) ($user->assigned_by ?? 0),
            $this->timestampOrZero($user->updated_at),
            $this->timestampOrZero(optional($user->facultyPosition)->updated_at),
            $this->timestampOrZero(optional($user->positionAssignee)->updated_at),
        ]);
    }

    private function timestampOrZero($value): int
    {
        if (empty($value)) {
            return 0;
        }

        $timestamp = strtotime((string) $value);

        return $timestamp !== false ? $timestamp : 0;
    }
}
