<?php

namespace Database\Seeders\Transactional;

use App\Helpers\ProductQtyHelper;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReceiveOrderDemoSeeder extends Seeder
{
    /**
     * Stok masuk untuk katalog produk apotek (`ProductSeeder`) lewat
     * `ProductQtyHelper::stockIn()` langsung — bukan lewat alur
     * `ReceiveOrder`/`ReceiveOrderDetail` penuh (tidak ada dokumen
     * penerimaan/supplier yang disimulasikan, cuma hasil akhirnya: lot stok
     * di `t_productqty`), supaya seeder ini tetap sederhana. Tanggal
     * expired sengaja divariasikan (sebagian dekat, sebagian jauh) supaya
     * FEFO (`stockOutFefo`) ada sesuatu yang nyata untuk diuji saat POS
     * checkout memakai produk-produk ini.
     */
    public function run(): void
    {
        $products = Product::where('klienfk', 1)->get();
        if ($products->isEmpty()) {
            return;
        }

        $qtyHelper = app(ProductQtyHelper::class);

        foreach ($products as $index => $product) {
            $alreadyStocked = DB::table('t_productqty')
                ->where('product_id', $product->id)
                ->where('source', 'receive')
                ->exists();
            if ($alreadyStocked) {
                continue;
            }

            // Dua lot per produk dengan tanggal expired beda, biar FEFO
            // benar-benar punya >1 lot untuk dipilih.
            $qtyHelper->stockIn([
                'product_id' => $product->id,
                'location_id' => 1,
                'qty' => 100,
                'source' => 'receive',
                'expired_date' => now()->addMonths(6 + $index)->toDateString(),
            ]);
            $qtyHelper->stockIn([
                'product_id' => $product->id,
                'location_id' => 1,
                'qty' => 50,
                'source' => 'receive',
                'expired_date' => now()->addMonths(18 + $index)->toDateString(),
            ]);
        }
    }
}
