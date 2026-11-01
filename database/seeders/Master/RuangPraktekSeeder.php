<?php

namespace Database\Seeders\Master;

use App\Models\RuangPraktek;
use Illuminate\Database\Seeder;

class RuangPraktekSeeder extends Seeder
{
    /**
     * `fk_dokter` sengaja dibiarkan null untuk baris baru — kolom itu legacy
     * dan sudah tidak dibaca kode manapun lagi (doctor-per-room sekarang
     * diatur lewat `JadwalDokter`, lihat catatan di `CLAUDE.md`).
     */
    public function run(): void
    {
        $existingCodes = RuangPraktek::pluck('code')->all();

        $rooms = [
            ['name' => 'Ruang Praktek 1', 'code' => 'R1'],
            ['name' => 'Ruang Praktek 2', 'code' => 'R2'],
            ['name' => 'Ruang Praktek 3', 'code' => 'R3'],
        ];

        foreach ($rooms as $room) {
            if (in_array($room['code'], $existingCodes, true)) {
                continue;
            }
            RuangPraktek::create(array_merge(['klienfk' => '1'], $room));
        }
    }
}
