<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminRedirect
{
    public function handle(Request $request, Closure $next): Response
    {
        // Not logged in → send to ADMIN login
        if (!Auth::check()) {
            return redirect()->route('blade.admin.login');
        }

        $user = Auth::user();

        // Logged in but not admin
        if ($user->role !== 'admin') {
            abort(403, 'Admins only');
        }

        return $next($request);
    }
}
