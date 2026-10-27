<?php

namespace Tests\Feature;

use App\Models\Serial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthApiMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        // Restore the phpunit.xml baseline (not unset!) — unsetting lets the next
        // test's fresh Application boot reload TYPE_LOCAL_SERVER from the real
        // .env file instead, which silently leaks the developer's local value.
        $this->setTypeLocalServer('0');
        parent::tearDown();
    }

    private function setTypeLocalServer(string $value): void
    {
        putenv("TYPE_LOCAL_SERVER={$value}");
        $_ENV['TYPE_LOCAL_SERVER'] = $value;
        $_SERVER['TYPE_LOCAL_SERVER'] = $value;
    }

    public function test_redirects_to_license_when_no_serial_row_exists(): void
    {
        Serial::query()->delete();

        $response = $this->get('/');

        $response->assertRedirect(route('license', ['expired' => 1]));
    }

    public function test_redirects_to_license_when_serial_code_is_empty(): void
    {
        // Migration seeds exactly one row with serial_code = null.
        $response = $this->get('/');

        $response->assertRedirect(route('license'));
    }

    public function test_type_local_server_2_bypasses_license_gate_entirely(): void
    {
        $this->setTypeLocalServer('2');
        Serial::query()->delete(); // would normally force a license redirect

        $response = $this->get('/');

        // Gate bypassed -> falls through to the normal `auth` middleware,
        // which redirects an unauthenticated request to login, not license.
        $response->assertRedirect(route('login'));
    }

    public function test_does_not_call_remote_endpoint_when_last_checked_recently(): void
    {
        Http::fake();

        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now()->subHour(), // within the 3h window
        ]);

        $response = $this->get('/');

        Http::assertNothingSent();
        $response->assertRedirect(route('login'));
    }

    public function test_calls_remote_endpoint_when_never_checked_before(): void
    {
        Http::fake([
            '*' => Http::response(['expires' => now()->addYear()->toDateString()], 200),
        ]);

        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => null,
        ]);

        $response = $this->get('/');

        Http::assertSentCount(1);
        $response->assertRedirect(route('login'));
    }

    public function test_redirects_to_license_when_valid_until_has_expired(): void
    {
        Http::fake(); // isolate from remote; last_check recent so it won't be called

        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->subDay()->toDateString(),
            'last_check' => now()->subHour(),
        ]);

        $response = $this->get('/');

        $response->assertRedirect(route('license', ['expired' => 1]));
    }
}
