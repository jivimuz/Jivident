<?php

namespace Database\Seeders\Pasien;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AgamaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('m_religion')->insert([
            ['klienfk' => '1', 'kdkemenkes' => '1', 'name' => 'Islam', 'reportdisplay' => 'Islam', 'created_at' => now(), 'updated_at' => now()],
            ['klienfk' => '1', 'kdkemenkes' => '2', 'name' => 'Kristen', 'reportdisplay' => 'Kristen', 'created_at' => now(), 'updated_at' => now()],
            ['klienfk' => '1', 'kdkemenkes' => '3', 'name' => 'Katolik', 'reportdisplay' => 'Katolik', 'created_at' => now(), 'updated_at' => now()],
            ['klienfk' => '1', 'kdkemenkes' => '4', 'name' => 'Hindu', 'reportdisplay' => 'Hindu', 'created_at' => now(), 'updated_at' => now()],
            ['klienfk' => '1', 'kdkemenkes' => '5', 'name' => 'Buddha', 'reportdisplay' => 'Buddha', 'created_at' => now(), 'updated_at' => now()],
            ['klienfk' => '1', 'kdkemenkes' => '6', 'name' => 'Konghucu', 'reportdisplay' => 'Konghucu', 'created_at' => now(), 'updated_at' => now()],
            ['klienfk' => '1', 'kdkemenkes' => '7', 'name' => 'Penghayat', 'reportdisplay' => 'Penghayat', 'created_at' => now(), 'updated_at' => now()],
            ['klienfk' => '1', 'kdkemenkes' => '8', 'name' => 'Lain-lain', 'reportdisplay' => 'Lain-lain', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
