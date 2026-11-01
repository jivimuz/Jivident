<?php

namespace Database\Seeders;

use Database\Seeders\Transactional\AppointmentDemoSeeder;
use Database\Seeders\Transactional\PatientVisitDemoSeeder;
use Database\Seeders\Transactional\PosSaleDemoSeeder;
use Database\Seeders\Transactional\RawatInapDemoSeeder;
use Database\Seeders\Transactional\ReceiveOrderDemoSeeder;
use Illuminate\Database\Seeder;

/**
 * Data transaksional contoh (kunjungan pasien, penjualan apotek, rawat inap,
 * appointment) — DIPISAH dari `DatabaseSeeder` secara sengaja, tidak
 * otomatis jalan lewat `php artisan migrate --seed`/`composer setup`. Ini
 * data demo/dev untuk melihat fitur jalan dengan data nyata, bukan baseline
 * yang pantas ikut terbawa ke setup produksi.
 *
 * Jalankan manual:
 *   php artisan db:seed --class=TransactionalSeeder
 *
 * Membutuhkan `MasterDataSeeder` (atau minimal master yang relevan: Produk,
 * Tindakan, Ruang Praktek) dan data pasien (`PasienSeeder`) sudah ada lebih
 * dulu — setiap sub-seeder di sini menunjuk entitas master yang sudah ada,
 * tidak membuat masternya sendiri.
 */
class TransactionalSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ReceiveOrderDemoSeeder::class,
            PatientVisitDemoSeeder::class,
            PosSaleDemoSeeder::class,
            AppointmentDemoSeeder::class,
            RawatInapDemoSeeder::class,
        ]);
    }
}
