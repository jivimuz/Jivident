<?php

namespace Database\Seeders\Pasien;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class KoordinatSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now()->toDateTimeString();
        $path = database_path('seeders/csv/koordinat.csv');

        if (!File::exists($path)) {
            $this->command->error("File CSV tidak ditemukan di: $path");
            return;
        }

        $file = fopen($path, 'r');
        $header = ['0', '1', 'name', '3', '4', 'longitude', 'latitude', '7'];
        $data = [];

        // Skip header jika ada
        fgetcsv($file);

        while (($row = fgetcsv($file)) !== false) {
            $row = array_combine($header, $row);

            unset($row['0'], $row['1'], $row['3'], $row['4'], $row['7']);
            $row['klienfk'] = 1;
            $row['created_at'] = $now;
            $row['updated_at'] = $now;

            $data[] = $row;
        }

        fclose($file);

        if (!empty($data)) {
            DB::table('m_koordinat')->insert($data);
        }
    }
}
