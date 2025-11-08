<?php

namespace Database\Seeders\Pasien;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KlienSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now()->toDateTimeString();

        $klien = [
            [
                'organisasifk' => 1,
                'kodeklinik' => 'JVD',
                'tipe' => 'A',
                'nama' => 'Jivident',
                'alamat' => 'Cirebon',
                'nohp' => '+6282120741970',
                'email' => 'jivirasgal@mail.com',
            ],
        ];

        DB::table('m_klien')->insert($klien);
    }
}
