<?php

namespace Database\Seeders\Transactional;

use App\Helpers\PosSaleHelper;
use App\Models\PosSale;
use App\Models\Product;
use Illuminate\Database\Seeder;

class PosSaleDemoSeeder extends Seeder
{
    /**
     * Dua penjualan apotek contoh lewat `PosSaleHelper::checkout()` yang
     * sungguhan (bukan insert mentah) — supaya stok yang sudah di-stock-in
     * (`ReceiveOrderDemoSeeder`) benar-benar berkurang lewat FEFO, dan
     * ledger + `t_possales`/`t_possale_items`-nya konsisten dengan yang
     * dihasilkan checkout asli dari halaman POS.
     */
    public function run(): void
    {
        if (PosSale::where('klienfk', 1)->exists()) {
            return;
        }

        $products = Product::where('klienfk', 1)->orderBy('id')->take(4)->get();
        if ($products->count() < 2) {
            return;
        }

        $posHelper = app(PosSaleHelper::class);

        $posHelper->checkout([
            'fk_payment' => 1,
            'buyer_name' => 'Pembeli Umum',
            'items' => [
                ['product_id' => $products[0]->id, 'qty' => 2],
                ['product_id' => $products[1]->id, 'qty' => 1],
            ],
        ]);

        if ($products->count() >= 4) {
            $posHelper->checkout([
                'fk_payment' => 3,
                'buyer_name' => 'Rina Amalia',
                'buyer_phone' => '081234567890',
                'items' => [
                    ['product_id' => $products[2]->id, 'qty' => 1, 'discount' => 500],
                    ['product_id' => $products[3]->id, 'qty' => 3],
                ],
            ]);
        }
    }
}
