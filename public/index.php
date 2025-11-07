<?php

declare(strict_types=1);

$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$staticFile = __DIR__ . $uriPath;

if (PHP_SAPI === 'cli-server' && $uriPath !== false && is_file($staticFile)) {
    return false;
}

$app = require __DIR__ . '/../app/bootstrap.php';

$app->run();
