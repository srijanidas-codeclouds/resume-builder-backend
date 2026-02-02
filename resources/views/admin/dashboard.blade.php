<style>
    .admin-container {
        font-family: system-ui, -apple-system, sans-serif;
        max-width: 900px;
        margin: 20px auto;
        color: #334155;
    }

    /* Warning Banner */
    .admin-warning {
        background-color: #fffbeb;
        border: 1px solid #fde68a;
        color: #92400e;
        padding: 16px;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    /* Profile Header */
    .admin-header {
        background: #ffffff;
        padding: 24px;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .avatar-circle {
        width: 50px;
        height: 50px;
        background: #2563eb;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.2rem;
    }

    /* Logout Button */
    .logout-btn {
        background: #ffffff;
        border: 1px solid #f87171;
        color: #dc2626;
        padding: 8px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.2s;
    }

    .logout-btn:hover {
        background: #fef2f2;
    }

    /* Info Grid */
    .admin-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }

    .info-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 15px;
        border-radius: 10px;
    }

    .info-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.05em;
        margin-bottom: 5px;
    }

    .info-value {
        font-weight: bold;
        font-size: 1.1rem;
    }

    .status-dot {
        height: 8px;
        width: 8px;
        background-color: #22c55e;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }

    /* Sections */
    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin: 30px 0 15px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Table Styles */
    .card-table {
        width: 100%;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        border-collapse: collapse;
        overflow: hidden;
        font-size: 0.9rem;
    }

    .card-table th {
        background: #f8fafc;
        text-align: left;
        padding: 12px 15px;
        border-bottom: 1px solid #e2e8f0;
        color: #64748b;
        text-transform: uppercase;
        font-size: 0.75rem;
    }

    .card-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #f1f5f9;
    }

    .badge {
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .badge-user { background: #dcfce7; color: #166534; }
    .badge-admin { background: #dbeafe; color: #1e40af; }

    /* Logs List */
    .log-container {
        background: #1e293b;
        color: #cbd5e1;
        padding: 15px;
        border-radius: 10px;
        font-family: 'Courier New', monospace;
        font-size: 0.85rem;
        line-height: 1.6;
        max-height: 300px;
        overflow-y: auto;
    }

    .log-entry { margin-bottom: 5px; border-bottom: 1px solid #334155; padding-bottom: 5px; }
    .log-time { color: #94a3b8; margin-right: 10px; }
    .log-success { color: #4ade80; }
    .log-error { color: #f87171; }
</style>

<div class="admin-container">
    
    <div class="admin-warning">
        <div>
            <strong style="display: block;">⚠️ Super Admin Mode</strong>
            <small>Changes affect the live system database. Proceed with caution.</small>
        </div>
        <form method="POST" action="/blade-admin/logout" style="margin: 0;">
            @csrf
            <button type="submit" class="logout-btn">Exit Admin</button>
        </form>
    </div>

    <div class="admin-header">
        <div class="avatar-circle">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
        <div>
            <h2 style="margin: 0; font-size: 1.25rem;">Welcome, {{ auth()->user()->name }}</h2>
            <p style="margin: 0; font-size: 0.85rem; color: #64748b;">
                Level: System Overseer • Session: {{ now()->format('H:i') }} UTC
            </p>
        </div>
    </div>

    <div class="admin-grid">
        <div class="info-card">
            <div class="info-label">System Health</div>
            <div class="info-value">
                <span class="status-dot"></span> Online
            </div>
        </div>
        <div class="info-card">
            <div class="info-label">Environment</div>
            <div class="info-value">{{ app()->environment() }}</div>
        </div>
        <div class="info-card">
            <div class="info-label">IP Address</div>
            <div class="info-value">{{ request()->ip() }}</div>
        </div>
    </div>

    <div class="section-title">👤 User Management</div>
    <div style="overflow-x: auto;">
        <table class="card-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
@foreach($users as $user)
<tr>
    <td><strong>{{ $user->name }}</strong></td>
    <td>{{ $user->email }}</td>
    <td>
        <span class="badge {{ $user->role === 'admin' ? 'badge-admin' : 'badge-user' }}">
            {{ ucfirst($user->role) }}
        </span>
    </td>
    <td>{{ $user->created_at->format('Y-m-d') }}</td>
    <td>
        <a href="{{ url('/blade-admin/users/'.$user->id.'/edit') }}"
           style="color:#2563eb;font-weight:600;">
           Edit
        </a>
    </td>
</tr>
@endforeach
</tbody>

        </table>
    </div>

    <div class="section-title">📜 System Activity Logs</div>
    <div class="log-container">
    @forelse($logs as $log)
        <div class="log-entry">
            <span class="log-time">
                [{{ $log['time']->format('H:i:s') }}]
            </span>

            @if($log['type'] === 'success')
                <span class="log-success">SUCCESS:</span>
            @elseif($log['type'] === 'error')
                <span class="log-error">ERROR:</span>
            @elseif($log['type'] === 'info')
                <span class="log-success">INFO:</span>
            @else
                <span>SYSTEM:</span>
            @endif

            {{ $log['message'] }}
        </div>
    @empty
        <div class="log-entry">
            <span>SYSTEM:</span> No recent activity detected.
        </div>
    @endforelse
</div>

    <div style="margin-top: 20px; text-align: center;">
        <small style="color: #94a3b8;">&copy; {{ date('Y') }} Curriculum Admin System • v1.0.4</small>
    </div>

</div>