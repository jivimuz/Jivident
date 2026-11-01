<?php

namespace Database\Seeders\Master;

use App\Models\Tindakan;
use Illuminate\Database\Seeder;

class TindakanSeeder extends Seeder
{
    public function run(): void
    {
        // Baris lama (name "abc") dari percobaan manual dibiarkan — seeder ini
        // cuma menambah daftar yang belum ada, bukan mereset ulang isinya.
        $existingNames = Tindakan::pluck('name')->map(fn ($n) => strtolower($n))->all();

        $tindakan = [
            ['name' => 'Konsultasi Dokter Gigi', 'amount' => 50000, 'butuh_consent' => false],
            ['name' => 'Pembersihan Karang Gigi (Scaling)', 'amount' => 250000, 'butuh_consent' => true],
            ['name' => 'Penambalan Gigi (Komposit)', 'amount' => 200000, 'butuh_consent' => true],
            ['name' => 'Pencabutan Gigi Sulung', 'amount' => 100000, 'butuh_consent' => true],
            ['name' => 'Pencabutan Gigi Permanen', 'amount' => 300000, 'butuh_consent' => true],
            ['name' => 'Pencabutan Gigi Bungsu (Odontektomi)', 'amount' => 1500000, 'butuh_consent' => true],
            ['name' => 'Perawatan Saluran Akar (Root Canal) per gigi', 'amount' => 800000, 'butuh_consent' => true],
            ['name' => 'Pemasangan Mahkota Gigi (Crown)', 'amount' => 1200000, 'butuh_consent' => true],
            ['name' => 'Pemutihan Gigi (Bleaching)', 'amount' => 1000000, 'butuh_consent' => true],
            ['name' => 'Pemasangan Behel Gigi (Konvensional)', 'amount' => 5000000, 'butuh_consent' => true],
            ['name' => 'Kontrol Behel Bulanan', 'amount' => 150000, 'butuh_consent' => false],
            ['name' => 'Rontgen Gigi (X-Ray Periapikal)', 'amount' => 100000, 'butuh_consent' => false],
            ['name' => 'Fluoride Treatment', 'amount' => 150000, 'butuh_consent' => false],
            ['name' => 'Fissure Sealant (Pelapisan Gigi)', 'amount' => 175000, 'butuh_consent' => false],
            ['name' => 'Pembuatan Gigi Palsu (per gigi)', 'amount' => 400000, 'butuh_consent' => true],
        ];

        foreach ($tindakan as $item) {
            if (in_array(strtolower($item['name']), $existingNames, true)) {
                continue;
            }
            Tindakan::create(array_merge(['klienfk' => 1], $item));
        }
    }
}
