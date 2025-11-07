<?php

declare(strict_types=1);

use App\Middleware\LocalizationMiddleware;
use App\Middleware\SessionMiddleware;
use App\Middleware\ThemeMiddleware;
use Slim\App;

return static function (App $app): void {
    $container = $app->getContainer();

    $app->add($container->get(ThemeMiddleware::class));
    $app->add($container->get(LocalizationMiddleware::class));
    $app->add($container->get(SessionMiddleware::class));
};
