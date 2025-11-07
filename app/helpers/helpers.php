<?php

use App\Services\I18n\I18nService;

if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }
}

if (!function_exists('__t')) {
    function __t(string $key, array $replacements = [], ?string $locale = null): string
    {
        static $translator;

        if ($translator === null) {
            $translator = app(I18nService::class);
        }

        return $translator->translate($key, $replacements, $locale);
    }
}

if (!function_exists('app')) {
    function app(string $abstract)
    {
        global $appContainer;
        if (!$appContainer) {
            throw new RuntimeException('Application container has not been initialized.');
        }

        return $appContainer->get($abstract);
    }
}
