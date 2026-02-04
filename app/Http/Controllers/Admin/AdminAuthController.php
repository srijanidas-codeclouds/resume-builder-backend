<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    /**
     * Display the admin login page.
     * Ensures any existing session is cleared for security.
     */
    public function showLogin(): View
    {
        // Only logout if user is already authenticated
        if (Auth::check()) {
            $this->clearSession();
        }

        return view('admin.auth.login');
    }

    /**
     * Handle admin login authentication.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $this->validateLoginRequest($request);

        $user = $this->findAdminUser($credentials['login']);

        if (!$user) {
            $this->logFailedAttempt($request, $credentials['login']);
            return $this->failedLogin('These credentials do not match our records.');
        }

        if (!$this->verifyPassword($credentials['password'], $user->password)) {
            $this->logFailedAttempt($request, $user->username ?? $user->email);
            return $this->failedLogin('The provided password is incorrect.', 'password');
        }

        return $this->authenticateUser($request, $user);
    }

    /**
     * Logout the admin user.
     */
    public function logout(Request $request): RedirectResponse
{
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    $redirectUrl = config(
        'app.admin_frontend_url',
        'https://resume-builder-frontend-ruby-nine.vercel.app/admin'
    );

    return redirect()->away($redirectUrl);
}


    /**
     * Validate the login request.
     */
    private function validateLoginRequest(Request $request): array
    {
        return $request->validate([
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'login.required' => 'Please enter your email or username.',
            'password.required' => 'Please enter your password.',
            'password.min' => 'Password must be at least 6 characters.',
        ]);
    }

    /**
     * Find an admin user by email or username.
     */
    private function findAdminUser(string $login): ?User
    {
        return User::query()
            ->where(function ($query) use ($login) {
                $query->where('email', $login)
                      ->orWhere('username', $login);
            })
            ->where('role', 'admin')
            ->first();
    }

    /**
     * Verify the provided password against the hashed password.
     */
    private function verifyPassword(string $password, string $hashedPassword): bool
    {
        return Hash::check($password, $hashedPassword);
    }

    /**
     * Authenticate the user and regenerate session.
     */
    private function authenticateUser(Request $request, User $user): RedirectResponse
    {
        // Login with "remember me" enabled
        Auth::login($user, true);
        
        // Regenerate session to prevent session fixation attacks
        $request->session()->regenerate();

        // Update last login timestamp
        $this->updateLastLogin($user);

        // Flash success message
        session()->flash('login_success', [
            'message' => 'Welcome back, ' . ($user->username ?? $user->name ?? 'Admin'),
            'time' => now(),
        ]);

        return redirect()
            ->intended(route('blade.admin.dashboard'))
            ->with('status', 'Successfully logged in!');
    }

    /**
     * Update the user's last login timestamp.
     */
    private function updateLastLogin(User $user): void
    {
        // Only update if column exists
        if (in_array('last_login_at', $user->getFillable())) {
            $user->update(['last_login_at' => now()]);
        }
    }

    /**
     * Log failed login attempt for security monitoring.
     */
    private function logFailedAttempt(Request $request, string $identifier): void
    {
        session()->flash('failed_login', [
            'identifier' => $identifier,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
        ]);

        // Optional: Log to file or database for security audit
        // Log::warning('Failed admin login attempt', [
        //     'identifier' => $identifier,
        //     'ip' => $request->ip(),
        // ]);
    }

    /**
     * Return a failed login response.
     */
    private function failedLogin(string $message, string $field = 'login'): RedirectResponse
    {
        return back()
            ->withInput(request()->only('login'))
            ->withErrors([$field => $message]);
    }

    /**
     * Clear the current session and logout.
     */
    private function clearSession(): void
    {
        Auth::guard('web')->logout();
        
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
}