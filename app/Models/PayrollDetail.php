<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayrollDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_id',
        'employee_id',
        'base_salary',
        'kpi_bonus',
        'meal_allowance',
        'overtime_total',
        'pph21_deduction',
        'bpjs_tk_deduction',
        'bpjs_kes_deduction',
        'other_deduction',
        'net_salary',
        'attendance_days',
        'late_count',
        'absent_count',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'kpi_bonus' => 'decimal:2',
        'meal_allowance' => 'decimal:2',
        'overtime_total' => 'decimal:2',
        'pph21_deduction' => 'decimal:2',
        'bpjs_tk_deduction' => 'decimal:2',
        'bpjs_kes_deduction' => 'decimal:2',
        'other_deduction' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'attendance_days' => 'integer',
        'late_count' => 'integer',
        'absent_count' => 'integer',
    ];

    // Relasi
    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}