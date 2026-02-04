<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with users, statistics, and activity logs.
     */
    public function index(): View
    {
        $users = $this->getRecentUsers();
        $stats = $this->getDashboardStats();
        $logs = $this->getActivityLogs();

        return view('admin.dashboard', compact('users', 'stats', 'logs'));
    }

    /**
     * Get recently registered users.
     */
    private function getRecentUsers(int $limit = 20): Collection
    {
        return User::query()
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Calculate dashboard statistics with caching.
     */
    private function getDashboardStats(): array
    {
        return Cache::remember('admin.dashboard.stats', now()->addMinutes(5), function () {
            return [
                'total_users' => User::count(),
                'admins' => $this->getAdminCount(),
                'active_users' => $this->getActiveUserCount(),
            ];
        });
    }

    /**
     * Get count of admin users.
     */
    private function getAdminCount(): int
    {
        if (!$this->columnExists('users', 'role')) {
            return 0;
        }

        return User::where('role', 'admin')->count();
    }

    /**
     * Get count of active users.
     */
    private function getActiveUserCount(): int
    {
        if (!$this->columnExists('users', 'status')) {
            return 0;
        }

        return User::where('status', 'active')->count();
    }

    /**
     * Build activity logs from user data.
     */
    // Make sure your logs are properly cast
    private function getActivityLogs(int $limit = 10): Collection
    {
        $logs = collect();

        $this->addLoginLogs($logs);
        $this->addRegistrationLogs($logs);
        $this->addUpdateLogs($logs);

        return $logs
            ->sortByDesc('time')
            ->take($limit)
            ->map(function ($log) {
                // Ensure time is always a Carbon instance
                if (is_string($log['time'])) {
                    $log['time'] = \Carbon\Carbon::parse($log['time']);
                }
                return $log;
            })
            ->values();
    }

    /**
     * Add recent login activity to logs.
     */
    private function addLoginLogs(Collection $logs): void
    {
        if (!$this->columnExists('users', 'last_login_at')) {
            return;
        }

        User::query()
            ->whereNotNull('last_login_at')
            ->latest('last_login_at')
            ->limit(5)
            ->get()
            ->each(function (User $user) use ($logs) {
                $logs->push([
                    'time' => $user->last_login_at,
                    'type' => 'info',
                    'message' => sprintf(
                        "User '%s' logged in",
                        $user->username ?? $user->email ?? 'Unknown'
                    ),
                    'user_id' => $user->id,
                ]);
            });
    }

    /**
     * Add recent user registrations to logs.
     */
    private function addRegistrationLogs(Collection $logs): void
    {
        User::query()
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->each(function (User $user) use ($logs) {
                $logs->push([
                    'time' => $user->created_at,
                    'type' => 'success',
                    'message' => sprintf(
                        "New user '%s' registered",
                        $user->username ?? $user->email
                    ),
                    'user_id' => $user->id,
                ]);
            });
    }

    /**
     * Add recent profile updates to logs.
     */
    private function addUpdateLogs(Collection $logs): void
    {
        User::query()
            ->whereColumn('updated_at', '!=', 'created_at')
            ->latest('updated_at')
            ->limit(5)
            ->get()
            ->each(function (User $user) use ($logs) {
                $logs->push([
                    'time' => $user->updated_at,
                    'type' => 'system',
                    'message' => sprintf(
                        "User '%s' profile updated",
                        $user->username ?? $user->email
                    ),
                    'user_id' => $user->id,
                ]);
            });
    }

    /**
     * Check if a column exists in a table (with caching).
     */
    private function columnExists(string $table, string $column): bool
    {
        $cacheKey = "schema.{$table}.{$column}";

        return Cache::rememberForever($cacheKey, function () use ($table, $column) {
            return Schema::hasColumn($table, $column);
        });
    }
}
