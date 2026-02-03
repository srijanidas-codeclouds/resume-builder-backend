<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin Frontend URL
    |--------------------------------------------------------------------------
    |
    | This URL is used to redirect users after logout from the admin panel.
    | Typically points to your React/Vue admin frontend.
    |
    */

    'admin_frontend_url' => env('ADMIN_FRONTEND_URL', 'http://localhost:5173/admin'),

    /*
    |--------------------------------------------------------------------------
    | Login Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Maximum number of login attempts before rate limiting kicks in.
    |
    */

    'max_login_attempts' => env('ADMIN_MAX_LOGIN_ATTEMPTS', 5),
    'lockout_duration' => env('ADMIN_LOCKOUT_DURATION', 60), // seconds

];
