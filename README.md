# Jivident — Clinic & Dental Practice Management System

<p align="center"><a href="README.id.md">🇮🇩 Bahasa Indonesia</a> · <strong>🇬🇧 English</strong></p>

A web-based clinic management application (Laravel 12), covering patient registration, medical records, billing, pharmacy/POS, payroll, doctor booking, up to basic hospital-style services (ER/Lab/Radiology/Inpatient care) and a self-service patient portal.

> ## 🔓 Free, No License — Self-Hosted Mode
>
> This application has a license/serial validation mechanism against a remote server by default. **To use it for free, with no license validation at all**, set the following in your `.env` file:
>
> ```env
> TYPE_LOCAL_SERVER=2
> ```
>
> With this value, the license gate (`AuthApiMiddleware`) is bypassed entirely — no serial check, no connection to any license server. Suitable for self-use, further development, or self-hosted deployment with no license cost. See the [License & Access Mode](#license--access-mode) section below for details.

## Table of Contents

- [Main Features](#main-features)
- [Tech Stack](#tech-stack)
- [Installation](#installation)
- [Running Locally](#running-locally)
- [Default Login](#default-login)
- [License & Access Mode](#license--access-mode)
- [Testing & Code Style](#testing--code-style)
- [Project Structure](#project-structure)

## Main Features

**Patient Registration & Queue**
- New/returning patient registration, automatic medical record number generation per branch.
- Waiting room, exam status (seen/cancelled), public queue display board (`/monitor-antrian`).
- Online doctor appointment booking by patients, with slots generated from a weekly practice schedule — one practice room can be shared by several doctors at different time slots.

**Examination & Medical Records**
- Vital signs, SOAPE notes, procedures, odontogram (adult & pediatric), ICD-9/ICD-10 diagnosis, prescriptions (including compounded ones).
- Informed consent (general & per-procedure) with the consent text snapshotted at signing time.
- Simple digital signature (canvas-based, not a certified electronic signature) for doctors, patients, and guardians.
- Print health/sick/referral certificates, as well as standalone certificates for people who aren't registered patients.

**Billing, Finance & Pharmacy**
- Per-visit billing, payment history, a transaction ledger (manual cashier entries, POS sales, supplier payments).
- Pharmacy POS with prices locked to the product master, FEFO (first-expired-first-out) stock deduction, supplier receive orders.
- Financial, clinical, and HR reports with charts (ApexCharts).

**HR & Payroll**
- Employee data, work schedules, leave requests & approvals.
- Payroll with BPJS Kesehatan/Ketenagakerjaan and PPh21 (Indonesian income tax, TER scheme per PMK 168/2023) calculations.

**Advanced Services (hospital-style)**
- ER (IGD) with a triage-based queue instead of arrival order.
- Lab & Radiology: orders placed from the Examination tab, fulfilled from a dedicated worklist, results & price snapshotted when the order is created.
- Inpatient care (Rawat Inap): admission for already-registered patients, billing accumulated automatically (daily room rate + procedures + diagnosis + lab + radiology + prescriptions during the stay).

**Multi-Branch & Patient Portal**
- Clinical/transactional data is scoped per branch (`Klien`) — users only see their own branch's data.
- A self-service patient portal (no staff account needed) to view visit history, prescriptions, bills, and book their own doctor appointments.

**Access Control & Audit**
- Module-based access (not fixed roles) — each user has a list of accessible modules, configurable per menu.
- Activity logging on key financial/medical models (registration, diagnosis, payroll, work schedules, leave, etc).

> The feature list above reflects the current state of the code. For deeper architectural/technical conventions, see [`CLAUDE.md`](CLAUDE.md).

## Tech Stack

- **Backend:** Laravel 12, PHP 8.2+, Eloquent ORM.
- **Database:** SQLite by default for development (MySQL/MariaDB also supported).
- **Frontend:** Blade (server-rendered, not an SPA framework) + jQuery, a Bootstrap admin template, DataTables, Select2, SweetAlert2, ApexCharts, FilePond, flatpickr — all loaded via plain `<script>`/`<link>` tags, **no build step** (Vite/Tailwind were removed from the original skeleton).
- **Navigation feels like an SPA** via [Turbo Drive](https://turbo.hotwired.dev/) (intercepts link clicks/form submits, swaps `<body>` via fetch) without rewriting the frontend into a JS framework.
- **PDF:** `barryvdh/laravel-dompdf` for every printout (letters, invoices, etc.), via the `viewOrPdf()` helper.

## Installation

### Requirements

- PHP 8.2 or newer + Composer.
- Node.js (only used for `concurrently`, the process runner for `composer dev` — **not** for a frontend build).
- Standard Laravel PHP extensions (`pdo_sqlite` or `pdo_mysql`, `mbstring`, `openssl`, etc).

### Steps

```bash
# 1. Clone the repo and enter it
git clone <this-repo-url> jivident
cd jivident

# 2. Install PHP dependencies
composer install

# 3. First-time setup — creates .env, generates APP_KEY, then migrates
composer setup
```

`composer setup` automatically:
1. Copies `.env.example` to `.env` (if it doesn't exist yet).
2. Generates `APP_KEY`.
3. Runs `php artisan migrate --force`.

After that, **set `TYPE_LOCAL_SERVER=2`** in `.env` as mentioned at the top, so you don't hit the license gate.

If you want sample data (Indonesian regional data for address autocomplete, ICD-9/10, product categories, etc.) seeded too:

```bash
php artisan migrate:fresh --seed
```

## Running Locally

```bash
composer dev
```

This runs three processes at once (via `concurrently`): `php artisan serve`, `php artisan queue:listen`, and `php artisan pail` (log tailing) — so a single terminal is enough for day-to-day development.

The app is reachable at `http://localhost:8000` (or whatever `APP_URL`/port `php artisan serve` ends up using).

## Default Login

The initial migration (`2024_07_17_005400_create_users_table.php`) already creates a few sample users:

| Username     | Password   | Note                  |
|--------------|------------|------------------------|
| `superadmin` | `12341234` | Full access            |
| `user1`      | `12341234` | Extra sample user      |
| `user2`      | `12341234` | Extra sample user      |

**Change these passwords immediately** if the app is used anywhere outside a local development environment.

## License & Access Mode

This application has a global middleware (`App\Http\Middleware\AuthApiMiddleware`) that runs on almost every request, checking a `serial` row in the database and — when it needs re-validating — making a request to the license server at `AUTH_SERVER` (env). Without a valid serial, the user is redirected to the `license` page.

There are two ways to run this application:

1. **Licensed mode (default):** requires a valid `serial_code`, periodically re-validated against `AUTH_SERVER`. Used when the app is deployed as a licensed product for other parties.
2. **Self-hosted / free mode:** set
   ```env
   TYPE_LOCAL_SERVER=2
   ```
   in `.env`. The license gate is bypassed entirely — no serial check, no connection to any server. **This is the recommended mode if you're running this app for yourself / your own organization, rather than as a product sold to other clinics.**

> Testing note: `phpunit.xml` pins `TYPE_LOCAL_SERVER=0`, so the test suite doesn't silently inherit a developer's local `.env` value — your own `.env` setting doesn't affect `composer test` results.

## Testing & Code Style

```bash
# Run the full test suite
composer test

# Run a specific test file/method
php artisan test tests/Unit/ProductQtyHelperTest.php
php artisan test --filter=test_method_name

# Check/format code style (Laravel Pint)
vendor/bin/pint --test   # check only, no write
vendor/bin/pint          # auto-format
```

## Project Structure

- `app/Http/Controllers/Website/` — all business logic controllers, one namespace for every feature.
- `app/Helpers/` — core logic shared across controllers (product stock/FEFO, transaction ledger, POS, payroll, doctor booking, manual database backup).
- `routes/web.php` — every route is defined here (no `api.php`), grouped per module with the `checkAccess:<MODULE_CODE>` middleware.
- `resources/views/pages/` — one folder per module/menu, following the `index` (empty DataTable) + `getList` (server-side DataTables) endpoint pattern.

For a deeper architectural explanation (conventions, design decisions, known gaps), read [`CLAUDE.md`](CLAUDE.md) at the project root — that document is written to help anyone (human or AI assistant) understand this codebase quickly.
