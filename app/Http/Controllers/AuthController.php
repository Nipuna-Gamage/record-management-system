<?php

namespace App\Http\Controllers;

use App\Support\ActivityLogger;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            ActivityLogger::log('auth.login_failed', 'A sign-in attempt failed for '.$credentials['email']);
            throw ValidationException::withMessages([
                'email' => 'The supplied credentials are incorrect.',
            ]);
        }

        $request->session()->regenerate();

        if (! Auth::user()->is_active) {
            ActivityLogger::log('auth.logout', 'User signed out.');
        Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors(['email' => 'This account is inactive. Please contact an administrator.']);
        }

        ActivityLogger::log('auth.login', 'User signed in successfully.');
        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request)
    {
        ActivityLogger::log('auth.logout', 'User signed out.');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
