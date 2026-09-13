<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->boolean('is_field_assignment')->default(false)->after('is_suspect')
                ->comment('True jika absen dilakukan saat dinas luar (bypass radius GPS)');
            $table->foreignId('field_assignment_id')->nullable()->after('is_field_assignment')
                ->constrained('field_assignments')->nullOnDelete()
                ->comment('Referensi ke jadwal dinas luar yang aktif saat check-in');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['field_assignment_id']);
            $table->dropColumn(['is_field_assignment', 'field_assignment_id']);
        });
    }
};
