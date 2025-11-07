# QA & Testing Guide

This document captures manual and automated checks to run after each project phase. Extend it as new phases are delivered.

## Phase 1 — Bootstrap & Scaffolding
**Goal**: Verify the Slim skeleton, Tailwind/Alpine pipeline, base layout, theme toggle, and multilingual shell.

### Environment Prep
- Copy `.env.example` to `.env` and adjust `DB_*` values for the dev database (`payroll_us_app`).
- Run `composer install` (already satisfied by the repo) and `npm install` if dependencies changed.
- Build front-end assets: `npm run build`.
- Seed UI translations: `composer seed:i18n`.

### Manual Checks
- **Mobile shell**: Start the dev server (`composer serve`) and open `http://localhost:8080` on a mobile viewport (browser dev tools). Toggle the sidebar open/closed; verify the backdrop appears, body scroll locks, focus returns to the toggle button when closed, and `Esc`/backdrop taps dismiss it.
- **Desktop layout**: Resize above 1024px; sidebar should stay docked and the page should remain scrollable.
- **Theme toggle**: Switch between system, light, and dark. Ensure the dropdown stays closed until activated, the visual theme updates instantly, and the preference survives a reload (check both localStorage and the `theme` cookie).
- **Language toggle**: Change the language between EN, ES, PT-BR, FR, DE, IT. After each change, confirm all visible labels (brand, nav, hero cards) update and persist after reload (cookie + session).
- **Lighthouse a11y**: Run Lighthouse (Chrome DevTools) on the dashboard view in mobile mode; confirm accessibility score ≥ 90.

### Smoke Commands
- `php -l app/bootstrap.php` – ensure PHP syntax validity.
- `composer dump-autoload` – confirm autoloader is healthy after new classes.

> Mark each item complete before continuing to Phase 2. Document issues inline so regressions are avoided later.
