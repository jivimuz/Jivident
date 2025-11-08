<?php

namespace Database\Seeders\Pasien;

use Illuminate\Database\Seeder;
use App\Models\Pekerjaan;

class PekerjaanSeeder extends Seeder
{
    public function run()
    {
        $pekerjaan = [
            ['klienfk' => '1', 'kdkemenkes' => '0', 'pekerjaan' => 'Tidak bekerja', 'reportdisplay' => 'Tidak bekerja'],
            ['klienfk' => '1', 'kdkemenkes' => '1', 'pekerjaan' => 'PNS', 'reportdisplay' => 'PNS'],
            ['klienfk' => '1', 'kdkemenkes' => '2', 'pekerjaan' => 'TNI/POLRI', 'reportdisplay' => 'TNI/POLRI'],
            ['klienfk' => '1', 'kdkemenkes' => '3', 'pekerjaan' => 'BUMN', 'reportdisplay' => 'BUMN'],
            ['klienfk' => '1', 'kdkemenkes' => '4', 'pekerjaan' => 'Pegawai Swasta/Wirausaha', 'reportdisplay' => 'Pegawai Swasta/Wirausaha'],
            ['klienfk' => '1', 'kdkemenkes' => '5', 'pekerjaan' => 'Lain-lain', 'reportdisplay' => 'Lain-lain'],
        ];

        // Bisa pakai insertMany (insert multiple sekaligus)
        Pekerjaan::insert($pekerjaan);
    }
}
