<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\User;
use App\Notifications\CheckInReminderNotification;
use App\Notifications\CheckOutReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAttendanceReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:send-reminders {type=first_checkin : Opsi type: first_checkin, second_checkin, checkout}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim notifikasi WebPush pengingat presensi masuk & keluar karyawan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');
        $today = Carbon::today();
        $this->info("Menjalankan pengingat presensi tipe: {$type}");

        // Ambil user role employee yang memiliki data employee
        $employeesQuery = User::where('role', 'employee')->has('employee');

        if ($type === 'first_checkin') {
            // 07.30 WIB: Pengingat pertama absensi masuk massal
            $users = $employeesQuery->get();
            $count = 0;
            foreach ($users as $user) {
                $user->notify(new CheckInReminderNotification(
                    'Pengingat Presensi Masuk ⏰',
                    'Selamat pagi! Jangan lupa melakukan presensi masuk sebelum jam kerja dimulai.'
                ));
                $count++;
            }
            $this->info("Pengingat presensi masuk pertama berhasil dikirim ke {$count} karyawan.");
        } elseif ($type === 'second_checkin') {
            // 07.50 WIB: Pengingat kedua khusus karyawan yang BELUM presensi masuk hari ini
            $checkedInEmployeeIds = Attendance::whereDate('date', $today)
                ->whereNotNull('check_in')
                ->pluck('employee_id')
                ->toArray();

            $usersToWarn = $employeesQuery->whereHas('employee', function ($q) use ($checkedInEmployeeIds) {
                $q->whereNotIn('id', $checkedInEmployeeIds);
            })->get();

            $count = 0;
            foreach ($usersToWarn as $user) {
                $user->notify(new CheckInReminderNotification(
                    '⚠️ Peringatan Presensi Masuk!',
                    'PENTING: Pukul 07.50 WIB dan Anda belum presensi masuk hari ini. Segera lakukan presensi!',
                    true
                ));
                $count++;
            }
            $this->info("Pengingat presensi masuk kedua (warning) dikirim ke {$count} karyawan.");
        } elseif ($type === 'checkout') {
            // 15 Min Sebelum Pulang: Kirim ke karyawan yang SUDAH check-in tapi BELUM check-out
            $checkedInEmployeeIds = Attendance::whereDate('date', $today)
                ->whereNotNull('check_in')
                ->whereNull('check_out')
                ->pluck('employee_id')
                ->toArray();

            $usersToNotify = $employeesQuery->whereHas('employee', function ($q) use ($checkedInEmployeeIds) {
                $q->whereIn('id', $checkedInEmployeeIds);
            })->get();

            $count = 0;
            foreach ($usersToNotify as $user) {
                $user->notify(new CheckOutReminderNotification(
                    'Pengingat Presensi Keluar 🕒',
                    'Waktu kerja akan segera berakhir. Pastikan melakukan presensi keluar sebelum pulang!'
                ));
                $count++;
            }
            $this->info("Pengingat presensi keluar berhasil dikirim ke {$count} karyawan.");
        } else {
            $this->error("Tipe pengingat '{$type}' tidak valid. Gunakan first_checkin, second_checkin, atau checkout.");
            return 1;
        }

        return 0;
    }
}
