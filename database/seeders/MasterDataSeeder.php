<?php

namespace Database\Seeders;

use Database\Seeders\Master\ExtendedMasterDataSeeder;
use Database\Seeders\Master\JadwalKerjaSeeder;
use Database\Seeders\Master\JenisPemeriksaanLabSeeder;
use Database\Seeders\Master\JenisPemeriksaanRadiologiSeeder;
use Database\Seeders\Master\ProductSeeder;
use Database\Seeders\Master\RuangPraktekSeeder;
use Database\Seeders\Master\SupplierSeeder;
use Database\Seeders\Master\TindakanSeeder;
use Illuminate\Database\Seeder;

/**
 * Master data yang sebelumnya kosong/sangat minim di DB baru — Supplier,
 * Tindakan, katalog Produk apotek, jenis pemeriksaan Lab/Radiologi, Ruang
 * Praktek, dan template Jadwal Kerja. Master yang SUDAH punya seeder sendiri
 * (Klien, data wilayah, ICD-9/10, kategori/satuan/unit produk, dll — lihat
 * `DatabaseSeeder`) tidak diulang di sini.
 *
 * Setiap sub-seeder idempotent — kebanyakan skip total kalau tabelnya sudah
 * tidak kosong; `TindakanSeeder`/`RuangPraktekSeeder` skip per-baris
 * (cocokkan nama/kode) supaya tetap bisa menambah entri baru ke tabel yang
 * sudah pernah disentuh user secara manual. Aman dipanggil berkali-kali.
 */
class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SupplierSeeder::class,
            TindakanSeeder::class,
            ProductSeeder::class,
            JenisPemeriksaanLabSeeder::class,
            JenisPemeriksaanRadiologiSeeder::class,
            RuangPraktekSeeder::class,
            JadwalKerjaSeeder::class,
            ExtendedMasterDataSeeder::class,
            ModuleSeeder::class,
        ]);
    }
}
