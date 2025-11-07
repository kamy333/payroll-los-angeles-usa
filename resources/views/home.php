<?php
/** @var callable $t */
/** @var array $chunks */
?>
<section class="space-y-10">
    <div class="rounded-3xl bg-gradient-to-br from-primary/10 via-primary/5 to-transparent p-6 text-foreground shadow-sm ring-1 ring-primary/10 sm:p-12">
        <div class="max-w-2xl space-y-4">
            <h2 class="text-2xl font-semibold sm:text-3xl"><?= htmlspecialchars($t('dashboard.hero_title'), ENT_QUOTES, 'UTF-8') ?></h2>
            <p class="text-base text-muted sm:text-lg"><?= htmlspecialchars($t('dashboard.hero_body'), ENT_QUOTES, 'UTF-8') ?></p>
            <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                <a href="#" class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-primary px-5 py-3 text-sm font-semibold text-primary-foreground shadow-lg shadow-primary/25 transition hover:bg-primary/90 focus:outline-none focus-visible:ring sm:w-auto sm:justify-start">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="h-4 w-4"><path d="M6.75 5.25A2.25 2.25 0 0 1 9 3h6a2.25 2.25 0 0 1 2.25 2.25V4.5A2.25 2.25 0 0 1 19.5 6.75v6.75a2.25 2.25 0 0 1-2.25 2.25H15l.675 2.025a.75.75 0 0 1-.141.72l-1.5 1.875a.75.75 0 0 1-1.176 0l-1.5-1.875a.75.75 0 0 1-.141-.72L10.5 15H8.25A2.25 2.25 0 0 1 6 12.75V12a2.25 2.25 0 0 1-2.25-2.25V6.75A2.25 2.25 0 0 1 6 4.5v.75Z"/></svg>
                    <span><?= htmlspecialchars($t('dashboard.cta_track_time'), ENT_QUOTES, 'UTF-8') ?></span>
                </a>
                <a href="#" class="inline-flex w-full items-center justify-center gap-2 rounded-full border border-border px-5 py-3 text-sm font-semibold text-foreground transition hover:bg-muted/40 focus:outline-none focus-visible:ring sm:w-auto sm:justify-start">
                    <span><?= htmlspecialchars($t('dashboard.cta_view_reports'), ENT_QUOTES, 'UTF-8') ?></span>
                </a>
            </div>
        </div>
    </div>
    <div class="grid gap-6 lg:grid-cols-2">
        <?php foreach ($chunks as $chunk): ?>
            <section class="rounded-2xl border border-border bg-elevated/40 p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-muted"><?= htmlspecialchars($t($chunk['title']), ENT_QUOTES, 'UTF-8') ?></h3>
                    <span class="inline-flex items-center gap-2 text-xs text-muted">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 3.75 5.25 9l6 5.25m7.5-10.5-6 5.25 6 5.25m-6 5.25-6-5.25" />
                        </svg>
                        <?= htmlspecialchars($t('dashboard.keep_momentum'), ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </div>
                <ul class="mt-5 space-y-3">
                    <?php foreach ($chunk['items'] as $item): ?>
                        <li>
                            <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>" class="group flex items-center justify-between rounded-xl border border-transparent bg-surface px-4 py-3 text-sm font-medium text-foreground transition hover:border-primary/40 hover:bg-primary/5 focus:outline-none focus-visible:ring">
                                <span><?= htmlspecialchars($t($item['label']), ENT_QUOTES, 'UTF-8') ?></span>
                                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 text-muted transition group-hover:translate-x-1 group-hover:text-primary">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12l-3.75 3.75M21 12H3" />
                                </svg>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endforeach; ?>
    </div>
</section>
