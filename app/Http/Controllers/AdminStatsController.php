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

        return [
            'totalUsers'      => User::count(),
            'activeToday'     => Schema::hasColumn('users', 'last_login_at')
                ? User::whereDate('last_login_at', today())->count()
                : 0,
            'premiumUsers'    => User::where('membership', 'premium')->count(),
            'suspendedUsers'  => User::where('status', 'suspended')->count(),
        ];
    }

    /**
     * API dashboard statistics
     */
    public function getStats(): JsonResponse
    {
        $stats = Cache::remember('admin.dashboard.stats', now()->addMinutes(5), function () {
            $totalUsers   = User::count();
            $activeUsers  = $this->getActiveUsersCount();
            $totalResumes = Resume::count();

            $previousUsers   = $this->getPreviousPeriodCount(User::class);
            $previousResumes = $this->getPreviousPeriodCount(Resume::class);

            return [
                'total_users'              => $totalUsers,
                'active_users'             => $activeUsers,
                'total_resumes'            => $totalResumes,
                'admins'                   => User::where('role', 'admin')->count(),

                'previous_total_users'     => $previousUsers,
                'previous_total_resumes'   => $previousResumes,

                'user_growth_percentage'   => $this->calculateGrowth($totalUsers, $previousUsers),
                'resume_growth_percentage' => $this->calculateGrowth($totalResumes, $previousResumes),
            ];
        });

        return response()->json($stats);
    }

    /**
     * User growth chart data
     */
    public function getUserGrowth(Request $request): JsonResponse
    {
        $days = (int) $request->input('days', 7);

        $data = Cache::remember("admin.user.growth.$days", now()->addMinutes(10), function () use ($days) {
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
     * System health
     */
    public function getSystemHealth(): JsonResponse
    {
        return response()->json([
            'server_load' => $this->getServerLoad(),
            'db_storage'  => $this->getDbStorage(),
            'api_uptime'  => '99.9%',
            'status'      => 'healthy',
        ]);
    }

    /**
     * Activity logs
     */
    public function getActivityLogs(Request $request): JsonResponse
    {
        $limit = (int) $request->input('limit', 20);

        $logs = Cache::remember("admin.activity.logs.$limit", now()->addMinutes(2), function () use ($limit) {
            $activities = [];

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

    /* =========================
       Internal helpers
       ========================= */

    private function getActiveUsersCount(): int
    {
        if (!Schema::hasColumn('users', 'last_login_at')) {
            return 0;
        }

        return User::where('last_login_at', '>=', now()->subMinutes(30))->count();
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
}
