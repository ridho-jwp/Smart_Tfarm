<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/* |-------------------------------------------------------------------------- | Smart Pakcoy Hidroponik — Console Routes |-------------------------------------------------------------------------- */

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Cek status perangkat setiap 2 menit
Schedule::command('devices:check-status')->everyTwoMinutes();
