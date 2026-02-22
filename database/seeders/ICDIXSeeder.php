<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ICDIXSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $data = [
            [
                'klienfk' => 1,
                'name' => 'Indeterminate Leprosy',
                'code' => 'DIX1',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'klienfk' => 1,
                'name' => 'Tuberculoid Leprosy',
                'code' => 'DIX2',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'klienfk' => 1,
                'name' => 'Zoonotic Bacterial Disease',
                'code' => 'DIX3',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'klienfk' => 1,
                'name' => 'Pharyngeal Diphtheria',
                'code' => 'DIX4',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'klienfk' => 1,
                'name' => 'Cervicofacial Actinomycosis',
                'code' => 'DIX5',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('m_icdix')->insert($data);
    }
}
