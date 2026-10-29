<?php

namespace Tests\Feature;

use App\Models\DaftarPasien;
use App\Models\Module;
use App\Models\Patient;
use App\Models\Serial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regresi audit keamanan: hampir semua method di PemeriksaanController (dan
 * OdontogramController, yang berbagi grup route yang sama) cuma base64-decode
 * `{id}` dari URL dan langsung query `t_daftarpasien` tanpa pernah cek
 * klienfk — staf dari cabang lain bisa lihat/ubah vital sign, SOAPE,
 * tindakan, diagnosa, resep, consent, lab, radiologi, dan status kunjungan
 * milik cabang yang bukan miliknya, hanya dengan mengirim ID kunjungan
 * cabang lain. Diperbaiki dengan satu middleware (`VerifyDaftarPasienBranch`)
 * di level grup route `pemeriksaan/pasien/{id}`, bukan tambal satu-satu per
 * method.
 */
class PemeriksaanBranchProtectionTest extends TestCase
{
    use RefreshDatabase;

    private function authorizedUser(int $klienfk): User
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);

        $user = User::first();
        $user->klienfk = $klienfk;
        $user->access = (string) Module::where('code', 'DPP')->value('id');
        $user->save();

        return $user;
    }

    private function makeDaftarPasien(int $klienfk): DaftarPasien
    {
        $patient = Patient::create([
            'klienfk' => $klienfk, 'norm' => 'RM-'.uniqid(), 'nik' => (string) random_int(1000000000, 9999999999), 'nama' => 'Pasien Cabang '.$klienfk,
        ]);

        return DaftarPasien::create([
            'klienfk' => $klienfk,
            'noregistrasi' => 'REG-'.uniqid(),
            'ruanganfk' => 1,
            'pasienfk' => $patient->id,
            'tglregistrasi' => now(),
            'statuspasien' => 'LAMA',
            'status' => 'Di Periksa',
            'noantrian' => 'A1',
            'statuspembayaran' => 'Belum Verifikasi',
            'biaya_registrasi' => 20000,
        ]);
    }

    public function test_cannot_open_pemeriksaan_tabs_for_a_visit_in_another_branch(): void
    {
        $daftarCabang2 = $this->makeDaftarPasien(2);
        $this->authorizedUser(1);
        $encodedId = base64_encode(base64_encode((string) $daftarCabang2->id));

        $endpoints = ['vitalsign', 'soape', 'tindakan', 'diagnosa', 'resep', 'cetaksurat', 'consent', 'lab', 'radiologi'];
        foreach ($endpoints as $endpoint) {
            $response = $this->actingAs(User::first())->post("/pemeriksaan/pasien/{$encodedId}/{$endpoint}");
            $this->assertSame(404, $response->status(), "Endpoint [{$endpoint}] seharusnya 404 untuk kunjungan cabang lain, dapat {$response->status()}");
        }
    }

    public function test_cannot_save_a_lab_order_for_a_visit_in_another_branch(): void
    {
        $daftarCabang2 = $this->makeDaftarPasien(2);
        $this->authorizedUser(1);
        $encodedId = base64_encode(base64_encode((string) $daftarCabang2->id));

        $response = $this->actingAs(User::first())->post("/pemeriksaan/pasien/{$encodedId}/lab/save", [
            'fk_jenispemeriksaan' => 1,
        ]);

        $response->assertStatus(404);
        $this->assertDatabaseMissing('t_labpasien', ['fk_daftarpasien' => $daftarCabang2->id]);
    }

    public function test_can_still_open_pemeriksaan_tabs_for_a_visit_in_the_same_branch(): void
    {
        $daftarCabang1 = $this->makeDaftarPasien(1);
        $this->authorizedUser(1);
        $encodedId = base64_encode(base64_encode((string) $daftarCabang1->id));

        $response = $this->actingAs(User::first())->post("/pemeriksaan/pasien/{$encodedId}/cetaksurat");

        $response->assertOk();
    }
}
