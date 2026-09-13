<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('field_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('date')->comment('Tanggal penugasan dinas luar');
            $table->string('location_name')->comment('Nama lokasi penugasan (misal: Gudang Sidoarjo)');
            $table->decimal('location_lat', 10, 7)->nullable()->comment('Koordinat acuan lokasi (opsional)');
            $table->decimal('location_lng', 10, 7)->nullable();
            $table->text('notes')->nullable()->comment('Catatan tambahan dari HR');
            $table->foreignId('assigned_by')->constrained('users')->comment('User HR yang membuat penugasan');
            $table->timestamps();

            // Index untuk pencarian cepat per tanggal
            $table->index(['date', 'employee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_assignments');
    }
};
