<?php

declare(strict_types=1);

namespace App\View;

use App\Services\I18n\I18nService;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class ViewRenderer
{
    public function __construct(
        private readonly string $basePath,
        private readonly I18nService $translator,
        private readonly array $appSettings
    ) {
    }

    public function render(ResponseInterface $response, string $template, array $data = []): ResponseInterface
    {
        $shared = $this->sharedData();
        $content = $this->renderTemplate($template, array_merge($shared, $data));

        $response->getBody()->write(
            $this->renderTemplate('layouts/app.php', array_merge($shared, $data, [
                'content' => $content,
                'translations' => $this->translator->getClientMessages(),
            ]))
        );

        return $response;
    }

    private function sharedData(): array
    {
        return [
            'appName' => $this->appSettings['name'] ?? 'Household Payroll',
            'appSettings' => $this->appSettings,
            'locale' => $this->translator->getLocale(),
            'locales' => $this->translator->getAvailableLocales(),
            'theme' => $_SESSION['theme'] ?? 'system',
            't' => fn (string $key, array $replacements = [], ?string $locale = null) => $this->translator->translate($key, $replacements, $locale),
        ];
    }

    private function renderTemplate(string $template, array $data): string
    {
        $path = $this->resolvePath($template);

        if (!is_file($path)) {
            throw new RuntimeException(sprintf('View [%s] not found at path [%s]', $template, $path));
        }

        extract($data, EXTR_OVERWRITE);

        ob_start();
        include $path;

        return (string) ob_get_clean();
    }

    private function resolvePath(string $template): string
    {
        $template = str_replace(['\\', '..'], ['/', ''], $template);
        $path = rtrim($this->basePath, '/').'/'.ltrim($template, '/');

        if (!str_ends_with($path, '.php')) {
            $path .= '.php';
        }

        return $path;
    }
}
