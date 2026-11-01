<?php

namespace Database\Seeders\Master;

use App\Models\JenisPemeriksaanLab;
use Illuminate\Database\Seeder;

class JenisPemeriksaanLabSeeder extends Seeder
{
    public function run(): void
    {
        if (JenisPemeriksaanLab::count() > 0) {
            return;
        }

        $jenis = [
            ['nama' => 'Hemoglobin (Hb)', 'satuan' => 'g/dL', 'nilai_rujukan' => 'L: 13.0-17.0, P: 12.0-15.0', 'harga' => 35000],
            ['nama' => 'Leukosit', 'satuan' => '/uL', 'nilai_rujukan' => '4.000-10.000', 'harga' => 35000],
            ['nama' => 'Trombosit', 'satuan' => '/uL', 'nilai_rujukan' => '150.000-450.000', 'harga' => 35000],
            ['nama' => 'Gula Darah Sewaktu (GDS)', 'satuan' => 'mg/dL', 'nilai_rujukan' => '<200', 'harga' => 25000],
            ['nama' => 'Gula Darah Puasa (GDP)', 'satuan' => 'mg/dL', 'nilai_rujukan' => '70-100', 'harga' => 25000],
            ['nama' => 'Kolesterol Total', 'satuan' => 'mg/dL', 'nilai_rujukan' => '<200', 'harga' => 40000],
            ['nama' => 'Asam Urat', 'satuan' => 'mg/dL', 'nilai_rujukan' => 'L: 3.4-7.0, P: 2.4-5.7', 'harga' => 30000],
            ['nama' => 'Ureum', 'satuan' => 'mg/dL', 'nilai_rujukan' => '10-50', 'harga' => 30000],
            ['nama' => 'Kreatinin', 'satuan' => 'mg/dL', 'nilai_rujukan' => 'L: 0.6-1.3, P: 0.5-1.1', 'harga' => 30000],
            ['nama' => 'SGOT', 'satuan' => 'U/L', 'nilai_rujukan' => '<40', 'harga' => 35000],
            ['nama' => 'SGPT', 'satuan' => 'U/L', 'nilai_rujukan' => '<41', 'harga' => 35000],
            ['nama' => 'Rapid Test Antigen COVID-19', 'satuan' => null, 'nilai_rujukan' => 'Negatif/Positif', 'harga' => 100000],
            ['nama' => 'Urine Lengkap', 'satuan' => null, 'nilai_rujukan' => 'Normal', 'harga' => 45000],
        ];

        foreach ($jenis as $item) {
            JenisPemeriksaanLab::create(array_merge(['klienfk' => 1], $item));
        }
    }
}
