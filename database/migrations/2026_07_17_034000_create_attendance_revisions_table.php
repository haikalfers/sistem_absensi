<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            // Nullable: bisa ajukan revisi meski belum ada record absensi di hari itu
            $table->foreignId('attendance_id')->nullable()->constrained('attendances')->nullOnDelete();
            $table->date('revision_date'); // Tanggal absensi yang diajukan revisi
            $table->time('requested_check_in')->nullable();
            $table->time('requested_check_out')->nullable();
            $table->text('reason'); // Alasan pengajuan (app crash, GPS error, dll)
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('review_notes')->nullable(); // Feedback dari admin jika ditolak
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            // Max 1 pengajuan pending per karyawan per tanggal
            // Ditangani di level aplikasi karena unique constraint tidak bisa filter by status
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_revisions');
    }
};
