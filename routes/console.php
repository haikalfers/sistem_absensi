<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Hapus foto selfie kadaluarsa (> 7 hari) setiap tengah malam
Schedule::command('selfies:delete-expired')->dailyAt('00:01');

// Pengingat Presensi WebPush (Browser Push Notification)
// 1. Pengingat Absen Masuk Pertama (07.30 WIB)
Schedule::command('attendance:send-reminders first_checkin')->dailyAt('07:30');

// 2. Pengingat Absen Masuk Kedua untuk Karyawan yang Belum Absen (07.50 WIB)
Schedule::command('attendance:send-reminders second_checkin')->dailyAt('07:50');

// 3. Pengingat Absen Keluar (15.45 WIB - 15 Menit Sebelum Jam Pulang 16.00)
Schedule::command('attendance:send-reminders checkout')->dailyAt('15:45');

