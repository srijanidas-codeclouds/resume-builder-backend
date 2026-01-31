<?php

namespace App\Http\Controllers;

use App\Models\User;

class AdminStatsController extends Controller
{
    public function index()
    {
        abort_unless(request()->user()->canManageUsers(), 403);

        return [
            'totalUsers'    => User::count(),
            'activeToday'   => User::whereDate('last_login_at', today())->count(),
            'premiumUsers'  => User::where('membership', 'premium')->count(),
            'suspendedUsers'=> User::where('status', 'suspended')->count(),
        ];
    }
}
