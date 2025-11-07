<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Nyholm\Psr7\Factory\Psr17Factory;
use Slim\App as SlimApp;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$basePath = dirname(__DIR__);

if (file_exists($basePath . '/.env')) {
    Dotenv::createImmutable($basePath)->safeLoad();
} else {
    Dotenv::createImmutable($basePath, '.env.example')->safeLoad();
}

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(require __DIR__ . '/container.php');
$container = $containerBuilder->build();

global $appContainer;
$appContainer = $container;

$psr17Factory = new Psr17Factory();
AppFactory::setContainer($container);
AppFactory::setResponseFactory($psr17Factory);
/** @var SlimApp $app */
$app = AppFactory::create();

(require __DIR__ . '/middleware/middleware.php')($app);
(require __DIR__ . '/routes/web.php')($app);
(require __DIR__ . '/routes/api.php')($app);

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

$settings = $container->get('settings.app');
$errorMiddleware = $app->addErrorMiddleware((bool) ($settings['debug'] ?? false), true, true);
$errorMiddleware->setDefaultErrorHandler(
    new \App\Support\Handlers\DefaultErrorHandler(
        $app->getCallableResolver(),
        $app->getResponseFactory(),
        $settings['debug'] ?? false
    )
);

return $app;
