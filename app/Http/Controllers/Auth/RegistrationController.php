<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RegistrationReceived;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegistrationController extends Controller
{
    public function showRegistration()
    {
        if (Auth::check()) {
            return redirect()
                ->route('dashboard')
                ->with('popupToast', 'You must Logout first.');
        }

        return view('layouts.register');
    }

    public function register(Request $request)
    {
        $rules = [
            'account_type' => 'nullable|string|in:faculty',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'contact_number' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        $validatedData = $request->validate($rules);

        $userData = [
            'account_type' => 'faculty',
            'first_name' => $validatedData['first_name'],
            'middle_name' => $validatedData['middle_name'] ?? null,
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'contact_number' => $validatedData['contact_number'] ?? null,
            'password' => Hash::make($validatedData['password']),
        ];

        if ($request->hasFile('profile_picture')) {
            $imageName = time() . '.' . $request->profile_picture->getClientOriginalExtension();
            $request->profile_picture->move(public_path('uploads/profile_picture/'), $imageName);
            $userData['profile_picture'] = $imageName;
        }

        $userData['status'] = 'for_approval';

        $user = User::create($userData);

        try {
            Mail::to($user->email)->send(new RegistrationReceived($user));
        } catch (\Exception $e) {
            \Log::error('Failed to send registration email: ' . $e->getMessage());
        }

        return redirect()->route('login')->with('success', 'Account created successfully! Your account is pending admin approval.');
    }

    public function validateLRN(Request $request)
    {
        return response()->json([
            'exists' => false,
            'message' => 'Student self-registration is currently disabled.'
        ], 400);
    }
}
