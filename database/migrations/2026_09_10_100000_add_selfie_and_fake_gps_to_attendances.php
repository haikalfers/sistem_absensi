<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Selfie photo — path relatif dari storage/app/public/selfies/
            $table->string('selfie_photo')->nullable()->after('notes');
            $table->timestamp('selfie_expires_at')->nullable()->after('selfie_photo'); // Tanggal otomatis dihapus

            // Fake GPS Detection
            $table->decimal('gps_accuracy', 8, 2)->nullable()->after('selfie_expires_at'); // Akurasi GPS dalam meter
            $table->string('ip_address', 45)->nullable()->after('gps_accuracy');           // IP saat check-in
            $table->json('fake_gps_flags')->nullable()->after('ip_address');               // Array flag dari setiap lapis
            $table->boolean('is_suspect')->default(false)->after('fake_gps_flags');        // True jika minimal 1 flag aktif
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'selfie_photo',
                'selfie_expires_at',
                'gps_accuracy',
                'ip_address',
                'fake_gps_flags',
                'is_suspect',
            ]);
        });
    }
};
