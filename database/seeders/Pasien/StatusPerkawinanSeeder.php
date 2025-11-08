<?php

namespace Database\Seeders\Pasien;

use App\Models\MarialStatus;
use Illuminate\Database\Seeder;
use App\Models\StatusPerkawinan;

class StatusPerkawinanSeeder extends Seeder
{
    public function run()
    {
        $statusPerkawinan = [
            ['klienfk' => '1', 'kdkemenkes' => '1', 'name' => 'Belum Kawin', 'reportdisplay' => 'Belum Kawin'],
            ['klienfk' => '1', 'kdkemenkes' => '2', 'name' => 'Kawin', 'reportdisplay' => 'Kawin'],
            ['klienfk' => '1', 'kdkemenkes' => '3', 'name' => 'Cerai Hidup', 'reportdisplay' => 'Cerai Hidup'],
            ['klienfk' => '1', 'kdkemenkes' => '4', 'name' => 'Cerai Mati', 'reportdisplay' => 'Cerai Mati'],
        ];

        MarialStatus::insert($statusPerkawinan);
    }
}
