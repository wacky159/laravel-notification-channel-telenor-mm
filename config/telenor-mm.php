<?php

return [
    'client_id' => env('TELENOR_MM_CLIENT_ID'),
    'client_secret' => env('TELENOR_MM_CLIENT_SECRET'),
    'api_url' => env('TELENOR_MM_API_URL', 'https://prod-apigw.atom.com.mm'),
    'callback_url' => env('TELENOR_MM_CALLBACK_URL', ''),
    'cache' => [
        'token_ttl' => env('TELENOR_MM_TOKEN_TTL', 3600),
        'auth_code_ttl' => env('TELENOR_MM_AUTH_CODE_TTL', 86400),
    ],
    'retry' => [
        'max_attempts' => env('TELENOR_MM_MAX_RETRY', 3),
        'delay' => env('TELENOR_MM_RETRY_DELAY', 1),
    ],
    'log' => [
        'enabled' => env('TELENOR_MM_LOG_ENABLED', false),
        'channel' => env('TELENOR_MM_LOG_CHANNEL', 'stack'),
    ],
];
