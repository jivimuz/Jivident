<?php

namespace Database\Seeders;

use App\Models\ProductCategories;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductCategories::insertOrIgnore([
            ['name' => 'UMUM', 'description' => 'Barang Umum'],
            ['name' => 'OBAT BEBAS (HIJAU)', 'description' => 'Paling aman, bisa dibeli tanpa resep dokter di apotek atau toko obat.'],
            ['name' => 'OBAT BEBAS TERBATAS (BIRU)', 'description' => 'Obat keras yang masih bisa dibeli tanpa resep, namun terbatas jumlah dan aturan pakainya. Terdapat peringatan (P1-P6) pada kemasan.'],
            ['name' => 'OBAT KERAS (MERAH)', 'description' => 'Hanya dapat dibeli dengan resep dokter. Memiliki risiko tinggi jika disalahgunakan.'],
            ['name' => 'OBAT NARKOTIKA (MERAH +)', 'description' => 'Zat yang berasal dari tanaman/sintetis, dapat menurunkan kesadaran dan menyebabkan ketergantungan. Harus dengan resep khusus.'],
            ['name' => 'OBAT TRADISIONAL / JAMU', 'description' => 'Bahan alami (tumbuhan, mineral). Harus terdaftar BPOM.',],
        ]);
    }
}
