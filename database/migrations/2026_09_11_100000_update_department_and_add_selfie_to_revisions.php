<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Alter employees table: department column to string (default Indobismar)
        DB::statement("ALTER TABLE employees MODIFY COLUMN department VARCHAR(255) NOT NULL DEFAULT 'Indobismar'");
        DB::table('employees')->update(['department' => 'Indobismar']);

        // 2. Add selfie_photo and selfie_expires_at to attendance_revisions table
        Schema::table('attendance_revisions', function (Blueprint $table) {
            $table->string('selfie_photo')->nullable()->after('reason');
            $table->timestamp('selfie_expires_at')->nullable()->after('selfie_photo');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_revisions', function (Blueprint $table) {
            $table->dropColumn(['selfie_photo', 'selfie_expires_at']);
        });

        DB::statement("ALTER TABLE employees MODIFY COLUMN department ENUM('Rungkut', 'Driyorejo') NOT NULL");
    }
};
