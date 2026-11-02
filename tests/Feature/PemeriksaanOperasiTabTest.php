<?php

namespace Tests\Feature;

use App\Models\DaftarPasien;
use App\Models\Employee;
use App\Models\JadwalOperasi;
use App\Models\Module;
use App\Models\Patient;
use App\Models\RuanganOperasi;
use App\Models\Serial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Operasi tab on the Pemeriksaan page — scheduling a surgery happens here
 * (doctor is already on this visit while examining the patient), not from a
 * standalone staff page. The calendar/monitor view and the Ruangan Operasi
 * master-data screen are separate, read-only/admin concerns elsewhere.
 */
class PemeriksaanOperasiTabTest extends TestCase
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
            'klienfk' => $klienfk, 'norm' => 'RM-'.uniqid(), 'nik' => (string) random_int(1000000000, 9999999999), 'nama' => 'Pasien Operasi Tab',
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

    private function makeDokter(int $klienfk): User
    {
        $employee = Employee::create(['fullname' => 'drg. Bedah', 'nik' => 'NIK-'.uniqid(), 'is_pemeriksapasien' => true]);

        return User::forceCreate([
            'klienfk' => $klienfk,
            'fk_employee' => $employee->id,
            'username' => 'dr_'.uniqid(),
            'display_name' => 'drg. Bedah',
            'email' => uniqid().'@test.com',
            'password' => bcrypt('password'),
            'access' => '1',
        ]);
    }

    private function makeRuangan(int $klienfk): RuanganOperasi
    {
        return RuanganOperasi::create(['klienfk' => $klienfk, 'nama' => 'OK '.uniqid(), 'status' => 'tersedia']);
    }

    private function encode(int $id): string
    {
        return base64_encode(base64_encode((string) $id));
    }

    public function test_tab_shows_empty_state_when_no_schedule_exists(): void
    {
        $user = $this->authorizedUser();
        $daftar = $this->makeDaftarPasien($user->klienfk);

        $response = $this->actingAs($user)->post("/pemeriksaan/pasien/{$this->encode($daftar->id)}/operasi");

        $response->assertOk();
        $response->assertSee('Belum ada jadwal operasi');
        $response->assertSee('btnBukaJadwalOperasi');
    }

    public function test_save_operasi_schedules_a_surgery_for_this_visit(): void
    {
        $user = $this->authorizedUser();
        $daftar = $this->makeDaftarPasien($user->klienfk);
        $ruangan = $this->makeRuangan($user->klienfk);
        $dokter = $this->makeDokter($user->klienfk);

        $response = $this->actingAs($user)->post("/pemeriksaan/pasien/{$this->encode($daftar->id)}/operasi/save", [
            'fk_ruangan_operasi' => $ruangan->id,
            'fk_dokter' => $dokter->id,
            'nama_operasi' => 'Odontektomi',
            'tanggal' => now()->addDay()->toDateString(),
            'jam_mulai' => '09:00',
            'jam_selesai' => '10:00',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('t_jadwal_operasi', [
            'fk_daftarpasien' => $daftar->id,
            'fk_pasien' => $daftar->pasienfk,
            'nama_operasi' => 'Odontektomi',
        ]);
    }

    public function test_save_operasi_rejects_a_second_active_schedule_for_the_same_visit(): void
    {
        $user = $this->authorizedUser();
        $daftar = $this->makeDaftarPasien($user->klienfk);
        $ruangan = $this->makeRuangan($user->klienfk);
        $dokter = $this->makeDokter($user->klienfk);

        JadwalOperasi::create([
            'klienfk' => $user->klienfk,
            'fk_ruangan_operasi' => $ruangan->id,
            'fk_pasien' => $daftar->pasienfk,
            'fk_dokter' => $dokter->id,
            'fk_daftarpasien' => $daftar->id,
            'nama_operasi' => 'Operasi Pertama',
            'tanggal' => now()->addDay()->toDateString(),
            'jam_mulai' => '09:00',
            'jam_selesai' => '10:00',
            'status' => 'dijadwalkan',
        ]);

        $response = $this->actingAs($user)->post("/pemeriksaan/pasien/{$this->encode($daftar->id)}/operasi/save", [
            'fk_ruangan_operasi' => $this->makeRuangan($user->klienfk)->id,
            'fk_dokter' => $dokter->id,
            'nama_operasi' => 'Operasi Kedua',
            'tanggal' => now()->addDays(2)->toDateString(),
            'jam_mulai' => '09:00',
            'jam_selesai' => '10:00',
        ]);

        $response->assertStatus(400);
    }

    public function test_update_status_and_cancel_are_scoped_to_the_visit(): void
    {
        $user = $this->authorizedUser();
        $daftar = $this->makeDaftarPasien($user->klienfk);
        $jadwal = JadwalOperasi::create([
            'klienfk' => $user->klienfk,
            'fk_ruangan_operasi' => $this->makeRuangan($user->klienfk)->id,
            'fk_pasien' => $daftar->pasienfk,
            'fk_dokter' => $this->makeDokter($user->klienfk)->id,
            'fk_daftarpasien' => $daftar->id,
            'nama_operasi' => 'Operasi',
            'tanggal' => now()->addDay()->toDateString(),
            'jam_mulai' => '09:00',
            'jam_selesai' => '10:00',
            'status' => 'dijadwalkan',
        ]);

        $this->actingAs($user)
            ->post("/pemeriksaan/pasien/{$this->encode($daftar->id)}/operasi/status/{$jadwal->id}", ['status' => 'berlangsung'])
            ->assertOk();
        $this->assertDatabaseHas('t_jadwal_operasi', ['id' => $jadwal->id, 'status' => 'berlangsung']);

        $this->actingAs($user)
            ->post("/pemeriksaan/pasien/{$this->encode($daftar->id)}/operasi/cancel/{$jadwal->id}")
            ->assertOk();
        $this->assertDatabaseHas('t_jadwal_operasi', ['id' => $jadwal->id, 'status' => 'dibatalkan']);
    }

    public function test_cannot_update_status_of_a_schedule_belonging_to_another_visit(): void
    {
        $user = $this->authorizedUser(klienfk: 1);
        $daftarSaya = $this->makeDaftarPasien(1);
        $daftarLain = $this->makeDaftarPasien(1);

        $jadwalLain = JadwalOperasi::create([
            'klienfk' => 1,
            'fk_ruangan_operasi' => $this->makeRuangan(1)->id,
            'fk_pasien' => $daftarLain->pasienfk,
            'fk_dokter' => $this->makeDokter(1)->id,
            'fk_daftarpasien' => $daftarLain->id,
            'nama_operasi' => 'Operasi Kunjungan Lain',
            'tanggal' => now()->addDay()->toDateString(),
            'jam_mulai' => '09:00',
            'jam_selesai' => '10:00',
            'status' => 'dijadwalkan',
        ]);

        $response = $this->actingAs($user)
            ->post("/pemeriksaan/pasien/{$this->encode($daftarSaya->id)}/operasi/status/{$jadwalLain->id}", ['status' => 'berlangsung']);

        $response->assertStatus(404);
    }
}
