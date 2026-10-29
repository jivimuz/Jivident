<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Module;
use App\Models\Patient;
use App\Models\RuangPraktek;
use App\Models\Serial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regresi audit keamanan: editPatient/updateData/registerPatient/
 * processPatient tidak men-scope lookup pasien ke klienfk — staf dari cabang
 * lain bisa lihat/ubah data PII pasien cabang lain (NIK, alamat, data wali),
 * atau mendaftarkan kunjungan baru atas nama pasien yang bukan milik
 * cabangnya.
 */
class PatientControllerMultiBranchTest extends TestCase
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
        $user->access = (string) Module::where('code', 'PA')->value('id');
        $user->save();

        return $user;
    }

    private function makePatientInBranch(int $klienfk, string $nama): Patient
    {
        return Patient::create([
            'klienfk' => $klienfk,
            'norm' => 'RM-'.uniqid(),
            'nik' => (string) random_int(1000000000, 9999999999),
            'nama' => $nama,
        ]);
    }

    public function test_cannot_open_edit_page_of_a_patient_in_another_branch(): void
    {
        $this->authorizedUser(1);
        $patient = $this->makePatientInBranch(2, 'Pasien Cabang 2');
        $encodedId = base64_encode(base64_encode((string) $patient->id));

        $response = $this->actingAs(User::first())->get("/patient/edit_patient/{$encodedId}");

        $response->assertStatus(404);
    }

    public function test_cannot_update_pii_of_a_patient_in_another_branch(): void
    {
        $this->authorizedUser(1);
        $patient = $this->makePatientInBranch(2, 'Pasien Asli Cabang 2');

        $response = $this->actingAs(User::first())->post('/patient/updateData', [
            'id' => $patient->id,
            'nik' => '9999999999',
            'nama' => 'Nama Diubah Paksa',
            'umur' => 30,
            'nohp' => '08199999999',
            'jeniskelaminfk' => 1,
            'tgllahir' => '1990-01-01',
            'namapenanggungjawab' => 'X',
            'hubungandenganpasien' => 'X',
            'nohppenanggungjawab' => '08199999999',
            'alamat' => 'X',
            'kelurahanfk' => 1,
        ]);

        $response->assertStatus(404);
        $this->assertSame('Pasien Asli Cabang 2', $patient->refresh()->nama);
    }

    public function test_cannot_open_registration_page_for_a_patient_in_another_branch(): void
    {
        $this->authorizedUser(1);
        $patient = $this->makePatientInBranch(2, 'Pasien Registrasi Cabang 2');
        $encodedId = base64_encode(base64_encode((string) $patient->id));

        $response = $this->actingAs(User::first())->get("/patient/register-patient/{$encodedId}");

        $response->assertStatus(404);
    }

    public function test_cannot_register_a_visit_against_a_patient_in_another_branch(): void
    {
        $user = $this->authorizedUser(1);
        $patientCabangLain = $this->makePatientInBranch(2, 'Pasien Cabang 2 Diregistrasi Paksa');
        $employee = Employee::create(['fullname' => 'drg. Test', 'nik' => 'NIK-'.uniqid()]);
        $dokter = User::forceCreate([
            'klienfk' => 1, 'fk_employee' => $employee->id, 'username' => 'drg_'.uniqid(),
            'display_name' => 'drg. Test', 'email' => uniqid().'@test.com', 'password' => bcrypt('x'), 'access' => '1',
        ]);
        $ruangan = RuangPraktek::create(['klienfk' => 1, 'name' => 'Ruang 1', 'code' => 'R1', 'fk_dokter' => $dokter->id]);
        $encodedId = base64_encode(base64_encode((string) $patientCabangLain->id));

        $response = $this->actingAs($user)->post("/patient/register-patient/{$encodedId}", [
            'ruanganfk' => $ruangan->id,
            'tglregistrasi' => now()->toDateString(),
            'jenispembayaran' => 'UMUM',
        ]);

        $response->assertStatus(404);
        $this->assertDatabaseMissing('t_daftarpasien', ['pasienfk' => $patientCabangLain->id]);
    }
}
