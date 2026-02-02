<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    /**
     * Show admin login page
     * Forces fresh authentication every time (double protection)
     */
    public function showLogin()
    {
        // Ensure no existing session is reused
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return view('admin.auth.login');
    }

    /**
     * Handle admin login
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        // Allow login using email or username
        $user = User::where('email', $request->login)
            ->orWhere('username', $request->login)
            ->first();

        // User not found
        if (!$user) {
            session()->flash('failed_login', [
                'username' => $request->login,
                'ip' => $request->ip(),
                'time' => now(),
            ]);

            return back()->withErrors([
                'login' => 'User not found',
            ]);
        }

        // Restrict access to admin users only
        if ($user->role !== 'admin') {
            return back()->withErrors([
                'login' => 'Unauthorized access',
            ]);
        }

        // Password verification
        if (!Hash::check($request->password, $user->password)) {
            session()->flash('failed_login', [
                'username' => $user->username,
                'ip' => $request->ip(),
                'time' => now(),
            ]);

            return back()->withErrors([
                'password' => 'Incorrect password',
            ]);
        }

        // Authenticate user and regenerate session
        Auth::login($user, false);
        $request->session()->regenerate();

        // Update last login timestamp
        $user->update([
            'last_login_at' => now(),
        ]);

        return redirect()->route('blade.admin.dashboard');
    }

    /**
     * Logout admin and redirect back to React admin panel
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->away('https://resume-builder-frontend-ruby-nine.vercel.app/admin');
    }
}
