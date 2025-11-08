<?php

namespace Database\Seeders;

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

                $kecamatan = \App\Models\Kecamatan::select('provinsifk', 'kotakabupatenfk')
                    ->where('id', $arr['kecamatanfk'])
                    ->first();

                if (!$kecamatan) return null; // Skip kalau data tidak ditemukan

                return [
                    'id' => $arr['id'],
                    'klienfk' => 1,
                    'kecamatanfk' => $arr['kecamatanfk'],
                    'kotakabupatenfk' => $kecamatan->kotakabupatenfk,
                    'provinsifk' => $kecamatan->provinsifk,
                    'kelurahan' => $arr['kelurahan'],
                    'kdkelurahan' => substr($arr['id'], 0, 2) . '.' . substr($arr['id'], 2, 2) . '.' . substr($arr['id'], 4, 2) . '.' . substr($arr['id'], 6, 4),
                ];
            }, $data);

            // Hapus null dari array
            $data = array_values(array_filter($data));

            // Pecah ke batch 500
            $chunks = array_chunk($data, 500);

            foreach ($chunks as $i => $chunk) {
                DB::table('m_kelurahan')->insert($chunk);
                $this->command->info("Batch " . ($i + 1) . " dari file {$file} berhasil diimport (" . count($chunk) . " baris).");

                // Delay 0.5 detik antar batch
                usleep(500000);
            }

            $this->command->info("File {$file} selesai diimport total: " . count($data) . " baris.\n");
        }
    }

    private function csvToArray($filename, $header)
    {
        $rows = [];
        if (!File::exists($filename)) return $rows;

        $file = fopen($filename, 'r');
        while (($data = fgetcsv($file, 1000, ',')) !== false) {
            if (count($data) === count($header)) {
                $rows[] = array_combine($header, $data);
            }
        }
        fclose($file);
        return $rows;
    }
}
