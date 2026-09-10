<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();    // Identifier unik, e.g. 'bpjs_jht_company_rate'
            $table->string('label');             // Label tampil untuk UI, e.g. 'BPJS JHT Perusahaan (%)'
            $table->string('type')->default('percentage'); // 'percentage' | 'nominal' | 'boolean'
            $table->decimal('value', 10, 4)->default(0);   // Nilai (persentase atau nominal)
            $table->string('group')->default('bpjs');       // 'bpjs' | 'deduction' | 'allowance'
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_settings');
    }
};
