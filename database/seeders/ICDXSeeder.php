<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;

class ICDXSeeder extends Seeder
{
    public function run(): void
    {
        $now = now()->toDateTimeString();

        $file = database_path('seeders/csv/m_icdx.csv');

        // Header target mengikuti versi CI4 kamu
        $header = [
            'id',
            'statusenable',
            'kdklinik',
            'diagnosa',
            'kddiagnosa',
            'reportdisplay',
            'created_at',
            'updated_at',
        ];

        $rows = $this->csvToArray($file, $header);

        $data = array_map(function (array $arr) use ($now) {
            // buang kolom yang tidak dipakai
            unset($arr['statusenable'], $arr['kdklinik'], $arr['reportdisplay']);

            // map field ke schema Laravel
            $arr['klienfk'] = 1;
            $arr['name'] = $arr['diagnosa'] ?? null;
            $arr['code'] = $arr['kddiagnosa'] ?? null;

            // paksa timestamp sekarang
            $arr['created_at'] = $now;
            $arr['updated_at'] = $now;

            // hapus field lama
            unset($arr['diagnosa'], $arr['kddiagnosa']);

            // Kalau tabel kamu pakai auto increment dan tidak mau pakai id dari CSV:
            // unset($arr['id']);

            return $arr;
        }, $rows);

        if (empty($data)) {
            $this->command?->warn('Tidak ada data ICDX dari CSV.');
            return;
        }

        // Insert batch (chunk biar aman kalau data besar)
        foreach (array_chunk($data, 1000) as $chunk) {
            DB::table('m_icdx')->insert($chunk);
        }

        $this->command?->info('Seeder m_icdx selesai: ' . count($data) . ' baris.');
    }

    /**
     * Baca CSV menjadi array associative berdasarkan header custom.
     * Menganggap file CSV tidak perlu header asli (atau header asli akan tetap dibaca sebagai data pertama).
     */
    private function csvToArray(string $file, array $header): array
    {
        if (!File::exists($file)) {
            throw new RuntimeException("File CSV tidak ditemukan: {$file}");
        }

        $rows = [];
        $handle = fopen($file, 'r');

        if ($handle === false) {
            throw new RuntimeException("Gagal membuka file CSV: {$file}");
        }

        try {
            while (($line = fgetcsv($handle, 0, ',')) !== false) {
                // skip baris kosong
                if ($line === [null] || count(array_filter($line, fn($v) => $v !== null && $v !== '')) === 0) {
                    continue;
                }

                // Samakan jumlah kolom dengan header
                $line = array_pad($line, count($header), null);
                $line = array_slice($line, 0, count($header));

                $rows[] = array_combine($header, $line);
            }
        } finally {
            fclose($handle);
        }

        return $rows;
    }
}
