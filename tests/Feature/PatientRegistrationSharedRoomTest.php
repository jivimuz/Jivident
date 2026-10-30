<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\JadwalDokter;
use App\Models\Module;
use App\Models\Patient;
use App\Models\RuangPraktek;
use App\Models\Serial;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * A ruang praktek can now be shared by several doctors across different
 * JadwalDokter shifts — the doctor for a walk-in registration is resolved
 * from the room's schedule at the chosen date/time instead of a single
 * fixed fk_dokter on the room.
 */
class PatientRegistrationSharedRoomTest extends TestCase
{
    use RefreshDatabase;

    private function authorizedUser(): User
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);

        $user = User::first();
        $user->klienfk = 1;
        $user->access = (string) Module::where('code', 'PA')->value('id');
        $user->save();

        return $user;
    }

    private function makeDokter(string $name): User
    {
        $employee = Employee::create([
            'fullname' => $name,
            'nik' => 'NIK-'.uniqid(),
            'is_pemeriksapasien' => true,
        ]);

        return User::forceCreate([
            'klienfk' => 1,
            'fk_employee' => $employee->id,
            'username' => 'dr_'.uniqid(),
            'display_name' => $name,
            'email' => uniqid().'@test.com',
            'password' => bcrypt('password'),
            'access' => '1',
        ]);
    }

    public function test_two_doctors_can_share_the_same_room_on_different_shifts(): void
    {
        $staff = $this->authorizedUser();
        $pagi = $this->makeDokter('drg. Pagi');
        $siang = $this->makeDokter('drg. Siang');
        $ruangan = RuangPraktek::create(['klienfk' => 1, 'name' => 'Ruang 1', 'code' => 'R1']);

        $hari = Carbon::now()->dayOfWeek;
        JadwalDokter::create([
            'klienfk' => 1, 'fk_dokter' => $pagi->id, 'fk_ruangpraktek' => $ruangan->id,
            'hari' => $hari, 'jam_mulai' => '08:00', 'jam_selesai' => '12:00', 'is_active' => true,
        ]);
        JadwalDokter::create([
            'klienfk' => 1, 'fk_dokter' => $siang->id, 'fk_ruangpraktek' => $ruangan->id,
            'hari' => $hari, 'jam_mulai' => '13:00', 'jam_selesai' => '17:00', 'is_active' => true,
        ]);

        $pasienPagi = Patient::create(['klienfk' => 1, 'norm' => 'RM-'.uniqid(), 'nik' => '1', 'nama' => 'Pasien Pagi']);
        $pasienSiang = Patient::create(['klienfk' => 1, 'norm' => 'RM-'.uniqid(), 'nik' => '2', 'nama' => 'Pasien Siang']);

        $this->actingAs($staff);

        $today = Carbon::now()->toDateString();

        $resolvedPagi = $this->postJson('/patient/resolve-dokter', [
            'ruanganfk' => $ruangan->id,
            'tglregistrasi' => "{$today} 09:00",
        ])->json();
        $this->assertTrue($resolvedPagi['auto']);
        $this->assertSame($pagi->id, $resolvedPagi['dokter']['id']);

        $resolvedSiang = $this->postJson('/patient/resolve-dokter', [
            'ruanganfk' => $ruangan->id,
            'tglregistrasi' => "{$today} 14:00",
        ])->json();
        $this->assertTrue($resolvedSiang['auto']);
        $this->assertSame($siang->id, $resolvedSiang['dokter']['id']);

        $idPagi = base64_encode(base64_encode((string) $pasienPagi->id));
        $responsePagi = $this->postJson("/patient/register-patient/{$idPagi}", [
            'ruanganfk' => $ruangan->id,
            'dokterfk' => $pagi->id,
            'tglregistrasi' => "{$today} 09:00",
            'jenispembayaran' => 'UMUM',
        ]);
        $responsePagi->assertOk();
        $this->assertDatabaseHas('t_daftarpasien', ['pasienfk' => $pasienPagi->id, 'dokterfk' => $pagi->id]);

        $idSiang = base64_encode(base64_encode((string) $pasienSiang->id));
        $responseSiang = $this->postJson("/patient/register-patient/{$idSiang}", [
            'ruanganfk' => $ruangan->id,
            'dokterfk' => $siang->id,
            'tglregistrasi' => "{$today} 14:00",
            'jenispembayaran' => 'UMUM',
        ]);
        $responseSiang->assertOk();
        $this->assertDatabaseHas('t_daftarpasien', ['pasienfk' => $pasienSiang->id, 'dokterfk' => $siang->id]);
    }

    public function test_resolve_dokter_falls_back_to_room_candidates_when_nobody_is_on_duty(): void
    {
        $staff = $this->authorizedUser();
        $pagi = $this->makeDokter('drg. Pagi');
        $ruangan = RuangPraktek::create(['klienfk' => 1, 'name' => 'Ruang 1', 'code' => 'R1']);

        JadwalDokter::create([
            'klienfk' => 1, 'fk_dokter' => $pagi->id, 'fk_ruangpraktek' => $ruangan->id,
            'hari' => Carbon::now()->dayOfWeek, 'jam_mulai' => '08:00', 'jam_selesai' => '12:00', 'is_active' => true,
        ]);

        $this->actingAs($staff);

        $response = $this->postJson('/patient/resolve-dokter', [
            'ruanganfk' => $ruangan->id,
            'tglregistrasi' => Carbon::now()->toDateString().' 20:00', // luar jam praktek
        ])->json();

        $this->assertFalse($response['auto']);
        $this->assertNull($response['dokter']);
        $this->assertCount(1, $response['candidates']);
        $this->assertSame($pagi->id, $response['candidates'][0]['id']);
    }

    public function test_cannot_register_with_a_doctor_who_does_not_belong_to_the_branch(): void
    {
        $staff = $this->authorizedUser();
        $ruangan = RuangPraktek::create(['klienfk' => 1, 'name' => 'Ruang 1', 'code' => 'R1']);
        $pasien = Patient::create(['klienfk' => 1, 'norm' => 'RM-'.uniqid(), 'nik' => '3', 'nama' => 'Pasien']);

        $employeeLain = Employee::create(['fullname' => 'drg. Cabang Lain', 'nik' => 'NIK-'.uniqid(), 'is_pemeriksapasien' => true]);
        $dokterCabangLain = User::forceCreate([
            'klienfk' => 2, 'fk_employee' => $employeeLain->id, 'username' => 'dr_'.uniqid(),
            'display_name' => 'drg. Cabang Lain', 'email' => uniqid().'@test.com', 'password' => bcrypt('x'), 'access' => '1',
        ]);

        $this->actingAs($staff);

        $id = base64_encode(base64_encode((string) $pasien->id));
        $response = $this->postJson("/patient/register-patient/{$id}", [
            'ruanganfk' => $ruangan->id,
            'dokterfk' => $dokterCabangLain->id,
            'tglregistrasi' => Carbon::now()->toDateString().' 09:00',
            'jenispembayaran' => 'UMUM',
        ]);

        $response->assertStatus(400);
        $this->assertDatabaseMissing('t_daftarpasien', ['pasienfk' => $pasien->id]);
    }
}
