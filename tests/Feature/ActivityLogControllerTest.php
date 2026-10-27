<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Module;
use App\Models\Serial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogControllerTest extends TestCase
{
    use RefreshDatabase;

    private function activateLicense(): void
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);
    }

    public function test_user_without_log_access_is_redirected_to_error(): void
    {
        $this->activateLicense();
        $user = User::first();
        $user->access = '999999'; // id modul yang tidak ada

        $response = $this->actingAs($user)->get('/activity-log');

        $response->assertRedirect(route('error'));
    }

    public function test_user_with_log_access_sees_the_page_and_own_tenant_logs_only(): void
    {
        $this->activateLicense();
        $user = User::first();

        $logModuleId = Module::where('code', 'LOG')->value('id');
        $user->access = (string) $logModuleId;
        $user->save();

        ActivityLog::create([
            'klienfk' => $user->klienfk,
            'user_id' => $user->id,
            'action' => 'created',
            'model_type' => 'App\\Models\\Transactions',
            'model_id' => 1,
            'changes' => ['amount' => 10000],
        ]);

        ActivityLog::create([
            'klienfk' => $user->klienfk + 1, // tenant lain
            'user_id' => null,
            'action' => 'created',
            'model_type' => 'App\\Models\\Transactions',
            'model_id' => 2,
            'changes' => ['amount' => 99999],
        ]);

        $page = $this->actingAs($user)->get('/activity-log');
        $page->assertOk();

        $list = $this->actingAs($user)->post('/activity-log/getList', [
            'draw' => 1, 'start' => 0, 'length' => 10,
        ]);

        $list->assertOk();
        $list->assertJsonCount(1, 'data');
        $list->assertJsonPath('data.0.model', 'Transactions');
    }
}
