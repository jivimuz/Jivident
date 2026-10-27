<?php

namespace Tests\Feature;

use App\Models\DaftarPasien;
use App\Models\Serial;
use App\Models\Transactions;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeDashboardTest extends TestCase
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

    public function test_dashboard_shows_real_counts_for_logged_in_user(): void
    {
        $this->activateLicense();

        $user = User::first();

        DaftarPasien::create([
            'klienfk' => $user->klienfk,
            'noregistrasi' => 'REG-1',
            'ruanganfk' => 1,
            'pasienfk' => 1,
            'tglregistrasi' => now(),
            'noantrian' => 'A1',
            'biaya_registrasi' => 50000,
        ]);

        Transactions::create([
            'klienfk' => $user->klienfk,
            'no_transaction' => 'TRX-1',
            'transaction_type' => 'in',
            'typecode' => 'PAY',
            'amount' => 75000,
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertOk();
        $response->assertViewIs('menu');
        $response->assertViewHas('patientsToday', 1);
        $response->assertViewHas('revenueToday', 75000.0);
        $response->assertViewHas('outOfStockCount');
        $response->assertSee('Pasien Hari Ini');
    }

    public function test_dashboard_does_not_count_another_tenants_data(): void
    {
        $this->activateLicense();

        $user = User::first();

        DaftarPasien::create([
            'klienfk' => $user->klienfk + 1, // tenant lain
            'noregistrasi' => 'REG-2',
            'ruanganfk' => 1,
            'pasienfk' => 1,
            'tglregistrasi' => now(),
            'noantrian' => 'A1',
            'biaya_registrasi' => 50000,
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertOk();
        $response->assertViewHas('patientsToday', 0);
    }
}
