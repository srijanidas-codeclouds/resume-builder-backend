<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Resume;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminStatsController extends Controller
{
    /**
     * Web/Admin summary stats
     */
public function index(): array
    {
        abort_unless(request()->user()?->canManageUsers(), 403);

        return Cache::remember('admin.summary.stats', now()->addHour(), function () {
            return [
                'totalUsers'      => User::count(),
                'activeUsers'     => $this->getActiveUsersCount(),
                'premiumUsers'    => $this->getPremiumUsersCount(),
                'suspendedUsers'  => $this->getSuspendedUsersCount(),
            ];
        });
    }

    /**
     * API dashboard statistics - Optimized with 1 hour cache
     */
    public function getStats(): JsonResponse
    {
        $stats = Cache::remember('admin.dashboard.stats', now()->addHour(), function () {
            $totalUsers   = User::count();
            $activeUsers  = $this->getActiveUsersCount();
            $totalResumes = Resume::count();

            $previousUsers   = $this->getPreviousPeriodCount(User::class);
            $previousResumes = $this->getPreviousPeriodCount(Resume::class);

            return [
                'total_users'              => $totalUsers,
                'active_users'             => $activeUsers,
                'total_resumes'            => $totalResumes,
                'admins'                   => $this->getAdminCount(),
                'premium_users'            => $this->getPremiumUsersCount(),
                'free_users'               => $this->getFreeUsersCount(),

                'previous_total_users'     => $previousUsers,
                'previous_total_resumes'   => $previousResumes,

                'user_growth_percentage'   => $this->calculateGrowth($totalUsers, $previousUsers),
                'resume_growth_percentage' => $this->calculateGrowth($totalResumes, $previousResumes),
                
                // SaaS Metrics
                'avg_resumes_per_user'     => $totalUsers > 0 ? round($totalResumes / $totalUsers, 2) : 0,
                'conversion_rate'          => $this->getConversionRate(),
                'churn_rate'               => $this->getChurnRate(),
            ];
        });

        return response()->json($stats);
    }


    /**
     * User growth chart data - Optimized with 1 hour cache
     */
    public function getUserGrowth(Request $request): JsonResponse
    {
        $days = (int) $request->input('days', 7);

        $data = Cache::remember("admin.user.growth.$days", now()->addHour(), function () use ($days) {
            $start = now()->subDays($days - 1)->startOfDay();

            $raw = User::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as count')
                )
                ->where('created_at', '>=', $start)
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return collect(range(0, $days - 1))->map(function ($i) use ($days, $raw) {
                $date = now()->subDays($days - 1 - $i);
                $row  = $raw->firstWhere('date', $date->format('Y-m-d'));

                return [
                    'name'     => $date->format('D'),
                    'fullDate' => $date->format('Y-m-d'),
                    'value'    => $row ? (int) $row->count : 0,
                ];
            })->values();
        });

        return response()->json($data);
    }

    /**
     * Revenue chart data (Monthly Recurring Revenue)
     */
    public function getRevenueData(Request $request): JsonResponse
    {
        $months = (int) $request->input('months', 6);

        $data = Cache::remember("admin.revenue.data.$months", now()->addHour(), function () use ($months) {
            return collect(range(0, $months - 1))->map(function ($i) use ($months) {
                $date = now()->subMonths($months - 1 - $i);
                
                // Simulate revenue calculation - Replace with actual subscription logic
                $premiumCount = User::where('membership', 'premium')
                    ->whereYear('created_at', '<=', $date->year)
                    ->whereMonth('created_at', '<=', $date->month)
                    ->count();

                return [
                    'month' => $date->format('M Y'),
                    'mrr'   => $premiumCount * 29, // $29 per premium user
                    'arr'   => $premiumCount * 29 * 12,
                ];
            })->values();
        });

        return response()->json($data);
    }

    /**
     * User engagement metrics
     */
    public function getEngagementMetrics(): JsonResponse
    {
        $data = Cache::remember('admin.engagement.metrics', now()->addHour(), function () {
            $last7Days = now()->subDays(7);
            $last30Days = now()->subDays(30);

            return [
                'dau' => Schema::hasColumn('users', 'last_login_at')
                    ? User::whereDate('last_login_at', today())->count()
                    : 0,
                'wau' => Schema::hasColumn('users', 'last_login_at')
                    ? User::where('last_login_at', '>=', $last7Days)->count()
                    : 0,
                'mau' => Schema::hasColumn('users', 'last_login_at')
                    ? User::where('last_login_at', '>=', $last30Days)->count()
                    : 0,
                'avg_session_duration' => '12m 34s', // Implement actual session tracking
                'bounce_rate' => '32%', // Implement actual bounce tracking
            ];
        });

        return response()->json($data);
    }

    /**
     * Membership distribution for pie chart
     */
    public function getMembershipDistribution(): JsonResponse
    {
        $data = Cache::remember('admin.membership.distribution', now()->addHour(), function () {
            $premium = User::where('membership', 'premium')->count();
            $free = User::where('membership', 'free')->orWhereNull('membership')->count();
            
            return [
                ['name' => 'Premium', 'value' => $premium, 'color' => '#10b981'],
                ['name' => 'Free', 'value' => $free, 'color' => '#64748b'],
            ];
        });

        return response()->json($data);
    }

    /**
     * Resume creation trends
     */
    public function getResumeCreationTrends(Request $request): JsonResponse
    {
        $days = (int) $request->input('days', 14);

        $data = Cache::remember("admin.resume.trends.$days", now()->addHour(), function () use ($days) {
            $start = now()->subDays($days - 1)->startOfDay();

            $raw = Resume::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as count')
                )
                ->where('created_at', '>=', $start)
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return collect(range(0, $days - 1))->map(function ($i) use ($days, $raw) {
                $date = now()->subDays($days - 1 - $i);
                $row  = $raw->firstWhere('date', $date->format('Y-m-d'));

                return [
                    'date'   => $date->format('M d'),
                    'resumes' => $row ? (int) $row->count : 0,
                ];
            })->values();
        });

        return response()->json($data);
    }

    /**
     * Top users by resume count
     */
    public function getTopUsers(): JsonResponse
    {
        $data = Cache::remember('admin.top.users', now()->addHour(), function () {
            $users = User::select('users.id', 'users.name', 'users.email');
            
            // Add membership/role column if exists
            if ($this->columnExists('users', 'membership')) {
                $users->addSelect('membership');
            } else if ($this->columnExists('users', 'role')) {
                $users->addSelect('role');
            }

            return $users->withCount('resumes')
                ->orderByDesc('resumes_count')
                ->limit(10)
                ->get()
                ->map(function($user) {
                    return [
                        'name' => $user->name,
                        'email' => $user->email,
                        'resumes' => $user->resumes_count,
                        'membership' => $this->getUserMembership($user),
                    ];
                });
        });

        return response()->json($data);
    }
    /**
     * System health - Cached for 30 minutes
     */
    public function getSystemHealth(): JsonResponse
    {
        $health = Cache::remember('admin.system.health', now()->addMinutes(30), function () {
            return [
                'server_load' => $this->getServerLoad(),
                'db_storage'  => $this->getDbStorage(),
                'cache_status' => $this->getCacheStatus(),
                'api_uptime'  => '99.9%',
                'status'      => 'healthy',
                'last_backup' => $this->getLastBackupTime(),
            ];
        });

        return response()->json($health);
    }

    /**
     * Activity logs - Cached for 15 minutes
     */
    public function getActivityLogs(Request $request): JsonResponse
    {
        $limit = (int) $request->input('limit', 20);

        $logs = Cache::remember("admin.activity.logs.$limit", now()->addMinutes(15), function () use ($limit) {
            $activities = [];

            if (Schema::hasColumn('users', 'last_login_at')) {
                User::whereNotNull('last_login_at')
                    ->latest('last_login_at')
                    ->limit(5)
                    ->get()
                    ->each(fn ($u) => $activities[] = [
                        'type'      => 'login',
                        'message'   => "User '{$u->name}' logged in",
                        'timestamp' => $u->last_login_at,
                        'user_id'   => $u->id,
                    ]);
            }

            User::latest()
                ->limit(5)
                ->get()
                ->each(fn ($u) => $activities[] = [
                    'type'      => 'registration',
                    'message'   => "New user '{$u->name}' registered",
                    'timestamp' => $u->created_at,
                    'user_id'   => $u->id,
                ]);

            Resume::with('user:id,name')
                ->latest()
                ->limit(5)
                ->get()
                ->each(fn ($r) => $activities[] = [
                    'type'      => 'resume_created',
                    'message'   => "User '{$r->user->name}' created a resume",
                    'timestamp' => $r->created_at,
                    'user_id'   => $r->user_id,
                ]);

            return collect($activities)
                ->sortByDesc('timestamp')
                ->take($limit)
                ->values();
        });

        return response()->json($logs);
    }

    /**
     * Clear dashboard cache (admin action)
     */
    public function clearCache(): JsonResponse
    {
        abort_unless(request()->user()?->canManageUsers(), 403);

        $patterns = [
            'admin.dashboard.stats',
            'admin.summary.stats',
            'admin.user.growth.*',
            'admin.revenue.data.*',
            'admin.engagement.metrics',
            'admin.membership.distribution',
            'admin.resume.trends.*',
            'admin.top.users',
            'admin.system.health',
            'admin.activity.logs.*',
        ];

        foreach ($patterns as $pattern) {
            if (str_contains($pattern, '*')) {
                // Clear pattern-based keys (implementation depends on cache driver)
                Cache::flush();
                break;
            } else {
                Cache::forget($pattern);
            }
        }

        return response()->json(['message' => 'Dashboard cache cleared successfully']);
    }

    /* =========================
       Internal helpers
       ========================= */

    /**
     * Check if a column exists in a table (cached forever)
     */
    private function columnExists(string $table, string $column): bool
    {
        $cacheKey = "schema.{$table}.{$column}";

        return Cache::rememberForever($cacheKey, function () use ($table, $column) {
            return Schema::hasColumn($table, $column);
        });
    }

    /**
     * Get active users count based on available columns
     * Uses status='active' if column exists, otherwise falls back to recent activity
     */
    private function getActiveUsersCount(): int
    {
        // Priority 1: Check status column (YOUR CURRENT SETUP)
        if ($this->columnExists('users', 'status')) {
            return User::where('status', 'active')->count();
        }

        // Priority 2: Check last_login_at (users who logged in last 30 minutes)
        if ($this->columnExists('users', 'last_login_at')) {
            return User::where('last_login_at', '>=', now()->subMinutes(30))->count();
        }

        // Priority 3: Fallback to users created in last 24 hours
        return User::where('created_at', '>=', now()->subDay())->count();
    }

    /**
     * Get users active today
     */
    private function getActiveTodayCount(): int
    {
        if ($this->columnExists('users', 'last_login_at')) {
            return User::whereDate('last_login_at', today())->count();
        }

        if ($this->columnExists('users', 'status')) {
            return User::where('status', 'active')->count();
        }

        return User::whereDate('created_at', today())->count();
    }

    /**
     * Get users active this week
     */
    private function getActiveWeekCount(): int
    {
        if ($this->columnExists('users', 'last_login_at')) {
            return User::where('last_login_at', '>=', now()->subDays(7))->count();
        }

        if ($this->columnExists('users', 'status')) {
            return User::where('status', 'active')->count();
        }

        return User::where('created_at', '>=', now()->subDays(7))->count();
    }

    /**
     * Get users active this month
     */
    private function getActiveMonthCount(): int
    {
        if ($this->columnExists('users', 'last_login_at')) {
            return User::where('last_login_at', '>=', now()->subDays(30))->count();
        }

        if ($this->columnExists('users', 'status')) {
            return User::where('status', 'active')->count();
        }

        return User::where('created_at', '>=', now()->subDays(30))->count();
    }

    /**
     * Get premium users count
     */
    private function getPremiumUsersCount(): int
    {
        if ($this->columnExists('users', 'membership')) {
            return User::where('membership', 'premium')->count();
        }

        if ($this->columnExists('users', 'role')) {
            return User::where('role', 'premium')->count();
        }

        return 0;
    }

    /**
     * Get premium users count up to a specific date
     */
    private function getPremiumUsersCountUpTo($date): int
    {
        if ($this->columnExists('users', 'membership')) {
            return User::where('membership', 'premium')
                ->where('created_at', '<=', $date)
                ->count();
        }

        if ($this->columnExists('users', 'role')) {
            return User::where('role', 'premium')
                ->where('created_at', '<=', $date)
                ->count();
        }

        return 0;
    }

    /**
     * Get free users count
     */
    private function getFreeUsersCount(): int
    {
        $total = User::count();
        $premium = $this->getPremiumUsersCount();
        return $total - $premium;
    }

    /**
     * Get suspended users count
     */
    private function getSuspendedUsersCount(): int
    {
        if ($this->columnExists('users', 'status')) {
            return User::where('status', 'suspended')->count();
        }

        return 0;
    }

    /**
     * Get admin users count
     */
    private function getAdminCount(): int
    {
        if ($this->columnExists('users', 'role')) {
            return User::where('role', 'admin')->count();
        }

        return 0;
    }

    /**
     * Get user membership status
     */
    private function getUserMembership($user): string
    {
        if (isset($user->membership)) {
            return $user->membership ?? 'free';
        }

        if (isset($user->role)) {
            return in_array($user->role, ['admin', 'premium']) ? $user->role : 'free';
        }

        return 'free';
    }

    private function getPreviousPeriodCount(string $model): int
    {
        return $model::whereBetween('created_at', [
            now()->subDays(60)->startOfDay(),
            now()->subDays(30)->endOfDay(),
        ])->count();
    }

    private function calculateGrowth(int $current, int $previous): string
    {
        if ($previous === 0) {
            return $current > 0 ? '+100%' : '0%';
        }

        $growth = (($current - $previous) / $previous) * 100;
        return ($growth >= 0 ? '+' : '') . number_format($growth, 1) . '%';
    }

    private function getConversionRate(): string
    {
        $total = User::count();
        if ($total === 0) return '0%';

        $premium = $this->getPremiumUsersCount();
        return number_format(($premium / $total) * 100, 1) . '%';
    }

    private function getChurnRate(): string
    {
        // Implement actual churn calculation based on subscription cancellations
        // This is a placeholder
        return '2.3%';
    }

    private function getServerLoad(): string
    {
        $load = sys_getloadavg()[0] ?? 0;
        $cpu  = (int) shell_exec('nproc') ?: 4;

        return number_format(min(($load / $cpu) * 100, 100), 0) . '%';
    }

    private function getDbStorage(): string
    {
        try {
            $size = DB::selectOne("
                SELECT SUM(data_length + index_length) / 1024 / 1024 AS size_mb
                FROM information_schema.TABLES
                WHERE table_schema = DATABASE()
            ")->size_mb ?? 0;

            return number_format(min(($size / 1000) * 100, 100), 0) . '%';
        } catch (\Throwable) {
            return '0%';
        }
    }

    private function getCacheStatus(): string
    {
        try {
            Cache::put('health_check', true, 60);
            return Cache::get('health_check') ? 'operational' : 'degraded';
        } catch (\Throwable) {
            return 'offline';
        }
    }

    private function getLastBackupTime(): string
    {
        // Implement actual backup time retrieval
        return now()->subHours(6)->diffForHumans();
    }
}