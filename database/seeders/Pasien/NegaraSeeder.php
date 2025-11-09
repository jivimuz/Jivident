<?php

namespace Database\Seeders\Pasien;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NegaraSeeder extends Seeder
{
    public function run(): void
    {
        $negara = [
            ['klienfk' => 1, 'negara' => 'Indonesia', 'reportdisplay' => 'Indonesia'],
            ['klienfk' => 1, 'negara' => 'Malaysia', 'reportdisplay' => 'Malaysia'],
            ['klienfk' => 1, 'negara' => 'Singapura', 'reportdisplay' => 'Singapura'],
            ['klienfk' => 1, 'negara' => 'Thailand', 'reportdisplay' => 'Thailand'],
            ['klienfk' => 1, 'negara' => 'Filipina', 'reportdisplay' => 'Filipina'],
            ['klienfk' => 1, 'negara' => 'Vietnam', 'reportdisplay' => 'Vietnam'],
            ['klienfk' => 1, 'negara' => 'Brunei Darussalam', 'reportdisplay' => 'Brunei Darussalam'],
            ['klienfk' => 1, 'negara' => 'Laos', 'reportdisplay' => 'Laos'],
            ['klienfk' => 1, 'negara' => 'Myanmar', 'reportdisplay' => 'Myanmar'],
            ['klienfk' => 1, 'negara' => 'Timor Leste', 'reportdisplay' => 'Timor Leste'],
            ['klienfk' => 1, 'negara' => 'Australia', 'reportdisplay' => 'Australia'],
            ['klienfk' => 1, 'negara' => 'Amerika Serikat', 'reportdisplay' => 'Amerika Serikat'],
            ['klienfk' => 1, 'negara' => 'Inggris', 'reportdisplay' => 'Inggris'],
            ['klienfk' => 1, 'negara' => 'Kanada', 'reportdisplay' => 'Kanada'],
            ['klienfk' => 1, 'negara' => 'Jepang', 'reportdisplay' => 'Jepang'],
            ['klienfk' => 1, 'negara' => 'Korea Selatan', 'reportdisplay' => 'Korea Selatan'],
            ['klienfk' => 1, 'negara' => 'Tiongkok', 'reportdisplay' => 'Tiongkok'],
            ['klienfk' => 1, 'negara' => 'India', 'reportdisplay' => 'India'],
            ['klienfk' => 1, 'negara' => 'Arab Saudi', 'reportdisplay' => 'Arab Saudi'],
            ['klienfk' => 1, 'negara' => 'Uni Emirat Arab', 'reportdisplay' => 'Uni Emirat Arab'],
            ['klienfk' => 1, 'negara' => 'Mesir', 'reportdisplay' => 'Mesir'],
            ['klienfk' => 1, 'negara' => 'Afrika Selatan', 'reportdisplay' => 'Afrika Selatan'],
            ['klienfk' => 1, 'negara' => 'Perancis', 'reportdisplay' => 'Perancis'],
            ['klienfk' => 1, 'negara' => 'Jerman', 'reportdisplay' => 'Jerman'],
            ['klienfk' => 1, 'negara' => 'Belanda', 'reportdisplay' => 'Belanda'],
            ['klienfk' => 1, 'negara' => 'Swiss', 'reportdisplay' => 'Swiss'],
            ['klienfk' => 1, 'negara' => 'Italia', 'reportdisplay' => 'Italia'],
            ['klienfk' => 1, 'negara' => 'Spanyol', 'reportdisplay' => 'Spanyol'],
            ['klienfk' => 1, 'negara' => 'Lainnya', 'reportdisplay' => 'Lainnya'],
        ];

        foreach ($negara as &$n) {
            $n['created_at'] = now();
            $n['updated_at'] = now();
        }

        DB::table('m_negara')->insert($negara);
    }
}
