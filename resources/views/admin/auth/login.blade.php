<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin Login - {{ config('app.name', 'Laravel') }}</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            line-height: 1.6;
        }

        /* Layout Container */
        .login-page {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Animated Background Elements */
        .login-page::before,
        .login-page::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 20s infinite ease-in-out;
        }

        .login-page::before {
            width: 400px;
            height: 400px;
            top: -100px;
            right: -100px;
            animation-delay: 0s;
        }

        .login-page::after {
            width: 300px;
            height: 300px;
            bottom: -50px;
            left: -50px;
            animation-delay: 5s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) scale(1);
            }
            50% {
                transform: translateY(-30px) scale(1.1);
            }
        }

        /* Glass-style Login Card */
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            width: 100%;
            max-width: 440px;
            padding: 48px;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            z-index: 1;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: 36px;
        }

        .logo-container {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.75rem;
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .login-header h1 {
            font-size: 1.75rem;
            color: #1e293b;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .login-header p {
            color: #64748b;
            font-size: 0.9375rem;
        }

        /* Success Message */
        .success-message {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border-left: 4px solid #10b981;
            padding: 16px;
            margin-bottom: 24px;
            border-radius: 8px;
            color: #065f46;
            font-size: 0.875rem;
            font-weight: 500;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Error Handling */
        .error-container {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border-left: 4px solid #ef4444;
            padding: 16px;
            margin-bottom: 24px;
            border-radius: 8px;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .error-container ul {
            margin: 0;
            padding-left: 24px;
            color: #991b1b;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .error-container li {
            margin: 4px 0;
        }

        /* Form Styling */
        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        input[type="text"], 
        input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            background-color: #f8fafc;
            font-size: 1rem;
            transition: all 0.2s ease;
            color: #1e293b;
        }

        input[type="text"]:focus, 
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        input[type="text"]::placeholder,
        input[type="password"]::placeholder {
            color: #94a3b8;
        }

        /* Input Icons */
        .input-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.25rem;
            pointer-events: none;
        }

        /* Submit Button */
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 12px;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.5);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Security Note */
        .security-note {
            text-align: center;
            margin-top: 28px;
            font-size: 0.8125rem;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .security-icon {
            font-size: 1rem;
        }

        /* Loading State */
        .loading {
            pointer-events: none;
        }

        .loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.6s linear infinite;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
        }

        @keyframes spin {
            to { transform: translateY(-50%) rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .login-card {
                padding: 32px 24px;
            }

            .login-header h1 {
                font-size: 1.5rem;
            }

            .logo-container {
                width: 56px;
                height: 56px;
                font-size: 1.5rem;
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

        /* Dark mode support (optional) */
        @media (prefers-color-scheme: dark) {
            .login-card {
                background: rgba(30, 41, 59, 0.95);
            }

            .login-header h1 {
                color: #f1f5f9;
            }

            .login-header p {
                color: #cbd5e1;
            }

            input[type="text"], 
            input[type="password"] {
                background-color: #1e293b;
                border-color: #334155;
                color: #f1f5f9;
            }

            input[type="text"]:focus, 
            input[type="password"]:focus {
                background-color: #0f172a;
            }

            .form-group label {
                color: #cbd5e1;
            }
        }
    </style>
</head>
<body>
    <div class="login-page">
        <main class="login-card">
            <div class="login-header">
                <div class="logo-container" aria-hidden="true">
                    {{ strtoupper(substr(config('app.name', 'C'), 0, 1)) }}
                </div>
                <h1>Admin Login</h1>
                <p>Enter your credentials to access the system</p>
            </div>

            {{-- Success Message --}}
            @if (session('status'))
                <div class="success-message" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Error Messages --}}
            @if ($errors->any())
                <div class="error-container" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('blade.admin.login.submit') }}" id="loginForm">
                @csrf

                <div class="form-group">
                    <label for="login">Username or Email</label>
                    <div class="input-wrapper">
                        <input 
                            type="text" 
                            id="login" 
                            name="login" 
                            placeholder="Enter your username or email" 
                            value="{{ old('login') }}" 
                            required 
                            autofocus 
                            autocomplete="username"
                            aria-describedby="{{ $errors->has('login') ? 'login-error' : '' }}"
                            aria-invalid="{{ $errors->has('login') ? 'true' : 'false' }}"
                        >
                        <span class="input-icon" aria-hidden="true">👤</span>
                    </div>
                    @error('login')
                        <span class="sr-only" id="login-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="Enter your password" 
                            required
                            autocomplete="current-password"
                            aria-describedby="{{ $errors->has('password') ? 'password-error' : '' }}"
                            aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                        >
                        <span class="input-icon" aria-hidden="true">🔒</span>
                    </div>
                    @error('password')
                        <span class="sr-only" id="password-error">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn-login" id="submitBtn">
                    Sign In to Dashboard
                </button>
            </form>

            <div class="security-note">
                <span class="security-icon" aria-hidden="true">🛡️</span>
                Secure, encrypted administrator session
            </div>
        </main>
    </div>

    <script>
        // Form submission loading state
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.textContent = 'Signing in...';
            btn.classList.add('loading');
        });

        // Auto-focus on input if there's an error
        @if ($errors->has('login'))
            document.getElementById('login').focus();
        @elseif ($errors->has('password'))
            document.getElementById('password').focus();
        @endif
    </script>
</body>
</html>