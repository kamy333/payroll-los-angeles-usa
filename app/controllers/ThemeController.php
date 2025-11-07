<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ThemeController
{
    private const SUPPORTED = ['light', 'dark', 'system'];

    public function update(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $request->getParsedBody() ?? [];
        $theme = is_array($data) ? ($data['theme'] ?? 'system') : 'system';

        if (!in_array($theme, self::SUPPORTED, true)) {
            $theme = 'system';
        }

        $_SESSION['theme'] = $theme;

        $cookieHeader = sprintf('theme=%s; Path=/; Max-Age=%d; SameSite=Lax', $theme, 60 * 60 * 24 * 365);
        $response = $response->withAddedHeader('Set-Cookie', $cookieHeader);

        $wantsJson = str_contains($request->getHeaderLine('Accept'), 'application/json')
            || strtolower($request->getHeaderLine('X-Requested-With')) === 'xmlhttprequest';

        if ($wantsJson) {
            $response->getBody()->write(json_encode(['status' => 'ok', 'theme' => $theme], JSON_THROW_ON_ERROR));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $redirectTo = $data['redirect_to'] ?? $request->getHeaderLine('Referer') ?: '/';

        if (is_string($redirectTo) && !str_starts_with($redirectTo, '/')) {
            $uri = parse_url($redirectTo);
            $redirectTo = $uri['path'] ?? '/';
        }

        return $response->withStatus(303)->withHeader('Location', $redirectTo);
    }
}
