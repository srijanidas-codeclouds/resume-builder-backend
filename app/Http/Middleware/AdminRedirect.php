<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
{
    if (!auth()->check()) {
        return redirect()->route('blade.admin.login');
    }

    $user = auth()->user();

    if ($user->role !== 'admin') {
        abort(403, 'Admins only');
    }

    return $next($request);
}
}