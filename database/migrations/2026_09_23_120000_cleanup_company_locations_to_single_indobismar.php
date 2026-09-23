<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\CompanyLocation;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ambil kantor pertama jika ada
        $primaryLocation = CompanyLocation::orderBy('id')->first();

        if ($primaryLocation) {
            // Update nama kantor menjadi 'Kantor PT. Indobismar'
            $primaryLocation->update([
                'name' => 'Kantor PT. Indobismar',
            ]);

            // Hapus kantor lain selain kantor utama
            CompanyLocation::where('id', '!=', $primaryLocation->id)->delete();
        } else {
            // Jika belum ada data sama sekali, buat 1 kantor Indobismar
            CompanyLocation::create([
                'name'          => 'Kantor PT. Indobismar',
                'latitude'      => (float) env('OFFICE_LAT', -7.3250610),
                'longitude'     => (float) env('OFFICE_LNG', 112.7112000),
                'radius_meters' => (int) env('OFFICE_RADIUS', 30),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu mengembalikan data kantor lama/dummy
    }
};
