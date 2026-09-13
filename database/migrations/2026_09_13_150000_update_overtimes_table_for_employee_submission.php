<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('overtimes', function (Blueprint $table) {
            // Mengubah foreign key attendance_id menjadi nullable
            $table->foreignId('attendance_id')->nullable()->change();
            
            // Menambahkan kolom pendukung pengajuan lembur mandiri
            $table->time('start_time')->nullable()->after('date');
            $table->time('end_time')->nullable()->after('start_time');
            $table->text('reason')->nullable()->after('type');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('reason');
            $table->text('rejection_reason')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('overtimes', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time', 'reason', 'status', 'rejection_reason']);
            $table->foreignId('attendance_id')->nullable(false)->change();
        });
    }
};
