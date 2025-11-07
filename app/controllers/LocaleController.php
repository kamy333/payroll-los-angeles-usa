<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\I18n\I18nService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LocaleController
{
    public function __construct(private readonly I18nService $translator)
    {
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $request->getParsedBody() ?? [];
        $locale = is_array($data) ? ($data['locale'] ?? '') : '';

        if (in_array($locale, $this->translator->getAvailableLocales(), true)) {
            $_SESSION['locale'] = $locale;
            $this->translator->setLocale($locale);
            $response = $response->withAddedHeader('Set-Cookie', sprintf('locale=%s; Path=/; Max-Age=%d; SameSite=Lax', $locale, 60 * 60 * 24 * 365));
        }

        $redirectTo = $this->determineRedirectTarget($request, $data);

        return $response
            ->withStatus(303)
            ->withHeader('Location', $redirectTo);
    }

    private function determineRedirectTarget(ServerRequestInterface $request, array $data): string
    {
        $target = $data['redirect_to'] ?? $request->getHeaderLine('Referer');
        if (is_string($target) && str_starts_with($target, '/')) {
            return $target;
        }

        if (is_string($target) && str_starts_with($target, 'http')) {
            $parsed = parse_url($target);
            if (!empty($parsed['path'])) {
                return $parsed['path'];
            }
        }

        return '/';
    }
}
