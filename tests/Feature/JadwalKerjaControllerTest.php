<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\JadwalKerja;
use App\Models\Module;
use App\Models\Serial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JadwalKerjaControllerTest extends TestCase
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
        $user->access = (string) Module::where('code', 'JDW')->value('id');
        $user->save();

        return $user;
    }

    public function test_overnight_shift_from_template_is_scheduled_to_next_day(): void
    {
        $user = $this->authorizedUser();
        $employee = Employee::first();
        $template = JadwalKerja::create([
            'kode' => 'MLM',
            'nama' => 'Shift Malam',
            'jammasuk' => '22:00',
            'jampulang' => '08:00',
        ]);

        $response = $this->actingAs($user)->post('/jadwal-kerja/store', [
            'fk_employee' => $employee->id,
            'fk_jadwalkerja' => $template->id,
            'tanggal' => '2026-03-02', // Senin
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('t_jadwalkerjakaryawan', [
            'fk_employee' => $employee->id,
            'jam_masuk' => '2026-03-02 22:00:00',
            'jam_pulang' => '2026-03-03 08:00:00',
        ]);
    }

    public function test_same_day_shift_from_template_does_not_roll_over(): void
    {
        $user = $this->authorizedUser();
        $employee = Employee::first();
        $template = JadwalKerja::create([
            'kode' => 'PG',
            'nama' => 'Shift Pagi',
            'jammasuk' => '08:00',
            'jampulang' => '16:00',
        ]);

        $response = $this->actingAs($user)->post('/jadwal-kerja/store', [
            'fk_employee' => $employee->id,
            'fk_jadwalkerja' => $template->id,
            'tanggal' => '2026-03-02',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('t_jadwalkerjakaryawan', [
            'fk_employee' => $employee->id,
            'jam_masuk' => '2026-03-02 08:00:00',
            'jam_pulang' => '2026-03-02 16:00:00',
        ]);
    }

    public function test_custom_overnight_shift_without_template(): void
    {
        $user = $this->authorizedUser();
        $employee = Employee::first();

        $response = $this->actingAs($user)->post('/jadwal-kerja/store', [
            'fk_employee' => $employee->id,
            'tanggal' => '2026-03-02',
            'jam_masuk_time' => '22:00',
            'jam_pulang_time' => '08:00',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('t_jadwalkerjakaryawan', [
            'fk_employee' => $employee->id,
            'jam_masuk' => '2026-03-02 22:00:00',
            'jam_pulang' => '2026-03-03 08:00:00',
        ]);
    }

    public function test_cannot_delete_template_still_used_by_a_roster_entry(): void
    {
        $user = $this->authorizedUser();
        $employee = Employee::first();
        $template = JadwalKerja::create([
            'kode' => 'PG',
            'nama' => 'Shift Pagi',
            'jammasuk' => '08:00',
            'jampulang' => '16:00',
        ]);

        $this->actingAs($user)->post('/jadwal-kerja/store', [
            'fk_employee' => $employee->id,
            'fk_jadwalkerja' => $template->id,
            'tanggal' => '2026-03-02',
        ]);

        $response = $this->actingAs($user)->post("/jadwal-kerja/deleteTemplate/{$template->id}");

        $response->assertStatus(400);
        $this->assertDatabaseHas('m_jadwalkerja', ['id' => $template->id]);
    }
}
