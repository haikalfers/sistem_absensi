<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained();
            $table->foreignId('employee_id')->constrained();
            $table->decimal('base_salary', 12, 2);
            $table->decimal('kpi_bonus', 12, 2)->default(0);
            $table->decimal('meal_allowance', 12, 2)->default(0);
            $table->decimal('overtime_total', 12, 2)->default(0);
            $table->decimal('pph21_deduction', 12, 2)->default(0);
            $table->decimal('bpjs_tk_deduction', 12, 2)->default(0);  // Ketenagakerjaan
            $table->decimal('bpjs_kes_deduction', 12, 2)->default(0); // Kesehatan
            $table->decimal('other_deduction', 12, 2)->default(0);
            $table->decimal('net_salary', 12, 2);
            $table->integer('attendance_days')->default(0);
            $table->integer('late_count')->default(0);
            $table->integer('absent_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_details');
    }
};