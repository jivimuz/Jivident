<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Serial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileSignatureTest extends TestCase
{
    use RefreshDatabase;

    // 1x1 transparent PNG, used as a minimal valid "drawn signature" payload.
    private const SAMPLE_PNG_BASE64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';

    private function authorizedUser(): User
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);

        return User::first();
    }

    protected function tearDown(): void
    {
        foreach (glob(public_path('images/signatures/*')) ?: [] as $file) {
            @unlink($file);
        }

        parent::tearDown();
    }

    public function test_get_signature_returns_null_when_not_set_yet(): void
    {
        $user = $this->authorizedUser();

        $response = $this->actingAs($user)->post('/profile/signature');

        $response->assertOk();
        $this->assertNull($response->json('data.signature_path'));
    }

    public function test_can_save_a_signature(): void
    {
        $user = $this->authorizedUser();

        $response = $this->actingAs($user)->post('/profile/signature/save', [
            'signature' => self::SAMPLE_PNG_BASE64,
        ]);

        $response->assertOk();
        $path = $response->json('data.signature_path');
        $this->assertNotNull($path);
        $this->assertFileExists(public_path($path));

        $employee = Employee::find($user->fk_employee);
        $this->assertSame($path, $employee->signature_path);
    }

    public function test_save_signature_rejects_non_png_payload(): void
    {
        $user = $this->authorizedUser();

        $response = $this->actingAs($user)->postJson('/profile/signature/save', [
            'signature' => 'not-a-valid-data-url',
        ]);

        $response->assertStatus(400);
    }

    public function test_can_delete_a_saved_signature(): void
    {
        $user = $this->authorizedUser();
        $this->actingAs($user)->post('/profile/signature/save', ['signature' => self::SAMPLE_PNG_BASE64]);

        $response = $this->actingAs($user)->post('/profile/signature/delete');

        $response->assertOk();
        $employee = Employee::find($user->fk_employee);
        $this->assertNull($employee->signature_path);
    }
}
