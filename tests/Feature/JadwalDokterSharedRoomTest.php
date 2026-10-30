<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\JadwalDokter;
use App\Models\Module;
use App\Models\RuangPraktek;
use App\Models\Serial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * A ruang praktek can be shared by several doctors across different shifts.
 * Covers the two gaps that previously still forced one-doctor-per-room in
 * practice even though JadwalDokter itself never enforced that: the room
 * picker on the Jadwal Dokter form was filtered by the room's legacy
 * fk_dokter column, and nothing stopped two doctors' shifts from literally
 * overlapping in the same room.
 */
class JadwalDokterSharedRoomTest extends TestCase
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
        $user->access = (string) Module::where('code', 'JDD')->value('id');
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

    public function test_room_picker_offers_a_room_already_owned_by_another_doctor(): void
    {
        $staff = $this->authorizedUser();
        $ownerDokter = $this->makeDokter('drg. Pemilik Ruangan');
        $otherDokter = $this->makeDokter('drg. Lain');
        $ruangan = RuangPraktek::create([
            'klienfk' => 1, 'name' => 'Ruang 1', 'code' => 'R1', 'fk_dokter' => $ownerDokter->id,
        ]);

        $response = $this->actingAs($staff)->get("/jadwal-dokter/getRuangPraktekByDokter/{$otherDokter->id}");

        $response->assertOk();
        $response->assertJsonFragment(['id' => $ruangan->id, 'name' => 'Ruang 1']);
    }

    public function test_can_add_a_second_doctors_non_overlapping_shift_to_the_same_room(): void
    {
        $staff = $this->authorizedUser();
        $pagi = $this->makeDokter('drg. Pagi');
        $siang = $this->makeDokter('drg. Siang');
        $ruangan = RuangPraktek::create(['klienfk' => 1, 'name' => 'Ruang 1', 'code' => 'R1', 'fk_dokter' => $pagi->id]);

        JadwalDokter::create([
            'klienfk' => 1, 'fk_dokter' => $pagi->id, 'fk_ruangpraktek' => $ruangan->id,
            'hari' => 1, 'jam_mulai' => '08:00', 'jam_selesai' => '12:00', 'durasi_slot' => 30, 'is_active' => true,
        ]);

        $response = $this->actingAs($staff)->postJson('/jadwal-dokter/store', [
            'fk_dokter' => $siang->id,
            'fk_ruangpraktek' => $ruangan->id,
            'hari' => 1,
            'jam_mulai' => '13:00',
            'jam_selesai' => '17:00',
            'durasi_slot' => 30,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('t_jadwaldokter', [
            'fk_dokter' => $siang->id, 'fk_ruangpraktek' => $ruangan->id, 'hari' => 1,
        ]);
    }

    public function test_rejects_a_shift_that_overlaps_another_doctor_in_the_same_room(): void
    {
        $staff = $this->authorizedUser();
        $pagi = $this->makeDokter('drg. Pagi');
        $siang = $this->makeDokter('drg. Siang');
        $ruangan = RuangPraktek::create(['klienfk' => 1, 'name' => 'Ruang 1', 'code' => 'R1', 'fk_dokter' => $pagi->id]);

        JadwalDokter::create([
            'klienfk' => 1, 'fk_dokter' => $pagi->id, 'fk_ruangpraktek' => $ruangan->id,
            'hari' => 1, 'jam_mulai' => '08:00', 'jam_selesai' => '12:00', 'durasi_slot' => 30, 'is_active' => true,
        ]);

        $response = $this->actingAs($staff)->postJson('/jadwal-dokter/store', [
            'fk_dokter' => $siang->id,
            'fk_ruangpraktek' => $ruangan->id,
            'hari' => 1,
            'jam_mulai' => '11:00', // overlaps drg. Pagi's 08:00-12:00
            'jam_selesai' => '14:00',
            'durasi_slot' => 30,
        ]);

        $response->assertStatus(400);
        $this->assertDatabaseMissing('t_jadwaldokter', [
            'fk_dokter' => $siang->id, 'fk_ruangpraktek' => $ruangan->id,
        ]);
    }
}
