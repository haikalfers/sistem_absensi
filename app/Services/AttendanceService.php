<?php

namespace App\Services;

use App\Models\{Attendance, Employee, CompanyLocation, WorkSchedule, Overtime};
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class AttendanceService
{
    public function __construct(private GeolocationService $geoService) {}

    /**
     * Process check-in dari karyawan
     * Validasi GPS radius untuk PWA, validasi jam kerja, simpan selfie & deteksi fake GPS
     *
     * @param int    $employeeId
     * @param float  $lat
     * @param float  $lng
     * @param string $source       'pwa' | 'fingerprint'
     * @param float  $gpsAccuracy  Akurasi GPS dalam meter (dari browser)
     * @param string|null $selfieBase64  Foto selfie dalam format base64 (data URI)
     * @param string|null $ipAddress    IP address karyawan
     */
    public function processCheckIn(
        int     $employeeId,
        float   $lat,
        float   $lng,
        string  $source = 'pwa',
        float   $gpsAccuracy = 999,
        ?string $selfieBase64 = null,
        ?string $ipAddress = null
    ): array {
        try {
            $employee = Employee::findOrFail($employeeId);
            $today    = Carbon::today();
            $now      = Carbon::now();

            // Cek sudah absen hari ini?
            $existing = Attendance::where('employee_id', $employeeId)
                ->whereDate('date', $today)
                ->first();

            if ($existing && $existing->check_in) {
                return [
                    'success' => false,
                    'message' => 'Anda sudah absen masuk hari ini.',
                    'code'    => 'ALREADY_CHECKED_IN',
                ];
            }

            // Validasi GPS Radius (hanya untuk PWA)
            $office = null;
            if ($source === 'pwa') {
                $office = CompanyLocation::where('name', 'like', '%' . $employee->department . '%')->first()
                    ?? CompanyLocation::first();

                if (!$office) {
                    return [
                        'success' => false,
                        'message' => 'Konfigurasi lokasi kantor tidak ditemukan.',
                        'code'    => 'OFFICE_CONFIG_NOT_FOUND',
                    ];
                }

                $gpsValidation = $this->geoService->isWithinRadius(
                    $lat, $lng,
                    $office->latitude, $office->longitude,
                    $office->radius_meters
                );

                if (!$gpsValidation['is_within']) {
                    return [
                        'success'  => false,
                        'message'  => $gpsValidation['message'],
                        'code'     => 'OUTSIDE_RADIUS',
                        'distance' => $gpsValidation['distance'],
                    ];
                }
            }

            // Tentukan jadwal kerja hari ini
            $schedule = $this->getScheduleForDay($now->dayOfWeek);
            if (!$schedule) {
                return [
                    'success' => false,
                    'message' => 'Hari ini bukan hari kerja.',
                    'code'    => 'NOT_WORKING_DAY',
                ];
            }

            // Tentukan status: Tepat Waktu atau Terlambat
            $checkInDue = Carbon::parse($today->toDateString() . ' ' . $schedule->check_in_time);
            $status     = $now->lte($checkInDue) ? 'on_time' : 'late';

            // ===== SIMPAN SELFIE =====
            $selfiePathRel   = null;
            $selfieExpiresAt = null;

            if ($selfieBase64) {
                $selfiePathRel   = $this->storeSelfiePhoto($selfieBase64, $employeeId, $today);
                $selfieExpiresAt = $now->copy()->addDays(7); // Dihapus otomatis setelah 7 hari
            }

            // ===== DETEKSI FAKE GPS =====
            $fakeGpsResult = ['is_suspect' => false, 'flags' => []];
            if ($source === 'pwa' && $ipAddress) {
                $fakeGpsResult = $this->geoService->detectFakeGps($lat, $lng, $gpsAccuracy, $ipAddress);
            }

            // Buat atau update record attendance
            $attendance = Attendance::updateOrCreate(
                ['employee_id' => $employeeId, 'date' => $today],
                [
                    'check_in'          => $now->toTimeString(),
                    'check_in_lat'      => $lat,
                    'check_in_lng'      => $lng,
                    'source'            => $source,
                    'status'            => $status,
                    'selfie_photo'      => $selfiePathRel,
                    'selfie_expires_at' => $selfieExpiresAt,
                    'gps_accuracy'      => $gpsAccuracy,
                    'ip_address'        => $ipAddress,
                    'fake_gps_flags'    => $fakeGpsResult['flags'] ?: null,
                    'is_suspect'        => $fakeGpsResult['is_suspect'],
                ]
            );

            $statusLabel = $status === 'on_time' ? 'Tepat Waktu' : 'Terlambat';
            $suspectNote = $fakeGpsResult['is_suspect']
                ? ' ⚠️ GPS terdeteksi mencurigakan, HR akan memverifikasi.'
                : '';

            Log::info('Check-in Success', [
                'employee_id'   => $employeeId,
                'employee_name' => $employee->name,
                'check_in_time' => $now->toTimeString(),
                'status'        => $status,
                'location'      => "$lat, $lng",
                'source'        => $source,
                'is_suspect'    => $fakeGpsResult['is_suspect'],
                'has_selfie'    => !is_null($selfiePathRel),
            ]);

            return [
                'success'    => true,
                'message'    => "Absen masuk berhasil. Status: {$statusLabel}.{$suspectNote}",
                'code'       => 'CHECK_IN_SUCCESS',
                'attendance' => $attendance,
                'status'     => $status,
                'is_suspect' => $fakeGpsResult['is_suspect'],
            ];
        } catch (\Exception $e) {
            Log::error('Check-in Error', [
                'employee_id' => $employeeId,
                'error'       => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat proses check-in.',
                'code'    => 'CHECK_IN_ERROR',
                'error'   => $e->getMessage(),
            ];
        }
    }

    /**
     * Process check-out dari karyawan
     */
    public function processCheckOut(
        int    $employeeId,
        float  $lat,
        float  $lng,
        string $source = 'pwa'
    ): array {
        try {
            $employee = Employee::findOrFail($employeeId);
            $today    = Carbon::today();
            $now      = Carbon::now();

            $attendance = Attendance::where('employee_id', $employeeId)
                ->whereDate('date', $today)
                ->first();

            if (!$attendance) {
                return [
                    'success' => false,
                    'message' => 'Anda belum melakukan check-in hari ini.',
                    'code'    => 'NO_CHECK_IN_TODAY',
                ];
            }

            if ($attendance->check_out) {
                return [
                    'success' => false,
                    'message' => 'Anda sudah absen keluar hari ini.',
                    'code'    => 'ALREADY_CHECKED_OUT',
                ];
            }

            $attendance->update(['check_out' => $now->toTimeString()]);

            // Hitung Lembur Otomatis
            $checkInTime  = Carbon::parse($attendance->date->toDateString() . ' ' . $attendance->check_in);
            $overtimeHours = $this->calculateOvertimeHours($checkInTime, $now);

            if ($overtimeHours > 0) {
                Overtime::updateOrCreate(
                    [
                        'attendance_id' => $attendance->id,
                        'employee_id'   => $employeeId,
                        'date'          => $today,
                    ],
                    [
                        'type'  => $this->determineOvertimeType($employee),
                        'hours' => $overtimeHours,
                    ]
                );
            }

            Log::info('Check-out Success', [
                'employee_id'    => $employeeId,
                'employee_name'  => $employee->name,
                'check_out_time' => $now->toTimeString(),
                'location'       => "$lat, $lng",
                'overtime_hours' => $overtimeHours,
            ]);

            return [
                'success'    => true,
                'message'    => 'Absen keluar berhasil.',
                'code'       => 'CHECK_OUT_SUCCESS',
                'attendance' => $attendance,
            ];
        } catch (\Exception $e) {
            Log::error('Check-out Error', [
                'employee_id' => $employeeId,
                'error'       => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat proses check-out.',
                'code'    => 'CHECK_OUT_ERROR',
                'error'   => $e->getMessage(),
            ];
        }
    }

    /**
     * Simpan foto selfie dari base64 ke storage
     * Return path relatif dari storage/app/public/
     */
    private function storeSelfiePhoto(string $base64Data, int $employeeId, Carbon $date): ?string
    {
        try {
            // Strip data URI prefix: "data:image/jpeg;base64,..."
            if (str_contains($base64Data, ',')) {
                [, $base64Data] = explode(',', $base64Data, 2);
            }

            $imageData = base64_decode($base64Data);
            if (!$imageData) return null;

            // Path: selfies/2026/09/10/emp_1_20260910_123456.jpg
            $dir      = 'selfies/' . $date->format('Y/m/d');
            $filename = "emp_{$employeeId}_{$date->format('Ymd')}_" . now()->format('His') . '.jpg';
            $path     = "{$dir}/{$filename}";

            Storage::disk('public')->put($path, $imageData);

            return $path;
        } catch (\Exception $e) {
            Log::warning('Selfie Storage Error', ['employee_id' => $employeeId, 'error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Ambil jadwal kerja untuk hari tertentu
     * dayOfWeek: 0=Minggu, 1=Senin, ..., 6=Sabtu
     */
    private function getScheduleForDay(int $dayOfWeek): ?WorkSchedule
    {
        return WorkSchedule::whereJsonContains('working_days', $dayOfWeek)->first();
    }

    /**
     * Hitung durasi kerja dalam jam
     */
    public function calculateWorkingHours(\DateTime $checkIn, \DateTime $checkOut): float
    {
        $diff  = $checkOut->diff($checkIn);
        $hours = $diff->h + ($diff->i / 60) + ($diff->s / 3600);
        return round($hours, 2);
    }

    /**
     * Tentukan apakah ada overtime berdasarkan durasi kerja
     */
    public function isOvertime(\DateTime $checkIn, \DateTime $checkOut, float $normalWorkingHours = 8): bool
    {
        return $this->calculateWorkingHours($checkIn, $checkOut) > $normalWorkingHours;
    }

    /**
     * Hitung overtime hours
     */
    public function calculateOvertimeHours(
        \DateTime $checkIn,
        \DateTime $checkOut,
        float $normalWorkingHours = 8
    ): float {
        $hours    = $this->calculateWorkingHours($checkIn, $checkOut);
        $overtime = $hours - $normalWorkingHours;

        if ($overtime > 0) {
            // Dibulatkan ke bawah ke kelipatan 0.5 jam (30 menit)
            return max(0, floor($overtime * 2) / 2);
        }

        return 0;
    }

    /**
     * Menentukan tipe lembur berdasarkan data karyawan
     */
    public function determineOvertimeType(Employee $employee): string
    {
        $division = strtolower($employee->division ?? '');
        $position = strtolower($employee->position ?? '');

        if (str_contains($division, 'produksi')) {
            if (str_contains($position, 'admin')) {
                return 'admin_production';
            }
            return 'production_aka';
        }

        return 'office';
    }
}