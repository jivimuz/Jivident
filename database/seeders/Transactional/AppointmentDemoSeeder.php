<?php

namespace Database\Seeders\Transactional;

use App\Helpers\AppointmentHelper;
use App\Models\JadwalDokter;
use App\Models\Patient;
use App\Models\RuangPraktek;
use App\Models\User;
use Illuminate\Database\Seeder;

class AppointmentDemoSeeder extends Seeder
{
    /**
     * Template jadwal dibuat untuk hari-dalam-minggu HARI INI (`now()->dayOfWeek`),
     * bukan hari tertentu yang di-hardcode — supaya booking percobaan di
     * bawah (untuk 7 hari ke depan, jadi selalu hari-dalam-minggu yang sama)
     * selalu match template ini apa pun tanggal seeder ini dijalankan.
     */
    public function run(): void
    {
        if (JadwalDokter::where('klienfk', 1)->exists()) {
            return;
        }

        $dokterId = User::where('klienfk', 1)->value('id') ?? 1;
        $ruanganId = RuangPraktek::where('klienfk', 1)->value('id') ?? 1;
        $patient = Patient::where('klienfk', 1)->orderBy('id')->first();

        JadwalDokter::create([
            'klienfk' => 1,
            'fk_dokter' => $dokterId,
            'fk_ruangpraktek' => $ruanganId,
            'hari' => now()->dayOfWeek,
            'jam_mulai' => '08:00',
            'jam_selesai' => '12:00',
            'durasi_slot' => 30,
            'is_active' => true,
        ]);

        if (! $patient) {
            return;
        }

        $tanggal = now()->addDays(7)->toDateString();
        $slots = app(AppointmentHelper::class)->getAvailableSlots($dokterId, $ruanganId, $tanggal);
        if (empty($slots)) {
            return;
        }

        app(AppointmentHelper::class)->book([
            'klienfk' => 1,
            'fk_dokter' => $dokterId,
            'fk_ruangpraktek' => $ruanganId,
            'fk_pasien' => $patient->id,
            'tanggal' => $tanggal,
            'jam_mulai' => $slots[0]['jam_mulai'],
            'notes' => 'Kontrol rutin (data demo)',
        ]);
    }
}
