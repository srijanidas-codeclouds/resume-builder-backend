<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        $users = User::latest()->limit(20)->get();

        // Build system activity logs from user table
        $logs = $this->buildActivityLogs();

        return view('admin.dashboard', [
            'users' => $users,
            'logs' => $logs,
            'stats' => [
                'total_users' => User::count(),
                'admins' => User::where('role', 'admin')->count(),
                'active_users' => User::where('status', 'active')->count(),
            ],
        ]);
    }

    /**
     * Create activity logs using existing user data
     */
    private function buildActivityLogs(): Collection
    {
        $logs = collect();

        // Recently logged in users
        User::whereNotNull('last_login_at')
            ->latest('last_login_at')
            ->limit(5)
            ->get()
            ->each(function ($user) use ($logs) {
                $logs->push([
                    'time' => $user->last_login_at,
                    'type' => 'info',
                    'message' => "User '{$user->username}' logged in",
                ]);
            });

        // Recently created users
        User::latest()
            ->limit(5)
            ->get()
            ->each(function ($user) use ($logs) {
                $logs->push([
                    'time' => $user->created_at,
                    'type' => 'success',
                    'message' => "New user '{$user->username}' registered",
                ]);
            });

        // Recently updated users
        User::whereColumn('updated_at', '!=', 'created_at')
            ->latest('updated_at')
            ->limit(5)
            ->get()
            ->each(function ($user) use ($logs) {
                $logs->push([
                    'time' => $user->updated_at,
                    'type' => 'system',
                    'message' => "User '{$user->username}' profile updated",
                ]);
            });

        // Sort logs by time descending
        return $logs->sortByDesc('time')->take(10)->values();
    }
}
