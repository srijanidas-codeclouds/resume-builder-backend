<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin Dashboard - {{ config('app.name', 'Laravel') }}</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f8fafc;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            line-height: 1.6;
            color: #334155;
        }

        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Warning Banner */
        .admin-warning {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border: 2px solid #fbbf24;
            color: #92400e;
            padding: 16px 20px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .admin-warning strong {
            display: block;
            font-size: 1rem;
            margin-bottom: 4px;
        }

        .admin-warning small {
            font-size: 0.875rem;
            opacity: 0.9;
        }

        /* Profile Header */
        .admin-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            padding: 24px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .avatar-circle {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);
        }

        .admin-header h2 {
            margin: 0;
            font-size: 1.5rem;
            color: #1e293b;
        }

        .admin-header p {
            margin: 4px 0 0 0;
            font-size: 0.875rem;
            color: #64748b;
        }

        /* Logout Button */
        .logout-btn {
            background: #ffffff;
            border: 2px solid #ef4444;
            color: #dc2626;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .logout-btn:hover {
            background: #fef2f2;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.2);
        }

        .logout-btn:active {
            transform: translateY(0);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border: 1px solid #e2e8f0;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .stat-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .stat-value {
            font-weight: 700;
            font-size: 2rem;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .icon-users { background: #dbeafe; }
        .icon-admin { background: #fce7f3; }
        .icon-active { background: #dcfce7; }

        /* Info Grid */
        .admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 30px;
        }

        .info-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 16px;
            border-radius: 10px;
            transition: background 0.2s;
        }

        .info-card:hover {
            background: #f1f5f9;
        }

        .info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .info-value {
            font-weight: 700;
            font-size: 1.125rem;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-dot {
            height: 10px;
            width: 10px;
            background-color: #22c55e;
            border-radius: 50%;
            display: inline-block;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Sections */
        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 40px 0 20px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #1e293b;
        }

        .section-title::before {
            content: '';
            width: 4px;
            height: 24px;
            background: linear-gradient(180deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 2px;
        }

        /* Table Styles */
        .table-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
        }

        .card-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        .card-table thead {
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        }

        .card-table th {
            text-align: left;
            padding: 16px 20px;
            border-bottom: 2px solid #e2e8f0;
            color: #475569;
            text-transform: uppercase;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.05em;
        }

        .card-table td {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
        }

        .card-table tbody tr {
            transition: background 0.2s;
        }

        .card-table tbody tr:hover {
            background: #f8fafc;
        }

        .card-table tbody tr:last-child td {
            border-bottom: none;
        }

        .badge {
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .badge-user {
            background: #dcfce7;
            color: #166534;
        }

        .badge-admin {
            background: #dbeafe;
            color: #1e40af;
        }

        .action-link {
            color: #2563eb;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }

        .action-link:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }

        /* Logs Container */
        .log-container {
            background: #1e293b;
            color: #cbd5e1;
            padding: 20px;
            border-radius: 12px;
            font-family: 'Monaco', 'Courier New', monospace;
            font-size: 0.875rem;
            line-height: 1.8;
            max-height: 400px;
            overflow-y: auto;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .log-container::-webkit-scrollbar {
            width: 8px;
        }

        .log-container::-webkit-scrollbar-track {
            background: #0f172a;
        }

        .log-container::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 4px;
        }

        .log-entry {
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid #334155;
        }

        .log-entry:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .log-time {
            color: #94a3b8;
            margin-right: 12px;
            font-weight: 600;
        }

        .log-success { color: #4ade80; font-weight: 600; }
        .log-error { color: #f87171; font-weight: 600; }
        .log-info { color: #38bdf8; font-weight: 600; }
        .log-system { color: #a78bfa; font-weight: 600; }

        /* Footer */
        .admin-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #94a3b8;
            font-size: 0.875rem;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #64748b;
        }

        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .admin-container {
                padding: 12px;
            }

            .admin-warning {
                flex-direction: column;
                gap: 16px;
                text-align: center;
            }

            .admin-header {
                flex-direction: column;
                text-align: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .card-table {
                font-size: 0.75rem;
            }

            .card-table th,
            .card-table td {
                padding: 12px;
            }

            .section-title {
                font-size: 1.125rem;
            }
        }

        /* Accessibility */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        
        {{-- Warning Banner --}}
        <div class="admin-warning" role="alert" aria-live="polite">
            <div>
                <strong>⚠️ Super Admin Mode</strong>
                <small>Changes affect the live system database. Proceed with caution.</small>
            </div>
            <form method="GET" action="{{ route('blade.admin.logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="logout-btn" aria-label="Logout from admin dashboard">
                    Exit Admin
                </button>
            </form>
        </div>

        {{-- Profile Header --}}
        <header class="admin-header">
            <div class="avatar-circle" aria-hidden="true">
                {{ strtoupper(substr(auth()->user()->name ?? auth()->user()->email, 0, 1)) }}
            </div>
            <div style="flex: 1;">
                <h2>Welcome, {{ auth()->user()->name ?? auth()->user()->username ?? 'Admin' }}</h2>
                <p>
                    Level: System Overseer • Session: {{ now()->format('H:i') }} UTC
                </p>
            </div>
        </header>

        {{-- Statistics Cards --}}
        <section class="stats-grid" aria-label="Dashboard Statistics">
            <div class="stat-card">
                <div class="stat-label">Total Users</div>
                <div class="stat-value">
                    <span class="stat-icon icon-users">👥</span>
                    {{ number_format($stats['total_users']) }}
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Admin Users</div>
                <div class="stat-value">
                    <span class="stat-icon icon-admin">👑</span>
                    {{ number_format($stats['admins']) }}
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Active Users</div>
                <div class="stat-value">
                    <span class="stat-icon icon-active">✓</span>
                    {{ number_format($stats['active_users']) }}
                </div>
            </div>
        </section>

        {{-- System Info Grid --}}
        <section class="admin-grid" aria-label="System Information">
            <div class="info-card">
                <div class="info-label">System Health</div>
                <div class="info-value">
                    <span class="status-dot" aria-label="System is online"></span>
                    Online
                </div>
            </div>
            <div class="info-card">
                <div class="info-label">Environment</div>
                <div class="info-value">{{ ucfirst(app()->environment()) }}</div>
            </div>
            <div class="info-card">
                <div class="info-label">IP Address</div>
                <div class="info-value">{{ request()->ip() }}</div>
            </div>
        </section>

        {{-- User Management Table --}}
        <h3 class="section-title">User Management</h3>
        <div class="table-container">
            @if($users->isNotEmpty())
                <table class="card-table" role="table" aria-label="User management table">
                    <thead>
                        <tr>
                            <th scope="col">User</th>
                            <th scope="col">Email</th>
                            <th scope="col">Role</th>
                            <th scope="col">Joined</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    <strong>{{ $user->name ?? $user->username ?? 'N/A' }}</strong>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge {{ $user->role === 'admin' ? 'badge-admin' : 'badge-user' }}">
                                        {{ ucfirst($user->role ?? 'user') }}
                                    </span>
                                </td>
                                <td>
                                    <time datetime="{{ $user->created_at->toIso8601String() }}">
                                        {{ $user->created_at->format('M d, Y') }}
                                    </time>
                                </td>
                                <td>
                                    <a href="{{ url('/blade-admin/users/' . $user->id . '/edit') }}" 
                                       class="action-link"
                                       aria-label="Edit user {{ $user->name ?? $user->email }}">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">📭</div>
                    <p>No users found in the system.</p>
                </div>
            @endif
        </div>

        {{-- System Activity Logs --}}
        {{-- System Activity Logs --}}
<h3 class="section-title">System Activity Logs</h3>
<div class="log-container" role="log" aria-label="System activity logs">
    @forelse($logs as $log)
        <div class="log-entry">
            <span class="log-time">
                [{{ is_string($log['time']) ? \Carbon\Carbon::parse($log['time'])->format('H:i:s') : $log['time']->format('H:i:s') }}]
            </span>

            @if($log['type'] === 'success')
                <span class="log-success">SUCCESS:</span>
            @elseif($log['type'] === 'error')
                <span class="log-error">ERROR:</span>
            @elseif($log['type'] === 'info')
                <span class="log-info">INFO:</span>
            @else
                <span class="log-system">SYSTEM:</span>
            @endif

            {{ $log['message'] }}
        </div>
    @empty
        <div class="log-entry">
            <span class="log-system">SYSTEM:</span> No recent activity detected.
        </div>
    @endforelse
</div>

        {{-- Footer --}}
        <footer class="admin-footer">
            <small>
                &copy; {{ date('Y') }} {{ config('app.name', 'Curriculum Admin System') }} • v1.0.5
                <span aria-hidden="true">•</span>
                Powered by Laravel {{ app()->version() }}
            </small>
        </footer>

    </div>
</body>
</html>