<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // Leave this empty when using patterns
    'allowed_origins' => [],

    'allowed_origins_patterns' => [
        '^https:\/\/.*\.vercel\.app$',
    ],

    'allowed_headers' => [
        'Content-Type',
        'Authorization',
        'X-Requested-With',
        'Accept',
    ],

    'exposed_headers' => ['Authorization'],

    'max_age' => 0,

    'supports_credentials' => false,
];

