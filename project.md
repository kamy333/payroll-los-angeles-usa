# Household Payroll Web App — Project Specification (PHP 8.3, Slim 4, Tailwind, Alpine.js, EN/ES)

> **Audience**: Code generation agent (Codex CLI) + human developer (Kamran).  
> **Objective**: Build a mobile‑first, multilingual (EN/ES), dark/light themed payroll/timekeeping web app for a California **household employer**. Employees enter hours; admin runs payroll compliant with CA household rules.  
> **Non‑negotiables**: PHP 8.3, Slim 4 (REST JSON + MVC), MySQL 8, TailwindCSS, Alpine.js, mobile‑first UI, role‑based portals (Admin vs Employee), structured phases.

---

## 0) Guiding Principles
- **Mobile‑first**: all screens optimized for small viewports first; progressively enhanced for desktop.  
- **Dark/Light theme**: auto by `prefers-color-scheme` + manual toggle; persist choice in `localStorage`.  
- **Separation of concerns**: Slim 4 routes -> Controllers -> Services -> Repositories -> Models/Entities -> Views (minimal; mostly SPA-like pages using Alpine for interactivity).  
- **Config‑driven compliance**: No hard‑coded tax rates or thresholds in code. Load from versioned JSON/DB tables by **year & jurisdiction** (federal, CA, city).  
- **Traceability & audits**: every approval, rate change, pay run, and export is logged.  
- **Accessibility**: color‑contrast safe, focus states, keyboard navigation for the sidebar and forms.  
- **Internationalization**: English (default) and Spanish; easily extendable. Static copy + dynamic labels via DB-backed i18n.  
- **Security**: CSRF, session hardening, rate‑limited auth, password hashing (Argon2id), RBAC.

> **Legal scope note**: This app *assists* payroll compliance; employer remains responsible for filings and remittances. The app produces statements, summaries, and **fillable PDFs** (e.g., Schedule H) populated from data.

---

## 1) Tech Stack & Project Structure

### Backend
- **Language/Runtime**: PHP 8.3
- **Framework**: Slim 4 + nyholm/psr7 + slim/psr7
- **DI Container**: PHP‑DI
- **Routing**: Slim Router
- **ORM/DB**: PDO (prepared statements). Optional: Doctrine DBAL for portability.
- **View**: Lightweight PHP templates for shells; content rendered by Tailwind + Alpine.
- **Auth**: Session‑based login (optional TOTP), RBAC (`employee`, `admin`).
- **PDF**: `dompdf/dompdf` or `spipu/html2pdf` for wage statements; `smalot/pdfparser` + `setasign/fpdi` for filling overlays on government PDFs.
- **Queue**: DB‑backed job table for pay runs & PDF renders (simple worker via CLI `php bin/worker.php`).

### Frontend
- **CSS**: Tailwind CSS (PostCSS build).  
- **JS**: Alpine.js for state & actions.  
- **Icon**: Heroicons or Lucide.  

### Directory Layout (MVC + services)
```
project/
  app/
    controllers/
    services/
      Payroll/
      Timekeeping/
      Tax/
      PDF/
      I18n/
    repositories/
    models/
    middleware/
    routes/
      web.php
      api.php
    helpers/
    validators/
  config/
    app.php
    db.php
    auth.php
    taxes/
      federal/2025.json
      ca/2025.json
      cities/ca/*.json
  public/
    index.php
    assets/
  resources/
    views/
    email/
    pdf_templates/
  storage/
    logs/
    cache/
    wage_statements/
    filled_forms/
  database/
    migrations/
    seeds/
  bin/
    worker.php
  tests/
  package.json
  tailwind.config.js
  composer.json
  .env.example
```

---

## 2) Roles & Portals
- **Employee**: login, view dashboard, enter/edit hours (until submitted), submit week, view balances (sick), download wage statements PDF, view payment history.
- **Admin (Employer)**: approve/reject hours, manage employees & pay profiles, run pay periods, generate wage statements, run compliance reports, export data, fill government form PDFs (e.g., Schedule H model), maintain tax rate configs.

RBAC policy file: `config/auth.php` mapping routes -> roles. Middleware enforces.

---

## 3) UI/UX Requirements
- **Layout**: Topbar (brand, theme toggle, language switch), **collapsible sidebar** (slide‑over on mobile; docked on desktop).  
- **Navigation**:
  - Employee: Dashboard, My Hours, Submit, My Pay Statements.
  - Admin: Approvals, Employees, Pay Periods/Pay Runs, Reports, Config, Forms (PDF), Audit.
- **Theme**: Tailwind with `data-theme` (`light` | `dark`). Persist in `localStorage`; respect `prefers-color-scheme`.
- **Language switcher**: EN/ES/PT‑BR/FR/DE/IT with optional flags (decorative). Persist in session + `localStorage`.
- **Mobile interactions**: large tap targets; sticky bottom action bar on forms (Save, Submit); swipe‑close sidebar; time pickers optimized for thumbs.
- **Nice-to-have / "delight"**:
  - Subtle **progress ring** for weekly hours (updates as user logs time).
  - **Confetti burst** on first successful week submission (respect reduced‑motion).
  - **Empty states** with clear CTAs and illustrations (monochrome to keep it clean in dark mode).
  - **Inline toasts** (success/error) and skeleton loaders.
- **A11y**: focus traps for dialogs/sidebars, visible focus styles, sufficient contrast in both themes.

---

## 4) Internationalization (EN, ES, PT-BR, FR, DE, IT)
- **Supported locales**: `en`, `es`, `pt_BR`, `fr`, `de`, `it`.  
- **Storage (normalized)**: two tables to avoid altering schema for new locales.
  - `i18n_keys(id, `key`, context)` — master list of translation keys.  
  - `i18n_translations(id, key_id, locale, value, updated_at, UNIQUE(key_id, locale))` — one row per key+locale.  
- **Helpers**: `__($key, $repl=[], $locale=null)` resolves with fallback chain: `user_locale → app_default (en)`.  
- **Admin UI**: translation editor with filters (missing/empty), CSV/JSON import/export, completion % per locale.  
- **Client preload**: expose a compact JSON map for current locale to Alpine via `<script>` tag; cache with ETag.

> **Flags**: show small flag icons **as decorative only** in the selector, but bind by **locale code** (flags ≠ languages; e.g., use Brazil flag for `pt_BR`). Provide an accessible text label and hide flags from screen readers (`aria-hidden="true"`).

---

## 5) Theming (Dark/Light) Implementation
- Add `data-theme` on `<html>` from server using cookie/session; Alpine listener toggles + persists.  
- Respect `prefers-color-scheme`; on first visit, adopt OS; thereafter use persisted choice.  
- Tailwind: define color tokens for `bg`, `text`, `muted`, `card`, `border` with dark variants.

---

## 6) Compliance Logic (Config‑Driven)
**Goal**: encapsulate CA household‑employer rules without freezing rates in code. The engine reads yearly config files and DB tables.

### Employee classification flags (stored per employee)
- `is_personal_attendant` (e.g., nannies, caregivers).  
- `is_live_in`.  
- Default job type (housekeeping / petcare / other).

### Overtime/Double‑time rules (abstracted)
- **Personal attendants**: overtime after *9 hours/day* or *45 hours/week*; no double‑time.  
- **Other domestic workers**: OT after *8/day* or *40/week*; DT after *12/day*; special rules for the *7th consecutive day*.  

> **Note**: The precise thresholds and carve‑outs load from `config/taxes/ca/20XX.json` so they can be revised yearly. Tests must assert classification is applied.

### Minimum wage
- Effective hourly >= **max(base_rate, locality_min_wage)**. Local tables by city/county with `effective_from/to`.

### Sick leave (CA PSL)
- Policy configurable per year: **frontload** (≥ 40h) or **accrual** (e.g., 1h per 30h worked) with carryover caps.

### Tax thresholds & rates (examples — values **must** come from config files)
- **Federal**: FICA trigger per household employee (annual), SS wage base, Medicare %, **FUTA** trigger per employer per quarter & wage base.  
- **California**: **SDI** rate (employee), **SUI/ETT** employer rates (configurable), optional SIT tables (if withholding requested via DE‑4).  

### Output artifacts
- **Wage statements** (CA Labor Code §226 fields).  
- **Employer summaries** per pay run, per quarter, per year.  
- **Government forms**: generate data payloads + fill **Schedule H** PDF; create model fill for W‑2/W‑3 (data export CSV for external filing), EDD registration helpers.

---

## 7) Database Schema (MySQL 8) — Initial Migration
```sql  -the tables has been created in localhost   database name = payroll_us_app  username = root password = Carribou333 
-- users & employees
CREATE TABLE users (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('employee','admin') NOT NULL DEFAULT 'employee',
  status ENUM('active','disabled') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE employees (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT NOT NULL,
  legal_name VARCHAR(190) NOT NULL,
  ssn_last4 CHAR(4) NULL,
  address VARCHAR(190), city VARCHAR(100), state CHAR(2), zip VARCHAR(15),
  hire_date DATE NOT NULL,
  termination_date DATE NULL,
  is_live_in TINYINT(1) NOT NULL DEFAULT 0,
  is_personal_attendant TINYINT(1) NOT NULL DEFAULT 0,
  default_location VARCHAR(120),
  notes TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_emp_user FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- i18n (normalized for multiple locales)
CREATE TABLE i18n_keys (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  `key` VARCHAR(190) NOT NULL UNIQUE,
  context VARCHAR(120) NULL
) ENGINE=InnoDB;

CREATE TABLE i18n_translations (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  key_id BIGINT NOT NULL,
  locale VARCHAR(10) NOT NULL, -- e.g., en, es, pt_BR, fr, de, it
  value TEXT NOT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uk_key_locale (key_id, locale),
  CONSTRAINT fk_i18n_key FOREIGN KEY (key_id) REFERENCES i18n_keys(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- pay profiles (rates vary over time)
CREATE TABLE pay_profiles (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  employee_id BIGINT NOT NULL,
  base_rate DECIMAL(8,2) NOT NULL,
  overtime_mult DECIMAL(4,2) NOT NULL DEFAULT 1.5,
  doubletime_mult DECIMAL(4,2) NOT NULL DEFAULT 2.0,
  min_wage_floor DECIMAL(8,2) NULL,
  city_code VARCHAR(64) NULL,
  effective_from DATE NOT NULL,
  effective_to DATE NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_pp_emp FOREIGN KEY (employee_id) REFERENCES employees(id)
) ENGINE=InnoDB;
```

-- (unchanged sections below remain as previously specified: `time_entries`, `weekly_totals`, `psl_balances`, `pay_periods`, `pay_runs`, `pay_run_items`, `wage_statements`, `audit_log`)

--- (rates vary over time)
CREATE TABLE pay_profiles (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  employee_id BIGINT NOT NULL,
  base_rate DECIMAL(8,2) NOT NULL,
  overtime_mult DECIMAL(4,2) NOT NULL DEFAULT 1.5,
  doubletime_mult DECIMAL(4,2) NOT NULL DEFAULT 2.0,
  min_wage_floor DECIMAL(8,2) NULL,
  city_code VARCHAR(64) NULL,
  effective_from DATE NOT NULL,
  effective_to DATE NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_pp_emp FOREIGN KEY (employee_id) REFERENCES employees(id)
) ENGINE=InnoDB;

-- time tracking
CREATE TABLE time_entries (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  employee_id BIGINT NOT NULL,
  work_date DATE NOT NULL,
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  unpaid_break_minutes INT NOT NULL DEFAULT 0,
  job_type ENUM('attendant','housekeeping','petcare','other') NOT NULL DEFAULT 'other',
  location VARCHAR(120),
  notes VARCHAR(255),
  status ENUM('draft','submitted','approved','rejected') NOT NULL DEFAULT 'draft',
  submitted_at DATETIME NULL,
  approved_at DATETIME NULL,
  approved_by BIGINT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_te_emp FOREIGN KEY (employee_id) REFERENCES employees(id)
) ENGINE=InnoDB;

CREATE TABLE weekly_totals (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  employee_id BIGINT NOT NULL,
  week_start_date DATE NOT NULL,
  reg_hours DECIMAL(6,2) NOT NULL DEFAULT 0,
  ot_hours DECIMAL(6,2) NOT NULL DEFAULT 0,
  dt_hours DECIMAL(6,2) NOT NULL DEFAULT 0,
  locked TINYINT(1) NOT NULL DEFAULT 0,
  calc_json JSON NULL,
  UNIQUE KEY uk_emp_week (employee_id, week_start_date),
  CONSTRAINT fk_wt_emp FOREIGN KEY (employee_id) REFERENCES employees(id)
) ENGINE=InnoDB;

-- sick leave balances
CREATE TABLE psl_balances (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  employee_id BIGINT NOT NULL,
  policy ENUM('frontload','accrual') NOT NULL DEFAULT 'frontload',
  year INT NOT NULL,
  hours_accrued DECIMAL(6,2) NOT NULL DEFAULT 0,
  hours_used DECIMAL(6,2) NOT NULL DEFAULT 0,
  hours_available DECIMAL(6,2) NOT NULL DEFAULT 0,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uk_emp_year (employee_id, year),
  CONSTRAINT fk_psl_emp FOREIGN KEY (employee_id) REFERENCES employees(id)
) ENGINE=InnoDB;

-- pay periods & runs
CREATE TABLE pay_periods (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  pay_date DATE NOT NULL,
  status ENUM('open','processing','closed') NOT NULL DEFAULT 'open'
) ENGINE=InnoDB;

CREATE TABLE pay_runs (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  pay_period_id BIGINT NOT NULL,
  created_by BIGINT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  status ENUM('draft','final') NOT NULL DEFAULT 'draft',
  CONSTRAINT fk_pr_pp FOREIGN KEY (pay_period_id) REFERENCES pay_periods(id)
) ENGINE=InnoDB;

CREATE TABLE pay_run_items (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  pay_run_id BIGINT NOT NULL,
  employee_id BIGINT NOT NULL,
  reg_hours DECIMAL(6,2) NOT NULL DEFAULT 0,
  ot_hours DECIMAL(6,2) NOT NULL DEFAULT 0,
  dt_hours DECIMAL(6,2) NOT NULL DEFAULT 0,
  psl_hours DECIMAL(6,2) NOT NULL DEFAULT 0,
  reg_pay DECIMAL(10,2) NOT NULL DEFAULT 0,
  ot_pay DECIMAL(10,2) NOT NULL DEFAULT 0,
  dt_pay DECIMAL(10,2) NOT NULL DEFAULT 0,
  psl_pay DECIMAL(10,2) NOT NULL DEFAULT 0,
  gross_pay DECIMAL(10,2) NOT NULL DEFAULT 0,
  fica_ss DECIMAL(10,2) NOT NULL DEFAULT 0,
  fica_medicare DECIMAL(10,2) NOT NULL DEFAULT 0,
  futa DECIMAL(10,2) NOT NULL DEFAULT 0,
  ca_sdi DECIMAL(10,2) NOT NULL DEFAULT 0,
  ca_sui DECIMAL(10,2) NOT NULL DEFAULT 0,
  ca_ett DECIMAL(10,2) NOT NULL DEFAULT 0,
  fit_withheld DECIMAL(10,2) NOT NULL DEFAULT 0,
  sit_withheld DECIMAL(10,2) NOT NULL DEFAULT 0,
  other_deductions DECIMAL(10,2) NOT NULL DEFAULT 0,
  net_pay DECIMAL(10,2) NOT NULL DEFAULT 0,
  calc_json JSON NULL,
  CONSTRAINT fk_pri_pr FOREIGN KEY (pay_run_id) REFERENCES pay_runs(id),
  CONSTRAINT fk_pri_emp FOREIGN KEY (employee_id) REFERENCES employees(id)
) ENGINE=InnoDB;

CREATE TABLE wage_statements (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  pay_run_item_id BIGINT NOT NULL,
  pdf_path VARCHAR(255) NOT NULL,
  statement_number VARCHAR(64) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_ws_pri FOREIGN KEY (pay_run_item_id) REFERENCES pay_run_items(id)
) ENGINE=InnoDB;

-- audit
CREATE TABLE audit_log (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  actor_user_id BIGINT NOT NULL,
  action VARCHAR(120) NOT NULL,
  entity VARCHAR(120) NOT NULL,
  entity_id BIGINT NULL,
  before_json JSON NULL,
  after_json JSON NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
```

---

## 8) REST API (Slim 4) — First Set
**Auth**
- `POST /api/auth/login` {email,password}
- `POST /api/auth/logout`

**Employee**
- `GET /api/me` — profile & balances
- `GET /api/time?from=YYYY-MM-DD&to=YYYY-MM-DD`
- `POST /api/time` — create entry (start, end, break, job_type, notes)
- `PUT /api/time/{id}` — edit (if status=draft)
- `POST /api/time/{id}/submit`
- `GET /api/payslips` — list statements

**Admin**
- `GET /api/admin/approvals?status=submitted`
- `POST /api/admin/time/{id}/approve`
- `POST /api/admin/time/{id}/reject`
- `POST /api/admin/pay-runs` {pay_period_id}
- `GET /api/admin/pay-runs/{id}` — summary, totals
- `POST /api/admin/pay-runs/{id}/finalize`
- `GET /api/admin/reports/quarterly-wages?year=YYYY&q=1..4`
- `GET /api/admin/reports/fica-futa-thresholds?year=YYYY`
- `GET /api/admin/forms/schedule-h/data?year=YYYY`

**Notifications**
- `POST /api/admin/notifications/test` — send a test email to current user
- **Webhooks**: none (queued internal jobs for emails)

---

## 9) Payroll Engine (Service) — Algorithm Outline
1. **Collect inputs**: approved `time_entries` in the pay period + `pay_profile` effective during each day + PSL usage + config for jurisdiction/year (federal & CA; locality MW).  
2. **Split per day** → compute daily hours net of unpaid breaks.  
3. **Classify hours** using rules by classification:
   - *Personal attendant*: OT > 9/day or > 45/week; **no DT**.
   - *Other domestic*: OT > 8/day to 12; DT > 12/day; special 7th‑day rules.
4. **Weekly caps**: apply weekly OT caps (40 or 45 per classification).  
5. **Effective rate**: `effective_rate = max(base_rate, locality_min_wage)` for that date/location.  
6. **Gross**: `reg*rate + ot*rate*overtime_mult + dt*rate*doubletime_mult + psl_pay`.  
7. **Taxes**: compute FICA, FUTA, CA SDI/SUI/ETT, optional FIT/SIT, driven by **yearly config** + thresholds (e.g., employer crosses FUTA trigger in quarter, etc.).  
8. **Net**: gross − employee taxes − other deductions.  
9. **Persist** pay_run_item; render **wage statement** (PDF) and employer **summary**.

**Config sources**: `config/taxes/federal/YYYY.json`, `config/taxes/ca/YYYY.json`, `config/taxes/cities/ca/*.json` with schemas for thresholds, rates, and rule toggles.

---

## 10) Misclassification & Compliance Module
- **Checklist**: guided questionnaire; result explains risks if "contractor" is chosen for domestic roles likely to be employees. Store answers for audit.
- **Registration helpers**: capture EIN, CA EDD account, pay frequency, workweek start day.
- **Filings**:
  - **Schedule H**: builder → FPDI overlay; stored in `storage/filled_forms/`.
  - **W‑2/W‑3**: CSV/EFW2 export (for external filing platforms).
  - **EDD**: quarterly totals export (SUI/ETT/SDI) in CSV.
- **Electronic delivery policy**: enable **electronic wage statements** and email *notifications*. Email should link to the secure portal (avoid sending full payslip in the message body). Require employee consent; allow paper on request.

---

## 11) Forms & PDFs
- **Wage Statement** (CA LC §226): employer name/address, employee name, last 4 SSN or employee ID, pay period dates, hours by rate (reg/OT/DT), gross, deductions (FICA, SDI, etc.), net, legal pay date. HTML template → PDF.
- **Schedule H**: map internal totals to IRS fields; generate filled PDF; store.
- **Notices**: email templates: time-submitted (to admin), time-approved (to employee), pay-run-finalized (to each employee with link to payslip).

---

## 12) Phased Delivery Plan (for Codex CLI) — with Checklists
> Execute phases in order. After each phase, **Kamran runs the checklist** before moving on.

### Phase 1 — Bootstrap & Scaffolding
**Codex builds**: Slim 4 skeleton; Tailwind+Alpine pipeline; base layout with topbar, theme toggle, language switcher; collapsible sidebar (mobile slide‑over, desktop docked); i18n service wired to `i18n_keys/i18n_translations` with seeds for EN/ES/PT‑BR/FR/DE/IT.
**Kamran checks**:
- Load on mobile; sidebar works; theme toggle persists; language switch updates labels for all seeded strings.
- Lighthouse accessibility score ≥ 90 on a sample page.

### Phase 2 — Auth & RBAC
**Codex builds**: login/logout; session; Argon2id; CSRF; rate limit; seed 1 admin + 2 employees; middleware guards.
**Kamran checks**:
- Wrong password throttles; employee cannot access admin routes; dark/light persists after login.

### Phase 3 — Timekeeping
**Codex builds**: CRUD for `time_entries`; weekly submission flow; admin approvals queue; audit logging.
**Kamran checks**:
- Enter a week, submit, verify entries lock; admin approves/rejects; audit log shows actions.

### Phase 4 — Payroll Engine v1
**Codex builds**: reg/OT/DT classification using config; pay periods, pay runs; preview and finalize (gross only).
**Kamran checks**:
- Test personal attendant vs housekeeping scenarios; verify hour splits by day/week.

### Phase 5 — Taxes & Deductions v1
**Codex builds**: FICA/FUTA/CA SDI/SUI/ETT + optional FIT/SIT via config; employer summary; threshold banners.
**Kamran checks**:
- Quarter with ≥$1,000 total triggers FUTA banner; employee crossing FICA trigger recalculates correctly.

### Phase 6 — PDFs & Statements
**Codex builds**: Wage statement PDF (LC §226), Schedule H builder + filled PDF; download list in employee portal.
**Kamran checks**:
- PDF shows required fields; employee sees own statements only; Schedule H totals match pay-run aggregates.

### Phase 7 — PSL (Paid Sick Leave)
**Codex builds**: front‑load & accrual policies; balances; request/use in time entry; admin override.
**Kamran checks**:
- Accrual ticks with hours; usage reduces balance; policy switch reflected next pay period.

### Phase 8 — Notifications & Email Queue
**Codex builds**: mailer (SMTP + queue); events → notifications: (1) *time submitted* → admin; (2) *time approved/rejected* → employee; (3) *pay-run finalized* → employee with **portal link** to payslip.
**Kamran checks**:
- Test emails fire and contain correct locale, employee name, and links; no PII beyond what’s necessary.

### Phase 9 — Reports & Exports
**Codex builds**: quarterly wages, locality MW compliance, PSL liability; CSV/JSON exports.
**Kamran checks**:
- Run reports for sample data; totals tie out to pay runs.

### Phase 10 — Hardening & A11y
**Codex builds**: unit tests for PayrollEngine; PDF snapshot tests; a11y improvements; logging.
**Kamran checks**:
- Test suite green; keyboard-only navigation workable; no console errors.

---

## 13) Codex CLI — Execution Hints / Prompts
Use the following **high‑level prompt** for Codex CLI to initialize and then proceed phase‑by‑phase. Replace placeholders as needed.

```
You are Codex CLI. Build a PHP 8.3 Slim 4 web app using Tailwind CSS and Alpine.js.
Follow the "Phased Delivery Plan" in project.md exactly, completing one phase at a time and ensuring the app runs.
Key requirements: mobile-first; dark/light theme; collapsible sidebar (mobile slide-over, desktop docked); EN/ES i18n from DB; RBAC (employee/admin); MySQL 8 schema from project.md; payroll rules via config files; PDFs for wage statements and Schedule H.
Generate:
- Composer project with Slim 4, PHP-DI, vlucas/phpdotenv, dompdf, fpdi.
- Migrations and seeds matching the SQL in project.md.
- Tailwind + PostCSS pipeline; Alpine installed; base layout with theme & language toggles.
- Controllers/Services/Repositories in MVC structure as specified.
- Sample data: 1 admin, 2 employees, 2 weeks of time entries.
- Scripts: `composer migrate`, `composer seed`, `npm run dev`, `npm run build`.
Complete Phase 1 and halt with run instructions. Await next command to proceed to Phase 2.
```

For **subsequent phases**, Codex should be instructed: “Continue to Phase N”, then verify via a health check endpoint and provide a short changelog.

---

## 14) Environment & Runbook
- **Requirements**: PHP 8.3, Composer, Node 20+, MySQL 8.  
- **Env**: `.env` with `DB_*`, `APP_ENV`, `APP_URL`, `SESSION_SECRET`, `MAIL_*` (SMTP host, port, username, from address).  
- **Install**: `composer install && npm install && npm run build && composer migrate && composer seed`.  
- **Serve**: `php -S localhost:8080 -t public` (dev) or Apache/Nginx with `public/` as web root.  
- **Worker**: `php bin/worker.php` (process email/PDF jobs) — run via supervisor in prod.

---

## 15) Security, Privacy, Audits
- Argon2id hashes; password rehash on login if params upgraded.  
- CSRF tokens; SameSite=Lax cookies; HTTPS only in prod; TLS for SMTP.  
- **Email policy**: send **notifications only**; the full payslip lives behind login. Avoid sending SSN fragments or full earnings breakdown in email.  
- Encrypt PII at rest where feasible; restrict access by role; least‑privilege DB users.  
- Audit every state change (approve/reject, rate changes, pay runs).  
- PII minimization: store `ssn_last4` only; log redaction.

---

## 16) Testing
- PHPUnit tests for PayrollEngine classifiers (attendant vs non‑attendant; 8/9/12‑hour splits; 7th consecutive day).  
- Golden master tests for wage statement PDFs (hash compare).  
- Smoke tests for API endpoints.

---

## 17) Deployment
- `.env` per environment; database migrations via CLI.  
- Static assets versioned.  
- Access logs + app logs retained 12 months.

---

## 18) Open Items / Config Data You Must Provide Later
- Year‑specific config JSONs for **federal/CA/locality**: tax rates, thresholds, minimum wages.  
- Official PDF templates for Schedule H and any other forms you want auto‑filled.  
- Employer identifiers (EIN, CA EDD account), pay frequency, workweek start day.

---

## 19) Acceptance Criteria (MVP)
- Employee can submit hours on mobile; admin can approve; a pay run computes reg/OT/DT and produces a wage statement PDF.  
- Theme toggle and language (EN/ES) persist across sessions.  
- Sidebar collapses on mobile and docks on desktop.  
- All rates/thresholds loaded from config, not hard‑coded.  
- Schedule H PDF fill produces a file containing expected year‑to‑date totals.

---

## 20) Next Steps
1. Run **Phase 1** with Codex CLI using the provided prompt.  
2. Share the generated repo; we’ll review structure and wire up configs.  
3. Proceed iteratively through phases, validating each on mobile.

