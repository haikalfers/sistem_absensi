<?php

namespace App\Services;

use App\Models\{Employee, Payroll, PayrollDetail, Attendance, Overtime};
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PayrollService
{
    // Konstanta BPJS & Pajak
    const BPJS_TK_RATE = 0.02;          // 2% JHT ditanggung karyawan
    const BPJS_KES_RATE = 0.01;         // 1% ditanggung karyawan
    const MEAL_ALLOWANCE_PER_DAY = 25000; // Rp 25.000 per hari
    const PTKP_TK_0 = 54_000_000;       // PTKP untuk TK/0
    const MAX_DAILY_OVERTIME = 3;       // Max 3 jam per hari (produksi ekspor)

    /**
     * Generate payroll untuk periode tertentu
     * Hitung gaji semua karyawan dalam periode
     */
    public function generatePayroll(Payroll $payroll): array
    {
        try {
            $employees = Employee::all();
            $details = [];

            foreach ($employees as $employee) {
                $detail = $this->generateDetailForEmployee($payroll, $employee);
                $details[] = $detail;
            }

            // Update status payroll menjadi finalized
            $payroll->update(['status' => 'finalized']);

            Log::info('Payroll Generated', [
                'payroll_id' => $payroll->id,
                'period' => $payroll->period_name,
                'total_employees' => count($details),
            ]);

            return [
                'success' => true,
                'message' => "Payroll {$payroll->period_name} berhasil digenerate untuk {$payroll->details->count()} karyawan.",
                'payroll' => $payroll,
                'details' => $details,
            ];
        } catch (\Exception $e) {
            Log::error('Payroll Generation Error', [
                'payroll_id' => $payroll->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat generate payroll.',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate detail payroll untuk satu karyawan
     */
    private function generateDetailForEmployee(Payroll $payroll, Employee $employee): PayrollDetail
    {
        // Hitung kehadiran dalam periode
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$payroll->period_start, $payroll->period_end])
            ->get();

        // Statistik kehadiran
        $onTime = $attendances->where('status', 'on_time')->count();
        $late = $attendances->where('status', 'late')->count();
        $absent = $attendances->where('status', 'absent')->count();
        $present = $onTime + $late;

        // Hitung hari kerja dalam periode
        $workingDays = $this->countWorkingDays($payroll->period_start, $payroll->period_end);

        // Tunjangan makan: per hari hadir
        $mealAllowance = $present * self::MEAL_ALLOWANCE_PER_DAY;

        // Hitung total overtime dari semua attendance
        $overtimes = Overtime::where('employee_id', $employee->id)
            ->whereBetween('date', [$payroll->period_start, $payroll->period_end])
            ->where('validated_by', '!=', null) // Hanya overtime yang sudah di-validate admin
            ->get();

        $overtimeTotal = $overtimes->sum('overtime_amount');

        // Gross salary (sebelum potongan)
        $kpiBonusDefault = 0; // Admin akan input manual di form
        $grossSalary = $employee->base_salary + $mealAllowance + $overtimeTotal + $kpiBonusDefault;

        // Potongan BPJS
        $bpjsTk = $employee->base_salary * self::BPJS_TK_RATE;
        $bpjsKes = $employee->base_salary * self::BPJS_KES_RATE;

        // PPh 21
        $pph21 = $this->calculatePph21($grossSalary);

        // Net salary (setelah potongan)
        $netSalary = $grossSalary - $bpjsTk - $bpjsKes - $pph21;

        // Create atau update payroll detail
        $detail = PayrollDetail::updateOrCreate(
            [
                'payroll_id' => $payroll->id,
                'employee_id' => $employee->id,
            ],
            [
                'base_salary' => $employee->base_salary,
                'kpi_bonus' => $kpiBonusDefault,
                'meal_allowance' => $mealAllowance,
                'overtime_total' => $overtimeTotal,
                'pph21_deduction' => $pph21,
                'bpjs_tk_deduction' => $bpjsTk,
                'bpjs_kes_deduction' => $bpjsKes,
                'other_deduction' => 0,
                'net_salary' => max(0, $netSalary), // Jangan negatif
                'attendance_days' => $present,
                'late_count' => $late,
                'absent_count' => $absent,
            ]
        );

        Log::info('Payroll Detail Generated', [
            'payroll_id' => $payroll->id,
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'net_salary' => $netSalary,
            'attendance_days' => $present,
        ]);

        return $detail;
    }

    /**
     * Hitung PPh 21 menggunakan tarif progresif
     * Berdasarkan UU No. 8 Tahun 1997 tentang Dokumen Perusahaan
     */
    private function calculatePph21(float $grossSalary): float
    {
        // Annualized gross untuk tarif progresif
        $annual = $grossSalary * 12;
        $ptkp = self::PTKP_TK_0;

        $pkp = max(0, $annual - $ptkp);

        // Tarif progresif
        $tax = match(true) {
            $pkp <= 60_000_000 => $pkp * 0.05,
            $pkp <= 250_000_000 => 3_000_000 + ($pkp - 60_000_000) * 0.15,
            $pkp <= 500_000_000 => 33_000_000 + ($pkp - 250_000_000) * 0.25,
            default => 95_500_000 + ($pkp - 500_000_000) * 0.30,
        };

        // Per bulan
        $monthlyTax = $tax / 12;

        return round($monthlyTax, 2);
    }

    /**
     * Hitung jumlah hari kerja dalam periode (Senin-Sabtu)
     */
    private function countWorkingDays(\DateTime|string $startDate, \DateTime|string $endDate): int
    {
        $start = $startDate instanceof \DateTime ? $startDate : Carbon::parse($startDate);
        $end = $endDate instanceof \DateTime ? $endDate : Carbon::parse($endDate);

        $count = 0;
        $current = $start->copy();

        while ($current <= $end) {
            $dayOfWeek = $current->dayOfWeek;
            // 1-6 = Senin-Sabtu (0 = Minggu)
            if (in_array($dayOfWeek, [1, 2, 3, 4, 5, 6])) {
                $count++;
            }
            $current->addDay();
        }

        return $count;
    }

    /**
     * Validasi & approve overtime dari admin
     */
    public function validateOvertime(
        Overtime $overtime,
        int $adminId
    ): array {
        try {
            // Check tipe overtime dan max hours
            $maxHours = match($overtime->type) {
                'office' => 1,
                'admin_production' => 2,
                'production_aka' => 3,
                'production_export' => 3,
                default => 1,
            };

            if ($overtime->hours > $maxHours) {
                return [
                    'success' => false,
                    'message' => "Overtime {$overtime->type} maksimal $maxHours jam.",
                    'code' => 'OVERTIME_EXCEEDS_MAX',
                ];
            }

            // Update overtime amount based on type
            $amount = $this->calculateOvertimeAmount($overtime);
            $overtime->update([
                'overtime_amount' => $amount,
                'validated_by' => $adminId,
            ]);

            Log::info('Overtime Validated', [
                'overtime_id' => $overtime->id,
                'employee_id' => $overtime->employee_id,
                'type' => $overtime->type,
                'hours' => $overtime->hours,
                'amount' => $amount,
                'validated_by' => $adminId,
            ]);

            return [
                'success' => true,
                'message' => "Overtime berhasil di-validasi. Amount: Rp " . number_format($amount, 0, ',', '.'),
                'overtime' => $overtime,
            ];
        } catch (\Exception $e) {
            Log::error('Overtime Validation Error', [
                'overtime_id' => $overtime->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat validasi overtime.',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Hitung overtime amount (nilai uang) berdasarkan tipe
     * Asumsi: gaji / 240 jam = rate per jam (30 hari kerja x 8 jam)
     */
    private function calculateOvertimeAmount(Overtime $overtime): float
    {
        $employee = $overtime->employee;
        $hourlyRate = $employee->base_salary / 240; // 240 = 30 hari x 8 jam

        if ($overtime->type === 'production_export') {
            // Bonus per kilo untuk produksi ekspor
            $bonusAmount = ($overtime->kg_amount ?? 0) * ($overtime->export_bonus_per_kg ?? 0);
            return $bonusAmount;
        }

        // Overtime multiplier (asumsi 1.5x untuk semua tipe)
        $multiplier = 1.5;
        $amount = $hourlyRate * ($overtime->hours ?? 0) * $multiplier;

        return round($amount, 2);
    }
}