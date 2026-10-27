<?php

namespace Tests\Feature;

use App\Models\Cuti;
use App\Models\Employee;
use App\Models\JenisCuti;
use App\Models\Module;
use App\Models\Serial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CutiControllerTest extends TestCase
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
        $user->access = (string) Module::where('code', 'CUT')->value('id');
        $user->save();

        return $user;
    }

    public function test_can_submit_a_leave_request(): void
    {
        $user = $this->authorizedUser();
        $employee = Employee::first();
        $jenisCuti = JenisCuti::where('name', 'Cuti Tahunan')->first();

        $response = $this->actingAs($user)->post('/cuti/store', [
            'fk_employee' => $employee->id,
            'fk_jeniscuti' => $jenisCuti->id,
            'startdate' => '2026-03-10',
            'enddate' => '2026-03-12',
            'notes' => 'Acara keluarga',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('t_cuti', [
            'fk_employee' => $employee->id,
            'status' => 'pending',
            'notes' => 'Acara keluarga',
        ]);
    }

    public function test_approving_sets_approver_and_timestamp(): void
    {
        $user = $this->authorizedUser();
        $employee = Employee::first();
        $jenisCuti = JenisCuti::first();

        $store = $this->actingAs($user)->post('/cuti/store', [
            'fk_employee' => $employee->id,
            'fk_jeniscuti' => $jenisCuti->id,
            'startdate' => '2026-03-10',
            'enddate' => '2026-03-12',
        ]);
        $cutiId = $store->json('data.id');

        $approve = $this->actingAs($user)->post("/cuti/approve/{$cutiId}");
        $approve->assertOk();

        $cuti = Cuti::find($cutiId);
        $this->assertSame('approved', $cuti->status);
        $this->assertSame($user->id, $cuti->approved_by);
        $this->assertNotNull($cuti->approved_at);
    }

    public function test_rejecting_requires_no_special_input_but_stores_reason(): void
    {
        $user = $this->authorizedUser();
        $employee = Employee::first();
        $jenisCuti = JenisCuti::first();

        $store = $this->actingAs($user)->post('/cuti/store', [
            'fk_employee' => $employee->id,
            'fk_jeniscuti' => $jenisCuti->id,
            'startdate' => '2026-03-10',
            'enddate' => '2026-03-12',
        ]);
        $cutiId = $store->json('data.id');

        $reject = $this->actingAs($user)->post("/cuti/reject/{$cutiId}", [
            'reason' => 'Bertepatan dengan jadwal praktek penting',
        ]);
        $reject->assertOk();

        $cuti = Cuti::find($cutiId);
        $this->assertSame('rejected', $cuti->status);
        $this->assertSame('Bertepatan dengan jadwal praktek penting', $cuti->rejected_reason);
    }

    public function test_cannot_approve_an_already_processed_request(): void
    {
        $user = $this->authorizedUser();
        $employee = Employee::first();
        $jenisCuti = JenisCuti::first();

        $store = $this->actingAs($user)->post('/cuti/store', [
            'fk_employee' => $employee->id,
            'fk_jeniscuti' => $jenisCuti->id,
            'startdate' => '2026-03-10',
            'enddate' => '2026-03-12',
        ]);
        $cutiId = $store->json('data.id');

        $this->actingAs($user)->post("/cuti/approve/{$cutiId}");
        $second = $this->actingAs($user)->post("/cuti/approve/{$cutiId}");

        $second->assertStatus(400);
    }

    public function test_cannot_delete_a_processed_request(): void
    {
        $user = $this->authorizedUser();
        $employee = Employee::first();
        $jenisCuti = JenisCuti::first();

        $store = $this->actingAs($user)->post('/cuti/store', [
            'fk_employee' => $employee->id,
            'fk_jeniscuti' => $jenisCuti->id,
            'startdate' => '2026-03-10',
            'enddate' => '2026-03-12',
        ]);
        $cutiId = $store->json('data.id');

        $this->actingAs($user)->post("/cuti/approve/{$cutiId}");
        $delete = $this->actingAs($user)->post("/cuti/delete/{$cutiId}");

        $delete->assertStatus(400);
        $this->assertDatabaseHas('t_cuti', ['id' => $cutiId]);
    }

    public function test_sick_leave_type_flags_issakit(): void
    {
        $user = $this->authorizedUser();
        $employee = Employee::first();
        $sakit = JenisCuti::where('name', 'Sakit')->first();

        $store = $this->actingAs($user)->post('/cuti/store', [
            'fk_employee' => $employee->id,
            'fk_jeniscuti' => $sakit->id,
            'startdate' => '2026-03-10',
            'enddate' => '2026-03-10',
        ]);

        $this->assertDatabaseHas('t_cuti', [
            'id' => $store->json('data.id'),
            'issakit' => 1,
        ]);
    }
}
