<style>
    /* Layout Container */
    .login-page {
        background: #f8fafc;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        padding: 20px;
    }

    /* Glass-style Login Card */
    .login-card {
        background: white;
        width: 100%;
        max-width: 400px;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
    }

    .login-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .login-header h1 {
        font-size: 1.5rem;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .login-header p {
        color: #64748b;
        font-size: 0.9rem;
    }

    /* Form Styling */
    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 8px;
    }

    input[type="text"], 
    input[type="password"] {
        width: 100%;
        padding: 12px 16px;
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        background-color: #fcfcfd;
        font-size: 1rem;
        transition: all 0.2s;
        box-sizing: border-box; /* Crucial for width */
    }

    input:focus {
        outline: none;
        border-color: #2563eb;
        background-color: #fff;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }

    /* Submit Button */
    .btn-login {
        width: 100%;
        background: #2563eb;
        color: white;
        padding: 14px;
        border: none;
        border-radius: 10px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
        margin-top: 10px;
    }

    .btn-login:hover {
        background: #1d4ed8;
    }

    /* Error Handling */
    .error-container {
        background: #fef2f2;
        border-left: 4px solid #ef4444;
        padding: 12px;
        margin-bottom: 20px;
        border-radius: 4px;
    }

    .error-container ul {
        margin: 0;
        padding-left: 20px;
        color: #b91c1c;
        font-size: 0.85rem;
    }

    .security-note {
        text-align: center;
        margin-top: 25px;
        font-size: 0.75rem;
        color: #94a3b8;
    }
</style>

<div class="login-page">
    <div class="login-card">
        <div class="login-header">
            <div style="width: 40px; height: 40px; background: #2563eb; border-radius: 10px; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                C
            </div>
            <h1>Admin Login</h1>
            <p>Enter your credentials to access the system.</p>
        </div>

        @if ($errors->any())
            <div class="error-container">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('blade.admin.login.submit') }}">
            @csrf

            <div class="form-group">
                <label for="login">Username or Email</label>
                <input type="text" id="login" name="login" placeholder="admin@example.com" value="{{ old('login') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-login">Sign In to Dashboard</button>
        </form>

        <div class="security-note">
            🛡️ Secure, encrypted administrator session.
        </div>
    </div>
</div>