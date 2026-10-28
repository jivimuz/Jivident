<?php

namespace Tests\Feature;

use App\Models\DaftarPasien;
use App\Models\Employee;
use App\Models\Module;
use App\Models\Patient;
use App\Models\Serial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrintSuratSignatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_surat_sehat_embeds_doctor_signature_when_present(): void
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);

        $user = User::first();
        $user->access = (string) Module::where('code', 'DPP')->value('id');
        $user->save();

        $dokter = Employee::create([
            'nik' => 'DOK-001',
            'fullname' => 'drg. Penanda Tangan',
            'signature_path' => 'images/signatures/employee-999-fake.png',
        ]);

        $patient = Patient::create([
            'klienfk' => $user->klienfk,
            'norm' => 'RM040',
            'nik' => '6666',
            'nama' => 'Pasien Surat Sehat',
        ]);

        $daftar = DaftarPasien::create([
            'klienfk' => $user->klienfk,
            'noregistrasi' => 'REG-030',
            'ruanganfk' => 1,
            'dokterfk' => $dokter->id,
            'pasienfk' => $patient->id,
            'tglregistrasi' => now(),
            'statuspasien' => 'BARU',
            'status' => 'Selesai',
            'noantrian' => 'A1',
            'statuspembayaran' => 'Lunas',
            'biaya_registrasi' => 20000,
        ]);

        $encodedId = base64_encode(base64_encode((string) $daftar->id));

        $response = $this->actingAs($user)->get("/print/print-surat-sehat-pasien/{$encodedId}");

        $response->assertOk();
        $response->assertSee('images/signatures/employee-999-fake.png', false);
        $response->assertSee('drg. Penanda Tangan');
    }

    public function test_surat_sehat_has_no_signature_image_when_doctor_has_none(): void
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);

        $user = User::first();
        $user->access = (string) Module::where('code', 'DPP')->value('id');
        $user->save();

        $dokter = Employee::create([
            'nik' => 'DOK-002',
            'fullname' => 'drg. Tanpa Tanda Tangan',
        ]);

        $patient = Patient::create([
            'klienfk' => $user->klienfk,
            'norm' => 'RM041',
            'nik' => '7777',
            'nama' => 'Pasien Surat Sehat Dua',
        ]);

        $daftar = DaftarPasien::create([
            'klienfk' => $user->klienfk,
            'noregistrasi' => 'REG-031',
            'ruanganfk' => 1,
            'dokterfk' => $dokter->id,
            'pasienfk' => $patient->id,
            'tglregistrasi' => now(),
            'statuspasien' => 'BARU',
            'status' => 'Selesai',
            'noantrian' => 'A2',
            'statuspembayaran' => 'Lunas',
            'biaya_registrasi' => 20000,
        ]);

        $encodedId = base64_encode(base64_encode((string) $daftar->id));

        $response = $this->actingAs($user)->get("/print/print-surat-sehat-pasien/{$encodedId}");

        $response->assertOk();
        $response->assertDontSee('<img', false);
    }
}
