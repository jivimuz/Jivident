<?php

namespace Database\Seeders\Pasien;

use Illuminate\Database\Seeder;
use App\Models\JenisKelamin;

class JenisKelaminSeeder extends Seeder
{
    public function run()
    {
        $jenisKelamin = [
            ['klienfk' => '1', 'kdkemenkes' => '1', 'jeniskelamin' => 'Laki-laki', 'reportdisplay' => 'Laki-laki'],
            ['klienfk' => '1', 'kdkemenkes' => '2', 'jeniskelamin' => 'Perempuan', 'reportdisplay' => 'Perempuan'],
            ['klienfk' => '1', 'kdkemenkes' => '3', 'jeniskelamin' => 'Tidak dapat ditentukan', 'reportdisplay' => 'Tidak dapat ditentukan'],
            ['klienfk' => '1', 'kdkemenkes' => '4', 'jeniskelamin' => 'Tidak mengisi', 'reportdisplay' => 'Tidak mengisi'],
            ['klienfk' => '1', 'kdkemenkes' => '0', 'jeniskelamin' => 'Tidak diketahui', 'reportdisplay' => 'Tidak diketahui'],
        ];

        JenisKelamin::insert($jenisKelamin);
    }
}
