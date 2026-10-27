# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

Jivident is a Laravel 12 clinic/dental-practice management system (patient registration, waiting room queue, examination/odontogram, prescriptions, billing, inventory, HR/employees). It's a classic server-rendered Blade admin app (jQuery + Bootstrap admin template), not an SPA — there is no API layer for a separate frontend.

## Commands

```bash
# First-time setup (creates .env, generates key, migrates)
composer setup

# Local dev — runs php artisan serve, queue:listen, and pail (log tailing) concurrently
composer dev

# Run the full test suite (clears config cache first)
composer test
# equivalent to:
php artisan test

# Run a single test file / method
php artisan test tests/Unit/ProductQtyHelperTest.php
php artisan test --filter=test_stock_out_fefo_consumes_earliest_expiring_lot_first

# Lint / format PHP (Laravel Pint)
vendor/bin/pint
vendor/bin/pint --test   # check only, no write

# DB
php artisan migrate
php artisan migrate:fresh --seed
```

There is no frontend build step — see the Vite note below. `tests/` has real coverage for the two business-logic helpers and the license gate middleware (`ProductQtyHelperTest`, `TransactionHelperTest`, `AuthApiMiddlewareTest`); everything else in the app still has none.

## Architecture

**All business logic lives under one controller namespace:** `App\Http\Controllers\Website\*`. Routes are defined entirely in [routes/web.php](routes/web.php) (no `api.php`), grouped by feature prefix (`patient`, `pemeriksaan`, `transaction`, `billing`, `product`, `employees`, etc.), each wrapped in `auth` + a per-feature `checkAccess:<CODE>` middleware group, and the whole authenticated group is throttled (`throttle:300,1`).

**Access control is module-code based, not role-based.** `App\Http\Middleware\CheckModuleAccess` (aliased as `checkAccess`) reads the comma-separated `access` column on the logged-in `User` (a list of `m_modules` IDs) and checks it against the module `code` passed to the middleware (e.g. `checkAccess:PA` for Patient, `checkAccess:MTU` for user management, `checkAccess:DPP` for pemeriksaan). See [app/Models/Module.php](app/Models/Module.php) and [app/Http/Controllers/Website/ModuleController.php](app/Http/Controllers/Website/ModuleController.php) for how access lists are assigned per user.

**UI pattern:** every list page follows the same convention — a Blade `index` view renders an empty DataTable, and a sibling `POST .../getList` endpoint returns server-side-processed DataTables JSON. CRUD is done via `store`/`update`/`delete` POST endpoints returning JSON, consumed by SweetAlert2/AJAX in page-specific `<script>` blocks (not components). Follow this pattern when adding new list/CRUD pages rather than introducing a different frontend approach.

**There is no Vite/frontend build step.** The unused `laravel/laravel`-skeleton Vite+Tailwind scaffolding (`vite.config.js`, `resources/css/app.css`, `resources/js/app.js`/`bootstrap.js`, the `@tailwindcss/vite`/`vite`/`axios`/`laravel-vite-plugin` npm deps) has been removed — no Blade view ever referenced it (`@vite(...)` was never used). `package.json` now only declares `concurrently`, used by `composer dev`'s process runner. The actual admin UI (jQuery, DataTables, SweetAlert2, Select2, FilePond, ApexCharts, flatpickr, etc.) is the pre-built Bootstrap template living under `public/assets/`, loaded via plain `<script>`/`<link>` tags in [resources/views/layout/header.blade.php](resources/views/layout/header.blade.php) and [footer.blade.php](resources/views/layout/footer.blade.php) — edit those files directly when changing styling or client behavior. If a real asset pipeline is ever wanted, it'd need to be reintroduced and actually wired into the layout, not resumed from the old scaffolding.

**Patient flow (core domain):** `DaftarPasien` (daily registration/queue entry, linked to `Patient`) moves through waiting room (`RuangTunggu`) → examination (`Pemeriksaan`, covering vital signs, SOAPE notes, `Tindakan` (procedures), `Odontogram`, diagnosis via ICD-9/ICD-10 models, prescriptions `ResepPasien`) → either cancelled (`Dibatalkan`) or completed (`Diperiksa`) → billing (`Billing`/`Payments`/`Transactions`). `Antrian`/`Monitor` controllers drive the public queue-number display board.

**Inventory/stock logic is concentrated in two large helper classes**, not spread across controllers:
- [app/Helpers/ProductQtyHelper.php](app/Helpers/ProductQtyHelper.php) — stock mutations (`t_productqty`), receive-order processing, FEFO (first-expired-first-out) stock-out logic. Purchase PPN is a single global rate, not per-item/per-invoice: `Setting` row with `parameter = 'ppn_persen'` (editable from the Setting page), read once per `processReceiveOrder()` call and fed into `updateProductPrices()` to compute `hpp`/`harga_jual`.
- [app/Helpers/TransactionHelper.php](app/Helpers/TransactionHelper.php) — creates ledger-style `Transactions` records (in/out) tied to patients/billing.

Both are written defensively (manual validation + `InvalidArgumentException`/`RuntimeException`) rather than using Form Requests — follow that existing style when extending them.

**Global helper functions** (`WIB()`, `generateNoRM()`, `countAge()`, `rupiah()`) are autoloaded unconditionally via `composer.json`'s `autoload.files` → [app/Helpers/globalHelper.php](app/Helpers/globalHelper.php). `generateNoRM()` depends on `Auth::user()->klienfk` (multi-tenant client key) to build the patient record number.

**Auth is session-based only** (`config/auth.php` `web` guard, Eloquent `User` provider on table `m_users`). There is no JWT/API guard (`tymon/jwt-auth` was an unused dependency and has been removed) — don't introduce API-token auth assumptions without adding that infrastructure first.

**App-wide license/serial gate:** [app/Http/Middleware/AuthApiMiddleware.php](app/Http/Middleware/AuthApiMiddleware.php) is registered globally (`bootstrap/app.php`) and runs before every request except `license*`/`serialActivation` (note: NOT `login` — reaching the login page itself requires a valid license too). It checks a single-row `serial` table ([app/Models/Serial.php](app/Models/Serial.php)) for a `serial_code`/`valid_until`, re-validates against a remote endpoint (`AUTH_SERVER` env) at most once every 3 hours (tracked via `serial.last_check`), and redirects to the `license` route if missing/expired. The source uses octal-escaped string literals (`"\154\x69..."` etc.) rather than plain strings — when editing this file, decode/verify literal values with PHP rather than guessing (e.g. `php -r 'echo "\154...";'`). Setting `TYPE_LOCAL_SERVER=2` in env bypasses this gate entirely; `tests/Feature/AuthApiMiddlewareTest.php` covers its branches and `phpunit.xml` pins `TYPE_LOCAL_SERVER=0` so tests don't silently inherit a developer's local `.env` value.

**Multi-tenancy:** most domain tables are scoped by `klienfk` (client/tenant ID) referencing `Klien`, and many queries filter by the logged-in user's `klienfk` — check this when adding new queries so data doesn't leak across tenants.

**Address data** (`Provinsi`/`KotaKabupaten`/`Kecamatan`/`Kelurahan`/`Alamat`) is a large seeded Indonesian administrative-region dataset (CSV seeders under `database/seeders/csv/`), used by patient address autocomplete (`PatientController::searchAddress`).
