# Jivident — Sistem Manajemen Klinik & Praktek Dokter Gigi

<p align="center"><strong>🇮🇩 Bahasa Indonesia</strong> · <a href="README.md">🇬🇧 English</a></p>

Aplikasi manajemen klinik berbasis web (Laravel 12), mencakup pendaftaran pasien, rekam medis, billing, farmasi/POS, payroll, booking dokter, hingga layanan rumah sakit dasar (IGD/Lab/Radiologi/Rawat Inap) dan portal pasien mandiri.

<p align="center">
  <a href="https://www.youtube.com/watch?v=cBc9dCsd9mc" target="_blank">
    <img src="https://img.youtube.com/vi/cBc9dCsd9mc/hqdefault.jpg" alt="Video Demo Jivident" width="560">
  </a>
  <br>
  <a href="https://www.youtube.com/watch?v=cBc9dCsd9mc" target="_blank"><strong>▶️ Tonton Video Demo</strong></a>
</p>

> ## 🔓 Gratis, Tanpa Lisensi — Mode Self-Hosted
>
> Aplikasi ini punya mekanisme validasi lisensi/serial ke server remote secara default. **Untuk memakainya secara gratis dan tanpa validasi lisensi sama sekali**, set env berikut di file `.env`:
>
> ```env
> TYPE_LOCAL_SERVER=2
> ```
>
> Dengan nilai ini, gate lisensi (`AuthApiMiddleware`) dilewati sepenuhnya — tidak ada pengecekan serial, tidak ada koneksi ke server lisensi mana pun. Cocok untuk dipakai sendiri, dikembangkan lebih lanjut, atau di-deploy mandiri tanpa biaya lisensi. Detail lebih lanjut ada di bagian [Lisensi & Mode Akses](#lisensi--mode-akses).

## Daftar Isi

- [Fitur Utama](#fitur-utama)
- [Teknologi](#teknologi)
- [Instalasi](#instalasi)
- [Menjalankan Secara Lokal](#menjalankan-secara-lokal)
- [Login Default](#login-default)
- [Lisensi & Mode Akses](#lisensi--mode-akses)
- [Testing & Code Style](#testing--code-style)
- [Struktur Singkat](#struktur-singkat)

## Fitur Utama

**Pendaftaran & Antrian Pasien**
- Pendaftaran pasien baru/lama, generate No. RM otomatis per cabang.
- Ruang tunggu, status pemeriksaan (diperiksa/dibatalkan), papan antrian publik (`/monitor-antrian`).
- Booking jadwal dokter online oleh pasien, dengan slot yang digenerate dari jadwal praktek mingguan — satu ruang praktek bisa dipakai bergilir oleh beberapa dokter di jam yang berbeda.

**Pemeriksaan & Rekam Medis**
- Vital signs, catatan SOAPE, tindakan, odontogram (gigi dewasa & anak), diagnosa ICD-9/ICD-10, resep obat (termasuk racikan).
- Informed consent (umum & per-tindakan) dengan snapshot teks persetujuan saat ditandatangani.
- Tanda tangan digital sederhana (canvas, bukan tanda tangan elektronik tersertifikasi) untuk dokter, pasien, dan wali.
- Cetak surat keterangan sehat/sakit/rujukan, serta surat keterangan mandiri (tanpa harus jadi pasien terdaftar).

**Billing, Keuangan & Farmasi**
- Billing per kunjungan, riwayat pembayaran, ledger transaksi (kasir manual, POS, pembayaran supplier).
- POS farmasi dengan harga terkunci ke master produk, potongan stok FEFO (first-expired-first-out), receive order dari supplier.
- Laporan finansial, klinis, dan kepegawaian dengan grafik (ApexCharts).

**Kepegawaian & Payroll**
- Data karyawan, jadwal kerja, pengajuan & approval cuti.
- Payroll dengan perhitungan BPJS Kesehatan/Ketenagakerjaan dan PPh21 (skema TER, PMK 168/2023).

**Layanan Lanjutan (gaya rumah sakit)**
- IGD dengan antrian berbasis triase, bukan urutan kedatangan.
- Lab & Radiologi: permintaan pemeriksaan dari tab Pemeriksaan, dikerjakan dari worklist tersendiri, hasil & harga di-snapshot saat order dibuat.
- Rawat Inap: admisi pasien yang sudah terdaftar, billing terakumulasi otomatis (tarif kamar per hari + tindakan + diagnosa + lab + radiologi + resep selama masa rawat).

**Multi-Cabang & Portal Pasien**
- Data klinis/transaksional di-scope per cabang (`Klien`) — pengguna hanya melihat data cabangnya sendiri.
- Portal pasien mandiri (tanpa akun staf) untuk melihat riwayat kunjungan, resep, tagihan, dan booking jadwal dokter sendiri.

**Akses & Audit**
- Hak akses berbasis modul (bukan role tetap) — setiap user punya daftar modul yang bisa diakses, diatur granular per menu.
- Log aktivitas pada model-model finansial/medis penting (pendaftaran, diagnosa, payroll, jadwal kerja, cuti, dll).

> Daftar fitur di atas mengikuti kondisi kode saat ini. Untuk detail arsitektur/konvensi teknis, lihat [`CLAUDE.md`](CLAUDE.md).

## Teknologi

- **Backend:** Laravel 12, PHP 8.2+, Eloquent ORM.
- **Database:** SQLite secara default untuk development (MySQL/MariaDB juga didukung).
- **Frontend:** Blade (server-rendered, bukan SPA framework) + jQuery, Bootstrap admin template, DataTables, Select2, SweetAlert2, ApexCharts, FilePond, flatpickr — semua dimuat lewat `<script>`/`<link>` langsung, **tidak ada build step** (Vite/Tailwind sudah dilepas dari skeleton bawaan).
- **Navigasi terasa seperti SPA** lewat [Turbo Drive](https://turbo.hotwired.dev/) (intersep klik link/submit form, swap `<body>` lewat fetch) tanpa menulis ulang frontend ke framework JS.
- **PDF:** `barryvdh/laravel-dompdf` untuk semua cetakan (surat, invoice, dll), lewat helper `viewOrPdf()`.

## Instalasi

### Kebutuhan

- PHP 8.2 atau lebih baru + Composer.
- Node.js (hanya dipakai untuk `concurrently`, process runner `composer dev` — **bukan** untuk build frontend).
- Ekstensi PHP standar Laravel (`pdo_sqlite` atau `pdo_mysql`, `mbstring`, `openssl`, dll).

### Langkah

```bash
# 1. Clone repo lalu masuk ke foldernya
git clone <url-repo-ini> jivident
cd jivident

# 2. Install dependency PHP
composer install

# 3. Setup awal — bikin .env, generate APP_KEY, lalu migrate
composer setup
```

`composer setup` otomatis:
1. Menyalin `.env.example` menjadi `.env` (kalau belum ada).
2. Generate `APP_KEY`.
3. Menjalankan `php artisan migrate --force`.

Setelah itu, **set `TYPE_LOCAL_SERVER=2`** di `.env` seperti disebut di bagian atas, supaya tidak terkena gate lisensi.

Kalau mau data contoh (wilayah Indonesia untuk autocomplete alamat, ICD-9/10, kategori produk, dll) ikut terisi:

```bash
php artisan migrate:fresh --seed
```

## Menjalankan Secara Lokal

```bash
composer dev
```

Perintah ini menjalankan tiga proses sekaligus (lewat `concurrently`): `php artisan serve`, `php artisan queue:listen`, dan `php artisan pail` (log tailing) — jadi satu terminal saja sudah cukup untuk development sehari-hari.

Aplikasi bisa diakses di `http://localhost:8000` (atau sesuai `APP_URL`/port yang dipakai `php artisan serve`).

## Login Default

Migrasi awal (`2024_07_17_005400_create_users_table.php`) sudah membuat beberapa user contoh:

| Username     | Password   | Catatan                 |
|--------------|------------|--------------------------|
| `superadmin` | `12341234` | Akses penuh              |
| `user1`      | `12341234` | Contoh user tambahan     |
| `user2`      | `12341234` | Contoh user tambahan     |

**Ganti password ini segera** kalau aplikasi dipakai di luar lingkungan development lokal.

## Lisensi & Mode Akses

Aplikasi ini punya middleware global (`App\Http\Middleware\AuthApiMiddleware`) yang jalan di hampir semua request, mengecek baris `serial` di database dan — kalau perlu divalidasi ulang — melakukan request ke server lisensi di `AUTH_SERVER` (env). Tanpa serial yang valid, user akan diarahkan ke halaman `license`.

Ada dua cara menjalankan aplikasi ini:

1. **Mode berlisensi (default):** butuh `serial_code` yang valid, divalidasi berkala ke `AUTH_SERVER`. Dipakai kalau aplikasi di-deploy sebagai produk berlisensi ke pihak lain.
2. **Mode self-hosted / gratis:** set
   ```env
   TYPE_LOCAL_SERVER=2
   ```
   di `.env`. Gate lisensi dilewati sepenuhnya — tidak ada pengecekan serial maupun koneksi ke server manapun. **Ini mode yang disarankan kalau kamu menjalankan aplikasi ini untuk dirimu sendiri / instansi sendiri, bukan sebagai produk yang dijual ke klinik lain.**

> Catatan untuk testing: `phpunit.xml` memang mem-pin `TYPE_LOCAL_SERVER=0` supaya test suite tidak diam-diam ikut nilai `.env` lokal developer — jadi nilai di `.env` kamu sendiri tidak memengaruhi hasil `composer test`.

## Testing & Code Style

```bash
# Jalankan seluruh test suite
composer test

# Test file/method tertentu
php artisan test tests/Unit/ProductQtyHelperTest.php
php artisan test --filter=nama_method_test

# Cek/format code style (Laravel Pint)
vendor/bin/pint --test   # cek saja, tanpa ubah file
vendor/bin/pint          # format otomatis
```

## Struktur Singkat

- `app/Http/Controllers/Website/` — seluruh business logic controller, satu namespace untuk semua fitur.
- `app/Helpers/` — logic inti yang dipakai lintas controller (stok produk/FEFO, ledger transaksi, POS, payroll, booking dokter, backup database manual).
- `routes/web.php` — semua route didefinisikan di sini (tidak ada `api.php`), dikelompokkan per modul dengan middleware `checkAccess:<KODE_MODUL>`.
- `resources/views/pages/` — satu folder per modul/menu, mengikuti pola `index` (DataTable kosong) + endpoint `getList` (server-side DataTables).

Untuk penjelasan arsitektur yang lebih dalam (konvensi, keputusan desain, known gaps), baca [`CLAUDE.md`](CLAUDE.md) di root project — dokumen itu ditulis untuk membantu siapa pun (manusia atau AI assistant) memahami codebase ini dengan cepat.
