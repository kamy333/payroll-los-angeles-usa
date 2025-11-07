<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ThemeMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $theme = $request->getCookieParams()['theme'] ?? ($_SESSION['theme'] ?? 'system');
        $_SESSION['theme'] = $theme;

        return $handler->handle($request->withAttribute('theme', $theme));
    }
}
