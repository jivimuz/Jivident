<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Butuh cron `* * * * * php artisan schedule:run` di server — tidak perlu
// queue worker (reminders:send mengirim mail secara sinkron).
Schedule::command('reminders:send')->dailyAt('08:00');
