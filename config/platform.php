<?php

declare(strict_types=1);

return [
    'api_key' => env('PLATFORM_API_KEY'),

    // No production deployment exists yet — set PLATFORM_BASE_URL explicitly
    // (e.g. http://localhost:3000 to target a local dev server).
    'base_url' => env('PLATFORM_BASE_URL'),

    'timeout_ms' => env('PLATFORM_TIMEOUT_MS', 10000),
];
