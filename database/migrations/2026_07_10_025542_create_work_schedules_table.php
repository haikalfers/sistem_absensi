<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // "Jadwal Normal"
            $table->json('working_days');                    // [1,2,3,4,5,6] = Senin-Sabtu
            $table->time('check_in_time');                   // 08:30 atau 08:00
            $table->time('check_out_time');                  // 16:30 atau 13:00
            $table->integer('late_tolerance_minutes')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_schedules');
    }
};