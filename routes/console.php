<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;
Schedule::command('bookings:send-reminders')->dailyAt('15:00')->timezone('Europe/Madrid');
Schedule::command('bookings:send-daily-agenda')->dailyAt('21:00')->timezone('Europe/Madrid');
Schedule::call(function () {
    \App\Models\PendingBooking::where('expires_at', '<', now())->delete();
})->hourly();

