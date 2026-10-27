<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Module;
use App\Models\Ptkp;
use App\Models\Serial;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollControllerTest extends TestCase
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
        $user->access = (string) Module::where('code', 'MTE')->value('id');
        $user->save();

        return $user;
    }

    private function setBasicBpjsRates(): void
    {
        Setting::where('parameter', 'bpjs_kesehatan_company_persen')->update(['value' => 4]);
        Setting::where('parameter', 'bpjs_kesehatan_employee_persen')->update(['value' => 1]);
        Setting::where('parameter', 'bpjs_jht_company_persen')->update(['value' => 3.7]);
        Setting::where('parameter', 'bpjs_jht_employee_persen')->update(['value' => 2]);
    }

    public function test_save_statutory_updates_employee_ptkp_and_bpjs_numbers(): void
    {
        $user = $this->authorizedUser();
        $employee = Employee::first();
        $ptkp = Ptkp::where('name', 'K1')->first();
        $idn = base64_encode($employee->id);

        $response = $this->actingAs($user)->post("/employees/{$idn}/statutory/save", [
            'fk_ptkp' => $ptkp->id,
            'npwp' => '12.345.678.9-012.000',
            'bpjs_kesehatan_no' => '0001234567890',
            'bpjs_tk_no' => '9988776655',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('m_employee', [
            'id' => $employee->id,
            'fk_ptkp' => $ptkp->id,
            'bpjs_kesehatan_no' => '0001234567890',
        ]);
    }

    public function test_save_salary_updates_gajipokok(): void
    {
        $user = $this->authorizedUser();
        $employee = Employee::first();
        $idn = base64_encode($employee->id);

        $response = $this->actingAs($user)->post("/employees/{$idn}/salary/save", [
            'gajipokok' => 8500000,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('m_employee', ['id' => $employee->id, 'gajipokok' => 8500000]);
    }

    public function test_calculate_payroll_preview_end_to_end(): void
    {
        $user = $this->authorizedUser();
        $this->setBasicBpjsRates();
        $employee = Employee::first();
        $employee->update(['fk_ptkp' => Ptkp::where('name', 'TK0')->value('id'), 'gajipokok' => 10000000]);
        $idn = base64_encode($employee->id);

        $response = $this->actingAs($user)->post("/employees/{$idn}/salary/calculate", [
            'periode' => '2026-03',
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.ter_kategori', 'A');
        $this->assertFalse($response->json('data.already_exists'));
    }

    public function test_generate_slip_then_calculate_flags_already_exists(): void
    {
        $user = $this->authorizedUser();
        $this->setBasicBpjsRates();
        $employee = Employee::first();
        $employee->update(['fk_ptkp' => Ptkp::where('name', 'TK0')->value('id'), 'gajipokok' => 10000000]);
        $idn = base64_encode($employee->id);

        $generate = $this->actingAs($user)->post("/employees/{$idn}/salary/generateSlip", [
            'periode' => '2026-03',
        ]);
        $generate->assertOk();

        $preview = $this->actingAs($user)->post("/employees/{$idn}/salary/calculate", [
            'periode' => '2026-03',
        ]);
        $this->assertTrue($preview->json('data.already_exists'));

        $slips = $this->actingAs($user)->post("/employees/{$idn}/salary/getSlips");
        $slips->assertOk();
        $slips->assertJsonCount(1, 'data');
    }

    public function test_calculate_fails_gracefully_without_ptkp(): void
    {
        $user = $this->authorizedUser();
        $employee = Employee::first();
        $employee->update(['fk_ptkp' => null, 'gajipokok' => 10000000]);
        $idn = base64_encode($employee->id);

        $response = $this->actingAs($user)->post("/employees/{$idn}/salary/calculate", [
            'periode' => '2026-03',
        ]);

        $response->assertStatus(400);
        $response->assertJsonStructure(['error']);
    }
}
