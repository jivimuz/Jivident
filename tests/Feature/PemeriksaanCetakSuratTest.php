<?php

namespace Tests\Feature;

use App\Models\DaftarPasien;
use App\Models\Module;
use App\Models\Patient;
use App\Models\Serial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PemeriksaanCetakSuratTest extends TestCase
{
    use RefreshDatabase;

    public function test_cetaksurat_does_not_error_when_patient_has_no_vital_sign_yet(): void
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);

        $user = User::first();
        $user->access = (string) Module::where('code', 'DPP')->value('id');
        $user->save();

        $patient = Patient::create([
            'klienfk' => $user->klienfk,
            'norm' => 'RM030',
            'nik' => '5555',
            'nama' => 'Pasien Belum Vital Sign',
        ]);

        $daftar = DaftarPasien::create([
            'klienfk' => $user->klienfk,
            'noregistrasi' => 'REG-020',
            'ruanganfk' => 1,
            'pasienfk' => $patient->id,
            'tglregistrasi' => now(),
            'statuspasien' => 'BARU',
            'status' => 'Di Periksa',
            'noantrian' => 'A1',
            'statuspembayaran' => 'Belum Verifikasi',
            'biaya_registrasi' => 20000,
        ]);

        // No VitalSign row created on purpose — this is the reported bug scenario.
        $encodedId = base64_encode(base64_encode((string) $daftar->id));

        $response = $this->actingAs($user)->post("/pemeriksaan/pasien/{$encodedId}/cetaksurat");

        $response->assertOk();
    }
}
