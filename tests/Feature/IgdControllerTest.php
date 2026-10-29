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

class IgdControllerTest extends TestCase
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
        $user->access = (string) Module::where('code', 'IGD')->value('id');
        $user->save();

        return $user;
    }

    private function makeRuanganWithDokter(): RuangPraktek
    {
        $employee = Employee::create(['fullname' => 'drg. Jaga IGD', 'nik' => 'NIK-'.uniqid(), 'is_pemeriksapasien' => true]);
        $dokter = User::forceCreate([
            'klienfk' => 1,
            'fk_employee' => $employee->id,
            'username' => 'drg_igd_'.uniqid(),
            'display_name' => 'drg. Jaga IGD',
            'email' => uniqid().'@test.com',
            'password' => bcrypt('password'),
            'access' => '1',
        ]);

        return RuangPraktek::create(['klienfk' => 1, 'name' => 'Ruang IGD', 'code' => 'IGD', 'fk_dokter' => $dokter->id]);
    }

    private function makePatient(array $overrides = []): Patient
    {
        return Patient::create(array_merge([
            'klienfk' => 1,
            'norm' => 'RM-'.uniqid(),
            'nik' => (string) random_int(1000000000, 9999999999),
            'nama' => 'Pasien IGD',
        ], $overrides));
    }

    public function test_igd_page_requires_access(): void
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);
        $user = User::first();
        $user->access = '999999';
        $user->save();

        $response = $this->actingAs($user)->get('/igd');

        $response->assertRedirect(route('error'));
    }

    public function test_can_register_a_patient_to_igd_with_triase(): void
    {
        $user = $this->authorizedUser();
        $ruangan = $this->makeRuanganWithDokter();
        $patient = $this->makePatient();

        $response = $this->actingAs($user)->post('/igd/store', [
            'pasienfk' => $patient->id,
            'ruanganfk' => $ruangan->id,
            'triase' => 'Merah',
            'keluhan' => 'Nyeri dada',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('t_daftarpasien', [
            'pasienfk' => $patient->id,
            'triase' => 'Merah',
            'status' => 'Dalam Antrian',
        ]);
    }

    public function test_store_rejects_room_without_assigned_doctor(): void
    {
        $user = $this->authorizedUser();
        $ruangan = RuangPraktek::create(['klienfk' => 1, 'name' => 'Ruang Kosong', 'code' => 'RK', 'fk_dokter' => null]);
        $patient = $this->makePatient();

        $response = $this->actingAs($user)->post('/igd/store', [
            'pasienfk' => $patient->id,
            'ruanganfk' => $ruangan->id,
            'triase' => 'Hijau',
        ]);

        $response->assertStatus(400);
    }

    public function test_queue_list_is_sorted_by_triase_priority_not_registration_time(): void
    {
        $user = $this->authorizedUser();
        $ruangan = $this->makeRuanganWithDokter();

        $pasienHijau = $this->makePatient(['nama' => 'Pasien Hijau']);
        $this->actingAs($user)->post('/igd/store', [
            'pasienfk' => $pasienHijau->id,
            'ruanganfk' => $ruangan->id,
            'triase' => 'Hijau',
        ]);

        $pasienMerah = $this->makePatient(['nama' => 'Pasien Merah']);
        $this->actingAs($user)->post('/igd/store', [
            'pasienfk' => $pasienMerah->id,
            'ruanganfk' => $ruangan->id,
            'triase' => 'Merah',
        ]);

        $response = $this->actingAs($user)->post('/igd/getQueueList');

        $response->assertOk();
        $data = $response->json('data');
        // Hijau mendaftar lebih dulu, tapi Merah harus tetap di urutan pertama.
        $this->assertSame('Merah', $data[0]['triase']);
        $this->assertSame('Hijau', $data[1]['triase']);
    }

    public function test_can_update_triase_of_a_waiting_patient(): void
    {
        $user = $this->authorizedUser();
        $ruangan = $this->makeRuanganWithDokter();
        $patient = $this->makePatient();

        $store = $this->actingAs($user)->post('/igd/store', [
            'pasienfk' => $patient->id,
            'ruanganfk' => $ruangan->id,
            'triase' => 'Hijau',
        ]);
        $daftarId = $store->json('data.id');

        $response = $this->actingAs($user)->post("/igd/updateTriase/{$daftarId}", [
            'triase' => 'Merah',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('t_daftarpasien', ['id' => $daftarId, 'triase' => 'Merah']);
    }
}
