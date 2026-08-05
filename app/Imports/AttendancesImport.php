<?php

namespace App\Imports;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Overtime;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AttendancesImport implements ToCollection, WithHeadingRow, SkipsOnFailure
{
    use SkipsFailures;

    public array $importResults = [];
    private int $successCount = 0;
    private int $skipCount = 0;
    private array $errors = [];

    /**
     * Format CSV dari mesin fingerprint biasanya:
     * No | ID Karyawan | Nama | Tanggal | Jam Masuk | Jam Keluar | Status
     *
     * Heading row mapping (sesuaikan dengan format CSV mesin fingerprint klien):
     * id_karyawan / employee_code
     * tanggal / date
     * jam_masuk / check_in
     * jam_keluar / check_out
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            try {
                // Normalisasi key — fingerprint machines often export inconsistent headers
                $employeeCode = $this->getValue($row, ['id_karyawan', 'employee_code', 'nik', 'id', 'no_induk']);
                $dateRaw      = $this->getValue($row, ['tanggal', 'date', 'tgl']);
                $checkInRaw   = $this->getValue($row, ['jam_masuk', 'check_in', 'masuk', 'time_in']);
                $checkOutRaw  = $this->getValue($row, ['jam_keluar', 'check_out', 'keluar', 'time_out']);

                if (!$employeeCode || !$dateRaw) {
                    $this->skipCount++;
                    $this->errors[] = "Row " . ($index + 2) . ": Kode karyawan atau tanggal kosong";
                    continue;
                }

                // Find employee by employee_code
                $employee = Employee::where('employee_code', $employeeCode)->first();
                if (!$employee) {
                    $this->skipCount++;
                    $this->errors[] = "Row " . ($index + 2) . ": Karyawan dengan kode '{$employeeCode}' tidak ditemukan";
                    continue;
                }

                // Parse date
                $date = $this->parseDate($dateRaw);
                if (!$date) {
                    $this->skipCount++;
                    $this->errors[] = "Row " . ($index + 2) . ": Format tanggal tidak valid ({$dateRaw})";
                    continue;
                }

                // Check if record already exists
                $existing = Attendance::where('employee_id', $employee->id)
                    ->whereDate('date', $date)
                    ->where('source', 'fingerprint')
                    ->first();

                if ($existing) {
                    // Update check_out jika sudah ada
                    if ($checkOutRaw) {
                        $parsedCheckOut = $this->parseTime($checkOutRaw, $date);
                        $existing->update([
                            'check_out' => $parsedCheckOut,
                        ]);

                        // Cek lembur untuk update record yang sudah ada
                        if ($existing->check_in && $parsedCheckOut) {
                            $attendanceService = app(\App\Services\AttendanceService::class);
                            $checkInTime = Carbon::parse($date . ' ' . $existing->check_in);
                            $checkOutTime = Carbon::parse($parsedCheckOut);
                            $overtimeHours = $attendanceService->calculateOvertimeHours($checkInTime, $checkOutTime);
                            
                            if ($overtimeHours > 0) {
                                Overtime::updateOrCreate(
                                    [
                                        'attendance_id' => $existing->id,
                                        'employee_id' => $employee->id,
                                        'date' => $date,
                                    ],
                                    [
                                        'type' => $attendanceService->determineOvertimeType($employee),
                                        'hours' => $overtimeHours,
                                    ]
                                );
                            }
                        }
                    }
                    $this->skipCount++;
                    continue;
                }

                // Parse times
                $checkIn  = $checkInRaw  ? $this->parseTime($checkInRaw, $date)  : null;
                $checkOut = $checkOutRaw ? $this->parseTime($checkOutRaw, $date) : null;

                // Determine status
                $status = $this->determineStatus($checkIn, $employee);

                $attendance = Attendance::create([
                    'employee_id' => $employee->id,
                    'date'        => $date,
                    'check_in'    => $checkIn,
                    'check_out'   => $checkOut,
                    'status'      => $status,
                    'source'      => 'fingerprint',
                    'latitude'    => null,
                    'longitude'   => null,
                    'notes'       => 'Import dari CSV fingerprint',
                ]);

                // Hitung Lembur Otomatis
                if ($checkIn && $checkOut) {
                    $attendanceService = app(\App\Services\AttendanceService::class);
                    $checkInTime = Carbon::parse($checkIn); // parseTime returns datetime string
                    $checkOutTime = Carbon::parse($checkOut);
                    $overtimeHours = $attendanceService->calculateOvertimeHours($checkInTime, $checkOutTime);
                    
                    if ($overtimeHours > 0) {
                        Overtime::updateOrCreate(
                            [
                                'attendance_id' => $attendance->id,
                                'employee_id' => $employee->id,
                                'date' => $date,
                            ],
                            [
                                'type' => $attendanceService->determineOvertimeType($employee),
                                'hours' => $overtimeHours,
                            ]
                        );
                    }
                }

                $this->successCount++;

            } catch (\Exception $e) {
                $this->skipCount++;
                $this->errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                Log::error('AttendancesImport error', ['row' => $index + 2, 'error' => $e->getMessage()]);
            }
        }

        $this->importResults = [
            'success' => $this->successCount,
            'skipped' => $this->skipCount,
            'errors'  => $this->errors,
        ];
    }

    private function getValue(Collection $row, array $keys): ?string
    {
        foreach ($keys as $key) {
            $val = $row->get($key) ?? $row->get(strtoupper($key));
            if ($val !== null && $val !== '') {
                return (string) $val;
            }
        }
        return null;
    }

    private function parseDate(string $raw): ?string
    {
        $formats = ['d/m/Y', 'Y-m-d', 'd-m-Y', 'd/m/y', 'Y/m/d'];
        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, trim($raw))->format('Y-m-d');
            } catch (\Exception $e) {
                // try next
            }
        }
        // Try Carbon's flexible parser
        try {
            return Carbon::parse($raw)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseTime(string $raw, string $date): ?string
    {
        try {
            // Format: "08:30" or "08:30:00"
            $time = Carbon::createFromFormat('H:i:s', trim($raw));
        } catch (\Exception $e) {
            try {
                $time = Carbon::createFromFormat('H:i', trim($raw));
            } catch (\Exception $e2) {
                return null;
            }
        }
        return $date . ' ' . $time->format('H:i:s');
    }

    private function determineStatus(?string $checkIn, Employee $employee): string
    {
        if (!$checkIn) return 'absent';

        try {
            $checkInTime = Carbon::parse($checkIn);
            // Standard check-in time: 08:30 (sesuaikan dengan work schedule)
            $standardCheckIn = Carbon::parse($checkIn)->setTime(8, 30, 0);
            return $checkInTime->lte($standardCheckIn) ? 'on_time' : 'late';
        } catch (\Exception $e) {
            return 'on_time';
        }
    }

    public function getResults(): array
    {
        return $this->importResults;
    }
}