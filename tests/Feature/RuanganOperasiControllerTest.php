<?php

namespace Tests\Feature;

use App\Models\JadwalOperasi;
use App\Models\Module;
use App\Models\Patient;
use App\Models\RuanganOperasi;
use App\Models\Serial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RuanganOperasiControllerTest extends TestCase
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
        $user->access = (string) Module::where('code', 'OK')->value('id');
        $user->save();

        return $user;
    }

    public function test_page_requires_access(): void
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);
        $user = User::first();
        $user->access = '999999';
        $user->save();

        $response = $this->actingAs($user)->get('/ruangan-operasi');

        $response->assertRedirect(route('error'));
    }

    public function test_store_creates_a_room(): void
    {
        $user = $this->authorizedUser();

        $response = $this->actingAs($user)->post('/ruangan-operasi/store', [
            'nama' => 'OK 1',
            'kode' => 'OK1',
            'status' => 'tersedia',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('m_ruangan_operasi', ['nama' => 'OK 1', 'klienfk' => $user->klienfk]);
    }

    public function test_get_list_only_shows_rooms_from_the_same_branch(): void
    {
        $user = $this->authorizedUser(klienfk: 1);
        RuanganOperasi::create(['klienfk' => 1, 'nama' => 'OK Cabang Saya', 'status' => 'tersedia']);
        RuanganOperasi::create(['klienfk' => 2, 'nama' => 'OK Cabang Lain', 'status' => 'tersedia']);

        $response = $this->actingAs($user)->post('/ruangan-operasi/getList');

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('nama');
        $this->assertTrue($names->contains('OK Cabang Saya'));
        $this->assertFalse($names->contains('OK Cabang Lain'));
    }

    public function test_destroy_is_rejected_when_room_has_an_active_schedule(): void
    {
        $user = $this->authorizedUser();
        $ruangan = RuanganOperasi::create(['klienfk' => $user->klienfk, 'nama' => 'OK 2', 'status' => 'tersedia']);
        $patient = Patient::create(['klienfk' => $user->klienfk, 'norm' => 'RM-'.uniqid(), 'nik' => '1', 'nama' => 'Pasien']);

        JadwalOperasi::create([
            'klienfk' => $user->klienfk,
            'fk_ruangan_operasi' => $ruangan->id,
            'fk_pasien' => $patient->id,
            'fk_dokter' => $user->id,
            'nama_operasi' => 'Operasi X',
            'tanggal' => now()->addDay()->toDateString(),
            'jam_mulai' => '09:00',
            'jam_selesai' => '10:00',
            'status' => 'dijadwalkan',
        ]);

        $response = $this->actingAs($user)->post("/ruangan-operasi/delete/{$ruangan->id}");

        $response->assertStatus(400);
        $this->assertDatabaseHas('m_ruangan_operasi', ['id' => $ruangan->id, 'deleted_at' => null]);
    }

    public function test_destroy_succeeds_when_room_has_no_active_schedule(): void
    {
        $user = $this->authorizedUser();
        $ruangan = RuanganOperasi::create(['klienfk' => $user->klienfk, 'nama' => 'OK 3', 'status' => 'tersedia']);

        $response = $this->actingAs($user)->post("/ruangan-operasi/delete/{$ruangan->id}");

        $response->assertOk();
        $this->assertSoftDeleted('m_ruangan_operasi', ['id' => $ruangan->id]);
    }

    public function test_cannot_update_a_room_from_another_branch(): void
    {
        $user = $this->authorizedUser(klienfk: 1);
        $ruangan = RuanganOperasi::create(['klienfk' => 2, 'nama' => 'OK Cabang Lain', 'status' => 'tersedia']);

        $response = $this->actingAs($user)->post("/ruangan-operasi/update/{$ruangan->id}", [
            'nama' => 'Diubah',
            'status' => 'tersedia',
        ]);

        $response->assertStatus(404);
        $this->assertDatabaseHas('m_ruangan_operasi', ['id' => $ruangan->id, 'nama' => 'OK Cabang Lain']);
    }
}
