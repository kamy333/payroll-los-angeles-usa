<?php

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Controllers\LocaleController;
use App\Controllers\ThemeController;
use Slim\App;

return static function (App $app): void {
    $app->get('/', [HomeController::class, 'index'])->setName('home');
    $app->post('/locale', [LocaleController::class, 'update'])->setName('locale.update');
    $app->post('/theme', [ThemeController::class, 'update'])->setName('theme.update');
};
