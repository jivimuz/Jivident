<?php

namespace App\Console\Commands;

use App\Helpers\ProductQtyHelper;
use App\Mail\OperationalReminderMail;
use App\Models\Product;
use App\Models\Serial;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendOperationalReminders extends Command
{
    protected $signature = 'reminders:send';

    protected $description = 'Kirim email reminder operasional (stok habis, produk mendekati expired, lisensi mendekati kadaluarsa) ke alamat email yang diatur di Setting';

    public function handle(): int
    {
        $email = Setting::where('parameter', 'notification_email')->value('value');

        if (empty($email)) {
            $this->info('notification_email belum diisi di Setting — reminder dilewati.');

            return self::SUCCESS;
        }

        $qtyHelper = app(ProductQtyHelper::class);

        $productIds = Product::pluck('id', 'id');
        $balances = $qtyHelper->getBalancesByProducts($productIds->keys()->all());
        $productNames = Product::whereIn('id', array_keys($balances))->pluck('name', 'id');

        $outOfStockProducts = [];
        foreach ($balances as $productId => $qty) {
            if ($qty <= 0) {
                $outOfStockProducts[] = $productNames[$productId] ?? "Produk #{$productId}";
            }
        }

        $expiringLots = $qtyHelper->getLotsExpiringSoon(30);

        $licenseExpiryDate = null;
        $serial = Serial::first();
        if ($serial && $serial->valid_until) {
            $validUntil = Carbon::parse($serial->valid_until);
            if ($validUntil->lessThanOrEqualTo(now()->addDays(14))) {
                $licenseExpiryDate = $validUntil->format('d F Y');
            }
        }

        if (empty($outOfStockProducts) && $expiringLots->isEmpty() && ! $licenseExpiryDate) {
            $this->info('Tidak ada yang perlu di-reminder hari ini.');

            return self::SUCCESS;
        }

        Mail::to($email)->send(new OperationalReminderMail($outOfStockProducts, $expiringLots, $licenseExpiryDate));

        $this->info("Reminder terkirim ke {$email}.");

        return self::SUCCESS;
    }
}
