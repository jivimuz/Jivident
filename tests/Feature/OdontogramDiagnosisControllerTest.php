<?php

namespace Tests\Feature;

use App\Models\DaftarPasien;
use App\Models\Module;
use App\Models\OdontogramDiagnosis;
use App\Models\Patient;
use App\Models\Serial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Master CRUD for odontogram diagnosis/kondisi-gigi (previously hardcoded in
 * adult.js/child.js/2dglobal.js) and the read-only endpoint the odontogram
 * tabs use to fetch it (OdontogramController::diagnosisList()).
 */
class OdontogramDiagnosisControllerTest extends TestCase
{
    use RefreshDatabase;

    private function authorizedUser(int $klienfk = 1): User
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);

        $ids = Module::whereIn('code', ['MOD', 'DPP'])->pluck('id');

        $user = User::first();
        $user->klienfk = $klienfk;
        $user->access = $ids->implode(',');
        $user->save();

        return $user;
    }

    private function makeDaftarPasien(int $klienfk): DaftarPasien
    {
        $patient = Patient::create([
            'klienfk' => $klienfk, 'norm' => 'RM-'.uniqid(), 'nik' => (string) random_int(1000000000, 9999999999), 'nama' => 'Pasien Odontogram',
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

    private function encode(int $id): string
    {
        return base64_encode(base64_encode((string) $id));
    }

    public function test_page_requires_access(): void
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);

        $response = $this->actingAs(User::first())->get('/odontogram-diagnosis');

        $response->assertStatus(302);
    }

    public function test_get_list_only_shows_own_branch(): void
    {
        $user = $this->authorizedUser(1);
        OdontogramDiagnosis::create(['klienfk' => 1, 'kategori' => '3d', 'nama' => 'Milik Cabang 1', 'warna' => '#111111']);
        OdontogramDiagnosis::create(['klienfk' => 2, 'kategori' => '3d', 'nama' => 'Milik Cabang 2', 'warna' => '#222222']);

        $response = $this->actingAs($user)->post('/odontogram-diagnosis/getList');

        $response->assertOk();
        $response->assertJsonFragment(['nama' => 'Milik Cabang 1']);
        $response->assertJsonMissing(['nama' => 'Milik Cabang 2']);
    }

    public function test_can_create_a_3d_diagnosis(): void
    {
        $user = $this->authorizedUser(1);

        $response = $this->actingAs($user)->post('/odontogram-diagnosis/saveDiagnosis', [
            'kategori' => '3d',
            'nama' => 'Nekrosis',
            'warna' => '#aa00ff',
            // sent anyway to prove the server strips them for kategori=3d
            'simbol' => 'N',
            'is_reset' => '1',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('m_odontogram_diagnosis', [
            'klienfk' => 1,
            'kategori' => '3d',
            'nama' => 'Nekrosis',
            'warna' => '#aa00ff',
            'simbol' => null,
            'is_reset' => false,
        ]);
    }

    public function test_can_create_a_2d_kondisi_with_simbol_and_reset_flag(): void
    {
        $user = $this->authorizedUser(1);

        $response = $this->actingAs($user)->post('/odontogram-diagnosis/saveDiagnosis', [
            'kategori' => '2d',
            'nama' => 'Bersih Ulang',
            'warna' => '#ffffff',
            'simbol' => 'BR',
            'is_reset' => '1',
            'urutan' => 5,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('m_odontogram_diagnosis', [
            'klienfk' => 1,
            'kategori' => '2d',
            'nama' => 'Bersih Ulang',
            'simbol' => 'BR',
            'is_reset' => true,
            'urutan' => 5,
        ]);
    }

    public function test_rejects_invalid_color_format(): void
    {
        $user = $this->authorizedUser(1);

        $response = $this->actingAs($user)->postJson('/odontogram-diagnosis/saveDiagnosis', [
            'kategori' => '3d',
            'nama' => 'Warna Salah',
            'warna' => 'blue',
        ]);

        $response->assertStatus(422);
    }

    public function test_can_update_an_existing_diagnosis(): void
    {
        $user = $this->authorizedUser(1);
        $diagnosis = OdontogramDiagnosis::create(['klienfk' => 1, 'kategori' => '3d', 'nama' => 'Karies', 'warna' => '#3b82f6']);

        $response = $this->actingAs($user)->post('/odontogram-diagnosis/saveDiagnosis', [
            'id' => $diagnosis->id,
            'kategori' => '3d',
            'nama' => 'Karies Dalam',
            'warna' => '#000080',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('m_odontogram_diagnosis', [
            'id' => $diagnosis->id,
            'nama' => 'Karies Dalam',
            'warna' => '#000080',
        ]);
    }

    public function test_cannot_update_a_diagnosis_from_another_branch(): void
    {
        $user = $this->authorizedUser(1);
        $diagnosisCabangLain = OdontogramDiagnosis::create(['klienfk' => 2, 'kategori' => '3d', 'nama' => 'Punya Cabang 2', 'warna' => '#111111']);

        $response = $this->actingAs($user)->post('/odontogram-diagnosis/saveDiagnosis', [
            'id' => $diagnosisCabangLain->id,
            'kategori' => '3d',
            'nama' => 'Diganti Cabang 1',
            'warna' => '#222222',
        ]);

        $response->assertStatus(404);
        $this->assertDatabaseHas('m_odontogram_diagnosis', ['id' => $diagnosisCabangLain->id, 'nama' => 'Punya Cabang 2']);
    }

    public function test_can_delete_a_diagnosis(): void
    {
        $user = $this->authorizedUser(1);
        $diagnosis = OdontogramDiagnosis::create(['klienfk' => 1, 'kategori' => '2d', 'nama' => 'Dihapus', 'warna' => '#ffffff']);

        $response = $this->actingAs($user)->post('/odontogram-diagnosis/deleteDiagnosis', ['id' => $diagnosis->id]);

        $response->assertOk();
        $this->assertSoftDeleted('m_odontogram_diagnosis', ['id' => $diagnosis->id]);
    }

    public function test_cannot_delete_a_diagnosis_from_another_branch(): void
    {
        $user = $this->authorizedUser(1);
        $diagnosisCabangLain = OdontogramDiagnosis::create(['klienfk' => 2, 'kategori' => '2d', 'nama' => 'Punya Cabang 2', 'warna' => '#ffffff']);

        $response = $this->actingAs($user)->post('/odontogram-diagnosis/deleteDiagnosis', ['id' => $diagnosisCabangLain->id]);

        $response->assertOk();
        $this->assertDatabaseHas('m_odontogram_diagnosis', ['id' => $diagnosisCabangLain->id, 'deleted_at' => null]);
    }

    public function test_odontogram_tab_diagnosis_list_filters_by_kategori_and_branch(): void
    {
        $user = $this->authorizedUser(1);
        $daftar = $this->makeDaftarPasien(1);
        OdontogramDiagnosis::create(['klienfk' => 1, 'kategori' => '3d', 'nama' => 'Karies 3D', 'warna' => '#3b82f6']);
        OdontogramDiagnosis::create(['klienfk' => 1, 'kategori' => '2d', 'nama' => 'Karies 2D', 'warna' => '#808080']);
        OdontogramDiagnosis::create(['klienfk' => 2, 'kategori' => '3d', 'nama' => 'Punya Cabang Lain', 'warna' => '#000000']);

        $response = $this->actingAs($user)->post("/pemeriksaan/pasien/{$this->encode($daftar->id)}/odontogram/diagnosisList", [
            'kategori' => '3d',
        ]);

        $response->assertOk();
        $response->assertJsonFragment(['nama' => 'Karies 3D']);
        $response->assertJsonMissing(['nama' => 'Karies 2D']);
        $response->assertJsonMissing(['nama' => 'Punya Cabang Lain']);
    }
}
