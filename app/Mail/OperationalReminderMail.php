<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Collection;

class OperationalReminderMail extends Mailable
{
    // Deliberately NOT ShouldQueue: this app targets shared hosting where a
    // persistent queue worker usually isn't available, so a queued mail would
    // just sit in the `jobs` table forever. Sent synchronously by the
    // scheduled command instead, which only needs cron (schedule:run), not a worker.

    public function __construct(
        public array $outOfStockProducts,
        public Collection $expiringLots,
        public ?string $licenseExpiryDate,
    ) {}

    public function build()
    {
        return $this->subject('Reminder Operasional - Jivident')
            ->view('emails.operational-reminder');
    }
}
