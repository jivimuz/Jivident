<?php

namespace Database\Seeders\Pasien;

use App\Models\Study;
use Illuminate\Database\Seeder;

class PendidikanSeeder extends Seeder
{
    public function run()
    {
        $pendidikan = [
            ['klienfk' => '1', 'kdkemenkes' => '1', 'name' => 'SD', 'reportdisplay' => 'SD'],
            ['klienfk' => '1', 'kdkemenkes' => '2', 'name' => 'SLTP sederajat', 'reportdisplay' => 'SLTP sederajat'],
            ['klienfk' => '1', 'kdkemenkes' => '3', 'name' => 'SLTA sederajat', 'reportdisplay' => 'SLTA sederajat'],
            ['klienfk' => '1', 'kdkemenkes' => '4', 'name' => 'D1-D3 sederajat', 'reportdisplay' => 'D1-D3 sederajat'],
            ['klienfk' => '1', 'kdkemenkes' => '5', 'name' => 'D4', 'reportdisplay' => 'D4'],
            ['klienfk' => '1', 'kdkemenkes' => '6', 'name' => 'S1', 'reportdisplay' => 'S1'],
            ['klienfk' => '1', 'kdkemenkes' => '7', 'name' => 'S2', 'reportdisplay' => 'S2'],
            ['klienfk' => '1', 'kdkemenkes' => '8', 'name' => 'S3', 'reportdisplay' => 'S3'],
            ['klienfk' => '1', 'kdkemenkes' => '0', 'name' => 'Tidak sekolah', 'reportdisplay' => 'Tidak sekolah'],
        ];

        Study::insert($pendidikan);
    }
}
