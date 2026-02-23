<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $rows = [
            ['code' => 'TAB',       'name' => 'Tablet',],
            ['code' => 'CAP',       'name' => 'Capsule',],
            ['code' => 'SYRUP',     'name' => 'Syrup',],
            ['code' => 'CHEWTAB',   'name' => 'Chewable Tablet',],
            ['code' => 'ELIXIR',    'name' => 'Elixir',],
            ['code' => 'IVSOL',     'name' => 'Intravenous Solution',],
            ['code' => 'CRM',       'name' => 'Cream',],
            ['code' => 'SUSP',      'name' => 'Suspension',],
            ['code' => 'GEL',       'name' => 'Gel',],
            ['code' => '385057009', 'name' => 'Film-coated tablet',],
            ['code' => 'DRTAB',     'name' => 'Delayed Release Tablet',],
            ['code' => 'POWD',      'name' => 'Powder',],
            ['code' => '421701006', 'name' => 'Tablet for conventional release oral solution',],
            ['code' => 'ORDROP',    'name' => 'Oral Drops',],
            ['code' => 'ORTROCHE',  'name' => 'Lozenge/Oral Troche',],
            ['code' => 'OINT',      'name' => 'Ointment',],
            ['code' => 'ERENTCAP',  'name' => 'Extended Release Enteric Coated Capsule',],
            ['code' => 'INHLSOL',   'name' => 'Inhalant Solution',],
            ['code' => '761877007', 'name' => 'Effervescent tablet',],
            ['code' => 'GRAN',      'name' => 'Granules',],
            ['code' => 'INHLPWD',   'name' => 'Inhalant Powder',],
            ['code' => 'ECTAB',     'name' => 'Enteric Coated Tablet',],
            ['code' => 'ORINHL',    'name' => 'Oral Inhalant',],
            ['code' => 'OPDROP',    'name' => 'Ophthalmic Drops',],
            ['code' => 'VAGCRM',    'name' => 'Vaginal Cream',],
            ['code' => 'OTDROP',    'name' => 'Otic Drops',],
            ['code' => 'CAPLET',    'name' => 'Caplet',],
            ['code' => '385174007', 'name' => 'Pessary',],
            ['code' => 'TOPPWD',    'name' => 'Topical Powder',],
            ['code' => 'DISINTAB',  'name' => 'Disintegrating Tablet',],
            ['code' => 'ENEMA',     'name' => 'Enema',],
            ['code' => '764296002', 'name' => 'Prolonged-release',],
            ['code' => '421366001', 'name' => 'Tablet for oral suspension',],
            ['code' => 'RINSE',     'name' => 'Mouthwash/Rinse',],
            ['code' => 'DERMSPRY',  'name' => 'Dermal Spray',],
            ['code' => 'VAGSUPP',   'name' => 'Vaginal Suppository',],
            ['code' => 'OPOINT',    'name' => 'Ophthalmic Ointment',],
            ['code' => 'NASSPRY',   'name' => 'Nasal Spray',],
            ['code' => 'SOL',       'name' => 'Solution',],
            ['code' => 'MDINHL',    'name' => 'Metered Dose Inhaler',],
            ['code' => 'NDROP',     'name' => 'Nasal Drops',],
        ];

        // Tambahkan timestamps
        $data = array_map(function ($row) use ($now) {
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
            return $row;
        }, $rows);

        // Kalau ada unique (klienfk + kdsatuan), insertOrIgnore aman saat rerun
        DB::table('m_unitproducts')->insertOrIgnore($data);
    }
}
