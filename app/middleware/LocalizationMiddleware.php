<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Services\I18n\I18nService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LocalizationMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly I18nService $translator)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $locale = $this->resolveLocale($request);
        $this->translator->setLocale($locale);

        return $handler->handle($request->withAttribute('locale', $locale));
    }

    private function resolveLocale(ServerRequestInterface $request): string
    {
        $available = $this->translator->getAvailableLocales();

        $sessionLocale = $_SESSION['locale'] ?? null;
        if ($sessionLocale && in_array($sessionLocale, $available, true)) {
            return $sessionLocale;
        }

        $cookieLocale = $request->getCookieParams()['locale'] ?? null;
        if ($cookieLocale && in_array($cookieLocale, $available, true)) {
            $_SESSION['locale'] = $cookieLocale;
            return $cookieLocale;
        }

        $headerLocales = $request->getHeaderLine('Accept-Language');
        if ($headerLocales) {
            $parsed = $this->parseAcceptLanguage($headerLocales);
            foreach ($parsed as $candidate) {
                if (in_array($candidate, $available, true)) {
                    $_SESSION['locale'] = $candidate;
                    return $candidate;
                }
            }
        }

        $fallback = $available[0] ?? 'en';
        $_SESSION['locale'] = $fallback;

        return $fallback;
    }

    /**
     * @return list<string>
     */
    private function parseAcceptLanguage(string $header): array
    {
        $locales = [];
        foreach (explode(',', $header) as $segment) {
            $parts = explode(';', $segment);
            $locales[] = str_replace('-', '_', strtolower(trim($parts[0])));
        }

        return $locales;
    }
}
