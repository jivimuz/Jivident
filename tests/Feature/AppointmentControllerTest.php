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

class AppointmentControllerTest extends TestCase
{
    use RefreshDatabase;

    private function authorizedUser(string $moduleCode): User
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);

        $user = User::first();
        $user->access = (string) Module::where('code', $moduleCode)->value('id');
        $user->save();

        return $user;
    }

    private function makeDokterWithTemplate(): array
    {
        $employee = Employee::create(['fullname' => 'drg. Test', 'nik' => 'NIK-'.uniqid(), 'is_pemeriksapasien' => true]);
        $dokter = User::forceCreate([
            'klienfk' => 1,
            'fk_employee' => $employee->id,
            'username' => 'drg_'.uniqid(),
            'display_name' => 'drg. Test',
            'email' => uniqid().'@test.com',
            'password' => bcrypt('password'),
            'access' => '1',
        ]);
        $ruangPraktek = RuangPraktek::create(['klienfk' => 1, 'name' => 'Ruang 1', 'code' => 'R1', 'fk_dokter' => $dokter->id]);

        $nextMonday = Carbon::now()->next(Carbon::MONDAY)->toDateString();
        JadwalDokter::create([
            'klienfk' => 1,
            'fk_dokter' => $dokter->id,
            'fk_ruangpraktek' => $ruangPraktek->id,
            'hari' => 1,
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'durasi_slot' => 30,
            'is_active' => true,
        ]);

        return [$dokter, $ruangPraktek, $nextMonday];
    }

    public function test_jadwal_dokter_page_requires_access(): void
    {
        $this->authorizedUser('BKG'); // akses booking, bukan jadwal dokter
        $response = $this->actingAs(User::first())->get('/jadwal-dokter');

        $response->assertRedirect(route('error'));
    }

    public function test_can_create_jadwal_dokter_template(): void
    {
        $user = $this->authorizedUser('JDD');
        $employee = Employee::create(['fullname' => 'drg. B', 'nik' => 'NIK-'.uniqid(), 'is_pemeriksapasien' => true]);
        $dokter = User::forceCreate([
            'klienfk' => 1, 'fk_employee' => $employee->id, 'username' => 'drgb_'.uniqid(),
            'display_name' => 'drg. B', 'email' => uniqid().'@test.com', 'password' => bcrypt('x'), 'access' => '1',
        ]);
        $ruangPraktek = RuangPraktek::create(['klienfk' => 1, 'name' => 'Ruang B', 'code' => 'RB', 'fk_dokter' => $dokter->id]);

        $response = $this->actingAs($user)->post('/jadwal-dokter/store', [
            'fk_dokter' => $dokter->id,
            'fk_ruangpraktek' => $ruangPraktek->id,
            'hari' => 2,
            'jam_mulai' => '09:00',
            'jam_selesai' => '12:00',
            'durasi_slot' => 20,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('t_jadwaldokter', ['fk_dokter' => $dokter->id, 'hari' => 2]);
    }

    public function test_booking_end_to_end_via_http(): void
    {
        $user = $this->authorizedUser('BKG');
        [$dokter, $ruangPraktek, $tanggal] = $this->makeDokterWithTemplate();
        $patient = Patient::create(['klienfk' => 1, 'norm' => 'RM001', 'nik' => '1', 'nama' => 'Pasien HTTP']);

        $slotsResponse = $this->actingAs($user)->post('/booking/getAvailableSlots', [
            'fk_dokter' => $dokter->id,
            'fk_ruangpraktek' => $ruangPraktek->id,
            'tanggal' => $tanggal,
        ]);
        $slotsResponse->assertOk();
        $slotsResponse->assertJsonCount(4, 'data'); // 08:00-10:00 slot 30 menit = 4 slot

        $bookResponse = $this->actingAs($user)->post('/booking/store', [
            'fk_dokter' => $dokter->id,
            'fk_ruangpraktek' => $ruangPraktek->id,
            'fk_pasien' => $patient->id,
            'tanggal' => $tanggal,
            'jam_mulai' => '08:30',
        ]);
        $bookResponse->assertOk();

        $listResponse = $this->actingAs($user)->post('/booking/getList', ['tanggal' => $tanggal]);
        $listResponse->assertOk();
        $listResponse->assertJsonPath('data.0.pasien', 'Pasien HTTP');

        $cancelResponse = $this->actingAs($user)->post('/booking/cancel/'.$bookResponse->json('data.id'));
        $cancelResponse->assertOk();
        $this->assertDatabaseHas('t_appointments', ['id' => $bookResponse->json('data.id'), 'status' => 'cancelled']);
    }

    public function test_booking_rejects_double_booked_slot_via_http(): void
    {
        $user = $this->authorizedUser('BKG');
        [$dokter, $ruangPraktek, $tanggal] = $this->makeDokterWithTemplate();
        $patientA = Patient::create(['klienfk' => 1, 'norm' => 'RM001', 'nik' => '1', 'nama' => 'Pasien A']);
        $patientB = Patient::create(['klienfk' => 1, 'norm' => 'RM002', 'nik' => '2', 'nama' => 'Pasien B']);

        $this->actingAs($user)->post('/booking/store', [
            'fk_dokter' => $dokter->id, 'fk_ruangpraktek' => $ruangPraktek->id,
            'fk_pasien' => $patientA->id, 'tanggal' => $tanggal, 'jam_mulai' => '08:30',
        ]);

        $second = $this->actingAs($user)->post('/booking/store', [
            'fk_dokter' => $dokter->id, 'fk_ruangpraktek' => $ruangPraktek->id,
            'fk_pasien' => $patientB->id, 'tanggal' => $tanggal, 'jam_mulai' => '08:30',
        ]);

        $second->assertStatus(400);
    }
}
