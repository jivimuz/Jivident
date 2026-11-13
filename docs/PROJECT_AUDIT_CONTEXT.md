# Jivident SIMRS & Clinic ERP — Project Audit, Architecture Context & Known Gaps

## 1. Project Overview & Architecture
Jivident is a comprehensive Laravel 12 clinic and dental-practice management system (SIMRS / Clinic ERP) that has evolved from basic patient registration and examination into a full multi-branch clinic ERP system. It features pharmacy POS, payroll (PPh21 / BPJS), employee scheduling & leave, doctor appointment booking, multi-branch management (`Klien`), financial/clinical/HR reporting, laboratory, radiology, inpatient (`Rawat Inap`), operating rooms (`Ruangan Operasi`), and e-prescriptions with pharmacy dispensing.

- **Architecture**: Classic server-rendered Blade admin application using jQuery and a Bootstrap admin template (`public/assets/`). There is no separate frontend SPA or API framework layer.
- **Navigation**: Uses Turbo Drive (`@hotwired/turbo`) for fast, SPA-like navigation across standard views, with a dedicated fullscreen layout (`layout/fullscreen.blade.php`) for cashier POS and public monitor boards.
- **Access Control**: Module-code based authorization managed by `App\Http\Middleware\CheckModuleAccess` (`checkAccess:<CODE>`), reading the user's comma-separated `access` module IDs.
- **Desktop Integration**: Fully configured for NativePHP desktop deployment with an offline Database Setup Wizard (`/native-config`) and flexible license gating (`AuthApiMiddleware`).

---

## 2. Core Modules & Subsystems

| Module Group | Code Prefix | Key Features & Subsystems |
| :--- | :--- | :--- |
| **Pendaftaran & Pasien** | `DPP`, `DPS`, `DPA`, `DPR`, `IGD`, `DBT` | Patient registration, Outpatient queue, Inpatient queue, IGD triage, visit cancellation. |
| **Pemeriksaan & Rekam Medis** | `DPP` | SOAP notes, Vitals, Tindakan (Procedures), Odontogram (3D & 2D diagnosis), Prescriptions (Resep Pasien), Consent. |
| **Farmasi & POS** | `POS`, `RO`, `VRS`, `SO`, `PRC` | Pharmacy POS checkout with FEFO stock-out logic, Receive Orders (RO), Recipe dispensing (`/verifikasi-resep`), Stock Opname, Procurement. |
| **Layanan Lanjutan** | `LY`, `LAB`, `RAD`, `RWI`, `OK` | Laboratory orders & results, Radiology orders & imaging, Inpatient (`Rawat Inap`) bed occupancy & billing, Operating Rooms (`Ruangan Operasi`). |
| **Keuangan & Akuntansi** | `BL`, `KSR`, `BTM`, `AKT`, `BPJ` | Patient billing, manual cashier ledger (`KSR`), additional charges (`Biaya Tambahan`), Double-entry accounting journals, BPJS claims. |
| **HR & Payroll** | `EMP`, `ROL`, `JDW`, `CUT`, `PAY` | Employee management, Role permissions, Working schedules, Leave requests (`Cuti`), Payroll with PPh21 TER brackets. |
| **Master Data** | `M`, `CAB`, `JDD`, `MOD`, `SUP`, `RPR` | Master data catalogs (Procedures, ICD-9/10, Suppliers, Rooms, Odontogram diagnosis master, Branches/Klien). |

---

## 3. Verified Strengths & Robust Features
1. **Idempotent & Comprehensive Seeders**: Split cleanly into `MasterDataSeeder` (master catalogs) and `TransactionalSeeder` (realistic demo data using business logic helpers like `ProductQtyHelper`, `PosSaleHelper`, and `TransactionHelper`).
2. **FEFO Inventory Control**: Automated first-expired-first-out stock consumption for pharmacy sales and prescription dispensing.
3. **Robust Billing Integration**: All charges (Tindakan, Diagnosa, Lab, Radiologi, Resep, Biaya Tambahan, Inpatient Room per-diem) aggregate seamlessly into billing and financial reports.
4. **NativePHP Desktop Offline Support**: Dedicated offline Setup Wizard (`/native-config`) supporting MySQL and SQLite drivers, dynamic `.env` writer, auto-migration/seeding, and flexible license bypass modes (`TYPE_LOCAL_SERVER`).

---

## 4. Identified Gaps, Technical Debt & Bugs

### A. Architectural & Database Gaps
- **Global HR/Payroll Scope**: Tables in the HR and payroll domain (`m_employee`, `t_payroll_slips`, `t_cuti`, `t_health_insurance`) currently lack a `klienfk` tenant-scoping column. Employees and payroll are globally shared across branches rather than branch-isolated.
- **BPJS Mock Implementation**: `m_setting` contains placeholder fields for VClaim and PCare credentials, but no live API client or HMAC-SHA256 signature generator is implemented yet. BPJS status (`jenispembayaran = 'BPJS'`) is purely informational and does not trigger automated tariff calculations or INA-CBG grouping.

### B. UI & Frontend Fragility
- **Turbo Drive Lifecycle Gotchas**: Shared scripts in footer rely on `data-turbo-permanent`, but page-specific scripts must carefully avoid top-level `let`/`const` declarations to prevent redeclaration crashes (`SyntaxError`) upon revisiting pages. Stateful chart or DataTable instances (e.g., ApexCharts, DataTables) require proper null-resetting (`var chart = null;`) to prevent stale DOM bindings.
- **Odontogram 2D Hardcoded Reset Index**: The 2D Global Odontogram modal hardcodes `accesskey="0"` assuming the "Bersih" reset condition is always at array index 0 in the master table.

### C. Error Handling & Edge Cases
- **License Server Dependency**: When `TYPE_LOCAL_SERVER = 0` or `1` (Online/Offline with validation), if the remote management server (`AUTH_SERVER`) is unreachable, the middleware now gracefully falls back to local grace periods, but persistent offline environments require `TYPE_LOCAL_SERVER = 2` or a valid cached token.
- **Single-Tenant Default Fallback**: Certain legacy helpers or master endpoints (e.g., Stock Opname) default to location ID `1` because multi-location warehouse mapping tables (`m_productlocations`) are minimally seeded.

---

## 5. Summary & Next Recommendations
The Jivident system is robust, thoroughly tested across 131+ unit and feature tests, and fully prepared for desktop distribution via NativePHP. To reach production grade across multi-branch hospital deployments:
1. Implement multi-branch tenant scoping (`klienfk`) for employee and payroll records.
2. Replace BPJS placeholder settings with an authenticated API client once official credentials are provided.
3. Continue maintaining idempotent seeders for any new master data additions.