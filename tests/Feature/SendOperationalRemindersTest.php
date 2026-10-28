<?php

namespace Tests\Feature;

use App\Helpers\ProductQtyHelper;
use App\Mail\OperationalReminderMail;
use App\Models\Product;
use App\Models\ReceiveOrder;
use App\Models\ReceiveOrderDetail;
use App\Models\Serial;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendOperationalRemindersTest extends TestCase
{
    use RefreshDatabase;

    public function test_skips_sending_when_notification_email_not_configured(): void
    {
        Mail::fake();

        $this->artisan('reminders:send')->assertExitCode(0);

        Mail::assertNothingSent();
    }

    public function test_skips_sending_when_nothing_needs_attention(): void
    {
        Mail::fake();
        Setting::where('parameter', 'notification_email')->update(['value' => 'admin@example.com']);

        $this->artisan('reminders:send')->assertExitCode(0);

        Mail::assertNothingSent();
    }

    public function test_sends_reminder_when_a_product_is_out_of_stock(): void
    {
        Mail::fake();
        Setting::where('parameter', 'notification_email')->update(['value' => 'admin@example.com']);

        Product::create(['klienfk' => 1, 'code' => 'P-'.uniqid(), 'name' => 'Obat Kosong']);

        $this->artisan('reminders:send')->assertExitCode(0);

        Mail::assertSent(OperationalReminderMail::class, function ($mail) {
            return in_array('Obat Kosong', $mail->outOfStockProducts, true);
        });
    }

    public function test_sends_reminder_when_a_lot_is_expiring_soon(): void
    {
        Mail::fake();
        Setting::where('parameter', 'notification_email')->update(['value' => 'admin@example.com']);

        $product = Product::create(['klienfk' => 1, 'code' => 'P-'.uniqid(), 'name' => 'Obat Mau Expired']);
        $receiveOrder = ReceiveOrder::create([
            'klienfk' => 1,
            'no_receipt' => 'RO-'.uniqid(),
            'supplier_id' => 1,
            'no_faktur' => 'FKT-'.uniqid(),
            'tgl_faktur' => now()->toDateString(),
        ]);
        ReceiveOrderDetail::create([
            'klienfk' => 1,
            'receiveorder_id' => $receiveOrder->id,
            'product_id' => $product->id,
            'batch_no' => 'BATCH-'.uniqid(),
            'expired_date' => now()->addDays(10)->toDateString(),
            'qty' => 5,
            'subtotal' => 0,
        ]);
        app(ProductQtyHelper::class)->processReceiveOrder($receiveOrder->id, 1);

        $this->artisan('reminders:send')->assertExitCode(0);

        Mail::assertSent(OperationalReminderMail::class, function ($mail) use ($product) {
            return $mail->expiringLots->contains(fn ($lot) => $lot->product_id === $product->id);
        });
    }

    public function test_sends_reminder_when_license_is_expiring_soon(): void
    {
        Mail::fake();
        Setting::where('parameter', 'notification_email')->update(['value' => 'admin@example.com']);

        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addDays(5)->toDateString(),
        ]);

        $this->artisan('reminders:send')->assertExitCode(0);

        Mail::assertSent(OperationalReminderMail::class, function ($mail) {
            return ! empty($mail->licenseExpiryDate);
        });
    }

    public function test_does_not_warn_about_license_that_is_not_close_to_expiry(): void
    {
        Mail::fake();
        Setting::where('parameter', 'notification_email')->update(['value' => 'admin@example.com']);

        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonths(6)->toDateString(),
        ]);

        $this->artisan('reminders:send')->assertExitCode(0);

        Mail::assertNothingSent();
    }
}
