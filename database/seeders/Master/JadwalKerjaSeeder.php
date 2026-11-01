<?php

namespace Database\Seeders\Master;

use App\Models\JadwalKerja;
use Illuminate\Database\Seeder;

class JadwalKerjaSeeder extends Seeder
{
    public function run(): void
    {
        if (JadwalKerja::count() > 0) {
            return;
        }

        $shifts = [
            ['kode' => 'PAGI', 'nama' => 'Shift Pagi', 'jammasuk' => '07:00', 'jampulang' => '14:00'],
            ['kode' => 'SIANG', 'nama' => 'Shift Siang', 'jammasuk' => '14:00', 'jampulang' => '21:00'],
            ['kode' => 'MALAM', 'nama' => 'Shift Malam', 'jammasuk' => '21:00', 'jampulang' => '07:00'],
            ['kode' => 'REGULER', 'nama' => 'Jam Kerja Reguler', 'jammasuk' => '08:00', 'jampulang' => '17:00'],
        ];

        foreach ($shifts as $shift) {
            JadwalKerja::create($shift);
        }
    }
}
