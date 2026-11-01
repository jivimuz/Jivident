<?php

namespace Database\Seeders\Master;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Katalog obat/produk apotek — cuma master (nama/kode/kategori/harga),
     * sengaja TANPA stok awal (`qty` tetap 0 di sini). Stok sungguhan
     * dikelola lewat `t_productqty` (lihat `ProductQtyHelper`), jadi
     * stok-in untuk data ini ada di seeder transaksional
     * (`ReceiveOrderDemoSeeder`), bukan di sini.
     *
     * kategorifk: 1=UMUM, 2=OBAT BEBAS (HIJAU), 3=OBAT BEBAS TERBATAS (BIRU),
     * 4=OBAT KERAS (MERAH), 5=OBAT NARKOTIKA, 6=OBAT TRADISIONAL/JAMU
     * (lihat ProductCategoriesSeeder).
     * unitfk: 1=Tablet, 2=Capsule, 3=Syrup, 4=Chewable Tablet, 7=Cream,
     * 8=Suspension (lihat UnitProductSeeder).
     * satuanfk: 2=Miligram, 5=Gram, 10=Milliliter (lihat SatuanProductSeeder).
     */
    public function run(): void
    {
        if (Product::count() > 0) {
            return;
        }

        $products = [
            ['code' => 'OBT-0001', 'name' => 'Paracetamol 500mg Tablet', 'strength' => 500, 'satuanfk' => 2, 'unitfk' => 1, 'kategorifk' => 2, 'harga_beli' => 150, 'hpp' => 170, 'harga_jual' => 500],
            ['code' => 'OBT-0002', 'name' => 'Amoxicillin 500mg Kapsul', 'strength' => 500, 'satuanfk' => 2, 'unitfk' => 2, 'kategorifk' => 4, 'is_antibiotik' => true, 'harga_beli' => 350, 'hpp' => 390, 'harga_jual' => 1000],
            ['code' => 'OBT-0003', 'name' => 'Ibuprofen 400mg Tablet', 'strength' => 400, 'satuanfk' => 2, 'unitfk' => 1, 'kategorifk' => 3, 'harga_beli' => 250, 'hpp' => 280, 'harga_jual' => 750],
            ['code' => 'OBT-0004', 'name' => 'Vitamin C 500mg Tablet Kunyah', 'strength' => 500, 'satuanfk' => 2, 'unitfk' => 4, 'kategorifk' => 2, 'harga_beli' => 300, 'hpp' => 330, 'harga_jual' => 1000],
            ['code' => 'OBT-0005', 'name' => 'OBH Combi Sirup Obat Batuk 100ml', 'strength' => 100, 'satuanfk' => 10, 'unitfk' => 3, 'kategorifk' => 2, 'harga_beli' => 12000, 'hpp' => 13000, 'harga_jual' => 22000],
            ['code' => 'OBT-0006', 'name' => 'Antasida DOEN Suspensi 60ml', 'strength' => 60, 'satuanfk' => 10, 'unitfk' => 8, 'kategorifk' => 2, 'harga_beli' => 9000, 'hpp' => 9800, 'harga_jual' => 16000],
            ['code' => 'OBT-0007', 'name' => 'Betadine Salep Antiseptik 20g', 'strength' => 20, 'satuanfk' => 5, 'unitfk' => 7, 'kategorifk' => 2, 'harga_beli' => 14000, 'hpp' => 15000, 'harga_jual' => 25000],
            ['code' => 'OBT-0008', 'name' => 'Amlodipine 10mg Tablet', 'strength' => 10, 'satuanfk' => 2, 'unitfk' => 1, 'kategorifk' => 4, 'is_kronis' => true, 'harga_beli' => 400, 'hpp' => 440, 'harga_jual' => 1200],
            ['code' => 'OBT-0009', 'name' => 'Metformin 500mg Tablet', 'strength' => 500, 'satuanfk' => 2, 'unitfk' => 1, 'kategorifk' => 4, 'is_kronis' => true, 'harga_beli' => 200, 'hpp' => 220, 'harga_jual' => 600],
            ['code' => 'OBT-0010', 'name' => 'CTM 4mg Tablet', 'strength' => 4, 'satuanfk' => 2, 'unitfk' => 1, 'kategorifk' => 3, 'harga_beli' => 100, 'hpp' => 115, 'harga_jual' => 300],
            ['code' => 'OBT-0011', 'name' => 'Vitamin B Complex Tablet', 'strength' => null, 'satuanfk' => null, 'unitfk' => 1, 'kategorifk' => 2, 'harga_beli' => 200, 'hpp' => 220, 'harga_jual' => 600],
            ['code' => 'OBT-0012', 'name' => 'Asam Mefenamat 500mg Tablet', 'strength' => 500, 'satuanfk' => 2, 'unitfk' => 1, 'kategorifk' => 3, 'harga_beli' => 220, 'hpp' => 245, 'harga_jual' => 700],
        ];

        foreach ($products as $product) {
            Product::create(array_merge([
                'klienfk' => 1,
                'is_fornas' => false,
                'is_generik' => true,
                'is_antibiotik' => false,
                'is_psikotropika' => false,
                'is_narkotika' => false,
                'is_kronis' => false,
                'qty' => 0,
            ], $product));
        }
    }
}
