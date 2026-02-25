<?php

return [
    'frontend_url' => env('FRONTEND_URL', 'http://localhost:3000'),

    'cors' => [
        'allowed_origins' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('CORS_ALLOWED_ORIGINS', 'http://localhost:3000,http://127.0.0.1:3000'))
        ))),
    ],

    'auth' => [
        'token_expiration' => (int) env('SANCTUM_TOKEN_EXPIRATION', 10080),
    ],

    'logging' => [
        'security_level' => env('LOG_SECURITY_LEVEL', 'info'),
        'security_days' => (int) env('LOG_SECURITY_DAYS', 30),
    ],
];
