<?php

namespace Database\Seeders\Pasien;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\KotaKabupaten;

class KecamatanSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now()->toDateTimeString();
        $filePath = database_path('seeders/csv/kecamatan_m.csv');

        if (!file_exists($filePath)) {
            $this->command->error("File CSV tidak ditemukan di: {$filePath}");
            return;
        }

        $rows = array_map('str_getcsv', file($filePath));
        $header = ['id', 'kotakabupatenfk', 'kecamatan', 'lat', 'long'];
        $data = [];

        foreach ($rows as $row) {
            if (count($row) !== count($header)) continue;

            $arr = array_combine($header, $row);
            $kotakabupaten = KotaKabupaten::select('provinsifk')->find($arr['kotakabupatenfk']);
            if (!$kotakabupaten) continue;

            $data[] = [
                'klienfk'          => 1,
                'provinsifk'       => $kotakabupaten->provinsifk,
                'kotakabupatenfk'  => $arr['kotakabupatenfk'],
                'kecamatan'        => $arr['kecamatan'],
                'kdkecamatan'      => substr($arr['id'], 0, 2) . '.' . substr($arr['id'], 2, 2) . '.' . substr($arr['id'], 4, 2),
                'created_at'       => $now,
                'updated_at'       => $now,
            ];
        }

        if (!empty($data)) {
            $chunks = array_chunk($data, 500); // pecah tiap 500 row
            foreach ($chunks as $i => $chunk) {
                DB::table('m_kecamatan')->insert($chunk);
                $this->command->info("Batch " . ($i + 1) . " berhasil diinsert (" . count($chunk) . " row)");
                sleep(2); // delay 2 detik sebelum batch berikutnya
            }
            $this->command->info(count($data) . " data kecamatan berhasil diimport.");
        } else {
            $this->command->warn("Tidak ada data yang diimport (mungkin format CSV salah).");
        }
    }
}
