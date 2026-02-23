<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SatuanProductSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $rows = [
            // =========================
            // BERAT (base_unit = g)
            // base_banding = jumlah unit dalam 1 gram
            // =========================
            ['category' => 'weight', 'base_unit' => 'g', 'code' => 'mcg', 'name' => 'Microgram', 'base_banding' => 1000000],
            ['category' => 'weight', 'base_unit' => 'g', 'code' => 'mg',  'name' => 'Miligram',  'base_banding' => 1000],
            ['category' => 'weight', 'base_unit' => 'g', 'code' => 'cg',  'name' => 'Centigram', 'base_banding' => 100],
            ['category' => 'weight', 'base_unit' => 'g', 'code' => 'dg',  'name' => 'Decigram',  'base_banding' => 10],
            ['category' => 'weight', 'base_unit' => 'g', 'code' => 'g',   'name' => 'Gram',      'base_banding' => 1],
            ['category' => 'weight', 'base_unit' => 'g', 'code' => 'dag', 'name' => 'Decagram',  'base_banding' => 0.1],
            ['category' => 'weight', 'base_unit' => 'g', 'code' => 'hg',  'name' => 'Hectogram', 'base_banding' => 0.01],
            ['category' => 'weight', 'base_unit' => 'g', 'code' => 'kg',  'name' => 'Kilogram',  'base_banding' => 0.001],

            // =========================
            // VOLUME (base_unit = l)
            // base_banding = jumlah unit dalam 1 liter
            // =========================
            ['category' => 'volume', 'base_unit' => 'l', 'code' => 'ul',  'name' => 'Microliter',  'base_banding' => 1000000],
            ['category' => 'volume', 'base_unit' => 'l', 'code' => 'ml',  'name' => 'Milliliter',  'base_banding' => 1000],
            ['category' => 'volume', 'base_unit' => 'l', 'code' => 'cc',  'name' => 'Cubic Centimeter (cc)', 'base_banding' => 1000], // 1 cc = 1 mL (tepat)
            ['category' => 'volume', 'base_unit' => 'l', 'code' => 'cl',  'name' => 'Centiliter',  'base_banding' => 100],
            ['category' => 'volume', 'base_unit' => 'l', 'code' => 'dl',  'name' => 'Deciliter',   'base_banding' => 10],
            ['category' => 'volume', 'base_unit' => 'l', 'code' => 'l',   'name' => 'Liter',       'base_banding' => 1],
            ['category' => 'volume', 'base_unit' => 'l', 'code' => 'dal', 'name' => 'Decaliter',   'base_banding' => 0.1],
            ['category' => 'volume', 'base_unit' => 'l', 'code' => 'hl',  'name' => 'Hectoliter',  'base_banding' => 0.01],
            ['category' => 'volume', 'base_unit' => 'l', 'code' => 'kl',  'name' => 'Kiloliter',   'base_banding' => 0.001],
        ];

        $data = array_map(function ($row) use ($now) {
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
            return $row;
        }, $rows);

        DB::table('m_satuanproducts')->insertOrIgnore($data);
    }
}
