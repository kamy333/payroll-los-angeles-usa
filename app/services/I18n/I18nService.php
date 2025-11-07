<?php

declare(strict_types=1);

namespace App\Services\I18n;

use App\Repositories\I18nRepository;

class I18nService
{
    private I18nRepository $repository;
    private array $availableLocales;
    private string $fallbackLocale;
    private string $currentLocale;
    private array $cache = [];

    public function __construct(I18nRepository $repository, array $availableLocales, string $fallbackLocale, string $defaultLocale)
    {
        $this->repository = $repository;
        $this->availableLocales = $availableLocales;
        $this->fallbackLocale = $fallbackLocale;
        $this->currentLocale = $this->sanitizeLocale($defaultLocale);
    }

    public function setLocale(string $locale): void
    {
        $this->currentLocale = $this->sanitizeLocale($locale);
    }

    public function getLocale(): string
    {
        return $this->currentLocale;
    }

    public function translate(string $key, array $replacements = [], ?string $locale = null): string
    {
        $localeChain = array_filter([
            $locale ? $this->sanitizeLocale($locale) : $this->currentLocale,
            $this->fallbackLocale,
        ]);

        foreach ($localeChain as $candidate) {
            $translations = $this->loadTranslations($candidate);
            if (array_key_exists($key, $translations)) {
                return $this->replaceTokens($translations[$key], $replacements);
            }
        }

        return $this->replaceTokens($key, $replacements);
    }

    public function getAvailableLocales(): array
    {
        return $this->availableLocales;
    }

    public function getClientMessages(?string $locale = null): array
    {
        $localeToLoad = $locale ? $this->sanitizeLocale($locale) : $this->currentLocale;
        $messages = $this->loadTranslations($localeToLoad);

        if ($localeToLoad !== $this->fallbackLocale) {
            $messages = array_replace($this->loadTranslations($this->fallbackLocale), $messages);
        }

        return $messages;
    }

    private function loadTranslations(string $locale): array
    {
        if (!isset($this->cache[$locale])) {
            $this->cache[$locale] = $this->repository->translationsForLocale($locale);
        }

        return $this->cache[$locale];
    }

    private function replaceTokens(string $value, array $replacements): string
    {
        foreach ($replacements as $key => $replacement) {
            $value = str_replace(':' . $key, (string) $replacement, $value);
        }

        return $value;
    }

    private function sanitizeLocale(string $locale): string
    {
        if (in_array($locale, $this->availableLocales, true)) {
            return $locale;
        }

        return $this->fallbackLocale;
    }
}
