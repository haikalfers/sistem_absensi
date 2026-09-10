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
        // Pendapatan porsi perusahaan (transparan di slip)
        'bpjs_jht_company',
        'bpjs_jkk_income',
        'bpjs_jkm_income',
        'bpjs_jkn_company',
        'jp_company_income',
        // Potongan
        'bpjs_jht_employee',
        'bpjs_jkk_deduct',
        'bpjs_jkm_deduct',
        'bpjs_jkn_company_deduct',
        'jp_company_deduct',
        'jp_employee',
        'pot_bpjs',
        'pot_pesantren',
        'absent_deduction',
        'other_deduction',
        // Summary
        'net_salary',
        'attendance_days',
        'late_count',
        'absent_count',
        'overtime_total',
    ];

    protected $casts = [
        'base_salary'            => 'decimal:2',
        'bpjs_jht_company'       => 'decimal:2',
        'bpjs_jkk_income'        => 'decimal:2',
        'bpjs_jkm_income'        => 'decimal:2',
        'bpjs_jkn_company'       => 'decimal:2',
        'jp_company_income'      => 'decimal:2',
        'bpjs_jht_employee'      => 'decimal:2',
        'bpjs_jkk_deduct'        => 'decimal:2',
        'bpjs_jkm_deduct'        => 'decimal:2',
        'bpjs_jkn_company_deduct'=> 'decimal:2',
        'jp_company_deduct'      => 'decimal:2',
        'jp_employee'            => 'decimal:2',
        'pot_bpjs'               => 'decimal:2',
        'pot_pesantren'          => 'decimal:2',
        'absent_deduction'       => 'decimal:2',
        'other_deduction'        => 'decimal:2',
        'overtime_total'         => 'decimal:2',
        'net_salary'             => 'decimal:2',
        'attendance_days'        => 'integer',
        'late_count'             => 'integer',
        'absent_count'           => 'integer',
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

    /**
     * Total pendapatan bruto (gaji pokok + semua komponen pendapatan perusahaan + overtime)
     */
    public function getTotalIncomeAttribute(): float
    {
        return (float) $this->base_salary
            + (float) $this->bpjs_jht_company
            + (float) $this->bpjs_jkk_income
            + (float) $this->bpjs_jkm_income
            + (float) $this->bpjs_jkn_company
            + (float) $this->jp_company_income
            + (float) $this->overtime_total;
    }

    /**
     * Total potongan
     */
    public function getTotalDeductionAttribute(): float
    {
        return (float) $this->bpjs_jht_employee
            + (float) $this->bpjs_jkk_deduct
            + (float) $this->bpjs_jkm_deduct
            + (float) $this->bpjs_jkn_company_deduct
            + (float) $this->jp_company_deduct
            + (float) $this->jp_employee
            + (float) $this->pot_bpjs
            + (float) $this->pot_pesantren
            + (float) $this->absent_deduction
            + (float) $this->other_deduction;
    }
}