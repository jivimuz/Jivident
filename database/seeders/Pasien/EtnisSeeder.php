<?php

namespace Database\Seeders\Pasien;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EtnisSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('m_etnis')->insert([
            ['klienfk' => '1', 'etnis' => 'Batak', 'reportdisplay' => 'Batak', 'created_at' => now(), 'updated_at' => now()],
            ['klienfk' => '1', 'etnis' => 'Melayu', 'reportdisplay' => 'Melayu', 'created_at' => now(), 'updated_at' => now()],
            ['klienfk' => '1', 'etnis' => 'Jawa', 'reportdisplay' => 'Jawa', 'created_at' => now(), 'updated_at' => now()],
            ['klienfk' => '1', 'etnis' => 'Sunda', 'reportdisplay' => 'Sunda', 'created_at' => now(), 'updated_at' => now()],
            ['klienfk' => '1', 'etnis' => 'Betawi', 'reportdisplay' => 'Betawi', 'created_at' => now(), 'updated_at' => now()],
            ['klienfk' => '1', 'etnis' => 'Dayak', 'reportdisplay' => 'Dayak', 'created_at' => now(), 'updated_at' => now()],
            ['klienfk' => '1', 'etnis' => 'Asmat', 'reportdisplay' => 'Asmat', 'created_at' => now(), 'updated_at' => now()],
            ['klienfk' => '1', 'etnis' => 'Bugis', 'reportdisplay' => 'Bugis', 'created_at' => now(), 'updated_at' => now()],
            ['klienfk' => '1', 'etnis' => 'Minang', 'reportdisplay' => 'Minang', 'created_at' => now(), 'updated_at' => now()],
            ['klienfk' => '1', 'etnis' => 'Baduy', 'reportdisplay' => 'Baduy', 'created_at' => now(), 'updated_at' => now()],
            ['klienfk' => '1', 'etnis' => 'Bali', 'reportdisplay' => 'Bali', 'created_at' => now(), 'updated_at' => now()],
            ['klienfk' => '1', 'etnis' => 'Ambon', 'reportdisplay' => 'Ambon', 'created_at' => now(), 'updated_at' => now()],
            ['klienfk' => '1', 'etnis' => 'Tengger', 'reportdisplay' => 'Tengger', 'created_at' => now(), 'updated_at' => now()],
            ['klienfk' => '1', 'etnis' => 'Sasak', 'reportdisplay' => 'Sasak', 'created_at' => now(), 'updated_at' => now()],
            ['klienfk' => '1', 'etnis' => 'Sumbawa', 'reportdisplay' => 'Sumbawa', 'created_at' => now(), 'updated_at' => now()],
            ['klienfk' => '1', 'etnis' => 'Flores', 'reportdisplay' => 'Flores', 'created_at' => now(), 'updated_at' => now()],
            ['klienfk' => '1', 'etnis' => 'Toraja', 'reportdisplay' => 'Toraja', 'created_at' => now(), 'updated_at' => now()],
            ['klienfk' => '1', 'etnis' => 'Osing', 'reportdisplay' => 'Osing', 'created_at' => now(), 'updated_at' => now()],
            ['klienfk' => '1', 'etnis' => 'Mandar', 'reportdisplay' => 'Mandar', 'created_at' => now(), 'updated_at' => now()],
            ['klienfk' => '1', 'etnis' => 'Lain-lain', 'reportdisplay' => 'Lain-lain', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
