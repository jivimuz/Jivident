<?php

namespace Database\Seeders\Pasien;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KebangsaanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('m_kebangsaan')->insert([
            ['klienfk' => '1', 'kebangsaan' => 'WNI', 'reportdisplay' => 'WNI', 'created_at' => now(), 'updated_at' => now()],
            ['klienfk' => '2', 'kebangsaan' => 'WNA', 'reportdisplay' => 'WNA', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
