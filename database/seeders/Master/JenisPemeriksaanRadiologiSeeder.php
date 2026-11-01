<?php

namespace Database\Seeders\Master;

use App\Models\JenisPemeriksaanRadiologi;
use Illuminate\Database\Seeder;

class JenisPemeriksaanRadiologiSeeder extends Seeder
{
    public function run(): void
    {
        if (JenisPemeriksaanRadiologi::count() > 0) {
            return;
        }

        $jenis = [
            ['nama' => 'Rontgen Panoramic Gigi', 'deskripsi' => 'Foto rontgen seluruh rahang & gigi dalam satu gambar', 'harga' => 150000],
            ['nama' => 'Rontgen Periapikal', 'deskripsi' => 'Foto rontgen gigi per area, untuk melihat akar & tulang penyangga', 'harga' => 75000],
            ['nama' => 'Rontgen Cephalometri', 'deskripsi' => 'Foto rontgen samping kepala, umum untuk perencanaan ortodonti', 'harga' => 250000],
            ['nama' => 'Rontgen Thorax (Dada)', 'deskripsi' => 'Foto rontgen dada untuk pemeriksaan paru & jantung', 'harga' => 180000],
            ['nama' => 'USG Abdomen', 'deskripsi' => 'Ultrasonografi rongga perut', 'harga' => 350000],
            ['nama' => 'CT Scan Kepala', 'deskripsi' => 'Pemeriksaan penunjang lanjutan untuk kasus rujukan', 'harga' => 1500000],
        ];

        foreach ($jenis as $item) {
            JenisPemeriksaanRadiologi::create(array_merge(['klienfk' => 1], $item));
        }
    }
}
