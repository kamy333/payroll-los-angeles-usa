<?php

declare(strict_types=1);

use App\Middleware\LocalizationMiddleware;
use App\Middleware\SessionMiddleware;
use App\Middleware\ThemeMiddleware;
use App\Repositories\I18nRepository;
use App\Services\I18n\I18nService;
use App\View\ViewRenderer;
use Psr\Container\ContainerInterface;
use function DI\autowire;

return [
    'settings.app' => static fn (): array => require __DIR__ . '/../config/app.php',
    'settings.db' => static fn (): array => require __DIR__ . '/../config/db.php',

    \PDO::class => static function (ContainerInterface $container): \PDO {
        $config = $container->get('settings.db');

        $dsn = sprintf(
            '%s:host=%s;port=%s;dbname=%s;charset=%s',
            $config['driver'],
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset']
        );

        $pdo = new \PDO($dsn, $config['username'], $config['password'] ?? '', $config['options'] ?? []);
        if (!empty($config['collation'])) {
            $pdo->exec(sprintf('SET NAMES %s COLLATE %s', $config['charset'], $config['collation']));
        }

        return $pdo;
    },

    I18nRepository::class => static fn (ContainerInterface $container): I18nRepository => new I18nRepository($container->get(\PDO::class)),

    I18nService::class => static function (ContainerInterface $container): I18nService {
        $settings = $container->get('settings.app');

        return new I18nService(
            $container->get(I18nRepository::class),
            $settings['available_locales'],
            $settings['fallback_locale'],
            $settings['locale'] ?? 'en'
        );
    },

    ViewRenderer::class => static function (ContainerInterface $container): ViewRenderer {
        $settings = $container->get('settings.app');

        return new ViewRenderer(
            __DIR__ . '/../resources/views',
            $container->get(I18nService::class),
            $settings
        );
    },

    SessionMiddleware::class => autowire(),
    LocalizationMiddleware::class => autowire(),
    ThemeMiddleware::class => autowire(),
];
