<?php

namespace Database\Seeders;

use App\Models\MarialStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PasienSeeder extends Seeder
{
    public function run(): void
    {
        $file = database_path('seeders/csv/pasien_m.csv');

        if (!file_exists($file) || !is_readable($file)) {
            throw new \Exception("File CSV tidak ditemukan atau tidak bisa dibaca: {$file}");
        }

        $header = [
            "id",
            "klienfk",
            "norm",
            "nik",
            "nama",
            "umur",
            "nobpjs",
            "nohp",
            "jeniskelaminfk",
            "tempatlahir",
            "tgllahir",
            "namaayah",
            "namaibu",
            "fk_religion",
            "fk_maritalstatus",
            "golongandarah",
            "noasuransilain",
            "fk_study",
            "pekerjaanfk",
            "alamatfk",
            "kebangsaanfk",
            "etnisfk",
            "negarafk",
            "namapenanggungjawab",
            "hubungandenganpasien",
            "nohppenanggungjawab",
            "created_at",
            "updated_at",
            "deleted_at"
        ];

        $data = [];

        if (($handle = fopen($file, 'r')) !== false) {
            fgetcsv($handle); // skip header
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $rowData = array_combine($header, $row);

                unset($rowData['deleted_at']);

                // Cek marital status
                $statusValue = $rowData['fk_maritalstatus'];
                if (!is_numeric($statusValue)) {
                    $status = MarialStatus::where('name', $statusValue)->first();
                    $statusValue = $status ? $status->id : null;
                }

                $rowData['fk_maritalstatus'] = $statusValue;
                $rowData['created_at'] = now();
                $rowData['updated_at'] = now();

                $data[] = $rowData;
            }
            fclose($handle);
        }

        if (!empty($data)) {
            DB::table('m_pasien')->insert($data);
        }
    }
}
