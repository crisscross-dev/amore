<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RegistrationReceived;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{
    private const ACCESS_TICKET_SESSION_KEY = 'faculty_register_access_ticket';
    private const REGISTER_NONCE_SESSION_KEY = 'faculty_register_nonce';

    public function showRegistration(Request $request)
    {
        if (Auth::check()) {
            return redirect()
                ->route('dashboard')
                ->with('popupToast', 'You must Logout first.');
        }

        $hasAccessTicket = (bool) $request->session()->pull(self::ACCESS_TICKET_SESSION_KEY, false);
        if (! $hasAccessTicket) {
            return redirect()
                ->route('login')
                ->with('error', 'Faculty access code is required before accessing registration.');
        }

        $registrationNonce = (string) Str::uuid();
        $request->session()->put(self::REGISTER_NONCE_SESSION_KEY, $registrationNonce);

        return view('layouts.register', compact('registrationNonce'));
    }

    public function verifyFacultyAccess(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'access_code' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Access code is required.',
            ], 422);
        }

        $expectedCode = (string) config('app.faculty_registration_code', '');
        $enteredCode = trim((string) $request->input('access_code'));

        if ($expectedCode !== '' && hash_equals(strtoupper($expectedCode), strtoupper($enteredCode))) {
            $request->session()->put(self::ACCESS_TICKET_SESSION_KEY, true);

            return response()->json([
                'ok' => true,
            ]);
        }

        return response()->json([
            'message' => 'Invalid access code.',
        ], 422);
    }

    public function register(Request $request)
    {
        $sessionNonce = (string) $request->session()->pull(self::REGISTER_NONCE_SESSION_KEY, '');
        $submittedNonce = (string) $request->input('registration_nonce', '');

        if ($sessionNonce === '' || $submittedNonce === '' || ! hash_equals($sessionNonce, $submittedNonce)) {
            return redirect()
                ->route('login')
                ->with('error', 'Registration access expired. Please verify faculty access code again.');
        }

        $rules = [
            'account_type' => 'nullable|string|in:faculty',
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z][A-Za-z\s\'\.-]*$/'],
            'middle_name' => ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z][A-Za-z\s\'\.-]*$/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z][A-Za-z\s\'\.-]*$/'],
            'suffix' => ['nullable', 'string', 'max:50', 'regex:/^[A-Za-z0-9\.\s-]+$/'],
            'email' => 'required|string|email|max:255|unique:users',
            'contact_number' => ['required', 'regex:/^\d{11}$/'],
            'password' => 'required|string|min:8|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        $validator = Validator::make($request->all(), $rules, [
            'first_name.regex' => 'First name must not contain numbers.',
            'middle_name.regex' => 'Middle name must not contain numbers.',
            'last_name.regex' => 'Last name must not contain numbers.',
            'suffix.regex' => 'Suffix contains invalid characters.',
            'contact_number.regex' => 'Contact number must be exactly 11 digits.',
        ]);

        if ($validator->fails()) {
            $request->session()->put(self::ACCESS_TICKET_SESSION_KEY, true);

            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $validatedData = $validator->validated();

        $userData = [
            'account_type' => 'faculty',
            'first_name' => $validatedData['first_name'],
            'middle_name' => $validatedData['middle_name'] ?? null,
            'last_name' => $validatedData['last_name'],
            'suffix' => $validatedData['suffix'] ?? null,
            'email' => $validatedData['email'],
            'contact_number' => $validatedData['contact_number'] ?? null,
            'password' => Hash::make($validatedData['password']),
        ];

        if ($request->hasFile('profile_picture')) {
            $userData['profile_picture'] = $this->storeProfilePicture($request->file('profile_picture'));
        }

        $userData['status'] = 'for_approval';

        $user = User::create($userData);

        try {
            Mail::to($user->email)->send(new RegistrationReceived($user));
        } catch (\Exception $e) {
            Log::error('Failed to send registration email: ' . $e->getMessage());
        }

        $request->session()->forget([self::ACCESS_TICKET_SESSION_KEY, self::REGISTER_NONCE_SESSION_KEY]);

        return redirect()->route('login')->with('success', 'Account created successfully! Your account is pending admin approval.');
    }

    public function validateLRN(Request $request)
    {
        return response()->json([
            'exists' => false,
            'message' => 'Student self-registration is currently disabled.'
        ], 400);
    }

    private function storeProfilePicture(UploadedFile $uploadedFile): string
    {
        $uploadsDirectory = 'uploads/profile_picture';
        $imageName = time() . '_' . uniqid() . '.' . $uploadedFile->getClientOriginalExtension();

        Storage::disk('public')->putFileAs($uploadsDirectory, $uploadedFile, $imageName);

        return $imageName;
    }
}
