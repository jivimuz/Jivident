<?php

namespace Tests\Feature;

use App\Helpers\RawatInapHelper;
use App\Models\DaftarPasien;
use App\Models\Employee;
use App\Models\Kamar;
use App\Models\Module;
use App\Models\Patient;
use App\Models\Serial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Rawat Inap tab on the Pemeriksaan page — room transfers happen here
 * instead of on the dedicated /rawat-inap page, since that's where staff
 * already are while rounding on a specific patient. Admission/discharge
 * itself is unaffected; this only covers viewing + transferring an already
 * active admission.
 */
class PemeriksaanRawatInapTabTest extends TestCase
{
    use RefreshDatabase;

    private function authorizedUser(int $klienfk = 1): User
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
            'klienfk' => $klienfk, 'norm' => 'RM-'.uniqid(), 'nik' => (string) random_int(1000000000, 9999999999), 'nama' => 'Pasien RWI Tab',
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

    public function test_tab_shows_empty_state_when_no_admission_exists(): void
    {
        $user = $this->authorizedUser();
        $daftar = $this->makeDaftarPasien($user->klienfk);

        $response = $this->actingAs($user)->post("/pemeriksaan/pasien/{$this->encode($daftar->id)}/rawatinap");

        $response->assertOk();
        $response->assertSee('belum rawat inap');
        $response->assertSee('Admisi ke Rawat Inap');
    }

    public function test_tab_shows_current_room_and_can_list_rooms_for_transfer(): void
    {
        $user = $this->authorizedUser();
        $daftar = $this->makeDaftarPasien($user->klienfk);
        $kamar = Kamar::create(['klienfk' => $user->klienfk, 'nama' => 'Kamar Reguler', 'tarif_per_hari' => 100000, 'status' => 'kosong']);
        app(RawatInapHelper::class)->admit(['klienfk' => $user->klienfk, 'fk_daftarpasien' => $daftar->id, 'fk_kamar' => $kamar->id]);

        $response = $this->actingAs($user)->post("/pemeriksaan/pasien/{$this->encode($daftar->id)}/rawatinap");
        $response->assertOk();
        $response->assertSee('Kamar Reguler');
        $response->assertSee('Sedang Dirawat');

        $ruanganResponse = $this->actingAs($user)->post("/pemeriksaan/pasien/{$this->encode($daftar->id)}/rawatinap/ruanganList");
        $ruanganResponse->assertOk();
    }

    public function test_can_transfer_room_from_the_tab(): void
    {
        $user = $this->authorizedUser();
        $daftar = $this->makeDaftarPasien($user->klienfk);
        $kamarAsal = Kamar::create(['klienfk' => $user->klienfk, 'nama' => 'Kamar Reguler', 'tarif_per_hari' => 100000, 'status' => 'kosong']);
        $kamarVip = Kamar::create(['klienfk' => $user->klienfk, 'nama' => 'Kamar VIP', 'tarif_per_hari' => 300000, 'status' => 'kosong']);
        app(RawatInapHelper::class)->admit(['klienfk' => $user->klienfk, 'fk_daftarpasien' => $daftar->id, 'fk_kamar' => $kamarAsal->id]);

        $response = $this->actingAs($user)->post("/pemeriksaan/pasien/{$this->encode($daftar->id)}/rawatinap/pindah", [
            'fk_kamar_tujuan' => $kamarVip->id,
            'catatan' => 'Pindah ke VIP',
        ]);

        $response->assertOk();
        $this->assertSame('kosong', $kamarAsal->refresh()->status);
        $this->assertSame('terisi', $kamarVip->refresh()->status);
        $this->assertDatabaseHas('t_rawatinap_pindah', [
            'fk_kamar_asal' => $kamarAsal->id,
            'fk_kamar_tujuan' => $kamarVip->id,
            'catatan' => 'Pindah ke VIP',
        ]);
    }

    public function test_cannot_transfer_to_an_occupied_room(): void
    {
        $user = $this->authorizedUser();
        $daftar = $this->makeDaftarPasien($user->klienfk);
        $kamarAsal = Kamar::create(['klienfk' => $user->klienfk, 'nama' => 'Kamar Reguler', 'tarif_per_hari' => 100000, 'status' => 'kosong']);
        $kamarPenuh = Kamar::create(['klienfk' => $user->klienfk, 'nama' => 'Kamar Penuh', 'tarif_per_hari' => 100000, 'status' => 'terisi']);
        app(RawatInapHelper::class)->admit(['klienfk' => $user->klienfk, 'fk_daftarpasien' => $daftar->id, 'fk_kamar' => $kamarAsal->id]);

        $response = $this->actingAs($user)->post("/pemeriksaan/pasien/{$this->encode($daftar->id)}/rawatinap/pindah", [
            'fk_kamar_tujuan' => $kamarPenuh->id,
        ]);

        $response->assertStatus(400);
        $this->assertSame('terisi', $kamarAsal->refresh()->status);
    }

    public function test_cannot_access_rawat_inap_tab_for_a_visit_in_another_branch(): void
    {
        $this->authorizedUser(1);
        $daftarCabangLain = $this->makeDaftarPasien(2);
        $encodedId = $this->encode($daftarCabangLain->id);

        $response = $this->actingAs(User::first())->post("/pemeriksaan/pasien/{$encodedId}/rawatinap");

        $response->assertStatus(404);
    }

    public function test_can_admit_patient_to_rawat_inap_from_the_tab(): void
    {
        $user = $this->authorizedUser();
        $daftar = $this->makeDaftarPasien($user->klienfk);
        $kamar = Kamar::create(['klienfk' => $user->klienfk, 'nama' => 'Kamar Reguler', 'tarif_per_hari' => 100000, 'status' => 'kosong']);
        $employee = Employee::create(['fullname' => 'drg. Admisi', 'nik' => 'NIK-'.uniqid(), 'is_pemeriksapasien' => true]);

        $response = $this->actingAs($user)->post("/pemeriksaan/pasien/{$this->encode($daftar->id)}/rawatinap/admit", [
            'fk_kamar' => $kamar->id,
            'fk_dokter_penanggungjawab' => $employee->id,
            'catatan_masuk' => 'Observasi demam',
        ]);

        $response->assertOk();
        $this->assertSame('terisi', $kamar->refresh()->status);
        $this->assertDatabaseHas('t_rawatinap', [
            'fk_daftarpasien' => $daftar->id,
            'fk_kamar' => $kamar->id,
            'fk_dokter_penanggungjawab' => $employee->id,
            'status' => 'dirawat',
        ]);
    }

    public function test_cannot_admit_when_a_visit_already_has_an_active_admission(): void
    {
        $user = $this->authorizedUser();
        $daftar = $this->makeDaftarPasien($user->klienfk);
        $kamarPertama = Kamar::create(['klienfk' => $user->klienfk, 'nama' => 'Kamar A', 'tarif_per_hari' => 100000, 'status' => 'kosong']);
        $kamarKedua = Kamar::create(['klienfk' => $user->klienfk, 'nama' => 'Kamar B', 'tarif_per_hari' => 100000, 'status' => 'kosong']);
        app(RawatInapHelper::class)->admit(['klienfk' => $user->klienfk, 'fk_daftarpasien' => $daftar->id, 'fk_kamar' => $kamarPertama->id]);

        $response = $this->actingAs($user)->post("/pemeriksaan/pasien/{$this->encode($daftar->id)}/rawatinap/admit", [
            'fk_kamar' => $kamarKedua->id,
        ]);

        $response->assertStatus(400);
        $this->assertSame('kosong', $kamarKedua->refresh()->status);
    }

    public function test_dokter_list_returns_only_employees_who_can_examine_patients(): void
    {
        $user = $this->authorizedUser();
        $daftar = $this->makeDaftarPasien($user->klienfk);
        $dokter = Employee::create(['fullname' => 'drg. Bisa Periksa', 'nik' => 'NIK-'.uniqid(), 'is_pemeriksapasien' => true]);
        Employee::create(['fullname' => 'Kasir Saja', 'nik' => 'NIK-'.uniqid(), 'is_pemeriksapasien' => false]);
        User::forceCreate([
            'klienfk' => $user->klienfk,
            'fk_employee' => $dokter->id,
            'username' => 'drg_'.uniqid(),
            'display_name' => $dokter->fullname,
            'email' => uniqid().'@test.com',
            'password' => bcrypt('password'),
            'access' => '1',
        ]);

        $response = $this->actingAs($user)->post("/pemeriksaan/pasien/{$this->encode($daftar->id)}/rawatinap/dokterList");

        $response->assertOk();
        $response->assertJsonFragment(['fullname' => 'drg. Bisa Periksa']);
        $response->assertJsonMissing(['fullname' => 'Kasir Saja']);
    }
}
