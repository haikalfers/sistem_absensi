<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Hapus foto selfie kadaluarsa (> 7 hari) setiap tengah malam
Schedule::command('selfies:delete-expired')->dailyAt('00:01');
