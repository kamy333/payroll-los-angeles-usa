<?php

return [
    'name' => 'Household Payroll Web App',
    'env' => env('APP_ENV', 'development'),
    'debug' => (bool) env('APP_DEBUG', true),
    'url' => env('APP_URL', 'http://localhost:8080'),
    'locale' => env('APP_LOCALE', 'en'),
    'fallback_locale' => 'en',
    'available_locales' => ['en', 'es', 'pt_BR', 'fr', 'de', 'it'],
    'theme' => [
        'default' => env('APP_THEME', 'system'),
    ],
];
