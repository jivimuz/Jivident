<?php

namespace Database\Seeders\Pasien;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class KotaKabupatenSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now()->toDateTimeString();
        $path = database_path('seeders/csv/kotakabupaten_m.csv');

        if (!File::exists($path)) {
            $this->command->error("File CSV tidak ditemukan di: $path");
            return;
        }

        $file = fopen($path, 'r');
        $header = ['id', 'provinsifk', 'kotakabupaten', 'lat', 'long'];
        $data = [];

        // Lewati baris header (kalau CSV ada header)
        fgetcsv($file);

        while (($row = fgetcsv($file)) !== false) {
            $row = array_combine($header, $row);

            unset($row['lat'], $row['long']);
            $row['klienfk'] = 1;
            $row['kdkotakabupaten'] = substr($row['id'], 0, 2) . '.' . substr($row['id'], 2, 2);
            $row['created_at'] = $now;
            $row['updated_at'] = $now;

            $data[] = $row;
        }

        fclose($file);

        if (!empty($data)) {
            DB::table('m_kotakabupaten')->insert($data);
        }
    }
}
