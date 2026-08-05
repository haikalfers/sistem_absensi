<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('employee_code')->unique();       // ID Karyawan
            $table->string('name');
            $table->string('position');                      // Jabatan
            $table->string('division')->nullable();
            $table->enum('department', ['Rungkut', 'Driyorejo']);
            $table->decimal('base_salary', 12, 2);
            $table->integer('annual_leave_balance')->default(12);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};