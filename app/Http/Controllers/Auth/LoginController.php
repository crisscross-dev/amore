<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    private function dashboardRouteFor(string $accountType): string
    {
        return match (strtolower($accountType)) {
            'admin' => 'dashboard.admin',
            'faculty' => 'dashboard.faculty',
            default => 'dashboard.student',
        };
    }

    public function showLogin()
    {
        if (Auth::check()) {
            $routeName = $this->dashboardRouteFor((string) Auth::user()->account_type);

            return redirect()
                ->route($routeName)
                ->with('popup', 'You are already logged in.');
        }

        return view('layouts.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->status === 'for_approval') {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'Your account is pending admin approval. Please wait for confirmation.'
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            $routeName = $this->dashboardRouteFor((string) $user->account_type);
            return redirect()->route($routeName);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome');
    }
}
