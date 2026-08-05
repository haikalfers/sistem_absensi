<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('overtimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained();
            $table->foreignId('employee_id')->constrained();
            $table->date('date');
            $table->enum('type', [
                'office',           // Lembur Kantor, maks 1 jam
                'admin_production', // Lembur Admin Produksi, maks 2 jam
                'production_aka',   // Lembur Produksi AKA, maks 3 jam
                'production_export' // Lembur Produksi Ekspor, bonus per kilo
            ]);
            $table->decimal('hours', 4, 2)->nullable();
            $table->decimal('kg_amount', 8, 2)->nullable();       // khusus Ekspor
            $table->decimal('export_bonus_per_kg', 10, 2)->nullable();
            $table->decimal('overtime_amount', 12, 2)->default(0);
            $table->foreignId('validated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('overtimes');
    }
};