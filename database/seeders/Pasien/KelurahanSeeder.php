<?php

namespace Database\Seeders\Pasien;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\Kecamatan;
use Carbon\Carbon;

class KelurahanSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now()->toDateTimeString();
        $folderPath = database_path('seeders/csv/kelurahan');

        // Ambil semua file CSV di folder kelurahan
        $resourceFiles = array_diff(scandir($folderPath), ['.', '..']);

        foreach ($resourceFiles as $file) {
            $filePath = $folderPath . '/' . $file;

            // Baca CSV
            $data = $this->csvToArray($filePath, ['id', 'kecamatanfk', 'kelurahan', 'lat', 'long', 'pos']);

            // Transformasi data
            $data = array_map(function ($arr) use ($now) {
                unset($arr['lat'], $arr['long'], $arr['pos']);

                $kecamatan = Kecamatan::select('provinsifk', 'kotakabupatenfk')->find($arr['kecamatanfk']);

                if (!$kecamatan) return null; // Skip kalau data tidak ditemukan

                return [
                    'id' => $arr['id'],
                    'klienfk' => 1,
                    'kecamatanfk' => $arr['kecamatanfk'],
                    'kotakabupatenfk' => $kecamatan->kotakabupatenfk,
                    'provinsifk' => $kecamatan->provinsifk,
                    'kelurahan' => $arr['kelurahan'],
                    'kdkelurahan' => substr($arr['id'], 0, 2) . '.' . substr($arr['id'], 2, 2) . '.' . substr($arr['id'], 4, 2) . '.' . substr($arr['id'], 6, 4),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }, $data);

            // Hapus null dari array (kalau ada yang gagal)
            $data = array_filter($data);

            // Insert batch
            DB::table('m_kelurahan')->insert($data);
            $this->command->info(count($data) . " data dari" . $filePath . " berhasil diimport.");
        }
    }

    private function csvToArray($filename, $header)
    {
        $rows = [];
        if (!File::exists($filename)) return $rows;

        $file = fopen($filename, 'r');
        while (($data = fgetcsv($file, 1000, ',')) !== false) {
            $rows[] = array_combine($header, $data);
        }
        fclose($file);
        return $rows;
    }
}
