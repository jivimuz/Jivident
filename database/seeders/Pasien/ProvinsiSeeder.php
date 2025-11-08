<?php

namespace Database\Seeders\Pasien;

use Illuminate\Database\Seeder;
use App\Models\Provinsi;

class ProvinsiSeeder extends Seeder
{
    public function run()
    {
        $file = database_path('seeders/csv/provinsi_m.csv'); // pastikan path bener
        $header = ['id', 'provinsi', 'lat', 'long'];
        $data = [];

        if (($handle = fopen($file, 'r')) !== false) {
            fgetcsv($handle); // skip header jika perlu
            while (($row = fgetcsv($handle)) !== false) {
                $rowData = array_combine($header, $row);

                $data[] = [
                    'klienfk'     => 1,
                    'kdprovinsi'  => $rowData['id'],
                    'provinsi'    => $rowData['provinsi'],
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }
            fclose($handle);
        }

        Provinsi::insert($data);
    }
}
