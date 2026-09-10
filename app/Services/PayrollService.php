<?php

namespace App\Services;

use App\Models\{Employee, Payroll, PayrollDetail, PayrollSetting, Attendance, Overtime, LeaveRequest};
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PayrollService
{
    const MAX_DAILY_OVERTIME = 3; // Max 3 jam per hari

    /**
     * Generate payroll untuk semua karyawan dalam periode
     */
    public function generatePayroll(Payroll $payroll): array
    {
        try {
            // Muat semua setting BPJS sekali (efisiensi query)
            $settings = $this->loadSettings();

            $employees = Employee::all();
            $details   = [];

            foreach ($employees as $employee) {
                $detail    = $this->generateDetailForEmployee($payroll, $employee, $settings);
                $details[] = $detail;
            }

            $payroll->update(['status' => 'finalized']);

            Log::info('Payroll Generated', [
                'payroll_id'      => $payroll->id,
                'period'          => $payroll->period_name,
                'total_employees' => count($details),
            ]);

            return [
                'success' => true,
                'message' => "Payroll {$payroll->period_name} berhasil digenerate untuk " . count($details) . " karyawan.",
                'payroll' => $payroll,
                'details' => $details,
            ];
        } catch (\Exception $e) {
            Log::error('Payroll Generation Error', [
                'payroll_id' => $payroll->id,
                'error'      => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat generate payroll: ' . $e->getMessage(),
                'error'   => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate detail payroll untuk satu karyawan
     *
     * Struktur Slip Gaji PT. Indobismar:
     *
     * PENDAPATAN:
     *   Gaji Pokok
     *   + BPJS JHT Perusahaan
     *   + BPJS JKK
     *   + BPJS JKM
     *   + BPJS JKN Perusahaan
     *   + JP Perusahaan
     *   + Lembur
     *   = Total Pendapatan
     *
     * POTONGAN:
     *   - BPJS JHT Tenaga Kerja
     *   - BPJS JKK (balik)
     *   - BPJS JKM (balik)
     *   - BPJS JKN Perusahaan (balik)
     *   - JP Perusahaan (balik)
     *   - JP Tenaga Kerja
     *   - Pot. BPJS
     *   - Pot. Pesantren
     *   - Potongan Alpa
     *   = Total Potongan
     *
     * GAJI BERSIH = Total Pendapatan - Total Potongan
     */
    private function generateDetailForEmployee(Payroll $payroll, Employee $employee, array $settings): PayrollDetail
    {
        $baseSalary = (float) $employee->base_salary;

        // ===== STATISTIK KEHADIRAN =====
        $attendances  = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$payroll->period_start, $payroll->period_end])
            ->get();

        $onTime  = $attendances->where('status', 'on_time')->count();
        $late    = $attendances->where('status', 'late')->count();
        $absent  = $attendances->where('status', 'absent')->count();
        $present = $onTime + $late;

        // Hitung hari kerja dalam periode (Senin–Sabtu)
        $workingDays = $this->countWorkingDays($payroll->period_start, $payroll->period_end);

        // ===== LEMBUR =====
        $overtimeTotal = (float) Overtime::where('employee_id', $employee->id)
            ->whereBetween('date', [$payroll->period_start, $payroll->period_end])
            ->whereNotNull('validated_by')
            ->sum('overtime_amount');

        // ===== KOMPONEN BPJS PENDAPATAN (Porsi Perusahaan) =====
        // Muncul di kolom pendapatan untuk transparansi, langsung dipotongan balik
        $bpjsJhtCompany  = $baseSalary * ($settings['bpjs_jht_company_rate'] ?? 0.0327);
        $bpjsJkkIncome   = $baseSalary * ($settings['bpjs_jkk_rate'] ?? 0.0089);
        $bpjsJkmIncome   = $baseSalary * ($settings['bpjs_jkm_rate'] ?? 0.003);
        $bpjsJknCompany  = $baseSalary * ($settings['bpjs_jkn_company_rate'] ?? 0.04);
        $jpCompanyIncome = $baseSalary * ($settings['jp_company_rate'] ?? 0.02);

        // ===== KOMPONEN POTONGAN =====
        // Potongan Balik (nilai sama dengan pendapatan porsi perusahaan)
        $bpjsJhtEmployee      = $baseSalary * ($settings['bpjs_jht_employee_rate'] ?? 0.02);
        $bpjsJkkDeduct        = $bpjsJkkIncome;    // Balik penuh
        $bpjsJkmDeduct        = $bpjsJkmIncome;    // Balik penuh
        $bpjsJknCompanyDeduct = $bpjsJknCompany;   // Balik penuh
        $jpCompanyDeduct      = $jpCompanyIncome;  // Balik penuh
        $jpEmployee           = $baseSalary * ($settings['jp_employee_rate'] ?? 0.01);
        $potBpjs              = (float) ($settings['pot_bpjs_nominal'] ?? 0);
        $potPesantren         = (float) ($settings['pot_pesantren_nominal'] ?? 0);

        // Potongan Alpa: Gaji Pokok ÷ Hari Kerja × Jumlah Hari Alpa
        $absentDeduction = $workingDays > 0
            ? round(($baseSalary / $workingDays) * $absent, 2)
            : 0;

        // ===== KALKULASI GAJI BERSIH =====
        $totalIncome = $baseSalary
            + $bpjsJhtCompany
            + $bpjsJkkIncome
            + $bpjsJkmIncome
            + $bpjsJknCompany
            + $jpCompanyIncome
            + $overtimeTotal;

        $totalDeduction = $bpjsJhtEmployee
            + $bpjsJkkDeduct
            + $bpjsJkmDeduct
            + $bpjsJknCompanyDeduct
            + $jpCompanyDeduct
            + $jpEmployee
            + $potBpjs
            + $potPesantren
            + $absentDeduction;

        $netSalary = max(0, $totalIncome - $totalDeduction);

        $detail = PayrollDetail::updateOrCreate(
            [
                'payroll_id'  => $payroll->id,
                'employee_id' => $employee->id,
            ],
            [
                'base_salary'             => $baseSalary,
                // Pendapatan porsi perusahaan
                'bpjs_jht_company'        => round($bpjsJhtCompany, 2),
                'bpjs_jkk_income'         => round($bpjsJkkIncome, 2),
                'bpjs_jkm_income'         => round($bpjsJkmIncome, 2),
                'bpjs_jkn_company'        => round($bpjsJknCompany, 2),
                'jp_company_income'       => round($jpCompanyIncome, 2),
                // Potongan
                'bpjs_jht_employee'       => round($bpjsJhtEmployee, 2),
                'bpjs_jkk_deduct'         => round($bpjsJkkDeduct, 2),
                'bpjs_jkm_deduct'         => round($bpjsJkmDeduct, 2),
                'bpjs_jkn_company_deduct' => round($bpjsJknCompanyDeduct, 2),
                'jp_company_deduct'       => round($jpCompanyDeduct, 2),
                'jp_employee'             => round($jpEmployee, 2),
                'pot_bpjs'                => round($potBpjs, 2),
                'pot_pesantren'           => round($potPesantren, 2),
                'absent_deduction'        => round($absentDeduction, 2),
                'other_deduction'         => 0,
                // Summary
                'overtime_total'          => round($overtimeTotal, 2),
                'net_salary'              => round($netSalary, 2),
                'attendance_days'         => $present,
                'late_count'              => $late,
                'absent_count'            => $absent,
            ]
        );

        Log::info('Payroll Detail Generated', [
            'payroll_id'      => $payroll->id,
            'employee_id'     => $employee->id,
            'employee_name'   => $employee->name,
            'net_salary'      => $netSalary,
            'attendance_days' => $present,
            'absent_count'    => $absent,
            'absent_deduction'=> $absentDeduction,
        ]);

        return $detail;
    }

    /**
     * Muat semua setting BPJS dari database ke array
     */
    private function loadSettings(): array
    {
        return \App\Models\PayrollSetting::where('is_active', true)
            ->pluck('value', 'key')
            ->map(fn($v) => (float) $v)
            ->toArray();
    }

    /**
     * Hitung jumlah hari kerja dalam periode (Senin-Sabtu)
     */
    private function countWorkingDays(\DateTime|string $startDate, \DateTime|string $endDate): int
    {
        $start   = $startDate instanceof \DateTime ? $startDate : Carbon::parse($startDate);
        $end     = $endDate instanceof \DateTime ? $endDate : Carbon::parse($endDate);
        $count   = 0;
        $current = $start->copy();

        while ($current <= $end) {
            if (in_array($current->dayOfWeek, [1, 2, 3, 4, 5, 6])) {
                $count++;
            }
            $current->addDay();
        }

        return $count;
    }

    /**
     * Validasi & approve overtime dari admin
     */
    public function validateOvertime(Overtime $overtime, int $adminId): array
    {
        try {
            $maxHours = match ($overtime->type) {
                'office'           => 1,
                'admin_production' => 2,
                'production_aka'   => 3,
                'production_export'=> 3,
                default            => 1,
            };

            if ($overtime->hours > $maxHours) {
                return [
                    'success' => false,
                    'message' => "Overtime {$overtime->type} maksimal $maxHours jam.",
                    'code'    => 'OVERTIME_EXCEEDS_MAX',
                ];
            }

            $amount = $this->calculateOvertimeAmount($overtime);
            $overtime->update([
                'overtime_amount' => $amount,
                'validated_by'    => $adminId,
            ]);

            Log::info('Overtime Validated', [
                'overtime_id' => $overtime->id,
                'employee_id' => $overtime->employee_id,
                'type'        => $overtime->type,
                'hours'       => $overtime->hours,
                'amount'      => $amount,
                'validated_by'=> $adminId,
            ]);

            return [
                'success'  => true,
                'message'  => 'Overtime berhasil di-validasi. Amount: Rp ' . number_format($amount, 0, ',', '.'),
                'overtime' => $overtime,
            ];
        } catch (\Exception $e) {
            Log::error('Overtime Validation Error', ['overtime_id' => $overtime->id, 'error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat validasi overtime.',
                'error'   => $e->getMessage(),
            ];
        }
    }

    /**
     * Hitung overtime amount (nilai uang) berdasarkan tipe
     * Asumsi: gaji / 240 jam = rate per jam (30 hari kerja x 8 jam)
     */
    private function calculateOvertimeAmount(Overtime $overtime): float
    {
        $employee   = $overtime->employee;
        $hourlyRate = (float) $employee->base_salary / 240; // 240 = 30 hari x 8 jam

        if ($overtime->type === 'production_export') {
            $bonusAmount = ($overtime->kg_amount ?? 0) * ($overtime->export_bonus_per_kg ?? 0);
            return $bonusAmount;
        }

        $multiplier = 1.5; // Overtime 1.5x
        $amount     = $hourlyRate * ($overtime->hours ?? 0) * $multiplier;

        return round($amount, 2);
    }
}