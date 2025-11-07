<?php
/** @var callable $t */
/** @var string $appName */
/** @var string $locale */
/** @var array $locales */
/** @var string $theme */
/** @var string $content */
/** @var array $translations */
/** @var array $appSettings */

$metaTitleKey = $pageTitle ?? 'app.title';
$title = $t($metaTitleKey);
$availableLocales = array_map(static fn (string $loc) => [
    'value' => $loc,
    'label' => $t('locale.' . $loc),
], $locales);
$translationsJson = json_encode($translations, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
$localesJson = json_encode($availableLocales, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
$initialTheme = $theme ?? 'system';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(str_replace('_', '-', $locale), ENT_QUOTES, 'UTF-8') ?>" data-theme="<?= htmlspecialchars($initialTheme, ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title . ' · ' . $appName, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/assets/app.css">
    <script>
        (function () {
            const stored = localStorage.getItem('theme-preference') ?? 'system';
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = stored === 'system' ? (prefersDark ? 'dark' : 'light') : stored;
            document.documentElement.dataset.theme = theme;
            document.documentElement.dataset.mode = stored;
        })();
    </script>
</head>
<body class="min-h-screen bg-surface text-foreground" x-data="appShell({
        meta: {
            currentLocale: '<?= htmlspecialchars($locale, ENT_QUOTES, 'UTF-8') ?>',
            availableLocales: <?= $localesJson ?>,
            theme: '<?= htmlspecialchars($initialTheme, ENT_QUOTES, 'UTF-8') ?>'
        },
        translations: <?= $translationsJson ?>
    })" x-init="init()">
<div x-data="sidebar()" class="flex min-h-screen overflow-hidden lg:overflow-visible">
    <div class="fixed inset-0 z-30 bg-black/40 lg:hidden" x-cloak x-show="open && isMobile" x-transition.opacity aria-hidden="true" @click="backdropClick()"></div>
    <aside x-ref="drawer" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="sidebar-label"
           class="fixed inset-y-0 left-0 z-40 w-72 transform bg-elevated shadow-xl ring-1 ring-black/5 transition duration-200 ease-in-out lg:static lg:translate-x-0"
           :class="{'-translate-x-full': isMobile && !open, 'translate-x-0': open}">
        <div class="flex h-full flex-col gap-6 px-6 py-6">
            <div class="flex items-center justify-between">
                <a href="/" class="flex items-center gap-3 text-lg font-semibold">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-primary/10 text-primary">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5"><path d="M12 2 2 7l10 5 10-5-10-5Zm0 7L2 4v13l10 5 10-5V4l-10 5Z" /></svg>
                    </span>
                    <span id="sidebar-label"><?= htmlspecialchars($t('app.brand'), ENT_QUOTES, 'UTF-8') ?></span>
                </a>
                <button type="button" x-ref="closeButton" class="rounded-full p-2 text-muted hover:bg-muted/40 focus:outline-none focus-visible:ring lg:hidden" @click="close()" aria-label="<?= htmlspecialchars($t('layout.close_menu'), ENT_QUOTES, 'UTF-8') ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m6 6 12 12M18 6 6 18" />
                    </svg>
                </button>
            </div>
            <nav class="flex-1 space-y-8 overflow-y-auto pb-10">
                <div>
                    <p class="px-2 text-xs font-semibold uppercase tracking-wide text-muted"><?= htmlspecialchars($t('nav.employee_header'), ENT_QUOTES, 'UTF-8') ?></p>
                    <ul class="mt-3 space-y-1">
                        <li><a href="#" class="nav-link"><?= htmlspecialchars($t('nav.employee_dashboard'), ENT_QUOTES, 'UTF-8') ?></a></li>
                        <li><a href="#" class="nav-link"><?= htmlspecialchars($t('nav.employee_hours'), ENT_QUOTES, 'UTF-8') ?></a></li>
                        <li><a href="#" class="nav-link"><?= htmlspecialchars($t('nav.employee_submit'), ENT_QUOTES, 'UTF-8') ?></a></li>
                        <li><a href="#" class="nav-link"><?= htmlspecialchars($t('nav.employee_payslips'), ENT_QUOTES, 'UTF-8') ?></a></li>
                    </ul>
                </div>
                <div>
                    <p class="px-2 text-xs font-semibold uppercase tracking-wide text-muted"><?= htmlspecialchars($t('nav.admin_header'), ENT_QUOTES, 'UTF-8') ?></p>
                    <ul class="mt-3 space-y-1">
                        <li><a href="#" class="nav-link"><?= htmlspecialchars($t('nav.admin_approvals'), ENT_QUOTES, 'UTF-8') ?></a></li>
                        <li><a href="#" class="nav-link"><?= htmlspecialchars($t('nav.admin_employees'), ENT_QUOTES, 'UTF-8') ?></a></li>
                        <li><a href="#" class="nav-link"><?= htmlspecialchars($t('nav.admin_payruns'), ENT_QUOTES, 'UTF-8') ?></a></li>
                        <li><a href="#" class="nav-link"><?= htmlspecialchars($t('nav.admin_reports'), ENT_QUOTES, 'UTF-8') ?></a></li>
                        <li><a href="#" class="nav-link"><?= htmlspecialchars($t('nav.admin_config'), ENT_QUOTES, 'UTF-8') ?></a></li>
                        <li><a href="#" class="nav-link"><?= htmlspecialchars($t('nav.admin_forms'), ENT_QUOTES, 'UTF-8') ?></a></li>
                        <li><a href="#" class="nav-link"><?= htmlspecialchars($t('nav.admin_audit'), ENT_QUOTES, 'UTF-8') ?></a></li>
                    </ul>
                </div>
            </nav>
            <div class="space-y-2 rounded-lg bg-muted/40 p-4 text-sm text-muted">
                <p class="font-medium text-foreground"><?= htmlspecialchars($t('sidebar.tip_title'), ENT_QUOTES, 'UTF-8') ?></p>
                <p><?= htmlspecialchars($t('sidebar.tip_body'), ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </div>
    </aside>
    <div class="flex flex-1 flex-col lg:pl-72">
        <header class="sticky top-0 z-20 border-b border-border/70 bg-surface/95 backdrop-blur">
            <div class="flex h-16 items-center gap-3 px-4 sm:px-6">
                <button type="button" x-ref="toggleButton" class="rounded-lg p-2 text-muted hover:bg-muted/40 focus:outline-none focus-visible:ring lg:hidden" @click="toggle()" :aria-expanded="open.toString()" aria-label="<?= htmlspecialchars($t('layout.open_menu'), ENT_QUOTES, 'UTF-8') ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                <div class="flex-1">
                    <h1 class="text-base font-semibold leading-tight text-foreground"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>
                    <p class="text-xs text-muted"><?= htmlspecialchars($t('dashboard.subtitle'), ENT_QUOTES, 'UTF-8') ?></p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex sm:items-center sm:gap-2">
                        <label for="language-select" class="text-xs font-medium uppercase tracking-wide text-muted hidden md:inline"><?= htmlspecialchars($t('topbar.language_label'), ENT_QUOTES, 'UTF-8') ?></label>
                        <form method="post" action="/locale" class="relative">
                            <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? '/', ENT_QUOTES, 'UTF-8') ?>">
                            <select id="language-select" name="locale" class="min-w-[110px] rounded-lg border border-border bg-surface px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/40" onchange="this.form.submit()">
                                <?php foreach ($availableLocales as $availableLocale): ?>
                                    <option value="<?= htmlspecialchars($availableLocale['value'], ENT_QUOTES, 'UTF-8') ?>" <?= $availableLocale['value'] === $locale ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($availableLocale['label'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>
                    <form method="post" action="/theme" class="flex items-center" x-data="themeSelect()" @submit.prevent>
                        <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? '/', ENT_QUOTES, 'UTF-8') ?>">
                        <label for="theme-select" class="sr-only"><?= htmlspecialchars($t('topbar.theme_toggle'), ENT_QUOTES, 'UTF-8') ?></label>
                        <select id="theme-select" name="theme" x-model="value" @change="change"
                                class="rounded-lg border border-border bg-surface px-3 py-2 text-sm font-medium text-foreground shadow-sm hover:bg-muted/40 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/40">
                            <option value="system"><?= htmlspecialchars($t('theme.system'), ENT_QUOTES, 'UTF-8') ?></option>
                            <option value="light"><?= htmlspecialchars($t('theme.light'), ENT_QUOTES, 'UTF-8') ?></option>
                            <option value="dark"><?= htmlspecialchars($t('theme.dark'), ENT_QUOTES, 'UTF-8') ?></option>
                        </select>
                    </form>
                </div>
            </div>
        </header>
        <main class="flex-1 overflow-y-auto bg-surface">
            <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-12">
                <?= $content ?>
            </div>
        </main>
    </div>
</div>
<script>
    window.APP_I18N = <?= $translationsJson ?>;
    window.APP_META = {
        locale: '<?= htmlspecialchars($locale, ENT_QUOTES, 'UTF-8') ?>',
        theme: '<?= htmlspecialchars($initialTheme, ENT_QUOTES, 'UTF-8') ?>'
    };
</script>
<script src="/assets/app.js" defer></script>
</body>
</html>
