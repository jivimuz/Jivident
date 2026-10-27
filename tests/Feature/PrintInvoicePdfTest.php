<?php

namespace Tests\Feature;

use App\Models\Module;
use App\Models\Serial;
use App\Models\Transactions;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrintInvoicePdfTest extends TestCase
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
        $user->access = (string) Module::where('code', 'TR')->value('id');
        $user->save();

        return $user;
    }

    public function test_invoice_prints_as_html_by_default(): void
    {
        $user = $this->authorizedUser();

        Transactions::create([
            'klienfk' => $user->klienfk,
            'no_transaction' => 'TRX-PDF-1',
            'transaction_type' => 'in',
            'typecode' => 'PAY',
            'amount' => 50000,
        ]);

        $response = $this->actingAs($user)->get('/transaction/print/TRX-PDF-1/a4');

        $response->assertOk();
        $this->assertStringContainsString('text/html', $response->headers->get('Content-Type'));
    }

    public function test_invoice_downloads_as_pdf_when_requested(): void
    {
        $user = $this->authorizedUser();

        Transactions::create([
            'klienfk' => $user->klienfk,
            'no_transaction' => 'TRX-PDF-2',
            'transaction_type' => 'in',
            'typecode' => 'PAY',
            'amount' => 75000,
        ]);

        $response = $this->actingAs($user)->get('/transaction/print/TRX-PDF-2/a4?pdf=1');

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('invoice-TRX-PDF-2.pdf', $response->headers->get('Content-Disposition'));
    }

    public function test_thermal_mode_also_downloads_as_pdf_when_requested(): void
    {
        $user = $this->authorizedUser();

        Transactions::create([
            'klienfk' => $user->klienfk,
            'no_transaction' => 'TRX-PDF-3',
            'transaction_type' => 'out',
            'typecode' => 'PAY',
            'amount' => 15000,
        ]);

        $response = $this->actingAs($user)->get('/transaction/print/TRX-PDF-3/thermal?pdf=1');

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
    }
}
